<?php
require_once __DIR__ . '/layouts/config.php';

// Auto-generate a SKU with optional prefix, e.g. PROD-20240810-4D6A
function generateSKU($prefix = 'PROD') {
    return $prefix . '-' . date('Ymd') . '-' . strtoupper(substr(uniqid('', true), -4));
}

// --- Handle Add Product ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $tenant_id = $_SESSION['tenant_id'] ?? 1;
    $name = mysqli_real_escape_string($link, $_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $sku = trim(mysqli_real_escape_string($link, $_POST['sku'] ?? ''));
    $description = mysqli_real_escape_string($link, $_POST['description'] ?? '');
    $status = mysqli_real_escape_string($link, $_POST['status'] ?? 'active');
    $stock = intval($_POST['stock'] ?? 0);
    $photo_url = mysqli_real_escape_string($link, $_POST['photo_url'] ?? '');
    $lot_area = floatval($_POST['lot_area'] ?? 0);
    $price_per_sqm = floatval($_POST['price_per_sqm'] ?? 0);
    $lot_class = mysqli_real_escape_string($link, $_POST['lot_class'] ?? '');
    $color = mysqli_real_escape_string($link, $_POST['color'] ?? '');
    $color_code = mysqli_real_escape_string($link, $_POST['color_code'] ?? '');
    $material = mysqli_real_escape_string($link, $_POST['material'] ?? '');
    $hardware = mysqli_real_escape_string($link, $_POST['hardware'] ?? '');
    $size = mysqli_real_escape_string($link, $_POST['size'] ?? '');
    $availability = mysqli_real_escape_string($link, $_POST['availability'] ?? '');
    $image = mysqli_real_escape_string($link, $_POST['image'] ?? '');

    // --- SKU auto-generation logic ---
    if ($sku === '') {
        // Ensure no duplicate SKU!
        do {
            $sku = generateSKU();
            $q = $link->prepare("SELECT COUNT(*) FROM products WHERE sku=?");
            $q->bind_param('s', $sku);
            $q->execute();
            $q->bind_result($count);
            $q->fetch();
            $q->close();
        } while ($count > 0);
    }

$insert_sql = "INSERT INTO products 
    (tenant_id, name, price, category_id, sku, description, status, stock, photo_url, lot_area, price_per_sqm, lot_class, color, color_code, material, hardware, size, availability, image)
    VALUES ($tenant_id, '$name', $price, $category_id, '$sku', '$description', '$status', $stock, '$photo_url', $lot_area, $price_per_sqm, '$lot_class', '$color', '$color_code', '$material', '$hardware', '$size', '$availability', '$image')";

    mysqli_query($link, $insert_sql);
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// --- Handle Edit Product (AJAX POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $id = intval($_POST['product_id']);
    $name = mysqli_real_escape_string($link, $_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $sku = mysqli_real_escape_string($link, $_POST['sku'] ?? '');
    $description = mysqli_real_escape_string($link, $_POST['description'] ?? '');
    $status = mysqli_real_escape_string($link, $_POST['status'] ?? 'active');
    $stock = intval($_POST['stock'] ?? 0);
    $photo_url = mysqli_real_escape_string($link, $_POST['photo_url'] ?? '');
    $lot_area = floatval($_POST['lot_area'] ?? 0);
    $price_per_sqm = floatval($_POST['price_per_sqm'] ?? 0);
    $lot_class = mysqli_real_escape_string($link, $_POST['lot_class'] ?? '');
    $color = mysqli_real_escape_string($link, $_POST['color'] ?? '');
    $color_code = mysqli_real_escape_string($link, $_POST['color_code'] ?? '');
    $material = mysqli_real_escape_string($link, $_POST['material'] ?? '');
    $hardware = mysqli_real_escape_string($link, $_POST['hardware'] ?? '');
    $size = mysqli_real_escape_string($link, $_POST['size'] ?? '');
    $availability = mysqli_real_escape_string($link, $_POST['availability'] ?? '');
    $image = mysqli_real_escape_string($link, $_POST['image'] ?? '');

    $update_sql = "UPDATE products SET 
        name='$name', price=$price, category_id=$category_id, sku='$sku', description='$description', status='$status', stock=$stock,
        photo_url='$photo_url', lot_area=$lot_area, price_per_sqm=$price_per_sqm, lot_class='$lot_class', color='$color', color_code='$color_code',
        material='$material', hardware='$hardware', size='$size', availability='$availability', image='$image', updated_at=NOW()
        WHERE id=$id";
    $ok = mysqli_query($link, $update_sql);
    echo json_encode(['status' => $ok ? 'success' : 'error']);
    exit;
}

// --- Bulk Delete ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_delete']) && !empty($_POST['selected'])) {
    $ids = array_map('intval', $_POST['selected']);
    $in  = implode(',', $ids);
    if ($in) {
        mysqli_query($link, "SET FOREIGN_KEY_CHECKS=0");
        $del = mysqli_query($link, "DELETE FROM products WHERE id IN ($in)");
        mysqli_query($link, "SET FOREIGN_KEY_CHECKS=1");
    }
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// --- Fetch Products for table ---
$sql = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.updated_at DESC";
$result = mysqli_query($link, $sql);
$products = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}

// --- Fetch Categories for dropdown ---
$categories = [];
$cat_res = mysqli_query($link, "SELECT id, name FROM categories ORDER BY name ASC");
while ($cat = mysqli_fetch_assoc($cat_res)) {
    $categories[] = $cat;
}
?>

<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Products Management | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <link href="assets/vendor/remixicon/fonts/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <?php include 'layouts/head-css.php'; ?>

<style>
    .drawer-modal.right {
    position: fixed;
    top: 0; right: 0; height: 100vh;
    width: 420px; max-width: 100vw;
    background: #fff; z-index: 1080;
    box-shadow: -2px 0 24px rgba(18, 38, 63, 0.14);
    transition: transform .2s;
    transform: translateX(100%);
    overflow-y: auto;
    border-radius: 0 0 0 1.5rem;
    display: flex;
    flex-direction: column;
}
.drawer-modal.right.show {
    transform: translateX(0);
}
.drawer-modal .modal-header {
    border-bottom: 1px solid #f0f0f0;
    padding: 1rem 1.5rem;
    background: #f8fafb;
    font-size: 1.1rem;
    font-weight: 500;
}
.drawer-modal .modal-title {
    font-weight: 500;
    font-size: 1.13rem;
    color: #18244e;
    margin: 0;
}
.drawer-modal .btn-close {
    margin-left: auto;
    background: transparent;
    font-size: 1.1rem;
}
.drawer-modal .modal-body {
    flex: 1 1 auto;
    padding: 1.5rem 1.5rem 2rem 1.5rem;
    background: #fff;
    overflow-y: auto;
}
.drawer-modal .modal-body .mb-2 {
    margin-bottom: 1.15rem !important;
}
.drawer-modal .form-label {
    font-weight: 500;
    color: #666e80;
    font-size: 0.97rem;
    margin-bottom: 0.2rem;
}
.drawer-modal .form-control, .drawer-modal .form-select, .drawer-modal textarea {
    border-radius: 0.45rem;
    border: 1px solid #e0e6ed;
    background: #f8fafb;
    font-size: 1rem;
    padding: 0.56rem 0.8rem;
    transition: border .2s;
}
.drawer-modal .form-control:focus, .drawer-modal .form-select:focus, .drawer-modal textarea:focus {
    border-color: #7da6ff;
    background: #fff;
    box-shadow: 0 0 0 1.5px #c2d8fa;
}
.drawer-modal .modal-footer {
    border-top: 1px solid #f0f0f0;
    padding: 1rem 1.5rem;
    background: #fff;
    justify-content: flex-end;
    gap: 0.7rem;
    position: sticky;
    bottom: 0;
    z-index: 2;
}
.drawer-modal .btn {
    min-width: 100px;
    font-size: 1rem;
    border-radius: 0.45rem;
}
.drawer-modal .btn-secondary {
    background: #f3f5f8;
    color: #343a40;
    border: none;
}
.drawer-modal .btn-primary {
    background: #3558e5;
    border: none;
    color: #fff;
    font-weight: 500;
    transition: background 0.15s;
}
.drawer-modal .btn-primary:hover {
    background: #2742a8;
}
@media (max-width: 575px) {
    .drawer-modal.right {
        width: 100vw !important;
        border-radius: 0 !important;
    }
    .drawer-modal .modal-header,
    .drawer-modal .modal-body,
    .drawer-modal .modal-footer {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
}

.custom-selected-actions {
    display: none;
    align-items: center;
    gap: 0.85em;
    margin-left: 0.5em;
    font-size: 15px;
    white-space: nowrap;
    background: none;
    padding: 0.2em 0.7em;
    border-radius: 0.33em;
    min-height: 32px;
}
.custom-selected-actions > span,
.custom-selected-actions > a {
    display: inline-block;
    margin: 0 !important;
    line-height: 1.7;
    vertical-align: middle;
}
.custom-selected-actions .custom-link,
.custom-selected-actions .custom-link.text-danger {
    text-decoration: underline;
    font-weight: 400;
    font-size: 15px;
    cursor: pointer;
    padding: 0 2px;
}
.custom-selected-actions .custom-link.text-danger {
    color: #eb2f2f;
}
.custom-selected-actions .custom-link.text-danger:hover {
    color: #c82333;
}
.custom-selected-actions .custom-link {
    color: #1677ff;
}
.custom-selected-actions .custom-link:hover {
    color: #0d6efd;
}

.mb-3 {
    margin-bottom: 1.5rem !important;
    margin-top: 15px !important;;
}

.table-responsive {
    padding-left: 23px;
    padding-right: 23px;
    padding-top: 20px;
}

.dropdown-menu {
    min-width: 180px;
    border-radius: 0.75rem;
    box-shadow: 0 4px 24px rgba(18, 38, 63, 0.11);
}
.dropdown-item i {
    font-size: 1.15em;
    vertical-align: middle;
}
.dropdown-item {
    padding: 0.7em 1.2em;
}

</style>

</head>
<body>
<div class="wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="content-page">
        <div class="content">
            <div class="container-fluid py-4">

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h3 class="mb-0">Products</h3>

                                    <div class="d-flex gap-2">
                                        <!-- Actions Dropdown -->
                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="actionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-settings-3-line"></i> Actions
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="actionsDropdown">                                                
                                                <li>
                                                    <a class="dropdown-item" href="products-grid.php">
                                                        <i class="ri-grid-fill me-2"></i>Grid View
                                                    </a>
                                                </li>
                                                <li>                                    
                                                    <a class="dropdown-item" href="#" id="importAction">
                                                        <i class="ri-upload-2-line me-2"></i>Import Products
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" id="exportAction">
                                                        <i class="ri-download-2-line me-2"></i>Export to CSV
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <!-- Add Product button remains -->
                                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                            <i class="ri-add-circle-line"></i> Add Product
                                        </button>
                                    </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom DataTable controls row -->
                <div class="custom-datatable-controls mb-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="custom-datatable-searchrow d-flex align-items-center gap-2 flex-nowrap">
                        <div class="input-group flex-nowrap">
                            <span class="input-group-text bg-white"><i class="ri-search-line"></i></span>
                            <input type="search" id="customSearchBox" class="form-control" placeholder="Search products...">
                        </div>
                        <div class="custom-selected-actions" id="selectedActions" style="display:none; margin-left:12px;">
                            <span id="selectedCount">0 selected</span>
                            <span class="ms-2">
                                <a href="#" class="custom-link" id="selectAllBtn">Select all <span id="totalCount"></span></a>
                            </span>
                            <span class="ms-2">
                                <a href="#" class="custom-link text-danger" id="deleteBtn" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Delete</a>
                            </span>
                        </div>
                    </div>
                    <div class="custom-datatable-length">
                        <label class="d-flex align-items-center mb-0" style="gap: .5em;">
                            Show
                            <select id="customLengthBox" class="form-select form-select-sm ms-2" style="width:auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            entries
                        </label>
                    </div>
                </div>

                <form method="post" id="productsForm">
                    <input type="hidden" name="bulk_delete" value="1">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="datatable" class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th style="width:40px;"><input type="checkbox" id="selectAll"></th>
                                        <th>Product Name</th>
                                        <th>SKU</th>
                                        <th>Price</th>
                                        <th>Stocks</th>
                                        <th>Category</th>
                                        <th>Last Updated</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($products)): ?>
                                        <?php foreach ($products as $row): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="row-checkbox" name="selected[]" value="<?= (int)$row['id'] ?>">
                                            </td>
                                            <td>
                                                <a href="#" class="product-link" data-id="<?= (int)$row['id'] ?>">
                                                    <?= htmlspecialchars($row['name'] ?? '') ?>
                                                </a>
                                            </td>
                                            <td><?= htmlspecialchars($row['sku'] ?? '-') ?></td>
                                            <td>₱<?= number_format($row['price'], 2) ?></td>
                                            <td><?= (int)$row['stock'] ?></td>
                                            <td><?= htmlspecialchars($row['category_name'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($row['updated_at'] ?? ''))) ?></td>
                                        </tr>
                                        <?php endforeach; ?>

                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No products found.</td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php include 'layouts/footer.php'; ?>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">SKU</label>
                    <input type="text" name="sku" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">Select category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Stock</label>
                    <input type="number" name="stock" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="discontinued">Discontinued</option>
                    </select>
                </div>
                <!-- Add more fields as needed, matching your schema -->
            </div>
            <div class="modal-footer">
                <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Product Right-Side Modal (Drawer) -->
<div class="drawer-modal right" id="editProductDrawer">
    <form id="editProductForm" class="modal-content" style="height:100%;border:none;">
        <div class="modal-header">
            <h5 class="modal-title">Edit Product</h5>
            <button type="button" class="btn-close drawer-close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="product_id" id="editProductId">
            <div class="mb-2">
                <label class="form-label">Product Name</label>
                <input type="text" name="name" id="editName" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">SKU</label>
                <input type="text" name="sku" id="editSku" class="form-control">
            </div>
            <div class="mb-2">
                <label class="form-label">Description</label>
                <textarea name="description" id="editDescription" class="form-control"></textarea>
            </div>
            <div class="mb-2">
                <label class="form-label">Category</label>
                <select name="category_id" id="editCategoryId" class="form-select">
                    <option value="">Select category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label">Price</label>
                <input type="number" step="0.01" name="price" id="editPrice" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Stock</label>
                <input type="number" name="stock" id="editStock" class="form-control">
            </div>
            <div class="mb-2">
                <label class="form-label">Status</label>
                <select name="status" id="editStatus" class="form-select">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="discontinued">Discontinued</option>
                </select>
            </div>
            <!-- Add more fields here as needed -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary drawer-close">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>
<div class="drawer-backdrop" id="drawerBackdrop"></div>

<!-- Confirm Delete Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the selected product(s)? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="modalConfirmDeleteBtn" class="btn btn-danger">Yes, Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importProductsModal" tabindex="-1" aria-labelledby="importProductsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="import_products.php" method="post" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importProductsModalLabel">Import Products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label for="importFile" class="form-label">Choose CSV file</label>
                    <input type="file" name="importFile" id="importFile" class="form-control" accept=".csv" required>
                </div>
                <small class="text-muted">CSV columns: name, sku, description, price, category_id, status, stock, etc.</small>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Import</button>
            </div>
        </form>
    </div>
</div>

<!-- Export Modal (Drag fields) -->
<div class="modal fade" id="exportProductsModal" tabindex="-1" aria-labelledby="exportProductsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="exportFieldsForm" onsubmit="return false;">
        <div class="modal-header">
          <h5 class="modal-title" id="exportProductsModalLabel">Export Products</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <h6>All Fields</h6>
              <ul id="allFieldsList" class="list-group min-vh-50"></ul>
            </div>
            <div class="col-md-6">
              <h6>Current Fields (will be exported, drag to reorder)</h6>
              <ul id="currentFieldsList" class="list-group min-vh-50"></ul>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <small class="text-muted me-auto">Drag fields between lists and reorder as needed.</small>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="exportCSVBtn">Export</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'layouts/right-sidebar.php'; ?>
<?php include 'layouts/footer-scripts.php'; ?>

<script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // DataTable init
    var table = $('#datatable').DataTable({
        responsive: true,
        pageLength: 10,
        dom: 'tip'
    });

    $('#customSearchBox').on('keyup change', function () {
        table.search(this.value).draw();
    });

    $('#customLengthBox').on('change', function () {
        table.page.len(this.value).draw();
    });

    const checkboxes = () => Array.from(document.querySelectorAll('.row-checkbox'));
    const selectAllBox = document.getElementById('selectAll');
    const selectedActions = document.getElementById('selectedActions');
    const selectedCount = document.getElementById('selectedCount');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const deleteBtn = document.getElementById('deleteBtn');
    const productsForm = document.getElementById('productsForm');
    const modalConfirmDeleteBtn = document.getElementById('modalConfirmDeleteBtn');

    function updateSelectedActions() {
        let allRows = checkboxes();
        let checked = allRows.filter(cb => cb.checked);
        selectedCount.textContent = `${checked.length} selected`;
        if (checked.length > 0) {
            selectedActions.style.display = 'flex';
            selectAllBtn.style.display = (checked.length < allRows.length) ? 'inline' : 'none';
            selectAllBtn.textContent = `Select all ${allRows.length}`;
        } else {
            selectedActions.style.display = 'none';
        }
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('row-checkbox') || e.target === selectAllBox) {
            let all = checkboxes();
            let checked = all.filter(cb => cb.checked);
            if (e.target === selectAllBox) {
                for (let cb of all) cb.checked = selectAllBox.checked;
            } else if (!e.target.checked && selectAllBox.checked) {
                selectAllBox.checked = false;
            } else if (checked.length === all.length) {
                selectAllBox.checked = true;
            }
            updateSelectedActions();
        }
    });

    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            let all = checkboxes();
            for (let cb of all) cb.checked = true;
            selectAllBox.checked = true;
            updateSelectedActions();
        });
    }

    if (modalConfirmDeleteBtn) {
        modalConfirmDeleteBtn.addEventListener('click', function() {
            productsForm.submit();
        });
    }

    table.on('draw', function() {
        updateSelectedActions();
    });

    // Right Drawer Modal logic
    const drawer = document.getElementById('editProductDrawer');
    const drawerBackdrop = document.getElementById('drawerBackdrop');
    function showDrawer() {
        drawer.classList.add('show');
        drawerBackdrop.classList.add('show');
        document.body.classList.add('modal-open');
    }
    function hideDrawer() {
        drawer.classList.remove('show');
        drawerBackdrop.classList.remove('show');
        document.body.classList.remove('modal-open');
    }
    document.querySelectorAll('.drawer-close').forEach(btn => {
        btn.addEventListener('click', hideDrawer);
    });
    drawerBackdrop.addEventListener('click', hideDrawer);

    // Edit Product: open drawer with AJAX data
    document.querySelectorAll('.product-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var id = this.getAttribute('data-id');
            fetch('get_product.php?id=' + id)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        var p = data.product;
                        document.getElementById('editProductId').value = p.id;
                        document.getElementById('editName').value = p.name ?? '';
                        document.getElementById('editSku').value = p.sku ?? '';
                        document.getElementById('editDescription').value = p.description ?? '';
                        document.getElementById('editCategoryId').value = p.category_id ?? '';
                        document.getElementById('editPrice').value = p.price ?? '';
                        document.getElementById('editStock').value = p.stock ?? '';
                        document.getElementById('editStatus').value = p.status ?? '';
                        showDrawer();
                    }
                });
        });
    });

    // Edit Product: submit form AJAX
    document.getElementById('editProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var form = this;
        var fd = new FormData(form);
        fd.append('edit_product', 1);
        fetch('', {
            method: 'POST',
            body: fd
        })
        .then(resp => resp.json())
        .then(data => {
            if (data.status === 'success') {
                hideDrawer();
                location.reload();
            } else {
                alert('Error saving changes!');
            }
        });
    });

    // --- Export Modal logic ---
    const allFields = [
        {field: 'id', label: 'ID'},
        {field: 'name', label: 'Product Name'},
        {field: 'sku', label: 'SKU'},
        {field: 'description', label: 'Description'},
        {field: 'price', label: 'Price'},
        {field: 'category_id', label: 'Category ID'},
        {field: 'stock', label: 'Stock'},
        {field: 'status', label: 'Status'},
        {field: 'created_at', label: 'Created At'},
        {field: 'updated_at', label: 'Updated At'}
    ];
    const defaultCurrentFields = [
        {field: 'name', label: 'Product Name'},
        {field: 'price', label: 'Price'},
        {field: 'category_id', label: 'Category ID'},
        {field: 'updated_at', label: 'Last Updated'}
    ];

    $('#exportProductsModal').on('show.bs.modal', function () {
        let current = JSON.parse(JSON.stringify(defaultCurrentFields));
        let currentFieldNames = current.map(f => f.field);
        let available = allFields.filter(f => !currentFieldNames.includes(f.field));
        $('#allFieldsList').empty();
        $('#currentFieldsList').empty();
        available.forEach(f => {
            $('#allFieldsList').append(
                `<li class="list-group-item" data-field="${f.field}">${f.label}</li>`
            );
        });
        current.forEach(f => {
            $('#currentFieldsList').append(
                `<li class="list-group-item" data-field="${f.field}">${f.label}</li>`
            );
        });
    });

    let sortable1, sortable2;
    $('#exportProductsModal').on('shown.bs.modal', function() {
        if (sortable1) sortable1.destroy();
        if (sortable2) sortable2.destroy();
        sortable1 = Sortable.create(document.getElementById('allFieldsList'), {
            group: { name: 'fields', pull: 'clone', put: true },
            sort: false,
            animation: 150
        });
        sortable2 = Sortable.create(document.getElementById('currentFieldsList'), {
            group: { name: 'fields', pull: true, put: true },
            sort: true,
            animation: 150
        });
    });

    document.getElementById('exportFieldsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let fields = [];
        $('#currentFieldsList li').each(function() {
            fields.push({
                field: $(this).data('field'),
                label: $(this).text()
            });
        });
        if (!fields.length) {
            alert('Please add at least one field to export.');
            return;
        }
        let products = window.exportProductsData || [];
        if (!products.length) {
            alert('No products to export.');
            return;
        }
        let csvRows = [];
        csvRows.push(fields.map(f => `"${f.label}"`).join(',')); // header
        products.forEach(row => {
            let vals = [];
            fields.forEach(f => {
                let val = row[f.field];
                if (typeof val === "undefined" || val === null) val = '';
                vals.push(`"${String(val).replace(/"/g,'""')}"`);
            });
            csvRows.push(vals.join(','));
        });
        let csvContent = csvRows.join('\r\n');
        let blob = new Blob([csvContent], {type: 'text/csv'});
        let url = URL.createObjectURL(blob);
        let link = document.createElement('a');
        link.href = url;
        let dt = new Date();
        link.download = 'products_export_' + dt.getFullYear() + ('0'+(dt.getMonth()+1)).slice(-2) + ('0'+dt.getDate()).slice(-2) + '.csv';
        document.body.appendChild(link);
        link.click();
        setTimeout(function(){ document.body.removeChild(link); URL.revokeObjectURL(url); }, 200);
        $('#exportProductsModal').modal('hide');
    });

    // Update the total count
    document.getElementById('totalCount').textContent = checkboxes().length;

    // Provide products data to JS (for CSV export)
    window.exportProductsData = <?php echo json_encode($products, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
        });
        </script>
        <script src="assets/js/app.min.js"></script>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Open Import Modal
            document.getElementById('importAction').addEventListener('click', function(e) {
                e.preventDefault();
                var importModal = new bootstrap.Modal(document.getElementById('importProductsModal'));
                importModal.show();
            });
            // Open Export Modal
            document.getElementById('exportAction').addEventListener('click', function(e) {
                e.preventDefault();
                var exportModal = new bootstrap.Modal(document.getElementById('exportProductsModal'));
                exportModal.show();
            });
        });
        </script>

</body>
</html>
