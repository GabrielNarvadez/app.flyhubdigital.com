<?php
require_once __DIR__ . '/layouts/config.php'; // adjust path as needed

// Fetch unit sales with unit and contact details
$sql = "
    SELECT 
        us.id,
        us.sale_date,
        us.sale_price,
        us.status AS sale_status,
        u.project_title,
        u.block,
        u.lot,
        c.first_name,
        c.last_name,
        c.phone_number
    FROM unit_sales us
    JOIN units u ON us.unit_id = u.id
    JOIN contacts c ON us.contact_id = c.id
    ORDER BY us.sale_date DESC
";

$result = $link->query($sql);

if (!$result) {
    echo "DB Query error: " . $link->error;
    exit;
}
?>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Sale ID</th>
            <th>Sale Date</th>
            <th>Sale Price</th>
            <th>Status</th>
            <th>Project</th>
            <th>Block</th>
            <th>Lot</th>
            <th>Buyer</th>
            <th>Phone</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['sale_date']) ?></td>
            <td>₱<?= number_format($row['sale_price'], 2) ?></td>
            <td><?= htmlspecialchars($row['sale_status']) ?></td>
            <td><?= htmlspecialchars($row['project_title']) ?></td>
            <td><?= htmlspecialchars($row['block']) ?></td>
            <td><?= htmlspecialchars($row['lot']) ?></td>
            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
            <td><?= htmlspecialchars($row['phone_number']) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
