<?php
// tenant-access.php

// 1) Bootstrap session & DB
require_once 'layouts/session.php';   // sets $_SESSION['user_id'], $_SESSION['tenant_id'], $_SESSION['role']
require_once 'layouts/config.php';    // defines $link (mysqli)
include    'layouts/main.php';        // page chrome / header

// 2) Determine which tenant we’re managing:
//    - If you’re super_admin AND you supplied ?tenant_id=XX, use that.
//    - Otherwise fall back to your session’s tenant.
$tenant_id = 0;
if (
    isset($_SESSION['role'])
    && in_array($_SESSION['role'], ['super_admin','admin'], true)
    && isset($_GET['tenant_id'])
) {
    // super admin explicitly wants to manage a different tenant
    $tenant_id = intval($_GET['tenant_id']);
} else {
    // ordinary tenant user: lock down to your own
    $tenant_id = intval($_SESSION['tenant_id'] ?? 0);
}
if ($tenant_id <= 0) {
    die('Invalid or missing tenant.');
}
// defensive check that this tenant actually exists
$chk = $link->prepare("SELECT 1 FROM tenants WHERE id = ?");
$chk->bind_param('i', $tenant_id);
$chk->execute();
$chk->store_result();
if ($chk->num_rows === 0) {
    die('Tenant not found.');
}
$chk->close();

// helper to build a redirect back to this page with the proper tenant_id
function redirect_back($path = null) {
    global $tenant_id;
    $url = $path ?: $_SERVER['PHP_SELF'];
    // only append tenant_id if super_admin is managing someone else
    if (
        isset($_SESSION['role'])
        && in_array($_SESSION['role'], ['super_admin','admin'], true)
    ) {
        $url .= '?tenant_id=' . $tenant_id;
    }
    header("Location: $url");
    exit;
}

// --- Handle Assign User to Tenant ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_user'])) {
    $user_id = intval($_POST['user_id']);
    $role    = $_POST['role'];
    $status  = $_POST['status'];

    // prevent duplicate
    $exist = $link->prepare("SELECT 1 FROM user_tenants WHERE tenant_id = ? AND user_id = ?");
    $exist->bind_param('ii', $tenant_id, $user_id);
    $exist->execute();
    $exist->store_result();
    if ($exist->num_rows === 0) {
        $insert = $link->prepare("
            INSERT INTO user_tenants (tenant_id, user_id, role, status)
            VALUES (?, ?, ?, ?)
        ");
        $insert->bind_param("iiss", $tenant_id, $user_id, $role, $status);
        $insert->execute();
        $insert->close();
    }
    $exist->close();

    redirect_back();
}

// --- Handle Remove User from Tenant ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_user_id'])) {
    $remove_user_id = intval($_POST['remove_user_id']);
    $del = $link->prepare("
        DELETE FROM user_tenants
         WHERE tenant_id = ?
           AND user_id   = ?
    ");
    $del->bind_param("ii", $tenant_id, $remove_user_id);
    $del->execute();
    $del->close();

    redirect_back();
}

// --- Handle Edit User Role/Status ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user_id'])) {
    $edit_user_id = intval($_POST['edit_user_id']);
    $new_role     = $_POST['edit_role'];
    $new_status   = $_POST['edit_status'];
    $upd = $link->prepare("
        UPDATE user_tenants
           SET role   = ?,
               status = ?
         WHERE tenant_id = ?
           AND user_id   = ?
    ");
    $upd->bind_param("ssii", $new_role, $new_status, $tenant_id, $edit_user_id);
    $upd->execute();
    $upd->close();

    redirect_back();
}

// --- Handle Delete Tenant ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_tenant'])) {
    // only super_admin should be able to delete across tenants
    if (! in_array($_SESSION['role'], ['super_admin','admin'], true)) {
        die('Unauthorized');
    }

    mysqli_query($link, "SET FOREIGN_KEY_CHECKS=0");
    $tables = [
        'user_tenants',
        'companies',
        'files',
        'petty_cash',
        'products',
        'tenant_addons',
        'tenant_module_access',
        'tenants'
    ];
    foreach ($tables as $tbl) {
        $link->query("DELETE FROM `$tbl` WHERE tenant_id = $tenant_id" . ($tbl==='tenants' ? " OR id = $tenant_id" : ""));
    }
    mysqli_query($link, "SET FOREIGN_KEY_CHECKS=1");

    // after deletion, go back to management list
    header("Location: tenant-management.php");
    exit;
}

// --- Handle Edit Tenant ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_tenant'])) {
    $name    = trim($_POST['tenant_name']);
    $plan_id = intval($_POST['plan_id']);
    $status  = $_POST['billing_status'];
    $email   = trim($_POST['contact_email']);

    $upd = $link->prepare("
        UPDATE tenants
           SET tenant_name   = ?,
               plan_id       = ?,
               billing_status= ?,
               contact_email = ?
         WHERE id = ?
    ");
    $upd->bind_param("sissi", $name, $plan_id, $status, $email, $tenant_id);
    $upd->execute();
    $upd->close();

    redirect_back();
}

// --- Fetch Tenant Info + Plan ---
$stmt = $link->prepare("
    SELECT t.*, p.plan_name
      FROM tenants t
 LEFT JOIN plans p ON t.plan_id = p.id
     WHERE t.id = ?
     LIMIT 1
");
$stmt->bind_param('i', $tenant_id);
$stmt->execute();
$tenant = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (! $tenant) {
    die("Tenant not found");
}

// --- Plan Limits & Counts ---
$plan_id = intval($tenant['plan_id']);

// user count
$res = $link->query("SELECT COUNT(*) AS total FROM users WHERE tenant_id = $tenant_id");
$user_count = $res->fetch_assoc()['total'] ?? 0;
$user_limits = [1=>1,2=>5,3=>20,4=>60,5=>999];
$user_limit  = $user_limits[$plan_id] ?? 0;

// company count
$res = $link->query("SELECT COUNT(*) AS total FROM companies WHERE tenant_id = $tenant_id");
$company_count = $res->fetch_assoc()['total'] ?? 0;
$company_limits = [1=>1,2=>3,3=>30,4=>999,5=>999];
$company_limit  = $company_limits[$plan_id] ?? 0;

// storage & API (static demo)
$storage_used  = '1.2GB';
$storage_limit = [1=>'100MB',2=>'5GB',3=>'30GB',4=>'200GB',5=>'∞'][$plan_id] ?? '-';
$api_limit     = [1=>0,2=>1,3=>3,4=>99,5=>999][$plan_id] ?? 0;

// modules included in plan
$modules = [];
$res = $link->query("
    SELECT m.id, m.module_name
      FROM plan_modules pm
      JOIN modules m ON m.id = pm.module_id
     WHERE pm.plan_id = $plan_id
");
while ($row = $res->fetch_assoc()) {
    $modules[$row['id']] = $row['module_name'];
}

// all modules for UI
$all_modules = [];
$res = $link->query("SELECT id, module_name FROM modules");
while ($row = $res->fetch_assoc()) {
    $all_modules[$row['id']] = $row['module_name'];
}

// users assigned to this tenant
$tenant_users = [];
$stmt = $link->prepare("
    SELECT u.id, u.name, u.email, ut.role, ut.status
      FROM user_tenants ut
      JOIN users u ON ut.user_id = u.id
     WHERE ut.tenant_id = ?
");
$stmt->bind_param('i', $tenant_id);
$stmt->execute();
$tu = $stmt->get_result();
while ($row = $tu->fetch_assoc()) {
    $tenant_users[] = $row;
}
$stmt->close();
?>
<!-- ... rest of your HTML/UI here ... -->


<head>
    <title>Tenant Access Management | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
</head>
<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include 'layouts/menu.php'; ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <!-- Page Title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-flex align-items-center justify-content-between">
                                <h4 class="page-title mb-0">Tenant Access Management</h4>
                                <div>
                                    <a href="tenant-management.php" class="btn btn-outline-secondary btn-sm">
                                        <i class="ri-arrow-go-back-line"></i> Back to Tenants
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tenant Overview -->
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card mb-3">
                                <div class="card-body d-flex align-items-center">
                                    <div>
                                        <div class="d-flex align-items-center flex-wrap mb-2" style="gap:12px;">
                                            <h4 class="fw-bold mb-0"><?= htmlspecialchars($tenant['tenant_name'] ?? '') ?></h4>
                                            <span class="badge bg-primary"><?= htmlspecialchars($tenant['plan_name'] ?? '') ?></span>
                                            <span class="badge bg-success"><?= htmlspecialchars(ucfirst($tenant['billing_status'] ?? '')) ?></span>
                                        </div>
                                        <a href="#" class="btn btn-outline-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#editTenantModal">
                                            <i class="bi bi-pencil-square"></i> Edit Tenant
                                        </a>
                                        <a href="#" class="btn btn-outline-danger btn-sm mt-2 ms-2" data-bs-toggle="modal" data-bs-target="#deleteTenantModal">
                                            <i class="bi bi-trash"></i> Delete Tenant
                                        </a>
                                    </div>
                                    <div class="ms-auto d-flex flex-wrap gap-2">
                                        <div class="text-center px-3">
                                            <div class="fw-semibold small text-muted">Users</div>
                                            <div><?= $user_count ?><?= $user_limit ? " / $user_limit" : "" ?></div>
                                        </div>
                                        <div class="text-center px-3">
                                            <div class="fw-semibold small text-muted">Companies</div>
                                            <div><?= $company_count ?><?= $company_limit ? " / $company_limit" : "" ?></div>
                                        </div>
                                        <div class="text-center px-3">
                                            <div class="fw-semibold small text-muted">Storage</div>
                                            <div><?= $storage_used ?><?= $storage_limit ? " / $storage_limit" : "" ?></div>
                                        </div>
                                        <div class="text-center px-3">
                                            <div class="fw-semibold small text-muted">API Integrations</div>
                                            <div><?= $api_limit ?><?= $api_limit != '∞' ? '' : '' ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Module/Feature Access Table -->
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header bg-white fw-bold">Module Access Control</div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table align-middle table-bordered mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Module</th>
                                                    <th>Included in Plan</th>
                                                    <th>Enabled</th>
                                                    <th>View</th>
                                                    <th>Edit</th>
                                                    <th>Export</th>
                                                    <th>Delete</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($all_modules as $mid => $mname): 
                                                    $included = in_array($mname, $modules);
                                                ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($mname) ?></td>
                                                    <td>
                                                        <?php if ($included): ?>
                                                            <span class="badge bg-success">✔</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">✘</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" <?= $included ? 'checked' : 'disabled' ?>>
                                                        </div>
                                                    </td>
                                                    <td><input type="checkbox" class="form-check-input" <?= $included ? 'checked' : 'disabled' ?>></td>
                                                    <td><input type="checkbox" class="form-check-input" <?= $included ? 'checked' : 'disabled' ?>></td>
                                                    <td><input type="checkbox" class="form-check-input" <?= $included ? '' : 'disabled' ?>></td>
                                                    <td><input type="checkbox" class="form-check-input" <?= $included ? '' : 'disabled' ?>></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- User Management Table -->
                    <div class="row">
                        <div class="col-lg-8">
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
                                                                    <!-- EDIT BUTTON: opens a pop-up to edit -->
                                                                    <button type="button"
                                                                            class="btn btn-sm btn-outline-secondary"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#editUserModal"
                                                                            data-user-id="<?= $user['id'] ?>"
                                                                            data-user-name="<?= htmlspecialchars($user['name']) ?>"
                                                                            data-user-email="<?= htmlspecialchars($user['email']) ?>"
                                                                            data-user-role="<?= htmlspecialchars($user['role']) ?>"
                                                                            data-user-status="<?= htmlspecialchars($user['status']) ?>">
                                                                        Edit
                                                                    </button>
                                                                    <!-- REMOVE BUTTON: removes the user -->
                                                                    <form method="post" action="?tenant_id=<?= $tenant_id ?>" class="d-inline" onsubmit="return confirm('Remove this user from tenant?');">
                                                                        <input type="hidden" name="remove_user_id" value="<?= $user['id'] ?>">
                                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                                                    </form>
                                                                </td>

                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="p-3">
                                        <a href="tenant-users.php" class="btn btn-outline-primary">
                                            Invite User
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- container -->
            </div> <!-- content -->
            <?php include 'layouts/footer.php'; ?>
        </div>
    </div>


<!-- Edit Tenant Modal -->
<div class="modal fade" id="editTenantModal" tabindex="-1" aria-labelledby="editTenantModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="editTenantModalLabel">Edit Tenant</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label">Tenant Name</label>
            <input type="text" name="tenant_name" class="form-control" required value="<?= htmlspecialchars($tenant['tenant_name'] ?? '') ?>">
          </div>
          <div class="mb-2">
            <label class="form-label">Plan</label>
            <select name="plan_id" class="form-select" required>
              <?php
                $res = mysqli_query($link, "SELECT id, plan_name FROM plans WHERE status='active' ORDER BY id ASC");
                while ($row = mysqli_fetch_assoc($res)) {
                  $selected = ($tenant['plan_id'] == $row['id']) ? 'selected' : '';
                  echo "<option value='{$row['id']}' $selected>" . htmlspecialchars($row['plan_name']) . "</option>";
                }
              ?>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label">Status</label>
            <select name="billing_status" class="form-select" required>
              <?php
                $statuses = ['active'=>'Active','trial'=>'Trial','past_due'=>'Past Due','cancelled'=>'Cancelled'];
                foreach ($statuses as $val => $label) {
                  $selected = ($tenant['billing_status'] == $val) ? 'selected' : '';
                  echo "<option value='$val' $selected>$label</option>";
                }
              ?>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label">Contact Email</label>
            <input type="email" name="contact_email" class="form-control" value="<?= htmlspecialchars($tenant['contact_email'] ?? '') ?>">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="edit_tenant" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Tenant Modal -->
<div class="modal fade" id="deleteTenantModal" tabindex="-1" aria-labelledby="deleteTenantModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteTenantModalLabel">Delete Tenant</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to <strong>permanently delete</strong> this tenant and all related data? This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="delete_tenant" class="btn btn-danger">Delete Tenant</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Invite User Modal -->
<div class="modal fade" id="inviteUserModal" tabindex="-1" aria-labelledby="inviteUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="?tenant_id=<?= $tenant_id ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="inviteUserModalLabel">Assign User to Tenant</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label">Select User</label>
            <select name="user_id" class="form-select" required>
              <option value="">-- Select User --</option>
              <?php
              // Fetch all users not yet assigned to this tenant
              $assigned_ids = array_column($tenant_users, 'id');
              $all_users_q = mysqli_query($link, "SELECT id, name, email FROM users");
              while ($u = mysqli_fetch_assoc($all_users_q)) {
                  if (!in_array($u['id'], $assigned_ids)) {
                      echo '<option value="'.$u['id'].'">'.htmlspecialchars($u['name'].' ('.$u['email'].')').'</option>';
                  }
              }
              ?>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
              <option value="owner">Owner</option>
              <option value="admin">Admin</option>
              <option value="user" selected>User</option>
              <option value="billing">Billing</option>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
              <option value="active" selected>Active</option>
              <option value="invited">Invited</option>
              <option value="suspended">Suspended</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="assign_user" class="btn btn-primary">Assign User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="?tenant_id=<?= $tenant_id ?>">
        <input type="hidden" name="edit_user_id" id="edit_user_id" value="">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel">Edit Tenant User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" id="edit_user_name" readonly>
          </div>
          <div class="mb-2">
            <label class="form-label">Email</label>
            <input type="text" class="form-control" id="edit_user_email" readonly>
          </div>
          <div class="mb-2">
            <label class="form-label">Role</label>
            <select name="edit_role" id="edit_role" class="form-select" required>
              <option value="owner">Owner</option>
              <option value="admin">Admin</option>
              <option value="user">User</option>
              <option value="billing">Billing</option>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label">Status</label>
            <select name="edit_status" id="edit_status" class="form-select" required>
              <option value="active">Active</option>
              <option value="invited">Invited</option>
              <option value="suspended">Suspended</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var editUserModal = document.getElementById('editUserModal');
    editUserModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('edit_user_id').value = button.getAttribute('data-user-id');
        document.getElementById('edit_user_name').value = button.getAttribute('data-user-name');
        document.getElementById('edit_user_email').value = button.getAttribute('data-user-email');
        document.getElementById('edit_role').value = button.getAttribute('data-user-role');
        document.getElementById('edit_status').value = button.getAttribute('data-user-status');
    });
});
</script>

    <!-- END wrapper -->
    <?php include 'layouts/right-sidebar.php'; ?>
    <?php include 'layouts/footer-scripts.php'; ?>
    <script src="assets/js/app.min.js"></script>
</body>
</html>
