<?php
session_start();
require_once __DIR__ . '/layouts/config.php';

$error = '';
$user_id = null;
$user_name = '';
$hash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Prepare and execute user lookup (for POS users)
    $sql = "SELECT id, name, password FROM users WHERE email = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $user_id, $user_name, $hash);
        $user_found = mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($user_found && password_verify($password, $hash)) {
            // Successful login for POS
            $_SESSION['user_id']   = $user_id;
            $_SESSION['name']      = $user_name;
            header('Location: pos-dashboard.php');
            exit;
        } else {
            $error = 'Invalid email or password';
        }
    } else {
        $error = 'Database error. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'layouts/main.php'; ?>
<head>
    <title>POS Login | Flyhub Digital</title>
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
                        <a href="pos-login.php">
                            <img src="assets/images/flyhub_logo.webp" alt="logo" height="50">
                        </a>
                    </div>

                    <div class="card-body p-4">

                        <div class="text-center w-75 m-auto">
                            <h4 class="text-dark-50 pb-0 fw-bold">POS Login</h4>
                            <p class="text-muted mb-4">Enter your credentials to access the Point of Sale system.</p>
                        </div>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="emailaddress" class="form-label">Email address</label>
                                <input name="email" type="email" class="form-control" id="emailaddress" required placeholder="Enter your email">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group input-group-merge">
                                    <input name="password" type="password" class="form-control" id="password" required placeholder="Enter your password">
                                    <div class="input-group-text" data-password="false">
                                        <span class="password-eye"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-0 text-center">
                                <button class="btn btn-primary" type="submit">Log In</button>
                            </div>
                        </form>
                    </div> <!-- end card-body -->
                </div> <!-- end card -->

                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <p class="text-muted"><a href="app-ecom-manager.php" class="text-decoration-underline"><b>Go back to admin</b></a></p>
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
