<?php
// layouts/session.php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: auth-login.php');
    exit;
}

require_once __DIR__ . '/config.php';

$stmt = $link->prepare("
  SELECT tenant_id,
         role
    FROM users
   WHERE id = ?
");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // if super admin, override tenant_id to 30
    if ($row['role'] === 'super_admin') {
        $_SESSION['tenant_id'] = 30;
    } else {
        // for normal users, use whatever is in the users.tenant_id column
        $_SESSION['tenant_id'] = $row['tenant_id'] !== null
            ? intval($row['tenant_id'])
            : null;
    }

    // always store role
    $_SESSION['role'] = $row['role'];
} else {
    session_destroy();
    header('Location: auth-login.php');
    exit;
}

$stmt->close();
