<?php\session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'layouts/main.php'; ?>
<head>
    <title>Logged Out | Flyhub Digital</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
</head>
<body class="authentication-bg position-relative">

<?php include 'layouts/background.php'; ?>

<div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-4 col-lg-5">
                <div class="card">

                    <!-- Logo -->
                    <div class="card-header py-4 text-center bg-primary">
                        <a href="index.php">
                            <img src="assets/images/flyhub_logo.webp" alt="logo" height="50">
                        </a>
                    </div>

                    <div class="card-body p-4 text-center">
                        <h4 class="text-dark-50 fw-bold">See You Again!</h4>
                        <p class="text-muted mb-4">You have successfully signed out.</p>
                        <a href="auth-login.php" class="btn btn-primary">Log In Again</a>
                    </div> <!-- end card-body -->
                </div> <!-- end card -->

                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <p class="text-muted">Back to <a href="auth-login.php" class="text-decoration-underline"><b>Log In</b></a></p>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- end container -->
</div> <!-- end page -->

<footer class="footer footer-alt fw-medium">
    <span>&copy; <script>document.write(new Date().getFullYear())</script> Flyhub Digital Inc.</span>
</footer>

<?php include 'layouts/footer-scripts.php'; ?>
<script src="assets/js/app.min.js"></script>
</body>
</html>
