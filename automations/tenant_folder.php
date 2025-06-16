<?php
/**
 * automation: tenant_folder.php
 * ---------------------------------
 * Creates tenant-specific image folders using a slugified version of the tenant name:
 *   assets/img/tenants/{tenant-name-with-hyphens}/products
 *
 * ‣ Run this file via cron or CLI, e.g.
 *     php /path/to/automations/tenant_folder.php
 *
 * ‣ Requires an existing `tenants` table with columns `id` and `tenant_name` (or similar).
 * ‣ Uses the global $link MySQLi connection provided by layouts/config.php.
 */

// Load DB connection ($link) and any required constants
require_once __DIR__ . '/../layouts/config.php'; // adjust path if layout differs

// Helper: create slug from tenant name (lowercase, alphanum + hyphens)
function slugify(string $text): string
{
    // Convert to UTF-8, strip tags, set to lowercase
    $text = strtolower(trim($text));
    // Replace non-letter/digit characters with hyphens
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
    // Trim leading/trailing hyphens and collapse duplicates
    $text = trim($text, '-');
    $text = preg_replace('/-+/', '-', $text);
    return $text ?: 'tenant';
}

// Base directory where tenant folders live
$baseDir = realpath(__DIR__ . '/../assets/img/tenants');
if ($baseDir === false) {
    fwrite(STDERR, "[ERROR] Base directory not found.\n");
    exit(1);
}

// Fetch tenant IDs + names
$sql = "SELECT id, tenant_name FROM tenants";
$result = $link->query($sql);
if (!$result) {
    fwrite(STDERR, "[ERROR] Database query failed: {$link->error}\n");
    exit(1);
}

while ($row = $result->fetch_assoc()) {
    $tenantId   = (int) $row['id'];
    $tenantName = $row['tenant_name'] ?? '';
    $slug       = slugify($tenantName);

    // Final path: assets/img/tenants/{slug}/products
    $tenantPath = $baseDir . DIRECTORY_SEPARATOR . $slug . DIRECTORY_SEPARATOR . 'products';

    if (!is_dir($tenantPath)) {
        if (mkdir($tenantPath, 0755, true)) {
            echo "[OK] Created folder for tenant #$tenantId ($slug): $tenantPath\n";
        } else {
            fwrite(STDERR, "[ERROR] Could not create folder: $tenantPath\n");
        }
    } else {
        echo "[SKIP] Already exists: $tenantPath\n";
    }
}

$result->free();
$link->close();

echo "--- Done creating tenant folders ---\n";
