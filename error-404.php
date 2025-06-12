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
    <title>Error 404 | Attex - Bootstrap 5 Admin & Dashboard Template</title>
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
                                <h1 class="text-error">4<i class="ri-emotion-sad-line"></i>4</h1>
                                <h4 class="text-uppercase text-danger mt-3">Page Not Found</h4>
                                <p class="text-muted mt-3">It's looking like you may have taken a wrong turn. Don't worry... it
                                    happens to the best of us. Here's a
                                    little tip that might help you get back on track.</p>

                                <a class="btn btn-info mt-3" href="index.php"><i class="ri-home-4-line"></i> Back to Home</a>
                            </div>
                        </div> <!-- end card-body-->
                    </div>
                    <!-- end card -->
                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->

    <footer class="footer footer-alt fw-medium">
        <span class="bg-body">
            <script>
                document.write(new Date().getFullYear())
            </script> © Attex - Coderthemes.com
        </span>
    </footer>
    <?php include __DIR__ . '/includes/footer-scripts.php'; ?>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

</body>

</html>