<?php
require_once __DIR__ . '/layouts/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $items      = $_POST['items'] ?? [];
    $total      = floatval($_POST['total'] ?? 0);
    $tax        = floatval($_POST['tax'] ?? 0);
    $discount   = floatval($_POST['discount'] ?? 0);
    $customer   = trim($_POST['customer'] ?? '');
    $notes      = trim($_POST['note'] ?? ''); // The frontend uses "note"
    $payment    = trim($_POST['payment'] ?? 'Cash');

    // Insert sale
    $stmt = $link->prepare("INSERT INTO sales (sale_datetime, total, tax, discount, notes, payment_method, status) VALUES (NOW(), ?, ?, ?, ?, ?, 'completed')");
    $stmt->bind_param("dddss", $total, $tax, $discount, $notes, $payment);
    $stmt->execute();
    $sale_id = $stmt->insert_id;
    $stmt->close();

    // Save sale items (matches your table)
    $itemsArr = json_decode($items, true);
    if ($itemsArr && is_array($itemsArr)) {
        foreach ($itemsArr as $item) {
            $product_id = intval($item['id']);
            $price = floatval($item['price']);
            $qty = intval($item['qty']);
            $total_item = $price * $qty;

            $stmt = $link->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiidd", $sale_id, $product_id, $qty, $price, $total_item);
            $stmt->execute();
            $stmt->close();
        }
    }

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'sale_id' => $sale_id]);
    exit;
}
?>
