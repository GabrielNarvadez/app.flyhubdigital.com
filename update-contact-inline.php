<?php
require_once __DIR__.'/layouts/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>'error','message'=>'Invalid request']); exit;
}

$cid   = intval($_POST['contact_id'] ?? 0);
$field = $_POST['field']            ?? '';

if (!$cid) {
    echo json_encode(['status'=>'error','message'=>'Invalid field or ID.']); exit;
}

/* ------------ master block coming from #topProfileForm ------------ */
if ($field === 'top_profile') {
    $first   = trim($_POST['first_name']   ?? '');
    $last    = trim($_POST['last_name']    ?? '');
    $company = trim($_POST['company_name'] ?? '');
    $pos     = trim($_POST['position']     ?? '');

    $stmt = $link->prepare(
        'UPDATE contacts
            SET first_name = ?,
                last_name  = ?,
                company_name = ?,
                position   = ?
          WHERE id = ?'
    );
    $stmt->bind_param('ssssi', $first, $last, $company, $pos, $cid);
    $ok = $stmt->execute();
    $stmt->close();

    echo json_encode(['status'=> $ok ? 'success' : 'error']);
    exit;
}

/* ------------ ordinary one-column inline edit ------------ */
$value   = trim($_POST['value'] ?? '');
$allowed = ['email','phone_number','city','contact_type'];

if (!in_array($field, $allowed, true)) {
    echo json_encode(['status'=>'error','message'=>'Invalid field or ID.']); exit;
}

$stmt = $link->prepare("UPDATE contacts SET $field = ? WHERE id = ?");
$stmt->bind_param('si', $value, $cid);
$ok = $stmt->execute();
$stmt->close();

echo json_encode(['status'=>$ok ? 'success' : 'error']);
