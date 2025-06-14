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
            CONCAT(c.first_name, ' ', c.last_name) AS customer_name, 
            s.issue_date 
        FROM soas s
        LEFT JOIN contacts c ON s.contact_id = c.id
        $where_sql
        ORDER BY s.id DESC";

// For procedural + prepared statements:
$stmt = mysqli_prepare($link, $sql);
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// For JS preview
$soa_list = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $soa_list[] = $row;
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<div class="my-4">
    <div class="row">
        <!-- Left: Filter + Table -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title">SOA List</h5>
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
                                           data-id="<?= $row['id'] ?>"
                                           data-soa_number="<?= htmlspecialchars($row['soa_number']) ?>"
                                           data-customer="<?= htmlspecialchars($row['customer_name']) ?>"
                                           data-issue_date="<?= htmlspecialchars($row['issue_date']) ?>"
                                        >
                                            <?= htmlspecialchars($row['soa_number']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
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
// Show SOA preview on click
$(document).on('click', '.soa-link', function(e) {
    e.preventDefault();
    const soa_number = $(this).data('soa_number');
    const customer = $(this).data('customer');
    const issue_date = $(this).data('issue_date');

    $('#preview-details').html(`
        <div class="mb-2"><span class="fw-bold">SOA Number:</span> ${soa_number}</div>
        <div class="mb-2"><span class="fw-bold">Customer:</span> ${customer}</div>
        <div class="mb-2"><span class="fw-bold">Issue Date:</span> ${issue_date}</div>
    `);
});
</script>
