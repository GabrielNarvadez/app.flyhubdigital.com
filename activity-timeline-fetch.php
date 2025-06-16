<?php
require_once __DIR__ . '/layouts/config.php';
header('Content-Type: application/json');
$entity_type = $_GET['entity_type'] ?? 'contact';
$entity_id = intval($_GET['entity_id'] ?? 0);
$data = [];
if ($entity_id > 0) {
    $sql = "SELECT * FROM activity_timeline WHERE entity_type=? AND entity_id=? ORDER BY created_at DESC";
    $stmt = $link->prepare($sql);
    $stmt->bind_param('si', $entity_type, $entity_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) $data[] = $row;
    $stmt->close();
}
echo json_encode($data);
