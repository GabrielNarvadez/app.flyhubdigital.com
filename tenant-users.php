<?php
include 'layouts/session.php';
include 'layouts/main.php';
require_once 'layouts/config.php';

// Get tenant_id from URL
$tenant_id = isset($_GET['tenant_id']) ? intval($_GET['tenant_id']) : 0;
if (!$tenant_id) {
    header("Location: access-management.php");
    exit;
}

// Fetch tenant info for header
$sql = "SELECT tenant_name FROM tenants WHERE id = $tenant_id LIMIT 1";
$tenant = mysqli_fetch_assoc(mysqli_query($link, $sql));
if (!$tenant) die("Tenant not found");

// Handle assign user form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_user'])) {
    $user_id = intval($_POST['user_id']);
    $role = $_POST['role'];
    $status = $_POST['status'];

    // Check if user is already assigned to tenant
    $check_sql = "SELECT COUNT(*) as cnt FROM user_tenants WHERE tenant_id = ? AND user_id = ?";
    $stmt = $link->prepare($check_sql);
    $stmt->bind_param("ii", $tenant_id, $user_id);
    $stmt->execute();
    $stmt->bind_result($cnt);
    $stmt->fetch();
    $stmt->close();

    if ($cnt == 0) {
        $insert_sql = "INSERT INTO user_tenants (tenant_id, user_id, role, status) VALUES (?, ?, ?, ?)";
        $stmt = $link->prepare($insert_sql);
        $stmt->bind_param("iiss", $tenant_id, $user_id, $role, $status);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: tenant-users.php?tenant_id=$tenant_id");
    exit;
}

// Handle delete user assignment
if (isset($_GET['action'], $_GET['tenant_id'], $_GET['user_id']) &&
    $_GET['action'] === 'delete') {

    $tenant_id = intval($_GET['tenant_id']);
    $user_id = intval($_GET['user_id']);

    $sql = "DELETE FROM user_tenants WHERE tenant_id = ? AND user_id = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("ii", $tenant_id, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: tenant-users.php?tenant_id=$tenant_id");
    exit;
}

// Handle edit user assignment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user_assignment'])) {
    $tenant_id = intval($_POST['tenant_id']);
    $user_id = intval($_POST['user_id']);
    $role = $_POST['role'];
    $status = $_POST['status'];

    $sql = "UPDATE user_tenants SET role = ?, status = ? WHERE tenant_id = ? AND user_id = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("ssii", $role, $status, $tenant_id, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: tenant-users.php?tenant_id=$tenant_id");
    exit;
}


// Fetch users assigned to this tenant
$sql = "
    SELECT u.id, u.name, u.email, ut.role, ut.status
    FROM users u
    JOIN user_tenants ut ON u.id = ut.user_id
    WHERE ut.tenant_id = $tenant_id
    ORDER BY u.name
";
$result = mysqli_query($link, $sql);
$tenant_users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $tenant_users[] = $row;
}

// Fetch all users NOT assigned to this tenant for the dropdown
$assigned_user_ids = array_column($tenant_users, 'id');
$all_users = [];
$user_sql = "SELECT id, name, email FROM users";
$res = mysqli_query($link, $user_sql);
while ($user = mysqli_fetch_assoc($res)) {
    if (!in_array($user['id'], $assigned_user_ids)) {
        $all_users[] = $user;
    }
}
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
