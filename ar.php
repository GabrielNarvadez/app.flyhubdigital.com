<?php
require_once __DIR__ . '/layouts/config.php';

// Helper: Aging bucket
function get_aging_bucket($days) {
    if ($days <= 0) return "Current";
    if ($days <= 30) return "0-30 days";
    if ($days <= 60) return "31-60 days";
    if ($days <= 90) return "61-90 days";
    return ">90 days";
}

// ===== AJAX Handler for Modal Details =====
if (isset($_GET['ajax']) && $_GET['ajax'] === '1' && isset($_GET['id'])) {
    $invoice_id = intval($_GET['id']);

    // Fetch invoice details
    $stmt = $link->prepare("
        SELECT i.invoice_number, i.total, i.issue_date, i.due_date, i.status, c.first_name, c.last_name 
        FROM invoices i 
        LEFT JOIN contacts c ON i.contact_id = c.id 
        WHERE i.id=?
    ");
    $stmt->bind_param("i", $invoice_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $invoice = $res->fetch_assoc();
    $stmt->close();

    if (!$invoice) {
        echo '<div class="alert alert-danger">Invoice not found.</div>';
        exit;
    }

    // Fetch payments
    $pay_q = $link->prepare("SELECT payment_date, amount, payment_method, reference, notes FROM invoice_payments WHERE invoice_id=? ORDER BY payment_date DESC");
    $pay_q->bind_param("i", $invoice_id);
    $pay_q->execute();
    $pay_res = $pay_q->get_result();
    $payments = [];
    while ($row = $pay_res->fetch_assoc()) $payments[] = $row;
    $pay_q->close();

    // Fetch AR activity logs
    $logs = [];
    $log_q = $link->prepare("SELECT a.activity_type, a.activity_date, a.notes, u.name as user_name
        FROM ar_activity_log a
        LEFT JOIN users u ON a.user_id = u.id
        WHERE a.invoice_id = ?
        ORDER BY a.activity_date DESC
    ");
    $log_q->bind_param("i", $invoice_id);
    $log_q->execute();
    $log_res = $log_q->get_result();
    while ($row = $log_res->fetch_assoc()) $logs[] = $row;
    $log_q->close();
    ?>
    <!-- Invoice Summary -->
    <h6>Invoice Details</h6>
    <ul class="list-group mb-3">
        <li class="list-group-item"><strong>Invoice #:</strong> <?= htmlspecialchars($invoice['invoice_number']) ?></li>
        <li class="list-group-item"><strong>Customer:</strong> <?= htmlspecialchars($invoice['first_name'] . ' ' . $invoice['last_name']) ?></li>
        <li class="list-group-item"><strong>Total:</strong> <?= number_format($invoice['total'], 2) ?></li>
        <li class="list-group-item"><strong>Issue Date:</strong> <?= htmlspecialchars($invoice['issue_date']) ?></li>
        <li class="list-group-item"><strong>Due Date:</strong> <?= htmlspecialchars($invoice['due_date']) ?></li>
        <li class="list-group-item"><strong>Status:</strong> <?= ucfirst($invoice['status']) ?></li>
    </ul>

    <!-- Payments -->
    <h6>Payments</h6>
    <?php if (count($payments)): ?>
        <ul class="list-group mb-3">
            <?php foreach ($payments as $p): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($p['payment_date']) ?></strong> —
                    <?= number_format($p['amount'], 2) ?>
                    (<?= htmlspecialchars($p['payment_method']) ?>)
                    <?php if ($p['reference']): ?> | Ref: <?= htmlspecialchars($p['reference']) ?><?php endif; ?>
                    <?php if ($p['notes']): ?><br><em><?= htmlspecialchars($p['notes']) ?></em><?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="text-muted mb-3">No payments recorded.</div>
    <?php endif; ?>

    <!-- Activity Logs -->
    <h6>Activity Log</h6>
    <?php if (count($logs)): ?>
        <ul class="list-group mb-3">
            <?php foreach ($logs as $l): ?>
                <li class="list-group-item">
                    <span class="fw-semibold"><?= ucfirst($l['activity_type']) ?></span> 
                    (<?= htmlspecialchars($l['activity_date']) ?>) 
                    by <?= htmlspecialchars($l['user_name']) ?><br>
                    <span><?= nl2br(htmlspecialchars($l['notes'])) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="text-muted mb-3">No logs/notes yet.</div>
    <?php endif;
    exit;
}

// ============ Main Page ============

// Date filter handling
$filter = $_GET['range'] ?? 'all';
$where = [
    "i.status NOT IN ('paid','cancelled')"
];
if ($filter === 'today') {
    $where[] = "DATE(i.issue_date) = CURDATE()";
} elseif ($filter === 'week') {
    $where[] = "YEARWEEK(i.issue_date, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($filter === 'month') {
    $where[] = "YEAR(i.issue_date) = YEAR(CURDATE()) AND MONTH(i.issue_date) = MONTH(CURDATE())";
} elseif ($filter === 'year') {
    $where[] = "YEAR(i.issue_date) = YEAR(CURDATE())";
}
$where_sql = implode(' AND ', $where);

// Main query
$sql = "
SELECT 
    i.id,
    i.invoice_number,
    i.total AS invoice_amount,
    i.issue_date,
    i.due_date,
    i.status,
    c.first_name,
    c.last_name,
    COALESCE(SUM(p.amount), 0) AS total_paid,
    (i.total - COALESCE(SUM(p.amount), 0)) AS outstanding_balance,
    DATEDIFF(CURDATE(), i.due_date) AS days_past_due
FROM invoices i
LEFT JOIN contacts c ON i.contact_id = c.id
LEFT JOIN invoice_payments p ON p.invoice_id = i.id
WHERE $where_sql
GROUP BY i.id
ORDER BY i.due_date DESC
";
$res = $link->query($sql);
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.offcanvas-start {
    width: 500px !important;
}
</style>
<div class="container my-4">
    <h4>Accounts Receivable Tracker</h4>
    <!-- Filters -->
    <form class="mb-3" method="get">
        <div class="d-flex gap-2 align-items-center">
            <label class="me-2">Filter by:</label>
            <select name="range" class="form-select w-auto" onchange="this.form.submit()">
                <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>All</option>
                <option value="today" <?= $filter == 'today' ? 'selected' : '' ?>>Today</option>
                <option value="week" <?= $filter == 'week' ? 'selected' : '' ?>>This Week</option>
                <option value="month" <?= $filter == 'month' ? 'selected' : '' ?>>This Month</option>
                <option value="year" <?= $filter == 'year' ? 'selected' : '' ?>>This Year</option>
            </select>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Invoice #</th>
                    <th>Customer Name</th>
                    <th>Invoice Amount</th>
                    <th>Date Issued</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Outstanding Balance</th>
                    <th>Aging</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($res && $res->num_rows > 0): ?>
                    <?php while ($row = $res->fetch_assoc()): 
                        $customer = htmlspecialchars(trim($row['first_name'] . ' ' . $row['last_name']));
                        $aging = get_aging_bucket($row['days_past_due']);
                        $outstanding = $row['outstanding_balance'];
                        $invoice_id = $row['id'];
                    ?>
                    <tr>
                        <td>
                            <a href="#" 
                               class="text-primary fw-semibold view-ar-details"
                               data-invoice-id="<?= $invoice_id ?>"
                               data-bs-toggle="offcanvas"
                               data-bs-target="#arDetailModal"
                               aria-controls="arDetailModal">
                                <?= htmlspecialchars($row['invoice_number']) ?>
                            </a>
                        </td>
                        <td><?= $customer ?></td>
                        <td><?= number_format($row['invoice_amount'], 2) ?></td>
                        <td><?= htmlspecialchars($row['issue_date']) ?></td>
                        <td><?= htmlspecialchars($row['due_date']) ?></td>
                        <td>
                            <?php
                                $status = $row['status'];
                                $badge = 'secondary';
                                if ($status == 'unpaid') $badge = 'danger';
                                else if ($status == 'partial') $badge = 'warning';
                                else if ($status == 'overdue') $badge = 'dark';
                                else $badge = 'secondary';
                            ?>
                            <span class="badge bg-<?= $badge ?>"><?= ucfirst($status) ?></span>
                        </td>
                        <td><?= number_format($outstanding, 2) ?></td>
                        <td><?= $aging ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">No accounts receivable found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Offcanvas modal for AR details -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="arDetailModal" aria-labelledby="arDetailModalLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="arDetailModalLabel">AR Details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body" id="ar-details-body">
    <div class="text-center my-5 text-muted">Loading...</div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.view-ar-details').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var invoiceId = this.getAttribute('data-invoice-id');
            var modalBody = document.getElementById('ar-details-body');
            modalBody.innerHTML = '<div class="text-center my-5 text-muted">Loading...</div>';
            fetch(window.location.pathname + '?ajax=1&id=' + invoiceId)
                .then(resp => resp.text())
                .then(html => modalBody.innerHTML = html)
                .catch(() => modalBody.innerHTML = '<div class="text-danger">Failed to load details.</div>');
        });
    });
});
</script>
