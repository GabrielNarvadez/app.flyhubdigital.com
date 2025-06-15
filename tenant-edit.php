<?php
require_once __DIR__ . '/layouts/config.php';

// For demo: get tenant ID from URL
$tenant_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Fetch tenant data
$tenant = [
    'tenant_name' => '', 'logo_url' => '', 'primary_color' => '',
    'secondary_color' => '', 'domain' => '', 'contact_email' => '',
    'contact_phone' => '', 'website' => ''
];
$sql = "SELECT * FROM tenants WHERE id = ?";
$stmt = $link->prepare($sql);
$stmt->bind_param("i", $tenant_id);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) $tenant = $row;
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // File upload handling for logo
    $logo_url = $tenant['logo_url'];
    if (!empty($_FILES['logo_file']['name'])) {
        $upload_dir = __DIR__ . '/uploads/tenants/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename = 'logo_' . $tenant_id . '_' . time() . '.' . pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION);
        $target_file = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $target_file)) {
            $logo_url = '/uploads/tenants/' . $filename;
            // Optionally, delete old logo here if you want (not covered for brevity)
        }
    } elseif (!empty($_POST['logo_url'])) {
        $logo_url = $_POST['logo_url'];
    }

    // Prepare update
    $sql = "UPDATE tenants SET tenant_name=?, logo_url=?, primary_color=?, secondary_color=?, domain=?, contact_email=?, contact_phone=?, website=? WHERE id=?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param(
        "ssssssssi",
        $_POST['tenant_name'],
        $logo_url,
        $_POST['primary_color'],
        $_POST['secondary_color'],
        $_POST['domain'],
        $_POST['contact_email'],
        $_POST['contact_phone'],
        $_POST['website'],
        $tenant_id
    );
    $stmt->execute();
    $stmt->close();
    // Optionally reload the page to see changes or show a success message
    header("Location: tenant-edit.php?id=$tenant_id&updated=1");
    exit;
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-body p-4">
                    <h3 class="mb-4 fw-bold">Edit Tenant Profile</h3>
                    <?php if (isset($_GET['updated'])): ?>
                        <div class="alert alert-success">Profile updated!</div>
                    <?php endif; ?>
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label fw-semibold">Company Name</label>
                            <div class="col-sm-9">
                                <input type="text" name="tenant_name" class="form-control" required value="<?= htmlspecialchars($tenant['tenant_name']) ?>">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label fw-semibold">Logo</label>
                            <div class="col-sm-3">
                                <?php if ($tenant['logo_url']): ?>
                                    <img src="<?= htmlspecialchars($tenant['logo_url']) ?>" class="img-thumbnail mb-2" style="max-width:80px;">
                                <?php else: ?>
                                    <span class="text-muted">No logo</span>
                                <?php endif; ?>
                            </div>
                            <div class="col-sm-6">
                                <input type="file" name="logo_file" class="form-control mb-2" accept="image/*">
                                <div class="form-text">Or enter image URL below</div>
                                <input type="text" name="logo_url" class="form-control" placeholder="https://..." value="<?= htmlspecialchars($tenant['logo_url']) ?>">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label fw-semibold">Primary Color</label>
                            <div class="col-sm-3">
                                <input type="color" name="primary_color" class="form-control form-control-color" value="<?= htmlspecialchars($tenant['primary_color']) ?>">
                            </div>
                            <label class="col-sm-3 col-form-label fw-semibold">Secondary Color</label>
                            <div class="col-sm-3">
                                <input type="color" name="secondary_color" class="form-control form-control-color" value="<?= htmlspecialchars($tenant['secondary_color']) ?>">
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label fw-semibold">Domain</label>
                            <div class="col-sm-9">
                                <input type="text" name="domain" class="form-control" value="<?= htmlspecialchars($tenant['domain']) ?>">
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label fw-semibold">Website</label>
                            <div class="col-sm-9">
                                <input type="text" name="website" class="form-control" value="<?= htmlspecialchars($tenant['website']) ?>">
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label fw-semibold">Contact Email</label>
                            <div class="col-sm-9">
                                <input type="email" name="contact_email" class="form-control" value="<?= htmlspecialchars($tenant['contact_email']) ?>">
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label fw-semibold">Contact Phone</label>
                            <div class="col-sm-9">
                                <input type="text" name="contact_phone" class="form-control" value="<?= htmlspecialchars($tenant['contact_phone']) ?>">
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
