<?php
require_once __DIR__ . '/layouts/config.php';
date_default_timezone_set('Asia/Manila');

// 1. Sales Today
$stmt = $link->prepare("SELECT COALESCE(SUM(total),0) FROM sales WHERE DATE(sale_datetime) = CURDATE() AND (status IS NULL OR status = 'completed')");
$stmt->execute(); $stmt->bind_result($sales_today); $stmt->fetch(); $stmt->close();

// 2. Orders Today
$stmt = $link->prepare("SELECT COUNT(*) FROM sales WHERE DATE(sale_datetime) = CURDATE()");
$stmt->execute(); $stmt->bind_result($orders_today); $stmt->fetch(); $stmt->close();

// 3. Low Stock Items
$stmt = $link->prepare("SELECT COUNT(*) FROM products WHERE stock <= COALESCE(min_stock, 3)");
$stmt->execute(); $stmt->bind_result($low_stock_count); $stmt->fetch(); $stmt->close();

// 4. Failed Orders (POS + Invoices)
$stmt = $link->prepare("SELECT COUNT(*) FROM sales WHERE DATE(sale_datetime) = CURDATE() AND status = 'failed'");
$stmt->execute(); $stmt->bind_result($failed_orders_pos); $stmt->fetch(); $stmt->close();

$stmt = $link->prepare("SELECT COUNT(*) FROM invoices WHERE DATE(issue_date) = CURDATE() AND status = 'failed'");
$stmt->execute(); $stmt->bind_result($failed_orders_inv); $stmt->fetch(); $stmt->close();

$failed_orders_total = $failed_orders_pos + $failed_orders_inv;

// 5. Active Channels
$channels = ['shopify' => 0, 'shopee' => 0, 'pos' => 0];
foreach (['shopify', 'shopee', 'pos'] as $ch) {
    $q = "SELECT COUNT(*) FROM products WHERE channel_{$ch} = 'ok'";
    $stmt = $link->prepare($q);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    if ($count > 0) $channels[$ch] = 1;
}
$active_channels = array_sum($channels);

$stmt = $link->prepare("SELECT COUNT(*) FROM invoices WHERE status = 'unfulfilled'");
$stmt->execute(); $stmt->bind_result($unfulfilled_orders); $stmt->fetch(); $stmt->close();

echo json_encode([
    'sales_today' => (int)$sales_today,
    'orders_today' => (int)$orders_today,
    'low_stock_count' => (int)$low_stock_count,
    'failed_orders_total' => (int)$failed_orders_total,
    'active_channels' => (int)$active_channels,
    'unfulfilled_orders' => (int)$unfulfilled_orders,
]);
