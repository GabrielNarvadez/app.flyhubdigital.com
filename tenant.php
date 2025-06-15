<?php
require_once __DIR__ . '/layouts/config.php';

// Handle adding new tenant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_tenant'])) {
    $sql = "INSERT INTO tenants 
        (tenant_name, slug, contact_email, contact_phone, domain, plan_id, billing_status, is_white_label, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $link->prepare($sql);
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $_POST['tenant_name']));
    $is_white_label = isset($_POST['is_white_label']) ? 1 : 0;
    $stmt->bind_param(
        "ssssissi",
        $_POST['tenant_name'],
        $slug,
        $_POST['contact_email'],
        $_POST['contact_phone'],
        $_POST['domain'],
        $_POST['plan_id'],
        $_POST['billing_status'],
        $is_white_label
    );
    $stmt->execute();
    $stmt->close();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_statuses']) && isset($_POST['tenant_status'])) {
    foreach ($_POST['tenant_status'] as $tenant_id => $new_status) {
        $tenant_id = intval($tenant_id);
        $sql = "UPDATE tenants SET billing_status=? WHERE id=?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("si", $new_status, $tenant_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Fetch plans for dropdown
$plans = [];
$plan_result = mysqli_query($link, "SELECT id, plan_name FROM plans WHERE status='active' ORDER BY plan_name ASC");
while ($row = mysqli_fetch_assoc($plan_result)) {
    $plans[$row['id']] = $row['plan_name'];
}

// Fetch tenants for table
$sql = "
    SELECT 
        t.id AS tenant_id,
        t.tenant_name,
        t.slug,
        t.logo_url,
        t.primary_color,
        t.secondary_color,
        t.domain,
        t.contact_email,
        t.contact_phone,
        t.billing_status,
        t.is_white_label,
        t.plan_id,
        t.created_at,
        p.plan_name
    FROM tenants t
    LEFT JOIN plans p ON p.id = t.plan_id
    ORDER BY t.created_at DESC
";
$result = mysqli_query($link, $sql);
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-11 col-lg-12">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                        <h3 class="fw-bold mb-0">Tenants</h3>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTenantModal">
                            <i class="bi bi-plus-circle"></i> Add Tenant
                        </button>
                    </div>
                    <form method="post">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Logo</th>
                                        <th>Name</th>
                                        <th>Domain</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Plan</th>
                                        <th>Status</th>
                                        <th>White Label?</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result && mysqli_num_rows($result) > 0):
                                        while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($row['logo_url'])): ?>
                                                    <img src="<?= htmlspecialchars($row['logo_url']) ?>" alt="Logo" style="width:36px;height:36px;border-radius:8px;background:#f3f3f3;">
                                                <?php else: ?>
                                                    <span class="text-muted fst-italic">No logo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="fw-semibold"><?= htmlspecialchars($row['tenant_name']) ?></span>
                                                <div class="small text-muted"><?= htmlspecialchars($row['slug']) ?></div>
                                            </td>
                                            <td>
                                                <?php if (!empty($row['domain'])): ?>
                                                    <a href="https://<?= htmlspecialchars($row['domain']) ?>" target="_blank"><?= htmlspecialchars($row['domain']) ?></a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($row['contact_email']) ?></td>
                                            <td><?= htmlspecialchars($row['contact_phone']) ?></td>
                                            <td>
                                                <?= htmlspecialchars($row['plan_name'] ?? '-') ?>
                                            </td>
                                            <td>
                                                <select name="tenant_status[<?= $row['tenant_id'] ?>]" class="form-select form-select-sm">
                                                    <?php
                                                    $statuses = ['active' => 'Active', 'trial' => 'Trial', 'past_due' => 'Past Due', 'cancelled' => 'Cancelled'];
                                                    foreach ($statuses as $val => $label) {
                                                        $selected = ($row['billing_status'] === $val) ? 'selected' : '';
                                                        echo "<option value=\"$val\" $selected>$label</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <?php if ($row['is_white_label']): ?>
                                                    <span class="badge bg-primary">White Label</span>
                                                <?php else: ?>
                                                    <span class="text-muted">No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
                                        </tr>
                                    <?php endwhile;
                                    else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">No tenants found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <button type="submit" name="save_statuses" class="btn btn-success">
                                <i class="bi bi-save"></i> Save Status Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Tenant Modal -->
<div class="modal fade" id="addTenantModal" tabindex="-1" aria-labelledby="addTenantModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content rounded-4">
      <form method="post">
        <input type="hidden" name="add_tenant" value="1">
        <div class="modal-header">
          <h5 class="modal-title fw-semibold" id="addTenantModalLabel">Add New Tenant</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body row g-3">
          <div class="col-md-6">
            <label class="form-label">Company Name</label>
            <input type="text" name="tenant_name" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Contact Email</label>
            <input type="email" name="contact_email" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Contact Phone</label>
            <input type="text" name="contact_phone" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Domain</label>
            <input type="text" name="domain" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Plan</label>
            <select name="plan_id" class="form-select">
              <option value="">Select plan</option>
              <?php foreach ($plans as $pid => $pname): ?>
                  <option value="<?= $pid ?>"><?= htmlspecialchars($pname) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Status</label>
            <select name="billing_status" class="form-select">
                <option value="active">Active</option>
                <option value="trial">Trial</option>
                <option value="past_due">Past Due</option>
                <option value="cancelled">Cancelled</option>
            </select>
          </div>
          <div class="col-md-6">
            <div class="form-check mt-4">
              <input class="form-check-input" type="checkbox" value="1" id="is_white_label" name="is_white_label">
              <label class="form-check-label" for="is_white_label">
                White Label
              </label>
            </div>
          </div>
        </div>
        <div class="modal-footer mt-4">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Tenant</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Bootstrap Icons CDN and JS for modal functionality -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
