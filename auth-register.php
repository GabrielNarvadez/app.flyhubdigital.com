<?php
session_start();

// Include DB config (using mysqli)
require_once __DIR__ . '/layouts/config.php';

$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first     = trim($_POST['firstname'] ?? '');
    $last      = trim($_POST['lastname'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $terms     = isset($_POST['terms']);

    // Basic validation
    if ($first === '') {
        $errors[] = 'First name is required.';
    }
    if ($last === '') {
        $errors[] = 'Last name is required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if (!$terms) {
        $errors[] = 'You must accept the Terms and Conditions.';
    }

    // Check for existing email
    if (empty($errors)) {
        $sql = "SELECT id FROM users WHERE email = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $errors[] = 'That email is already registered.';
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Insert new user
    if (empty($errors)) {
        $fullname = $first . ' ' . $last;
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, 'sss', $fullname, $email, $hash);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $_SESSION['flash'] = 'Registration successful! Please log in.';
            header('Location: auth-login.php');
            exit;
        } else {
            $errors[] = 'An unexpected error occurred. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'layouts/main.php'; ?>
<head>
    <title>Register | Flyhub Digital</title>
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

                    <div class="card-body p-4">
                        <div class="text-center w-75 m-auto">
                            <h4 class="text-dark-50 pb-0 fw-bold">Register a new user</h4>
                        </div>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $err): ?>
                                        <li><?= htmlspecialchars($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="firstname" class="form-label">First Name</label>
                                <input name="firstname" type="text" class="form-control" id="firstname" value="<?= htmlspecialchars($first ?? '') ?>" required placeholder="Enter your first name">
                            </div>

                            <div class="mb-3">
                                <label for="lastname" class="form-label">Last Name</label>
                                <input name="lastname" type="text" class="form-control" id="lastname" value="<?= htmlspecialchars($last ?? '') ?>" required placeholder="Enter your last name">
                            </div>

                            <div class="mb-3">
                                <label for="emailaddress" class="form-label">Email address</label>
                                <input name="email" type="email" class="form-control" id="emailaddress" value="<?= htmlspecialchars($email ?? '') ?>" required placeholder="Enter your email">
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

                            <div class="mb-3 form-check">
                                <input name="terms" type="checkbox" class="form-check-input" id="checkbox-signup" <?= isset($terms) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="checkbox-signup">I accept <a href="#" class="text-muted">Terms and Conditions</a></label>
                            </div>

                            <div class="mb-0 text-center">
                                <button class="btn btn-primary" type="submit">Sign Up</button>
                            </div>
                        </form>
                    </div> <!-- end card-body -->
                </div> <!-- end card -->

                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <p class="text-muted">Already have an account? <a href="auth-login.php" class="text-decoration-underline"><b>Log In</b></a></p>
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
