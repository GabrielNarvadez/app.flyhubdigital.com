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

// --- FILTER HANDLING ---
$filter_range = $_GET['range'] ?? '';
$where = [];
$status_filter = $_GET['status'] ?? '';
$allowed_statuses = ['draft', 'sent', 'paid', 'void', 'canceled'];

if ($filter_range) {
    if ($filter_range === 'today') {
        $where[] = "DATE(i.issue_date) = CURDATE()";
    } elseif ($filter_range === 'week') {
        $where[] = "YEARWEEK(i.issue_date, 1) = YEARWEEK(CURDATE(), 1)";
    } elseif ($filter_range === 'month') {
        $where[] = "YEAR(i.issue_date) = YEAR(CURDATE()) AND MONTH(i.issue_date) = MONTH(CURDATE())";
    } elseif ($filter_range === 'year') {
        $where[] = "YEAR(i.issue_date) = YEAR(CURDATE())";
    }
}

if ($status_filter !== '' && $status_filter !== 'all' && in_array($status_filter, $allowed_statuses)) {
    $where[] = "i.status = '" . $status_filter . "'";
}

$whereSQL = $where ? "WHERE " . implode(' AND ', $where) : "";

// --- QUERY: Invoices ---
$sql = "SELECT 
            i.invoice_number,
            CONCAT(COALESCE(c.first_name, ''), ' ', COALESCE(c.last_name, '')) AS client_name,
            i.total,
            i.status,
            i.issue_date
        FROM invoices i
        LEFT JOIN contacts c ON i.contact_id = c.id
        $whereSQL
        ORDER BY i.created_at DESC";

$result = mysqli_query($link, $sql);

if (!$result) {
    echo '<div class="alert alert-danger">Failed to fetch invoices: ' . htmlspecialchars(mysqli_error($link)) . '</div>';
    return;
}

// --- STATUS ARRAYS ---
$statuses = [
    'all' => 'All Status',
    'draft' => 'Draft',
    'sent' => 'Sent',
    'paid' => 'Paid',
    'void' => 'Void',
    'canceled' => 'Canceled'
];

$statusBadgeClass = [
    'paid' => 'success',
    'sent' => 'primary',
    'draft' => 'secondary',
    'canceled' => 'danger',
    'void' => 'warning'
];

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
                                <h4 class="page-title">Blank Page</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <!-- Left: Apps Menu -->
                        <div class="col-12 col-md-3">
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-white fw-bold">Apps</div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <a href="http://localhost/app.flyhubdigital.com/invoicing.php" class="text-decoration-none">
                                                <div class="card border-0 text-center p-3 h-100 shadow-sm">
                                                    <div class="mb-2 display-6 text-info"><i class="ri-bill-line"></i></div>
                                                    <div class="fw-semibold small">Invoicing</div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-12">
                                            <a href="sales.php" class="text-decoration-none">
                                                <div class="card border-0 text-center p-3 h-100 shadow-sm">
                                                    <div class="mb-2 display-6 text-warning"><i class="ri-cash-line"></i></div>
                                                    <div class="fw-semibold small">Sales</div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-12">
                                            <a href="products.php" class="text-decoration-none">
                                                <div class="card border-0 text-center p-3 h-100 shadow-sm">
                                                    <div class="mb-2 display-6 text-warning"><i class="ri-database-2-line"></i></div>
                                                    <div class="fw-semibold small">Inventory</div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-12">
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
                                <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&amp;fit=crop&amp;w=800&amp;q=80" class="w-100" alt="Aerial Property View" style="max-height:180px;object-fit:cover;">
                                <div class="card-img-overlay p-3 d-flex flex-column justify-content-end align-items-end">
                                    <span class="badge bg-primary shadow">Featured Property</span>
                                </div>
                            </div>
                            <div class="card shadow-sm mb-4">
                                <div class="card-body">
                                    <div class="row align-items-end mb-3">
                                        <div class="col-md-6">
                                            <form method="get" class="row g-2">
                                                <div class="col-auto">
                                                    <label class="form-label mb-0">Filter:</label>
                                                </div>
                                                <div class="col-auto">
                                                    <select name="range" class="form-select form-select-sm" onchange="this.form.submit()">
                                                        <option value="">All Dates</option>
                                                        <option value="today" <?= ($filter_range == 'today') ? 'selected' : '' ?>>Today</option>
                                                        <option value="week" <?= ($filter_range == 'week') ? 'selected' : '' ?>>This Week</option>
                                                        <option value="month" <?= ($filter_range == 'month') ? 'selected' : '' ?>>This Month</option>
                                                        <option value="year" <?= ($filter_range == 'year') ? 'selected' : '' ?>>This Year</option>
                                                    </select>
                                                </div>
                                                <div class="col-auto">
                                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                        <?php foreach ($statuses as $key => $label): ?>
                                                            <option value="<?= htmlspecialchars($key) ?>" <?= ($status_filter === $key) ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <input type="hidden" name="page" value="<?= htmlspecialchars($_GET['page'] ?? '') ?>">
                                            </form>
                                        </div>
                                        <div class="col-md-6 d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="printTable()">Print</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportTableToCSV('invoices.csv')">Export</button>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="invoice-table" class="table table-bordered table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Invoice #</th>
                                                    <th>Client Name</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Invoice Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php if (mysqli_num_rows($result) > 0): ?>
                                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                    <?php 
                                                        $status = strtolower($row['status']);
                                                        $badgeClass = $statusBadgeClass[$status] ?? 'secondary';
                                                    ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($row['invoice_number']) ?></td>
                                                        <td><?= htmlspecialchars(trim($row['client_name']) ?: '—') ?></td>
                                                        <td><strong><?= number_format($row['total'], 2) ?></strong></td>
                                                        <td>
                                                            <span class="badge bg-<?= $badgeClass ?>">
                                                                <?= htmlspecialchars(ucfirst($status)) ?>
                                                            </span>
                                                        </td>
                                                        <td><?= htmlspecialchars($row['issue_date']) ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No invoices found.</td>
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
                            <div class="row mb-4 g-3">
                                <div class="col-12">
                                    <div class="card text-success border-success border shadow h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="display-6 me-3"><i class="ri-home-2-line"></i></div>
                                                <div>
                                                    <h6 class="mb-1 fw-bold">Units Sold</h6>
                                                    <div class="fs-3 fw-semibold"><?= number_format($units_sold) ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card text-primary border-primary border shadow h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="display-6 me-3"><i class="ri-currency-line"></i></div>
                                                <div>
                                                    <h6 class="mb-1 fw-bold">Revenue (YTD)</h6>
                                                    <div class="fs-3 fw-semibold">₱<?= number_format($revenue_ytd, 2) ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
