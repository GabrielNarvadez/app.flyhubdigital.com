<?php
include 'layouts/session.php';
include 'layouts/main.php';
require_once 'layouts/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_tenant'])) {
    $name = trim($_POST['tenant_name']);
    $plan_id = intval($_POST['plan_id']);
    $status = $_POST['billing_status'];
    $email = trim($_POST['contact_email']);

    $sql = "INSERT INTO tenants (tenant_name, plan_id, billing_status, contact_email, created_at)
            VALUES (?, ?, ?, ?, NOW())";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("siss", $name, $plan_id, $status, $email);
    $stmt->execute();
    $stmt->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch all tenants and their plans/status in one go
$sql = "
    SELECT 
        t.id AS tenant_id,
        t.tenant_name,
        t.plan_id,
        t.billing_status,
        t.created_at,
        p.plan_name
    FROM tenants t
    LEFT JOIN plans p ON t.plan_id = p.id
    ORDER BY t.created_at DESC
";
$tenants = [];
$result = mysqli_query($link, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $tenants[$row['tenant_id']] = $row;
}

// Get user counts per tenant
$user_counts = [];
$res = mysqli_query($link, "SELECT tenant_id, COUNT(*) AS total FROM users GROUP BY tenant_id");
while ($row = mysqli_fetch_assoc($res)) $user_counts[$row['tenant_id']] = $row['total'];

// Get company counts per tenant
$company_counts = [];
$res = mysqli_query($link, "SELECT tenant_id, COUNT(*) AS total FROM companies GROUP BY tenant_id");
while ($row = mysqli_fetch_assoc($res)) $company_counts[$row['tenant_id']] = $row['total'];

// Get modules per plan (join for speed)
$modules_per_plan = [];
$res = mysqli_query($link, "
    SELECT pm.plan_id, m.module_name 
    FROM plan_modules pm
    JOIN modules m ON m.id = pm.module_id
    ORDER BY m.module_name
");
while ($row = mysqli_fetch_assoc($res)) {
    $modules_per_plan[$row['plan_id']][] = $row['module_name'];
}
?>
<head>
    <title>Access Management | Flyhub Business Apps</title>
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
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="page-title">Access Management</h4>
                            </div>
                        </div>
                    </div>
                    <!-- Tenants Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                                    <span>Tenants</span>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTenantModal">
                                        <i class="bi bi-plus-circle"></i> Add Tenant
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table align-middle table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Tenant Name</th>
                                                    <th>Plan/Tier</th>
                                                    <th>Status</th>
                                                    <th>Users</th>
                                                    <th>Clients/Companies</th>
                                                    <th>Modules Enabled</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($tenants)): ?>
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted">No tenants found.</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($tenants as $tenant_id => $tenant): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($tenant['tenant_name']) ?></td>
                                                            <td>
                                                                <span class="badge bg-primary"><?= htmlspecialchars($tenant['plan_name'] ?? '-') ?></span>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $bs = strtolower($tenant['billing_status']);
                                                                $status_class = $bs === 'active' ? 'success' : ($bs === 'trial' ? 'warning' : ($bs === 'past_due' ? 'danger' : 'secondary'));
                                                                ?>
                                                                <span class="badge bg-<?= $status_class ?>">
                                                                    <?= ucfirst($tenant['billing_status']) ?>
                                                                </span>
                                                            </td>
                                                            <td><?= intval($user_counts[$tenant_id] ?? 0) ?></td>
                                                            <td><?= intval($company_counts[$tenant_id] ?? 0) ?></td>
                                                            <td>
                                                                <?php
                                                                $modules = $modules_per_plan[$tenant['plan_id']] ?? [];
                                                                if (!$modules) echo '<span class="text-muted small">None</span>';
                                                                else foreach ($modules as $mod) {
                                                                    echo '<span class="badge bg-info me-1">' . htmlspecialchars($mod) . '</span>';
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <a href="tenant-access.php?tenant_id=<?= $tenant_id ?>" class="btn btn-sm btn-outline-primary">Manage</a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div><!-- table-responsive -->
                                </div><!-- card-body -->
                            </div><!-- card -->
                        </div><!-- col -->
                    </div><!-- row -->
                </div> <!-- container -->
            </div> <!-- content -->
            <?php include 'layouts/footer.php'; ?>
        </div>
    </div>

<!-- Add Tenant Modal -->
<div class="modal fade" id="addTenantModal" tabindex="-1" aria-labelledby="addTenantModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="addTenantModalLabel">Add New Tenant</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label">Tenant Name</label>
            <input type="text" name="tenant_name" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Plan</label>
            <select name="plan_id" class="form-select" required>
              <option value="">Select plan</option>
              <?php
                $res = mysqli_query($link, "SELECT id, plan_name FROM plans WHERE status='active' ORDER BY id ASC");
                while ($row = mysqli_fetch_assoc($res)) {
                  echo "<option value='{$row['id']}'>" . htmlspecialchars($row['plan_name']) . "</option>";
                }
              ?>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label">Status</label>
            <select name="billing_status" class="form-select" required>
              <option value="active">Active</option>
              <option value="trial">Trial</option>
              <option value="past_due">Past Due</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label">Contact Email</label>
            <input type="email" name="contact_email" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="add_tenant" class="btn btn-primary">Add Tenant</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- END Add Tenant Modal -->

    <!-- END wrapper -->
    <?php include 'layouts/right-sidebar.php'; ?>
    <?php include 'layouts/footer-scripts.php'; ?>
    <script src="assets/js/app.min.js"></script>
</body>
</html>
