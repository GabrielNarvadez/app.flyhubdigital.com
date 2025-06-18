<?php
require_once __DIR__ . '/layouts/config.php';

// Adjust table and field names if needed
$sql = "
    SELECT 
        us.id AS sale_id,
        us.status AS sale_status,
        us.sale_date,
        us.sale_price,
        us.monthly_payment,
        us.created_at AS sale_created,
        u.id AS unit_id,
        u.project_title,
        u.phase,
        u.block,
        u.lot,
        u.lot_class,
        u.lot_area,
        u.price_per_sqm,
        u.total_price,
        u.status AS unit_status,
        u.owner_contact_id,
        c.id AS contact_id,
        c.first_name,
        c.last_name,
        c.email,
        c.phone_number,
        c.city,
        p.id AS project_id,
        p.project_title AS project_name,
        p.location AS project_location
    FROM unit_sales us
    LEFT JOIN units u ON us.unit_id = u.id
    LEFT JOIN contacts c ON us.contact_id = c.id
    LEFT JOIN projects p ON u.project_id = p.id
    ORDER BY us.created_at DESC
";

$result = $link->query($sql);

if (!$result) {
    die("Database query failed: " . $link->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Raw Kanban Data Table</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        table { font-size: 13px; }
        th, td { white-space: nowrap; }
        .table-responsive { max-height: 75vh; overflow-y: auto; }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">All Unit Sales (Kanban Raw Data)</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Sale Status</th>
                    <th>Sale Date</th>
                    <th>Sale Price</th>
                    <th>Monthly Payment</th>
                    <th>Sale Created</th>
                    <th>Unit ID</th>
                    <th>Project Title</th>
                    <th>Phase</th>
                    <th>Block</th>
                    <th>Lot</th>
                    <th>Lot Class</th>
                    <th>Lot Area</th>
                    <th>Price/sqm</th>
                    <th>Total Price</th>
                    <th>Unit Status</th>
                    <th>Buyer ID</th>
                    <th>Buyer Name</th>
                    <th>Buyer Email</th>
                    <th>Buyer Phone</th>
                    <th>Buyer City</th>
                    <th>Project Name</th>
                    <th>Project Location</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): $rownum = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $rownum++ ?></td>
                        <td><?= htmlspecialchars($row['sale_status'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['sale_date'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['sale_price'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['monthly_payment'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['sale_created'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['unit_id'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['project_title'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['phase'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['block'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['lot'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['lot_class'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['lot_area'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['price_per_sqm'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['total_price'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['unit_status'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['contact_id'] ?? '') ?></td>
                        <td><?= htmlspecialchars(trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))) ?></td>
                        <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['phone_number'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['city'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['project_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['project_location'] ?? '') ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="23" class="text-center text-muted">No data found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
<?php
$result->free();
$link->close();
?>
