<?php
require_once __DIR__ . '/layouts/config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    echo '<div class="alert alert-danger">No contact specified.</div>';
    exit;
}

$q = $link->prepare("SELECT * FROM contacts WHERE id=?");
$q->bind_param('i', $id);
$q->execute();
$res = $q->get_result();
$contact = $res->fetch_assoc();
$q->close();

if (!$contact) {
    echo '<div class="alert alert-danger">Contact not found.</div>';
    exit;
}

$edit_mode = false;
$edit_msg = '';

// If edit form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_profile'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $position = trim($_POST['position']);
    $company_name = trim($_POST['company_name']);
    $phone = trim($_POST['phone_number']);
    $email = trim($_POST['email']);

    $stmt = $link->prepare("UPDATE contacts SET first_name=?, last_name=?, position=?, company_name=?, phone_number=?, email=? WHERE id=?");
    $stmt->bind_param("ssssssi", $first_name, $last_name, $position, $company_name, $phone, $email, $id);
    $stmt->execute();
    $stmt->close();
    $edit_msg = "Profile updated!";

    // Reload updated profile
    $q = $link->prepare("SELECT * FROM contacts WHERE id=?");
    $q->bind_param('i', $id);
    $q->execute();
    $res = $q->get_result();
    $contact = $res->fetch_assoc();
    $q->close();
}

// Enable edit mode if URL says so or Save pressed
if ((isset($_GET['edit']) && $_GET['edit'] == 1) || (isset($_POST['edit_mode']) && $_POST['edit_mode'] == 1)) {
    $edit_mode = true;
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

<div class="container-fluid py-5">
  <div class="row g-4">
    <!-- Profile Details (Left) -->
    <div class="col-lg-3">
      <div class="card shadow-sm border-0 rounded-4 text-center mb-4">
        <div class="card-body">
          <?php if ($edit_msg): ?>
            <div class="alert alert-success"><?= $edit_msg ?></div>
          <?php endif; ?>

          <?php if (!$edit_mode): ?>
            <h4 class="mb-2"><?= htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']) ?></h4>
            <div class="mb-2 text-muted"><?= htmlspecialchars($contact['position'] ?? '') ?></div>
            <div class="mb-2"><i class="ri-building-2-line me-1"></i><?= htmlspecialchars($contact['company_name']) ?></div>
            <div class="mb-2"><i class="ri-phone-line me-1"></i><?= htmlspecialchars($contact['phone_number']) ?></div>
            <div class="mb-2"><i class="ri-mail-line me-1"></i><?= htmlspecialchars($contact['email']) ?></div>
            <div class="mb-2 small text-muted">Added: <?= htmlspecialchars(date('Y-m-d', strtotime($contact['created_at']))) ?></div>
            <div class="mb-2 small text-muted">Updated: <?= htmlspecialchars(date('Y-m-d', strtotime($contact['updated_at']))) ?></div>
            <form method="get" class="mt-3">
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="hidden" name="edit" value="1">
                <button class="btn btn-outline-primary w-100" type="submit"><i class="ri-edit-line me-1"></i>Edit</button>
            </form>
          <?php else: ?>
            <form method="post" class="text-start">
              <input type="hidden" name="edit_profile" value="1">
              <input type="hidden" name="edit_mode" value="1">
              <label class="form-label fw-semibold">Full Name</label>
              <div class="input-group mb-2">
                <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($contact['first_name']) ?>" required>
                <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($contact['last_name']) ?>" required>
              </div>
              <label class="form-label fw-semibold">Position</label>
              <input type="text" name="position" class="form-control mb-2" value="<?= htmlspecialchars($contact['position'] ?? '') ?>">
              <label class="form-label fw-semibold">Company</label>
              <input type="text" name="company_name" class="form-control mb-2" value="<?= htmlspecialchars($contact['company_name']) ?>">
              <label class="form-label fw-semibold">Phone</label>
              <input type="text" name="phone_number" class="form-control mb-2" value="<?= htmlspecialchars($contact['phone_number']) ?>">
              <label class="form-label fw-semibold">Email</label>
              <input type="email" name="email" class="form-control mb-2" value="<?= htmlspecialchars($contact['email']) ?>">
              <button class="btn btn-primary w-100 mt-2" type="submit"><i class="ri-save-2-line me-1"></i>Save Changes</button>
              <a href="single-contact.php?id=<?= $id ?>" class="btn btn-link w-100 mt-1">Cancel</a>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <!-- Timeline Activities (Center - blank) -->
    <div class="col-lg-6">
      <div class="card shadow-sm border-0 rounded-4 mb-4" style="min-height:350px;">
        <div class="card-body"></div>
      </div>
    </div>
    <!-- Associations (Right - blank) -->
    <div class="col-lg-3">
      <div class="card shadow-sm border-0 rounded-4 mb-4" style="min-height:350px;">
        <div class="card-body"></div>
      </div>
    </div>
  </div>
</div>
