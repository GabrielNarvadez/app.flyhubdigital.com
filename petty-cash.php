<?php
require_once __DIR__ . '/layouts/config.php';

$tenant_id = 1; // Set this dynamically as needed

// --- Handle add replenish ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['replenish'])) {
    $sql = "INSERT INTO petty_cash (tenant_id, txn_date, amount, txn_type, description, requested_by, approved_by)
            VALUES (?, ?, ?, 'in', ?, ?, ?)";
    $stmt = $link->prepare($sql);
    $stmt->bind_param(
        "isdsss",
        $tenant_id,
        $_POST['txn_date'],
        $_POST['amount'],
        $_POST['description'],
        $_POST['requested_by'],
        $_POST['approved_by']
    );
    $stmt->execute();
    $stmt->close();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// --- Handle add expense/transaction ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transaction'])) {
    $sql = "INSERT INTO petty_cash (tenant_id, txn_date, amount, txn_type, description, requested_by, approved_by)
            VALUES (?, ?, ?, 'out', ?, ?, ?)";
    $stmt = $link->prepare($sql);
    $stmt->bind_param(
        "isdsss",
        $tenant_id,
        $_POST['txn_date'],
        $_POST['amount'],
        $_POST['description'],
        $_POST['requested_by'],
        $_POST['approved_by']
    );
    $stmt->execute();
    $stmt->close();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// --- Handle date filter ---
$filter_from = $_GET['from'] ?? '';
$filter_to   = $_GET['to'] ?? '';
$where = "tenant_id = ?";
$params = [$tenant_id];
$types = "i";
if ($filter_from) {
    $where .= " AND txn_date >= ?";
    $params[] = $filter_from;
    $types .= "s";
}
if ($filter_to) {
    $where .= " AND txn_date <= ?";
    $params[] = $filter_to;
    $types .= "s";
}
$sql = "SELECT * FROM petty_cash WHERE $where ORDER BY txn_date DESC, id DESC";
$stmt = $link->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

// Build rows array for balance calculation
$rows = [];
$balance = 0;
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $rows[] = $row;
    }
    // Moving total logic (most recent transaction first)
    $display_rows = array_reverse($rows); // For computing moving total top-to-bottom
} else {
    $display_rows = [];
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container py-5">
    <div class="row justify-content-center mb-4">
        <div class="col-lg-10 col-xl-9">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
                        <h3 class="fw-bold mb-0">Petty Cash Tracker</h3>
                        <!-- Date Filter Form -->
                        <form class="d-flex align-items-end gap-2" method="get">
                            <div>
                                <label class="form-label mb-0 small">From</label>
                                <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($filter_from) ?>">
                            </div>
                            <div>
                                <label class="form-label mb-0 small">To</label>
                                <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($filter_to) ?>">
                            </div>
                            <button class="btn btn-outline-primary mb-1" type="submit">Filter</button>
                            <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-outline-secondary mb-1">Reset</a>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Add Replenish/Transaction Forms -->
            <div class="row g-4 mb-4">
                <!-- Replenish Form -->
                <div class="col-md-6">
                    <div class="card shadow-sm rounded-4 border-0">
                        <div class="card-body p-4">
                            <h5 class="mb-3 fw-semibold">Replenish (Add Funds)</h5>
                            <form method="post" autocomplete="off">
                                <input type="hidden" name="replenish" value="1">
                                <div class="mb-2">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="txn_date" class="form-control" required value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Amount</label>
                                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Description</label>
                                    <input type="text" name="description" class="form-control" placeholder="e.g. Top-up, replenishment" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Requested By</label>
                                    <input type="text" name="requested_by" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Approved By</label>
                                    <input type="text" name="approved_by" class="form-control" required>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-success">Replenish</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Add Transaction Form -->
                <div class="col-md-6">
                    <div class="card shadow-sm rounded-4 border-0">
                        <div class="card-body p-4">
                            <h5 class="mb-3 fw-semibold">New Expense/Transaction</h5>
                            <form method="post" autocomplete="off">
                                <input type="hidden" name="transaction" value="1">
                                <div class="mb-2">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="txn_date" class="form-control" required value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Amount</label>
                                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Description</label>
                                    <input type="text" name="description" class="form-control" placeholder="e.g. Snacks, courier fee" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Requested By</label>
                                    <input type="text" name="requested_by" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Approved By</label>
                                    <input type="text" name="approved_by" class="form-control" required>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-danger">Add Expense</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Petty Cash Table -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex gap-2 mb-2 justify-content-end">
                        <button class="btn btn-outline-success btn-sm" onclick="exportTableToExcel('petty-cash-table')">Export to Excel</button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="printPDF()">Print PDF</button>
                    </div>
                    <h5 class="mb-3 fw-bold">Petty Cash Records<?= ($filter_from || $filter_to) ? " (Filtered)" : "" ?></h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0" id="petty-cash-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th>Requested By</th>
                                    <th>Approved By</th>
                                    <th>Running Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $running = 0;
                                if (!empty($display_rows)) :
                                    foreach ($display_rows as $row):
                                        $amount = ($row['txn_type'] == 'in') ? $row['amount'] : -$row['amount'];
                                        $running += $amount;
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['txn_date']) ?></td>
                                        <td>
                                            <?php if ($row['txn_type'] == 'in'): ?>
                                                <span class="badge bg-success">In</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Out</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($row['txn_type'] == 'in') {
                                                echo '<span class="text-success fw-semibold">+' . number_format($row['amount'], 2) . '</span>';
                                            } else {
                                                echo '<span class="text-danger fw-semibold">-' . number_format($row['amount'], 2) . '</span>';
                                            }
                                            ?>
                                        </td>
                                        <td><?= htmlspecialchars($row['description']) ?></td>
                                        <td><?= htmlspecialchars($row['requested_by']) ?></td>
                                        <td><?= htmlspecialchars($row['approved_by']) ?></td>
                                        <td class="fw-bold"><?= number_format($running, 2) ?></td>
                                    </tr>
                                <?php
                                    endforeach;
                                else:
                                ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No petty cash records found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportTableToExcel(tableID, filename = ''){
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

    filename = filename ? filename+'.xls' : 'petty-cash.xls';

    downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);

    if(navigator.msSaveOrOpenBlob){
        var blob = new Blob(['\ufeff', tableHTML], { type: dataType });
        navigator.msSaveOrOpenBlob( blob, filename);
    }else{
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
        downloadLink.download = filename;
        downloadLink.click();
    }
}

function printPDF() {
    var printContents = document.getElementById('petty-cash-table').outerHTML;
    var w = window.open('', '', 'height=700,width=1000');
    w.document.write('<html><head><title>Petty Cash Records</title>');
    w.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">');
    w.document.write('</head><body class="p-3">');
    w.document.write('<h3>Petty Cash Records</h3>');
    w.document.write(printContents);
    w.document.write('</body></html>');
    w.document.close();
    w.print();
}
</script>
