<?php
require_once __DIR__ . '/../layouts/config.php';

// --- Fetch customers for dropdown ---
$customers = [];
$cust_sql = "SELECT DISTINCT c.id, CONCAT(c.first_name, ' ', c.last_name) AS customer_name
             FROM contacts c
             JOIN soas s ON s.contact_id = c.id
             ORDER BY customer_name";
$cust_res = mysqli_query($link, $cust_sql);
while ($row = mysqli_fetch_assoc($cust_res)) {
    $customers[] = $row;
}

// --- Handle filters ---
$where = [];
$params = [];
$types = '';

if (!empty($_GET['customer_id'])) {
    $where[] = "s.contact_id = ?";
    $params[] = (int)$_GET['customer_id'];
    $types .= 'i';
}

// DATE RANGE DROPDOWN LOGIC
if (!empty($_GET['date_range'])) {
    $range = $_GET['date_range'];
    $today = date('Y-m-d');
    if ($range === 'today') {
        $where[] = "s.issue_date = ?";
        $params[] = $today;
        $types .= 's';
    } elseif ($range === 'week') {
        $monday = date('Y-m-d', strtotime('monday this week'));
        $sunday = date('Y-m-d', strtotime('sunday this week'));
        $where[] = "s.issue_date BETWEEN ? AND ?";
        $params[] = $monday;
        $params[] = $sunday;
        $types .= 'ss';
    } elseif ($range === 'quarter') {
        $currentMonth = date('n');
        $currentYear = date('Y');
        $quarter = floor(($currentMonth - 1) / 3) + 1;
        $startMonth = ($quarter - 1) * 3 + 1;
        $endMonth = $startMonth + 2;
        $startDate = date('Y-m-d', strtotime("$currentYear-$startMonth-01"));
        $endDate = date('Y-m-t', strtotime("$currentYear-$endMonth-01"));
        $where[] = "s.issue_date BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
        $types .= 'ss';
    } elseif ($range === 'year') {
        $startDate = date('Y-01-01');
        $endDate = date('Y-12-31');
        $where[] = "s.issue_date BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
        $types .= 'ss';
    }
}

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT 
            s.id,
            s.soa_number, 
            s.soa_name,
            s.issue_date,
            s.months,
            s.contract_price,
            s.monthly_amort,
            s.misc_fee,
            s.misc_fee_option,
            s.reservation,
            s.total_payable,
            s.net_selling_price,
            s.balance,
            s.total_paid,
            c.first_name,
            c.last_name,
            c.email,
            c.phone_number,
            p.project_title,
            p.project_site,
            u.phase,
            u.block,
            u.lot,
            u.lot_area,
            u.lot_class,
            u.price_per_sqm
        FROM soas s
        LEFT JOIN contacts c ON s.contact_id = c.id
        LEFT JOIN units u ON s.unit_id = u.id
        LEFT JOIN projects p ON u.project_id = p.id
        $where_sql
        ORDER BY s.id DESC";

// Prepare statement for procedural mysqli
$stmt = mysqli_prepare($link, $sql);
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$soa_list = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $soa_list[] = $row;
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="my-4">
    <div class="row">
        <!-- Left: Filter + Table -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title">All Statements of Accounts</h5>
                    <form method="get" id="filter-form">
                        <table class="table table-bordered table-hover align-middle" id="soa-table">
                            <thead>
                                <!-- Filter Row -->
                                <tr>
                                    <th></th>
                                    <th>
                                        <select class="form-select form-select-sm" name="customer_id" onchange="document.getElementById('filter-form').submit();">
                                            <option value="">All</option>
                                            <?php foreach ($customers as $c): ?>
                                                <option value="<?= $c['id'] ?>" <?= (isset($_GET['customer_id']) && $_GET['customer_id'] == $c['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($c['customer_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </th>
                                    <th>
                                        <select class="form-select form-select-sm" name="date_range" onchange="document.getElementById('filter-form').submit();">
                                            <option value="">All Dates</option>
                                            <option value="today" <?= (isset($_GET['date_range']) && $_GET['date_range'] == 'today') ? 'selected' : '' ?>>Today</option>
                                            <option value="week" <?= (isset($_GET['date_range']) && $_GET['date_range'] == 'week') ? 'selected' : '' ?>>This Week</option>
                                            <option value="quarter" <?= (isset($_GET['date_range']) && $_GET['date_range'] == 'quarter') ? 'selected' : '' ?>>This Quarter</option>
                                            <option value="year" <?= (isset($_GET['date_range']) && $_GET['date_range'] == 'year') ? 'selected' : '' ?>>This Year</option>
                                        </select>
                                    </th>
                                </tr>
                                <tr>
                                    <th>SOA Number</th>
                                    <th>Customer Name</th>
                                    <th>Issue Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($soa_list as $row): ?>
                                <tr>
                                    <td>
                                        <a href="#" class="soa-link text-primary fw-semibold"
                                            <?php foreach ($row as $k=>$v): ?>
                                                data-<?= $k ?>="<?= htmlspecialchars($v) ?>"
                                            <?php endforeach; ?>
                                        >
                                            <?= htmlspecialchars($row['soa_number']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                    <td><?= htmlspecialchars($row['issue_date']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (count($soa_list) == 0): ?>
                                <tr><td colspan="3" class="text-center">No records found.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right: Preview -->
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm" id="soa-preview">
                <div class="card-body">
                    <div class="d-flex justify-content-end gap-2 mb-2">
                        <button class="btn btn-outline-secondary btn-sm" id="btn-soa-print" style="display:none;"><i class="ri-printer-line"></i> Print to PDF</button>
                        <button class="btn btn-primary btn-sm" id="btn-soa-send-email" style="display:none;"><i class="ri-mail-send-line"></i> Send Email</button>
                    </div>
                    <h5 class="card-title">SOA Preview</h5>
                    <div id="preview-details" class="text-secondary">
                        <em>Select an SOA to preview details.</em>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function currency(n) {
    if (n === undefined || n === null || n === '') return '';
    n = parseFloat(n);
    return isNaN(n) ? '' : '₱' + n.toLocaleString('en-PH', {minimumFractionDigits:2});
}

// On preview: show buttons
$(document).on('click', '.soa-link', function(e) {
    e.preventDefault();
    const d = $(this).data();

    // Show buttons
    $('#btn-soa-print, #btn-soa-send-email').show();

    $('#preview-details').html(`
    <div class="clearfix mb-2">
        <div class="float-start"><img src="assets/images/logo-dark.png" alt="logo" height="28"></div>
        <div class="float-end"><h3 class="m-0">Statement of Account</h3></div>
    </div>

    <!-- Customer Details -->
    <div class="mb-3 soa-info-section">
        <h6>Customer Details</h6>
        <table class="table soa-info-table table-borderless w-100 mb-0">
            <tbody>
            <tr><th width="38%">Name:</th><td>${d.first_name || ''} ${d.last_name || ''}</td></tr>
            <tr><th>Contact Number:</th><td>${d.phone_number || ''}</td></tr>
            <tr><th>Email:</th><td>${d.email || ''}</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Property Details -->
    <div class="mb-3 soa-info-section">
        <h6>Property Details</h6>
        <table class="table soa-info-table table-borderless w-100 mb-0">
            <tbody>
            <tr><th>Project:</th><td>${d.project_title || ''}</td></tr>
            <tr><th>Site/Location:</th><td>${d.project_site || ''}</td></tr>
            <tr><th>Block / Lot / Phase:</th><td>Block ${d.block || ''}, Lot ${d.lot || ''}, Phase ${d.phase || ''}</td></tr>
            <tr><th>Area (sqm):</th><td>${d.lot_area || ''} sqm ${d.lot_class ? '('+d.lot_class+')' : ''}</td></tr>
            <tr><th>Price per sqm:</th><td>${currency(d.price_per_sqm)}</td></tr>
            </tbody>
        </table>
    </div>

    <!-- SOA / Account Summary -->
    <div class="mb-3 soa-info-section">
        <h6>SOA / Account Summary</h6>
        <table class="table soa-info-table table-borderless w-100 mb-0">
            <tbody>
            <tr><th>SOA #:</th><td>${d.soa_number || ''}</td></tr>
            <tr><th>SOA Name:</th><td>${d.soa_name || ''}</td></tr>
            <tr><th>Issue Date:</th><td>${d.issue_date || ''}</td></tr>
            <tr><th>Payment Terms:</th><td>${d.months || ''} months</td></tr>
            <tr><th>Total Contract Price:</th><td>${currency(d.contract_price)}</td></tr>
            <tr><th>Monthly Amortization:</th><td>${currency(d.monthly_amort)}</td></tr>
            <tr><th>Reservation Fee:</th><td>${currency(d.reservation)}</td></tr>
            <tr><th>Miscellaneous Fee:</th><td>${currency(d.misc_fee)}</td></tr>
            <tr><th>Misc Fee Option:</th><td>${d.misc_fee_option || ''}</td></tr>
            <tr><th>Total Amount Payable:</th><td>${currency(d.total_payable)}</td></tr>
            <tr><th>Net Selling Price:</th><td>${currency(d.net_selling_price)}</td></tr>
            <tr><th>Balance Payable:</th><td>${currency(d.balance)}</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Payment History -->
    <div class="mb-3 soa-info-section">
        <h6>Payment History</h6>
        <table class="table table-sm table-centered table-bordered mb-0">
            <thead class="border-top border-bottom bg-light-subtle border-light">
                <tr>
                    <th>#</th>
                    <th>Due Date</th>
                    <th>Amount Paid</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>${d.issue_date || ''}</td>
                    <td>${currency(d.total_paid)}</td>
                    <td><span class="text-success">${parseFloat(d.total_paid || 0) > 0 ? "Paid" : "Pending"}</span></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" class="text-end">Total Paid:</th>
                    <th>${currency(d.total_paid)}</th>
                    <th></th>
                </tr>
                <tr>
                    <th colspan="2" class="text-end">Balance:</th>
                    <th>${currency(d.balance)}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Notes -->
    <div class="soa-info-section mb-2">
        <h6>Notes</h6>
        <div class="alert alert-info mt-1 mb-2 p-2" style="font-size:14px">
            <b>Tip:</b> You can view all your SOAs and invoices in your portal. Need help? Contact our customer service.
        </div>
        <div><small class="text-muted">
            By signing below you certify that all information provided is true and accurate. Furthermore, you authorize
            Faith and Love Realty and Development Co. to verify all information with any source and to obtain such other
            information as may be required for the purpose of evaluating your application for a purchase of a lot/house/
            farm lot property. Reservation Fee is non-refundable.
        </small></div>
        <div class="row mt-2">
            <div class="col-4"><strong>Prepared by:</strong><br><br>__________________________</div>
            <div class="col-4"><strong>Approved by:</strong><br><br>__________________________</div>
            <div class="col-4"><strong>Received by:</strong><br><br>__________________________</div>
        </div>
    </div>
    `);
});

// Print to PDF (prints the preview area only)
$(document).on('click', '#btn-soa-print', function() {
    var printContents = document.getElementById('soa-preview').innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    window.location.reload();
});

// Send Email button placeholder
$(document).on('click', '#btn-soa-send-email', function() {
    alert('Send Email function will be implemented here.');
});
</script>
