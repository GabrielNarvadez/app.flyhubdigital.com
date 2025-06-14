<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Dashboard | Flyhub Business Apps</title>
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
                                <?php
                                    // Start session if not already started
                                    if (session_status() !== PHP_SESSION_ACTIVE) {
                                        session_start();
                                    }

                                    // Make sure you have a database connection
                                    require_once __DIR__ . '/layouts/config.php';

                                    // Check if the user is logged in
                                    if (isset($_SESSION['user_id'])) {
                                        $userId = $_SESSION['user_id'];

                                        // Prepare and execute the query to fetch user's name
                                        $sql = "SELECT name FROM users WHERE id = ?";
                                        $stmt = $link->prepare($sql);
                                        $stmt->bind_param("i", $userId);
                                        $stmt->execute();
                                        $stmt->bind_result($name);
                                        $stmt->fetch();
                                        $stmt->close();

                                        // Display the welcome message
                                        echo '<h4 class="page-title">Welcome, ' . htmlspecialchars($name) . '!</h4>';
                                    } else {
                                        // Fallback if not logged in
                                        echo '<h4 class="page-title">Welcome, Guest!</h4>';
                                    }

                                    $link->close();
                                    ?>


                                
                                <?php include 'modules/dashboard/dashboard-structure.php'; ?>

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