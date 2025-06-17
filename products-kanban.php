<?php
// === INIT ===
require_once __DIR__ . '/layouts/config.php';
// Do NOT call session_start() here; it's handled in layouts/session.php

// --- Success & Error Messages (from session) ---
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

// --- Handle Add Unit Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_unit'])) {
    $project_id   = intval($_POST['project_id']);
    $phase        = trim($_POST['phase']);
    $block        = trim($_POST['block']);
    $lot          = trim($_POST['lot']);
    $lot_class    = trim($_POST['lot_class']);
    $lot_area     = floatval($_POST['lot_area']);
    $price_per_sqm= floatval($_POST['price_per_sqm']);
    $total_price  = $lot_area * $price_per_sqm;
    $status       = 'Available';

    // Get project site based on project_id
    $site = '';
    foreach ($projects_for_dropdown as $proj) {
        if ($proj['id'] == $project_id) $site = $proj['project_site'];
    }

    $stmt = $link->prepare("INSERT INTO units (project_id, project_title, project_site, phase, block, lot, lot_class, lot_area, price_per_sqm, total_price, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $proj_title = '';
    foreach ($projects_for_dropdown as $proj) {
        if ($proj['id'] == $project_id) $proj_title = $proj['project_title'];
    }
    $stmt->bind_param('issssssddds', $project_id, $proj_title, $site, $phase, $block, $lot, $lot_class, $lot_area, $price_per_sqm, $total_price, $status);
    $ok = $stmt->execute();
    $stmt->close();

    if ($ok) {
        $_SESSION['success_message'] = "New unit added successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to add new unit.";
    }
    header("Location: products-kanban.php");
    exit;
}

function getStatusBadgeClass($status) {
    $s = strtolower(trim($status));
    if ($s == "available") return "bg-primary";
    if ($s == "reserved")  return "bg-warning text-dark";
    if ($s == "sold")      return "bg-success";
    return "bg-secondary";
}

// --- Fetch Units (all units, with normalized project title/site) ---
$sql = "SELECT 
    u.*, 
    p.project_title AS normalized_project_title, 
    p.project_site AS normalized_project_site
FROM units u
LEFT JOIN projects p ON u.project_id = p.id
ORDER BY u.created_at DESC";
$result = mysqli_query($link, $sql);

// --- Fetch Projects for dropdown (optional for future use) ---
$projects_for_dropdown = [];
$proj_res = mysqli_query($link, "SELECT DISTINCT id, project_title, project_site FROM projects ORDER BY project_title");
while ($pr = mysqli_fetch_assoc($proj_res)) {
    $projects_for_dropdown[] = $pr;
}

// --- Fetch unit owners for display (if needed) ---
$owner_names = [];
// Find all unique owner_contact_id from the units result
$owner_ids = [];
if ($result && mysqli_num_rows($result) > 0) {
    mysqli_data_seek($result, 0); // reset pointer if needed
    while ($row = mysqli_fetch_assoc($result)) {
        if (!empty($row['owner_contact_id'])) {
            $owner_ids[] = (int)$row['owner_contact_id'];
        }
    }
    // Remove duplicates
    $owner_ids = array_unique($owner_ids);
    // Fetch all at once if any owners exist
    if ($owner_ids) {
        $ids_str = implode(',', $owner_ids);
        $owners_res = mysqli_query($link, "SELECT id, name FROM contacts WHERE id IN ($ids_str)");
        while ($or = mysqli_fetch_assoc($owners_res)) {
            $owner_names[$or['id']] = $or['name'];
        }
    }
    // Re-run main result for display (we already fetched rows above)
    mysqli_data_seek($result, 0);
}
?>

<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Products Kanban | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include 'layouts/menu.php'; ?>

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-left" style="margin-top: 30px;">
                                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                        <h3 class="mb-0">Units</h3>
                                        <div class="d-flex gap-2 align-items-center">
                                            <!-- List View Button with Tooltip -->
                                            <a href="estate-units.php" class="btn btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Switch to List View">
                                                <i class="ri-list-unordered"></i>
                                            </a>
                                            <!-- Add New Property Button -->
                                            <button type="button" class="btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#addUnitOffcanvas">
                                                <i class="ri-add-line" style="font-size: 1.2rem; margin-right: 3px;"></i> Add New Property
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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

                    <?php if (!$result): ?>
                        <div class="alert alert-danger">Error fetching units: <?= htmlspecialchars(mysqli_error($link)) ?></div>
                    <?php else: ?>

                    <!-- FILTER ROW: Place right above the cards grid -->
                    <div class="row mb-4 gx-2">
                        <!-- Search Bar -->
                        <div class="col-lg-5 col-md-6 mb-2 mb-lg-0">
                            <input type="text" id="unitSearch" class="form-control" placeholder="Search units by keyword...">
                        </div>
                        <!-- Project Name Filter -->
                        <div class="col-lg-2 col-md-6 mb-2 mb-lg-0">
                            <select id="projectFilter" class="form-select">
                                <option value="">All Projects</option>
                                <?php foreach ($projects_for_dropdown as $proj): ?>
                                    <option value="<?= htmlspecialchars($proj['project_title']) ?>"><?= htmlspecialchars($proj['project_title']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Status Filter -->
                        <div class="col-lg-2 col-md-6 mb-2 mb-lg-0">
                            <select id="statusFilter" class="form-select">
                                <option value="">All Status</option>
                                <option value="Available">Available</option>
                                <option value="Reserved">Reserved</option>
                                <option value="Sold">Sold</option>
                            </select>
                        </div>
                        <!-- Lot Class Filter -->
                        <div class="col-lg-3 col-md-6">
                            <select id="classFilter" class="form-select">
                                <option value="">All Classes</option>
                                <option value="Inner Lot">Inner Lot</option>
                                <option value="Corner/End Lot">Corner/End Lot</option>
                                <option value="Prime Lot">Prime Lot</option>
                                <option value="Residential">Residential</option>
                            </select>
                        </div>
                    </div>

                        <!-- Units Grid -->
                        <div class="row g-4">
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $i = 0; while ($row = mysqli_fetch_assoc($result)): $i++;
                                    $lot_area = isset($row['lot_area']) && $row['lot_area'] !== null ? $row['lot_area'] : 0;
                                    $ppsqm = isset($row['price_per_sqm']) && $row['price_per_sqm'] !== null ? $row['price_per_sqm'] : 0;
                                    $total_price = $lot_area * $ppsqm;
                                    $unitStatus = isset($row['status']) ? $row['status'] : "Available";
                                    $owner_id = isset($row['owner_contact_id']) ? (int)$row['owner_contact_id'] : 0;
                                    $owner_name = ($owner_id && isset($owner_names[$owner_id])) ? $owner_names[$owner_id] : '';
                                ?>
                                <div class="col-md-2 unit-card"
                                  data-title="<?= htmlspecialchars($row['normalized_project_title'] ?? '') ?>"
                                  data-status="<?= htmlspecialchars($unitStatus) ?>"
                                  data-class="<?= htmlspecialchars($row['lot_class'] ?? '') ?>"
                                  data-keywords="<?= strtolower(
                                    htmlspecialchars($row['normalized_project_title'] ?? '') . ' ' .
                                    htmlspecialchars($row['normalized_project_site'] ?? '') . ' ' .
                                    htmlspecialchars($row['phase'] ?? '') . ' ' .
                                    htmlspecialchars($row['block'] ?? '') . ' ' .
                                    htmlspecialchars($row['lot'] ?? '') . ' ' .
                                    htmlspecialchars($row['lot_class'] ?? '')
                                  ) ?>"
                                >
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <h5 class="card-title mb-0"><?= htmlspecialchars($row['normalized_project_title'] ?? '') ?></h5>
                                                <span class="badge <?= getStatusBadgeClass($unitStatus) ?>">
                                                    <?= htmlspecialchars($unitStatus) ?>
                                                </span>
                                            </div>
                                            <p class="card-text mb-1"><strong>Site:</strong> <?= htmlspecialchars($row['normalized_project_site'] ?? '') ?></p>
                                            <p class="card-text mb-1"><strong>Phase:</strong> <?= htmlspecialchars($row['phase'] ?? '') ?></p>
                                            <p class="card-text mb-1"><strong>Class:</strong> <?= htmlspecialchars($row['lot_class'] ?? '') ?></p>
                                            <p class="card-text mb-0"><strong>Block:</strong> <?= htmlspecialchars($row['block'] ?? '') ?></p>
                                            <p class="card-text mb-0"><strong>Lot:</strong> <?= htmlspecialchars($row['lot'] ?? '') ?></p>
                                            <p class="card-text mb-1"><strong>Lot Area:</strong> <?= htmlspecialchars($lot_area) ?> sqm</p>
                                            <p class="card-text mb-1"><strong>Price per sqm:</strong> ₱<?= number_format($ppsqm, 2) ?></p>
                                            <p class="card-text text-primary mb-1"><strong>Total Price:</strong> ₱<?= number_format($total_price, 2) ?></p>
                                            <?php if ($owner_name): ?>
                                                <p class="card-text mb-1"><strong>Owner:</strong> <?= htmlspecialchars($owner_name) ?></p>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-primary mt-auto" data-bs-toggle="offcanvas" data-bs-target="#unitDetails<?= $i ?>" aria-controls="unitDetails<?= $i ?>">
                                                View Details
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Offcanvas Modal for Unit Details -->
                                <div class="offcanvas offcanvas-end" tabindex="-1" id="unitDetails<?= $i ?>" aria-labelledby="unitDetailsLabel<?= $i ?>">
                                    <div class="offcanvas-header">
                                        <h3 class="offcanvas-title" id="unitDetailsLabel<?= $i ?>">Unit Details</h3>
                                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                    </div>
                                    <div class="offcanvas-body">

                                    <form id="unitForm<?= $i ?>" method="post" action="units-edit.php" class="unit-edit-form">
                                        <input type="hidden" name="unit_id" value="<?= $row['id'] ?>">
                                        <div class="row mb-2">
                                            <label class="col-sm-4 col-form-label">Project Title</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="project_title" class="form-control-plaintext" value="<?= htmlspecialchars($row['normalized_project_title'] ?? '') ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label class="col-sm-4 col-form-label">Site</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="project_site" class="form-control-plaintext" value="<?= htmlspecialchars($row['normalized_project_site'] ?? '') ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label class="col-sm-4 col-form-label">Phase</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="phase" class="form-control-plaintext" value="<?= htmlspecialchars($row['phase'] ?? '') ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label class="col-sm-4 col-form-label">Block</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="block" class="form-control-plaintext" value="<?= htmlspecialchars($row['block'] ?? '') ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label class="col-sm-4 col-form-label">Lot</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="lot" class="form-control-plaintext" value="<?= htmlspecialchars($row['lot'] ?? '') ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label class="col-sm-4 col-form-label">Class</label>
                                            <div class="col-sm-8">
                                                <span class="class-plain" style="display:block;"><?= htmlspecialchars($row['lot_class'] ?? '') ?></span>
                                                <select name="lot_class" class="form-select d-none">
                                                    <option value="Inner Lot" <?= ($row['lot_class'] ?? '') == 'Inner Lot' ? 'selected' : '' ?>>Inner Lot</option>
                                                    <option value="Corner/End Lot" <?= ($row['lot_class'] ?? '') == 'Corner/End Lot' ? 'selected' : '' ?>>Corner/End Lot</option>
                                                    <option value="Prime Lot" <?= ($row['lot_class'] ?? '') == 'Prime Lot' ? 'selected' : '' ?>>Prime Lot</option>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if ($owner_name): ?>
                                        <div class="row mb-2">
                                            <label class="col-sm-4 col-form-label">Owner</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control-plaintext" value="<?= htmlspecialchars($owner_name) ?>" readonly>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <div class="row mb-2">
                                            <label class="col-sm-4 col-form-label">Created At</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control-plaintext" value="<?= htmlspecialchars($row['created_at'] ?? '') ?>" readonly>
                                            </div>
                                        </div>
                                        <!-- Add Save button, hidden by default -->
                                        <div class="row">
                                            <div class="col-sm-12 text-end">
                                                <button type="submit" class="btn btn-success btn-sm d-none" id="saveBtn<?= $i ?>">Save</button>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="d-flex gap-2 mt-3">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="enableEdit('unitForm<?= $i ?>', 'saveBtn<?= $i ?>')" <?= ($unitStatus == 'Sold') ? 'disabled' : '' ?>>Edit</button>
                                    <?php if ($unitStatus == 'Available'): ?>
                                        <form method="post" action="units-delete.php" style="display:inline;">
                                            <input type="hidden" name="unit_id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                    </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <div class="alert alert-info text-center">No units found.</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Add New Property Offcanvas -->
                    <div class="offcanvas offcanvas-end" tabindex="-1" id="addUnitOffcanvas" aria-labelledby="addUnitOffcanvasLabel">
                      <div class="offcanvas-header">
                        <h3 class="offcanvas-title" id="addUnitOffcanvasLabel">Add New Property</h3>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
                      </div>
                      <div class="offcanvas-body">
                        <form id="addUnitForm" method="post" action="products-kanban.php" autocomplete="off">
                          <input type="hidden" name="add_unit" value="1">

                          <!-- Project Name Dropdown -->
                          <div class="mb-3">
                            <label class="form-label">Project Name</label>
                            <select class="form-select" name="project_id" id="add_project_id" required>
                              <option value="">Select Project</option>
                              <?php foreach ($projects_for_dropdown as $proj): ?>
                                <option value="<?= $proj['id'] ?>" data-site="<?= htmlspecialchars($proj['project_site']) ?>">
                                  <?= htmlspecialchars($proj['project_title']) ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </div>
                          <!-- Site (auto-filled) -->
                          <div class="mb-3">
                            <label class="form-label">Site</label>
                            <input type="text" class="form-control" id="add_project_site" name="project_site" readonly required>
                          </div>

                          <!-- Phase, Block, Lot on One Row -->
                          <div class="row mb-3">
                            <div class="col">
                              <label class="form-label">Phase</label>
                              <input type="text" class="form-control" name="phase" required>
                            </div>
                            <div class="col">
                              <label class="form-label">Block</label>
                              <input type="text" class="form-control" name="block" required>
                            </div>
                            <div class="col">
                              <label class="form-label">Lot</label>
                              <input type="text" class="form-control" name="lot" required>
                            </div>
                          </div>
                          <!-- Lot Class Dropdown -->
                          <div class="mb-3">
                            <label class="form-label">Lot Class</label>
                            <select class="form-select" name="lot_class" required>
                              <option value="">Select Class</option>
                              <option value="Inner Lot">Inner Lot</option>
                              <option value="Corner/End Lot">Corner/End Lot</option>
                              <option value="Prime Lot">Prime Lot</option>
                            </select>
                          </div>
                          <!-- Lot Area & Price per sqm on Row -->
                          <div class="row mb-3">
                            <div class="col">
                              <label class="form-label">Lot Area (sqm)</label>
                              <input type="number" class="form-control" name="lot_area" id="add_lot_area" min="0" step="0.01" required>
                            </div>
                            <div class="col">
                              <label class="form-label">Price per sqm</label>
                              <input type="number" class="form-control" name="price_per_sqm" id="add_price_per_sqm" min="0" step="0.01" required>
                            </div>
                          </div>
                          <!-- Total Contract Price (auto, readonly) -->
                          <div class="mb-3">
                            <label class="form-label">Total Contract Price</label>
                            <input type="text" class="form-control" name="total_price" id="add_total_price" readonly>
                          </div>
                          <div class="text-end mt-3">
                            <button type="submit" class="btn btn-success">Save</button>
                          </div>
                        </form>
                      </div>
                    </div>
                </div> <!-- container -->

            </div> <!-- content -->

            <?php include 'layouts/footer.php'; ?>

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

<script>
function enableEdit(formId, saveBtnId) {
    var form = document.getElementById(formId);
    var saveBtn = document.getElementById(saveBtnId);
    if (!form) return;
    // Enable text inputs except Created At
    Array.from(form.querySelectorAll('input')).forEach(function(input) {
        if (
            input.name !== 'unit_id' &&
            input.name !== '' &&
            input.parentElement.previousElementSibling &&
            input.parentElement.previousElementSibling.innerText.trim() !== "Created At"
        ) {
            input.removeAttribute('readonly');
            input.classList.remove('form-control-plaintext');
            input.classList.add('form-control');
        }
    });
    // Show the dropdown, hide the plain text for Class
    var select = form.querySelector('select[name="lot_class"]');
    var span = form.querySelector('.class-plain');
    if (select) select.classList.remove('d-none');
    if (span) span.style.display = 'none';
    if (select) select.removeAttribute('disabled');
    if (saveBtn) saveBtn.classList.remove('d-none');

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl)
  })
}

// Autofill Site field when selecting a project
document.getElementById('add_project_id').addEventListener('change', function() {
  var site = this.selectedOptions[0].getAttribute('data-site') || '';
  document.getElementById('add_project_site').value = site;
});

// Auto-calculate Total Contract Price
function calcTotalPrice() {
  var lot_area = parseFloat(document.getElementById('add_lot_area').value) || 0;
  var price_per_sqm = parseFloat(document.getElementById('add_price_per_sqm').value) || 0;
  var total = lot_area * price_per_sqm;
  document.getElementById('add_total_price').value = total > 0 ? "₱" + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '';
}
document.getElementById('add_lot_area').addEventListener('input', calcTotalPrice);
document.getElementById('add_price_per_sqm').addEventListener('input', calcTotalPrice);

// Filter function
function filterUnits() {
  let search = document.getElementById('unitSearch').value.trim().toLowerCase();
    let proj   = document.getElementById('projectFilter').value;
    let stat   = document.getElementById('statusFilter').value;
    let klass  = document.getElementById('classFilter').value;

  let cards = document.querySelectorAll('.unit-card');
  let anyVisible = false;

  cards.forEach(card => {
    let title = card.getAttribute('data-title') || '';
    let status = card.getAttribute('data-status') || '';
    let lotClass = card.getAttribute('data-class') || '';
    let keywords = card.getAttribute('data-keywords') || '';

    // Filter logic
    let match = true;
    if (search && !keywords.includes(search)) match = false;
    if (proj && title !== proj) match = false;
    if (stat && status !== stat) match = false;
    if (klass && lotClass !== klass) match = false;

    card.style.display = match ? '' : 'none';
    if (match) anyVisible = true;
  });

  // Show "no results" if needed
  let noRes = document.getElementById('noUnitsMsg');
  if (noRes) noRes.style.display = anyVisible ? 'none' : '';
}

// Bind filters
['unitSearch','projectFilter','statusFilter','classFilter'].forEach(id => {
  document.getElementById(id).addEventListener('input', filterUnits);
  document.getElementById(id).addEventListener('change', filterUnits);
});
</script>

    <?php include 'layouts/right-sidebar.php'; ?>

    <?php include 'layouts/footer-scripts.php'; ?>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

</body>
</html>
