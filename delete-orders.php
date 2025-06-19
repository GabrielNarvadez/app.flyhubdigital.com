<?php
require_once __DIR__ . '/layouts/config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if (empty($data['items']) || !is_array($data['items'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    exit;
}

foreach ($data['items'] as $item) {
    $type = $item['type'] ?? '';
    $id = intval($item['id'] ?? 0);
    if ($id <= 0) continue;

    if ($type === 'Invoice') {
        $stmt = $link->prepare("DELETE FROM invoices WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    } elseif ($type === 'Order') {
        // First, delete related items in sale_items (child table)
        $stmt_items = $link->prepare("DELETE FROM sale_items WHERE sale_id=?");
        $stmt_items->bind_param('i', $id);
        $stmt_items->execute();
        $stmt_items->close();

        // Then, delete the order itself
        $stmt = $link->prepare("DELETE FROM sales WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
}

echo json_encode(['success' => true]);
