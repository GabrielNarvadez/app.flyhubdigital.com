<?php
// modules/contact-table.php

// Ensure session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../layouts/config.php';

// Initialize flash
if (empty($_SESSION['flash'])) {
    $_SESSION['flash'] = '';
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $flash = '';

    // CREATE
    if ($action === 'create') {
        $first_name   = mysqli_real_escape_string($link, trim($_POST['first_name']   ?? ''));
        $last_name    = mysqli_real_escape_string($link, trim($_POST['last_name']    ?? ''));
        $country      = mysqli_real_escape_string($link, trim($_POST['country']      ?? ''));
        $age          = intval($_POST['age']                                   ?? 0);
        $email        = mysqli_real_escape_string($link, trim($_POST['email']        ?? ''));
        $phone_number = mysqli_real_escape_string($link, trim($_POST['phone_number'] ?? ''));
        $address      = mysqli_real_escape_string($link, trim($_POST['address']      ?? ''));
        $company_id   = intval($_POST['company_id']                           ?? 0);

        if ($first_name && $last_name && $email) {
            $stmt = mysqli_prepare($link,
                "INSERT INTO contacts
                  (first_name,last_name,country,age,email,phone_number,address,company_id)
                 VALUES (?,?,?,?,?,?,?,?)"
            );
            mysqli_stmt_bind_param(
                $stmt,
                'sssisssi',
                $first_name,
                $last_name,
                $country,
                $age,
                $email,
                $phone_number,
                $address,
                $company_id
            );
            $flash = mysqli_stmt_execute($stmt)
                   ? 'Contact added successfully.'
                   : 'Error adding contact.';
            mysqli_stmt_close($stmt);
        } else {
            $flash = 'First name, last name and email are required.';
        }
    }

    // UPDATE
    if ($action === 'update') {
        $id           = intval($_POST['id']                                    ?? 0);
        $first_name   = mysqli_real_escape_string($link, trim($_POST['first_name']   ?? ''));
        $last_name    = mysqli_real_escape_string($link, trim($_POST['last_name']    ?? ''));
        $country      = mysqli_real_escape_string($link, trim($_POST['country']      ?? ''));
        $age          = intval($_POST['age']                                   ?? 0);
        $email        = mysqli_real_escape_string($link, trim($_POST['email']        ?? ''));
        $phone_number = mysqli_real_escape_string($link, trim($_POST['phone_number'] ?? ''));
        $address      = mysqli_real_escape_string($link, trim($_POST['address']      ?? ''));
        $company_id   = intval($_POST['company_id']                           ?? 0);

        if ($id && $first_name && $last_name && $email) {
            $stmt = mysqli_prepare($link,
                "UPDATE contacts SET
                  first_name   = ?,
                  last_name    = ?,
                  country      = ?,
                  age          = ?,
                  email        = ?,
                  phone_number = ?,
                  address      = ?,
                  company_id   = ?,
                  updated_at   = NOW()
                 WHERE id = ?"
            );
            mysqli_stmt_bind_param(
                $stmt,
                'sssisssii',
                $first_name,
                $last_name,
                $country,
                $age,
                $email,
                $phone_number,
                $address,
                $company_id,
                $id
            );
            $flash = mysqli_stmt_execute($stmt)
                   ? 'Contact updated successfully.'
                   : 'Error updating contact.';
            mysqli_stmt_close($stmt);
        } else {
            $flash = 'All required fields must be filled out.';
        }
    }

    // DELETE
    if ($action === 'delete' && !empty($_POST['ids'])) {
        $ids = array_map('intval', explode(',', $_POST['ids']));
        $in  = implode(',', $ids);
        $flash = mysqli_query($link, "DELETE FROM contacts WHERE id IN ($in)")
               ? 'Selected contact(s) deleted.'
               : 'Error deleting contacts.';
    }

    $_SESSION['flash'] = $flash;
    echo "<script>window.location.replace('".htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES)."');</script>";
    exit;
}

// Fetch contacts
$contacts = [];
$sql = "SELECT
          id, first_name, last_name, country, age,
          email, phone_number, address, company_id,
          created_at, updated_at
        FROM contacts
        ORDER BY first_name";
$res = mysqli_query($link, $sql);
if (!$res) {
    die("Query failed: " . mysqli_error($link));
}
while ($row = mysqli_fetch_assoc($res)) {
    $contacts[] = $row;
}
mysqli_free_result($res);
?>

<!-- Page title and flash -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="index.php">Flyhub Digital</a></li>
          <li class="breadcrumb-item active">Contacts</li>
        </ol>
      </div>
      <h4 class="page-title">Contact Management</h4>
    </div>
  </div>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-info"><?= $_SESSION['flash']; ?></div>
  <?php $_SESSION['flash'] = ''; ?>
<?php endif; ?>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h4 class="header-title mb-0">Contacts List</h4>
          <button id="btn-add" class="btn btn-success">+ Add Contact</button>
        </div>

        <!-- Bulk panel -->
        <div id="bulk-panel"
             class="d-flex align-items-center bg-light border rounded px-3 py-2 mb-3"
             style="display: none;">
          <small id="selected-count" class="me-3">0 contacts selected</small>
          <a href="#" id="select-all-link" class="me-3">
            Select all <span id="total-count"><?= count($contacts) ?></span> contacts
          </a>
          <a href="#" id="bulk-edit" class="me-3">Edit</a>
          <a href="#" id="bulk-delete" class="me-3 text-danger">Delete</a>
          <a href="#" id="bulk-create-tasks" class="me-3">Create tasks</a>
          <div class="dropdown">
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">Export</a></li>
              <li><a class="dropdown-item" href="#">Other</a></li>
            </ul>
          </div>
        </div>

        <!-- Bulk-delete form -->
        <form id="bulk-form" method="POST" style="display:none;">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="ids" id="bulk-ids">
        </form>

        <!-- DataTable -->
        <table id="contacts-table" class="table table-striped dt-responsive nowrap w-100">
          <thead>
            <tr>
              <th><input type="checkbox" id="select-all"></th>
              <th>ID</th>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Country</th>
              <th>Age</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Address</th>
              <th>Company ID</th>
              <th>Created At</th>
              <th>Updated At</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($contacts as $c): ?>
              <tr
                data-id="<?= $c['id'] ?>"
                data-first_name="<?= htmlspecialchars($c['first_name'],   ENT_QUOTES) ?>"
                data-last_name  ="<?= htmlspecialchars($c['last_name'],    ENT_QUOTES) ?>"
                data-country    ="<?= htmlspecialchars($c['country'],      ENT_QUOTES) ?>"
                data-age        ="<?= $c['age'] ?>"
                data-email      ="<?= htmlspecialchars($c['email'],        ENT_QUOTES) ?>"
                data-phone_number="<?= htmlspecialchars($c['phone_number'], ENT_QUOTES) ?>"
                data-address    ="<?= htmlspecialchars($c['address'],      ENT_QUOTES) ?>"
                data-company_id ="<?= $c['company_id'] ?>"
              >
                <td><input type="checkbox" class="row-checkbox"></td>
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['first_name']) ?></td>
                <td><?= htmlspecialchars($c['last_name'])  ?></td>
                <td><?= htmlspecialchars($c['country'])    ?></td>
                <td><?= $c['age'] ?></td>
                <td><?= htmlspecialchars($c['email'])      ?></td>
                <td><?= htmlspecialchars($c['phone_number'])?></td>
                <td><?= htmlspecialchars($c['address'])    ?></td>
                <td><?= $c['company_id'] ?></td>
                <td><?= $c['created_at'] ?></td>
                <td><?= $c['updated_at'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

      </div>
    </div>
  </div>
</div>

<!-- Offcanvas form -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="userCanvas">
  <div class="offcanvas-header">
    <h5 id="canvas-title">Add Contact</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <form id="user-form" method="POST">
      <input type="hidden" name="action" id="form-action" value="create">
      <input type="hidden" name="id"     id="form-id">

      <div class="mb-3">
        <label for="form-first_name" class="form-label">First Name</label>
        <input type="text" class="form-control" id="form-first_name" name="first_name" required>
      </div>
      <div class="mb-3">
        <label for="form-last_name" class="form-label">Last Name</label>
        <input type="text" class="form-control" id="form-last_name" name="last_name" required>
      </div>
      <div class="mb-3">
        <label for="form-country" class="form-label">Country</label>
        <input type="text" class="form-control" id="form-country" name="country">
      </div>
      <div class="mb-3">
        <label for="form-age" class="form-label">Age</label>
        <input type="number" class="form-control" id="form-age" name="age" min="0">
      </div>
      <div class="mb-3">
        <label for="form-email" class="form-label">Email</label>
        <input type="email" class="form-control" id="form-email" name="email" required>
      </div>
      <div class="mb-3">
        <label for="form-phone_number" class="form-label">Phone Number</label>
        <input type="text" class="form-control" id="form-phone_number" name="phone_number">
      </div>
      <div class="mb-3">
        <label for="form-address" class="form-label">Address</label>
        <textarea class="form-control" id="form-address" name="address" rows="2"></textarea>
      </div>
      <div class="mb-3">
        <label for="form-company_id" class="form-label">Company ID</label>
        <input type="number" class="form-control" id="form-company_id" name="company_id" min="0">
      </div>

      <button type="submit" class="btn btn-primary" id="canvas-submit">Save</button>
    </form>
  </div>
</div>

<!-- Include CSS/JS -->
<link rel="stylesheet" href="assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<script src="assets/vendor/jquery/jquery.min.js"></script>
<script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const bulkPanel        = document.getElementById('bulk-panel');
  const selectedCount    = document.getElementById('selected-count');
  const selectAllLink    = document.getElementById('select-all-link');
  const bulkForm         = document.getElementById('bulk-form');
  const bulkIds          = document.getElementById('bulk-ids');
  const selectAllCheckbox = document.getElementById('select-all');

  const table = $('#contacts-table').DataTable({
    responsive: true,
    order: [[2, 'asc']]
  });

  function getSelectedIds() {
    return Array.from(document.querySelectorAll('.row-checkbox:checked'))
      .map(cb => cb.closest('tr').dataset.id);
  }

  function updateBulkPanel() {
    const ids = getSelectedIds();
    if (ids.length) {
      bulkPanel.style.display = 'flex';
      selectedCount.textContent = `${ids.length} contacts selected`;
      bulkIds.value = ids.join(',');
    } else {
      bulkPanel.style.display = 'none';
      selectedCount.textContent = `0 contacts selected`;
    }
  }

  $('#contacts-table tbody').on('change', '.row-checkbox', updateBulkPanel);
  table.on('draw', () => {
    selectAllCheckbox.checked = false;
    updateBulkPanel();
  });

  selectAllCheckbox.addEventListener('change', () => {
    document.querySelectorAll('.row-checkbox')
      .forEach(cb => cb.checked = selectAllCheckbox.checked);
    updateBulkPanel();
  });

  selectAllLink.addEventListener('click', e => {
    e.preventDefault();
    document.querySelectorAll('.row-checkbox')
      .forEach(cb => cb.checked = true);
    selectAllCheckbox.checked = true;
    updateBulkPanel();
  });

  document.getElementById('bulk-delete').addEventListener('click', e => {
    e.preventDefault();
    const ids = getSelectedIds();
    if (!ids.length) return alert('No contacts selected.');
    if (confirm('Delete selected contact(s)?')) bulkForm.submit();
  });

  document.getElementById('bulk-edit').addEventListener('click', e => {
    e.preventDefault();
    const ids = getSelectedIds();
    if (ids.length !== 1) return alert('Please select exactly one contact to edit.');
    const tr = document.querySelector(`tr[data-id="${ids[0]}"]`);
    const offcanvas = new bootstrap.Offcanvas(document.getElementById('userCanvas'));

    document.getElementById('canvas-title').textContent = 'Edit Contact';
    document.getElementById('form-action').value   = 'update';
    document.getElementById('form-id').value       = tr.dataset.id;
    document.getElementById('form-first_name').value   = tr.dataset.first_name;
    document.getElementById('form-last_name').value    = tr.dataset.last_name;
    document.getElementById('form-country').value      = tr.dataset.country;
    document.getElementById('form-age').value          = tr.dataset.age;
    document.getElementById('form-email').value        = tr.dataset.email;
    document.getElementById('form-phone_number').value = tr.dataset.phone_number;
    document.getElementById('form-address').value      = tr.dataset.address;
    document.getElementById('form-company_id').value   = tr.dataset.company_id;
    document.getElementById('canvas-submit').textContent = 'Update';

    offcanvas.show();
  });

  document.getElementById('btn-add').addEventListener('click', () => {
    const offcanvas = new bootstrap.Offcanvas(document.getElementById('userCanvas'));
    document.getElementById('canvas-title').textContent = 'Add Contact';
    document.getElementById('form-action').value   = 'create';
    document.getElementById('form-id').value       = '';
    document.querySelectorAll('#user-form input, #user-form textarea').forEach(el => el.value = '');
    document.getElementById('canvas-submit').textContent = 'Add';
    offcanvas.show();
  });
});
</script>
