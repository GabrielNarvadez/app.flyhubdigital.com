<?php
require_once 'layouts/session.php';
require_once 'layouts/config.php';

// Only allow super_admin or admin
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_admin', 'admin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$tenant_id = intval($data['tenant_id'] ?? 0);
$module_id = intval($data['module_id'] ?? 0);
$permission = $data['permission'] ?? '';
$value = isset($data['value']) ? (bool)$data['value'] : false;

if (!$tenant_id || !$module_id || !in_array($permission, ['enabled', 'can_view', 'can_edit', 'can_export', 'can_delete'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

// Check if record exists
$stmt = $link->prepare("SELECT 1 FROM tenant_module_access WHERE tenant_id = ? AND module_id = ?");
$stmt->bind_param("ii", $tenant_id, $module_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $sql = "UPDATE tenant_module_access SET $permission = ? WHERE tenant_id = ? AND module_id = ?";
    $upd = $link->prepare($sql);
    $valInt = $value ? 1 : 0;
    $upd->bind_param("iii", $valInt, $tenant_id, $module_id);
    $success = $upd->execute();
    $upd->close();
} else {
    $stmt->close();
    $enabled = 0;
    $can_view = 0;
    $can_edit = 0;
    $can_export = 0;
    $can_delete = 0;

    if ($permission === 'enabled') $enabled = $value ? 1 : 0;
    elseif ($permission === 'can_view') $can_view = $value ? 1 : 0;
    elseif ($permission === 'can_edit') $can_edit = $value ? 1 : 0;
    elseif ($permission === 'can_export') $can_export = $value ? 1 : 0;
    elseif ($permission === 'can_delete') $can_delete = $value ? 1 : 0;

    $ins = $link->prepare("
        INSERT INTO tenant_module_access
        (tenant_id, module_id, enabled, can_view, can_edit, can_export, can_delete)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $ins->bind_param("iiiiiii", $tenant_id, $module_id, $enabled, $can_view, $can_edit, $can_export, $can_delete);
    $success = $ins->execute();
    $ins->close();
}

echo json_encode(['success' => $success]);
