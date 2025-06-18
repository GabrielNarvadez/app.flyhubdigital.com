<?php
// 1. INIT: Load session, config, and main layout
require_once __DIR__ . '/layouts/session.php';
require_once __DIR__ . '/layouts/config.php'; // This should define $link
require_once __DIR__ . '/layouts/main.php';

// 2. CHECK: Database connection
if (!isset($link) || !$link) {
    echo '<div class="alert alert-danger">Database connection error.</div>';
    return;
}

// --- STATUS ARRAYS ---
$statuses = [
    'all'      => 'All Status',
    'active'   => 'Active',
    'draft'    => 'Draft',
    'archived' => 'Archived'
];

// --- CATEGORY FETCH ---
$categories = [];
$res_cat = mysqli_query($link, "SELECT id, name FROM categories ORDER BY name");
while ($cat = mysqli_fetch_assoc($res_cat)) $categories[$cat['id']] = $cat['name'];

// --- FILTER HANDLING ---
$category_filter = $_GET['category'] ?? '';
$status_filter   = $_GET['status'] ?? '';

$where = [];
if ($category_filter !== '' && $category_filter !== 'all') {
    $where[] = "p.category_id = '".intval($category_filter)."'";
}
if ($status_filter !== '' && $status_filter !== 'all') {
    $where[] = "p.status = '".mysqli_real_escape_string($link, $status_filter)."'";
}
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// --- QUERY: Products ---
$sql = "SELECT p.id, p.name, p.description, p.price, p.stock, p.product_type, p.status, p.category_id
        FROM products p
        $whereSQL
        ORDER BY p.id DESC";
$result = mysqli_query($link, $sql);
if (!$result) {
    echo '<div class="alert alert-danger">Failed to fetch products: ' . htmlspecialchars(mysqli_error($link)) . '</div>';
    $result = false;
}

// --- CARD 1: Units Sold ---
$units_sql = "SELECT COUNT(*) AS units_sold FROM invoices WHERE status IN ('paid', 'sent')";
$units_res = mysqli_query($link, $units_sql);
$units_row = mysqli_fetch_assoc($units_res);
$units_sold = $units_row['units_sold'] ?? 0;

// --- CARD 2: Revenue YTD (Invoices + SOAs) ---
$ytd_invoice_sql = "SELECT SUM(total) AS total FROM invoices WHERE status = 'paid' AND YEAR(issue_date) = YEAR(CURDATE())";
$ytd_invoice_res = mysqli_query($link, $ytd_invoice_sql);
$ytd_invoice = mysqli_fetch_assoc($ytd_invoice_res)['total'] ?? 0;

$ytd_soa_sql = "SELECT SUM(total_paid) AS total FROM soas WHERE YEAR(issue_date) = YEAR(CURDATE())";
$ytd_soa_res = mysqli_query($link, $ytd_soa_sql);
$ytd_soa = mysqli_fetch_assoc($ytd_soa_res)['total'] ?? 0;

$revenue_ytd = ($ytd_invoice ?: 0) + ($ytd_soa ?: 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Products Kanban | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>

    <style>
    /* Reduce vertical space above and below filters */
    .card-body .row.align-items-center.mb-2 {
        margin-bottom: 20px !important;
        margin-top: 0 !important;
    }
    .card-body .form-select-sm {
        padding-top: 3px;
        padding-bottom: 3px;
        font-size: 0.96rem;
    }
    .card-body label.form-label {
        font-size: 1rem;
        margin-right: 8px;
    }
    @media (max-width: 991.98px) {
        .card-body .row.align-items-center.mb-2 {
            flex-direction: column !important;
            align-items: flex-start !important;
        }
        .card-body .col.d-flex.justify-content-end {
            justify-content: flex-start !important;
            margin-top: 10px;
        }
    }
    </style>

</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include 'layouts/menu.php'; ?>

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right"><!-- 
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="app-ecom-manager.php">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Extended UI</a></li>
                                        <li class="breadcrumb-item active">Scrollbar</li>
                                    </ol> -->
                                </div>
                                <h4 class="page-title">E-Commerce Manager</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <!-- Left: Apps Menu -->
                        <div class="col-12 col-md-3">
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-white fw-bold">Apps</div>
                                <div class="card-body">
                                    <div class="row row-cols-2 g-3">
                                        <div class="col">
                                            <a href="http://localhost/app.flyhubdigital.com/invoicing.php" class="text-decoration-none">
                                                <div class="card border-0 text-center p-3 h-100 shadow-sm">
                                                    <div class="mb-2 display-6 text-info"><i class="ri-bill-line"></i></div>
                                                    <div class="fw-semibold small">Invoicing</div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col">
                                            <a href="sales.php" class="text-decoration-none">
                                                <div class="card border-0 text-center p-3 h-100 shadow-sm">
                                                    <div class="mb-2 display-6 text-warning"><i class="ri-cash-line"></i></div>
                                                    <div class="fw-semibold small">Sales</div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col">
                                            <a href="products.php" class="text-decoration-none">
                                                <div class="card border-0 text-center p-3 h-100 shadow-sm">
                                                    <div class="mb-2 display-6 text-warning"><i class="ri-database-2-line"></i></div>
                                                    <div class="fw-semibold small">Inventory</div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col">
                                            <a href="pos.php" class="text-decoration-none">
                                                <div class="card border-0 text-center p-3 h-100 shadow-sm">
                                                    <div class="mb-2 display-6 text-info"><i class="ri-store-2-line"></i></div>
                                                    <div class="fw-semibold small">POS</div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Center: Main Content -->
                        <div class="col-12 col-md-6">                                        
                            <div class="card shadow-sm mb-4 overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=800&q=80" class="w-100" alt="E-commerce Bags" style="max-height:180px;object-fit:cover;">
                                <div class="card-img-overlay p-3 d-flex flex-column justify-content-end align-items-end">
                                </div>
                            </div>
                            <div class="card shadow-sm mb-4">
                                <div class="card-body">

                                        <div class="row align-items-center mb-2 flex-nowrap">
                                            <div class="col-auto d-flex align-items-center gap-2" style="flex-wrap:nowrap;">
                                                <label class="form-label mb-0 me-2">Filter:</label>
                                                <form method="get" class="d-flex align-items-center gap-2" style="margin-bottom:0;">
                                                    <select name="category" class="form-select form-select-sm" style="width: 145px;" onchange="this.form.submit()">
                                                        <option value="all">All Categories</option>
                                                        <?php foreach ($categories as $id => $catname): ?>
                                                        <option value="<?= htmlspecialchars($id) ?>" <?= ($category_filter == $id) ? 'selected' : '' ?>><?= htmlspecialchars($catname) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <select name="status" class="form-select form-select-sm" style="width: 130px;" onchange="this.form.submit()">
                                                        <option value="all">All Status</option>
                                                        <option value="active" <?= ($status_filter == 'active') ? 'selected' : '' ?>>Active</option>
                                                        <option value="draft" <?= ($status_filter == 'draft') ? 'selected' : '' ?>>Draft</option>
                                                        <option value="archived" <?= ($status_filter == 'archived') ? 'selected' : '' ?>>Archived</option>
                                                    </select>
                                                    <input type="hidden" name="page" value="<?= htmlspecialchars($_GET['page'] ?? '') ?>">
                                                </form>
                                            </div>
                                            <div class="col d-flex justify-content-end">
                                                <a href="products.php" class="btn btn-link fw-semibold px-2" style="white-space:nowrap;">View All Products</a>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table id="product-table" class="table table-bordered table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Description</th>
                                                        <th>Type</th>
                                                        <th>Price</th>
                                                        <th>Stock</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php if (mysqli_num_rows($result) > 0): ?>
                                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($row['name']) ?></td>
                                                            <td><?= htmlspecialchars($row['description']) ?></td>
                                                            <td><?= htmlspecialchars($row['product_type']) ?></td>
                                                            <td>₱<?= number_format($row['price'], 2) ?></td>
                                                            <td><?= htmlspecialchars($row['stock']) ?></td>
                                                            <td>
                                                                <span class="badge bg-<?= $row['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                                    <?= htmlspecialchars(ucfirst($row['status'])) ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted">No products found.</td>
                                                    </tr>
                                                <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                </div>
                            </div>
                        </div>

                        <!-- Right: Metrics -->
                        <div class="col-12 col-md-3">

                        </div>
                    </div><!-- end row g-4 -->

                </div> <!-- container -->

            </div> <!-- content -->

            <?php include 'layouts/footer.php'; ?>

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <?php include 'layouts/right-sidebar.php'; ?>
    <?php include 'layouts/footer-scripts.php'; ?>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

<script>
function printTable() {
    var printContents = document.getElementById('invoice-table').outerHTML;
    var win = window.open('', '', 'height=700,width=900');
    win.document.write('<html><head><title>Print Invoices</title>');
    win.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">');
    win.document.write('</head><body>');
    win.document.write('<h3>Invoices</h3>');
    win.document.write(printContents);
    win.document.write('</body></html>');
    win.document.close();
    win.focus();
    win.print();
    win.close();
}

function exportTableToCSV(filename) {
    var csv = [];
    var rows = document.querySelectorAll("#invoice-table tr");
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("th,td");
        for (var j = 0; j < cols.length; j++)
            row.push('"' + cols[j].innerText.replace(/"/g, '""') + '"');
        csv.push(row.join(","));
    }
    var csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
    var downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
}
</script>

</body>
</html>
