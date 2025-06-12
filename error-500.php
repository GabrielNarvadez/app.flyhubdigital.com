<?php
// index.php (project root)

// 1. App init
require __DIR__ . '/bootstrap.php';

// 2. Database connection
require __DIR__ . '/config/db.php';

// 3. Auth guard
if (empty($_SESSION['user'])) {
    header('Location: auth/login.php');
    exit;
}

$activePage = 'dashboard';


?>
    <head>
        <title>Error 500 | Attex - Bootstrap 5 Admin & Dashboard Template</title>
    <?php include __DIR__ . '/includes/title-meta.php'; ?>

    <?php include __DIR__ . '/includes/head-css.php'; ?>
    </head>

    <body class="authentication-bg">

    <?php include 'layouts/background.php'; ?>

        <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xxl-4 col-lg-5">
                        <div class="card">
                            <!-- Logo -->
                            <div class="card-header py-4 text-center bg-primary">
                                <a href="index.php">
                                    <span><img src="assets/images/logo.png" alt="logo" height="22"></span>
                                </a>
                            </div>
                            
                            <div class="card-body p-4">

                                <div class="text-center">
                                    <img src="assets/images/svg/startman.svg" height="120" alt="File not found Image">

                                    <h1 class="text-error mt-4">500</h1>
                                    <h4 class="text-uppercase text-danger mt-3">Internal Server Error</h4>
                                    <p class="text-muted mt-3">Why not try refreshing your page? or you can contact <a href="" class="text-muted"><b>Support</b></a></p>

                                    <a class="btn btn-info mt-3" href="index.php"><i class="ri-home-4-line me-1"></i> Return Home</a>
                                </div>

                            </div> <!-- end card-body-->
                        </div>
                        <!-- end card-->
                        
                    </div> <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end page -->

        <footer class="footer footer-alt fw-medium">
            <span class="bg-body"><script>document.write(new Date().getFullYear())</script> © Attex - Coderthemes.com</span>
        </footer>
        <?php include __DIR__ . '/includes/footer-scripts.php'; ?>
        
        <!-- App js -->
        <script src="assets/js/app.min.js"></script>

    </body>
</html>
