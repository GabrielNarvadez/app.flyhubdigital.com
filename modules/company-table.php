<?php
// modules/company-table.php


// 2) Database config
require_once __DIR__ . '/../layouts/config.php';

// 3) Initialize flash
if (empty($_SESSION['flash'])) {
    $_SESSION['flash'] = '';
}

// 4) Handle Create / Update / Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action         = $_POST['action']            ?? '';
    $name           = mysqli_real_escape_string($link, trim($_POST['name']           ?? ''));
    $address        = mysqli_real_escape_string($link, trim($_POST['address']        ?? ''));
    $city           = mysqli_real_escape_string($link, trim($_POST['city']           ?? ''));
    $country        = mysqli_real_escape_string($link, trim($_POST['country']        ?? 'USA'));
    $email          = mysqli_real_escape_string($link, trim($_POST['email']          ?? ''));
    $phone          = mysqli_real_escape_string($link, trim($_POST['phone']          ?? ''));
    $website        = mysqli_real_escape_string($link, trim($_POST['website']        ?? ''));
    $industry       = mysqli_real_escape_string($link, trim($_POST['industry']       ?? ''));
    $employee_count = intval($_POST['employee_count']                       ?? 0);
    $flash          = '';

    // CREATE
    if ($action === 'create') {
        if ($name !== '') {
            $stmt = mysqli_prepare($link, "
                INSERT INTO companies
                  (name,address,city,country,email,phone,website,industry,employee_count)
                VALUES (?,?,?,?,?,?,?,?,?)
            ");
            mysqli_stmt_bind_param($stmt,
                'ssssssssi',
                $name, $address, $city, $country,
                $email, $phone, $website, $industry,
                $employee_count
            );
            $flash = mysqli_stmt_execute($stmt)
                   ? 'Company added successfully.'
                   : 'Error adding company: ' . mysqli_error($link);
            mysqli_stmt_close($stmt);
        } else {
            $flash = 'Name is required.';
        }

    // UPDATE
    } elseif ($action === 'update') {
        $id = intval($_POST['id'] ?? 0);
        if ($id && $name !== '') {
            $stmt = mysqli_prepare($link, "
                UPDATE companies SET
                  name           = ?,
                  address        = ?,
                  city           = ?,
                  country        = ?,
                  email          = ?,
                  phone          = ?,
                  website        = ?,
                  industry       = ?,
                  employee_count = ?,
                  updated_at     = NOW()
                WHERE id = ?
            ");
            mysqli_stmt_bind_param($stmt,
                'ssssssssii',
                $name, $address, $city, $country,
                $email, $phone, $website, $industry,
                $employee_count, $id
            );
            $flash = mysqli_stmt_execute($stmt)
                   ? 'Company updated successfully.'
                   : 'Error updating company: ' . mysqli_error($link);
            mysqli_stmt_close($stmt);
        } else {
            $flash = 'ID and Name are required.';
        }

    // DELETE (with FK check)
    } elseif ($action === 'delete' && !empty($_POST['ids'])) {
        $ids = array_map('intval', explode(',', $_POST['ids']));
        $in  = implode(',', $ids);

        // check for existing contacts
        $blocked = [];
        $sqlCheck = "
            SELECT comp.id, comp.name, COUNT(ct.id) AS cnt
            FROM companies comp
            JOIN contacts ct ON ct.company_id = comp.id
            WHERE comp.id IN ($in)
            GROUP BY comp.id
        ";
        if ($resCheck = mysqli_query($link, $sqlCheck)) {
            while ($row = mysqli_fetch_assoc($resCheck)) {
                $blocked[$row['id']] = ['name'=>$row['name'], 'cnt'=>$row['cnt']];
            }
            mysqli_free_result($resCheck);
        }

        if (!empty($blocked)) {
            $msgs = [];
            foreach ($blocked as $b) {
                $msgs[] = "{$b['name']} ({$b['cnt']} contacts)";
            }
            $flash = 'Cannot delete companies with existing contacts: ' . implode(', ', $msgs) . '.';
        } else {
            $sql = "DELETE FROM companies WHERE id IN ($in)";
            if (mysqli_query($link, $sql)) {
                $flash = 'Selected company(s) deleted.';
            } else {
                $flash = 'Error deleting companies: ' . mysqli_error($link);
            }
        }
    }

    $_SESSION['flash'] = $flash;

    // JS redirect avoids header issues
    $self = htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES);
    echo "<script>window.location.replace('{$self}');</script>";
    exit;
}

// 5) Fetch companies for display
$companies = [];
$sql = "
    SELECT
      id, name, address, city, country,
      email, phone, website, industry, employee_count,
      created_at, updated_at
    FROM companies
    ORDER BY name
";
$res = mysqli_query($link, $sql);
if (!$res) {
    die("Fetch failed: " . mysqli_error($link));
}
while ($row = mysqli_fetch_assoc($res)) {
    $companies[] = $row;
}
mysqli_free_result($res);
?>
<!-- Page Title & Flash -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="index.php">Flyhub Digital</a></li>
          <li class="breadcrumb-item active">Companies</li>
        </ol>
      </div>
      <h4 class="page-title">Company Management</h4>
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
          <h4 class="header-title mb-0">Companies List</h4>
          <button id="btn-add" class="btn btn-success">+ Add Company</button>
        </div>

        <!-- Bulk panel -->
        <div id="bulk-panel" class="d-flex align-items-center bg-light border rounded px-3 py-2 mb-3" style="display: none;">
          <small id="selected-count" class="me-3">0 selected</small>
          <a href="#" id="select-all-link" class="me-3">
            Select all <span id="total-count"><?= count($companies) ?></span>
          </a>
          <a href="#" id="bulk-edit" class="me-3">Edit</a>
          <a href="#" id="bulk-delete" class="me-3 text-danger">Delete</a>
        </div>

        <!-- Bulk-delete form -->
        <form id="bulk-form" method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES) ?>" style="display: none;">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="ids" id="bulk-ids">
        </form>

        <!-- DataTable -->
        <table id="companies-table" class="table table-striped dt-responsive nowrap w-100">
          <thead>
            <tr>
              <th><input type="checkbox" id="select-all"></th>
              <th>ID</th>
              <th>Name</th>
              <th>Address</th>
              <th>City</th>
              <th>Country</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Website</th>
              <th>Industry</th>
              <th>Employees</th>
              <th>Created At</th>
              <th>Updated At</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($companies as $c): ?>
              <tr
                data-id="<?= $c['id'] ?>"
                data-name="<?= htmlspecialchars($c['name'], ENT_QUOTES) ?>"
                data-address="<?= htmlspecialchars($c['address'], ENT_QUOTES) ?>"
                data-city="<?= htmlspecialchars($c['city'], ENT_QUOTES) ?>"
                data-country="<?= htmlspecialchars($c['country'], ENT_QUOTES) ?>"
                data-email="<?= htmlspecialchars($c['email'], ENT_QUOTES) ?>"
                data-phone="<?= htmlspecialchars($c['phone'], ENT_QUOTES) ?>"
                data-website="<?= htmlspecialchars($c['website'], ENT_QUOTES) ?>"
                data-industry="<?= htmlspecialchars($c['industry'], ENT_QUOTES) ?>"
                data-employee_count="<?= $c['employee_count'] ?>"
              >
                <td><input type="checkbox" class="row-checkbox"></td>
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= htmlspecialchars($c['address']) ?></td>
                <td><?= htmlspecialchars($c['city']) ?></td>
                <td><?= htmlspecialchars($c['country']) ?></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><?= htmlspecialchars($c['phone']) ?></td>
                <td><?= htmlspecialchars($c['website']) ?></td>
                <td><?= htmlspecialchars($c['industry']) ?></td>
                <td><?= $c['employee_count'] ?></td>
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

<!-- Offcanvas form for Add / Edit -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="companyCanvas">
  <div class="offcanvas-header">
    <h5 id="canvas-title">Add Company</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <form id="company-form" method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES) ?>">
      <input type="hidden" name="action" id="form-action" value="create">
      <input type="hidden" name="id"     id="form-id">

      <div class="mb-3">
        <label for="form-name" class="form-label">Name *</label>
        <input type="text" class="form-control" id="form-name" name="name" required>
      </div>
      <div class="mb-3">
        <label for="form-address" class="form-label">Address</label>
        <input type="text" class="form-control" id="form-address" name="address">
      </div>
      <div class="mb-3">
        <label for="form-city" class="form-label">City</label>
        <input type="text" class="form-control" id="form-city" name="city">
      </div>
      <div class="mb-3">
        <label for="form-country" class="form-label">Country</label>
        <input type="text" class="form-control" id="form-country" name="country" value="USA" required>
      </div>
      <div class="mb-3">
        <label for="form-email" class="form-label">Email</label>
        <input type="email" class="form-control" id="form-email" name="email">
      </div>
      <div class="mb-3">
        <label for="form-phone" class="form-label">Phone</label>
        <input type="text" class="form-control" id="form-phone" name="phone">
      </div>
      <div class="mb-3">
        <label for="form-website" class="form-label">Website</label>
        <input type="url" class="form-control" id="form-website" name="website">
      </div>
      <div class="mb-3">
        <label for="form-industry" class="form-label">Industry</label>
        <input type="text" class="form-control" id="form-industry" name="industry">
      </div>
      <div class="mb-3">
        <label for="form-employee_count" class="form-label">Employees</label>
        <input type="number" class="form-control" id="form-employee_count" name="employee_count" min="0">
      </div>

      <button type="submit" class="btn btn-primary" id="canvas-submit">Save</button>
    </form>
  </div>
</div>

<!-- CSS / JS includes -->
<link rel="stylesheet" href="assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<script src="assets/vendor/jquery/jquery.min.js"></script>
<script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const bulkPanel         = document.getElementById('bulk-panel');
  const selectedCount     = document.getElementById('selected-count');
  const selectAllLink     = document.getElementById('select-all-link');
  const bulkForm          = document.getElementById('bulk-form');
  const bulkIds           = document.getElementById('bulk-ids');
  const selectAllCheckbox = document.getElementById('select-all');
  const table             = $('#companies-table').DataTable({ responsive: true, order: [[2,'asc']] });

  function getSelectedIds() {
    return Array.from(document.querySelectorAll('.row-checkbox:checked'))
      .map(cb => cb.closest('tr').dataset.id);
  }

  function updateBulkPanel() {
    const ids = getSelectedIds();
    if (ids.length) {
      bulkPanel.style.display   = 'flex';
      selectedCount.textContent = `${ids.length} selected`;
      bulkIds.value             = ids.join(',');
    } else {
      bulkPanel.style.display   = 'none';
      selectedCount.textContent = '0 selected';
    }
  }

  $('#companies-table tbody').on('change', '.row-checkbox', updateBulkPanel);
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
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = true);
    selectAllCheckbox.checked = true;
    updateBulkPanel();
  });

  document.getElementById('bulk-delete').addEventListener('click', function(e) {
    e.preventDefault();
    const ids = getSelectedIds();
    if (!ids.length) return alert('No companies selected.');
    if (confirm('Delete selected company(s)?')) {
      bulkIds.value = ids.join(',');
      bulkForm.submit();
    }
  });

  document.getElementById('bulk-edit').addEventListener('click', function(e) {
    e.preventDefault();
    const ids = getSelectedIds();
    if (ids.length !== 1) return alert('Please select exactly one company to edit.');
    const tr = document.querySelector(`tr[data-id="${ids[0]}"]`);
    const offcanvas = new bootstrap.Offcanvas(document.getElementById('companyCanvas'));

    document.getElementById('canvas-title').textContent = 'Edit Company';
    document.getElementById('form-action').value        = 'update';
    document.getElementById('form-id').value            = tr.dataset.id;
    ['name','address','city','country','email','phone','website','industry','employee_count']
      .forEach(field => {
        document.getElementById('form-' + field).value = tr.dataset[field];
      });
    document.getElementById('canvas-submit').textContent = 'Update';
    offcanvas.show();
  });

  document.getElementById('btn-add').addEventListener('click', function() {
    const offcanvas = new bootstrap.Offcanvas(document.getElementById('companyCanvas'));
    document.getElementById('canvas-title').textContent = 'Add Company';
    document.getElementById('form-action').value        = 'create';
    document.getElementById('form-id').value            = '';
    ['name','address','city','country','email','phone','website','industry','employee_count']
      .forEach(field => {
        document.getElementById('form-' + field).value = field === 'country' ? 'USA' : '';
      });
    document.getElementById('canvas-submit').textContent = 'Add';
    offcanvas.show();
  });
});
</script>
