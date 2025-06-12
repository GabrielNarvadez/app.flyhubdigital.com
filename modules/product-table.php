<<<<<<< Updated upstream
<?php
// modules/product-table.php
=======
                    <!-- start page title -->
                    
>>>>>>> Stashed changes

// 1) Ensure session
session_start();

<<<<<<< Updated upstream
// 2) Database config
require_once __DIR__ . '/../layouts/config.php';
=======
                                    <h4 class="header-title">Here are all your products and services are stored in a dynamic data table, making them easy to manage and readily available for selling, creating deals, quotes, and proposals, while allowing for flexible expansion based on what works best for your business.</h4>
                                    <p class="text-muted fs-14">
                                      
                                    </p>
>>>>>>> Stashed changes

// 3) Initialize flash
if (empty($_SESSION['flash'])) {
    $_SESSION['flash'] = '';
}

// 4) Handle Create / Update / Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action      = $_POST['action']                         ?? '';
    $name        = mysqli_real_escape_string($link, trim($_POST['name']        ?? ''));
    $sku         = mysqli_real_escape_string($link, trim($_POST['sku']         ?? ''));
    $description = mysqli_real_escape_string($link, trim($_POST['description'] ?? ''));
    $price       = floatval($_POST['price']                 ?? 0.00);
    $stock       = intval($_POST['stock']                   ?? 0);
    $flash       = '';

    // CREATE
    if ($action === 'create') {
        if ($name !== '' && $sku !== '') {
            $stmt = mysqli_prepare($link, "
                INSERT INTO products
                  (name, sku, description, price, stock)
                VALUES (?,    ?,   ?,           ?,     ?)
            ");
            mysqli_stmt_bind_param($stmt,
                'ssdii',
                $name, $sku, $description, $price, $stock
            );
            $flash = mysqli_stmt_execute($stmt)
                   ? 'Product added successfully.'
                   : 'Error adding product: ' . mysqli_error($link);
            mysqli_stmt_close($stmt);
        } else {
            $flash = 'Name and SKU are required.';
        }

    // UPDATE
    } elseif ($action === 'update') {
        $id = intval($_POST['id'] ?? 0);
        if ($id && $name !== '' && $sku !== '') {
            $stmt = mysqli_prepare($link, "
                UPDATE products SET
                  name        = ?,
                  sku         = ?,
                  description = ?,
                  price       = ?,
                  stock       = ?,
                  updated_at  = NOW()
                WHERE id = ?
            ");
            mysqli_stmt_bind_param($stmt,
                'ssdiii',
                $name, $sku, $description, $price, $stock, $id
            );
            $flash = mysqli_stmt_execute($stmt)
                   ? 'Product updated successfully.'
                   : 'Error updating product: ' . mysqli_error($link);
            mysqli_stmt_close($stmt);
        } else {
            $flash = 'ID, Name and SKU are required.';
        }

    // DELETE
    } elseif ($action === 'delete' && !empty($_POST['ids'])) {
        $ids = array_map('intval', explode(',', $_POST['ids']));
        $in  = implode(',', $ids);
        $sql = "DELETE FROM products WHERE id IN ($in)";
        if (mysqli_query($link, $sql)) {
            $flash = 'Selected product(s) deleted.';
        } else {
            $flash = 'Error deleting products: ' . mysqli_error($link);
        }
    }

    $_SESSION['flash'] = $flash;

    // JS redirect avoids header issues
    $self = htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES);
    echo "<script>window.location.replace('{$self}');</script>";
    exit;
}

// 5) Fetch products for display
$products = [];
$sql = "
    SELECT
      id, name, sku, description,
      price, stock, created_at, updated_at
    FROM products
    ORDER BY name
";
$res = mysqli_query($link, $sql);
if (!$res) {
    die("Fetch failed: " . mysqli_error($link));
}
while ($row = mysqli_fetch_assoc($res)) {
    $products[] = $row;
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
          <li class="breadcrumb-item active">Products</li>
        </ol>
      </div>
      <h4 class="page-title">Product Management</h4>
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
          <h4 class="header-title mb-0">Products List</h4>
          <button id="btn-add" class="btn btn-success">+ Add Product</button>
        </div>

        <!-- Bulk panel -->
        <div id="bulk-panel"
             class="d-flex align-items-center bg-light border rounded px-3 py-2 mb-3"
             style="display: none;">
          <small id="selected-count" class="me-3">0 selected</small>
          <a href="#" id="select-all-link" class="me-3">
            Select all <span id="total-count"><?= count($products) ?></span>
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
        <table id="products-table" class="table table-striped dt-responsive nowrap w-100">
          <thead>
            <tr>
              <th><input type="checkbox" id="select-all"></th>
              <th>ID</th>
              <th>Name</th>
              <th>SKU</th>
              <th>Description</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Created At</th>
              <th>Updated At</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $p): ?>
              <tr
                data-id="<?= $p['id'] ?>"
                data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
                data-sku="<?= htmlspecialchars($p['sku'], ENT_QUOTES) ?>"
                data-description="<?= htmlspecialchars($p['description'], ENT_QUOTES) ?>"
                data-price="<?= $p['price'] ?>"
                data-stock="<?= $p['stock'] ?>"
              >
                <td><input type="checkbox" class="row-checkbox"></td>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['sku']) ?></td>
                <td><?= htmlspecialchars($p['description']) ?></td>
                <td><?= number_format($p['price'],2) ?></td>
                <td><?= $p['stock'] ?></td>
                <td><?= $p['created_at'] ?></td>
                <td><?= $p['updated_at'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

      </div>
    </div>
  </div>
</div>

<!-- Offcanvas form for Add / Edit -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="productCanvas">
  <div class="offcanvas-header">
    <h5 id="canvas-title">Add Product</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <form id="product-form"
          method="POST"
          action="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES) ?>">
      <input type="hidden" name="action" id="form-action" value="create">
      <input type="hidden" name="id"     id="form-id">

      <div class="mb-3">
        <label for="form-name" class="form-label">Name *</label>
        <input type="text" class="form-control" id="form-name" name="name" required>
      </div>
      <div class="mb-3">
        <label for="form-sku" class="form-label">SKU *</label>
        <input type="text" class="form-control" id="form-sku" name="sku" required>
      </div>
      <div class="mb-3">
        <label for="form-description" class="form-label">Description</label>
        <textarea class="form-control" id="form-description" name="description" rows="2"></textarea>
      </div>
      <div class="mb-3">
        <label for="form-price" class="form-label">Price</label>
        <input type="number" step="0.01" class="form-control" id="form-price" name="price" value="0.00">
      </div>
      <div class="mb-3">
        <label for="form-stock" class="form-label">Stock</label>
        <input type="number" class="form-control" id="form-stock" name="stock" value="0">
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
  const table             = $('#products-table').DataTable({ responsive: true, order: [[2,'asc']] });

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

  $('#products-table tbody').on('change', '.row-checkbox', updateBulkPanel);
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
    if (!ids.length) return alert('No products selected.');
    if (confirm('Delete selected product(s)?')) {
      bulkIds.value = ids.join(',');
      bulkForm.submit();
    }
  });

  document.getElementById('bulk-edit').addEventListener('click', function(e) {
    e.preventDefault();
    const ids = getSelectedIds();
    if (ids.length !== 1) return alert('Please select exactly one product to edit.');
    const tr = document.querySelector(`tr[data-id="${ids[0]}"]`);
    const offcanvas = new bootstrap.Offcanvas(document.getElementById('productCanvas'));

    document.getElementById('canvas-title').textContent = 'Edit Product';
    document.getElementById('form-action').value        = 'update';
    document.getElementById('form-id').value            = tr.dataset.id;
    ['name','sku','description','price','stock'].forEach(field => {
      document.getElementById('form-' + field).value = tr.dataset[field];
    });
    document.getElementById('canvas-submit').textContent = 'Update';
    offcanvas.show();
  });

  document.getElementById('btn-add').addEventListener('click', function() {
    const offcanvas = new bootstrap.Offcanvas(document.getElementById('productCanvas'));
    document.getElementById('canvas-title').textContent = 'Add Product';
    document.getElementById('form-action').value        = 'create';
    document.getElementById('form-id').value            = '';
    ['name','sku','description','price','stock'].forEach(field => {
      const el = document.getElementById('form-' + field);
      el.value = (field === 'price' ? '0.00' : field === 'stock' ? '0' : '');
    });
    document.getElementById('canvas-submit').textContent = 'Add';
    offcanvas.show();
  });
});
</script>
