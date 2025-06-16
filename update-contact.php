<?php
require_once __DIR__ . '/layouts/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_id'])) {
    $id = intval($_POST['contact_id']);
    $fields = [
        'first_name', 'last_name', 'email', 'phone_number', 'position', 'city', 'company_name', 'contact_type'
    ];
    $updates = [];
    $types = '';
    $params = [];
    foreach($fields as $f) {
        $updates[] = "$f=?";
        $types .= 's';
        $params[] = trim($_POST[$f] ?? '');
    }
    $types .= 'i';
    $params[] = $id;
    $stmt = $link->prepare("UPDATE contacts SET ".implode(', ', $updates).", updated_at=NOW() WHERE id=?");
    $stmt->bind_param($types, ...$params);
    $ok = $stmt->execute();
    $stmt->close();
    echo json_encode(['status' => $ok ? 'success' : 'error']);
    exit;
}
echo json_encode(['status'=>'error','message'=>'Invalid']);
