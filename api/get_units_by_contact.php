<?php
require_once __DIR__ . '/../layouts/config.php'; // Adjust path to your config file

header('Content-Type: application/json');

if (!isset($_GET['contact_id'])) {
    echo json_encode([]);
    exit;
}

$contact_id = intval($_GET['contact_id']);

$sql = "
    SELECT 
        u.id,
        u.project_title,
        u.block,
        u.lot,
        u.lot_area,
        COALESCE(us.monthly_payment, us.sale_price) AS monthly_payment,
        us.status
    FROM units u
    JOIN unit_sales us ON us.unit_id = u.id
    WHERE us.contact_id = ? AND us.status = 'Active'
    ORDER BY u.project_title, u.block, u.lot
";

$stmt = $link->prepare($sql);
$stmt->bind_param('i', $contact_id);
$stmt->execute();
$result = $stmt->get_result();

$units = [];
while ($row = $result->fetch_assoc()) {
    $units[] = $row;
}

echo json_encode($units);
