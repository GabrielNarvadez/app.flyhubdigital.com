<?php
declare(strict_types=1);
/**
 * Shopify two‑way scheduled integration for Flyhub Business Apps
 * -----------------------------------------------------------------
 * Path   : /integrations/shopify.php
 * Cron   : every 5 minutes:
 *            php /home/USERNAME/public_html/integrations/shopify.php --direction=shopify
 *            php /home/USERNAME/public_html/integrations/shopify.php --direction=flyhub
 * Default: tenant ID 12 (constant DEFAULT_TENANT_ID) when none provided.
 * Images : D:/Projects/app.flyhubdigital.com/assets/img/tenants/{slug}/products/
 */

ini_set('max_execution_time', '1000');
ini_set('memory_limit', '512M');

require_once __DIR__ . '/../layouts/config.php'; // $link (mysqli)

// ───── Constants ────────────────────────────────────────────
const API_VERSION       = '2024-04';
const IMG_ROOT          = 'D:/Projects/app.flyhubdigital.com/assets/img/tenants';
const DEFAULT_TENANT_ID = 12;

// ───── Helper: slugify tenant name ───────────────────────────
function getTenantSlug(int $tenantId): string
{
    global $link;
    $stmt = $link->prepare('SELECT tenant_name FROM tenants WHERE id = ?');
    $stmt->bind_param('i', $tenantId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row    = $result->fetch_assoc();
    $name   = $row['tenant_name'] ?? (string)$tenantId;
    return strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', $name), '-'));
}

// ───── Helper: fetch Shopify credentials ─────────────────────
function getShopifyCreds(int $tenantId): ?array
{
    global $link;
    $stmt = $link->prepare(
        'SELECT shop, api_key, api_password, last_shopify_sync, last_flyhub_sync
         FROM shopify_credentials WHERE tenant_id = ?'
    );
    $stmt->bind_param('i', $tenantId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}

// ───── Helper: call Shopify REST Admin API ──────────────────
function shopifyRequest(array $creds, string $method, string $endpoint, array $payload = [])
{
    $url = sprintf('https://%s/admin/api/%s/%s', $creds['shop'], API_VERSION, ltrim($endpoint, '/'));
    $ch  = curl_init($url);

    $headers = ['Content-Type: application/json'];
    if (!empty($creds['api_key'])) {
        curl_setopt($ch, CURLOPT_USERPWD, $creds['api_key'] . ':' . $creds['api_password']);
    } else {
        $headers[] = 'X-Shopify-Access-Token: ' . $creds['api_password'];
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_HTTPHEADER     => $headers,
    ]);

    if ($payload) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_SLASHES));
    }

    $resp = curl_exec($ch);
    if ($resp === false) {
        throw new RuntimeException('cURL error: ' . curl_error($ch));
    }
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code >= 400) {
        throw new RuntimeException("Shopify API error ({$code}): {$resp}");
    }
    return json_decode($resp, true);
}

// ───── Helper: log sync actions ─────────────────────────────
function logSync(int $tenantId, string $direction, string $msg): void
{
    global $link;
    $stmt = $link->prepare(
        'INSERT INTO shopify_sync_log (tenant_id, direction, message) VALUES (?,?,?)'
    );
    $stmt->bind_param('iss', $tenantId, $direction, $msg);
    $stmt->execute();
}

// ───── Upsert variant into Flyhub products table ─────────────
function upsertVariant(int $tenantId, array $product, array $variant): void
{
    global $link;
    // Determine image URL
    $imgUrl = null;
    foreach ($product['images'] ?? [] as $img) {
        if (in_array($variant['id'], $img['variant_ids'] ?? [], true)) {
            $imgUrl = $img['src'];
            break;
        }
    }
    if (!$imgUrl && !empty($product['images'][0]['src'])) {
        $imgUrl = $product['images'][0]['src'];
    }

    // Download & save image locally
    $localImg = $imgUrl;

    // Prepare variables
    $title       = $product['title'];
    $description = strip_tags($product['body_html']);
    $sku         = $variant['sku'];
    $barcode     = $variant['barcode'];
    $price       = $variant['price'];
    $stock       = $variant['inventory_quantity'];
    $status      = ($product['status'] === 'active') ? 'active' : 'draft';

    // Upsert into products
    $sql = 'INSERT INTO products
            (tenant_id,name,description,sku,barcode,price,stock,status,photo_url,shopify_product_id,shopify_variant_id)
            VALUES (?,?,?,?,?,?,?,?,?,?,?)
            ON DUPLICATE KEY UPDATE
              name        = VALUES(name),
              description = VALUES(description),
              price       = VALUES(price),
              stock       = VALUES(stock),
              barcode     = VALUES(barcode),
              photo_url   = VALUES(photo_url),
              updated_at  = NOW()';
    $stmt = $link->prepare($sql);
    if (!$stmt) {
        die("SQL error: " . $link->error . "\nQuery: " . $sql);
    }    
    $stmt->bind_param(
        'issssdisiii',
        $tenantId,
        $title,
        $description,
        $sku,
        $barcode,
        $price,
        $stock,
        $status,
        $localImg,
        $product['id'],
        $variant['id']
    );
    $stmt->execute();
}

// ───── Sync Shopify → Flyhub ────────────────────────────────
function syncShopifyToFlyhub(int $tenantId, array $creds): void
{
    global $link;
    $since = $creds['last_shopify_sync']
        ? '&updated_at_min=' . urlencode($creds['last_shopify_sync'])
        : '';
    $page = '';
    do {
        $endpoint = "products.json?limit=250{$page}{$since}" .
                    "&fields=id,title,body_html,status,variants,images";
        $resp = shopifyRequest($creds, 'GET', $endpoint);
        foreach ($resp['products'] ?? [] as $product) {
            // Only process active products
            if ($product['status'] !== 'active') {
                continue;
            }
            foreach ($product['variants'] as $variant) {
                upsertVariant($tenantId, $product, $variant);
            }
        }
        $page = isset($resp['next_page']) ? '&page=' . $resp['next_page'] : '';
    } while ($page);

    $link->query(
        "UPDATE shopify_credentials SET last_shopify_sync = NOW() WHERE tenant_id = {$tenantId}"
    );
    logSync($tenantId, 'shopify_to_flyhub', 'Pull complete (active only)');
}

// ───── Sync Flyhub → Shopify ────────────────────────────────
function syncFlyhubToShopify(int $tenantId, array $creds): void
{
    global $link;
    $stmt = $link->prepare(
        'SELECT * FROM products
         WHERE tenant_id = ? AND updated_at > IFNULL(
               (SELECT last_flyhub_sync FROM shopify_credentials WHERE tenant_id = ?),
               "1970-01-01")'
    );
    $stmt->bind_param('ii', $tenantId, $tenantId);
    $stmt->execute();
    $rows = $stmt->get_result();

    while ($p = $rows->fetch_assoc()) {
        if (!$p['shopify_variant_id']) {
            continue;
        }
        $payload = ['variant' => [
            'id'      => (int)$p['shopify_variant_id'],
            'price'   => (string)$p['price'],
            'sku'     => $p['sku'],
            'barcode' => $p['barcode'],
        ]];
        shopifyRequest(
            $creds,
            'PUT',
            'variants/' . $p['shopify_variant_id'] . '.json',
            $payload
        );
    }

    $link->query(
        "UPDATE shopify_credentials SET last_flyhub_sync = NOW() WHERE tenant_id = {$tenantId}"
    );
    logSync($tenantId, 'flyhub_to_shopify', 'Push complete');
}

// ───── Manual trigger (tenant 12) ───────────────────────────
if (isset($_GET['manual'])) {
    runForTenant(DEFAULT_TENANT_ID, '--direction=shopify');
    runForTenant(DEFAULT_TENANT_ID, '--direction=flyhub');
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Sync complete for tenant ' . DEFAULT_TENANT_ID]);
    exit;
}

// ───── CLI entry point ─────────────────────────────────────
$direction = $argv[1] ?? '--direction=shopify';
$tenantId  = isset($argv[2]) ? (int)$argv[2] : DEFAULT_TENANT_ID;
runForTenant($tenantId, $direction);

// ───── Runner ──────────────────────────────────────────────
function runForTenant(int $tenantId, string $direction): void
{
    $creds = getShopifyCreds($tenantId);
    if (!$creds) {
        logSync($tenantId, $direction, 'No credentials found');
        return;
    }
    if ($direction === '--direction=shopify') {
        syncShopifyToFlyhub($tenantId, $creds);
    } elseif ($direction === '--direction=flyhub') {
        syncFlyhubToShopify($tenantId, $creds);
    }
}
