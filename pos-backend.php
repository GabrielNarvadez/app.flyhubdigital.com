<?php
session_start();
require_once __DIR__ . '/layouts/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle filters
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$where = [];
$params = [];

if ($from) {
    $where[] = "s.sale_datetime >= ?";
    $params[] = $from . " 00:00:00";
}
if ($to) {
    $where[] = "s.sale_datetime <= ?";
    $params[] = $to . " 23:59:59";
}

// Get sales summary (1 row per sale, with customer, transaction #, item count, amount, date)
$sql = "
SELECT
    s.id AS transaction_number,
    s.sale_datetime,
    s.total,
    CONCAT_WS(' ', c.first_name, c.last_name) AS customer_name,
    COALESCE(SUM(si.quantity),0) AS items_count
FROM sales s
LEFT JOIN contacts c ON c.id = s.contact_id
LEFT JOIN sale_items si ON si.sale_id = s.id
" . ($where ? "WHERE " . implode(' AND ', $where) : "") . "
GROUP BY s.id
ORDER BY s.sale_datetime DESC
";
$stmt = $link->prepare($sql);
if ($params) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$sales = $result->fetch_all(MYSQLI_ASSOC);

// Total sales calculation
$total_sales = 0;
foreach ($sales as $row) $total_sales += $row['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sales Table | POS Backend</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    @media print {
      .noprint { display: none !important; }
    }
    .table td, .table th { vertical-align: middle !important; }
  </style>
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-primary fw-bold m-0">Sales Table</h4>
    <div class="d-flex align-items-center">
      <button onclick="window.print()" class="btn btn-outline-primary noprint me-2">
        <i class="bi bi-printer"></i> Print
      </button>
      <button class="btn btn-outline-danger noprint" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</button>
    </div>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <form class="row g-2 align-items-end" method="get" action="">
        <div class="col-auto">
          <label for="from" class="form-label mb-0">From</label>
          <input type="date" class="form-control" id="from" name="from" value="<?= htmlspecialchars($from) ?>">
        </div>
        <div class="col-auto">
          <label for="to" class="form-label mb-0">To</label>
          <input type="date" class="form-control" id="to" name="to" value="<?= htmlspecialchars($to) ?>">
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary"><i class="bi bi-filter"></i> Filter</button>
          <a href="?" class="btn btn-outline-secondary">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Customer Name</th>
              <th>Transaction #</th>
              <th>Number of Items</th>
              <th>Total Amount</th>
              <th>Date and Time</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($sales as $row): ?>
              <tr>
                <td>
                  <?php
                    $cust = trim($row['customer_name']);
                    echo $cust ? htmlspecialchars($cust) : '<span class="text-muted">Walk-in</span>';
                  ?>
                </td>
                <td><?= $row['transaction_number'] ?></td>
                <td><?= $row['items_count'] ?></td>
                <td class="text-end">₱<?= number_format($row['total'], 2) ?></td>
                <td><?= date('Y-m-d H:i', strtotime($row['sale_datetime'])) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($sales)): ?>
              <tr>
                <td colspan="5" class="text-center text-muted">No sales found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
          <tfoot>
            <tr class="fw-bold bg-light">
              <td colspan="3" class="text-end">Total Sales:</td>
              <td class="text-end">₱<?= number_format($total_sales, 2) ?></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutLabel">Confirm Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to log out?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <a href="pos.php" class="btn btn-danger">Logout</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
