<?php
require_once __DIR__ . '/layouts/config.php';

// ADD/EDIT CONTACT
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_contact'])) {
    $first_name  = trim($_POST['first_name']);
    $last_name   = trim($_POST['last_name']);
    $email       = trim($_POST['email']);
    $phone       = trim($_POST['phone_number']);
    $company_id  = $_POST['company_id'] ? intval($_POST['company_id']) : null;
    $company_name= trim($_POST['company_name']);

    if ($_POST['contact_id']) {
        $stmt = $link->prepare("UPDATE contacts SET first_name=?, last_name=?, email=?, phone_number=?, company_id=?, company_name=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param('ssssisi', $first_name, $last_name, $email, $phone, $company_id, $company_name, $_POST['contact_id']);
        $stmt->execute();
        $stmt->close();
        $msg = "Contact updated!";
    } else {
        $stmt = $link->prepare("INSERT INTO contacts (first_name, last_name, email, phone_number, company_id, company_name, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param('ssssis', $first_name, $last_name, $email, $phone, $company_id, $company_name);
        $stmt->execute();
        $stmt->close();
        $msg = "Contact added!";
    }
}

// DELETE CONTACT
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $link->query("DELETE FROM contacts WHERE id=$id");
    $msg = "Contact deleted!";
}

// EXPORT CSV
if (isset($_GET['export_csv'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=contacts_export.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Name', 'Email', 'Phone', 'Company', 'Created At']);
    $res = mysqli_query($link, "SELECT * FROM contacts");
    while ($row = mysqli_fetch_assoc($res)) {
        fputcsv($out, [
            $row['first_name'] . ' ' . $row['last_name'],
            $row['email'],
            $row['phone_number'],
            $row['company_name'],
            $row['created_at']
        ]);
    }
    fclose($out);
    exit;
}

// IMPORT CSV
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['import_csv']['tmp_name'])) {
    $f = fopen($_FILES['import_csv']['tmp_name'], 'r');
    fgetcsv($f); // header
    while ($row = fgetcsv($f)) {
        [$name, $email, $phone, $company_name, $created_at] = $row;
        $parts = explode(' ', $name, 2);
        $first_name = $parts[0];
        $last_name = $parts[1] ?? '';
        $stmt = $link->prepare("INSERT INTO contacts (first_name, last_name, email, phone_number, company_name, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param('sssss', $first_name, $last_name, $email, $phone, $company_name);
        $stmt->execute();
        $stmt->close();
    }
    fclose($f);
    $msg = "Contacts imported!";
}

// FILTER/SEARCH
$filter = $_GET['filter'] ?? '';
$where = '';
if ($filter) {
    $s = mysqli_real_escape_string($link, $filter);
    $where = "WHERE (CONCAT(first_name, ' ', last_name) LIKE '%$s%' OR email LIKE '%$s%' OR phone_number LIKE '%$s%' OR company_name LIKE '%$s%')";
}
$res = mysqli_query($link, "SELECT * FROM contacts $where ORDER BY created_at DESC");

// For edit
$edit = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $q = mysqli_query($link, "SELECT * FROM contacts WHERE id=$id");
    if ($q && $row = mysqli_fetch_assoc($q)) $edit = $row;
}

// Optionally, for company dropdown (if you have a companies table)
$company_opt = [];
$q = @mysqli_query($link, "SELECT id, company_name FROM companies ORDER BY company_name ASC");
if ($q) while ($r = mysqli_fetch_assoc($q)) $company_opt[$r['id']] = $r['company_name'];
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container py-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Contacts</h3>
    <div class="d-flex gap-2">
      <form method="get" class="d-flex">
        <input type="text" class="form-control form-control-sm me-1" name="filter" value="<?= htmlspecialchars($filter) ?>" placeholder="Search...">
        <button class="btn btn-outline-secondary btn-sm">Search</button>
      </form>
      <form method="get" class="d-inline">
        <input type="hidden" name="export_csv" value="1">
        <button class="btn btn-outline-primary btn-sm" type="submit">Export CSV</button>
      </form>
      <form method="post" enctype="multipart/form-data" class="d-inline">
        <input type="file" name="import_csv" accept=".csv" class="form-control form-control-sm d-inline" style="width:150px;display:inline;">
        <button class="btn btn-outline-success btn-sm" type="submit">Import CSV</button>
      </form>
      <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">Print</button>
    </div>
  </div>
  <?php if ($msg): ?>
    <div class="alert alert-info"><?= $msg ?></div>
  <?php endif; ?>

  <!-- ADD/EDIT FORM -->
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
      <form method="post">
        <input type="hidden" name="save_contact" value="1">
        <input type="hidden" name="contact_id" value="<?= $edit['id'] ?? '' ?>">
        <div class="row g-2 align-items-end">
          <div class="col-md-3">
            <label class="form-label">First Name</label>
            <input type="text" class="form-control" name="first_name" required value="<?= htmlspecialchars($edit['first_name'] ?? '') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Last Name</label>
            <input type="text" class="form-control" name="last_name" required value="<?= htmlspecialchars($edit['last_name'] ?? '') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($edit['email'] ?? '') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Phone</label>
            <input type="text" class="form-control" name="phone_number" value="<?= htmlspecialchars($edit['phone_number'] ?? '') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Company Name</label>
            <input type="text" class="form-control" name="company_name" value="<?= htmlspecialchars($edit['company_name'] ?? '') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Company (optional)</label>
            <select name="company_id" class="form-select">
              <option value="">--</option>
              <?php foreach ($company_opt as $cid => $cname): ?>
                <option value="<?= $cid ?>" <?= (isset($edit['company_id']) && $edit['company_id']==$cid) ? 'selected' : '' ?>><?= htmlspecialchars($cname) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <button class="btn btn-primary px-4" type="submit"><?= $edit ? 'Update' : 'Add' ?> Contact</button>
            <?php if ($edit): ?>
              <a href="contacts.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- CONTACTS TABLE -->
<div class="card border-0 shadow-sm rounded-4">
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Company Name</th>
            <th>Added</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php if ($res && mysqli_num_rows($res) > 0):
            while ($row = mysqli_fetch_assoc($res)): ?>
            <tr>
              <td>
                <!-- Update this line -->
                <a href="single-contact.php?id=<?= $row['id'] ?>" class="fw-semibold text-decoration-underline">
                  <?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?>
                </a>
              </td>
              <td>
                <!-- Update this line -->
                <a href="single-contact.php?id=<?= $row['id'] ?>" class="text-decoration-underline">
                  <?= htmlspecialchars($row['email']) ?>
                </a>
              </td>
              <td><?= htmlspecialchars($row['phone_number']) ?></td>
              <td><?= htmlspecialchars($row['company_name']) ?></td>
              <td><?= htmlspecialchars(date('Y-m-d', strtotime($row['created_at']))) ?></td>
              <td>
                <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="ri-edit-line"></i></a>
                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this contact?');"><i class="ri-delete-bin-2-line"></i></a>
              </td>
            </tr>
          <?php endwhile; else: ?>
            <tr>
              <td colspan="6" class="text-center text-muted">No contacts found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>


<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
