<?php
require_once __DIR__ . '/layouts/config.php';  // Always the first line

// --- AJAX: Create New Contact ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_contact') {
    $first = trim($_POST['first_name'] ?? '');
    $last  = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city  = trim($_POST['city'] ?? '');

    if (!$first || !$last) {
        echo json_encode(['success'=>false, 'msg'=>'First and last name required.']);
        exit;
    }

    $stmt = $link->prepare("INSERT INTO contacts (first_name, last_name, email, phone_number, city) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param('sssss', $first, $last, $email, $phone, $city);
        $ok = $stmt->execute();
        $new_id = $stmt->insert_id;
        $stmt->close();
        if ($ok) {
            echo json_encode([
                'success' => true,
                'id' => $new_id,
                'name' => $first . ' ' . $last,
                'email' => $email,
                'phone' => $phone
            ]);
        } else {
            echo json_encode(['success'=>false, 'msg'=>'Failed to create contact.']);
        }
    } else {
        echo json_encode(['success'=>false, 'msg'=>'DB error.']);
    }
    exit;
}

// Fetch contacts
$contacts = [];
$sql = "SELECT id, first_name, last_name, email, phone_number FROM contacts ORDER BY first_name, last_name";
if ($result = mysqli_query($link, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $contacts[] = $row;
    }
    mysqli_free_result($result);
}

// Fetch units
$units = [];
$sql = "SELECT id, project_title, project_site, block, lot, phase, lot_class, lot_area, price_per_sqm
        FROM units
        ORDER BY project_title, block, lot";
if ($result = mysqli_query($link, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $units[] = $row;
    }
    mysqli_free_result($result);
}
?>


<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>


<head>
    <title>Sales Management | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>


<style>
/* Force Select2 to match Bootstrap form-control height & style */
.select2-container .select2-selection--single {
    height: 38px !important;           /* Matches .form-control (default Bootstrap 5) */
    padding: 0.375rem 0.75rem !important;
    font-size: 1rem !important;
    line-height: 1.5 !important;
    border-radius: 0.375rem !important;
    border: 1px solid #ced4da !important;
    background: #fff !important;
    display: flex !important;
    align-items: center !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 1.5 !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
    color: #212529 !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
    right: 8px !important;
}
</style>


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
                                <h4 class="page-title">Sales Management</h4>
                                
                                <?php include 'modules/sales-editor.php'; ?>

                            </div>
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

    <?php include 'layouts/right-sidebar.php'; ?>

    <?php include 'layouts/footer-scripts.php'; ?>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

</body>

</html>