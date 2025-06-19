<?php
include 'layouts/session.php'; // if you need session_start()

// Only allow admin/super admin to use this
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: tenant-management.php');
    exit;
}

if (isset($_GET['tenant_id'])) {
    $_SESSION['admin_switched_tenant_id'] = intval($_GET['tenant_id']);
}

// Redirect back to the super admin dashboard (change filename if needed)
header('Location: super-admin-dashboard.php');
exit;
?>
