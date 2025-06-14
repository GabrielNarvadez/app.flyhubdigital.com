<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<?php
require_once __DIR__ . '/layouts/config.php';

// Start session if not already started (usually in session.php, but just in case)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Initialize success message
$success_msg = '';

// --- POST: Bulk Archive ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_archive']) && !empty($_POST['selected'])) {
    $ids = array_map('intval', $_POST['selected']);
    $in  = implode(',', $ids);
    if ($in) {
        mysqli_query($link, "UPDATE projects SET archived = 1 WHERE id IN ($in)");
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// --- POST: Add Unit ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_unit'])) {
    $project_id = intval($_POST['project_id']);
    $site = mysqli_real_escape_string($link, $_POST['site']);
    $phase = mysqli_real_escape_string($link, $_POST['phase']);
    $lot_class = mysqli_real_escape_string($link, $_POST['lot_class']);
    $block = mysqli_real_escape_string($link, $_POST['block']);
    $lot = mysqli_real_escape_string($link, $_POST['lot']);
    $lot_area = floatval($_POST['lot_area']);
    $price_per_sqm = floatval($_POST['price_per_sqm']);
    $created_at = date('Y-m-d H:i:s');

    // Insert new unit, clone project_title & project_site from selected project
    $insert_sql = "INSERT INTO projects (project_title, project_site, phase, lot_class, block, lot, lot_area, price_per_sqm, created_at)
                   SELECT project_title, project_site, '$phase', '$lot_class', '$block', '$lot', $lot_area, $price_per_sqm, '$created_at'
                   FROM projects WHERE id = $project_id LIMIT 1";

    if (mysqli_query($link, $insert_sql)) {
        // Set success message in session to show after redirect
        $_SESSION['success_message'] = 'New unit has been successfully added.';
    } else {
        // Optionally handle error here
        $_SESSION['error_message'] = 'Error adding new unit: ' . mysqli_error($link);
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Show success message if available and clear it after
if (!empty($_SESSION['success_message'])) {
    $success_msg = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Show error message if available and clear it after
$error_msg = '';
if (!empty($_SESSION['error_message'])) {
    $error_msg = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// --- Fetch units (non-archived) ---
$sql = "SELECT * FROM projects WHERE archived = 0 ORDER BY created_at DESC";
$result = mysqli_query($link, $sql);

// --- Fetch projects for Add Unit dropdown ---
$projects_for_dropdown = [];
$proj_res = mysqli_query($link, "SELECT DISTINCT id, project_title, project_site FROM projects WHERE archived = 0 ORDER BY project_title");
while ($pr = mysqli_fetch_assoc($proj_res)) {
    $projects_for_dropdown[] = $pr;
}

// --- Fetch distinct project titles for filter dropdown ---
$project_titles = [];
$proj_title_res = mysqli_query($link, "SELECT DISTINCT project_title FROM projects WHERE archived = 0 ORDER BY project_title");
while ($pt = mysqli_fetch_assoc($proj_title_res)) {
    $project_titles[] = $pt['project_title'];
}

if (!$result) {
    echo '<div class="alert alert-danger">Error fetching projects: ' . htmlspecialchars(mysqli_error($link)) . '</div>';
    return;
}
?>


<!-- The rest of your page content goes here, e.g. table, filters, forms -->


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Real Estate Management | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>

    <!-- Datatables CSS -->
    <link href="assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link href="assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" />
    <link href="assets/vendor/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css" rel="stylesheet" />
    <link href="assets/vendor/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css" rel="stylesheet" />
    <link href="assets/vendor/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" />
    <link href="assets/vendor/datatables.net-select-bs5/css/select.bootstrap5.min.css" rel="stylesheet" />

    <?php include 'layouts/head-css.php'; ?>
</head>

<body>
    <div class="wrapper">


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

        <?php include 'layouts/menu.php'; ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-left" style="margin-top: 30px;">
                                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                        <h3 class="mb-0">Units</h3>
                                        <div class="d-flex gap-2">
                                            <a href="estate-units.php" class="btn btn-outline-primary active">
                                                <i class="ri-list-unordered"></i>
                                            </a>
                                            <a href="products-kanban.php" class="btn btn-outline-primary">
                                                <i class="ri-grid-fill"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Include Units Table module -->
                    <?php include 'modules/units-table.php'; ?>

                </div> <!-- container -->
            </div> <!-- content -->

            <?php include 'layouts/footer.php'; ?>

        </div> <!-- content-page -->

    </div> <!-- wrapper -->

    <?php include 'layouts/right-sidebar.php'; ?>
    <?php include 'layouts/footer-scripts.php'; ?>

    <!-- Datatables JS -->
    <script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
    <script src="assets/vendor/datatables.net-fixedcolumns-bs5/js/fixedColumns.bootstrap5.min.js"></script>
    <script src="assets/vendor/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="assets/vendor/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="assets/vendor/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
    <script src="assets/vendor/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="assets/vendor/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="assets/vendor/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="assets/vendor/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="assets/vendor/datatables.net-select/js/dataTables.select.min.js"></script>

    <!-- Datatable Init (optional) -->
    <script src="assets/js/pages/demo.datatable-init.js"></script>

    <!-- App JS -->
    <script src="assets/js/app.min.js"></script>
</body>

</html>
