<?php
// modules/units-table.php

// only start session if not already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// load your DB config (must define $link = mysqli_connect(...))
require_once __DIR__ . '/../layouts/config.php';

// initialize flash
if (empty($_SESSION['flash'])) {
    $_SESSION['flash'] = '';
}

// handle Create / Update / Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action                  = $_POST['action']                         ?? '';
    $unit_status             = mysqli_real_escape_string($link, trim($_POST['unit_status']             ?? ''));
    $project_title           = mysqli_real_escape_string($link, trim($_POST['project_title']           ?? ''));
    $project_site            = mysqli_real_escape_string($link, trim($_POST['project_site']            ?? ''));
    $phase                   = mysqli_real_escape_string($link, trim($_POST['phase']                   ?? ''));
    $block                   = mysqli_real_escape_string($link, trim($_POST['block']                   ?? ''));
    $lot                     = mysqli_real_escape_string($link, trim($_POST['lot']                     ?? ''));
    $lot_class               = mysqli_real_escape_string($link, trim($_POST['lot_class']               ?? ''));
    $lot_area                = floatval($_POST['lot_area']                ?? 0);
    $price_per_sqm           = floatval($_POST['price_per_sqm']           ?? 0);
    $date_of_reservation     = $_POST['date_of_reservation']             ?? null;
    $total_contract_price    = floatval($_POST['total_contract_price']    ?? 0);
    $additional_misc_fee     = floatval($_POST['additional_misc_fee']     ?? 0);
    $reservation_fee         = floatval($_POST['reservation_fee']         ?? 0);
    $interest                = floatval($_POST['interest']                ?? 0);
    $net_selling_price       = floatval($_POST['net_selling_price']       ?? 0);
    $total_amount_payable    = floatval($_POST['total_amount_payable']    ?? 0);
    $monthly_amortization    = floatval($_POST['monthly_amortization']    ?? 0);
    $amortization_start_date = $_POST['amortization_start_date']        ?? null;
    $payment_terms           = intval($_POST['payment_terms']            ?? 0);
    $client_name             = mysqli_real_escape_string($link, trim($_POST['client_name']             ?? ''));
    $balance_payable         = floatval($_POST['balance_payable']         ?? 0);
    $view_360_link           = mysqli_real_escape_string($link, trim($_POST['view_360_link']           ?? ''));
    $flash                   = '';

    if ($action === 'create') {
        if ($unit_status !== '' && $project_title !== '') {
            $stmt = mysqli_prepare($link, "
                INSERT INTO units
                  (unit_status, project_title, project_site, phase, block, lot, lot_class,
                   lot_area, price_per_sqm, date_of_reservation, total_contract_price,
                   additional_misc_fee, reservation_fee, interest, net_selling_price,
                   total_amount_payable, monthly_amortization, amortization_start_date,
                   payment_terms, client_name, balance_payable, view_360_link)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            mysqli_stmt_bind_param($stmt,
                'sssssssddsdddddddsisdsi',
                $unit_status,
                $project_title,
                $project_site,
                $phase,
                $block,
                $lot,
                $lot_class,
                $lot_area,
                $price_per_sqm,
                $date_of_reservation,
                $total_contract_price,
                $additional_misc_fee,
                $reservation_fee,
                $interest,
                $net_selling_price,
                $total_amount_payable,
                $monthly_amortization,
                $amortization_start_date,
                $payment_terms,
                $client_name,
                $balance_payable,
                $view_360_link
            );
            $flash = mysqli_stmt_execute($stmt)
                   ? 'Unit added successfully.'
                   : 'Error adding unit: ' . mysqli_error($link);
            mysqli_stmt_close($stmt);
        } else {
            $flash = 'Status and Project Title are required.';
        }

    } elseif ($action === 'update') {
        $id = intval($_POST['id'] ?? 0);
        if ($id && $unit_status !== '' && $project_title !== '') {
            $stmt = mysqli_prepare($link, "
                UPDATE units SET
                  unit_status             = ?,
                  project_title           = ?,
                  project_site            = ?,
                  phase                   = ?,
                  block                   = ?,
                  lot                     = ?,
                  lot_class               = ?,
                  lot_area                = ?,
                  price_per_sqm           = ?,
                  date_of_reservation     = ?,
                  total_contract_price    = ?,
                  additional_misc_fee     = ?,
                  reservation_fee         = ?,
                  interest                = ?,
                  net_selling_price       = ?,
                  total_amount_payable    = ?,
                  monthly_amortization    = ?,
                  amortization_start_date = ?,
                  payment_terms           = ?,
                  client_name             = ?,
                  balance_payable         = ?,
                  view_360_link           = ?,
                  updated_at              = NOW()
                WHERE id = ?
            ");
            mysqli_stmt_bind_param($stmt,
                'sssssssddsdddddddsisdsi',
                $unit_status,
                $project_title,
                $project_site,
                $phase,
                $block,
                $lot,
                $lot_class,
                $lot_area,
                $price_per_sqm,
                $date_of_reservation,
                $total_contract_price,
                $additional_misc_fee,
                $reservation_fee,
                $interest,
                $net_selling_price,
                $total_amount_payable,
                $monthly_amortization,
                $amortization_start_date,
                $payment_terms,
                $client_name,
                $balance_payable,
                $view_360_link,
                $id
            );
            $flash = mysqli_stmt_execute($stmt)
                   ? 'Unit updated successfully.'
                   : 'Error updating unit: ' . mysqli_error($link);
            mysqli_stmt_close($stmt);
        } else {
            $flash = 'ID, Status and Project Title are required.';
        }

    } elseif ($action === 'delete' && !empty($_POST['ids'])) {
        $ids = array_map('intval', explode(',', $_POST['ids']));
        $in  = implode(',', $ids);
        if (mysqli_query($link, "DELETE FROM units WHERE id IN ($in)")) {
            $flash = 'Selected unit(s) deleted.';
        } else {
            $flash = 'Error deleting units: ' . mysqli_error($link);
        }
    }

    $_SESSION['flash'] = $flash;
    echo "<script>window.location.replace('".htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES)."');</script>";
    exit;
}

// fetch units for display
$units = [];
$sql   = "
    SELECT
      id, unit_status, project_title, project_site, phase, block, lot, lot_class,
      lot_area, price_per_sqm, date_of_reservation, total_contract_price,
      additional_misc_fee, reservation_fee, interest, net_selling_price,
      total_amount_payable, monthly_amortization, amortization_start_date,
      payment_terms, client_name, balance_payable, view_360_link
    FROM units
    ORDER BY project_title
";
$res = mysqli_query($link, $sql);
if (!$res) {
    die("Fetch failed: " . mysqli_error($link));
}
while ($row = mysqli_fetch_assoc($res)) {
    $units[] = $row;
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
          <li class="breadcrumb-item active">Units</li>
        </ol>
      </div>
      <h4 class="page-title">Units Management</h4>
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
          <h4 class="header-title mb-0">Units List</h4>
          <button id="btn-add" class="btn btn-success">+ Add Unit</button>
        </div>

        <!-- Bulk panel -->
        <div id="bulk-panel"
             class="d-flex align-items-center bg-light border rounded px-3 py-2 mb-3"
             style="display: none;">
          <small id="selected-count" class="me-3">0 selected</small>
          <a href="#" id="select-all-link" class="me-3">
            Select all <span id="total-count"><?= count($units) ?></span>
          </a>
          <a href="#" id="bulk-edit" class="me-3">Edit</a>
          <a href="#" id="bulk-delete" class="me-3 text-danger">Delete</a>
        </div>

        <!-- Bulk-delete form -->
        <form id="bulk-form"
              method="POST"
              action="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES) ?>"
              style="display: none;">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="ids" id="bulk-ids">
        </form>

        <!-- DataTable -->
        <table id="units-table" class="table table-striped dt-responsive nowrap w-100">
          <thead>
            <tr>
              <th><input type="checkbox" id="select-all"></th>

              <th>Project Title</th>
              <th>Project Site</th>
              <th>Status</th>
              <th>Phase</th>
              <th>Block</th>
              <th>Lot</th>
              <th>Lot Class</th>
              <th>Area (sqm)</th>
              <th>Price/sqm</th>
              <th>Reserved On</th>
              <th>Contract Price</th>
              <th>Misc Fee</th>
              <th>Reservation Fee</th>
            </tr>
          </thead>
          <tbody>
    <?php foreach ($units as $u): ?>
      <tr
        data-id="<?= $u['id'] ?>"
        data-unit_status="<?= htmlspecialchars($u['unit_status'], ENT_QUOTES) ?>"
        data-project_title="<?= htmlspecialchars($u['project_title'], ENT_QUOTES) ?>"
        data-project_site="<?= htmlspecialchars($u['project_site'], ENT_QUOTES) ?>"
        data-phase="<?= htmlspecialchars($u['phase'], ENT_QUOTES) ?>"
        data-block="<?= htmlspecialchars($u['block'], ENT_QUOTES) ?>"
        data-lot="<?= htmlspecialchars($u['lot'], ENT_QUOTES) ?>"
        data-lot_class="<?= htmlspecialchars($u['lot_class'], ENT_QUOTES) ?>"
        data-lot_area="<?= $u['lot_area'] ?>"
        data-price_per_sqm="<?= $u['price_per_sqm'] ?>"
        data-date_of_reservation="<?= $u['date_of_reservation'] ?>"
        data-total_contract_price="<?= $u['total_contract_price'] ?>"
        data-additional_misc_fee="<?= $u['additional_misc_fee'] ?>"
        data-reservation_fee="<?= $u['reservation_fee'] ?>"
        data-interest="<?= $u['interest'] ?>"
        data-net_selling_price="<?= $u['net_selling_price'] ?>"
        data-total_amount_payable="<?= $u['total_amount_payable'] ?>"
        data-monthly_amortization="<?= $u['monthly_amortization'] ?>"
        data-amortization_start_date="<?= $u['amortization_start_date'] ?>"
        data-payment_terms="<?= $u['payment_terms'] ?>"
        data-client_name="<?= htmlspecialchars($u['client_name'], ENT_QUOTES) ?>"
        data-balance_payable="<?= $u['balance_payable'] ?>"
        data-view_360_link="<?= htmlspecialchars($u['view_360_link'], ENT_QUOTES) ?>"
      >
        <td><input type="checkbox" class="row-checkbox"></td>

        <td>
        <a href="unit-profile.php?id=<?= $u['id'] ?>">
          <?= htmlspecialchars($u['project_title']) ?>
    </a>
        </td>
        <td><?= htmlspecialchars($u['project_site']) ?></td>
        <td><?= htmlspecialchars($u['unit_status']) ?></td>
        <td><?= htmlspecialchars($u['phase']) ?></td>
        <td><?= htmlspecialchars($u['block']) ?></td>
        <td><?= htmlspecialchars($u['lot']) ?></td>
        <td><?= htmlspecialchars($u['lot_class']) ?></td>
        <td><?= number_format($u['lot_area'], 2) ?></td>
        <td><?= number_format($u['price_per_sqm'], 2) ?></td>
        <td><?= $u['date_of_reservation'] ?></td>
        <td><?= number_format($u['total_contract_price'], 2) ?></td>
        <td><?= number_format($u['additional_misc_fee'], 2) ?></td>
        <td><?= number_format($u['reservation_fee'], 2) ?></td>
      </tr>
    <?php endforeach; ?>
          </tbody>
        </table>

      </div>
    </div>
  </div>
</div>

<!-- Offcanvas form for Add / Edit -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="unitCanvas">
  <div class="offcanvas-header">
    <h5 id="canvas-title">Add Unit</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <form id="unit-form"
          method="POST"
          action="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES) ?>">
      <input type="hidden" name="action" id="form-action" value="create">
      <input type="hidden" name="id"     id="form-id">

      <div class="mb-3">
        <label for="form-unit_status" class="form-label">Status *</label>
        <select class="form-control" id="form-unit_status" name="unit_status" required>
          <option value="">Select…</option>
          <option>Available</option>
          <option>Sold</option>
          <option>Reserved</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="form-project_title" class="form-label">Project Title *</label>
        <input type="text" class="form-control" id="form-project_title" name="project_title" required>
      </div>
      <div class="mb-3">
        <label for="form-project_site" class="form-label">Project Site</label>
        <input type="text" class="form-control" id="form-project_site" name="project_site">
      </div>
      <div class="mb-3">
        <label for="form-phase" class="form-label">Phase</label>
        <input type="text" class="form-control" id="form-phase" name="phase">
      </div>
      <div class="mb-3">
        <label for="form-block" class="form-label">Block</label>
        <input type="text" class="form-control" id="form-block" name="block">
      </div>
      <div class="mb-3">
        <label for="form-lot" class="form-label">Lot</label>
        <input type="text" class="form-control" id="form-lot" name="lot">
      </div>
      <div class="mb-3">
        <label for="form-lot_class" class="form-label">Lot Class</label>
        <input type="text" class="form-control" id="form-lot_class" name="lot_class">
      </div>
      <div class="mb-3">
        <label for="form-lot_area" class="form-label">Lot Area (sqm)</label>
        <input type="number" step="0.01" class="form-control" id="form-lot_area" name="lot_area" value="0.00">
      </div>
      <div class="mb-3">
        <label for="form-price_per_sqm" class="form-label">Price per sqm</label>
        <input type="number" step="0.01" class="form-control" id="form-price_per_sqm" name="price_per_sqm" value="0.00">
      </div>
      <div class="mb-3">
        <label for="form-date_of_reservation" class="form-label">Date of Reservation</label>
        <input type="date" class="form-control" id="form-date_of_reservation" name="date_of_reservation">
      </div>
      <div class="mb-3">
        <label for="form-total_contract_price" class="form-label">Total Contract Price</label>
        <input type="number" step="0.01" class="form-control" id="form-total_contract_price" name="total_contract_price" value="0.00">
      </div>
      <div class="mb-3">
        <label for="form-additional_misc_fee" class="form-label">Additional Misc Fee</label>
        <input type="number" step="0.01" class="form-control" id="form-additional_misc_fee" name="additional_misc_fee" value="0.00">
      </div>
      <div class="mb-3">
        <label for="form-reservation_fee" class="form-label">Reservation Fee</label>
        <input type="number" step="0.01" class="form-control" id="form-reservation_fee" name="reservation_fee" value="0.00">
      </div>
      <div class="mb-3">
        <label for="form-interest" class="form-label">Interest</label>
        <input type="number" step="0.01" class="form-control" id="form-interest" name="interest" value="0.00">
      </div>
      <div class="mb-3">
        <label for="form-net_selling_price" class="form-label">Net Selling Price</label>
        <input type="number" step="0.01" class="form-control" id="form-net_selling_price" name="net_selling_price" value="0.00">
      </div>
      <div class="mb-3">
        <label for="form-total_amount_payable" class="form-label">Total Amount Payable</label>
        <input type="number" step="0.01" class="form-control" id="form-total_amount_payable" name="total_amount_payable" value="0.00">
      </div>
      <div class="mb-3">
        <label for="form-monthly_amortization" class="form-label">Monthly Amortization</label>
        <input type="number" step="0.01" class="form-control" id="form-monthly_amortization" name="monthly_amortization" value="0.00">
      </div>
      <div class="mb-3">
        <label for="form-amortization_start_date" class="form-label">Amortization Start Date</label>
        <input type="date" class="form-control" id="form-amortization_start_date" name="amortization_start_date">
      </div>
      <div class="mb-3">
        <label for="form-payment_terms" class="form-label">Payment Terms (months)</label>
        <input type="number" class="form-control" id="form-payment_terms" name="payment_terms" value="0">
      </div>
      <div class="mb-3">
        <label for="form-client_name" class="form-label">Client Name</label>
        <input type="text" class="form-control" id="form-client_name" name="client_name">
      </div>
      <div class="mb-3">
        <label for="form-balance_payable" class="form-label">Balance Payable</label>
        <input type="number" step="0.01" class="form-control" id="form-balance_payable" name="balance_payable" value="0.00">
      </div>
      <div class="mb-3">
        <label for="form-view_360_link" class="form-label">360° View Link</label>
        <input type="url" class="form-control" id="form-view_360_link" name="view_360_link">
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
  const table             = $('#units-table').DataTable({
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
      bulkPanel.style.display   = 'flex';
      selectedCount.textContent = `${ids.length} selected`;
      bulkIds.value             = ids.join(',');
    } else {
      bulkPanel.style.display   = 'none';
      selectedCount.textContent = '0 selected';
    }
  }

  $('#units-table tbody').on('change', '.row-checkbox', updateBulkPanel);
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
    if (!ids.length) return alert('No units selected.');
    if (confirm('Delete selected unit(s)?')) {
      bulkIds.value = ids.join(',');
      bulkForm.submit();
    }
  });

  document.getElementById('bulk-edit').addEventListener('click', function(e) {
    e.preventDefault();
    const ids = getSelectedIds();
    if (ids.length !== 1) return alert('Please select exactly one unit to edit.');
    const tr = document.querySelector(`tr[data-id="${ids[0]}"]`);
    const offcanvas = new bootstrap.Offcanvas(document.getElementById('unitCanvas'));

    document.getElementById('canvas-title').textContent = 'Edit Unit';
    document.getElementById('form-action').value      = 'update';
    document.getElementById('form-id').value          = tr.dataset.id;

    const fields = [
      'unit_status','project_title','project_site','phase','block','lot','lot_class',
      'lot_area','price_per_sqm','date_of_reservation','total_contract_price',
      'additional_misc_fee','reservation_fee','interest','net_selling_price',
      'total_amount_payable','monthly_amortization','amortization_start_date',
      'payment_terms','client_name','balance_payable','view_360_link'
    ];
    fields.forEach(field => {
      document.getElementById('form-' + field).value = tr.dataset[field] || '';
    });

    document.getElementById('canvas-submit').textContent = 'Update';
    offcanvas.show();
  });

  document.getElementById('btn-add').addEventListener('click', function() {
    const offcanvas = new bootstrap.Offcanvas(document.getElementById('unitCanvas'));
    document.getElementById('canvas-title').textContent = 'Add Unit';
    document.getElementById('form-action').value      = 'create';
    document.getElementById('form-id').value          = '';

    const fields = [
      'unit_status','project_title','project_site','phase','block','lot','lot_class',
      'lot_area','price_per_sqm','date_of_reservation','total_contract_price',
      'additional_misc_fee','reservation_fee','interest','net_selling_price',
      'total_amount_payable','monthly_amortization','amortization_start_date',
      'payment_terms','client_name','balance_payable','view_360_link'
    ];
    fields.forEach(field => {
      const el = document.getElementById('form-' + field);
      if (!el) return;
      if (['lot_area','price_per_sqm','total_contract_price','additional_misc_fee','reservation_fee','interest','net_selling_price','total_amount_payable','monthly_amortization','balance_payable'].includes(field)) {
        el.value = '0.00';
      } else if (field === 'payment_terms') {
        el.value = '0';
      } else {
        el.value = '';
      }
    });

    document.getElementById('canvas-submit').textContent = 'Add';
    offcanvas.show();
  });
});
</script>
