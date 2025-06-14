<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Blank | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>

    <?php include 'layouts/head-css.php'; ?>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php
            require_once __DIR__ . '/layouts/config.php';

            // --- CREATE/UPDATE ---
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_room'])) {
                $id           = $_POST['room_id'] ?? null;
                $room_number  = $_POST['room_number'];
                $property_name= $_POST['property_name'];
                $type         = $_POST['type'];
                $description  = $_POST['description'];
                $status       = $_POST['status'];
                $price        = $_POST['price'];
                $floor        = $_POST['floor'];
                $capacity     = $_POST['capacity'];

                if (empty($id)) {
                    // Add
                    $stmt = $link->prepare("INSERT INTO rooms (room_number, property_name, type, description, status, price, floor, capacity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssssdii", $room_number, $property_name, $type, $description, $status, $price, $floor, $capacity);
                    $stmt->execute();
                    $stmt->close();
                    // Prevent form resubmission
                    header("Location: ".$_SERVER['PHP_SELF']);
                    exit;
                } else {
                    // Edit
                    $stmt = $link->prepare("UPDATE rooms SET room_number=?, property_name=?, type=?, description=?, status=?, price=?, floor=?, capacity=? WHERE id=?");
                    $stmt->bind_param("sssssdiii", $room_number, $property_name, $type, $description, $status, $price, $floor, $capacity, $id);
                    $stmt->execute();
                    $stmt->close();
                    // Prevent form resubmission
                    header("Location: ".$_SERVER['PHP_SELF']);
                    exit;
                }
            }

            // --- DELETE ---
            if (isset($_POST['delete_room'])) {
                $id = $_POST['delete_room'];
                $stmt = $link->prepare("DELETE FROM rooms WHERE id=?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
                // Prevent form resubmission
                header("Location: ".$_SERVER['PHP_SELF']);
                exit;
            }

            // --- GET ALL ROOMS ---
            $rooms = [];
            $result = $link->query("SELECT * FROM rooms ORDER BY id DESC");
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $rooms[] = $row;
                }
            }
            ?>

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
                                <h4 class="page-title">Rental Property Booking</h4>
                                
                                <?php include 'modules/rental/room-management.php'; ?>

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