<?php
// --- DB connection ($link) must be set before this include

$units = [];
$sql = "SELECT 
    id, 
    project_title, 
    project_site, 
    phase, 
    block, 
    lot, 
    lot_class, 
    lot_area, 
    price_per_sqm, 
    status 
    FROM units";
if ($result = mysqli_query($link, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $units[] = $row;
    }
    mysqli_free_result($result);
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container py-4">
    <h3 class="mb-4">Units</h3>
    <div class="row g-4">
        <?php foreach ($units as $u): 
            $project = isset($u['project_title']) ? $u['project_title'] : '';
            $site = isset($u['project_site']) ? $u['project_site'] : '';
            $phase = isset($u['phase']) ? $u['phase'] : '';
            $class = isset($u['lot_class']) ? $u['lot_class'] : '';
            $block = isset($u['block']) ? $u['block'] : '';
            $lot = isset($u['lot']) ? $u['lot'] : '';
            $area = isset($u['lot_area']) && $u['lot_area'] !== null ? $u['lot_area'] : 0;
            $ppsqm = isset($u['price_per_sqm']) && $u['price_per_sqm'] !== null ? $u['price_per_sqm'] : 0;
            $status = isset($u['status']) ? $u['status'] : '';
            $total_price = $area * $ppsqm;
        ?>
        <div class="col-sm-6 col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white fw-bold">
                    <?= htmlspecialchars($project) ?>
                </div>
                <div class="card-body">
                    <div><span class="badge bg-success"><?= htmlspecialchars($status) ?></span></div>
                    <div class="mt-2"><strong>Site:</strong> <?= htmlspecialchars($site) ?></div>
                    <div><strong>Phase:</strong> <?= htmlspecialchars($phase) ?></div>
                    <div><strong>Class:</strong> <?= htmlspecialchars($class) ?></div>
                    <div><strong>Block:</strong> <?= htmlspecialchars($block) ?></div>
                    <div><strong>Lot:</strong> <?= htmlspecialchars($lot) ?></div>
                    <div><strong>Lot Area:</strong> <?= htmlspecialchars($area) ?> sqm</div>
                    <div><strong>Price per sqm:</strong> ₱<?= number_format($ppsqm, 2) ?></div>
                    <div><strong>Total Price:</strong> ₱<?= number_format($total_price, 2) ?></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($units)): ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">No units found in the database.</div>
            </div>
        <?php endif; ?>
    </div>
</div>
