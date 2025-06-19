<?php
// tenant-users.php

// 1) Bootstrap session & DB
require_once 'layouts/session.php';   // sets $_SESSION['user_id'], ['tenant_id'], ['role']
require_once 'layouts/config.php';    // gives you $link = new mysqli(...)
include    'layouts/main.php';        // your header, nav, etc.

// 2) Determine which tenant we're managing
$role            = $_SESSION['role']      ?? '';
$sessionTenantId = intval($_SESSION['tenant_id'] ?? 0);

// If a tenant_id is passed via GET, only super_admins may override:
if (!empty($_GET['tenant_id']) && $role === 'super_admin') {
    $tenant_id = intval($_GET['tenant_id']);
}
// Otherwise fall back to the session’s tenant:
else {
    $tenant_id = $sessionTenantId;
}

// If we still have no tenant_id (and we're not super_admin), redirect back:
if (!$tenant_id) {
    header('Location: access-management.php');
    exit;
}

// 3) Fetch the tenant name (for your header/title)
$stmt = $link->prepare("SELECT tenant_name FROM tenants WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $tenant_id);
$stmt->execute();
$tenant = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$tenant) {
    die('Tenant not found.');
}

// 4) ASSIGN A USER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_user'])) {
    $user_id = intval($_POST['user_id']);
    $r       = $_POST['role'];
    $s       = $_POST['status'];

    // defensive: tenant must exist
    $chk = $link->prepare("SELECT 1 FROM tenants WHERE id = ?");
    $chk->bind_param('i', $tenant_id);
    $chk->execute();
    $chk->store_result();
    if ($chk->num_rows === 0) {
        die('Tenant not found for assignment.');
    }
    $chk->close();

    // prevent dupes
    $chk2 = $link->prepare("SELECT 1 FROM user_tenants WHERE tenant_id = ? AND user_id = ?");
    $chk2->bind_param('ii', $tenant_id, $user_id);
    $chk2->execute();
    $chk2->store_result();
    if ($chk2->num_rows === 0) {
        $ins = $link->prepare("
            INSERT INTO user_tenants
              (tenant_id, user_id, role, status)
            VALUES (?,?,?,?)
        ");
        $ins->bind_param('iiss', $tenant_id, $user_id, $r, $s);
        $ins->execute();
        $ins->close();
    }
    $chk2->close();

    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// 5) DELETE AN ASSIGNMENT
if (isset($_GET['action'], $_GET['user_id']) && $_GET['action'] === 'delete') {
    $delUser = intval($_GET['user_id']);
    $del = $link->prepare("
        DELETE FROM user_tenants
         WHERE tenant_id = ?
           AND user_id   = ?
    ");
    $del->bind_param('ii', $tenant_id, $delUser);
    $del->execute();
    $del->close();

    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// 6) EDIT AN ASSIGNMENT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user_assignment'])) {
    $u    = intval($_POST['user_id']);
    $r2   = $_POST['role'];
    $s2   = $_POST['status'];

    $upd = $link->prepare("
        UPDATE user_tenants
           SET role   = ?,
               status = ?
         WHERE tenant_id = ?
           AND user_id   = ?
    ");
    $upd->bind_param('ssii', $r2, $s2, $tenant_id, $u);
    $upd->execute();
    $upd->close();

    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// 7) LOAD ASSIGNED USERS
$stmt = $link->prepare("
    SELECT
      u.id, u.name, u.email,
      ut.role, ut.status
    FROM users u
    JOIN user_tenants ut ON u.id = ut.user_id
   WHERE ut.tenant_id = ?
 ORDER BY u.name
");
$stmt->bind_param('i', $tenant_id);
$stmt->execute();
$tenant_users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 8) LOAD ALL OTHER USERS
$assigned_ids = array_column($tenant_users, 'id');
$stmt = $link->prepare("SELECT id, name, email FROM users ORDER BY name");
$stmt->execute();
$all_users = [];
foreach ($stmt->get_result() as $u) {
    if (!in_array($u['id'], $assigned_ids, true)) {
        $all_users[] = $u;
    }
}
$stmt->close();
?>
<head>
    <title>Tenant Users | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
</head>
<body>
    <div class="wrapper">
        <?php include 'layouts/menu.php'; ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <!-- Page Header -->
                    <div class="row mb-3" style="margin-top: 30px;">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <h3>Users for <?= htmlspecialchars($tenant['tenant_name']) ?></h3>
                            <a href="tenant-management.php" class="btn btn-outline-secondary btn-sm">
                                <i class="ri-arrow-go-back-line"></i> Back to Tenants
                            </a>
                        </div>
                    </div>

                    <!-- Assign User Form -->
                    <div class="mb-4">
                        <form method="post" action="" class="row g-3 align-items-end">
                            <input type="hidden" name="tenant_id" value="<?= $tenant_id ?>">
                            <div class="col-md-5">
                                <label for="user_id" class="form-label">Select User to Assign</label>
                                <select name="user_id" id="user_id" class="form-select" required>
                                    <option value="">-- Select User --</option>
                                    <?php foreach ($all_users as $user): ?>
                                        <option value="<?= $user['id'] ?>">
                                            <?= htmlspecialchars($user['name'] . ' (' . $user['email'] . ')') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="role" class="form-label">Role</label>
                                <select name="role" id="role" class="form-select" required>
                                    <option value="owner">Owner</option>
                                    <option value="admin">Admin</option>
                                    <option value="user" selected>User</option>
                                    <option value="billing">Billing</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="active" selected>Active</option>
                                    <option value="invited">Invited</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" name="assign_user" class="btn btn-primary w-100">Assign User</button>
                            </div>
                        </form>
                    </div>

                    <!-- Tenant Users Table -->
                    <div class="card">
                        <div class="card-header bg-white fw-bold">Tenant Users</div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-middle table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>

                                        <tbody>
                                            <?php if (empty($tenant_users)): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No users found.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($tenant_users as $user): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($user['name']) ?></td>
                                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($user['role']) ?></span></td>
                                                        <td>
                                                            <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                                <?= ucfirst($user['status']) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button
                                                                class="btn btn-sm btn-outline-secondary"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editUserModal"
                                                                data-user-id="<?= $user['id'] ?>"
                                                                data-tenant-id="<?= $tenant_id ?>"
                                                                data-role="<?= htmlspecialchars($user['role']) ?>"
                                                                data-status="<?= htmlspecialchars($user['status']) ?>"
                                                            >
                                                                Edit
                                                            </button>
                                                            <a
                                                                href="tenant-users.php?tenant_id=<?= $tenant_id ?>&user_id=<?= $user['id'] ?>&action=delete"
                                                                onclick="return confirm('Are you sure you want to remove this user from the tenant?');"
                                                                class="btn btn-sm btn-outline-danger"
                                                            >
                                                                Remove
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div> <!-- container -->
            </div> <!-- content -->
            <?php include 'layouts/footer.php'; ?>
        </div>
    </div>

    <!-- Edit User Assignment Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="">
        <input type="hidden" name="tenant_id" id="edit_tenant_id">
        <input type="hidden" name="user_id" id="edit_user_id">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel">Edit User Assignment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label">Role</label>
            <select name="role" id="edit_role" class="form-select" required>
              <option value="owner">Owner</option>
              <option value="admin">Admin</option>
              <option value="user">User</option>
              <option value="billing">Billing</option>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label">Status</label>
            <select name="status" id="edit_status" class="form-select" required>
              <option value="active">Active</option>
              <option value="invited">Invited</option>
              <option value="suspended">Suspended</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="edit_user_assignment" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

    <?php include 'layouts/right-sidebar.php'; ?>
    <?php include 'layouts/footer-scripts.php'; ?>
    <script src="assets/js/app.min.js"></script>
</body>
</html>
