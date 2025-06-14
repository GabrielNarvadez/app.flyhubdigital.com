<?php
require_once __DIR__ . '/../layouts/config.php';

// Start session if not active (usually in session.php)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Success & error messages from session (e.g. after adding a unit)
$success_msg = '';
if (!empty($_SESSION['success_message'])) {
    $success_msg = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
$error_msg = '';
if (!empty($_SESSION['error_message'])) {
    $error_msg = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Fetch units (non-archived)
$sql = "SELECT * FROM projects WHERE archived = 0 ORDER BY created_at DESC";
$result = mysqli_query($link, $sql);

// Fetch projects for Add Unit dropdown
$projects_for_dropdown = [];
$proj_res = mysqli_query($link, "SELECT DISTINCT id, project_title, project_site FROM projects WHERE archived = 0 ORDER BY project_title");
while ($pr = mysqli_fetch_assoc($proj_res)) {
    $projects_for_dropdown[] = $pr;
}

if (!$result) {
    echo '<div class="alert alert-danger">Error fetching units: ' . htmlspecialchars(mysqli_error($link)) . '</div>';
    return;
}
?>

<!-- Display success or error messages -->
<?php if ($success_msg): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($success_msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($error_msg): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($error_msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Units Grid -->
<div class="row g-4">
    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php $i = 0; while ($row = mysqli_fetch_assoc($result)): $i++;
            $total_price = $row['lot_area'] * $row['price_per_sqm'];
            $unitStatus = "Available"; // Adjust as needed if you have a status field
        ?>
        <div class="col-md-2">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="card-title mb-0"><?= htmlspecialchars($row['project_title']) ?></h5>
                        <span class="badge bg-success"><?= $unitStatus ?></span>
                    </div>

                    <p class="card-text mb-1"><strong>Site:</strong> <?= htmlspecialchars($row['project_site']) ?></p>
                    <p class="card-text mb-1"><strong>Phase:</strong> <?= htmlspecialchars($row['phase']) ?></p>

                    <p class="card-text mb-1"><strong>Class:</strong> <?= htmlspecialchars($row['lot_class']) ?></p>
              
                    <div class="row mb-1">
                      <div class="col">
                        <p class="card-text mb-0"><strong>Block:</strong> <?= htmlspecialchars($row['block']) ?></p>
                      </div>
                      <div class="col">
                        <p class="card-text mb-0"><strong>Lot:</strong> <?= htmlspecialchars($row['lot']) ?></p>
                      </div>
                    </div>

                    <p class="card-text mb-1"><strong>Lot Area:</strong> <?= htmlspecialchars($row['lot_area']) ?> sqm</p>
                    <p class="card-text mb-1"><strong>Price per sqm:</strong> ₱<?= number_format($row['price_per_sqm'], 2) ?></p>
                    <p class="card-text text-primary mb-3"><strong>Total Price:</strong> ₱<?= number_format($total_price, 2) ?></p>
                    
                    <button type="button" class="btn btn-primary mt-auto" data-bs-toggle="offcanvas" data-bs-target="#unitDetails<?= $i ?>" aria-controls="unitDetails<?= $i ?>">
                        View Details
                    </button>
                </div>
            </div>
        </div>

        <!-- Offcanvas Modal for Unit Details -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="unitDetails<?= $i ?>" aria-labelledby="unitDetailsLabel<?= $i ?>">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="unitDetailsLabel<?= $i ?>">Unit Details</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <dl class="row">
                    <dt class="col-sm-4">Project Title</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($row['project_title']) ?></dd>
                    <dt class="col-sm-4">Site</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($row['project_site']) ?></dd>
                    <dt class="col-sm-4">Phase</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($row['phase']) ?></dd>
                    <dt class="col-sm-4">Block</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($row['block']) ?></dd>
                    <dt class="col-sm-4">Lot</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($row['lot']) ?></dd>
                    <dt class="col-sm-4">Class</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($row['lot_class']) ?></dd>
                    <dt class="col-sm-4">Lot Area</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($row['lot_area']) ?> sqm</dd>
                    <dt class="col-sm-4">Price per sqm</dt>
                    <dd class="col-sm-8">₱<?= number_format($row['price_per_sqm'], 2) ?></dd>
                    <dt class="col-sm-4">Total Price</dt>
                    <dd class="col-sm-8">₱<?= number_format($total_price, 2) ?></dd>
                    <dt class="col-sm-4">Created At</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($row['created_at']) ?></dd>
                </dl>
            </div>
        </div>

        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info text-center">No units found.</div>
        </div>
    <?php endif; ?>
</div>
