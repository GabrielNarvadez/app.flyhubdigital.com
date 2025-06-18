<?php
declare(strict_types=1);
/**
 * Shopify two-way scheduled integration for Flyhub Business Apps
 * -----------------------------------------------------------------
 * Path   : /integrations/shopify.php
 * Cron   : every 5 minutes:
 *            php /home/USERNAME/public_html/integrations/shopify.php --direction=shopify
 *            php /home/USERNAME/public_html/integrations/shopify.php --direction=flyhub
 * Default: tenant ID 12 (constant DEFAULT_TENANT_ID) when none provided.
 * Images : D:/Projects/app.flyhubdigital.com/assets/img/tenants/{slug}/products/
 */

ini_set('max_execution_time', '1000');
ini_set('memory_limit', '512M');

require_once __DIR__ . '/../layouts/config.php'; // $link (mysqli)


// If this script is hit over HTTP with ?direction=…, run immediately
if (php_sapi_name() !== 'cli' && isset($_GET['direction'])) {
    // sanitize inputs
    $dir     = in_array($_GET['direction'], ['shopify','flyhub'], true)
             ? $_GET['direction']
             : die(json_encode(['error'=>'invalid direction']));
    $tenant  = isset($_GET['tenant']) ? (int)$_GET['tenant'] : DEFAULT_TENANT_ID;
    $flag    = "--direction={$dir}";
    header('Content-Type: application/json');

    try {
        runForTenant($tenant, $flag);
        echo json_encode([
          'message'   => "Sync complete for tenant {$tenant}",
          'direction' => $dir
        ]);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode([
          'error'   => $e->getMessage(),
          'trace'   => $e->getTraceAsString()
        ]);
    }
    exit;
}


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

        // === new: echo to console if running via CLI ===
        if (php_sapi_name() === 'cli') {
            $ts = date('Y-m-d H:i:s');
            // if flyhub_to_shopify update, collapse to just the variant ID
            if ($direction === 'flyhub_to_shopify'
                && preg_match('/^Updated variant (\d+)/', $msg, $m)
            ) {
                echo "[{$ts}][{$direction}] Updated variant {$m[1]}\n";
            }
            // if flyhub_to_shopify inventory set, collapse similarly
            elseif ($direction === 'flyhub_to_shopify'
                && preg_match('/^Set inventory for variant (\d+)/', $msg, $m2)
            ) {
                echo "[{$ts}][{$direction}] Set inventory for variant {$m2[1]}\n";
            }
            // everything else prints as-is
            else {
                echo "[{$ts}][{$direction}] {$msg}\n";
            }
            flush();
        }

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

    // Download & save image locally (not shown here)
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
        'issssdissii',
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

// ───── Sync Shopify -> Flyhub ────────────────────────────────
function syncShopifyToFlyhub(int $tenantId, array $creds): void
{
    global $link;
    $since      = '';
    $sinceId    = 0;
    $batchCount = 0;

    do {
$endpoint = "products.json"
          . "?limit=250"
          . "&since_id={$sinceId}"
          . "&published_status=published"    // ← only get active products
          . "&fields=id,title,body_html,status,variants,images";

        $resp = shopifyRequest($creds, 'GET', $endpoint);
        if (empty($resp['products'])) {
            break;
        }

        foreach ($resp['products'] as $product) {
            if ($product['status'] !== 'active') {
                logSync($tenantId, 'shopify_to_flyhub', "Skipping inactive product {$product['id']}");
                continue;
            }

            // Log that we’re about to process this product:
            logSync($tenantId, 'shopify_to_flyhub', "Processing product {$product['id']} (“{$product['title']}”)");

            foreach ($product['variants'] as $variant) {
                try {
                    upsertVariant($tenantId, $product, $variant);
                    logSync(
                        $tenantId,
                        'shopify_to_flyhub',
                        "✔️ Upserted variant {$variant['id']} (SKU={$variant['sku']}, QTY={$variant['inventory_quantity']})"
                    );
                } catch (\Exception $e) {
                    logSync(
                        $tenantId,
                        'shopify_to_flyhub',
                        "❌ Error on variant {$variant['id']}: " . $e->getMessage()
                    );
                }
                $batchCount++;
            }

            // Keep track for pagination
            $sinceId = max($sinceId, (int)$product['id']);
        }
    } while (count($resp['products']) === 250);

    // Final sync timestamp & summary
    $link->query(
        "UPDATE shopify_credentials 
            SET last_shopify_sync = NOW() 
          WHERE tenant_id = {$tenantId}"
    );
    logSync(
        $tenantId,
        'shopify_to_flyhub',
        "Pull complete: {$batchCount} variants processed"
    );
}


// ───── Fetch first active Shopify location ID ────────────────
function getShopifyLocationId(array $creds): int
{
    $resp = shopifyRequest($creds, 'GET', 'locations.json');
    foreach ($resp['locations'] as $loc) {
        if (!empty($loc['active'])) {
            return (int)$loc['id'];
        }
    }
    throw new RuntimeException('No active Shopify location found');
}

// ───── Sync Flyhub -> Shopify, including inventory_levels ───
function syncFlyhubToShopify(int $tenantId, array $creds): void
{
    global $link;

    // 1) Find any products changed since last push
    $stmt = $link->prepare(
        'SELECT id, sku, barcode, price, stock, shopify_variant_id
           FROM products
          WHERE tenant_id = ?
            AND updated_at > IFNULL(
                  (SELECT last_flyhub_sync FROM shopify_credentials WHERE tenant_id = ?),
                  "1970-01-01"
               )'
    );
    $stmt->bind_param('ii', $tenantId, $tenantId);
    $stmt->execute();
    $rows = $stmt->get_result();
    $stmt->close();

    if ($rows->num_rows === 0) {
        logSync($tenantId, 'flyhub_to_shopify', 'No changes to push');
        return;
    }

    // 2) Fetch and log your Shopify location
    $locationId = getShopifyLocationId($creds);
    logSync($tenantId, 'flyhub_to_shopify', "Using Shopify location {$locationId}");

    $count = 0;
    while ($p = $rows->fetch_assoc()) {
        $variantId = (int)$p['shopify_variant_id'];
        if (!$variantId) {
            logSync(
                $tenantId,
                'flyhub_to_shopify',
                "Skipping product {$p['id']} with missing shopify_variant_id"
            );
            continue;
        }

        // a) Update variant fields
        try {
            $payloadVar = ['variant' => [
                'id'      => $variantId,
                'price'   => (string)$p['price'],
                'sku'     => $p['sku'],
                'barcode' => $p['barcode'],
            ]];
            $responseVar = shopifyRequest(
                $creds,
                'PUT',
                "variants/{$variantId}.json",
                $payloadVar
            );
            logSync(
                $tenantId,
                'flyhub_to_shopify',
                "Updated variant {$variantId}: " . json_encode($responseVar)
            );
        } catch (\Exception $e) {
            logSync(
                $tenantId,
                'flyhub_to_shopify',
                "Error updating variant {$variantId}: " . $e->getMessage()
            );
            continue;
        }

        // b) Fetch inventory_item_id
        try {
            $vr = shopifyRequest($creds, 'GET', "variants/{$variantId}.json");
            $invItemId = $vr['variant']['inventory_item_id'] ?? null;
            $oldQty   = isset($vr['variant']['inventory_quantity'])
            ? (int)$vr['variant']['inventory_quantity']
            : null;
            logSync(
                $tenantId,
                'flyhub_to_shopify',
                "Fetched inventory_item_id {$invItemId} for variant {$variantId}",
                "Fetched inv_item {$invItemId} for variant {$variantId} (old_qty={$oldQty})"
            );
        } catch (\Exception $e) {
            logSync(
                $tenantId,
                'flyhub_to_shopify',
                "Error fetching variant {$variantId}: " . $e->getMessage()
            );
            continue;
        }

        // c) Set new on-hand quantity
        if ($invItemId) {
            try {
                $newQty = (int)$p['stock'];
                $invPayload = [
                    'location_id'       => $locationId,
                    'inventory_item_id' => $invItemId,
                    'available'         => $newQty,
                ];
                $responseInv = shopifyRequest(
                    $creds,
                    'POST',
                    'inventory_levels/set.json',
                    $invPayload
                );
                logSync(
                    $tenantId,
                    'flyhub_to_shopify',
                    "Variant {$variantId} inventory changed from {$oldQty} to {$newQty}"

                );
            } catch (\Exception $e) {
                logSync(
                    $tenantId,
                    'flyhub_to_shopify',
                    "Error setting inventory for variant {$variantId}: "
                    . $e->getMessage()
                );
            }
        } else {
            logSync(
                $tenantId,
                'flyhub_to_shopify',
                "No inventory_item_id for variant {$variantId}, skipping inventory set"
            );
        }

        $count++;
    }

    // 4) Mark the sync time & log summary
    $link->query(
        "UPDATE shopify_credentials
            SET last_flyhub_sync = NOW()
          WHERE tenant_id = {$tenantId}"
    );
    logSync(
        $tenantId,
        'flyhub_to_shopify',
        "Pushed {$count} variant(s) + inventory levels"
    );
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
