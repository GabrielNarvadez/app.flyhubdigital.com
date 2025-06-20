<?php
require_once __DIR__ . '/layouts/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact_id = intval($_POST['contact_id'] ?? 0);
    $field = $_POST['field'] ?? '';
    $value = trim($_POST['value'] ?? '');

    // Only allow certain fields
    $allowed = ['email', 'phone_number', 'city', 'contact_type'];
    if (!$contact_id || !in_array($field, $allowed)) {
        echo json_encode(['status'=>'error','message'=>'Invalid field or ID.']); exit;
    }

    $sql = "UPDATE contacts SET $field=? WHERE id=?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param('si', $value, $contact_id);
    $ok = $stmt->execute();
    $stmt->close();

    echo json_encode(['status'=>$ok?'success':'error']);
    exit;
}
echo json_encode(['status'=>'error','message'=>'Invalid request']);
exit;
