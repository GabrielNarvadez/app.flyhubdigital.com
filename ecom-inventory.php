<?php
require_once __DIR__ . '/layouts/config.php';
define('DEFAULT_TENANT_ID', 12);
$tenant_id = $_SESSION['tenant_id'] ?? DEFAULT_TENANT_ID;

// ... [Handle Add/Edit/Delete code remains unchanged] ...

// Fetch Products with barcode and channel fields
$sql = "
  SELECT 
    p.*, c.name AS category_name,
    IFNULL(p.barcode, '') AS barcode,
    p.channel_shopify, p.channel_shopee, p.channel_pos
  FROM products p
  LEFT JOIN categories c ON p.category_id = c.id
  ORDER BY p.updated_at DESC
";
$result = mysqli_query($link, $sql);
$products = [];
if ($result && mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) $products[] = $row;
}

$categories = [];
$cat_res = mysqli_query($link, "SELECT id, name FROM categories ORDER BY name ASC");
while ($cat = mysqli_fetch_assoc($cat_res)) $categories[] = $cat;

// --- Channel status helper ---
function channel_icon($status, $platform) {
  $icons = [
    'shopify' => 'ri-store-line',
    'shopee'  => 'ri-shopping-bag-2-line',
    'pos'     => 'ri-terminal-box-line'
  ];
  $colors = [
    'ok'     => 'text-success',
    'error'  => 'text-danger',
    'syncing'=> 'text-warning',
    'none'   => 'text-muted'
  ];
  $labels = [
    'ok'     => 'Synced',
    'error'  => 'Error',
    'syncing'=> 'Syncing',
    'none'   => 'Not Connected'
  ];
  $icon = $icons[$platform] ?? 'ri-link';
  $color = $colors[$status] ?? 'text-muted';
  $label = $labels[$status] ?? 'N/A';
  return "<span class='$color' data-bs-toggle='tooltip' title='$label'><i class='$icon'></i> ●</span>";
}
?>

<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Products & Inventory | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <link href="assets/vendor/remixicon/fonts/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <?php include 'layouts/head-css.php'; ?>
<style>
    /* Only new/modified styles shown for brevity */
    .barcode-col { font-size: 1.09rem; }
    .barcode-btn { border: none; background: none; color: #1564c0; cursor: pointer; }
    .barcode-btn:hover { color: #22458d; }
    .channel-dot { font-size: 1.14rem; margin-right: 2px; }
    .stock-alert { background:#ffe4e1; color:#c0392b; border-radius:7px; font-weight:500; padding:2px 8px;}
    .variant-badge { background:#e4f7fa; color:#21717c; font-size:12px; border-radius:5px; padding:2px 8px;}
    .channel-filter { width:auto; min-width:170px; }
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
                                <h3 class="mb-0">Products & Inventory</h3>
                                <div class="d-flex gap-2">
                                    <select class="form-select channel-filter" id="channelFilter">
                                        <option value="">All Channels</option>
                                        <option value="shopify">Shopify</option>
                                        <option value="shopee">Shopee</option>
                                        <option value="pos">POS</option>
                                    </select>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="actionsDropdown" data-bs-toggle="dropdown">
                                            <i class="ri-settings-3-line"></i> Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" id="syncAllBtn"><i class="ri-refresh-line me-2"></i>Sync All</a></li>
                                            <li><a class="dropdown-item" href="products-grid.php"><i class="ri-grid-fill me-2"></i>Grid View</a></li>
                                            <li><a class="dropdown-item" href="#" id="importAction"><i class="ri-upload-2-line me-2"></i>Import Products</a></li>
                                            <li><a class="dropdown-item" href="#" id="exportAction"><i class="ri-download-2-line me-2"></i>Export to CSV</a></li>
                                        </ul>
                                    </div>
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
                        <div class="input-group flex-nowrap" style="padding-top: 15px;">
                            <span class="input-group-text bg-white"><i class="ri-search-line"></i></span>
                            <input type="search" id="customSearchBox" class="form-control" placeholder="Search products...">
                        </div>
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
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>Product Name</th>
                                        <th>SKU</th>
                                        <th class="barcode-col">Barcode</th>
                                        <th>Price</th>
                                        <th>Stocks</th>
                                        <th>Variants</th>
                                        <th>Category</th>
                                        <th>Shopify</th>
                                        <th>POS</th>
                                        <th>Last Updated</th>
                                        <th>Sync</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($products as $row): ?>
                                        <tr data-channels="<?= 
                                            ($row['channel_shopify'] ? 'shopify ' : '') .
                                            ($row['channel_pos'] ? 'pos' : '')
                                         ?>">
                                            <td><input type="checkbox" class="row-checkbox" name="selected[]" value="<?= (int)$row['id'] ?>"></td>
                                            <td>
                                                <a href="#" class="product-link" data-id="<?= (int)$row['id'] ?>">
                                                    <?= htmlspecialchars($row['name'] ?? '') ?>
                                                </a>
                                            </td>
                                            <td><?= htmlspecialchars($row['sku'] ?? '-') ?></td>
                                            <!-- Barcode/Scan -->
                                            <td class="barcode-col">
                                                <?php if (!empty($row['barcode'])): ?>
                                                    <?= htmlspecialchars($row['barcode']) ?>
                                                <?php else: ?>
                                                    <button class="barcode-btn" type="button" title="Scan barcode" data-id="<?= (int)$row['id'] ?>" data-bs-toggle="modal" data-bs-target="#scanBarcodeModal">
                                                        <i class="ri-barcode-box-line"></i> Scan
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                            <td>₱<?= number_format($row['price'], 2) ?></td>
                                            <td>
                                                <?php if ($row['stock'] < 5): ?>
                                                    <span class="stock-alert"><?= (int)$row['stock'] ?></span>
                                                <?php else: ?>
                                                    <?= (int)$row['stock'] ?>
                                                <?php endif; ?>
                                            </td>
                                            <!-- Variants badge, for now static placeholder -->
                                            <td>
                                                <?php if (!empty($row['variant'])): ?>
                                                    <span class="variant-badge"><?= htmlspecialchars($row['variant']) ?></span>
                                                <?php else: ?>–<?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($row['category_name'] ?? '-') ?></td>
                                            <!-- Channel icons -->
                                            <td><?= channel_icon($row['channel_shopify'] ?? 'none', 'shopify') ?></td>
                                            <td><?= channel_icon($row['channel_pos'] ?? 'none', 'pos') ?></td>
                                            <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($row['updated_at'] ?? ''))) ?></td>
                                            <td>
                                                <button class="btn btn-outline-secondary btn-sm sync-product-btn" title="Sync Now" data-id="<?= (int)$row['id'] ?>">
                                                    <i class="ri-refresh-line"></i>
                                                </button>
                                            </td>
                                            <td><!-- Actions (edit, etc.) --></td>
                                        </tr>
                                    <?php endforeach; ?>
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

<!-- Barcode Scan Modal -->
<div class="modal fade" id="scanBarcodeModal" tabindex="-1" aria-labelledby="scanBarcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan Barcode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Point your barcode scanner to the input below, or manually enter the barcode value.</p>
                <input type="text" id="barcodeInput" class="form-control" placeholder="Scan or enter barcode">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveBarcodeBtn">Save</button>
            </div>
        </div>
    </div>
</div>

<?php include 'layouts/right-sidebar.php'; ?>
<?php include 'layouts/footer-scripts.php'; ?>
<script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function(){
    // DataTable as before
    var table = $('#datatable').DataTable({
        responsive: true, pageLength: 10, dom: 'tip'
    });
    $('#customSearchBox').on('keyup change', function(){ table.search(this.value).draw(); });
    $('#channelFilter').on('change', function(){
        var val = $(this).val();
        table.rows().every(function(){
            var row = $(this.node());
            if(!val || row.attr('data-channels').includes(val)) {
                row.show();
            } else {
                row.hide();
            }
        });
    });
    // Barcode Scan Modal
    let currentProductId = null;
    $('.barcode-btn').on('click', function(){
        currentProductId = $(this).data('id');
        $('#barcodeInput').val('');
    });
    $('#saveBarcodeBtn').on('click', function(){
        var val = $('#barcodeInput').val().trim();
        if (!val) return;
        $.post('save_barcode.php', {id: currentProductId, barcode: val}, function(resp){
            location.reload();
        });
    });
    // Tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    // Sync Now
    $('.sync-product-btn').on('click', function(){
        var id = $(this).data('id');
        // Placeholder: call your sync endpoint here!
        alert('Sync product ' + id + ' (implement API call)');
    });
    $('#syncAllBtn').on('click', function(){ alert('Syncing all products... (implement API call)'); });
});
</script>
<script src="assets/js/app.min.js"></script>
</body>
</html>
