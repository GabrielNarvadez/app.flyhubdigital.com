<?php
// Start session if not already started
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Database connection (adjust path as needed)
require_once __DIR__ . '/layouts/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Real Estate Management | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
</head>
<body>
    <!-- Begin page -->
    <div class="wrapper">
        <?php include 'layouts/menu.php'; ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <?php
                                // User welcome message
                                if (isset($_SESSION['user_id'])) {
                                    $userId = $_SESSION['user_id'];
                                    $sql = "SELECT name FROM users WHERE id = ?";
                                    $stmt = $link->prepare($sql);
                                    $stmt->bind_param("i", $userId);
                                    $stmt->execute();
                                    $stmt->bind_result($name);
                                    $stmt->fetch();
                                    $stmt->close();

                                    echo '<h4 class="page-title">Welcome, ' . htmlspecialchars($name) . '!</h4>';
                                } else {
                                    echo '<h4 class="page-title">Welcome, Guest!</h4>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Your Real Estate Modules Go Here -->
                    <div class="row">
                        <div class="col-12">
                            <?php include 'modules/real-estate/structure.php'; ?>
                        </div>
                    </div>

                </div> <!-- container -->
            </div> <!-- content -->

            <?php include 'layouts/footer.php'; ?>
        </div>
    </div>
    <!-- END wrapper -->

    <?php include 'layouts/right-sidebar.php'; ?>
    <?php include 'layouts/footer-scripts.php'; ?>
    <script src="assets/js/app.min.js"></script>
</body>
</html>
<?php
// Only close the connection here if you want; otherwise, let PHP handle it automatically.
// $link->close();
?>
