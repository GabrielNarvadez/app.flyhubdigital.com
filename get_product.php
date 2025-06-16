<?php
// get_product.php
require_once __DIR__ . '/layouts/config.php';

$id = intval($_GET['id'] ?? 0);
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $link->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$product = $res->fetch_assoc();
$stmt->close();

if ($product) {
    echo json_encode(['status' => 'success', 'product' => $product]);
} else {
    echo json_encode(['status' => 'error', 'product' => null]);
}
exit;
