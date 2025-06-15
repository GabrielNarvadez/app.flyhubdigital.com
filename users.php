<?php
require_once __DIR__ . '/layouts/config.php';

// Simulate current admin
$current_user_id = 1;

// Handle Add User (same as before)
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = trim($_POST['role'] ?? 'user');
    $phone = trim($_POST['phone'] ?? '');
    $avatar = null;

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $fname = 'avatar_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        $target = __DIR__ . '/assets/img/avatars/' . $fname;
        if (!is_dir(__DIR__ . '/assets/img/avatars/')) {
            mkdir(__DIR__ . '/assets/img/avatars/', 0777, true);
        }
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
            $avatar = $fname;
        }
    }

    $status = 'active';
    $email_verified = 0;
    $created_at = date('Y-m-d H:i:s');
    $updated_at = $created_at;
    $password = password_hash('password123', PASSWORD_BCRYPT);

    $sql = "INSERT INTO users
        (name, email, password, role, avatar, phone, status, email_verified, created_at, updated_at, created_by, updated_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $link->prepare($sql);
    $stmt->bind_param(
        "ssssssssssii",
        $name, $email, $password, $role, $avatar, $phone, $status,
        $email_verified, $created_at, $updated_at, $current_user_id, $current_user_id
    );
    if ($stmt->execute()) {
        $msg = '<div class="alert alert-success mb-2">User added!</div>';
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $msg = '<div class="alert alert-danger mb-2">Error: '.$stmt->error.'</div>';
    }
    $stmt->close();
}

// Handle Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];

    // Fix: Use the correct column in your invoices table, e.g. created_by
    $own_any = false;
    $check_sql = "SELECT COUNT(*) FROM invoices WHERE created_by = ?";
    $check_stmt = $link->prepare($check_sql);
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $check_stmt->bind_result($owned_count);
    $check_stmt->fetch();
    $own_any = $owned_count > 0;
    $check_stmt->close();

    if (!$own_any) {
        $avatar_res = mysqli_query($link, "SELECT avatar FROM users WHERE id = $user_id");
        $avatar_row = mysqli_fetch_assoc($avatar_res);
        if ($avatar_row && !empty($avatar_row['avatar'])) {
            $avatar_file = __DIR__ . '/assets/img/avatars/' . $avatar_row['avatar'];
            if (file_exists($avatar_file)) unlink($avatar_file);
        }

        $stmt = $link->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        $msg = '<div class="alert alert-success mb-2">User deleted.</div>';
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $msg = '<div class="alert alert-warning mb-2">Cannot delete user: user owns records in the system.</div>';
    }
}

$sql = "SELECT id, name, email, role, avatar, phone, status, email_verified, last_login, created_at, updated_at, created_by FROM users ORDER BY id DESC";
$result = mysqli_query($link, $sql);
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4">
    <h4 class="mb-3">Add New User</h4>
    <?= $msg ?>
    <form method="post" enctype="multipart/form-data" class="row g-3 mb-4">
        <input type="hidden" name="add_user" value="1">
        <div class="col-md-4">
            <label class="form-label">Name</label>
            <input name="name" type="text" required class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Email</label>
            <input name="email" type="email" required class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">User Role</label>
            <select name="role" class="form-select" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
                <option value="manager">Manager</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Phone</label>
            <input name="phone" type="text" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Avatar Image</label>
            <input name="avatar" type="file" class="form-control" accept="image/*">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Add User</button>
        </div>
    </form>

    <h4 class="mb-3">User List</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Avatar</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Email Verified</th>
                    <th>Last Login</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Created By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <a href="user-profile.php?id=<?= urlencode($row['id']) ?>">
                                    <?= htmlspecialchars($row['name']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?></td>
                            <td>
                                <?php if (!empty($row['avatar'])): ?>
                                    <img src="/assets/img/avatars/<?= htmlspecialchars($row['avatar']) ?>" alt="avatar" width="40" height="40" class="rounded-circle">
                                <?php else: ?>
                                    <span class="text-muted">No Avatar</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['phone'] ?? '') ?></td>
                            <td>
                                <?php
                                $status = $row['status'] ?? 'active';
                                $badgeClass = 'secondary';
                                if ($status == 'active') $badgeClass = 'success';
                                elseif ($status == 'inactive') $badgeClass = 'warning';
                                elseif ($status == 'banned') $badgeClass = 'danger';
                                elseif ($status == 'pending') $badgeClass = 'info';
                                ?>
                                <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($status) ?></span>
                            </td>
                            <td>
                                <?= ($row['email_verified'] ?? 0) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?>
                            </td>
                            <td><?= htmlspecialchars($row['last_login'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                            <td><?= htmlspecialchars($row['updated_at'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['created_by'] ?? '') ?></td>
                            <td>
                                <button
                                    class="btn btn-sm btn-danger"
                                    onclick="deleteUser(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['name'])) ?>')"
                                >Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12" class="text-center text-muted">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function deleteUser(id, name) {
    if (confirm('Are you sure you want to delete "' + name + '"?\nThis action cannot be undone.')) {
        window.location = '?delete=' + id;
    }
}
</script>
