<?php
// modules/contact-table.php

// 1) Ensure session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Database config
require_once __DIR__ . '/../layouts/config.php';

// 3) Initialize flash
if (empty($_SESSION['flash'])) {
    $_SESSION['flash'] = '';
}

// 4) Handle Create / Update / Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action       = $_POST['action']            ?? '';
    $flash        = '';

    // collect common fields
    $first_name   = mysqli_real_escape_string($link, trim($_POST['first_name']   ?? ''));
    $last_name    = mysqli_real_escape_string($link, trim($_POST['last_name']    ?? ''));
    $country      = mysqli_real_escape_string($link, trim($_POST['country']      ?? ''));
    $age          = intval($_POST['age']                                   ?? 0);
    $email        = mysqli_real_escape_string($link, trim($_POST['email']        ?? ''));
    $phone_number = mysqli_real_escape_string($link, trim($_POST['phone_number'] ?? ''));
    $address      = mysqli_real_escape_string($link, trim($_POST['address']      ?? ''));
    $raw_cid      = trim($_POST['company_id'] ?? '');
    $company_id   = ($raw_cid !== '' && intval($raw_cid) > 0) ? intval($raw_cid) : null;

    // CREATE
    if ($action === 'create') {
        if ($first_name && $last_name && $email) {
            $fields       = ['first_name','last_name','country','age','email','phone_number','address'];
            $placeholders = array_fill(0, count($fields), '?');
            $types        = 'sssisss';
            $params       = [ $first_name, $last_name, $country, $age, $email, $phone_number, $address ];

            if ($company_id !== null) {
                $fields[]       = 'company_id';
                $placeholders[] = '?';
                $types         .= 'i';
                $params[]       = $company_id;
            }

            $sql  = "INSERT INTO contacts (" . implode(',', $fields) . ")
                     VALUES (" . implode(',', $placeholders) . ")";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            $ok    = mysqli_stmt_execute($stmt);
            $flash = $ok
                   ? 'Contact added successfully.'
                   : 'Error adding contact: ' . mysqli_error($link);
            mysqli_stmt_close($stmt);
        } else {
            $flash = 'First name, last name and email are required.';
        }

    // UPDATE
    } elseif ($action === 'update') {
        $id = intval($_POST['id'] ?? 0);
        if ($id && $first_name && $last_name && $email) {
            // prepare update
            $sets   = [
                'first_name = ?',
                'last_name    = ?',
                'country      = ?',
                'age          = ?',
                'email        = ?',
                'phone_number = ?',
                'address      = ?'
            ];
            $types  = 'sssisss';
            $params = [ $first_name, $last_name, $country, $age, $email, $phone_number, $address ];
            if ($company_id !== null) {
                $sets[]   = 'company_id   = ?';
                $types   .= 'i';
                $params[] = $company_id;
            } else {
                $sets[] = 'company_id = NULL';
            }
            $sets[]      = 'updated_at   = NOW()';
            $types      .= 'i';
            $params[]    = $id;
            $sql         = "UPDATE contacts SET " . implode(',', $sets) . " WHERE id = ?";
            $stmt        = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            $ok          = mysqli_stmt_execute($stmt);
            $flash       = $ok
                         ? 'Contact updated successfully.'
                         : 'Error updating contact: ' . mysqli_error($link);
            mysqli_stmt_close($stmt);
        } else {
            $flash = 'All required fields must be filled out.';
        }

    // DELETE
    } elseif ($action === 'delete' && !empty($_POST['ids'])) {
        $ids = array_map('intval', explode(',', $_POST['ids']));
        $in  = implode(',', $ids);
        $ok   = mysqli_query($link, "DELETE FROM contacts WHERE id IN ($in)");
        $flash = $ok
               ? 'Selected contact(s) deleted.'
               : 'Error deleting contacts: ' . mysqli_error($link);
    }

    $_SESSION['flash'] = $flash;
    // JS redirect to avoid header issues
    $self = htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES);
    echo "<script>window.location.replace('{$self}');</script>";
    exit;
}

// 5) Fetch contacts with company name
$contacts = [];
$sql = "
  SELECT
    c.id, c.first_name, c.last_name, c.country, c.age,
    c.email, c.phone_number, c.address, c.company_id,
    comp.name AS company_name,
    c.created_at, c.updated_at
  FROM contacts c
  LEFT JOIN companies comp
    ON c.company_id = comp.id
  ORDER BY c.first_name
";
$res = mysqli_query($link, $sql) or die("Query failed: " . mysqli_error($link));
while ($row = mysqli_fetch_assoc($res)) {
    $contacts[] = $row;
}
mysqli_free_result($res);
?>
<!-- Page title & flash -->
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

<?php if ($_SESSION['flash']): ?>
  <div class="alert alert-info"><?= $_SESSION['flash'] ?></div>
  <?php $_SESSION['flash'] = '' ?>
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
             style="display:none;">
          <small id="selected-count" class="me-3">0 selected</small>
          <a href="#" id="select-all-link" class="me-3">
            Select all <span id="total-count"><?= count($contacts) ?></span>
          </a>
          <a href="#" id="bulk-save" class="me-3">Save</a>
          <a href="#" id="bulk-delete" class="me-3 text-danger">Delete</a>
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
              <th>Name</th>
              <th>Email</th>
              <th>Age</th>
              <th>Country</th>
              <th>Phone</th>
              <th>Address</th>
              <th>Company</th>
              <th>Created At</th>
              <th>Updated At</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($contacts as $c): ?>
            <tr data-id="<?= $c['id'] ?>" data-company-id="<?= $c['company_id'] ?>">
              <td><input type="checkbox" class="row-checkbox"></td>
              <td data-field="name">
                <a href="contacts-profile.php?id=<?= $c['id'] ?>">
                  <?= htmlspecialchars($c['first_name'].' '.$c['last_name']) ?>
                </a>
              </td>
              <td data-field="email">
                <a href="contacts-profile.php?id=<?= $c['id'] ?>">
                  <?= htmlspecialchars($c['email']) ?>
                </a>
              </td>
              <td data-field="age" contenteditable="true"><?= $c['age'] ?></td>
              <td data-field="country" contenteditable="true"><?= htmlspecialchars($c['country']) ?></td>
              <td data-field="phone_number" contenteditable="true"><?= htmlspecialchars($c['phone_number']) ?></td>
              <td data-field="address" contenteditable="true"><?= htmlspecialchars($c['address']) ?></td>
              <td><?= htmlspecialchars($c['company_name'] ?? '—') ?></td>
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

<!-- Offcanvas form for Add -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="userCanvas">
  <div class="offcanvas-header">
    <h5 id="canvas-title">Add Contact</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <form id="user-form" method="POST">
      <input type="hidden" name="action" id="form-action" value="create">
      <input type="hidden" name="id"     id="form-id">
      <div class="mb-3">
        <label class="form-label">First Name</label>
        <input type="text" class="form-control" name="first_name" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Last Name</label>
        <input type="text" class="form-control" name="last_name" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Country</label>
        <input type="text" class="form-control" name="country">
      </div>
      <div class="mb-3">
        <label class="form-label">Age</label>
        <input type="number" class="form-control" name="age" min="0">
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Phone Number</label>
        <input type="text" class="form-control" name="phone_number">
      </div>
      <div class="mb-3">
        <label class="form-label">Address</label>
        <textarea class="form-control" name="address" rows="2"></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Company (ID)</label>
        <input type="number" class="form-control" name="company_id" min="0" placeholder="leave blank for none">
      </div>
      <button type="submit" class="btn btn-primary">Save</button>
    </form>
  </div>
</div>

<!-- CSS/JS includes -->
<link rel="stylesheet" href="assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<script src="assets/vendor/jquery/jquery.min.js"></script>
<script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const bulkPanel         = document.getElementById('bulk-panel');
  const selectedCount     = document.getElementById('selected-count');
  const selectAllLink     = document.getElementById('select-all-link');
  const bulkForm          = document.getElementById('bulk-form');
  const bulkIds           = document.getElementById('bulk-ids');
  const selectAllCheckbox = document.getElementById('select-all');
  const btnSave           = document.getElementById('bulk-save');
  const table             = $('#contacts-table').DataTable({ responsive: true, order: [[2,'asc']] });

  function getSelectedIds() {
    return Array.from(document.querySelectorAll('.row-checkbox:checked'))
      .map(cb => cb.closest('tr').dataset.id);
  }

  function updateBulkPanel() {
    const ids = getSelectedIds();
    if (ids.length) {
      bulkPanel.style.display = 'flex';
      selectedCount.textContent = `${ids.length} selected`;
      bulkIds.value = ids.join(',');
    } else {
      bulkPanel.style.display = 'none';
      selectedCount.textContent = '0 selected';
    }
  }

  $('#contacts-table tbody').on('change', '.row-checkbox', updateBulkPanel);
  table.on('draw', () => {
    selectAllCheckbox.checked = false;
    updateBulkPanel();
  });

  selectAllCheckbox.addEventListener('change', () => {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = selectAllCheckbox.checked);
    updateBulkPanel();
  });

  selectAllLink.addEventListener('click', e => {
    e.preventDefault();
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = true);
    selectAllCheckbox.checked = true;
    updateBulkPanel();
  });

  // Delete
  document.getElementById('bulk-delete').addEventListener('click', e => {
    e.preventDefault();
    const ids = getSelectedIds();
    if (!ids.length) return alert('No contacts selected.');
    if (confirm('Delete selected contact(s)?')) bulkForm.submit();
  });

  // Save (inline update)
  btnSave.addEventListener('click', e => {
    e.preventDefault();
    const ids = getSelectedIds();
    if (ids.length !== 1) return alert('Please select exactly one contact to save.');
    const row = document.querySelector(`tr[data-id="${ids[0]}"]`);
    const id  = row.dataset.id;

    // collect edited values
    const fullName = row.querySelector('td[data-field="name"]').innerText.trim();
    const [first, ...rest] = fullName.split(' ');
    const last = rest.join(' ');
    const country      = row.querySelector('td[data-field="country"]').innerText.trim();
    const age          = row.querySelector('td[data-field="age"]').innerText.trim();
    const email        = row.querySelector('td[data-field="email"]').innerText.trim();
    const phone_number = row.querySelector('td[data-field="phone_number"]').innerText.trim();
    const address      = row.querySelector('td[data-field="address"]').innerText.trim();
    const company_id   = row.dataset.companyId || '';

    // build and submit form
    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    [
      ['action','update'],
      ['id', id],
      ['first_name', first],
      ['last_name', last],
      ['country', country],
      ['age', age],
      ['email', email],
      ['phone_number', phone_number],
      ['address', address],
      ['company_id', company_id]
    ].forEach(([name,val]) => {
      const inp = document.createElement('input');
      inp.name  = name;
      inp.value = val;
      form.appendChild(inp);
    });
    document.body.appendChild(form);
    form.submit();
  });

  // Add new
  document.getElementById('btn-add').addEventListener('click', () => {
    const offcanvas = new bootstrap.Offcanvas(document.getElementById('userCanvas'));
    offcanvas.show();
  });
});
</script>
