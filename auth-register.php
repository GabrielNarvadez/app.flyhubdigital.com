<?php
session_start();
require_once __DIR__ . '/layouts/config.php';

$errors = [];
$success = '';
$first = $last = $email = $password = $company = '';
$terms = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first     = trim($_POST['firstname'] ?? '');
    $last      = trim($_POST['lastname'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $company   = trim($_POST['company'] ?? '');
    $terms     = isset($_POST['terms']);

    // Basic validation
    if ($first === '') $errors[] = 'First name is required.';
    if ($last === '') $errors[] = 'Last name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email address is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($company === '') $errors[] = 'Company name is required.';
    if (!$terms) $errors[] = 'You must accept the Terms and Conditions.';

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

    // Proceed to insert new tenant and user
    if (empty($errors)) {
        // 1. Create new tenant
        $sql = "INSERT INTO tenants (tenant_name) VALUES (?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, 's', $company);
            mysqli_stmt_execute($stmt);
            $tenant_id = mysqli_insert_id($link);
            mysqli_stmt_close($stmt);
        } else {
            $errors[] = 'Unable to create tenant. Please try again.';
        }
    }

    // 2. Create user, generate verification token, set is_verified=0
    if (empty($errors)) {
        $fullname = $first . ' ' . $last;
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $verification_token = bin2hex(random_bytes(16));
        $sql = "INSERT INTO users (name, email, password, tenant_id, is_verified, verification_token) VALUES (?, ?, ?, ?, 0, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, 'sssiss', $fullname, $email, $hash, $tenant_id, $verification_token);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // 3. Send email verification
            $verification_link = 'https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/auth-verify.php?token=' . $verification_token;
            $subject = 'Verify your email - Flyhub Digital';
            $message = "Hi $fullname,\n\nThank you for registering at Flyhub Digital.\nPlease click the link below to verify your email:\n\n$verification_link\n\nIf you did not register, please ignore this email.\n";
            $headers = "From: noreply@yourdomain.com\r\n";
            // @ for silent fail (replace with SMTP for production!)
            @mail($email, $subject, $message, $headers);

            $success = 'Registration successful! Please check your email to verify your account before logging in.';
            // Optionally, clear form fields
            $first = $last = $email = $password = $company = '';
            $terms = false;
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
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <?= htmlspecialchars($success) ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="" class="mb-3">
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
                                <label for="company" class="form-label">Company Name</label>
                                <input name="company" type="text" class="form-control" id="company" value="<?= htmlspecialchars($company ?? '') ?>" required placeholder="Enter your company or business name">
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
                                <input name="terms" type="checkbox" class="form-check-input" id="checkbox-signup" <?= $terms ? 'checked' : '' ?>>
                                <label class="form-check-label" for="checkbox-signup">
                                    I accept <a href="#" class="text-muted">Terms and Conditions</a>
                                </label>
                            </div>

                            <!-- Full width signup button -->
                            <div class="mb-3">
                                <button class="btn btn-primary btn-lg w-100" type="submit">Sign Up</button>
                            </div>
                        </form>

                        <!-- Divider with text -->
                        <div class="d-flex align-items-center my-3">
                            <hr class="flex-grow-1">
                            <span class="mx-2 text-muted">or</span>
                            <hr class="flex-grow-1">
                        </div>

                        <!-- Google Sign Up -->
                        <div class="mb-2">
                            <a href="auth-google-login.php" class="btn btn-lg w-100" style="background-color:#4285F4;color:#fff;">
                                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" width="22" class="me-2" style="position:relative;top:-2px;"> Sign up with Google
                            </a>
                        </div>

                        <!-- Microsoft Sign Up -->
                        <div class="mb-2">
                            <a href="auth-microsoft-login.php" class="btn btn-lg w-100" style="background-color:#000;color:#fff;">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/4/44/Microsoft_logo.svg" width="22" class="me-2" style="background:#fff;border-radius:2px;position:relative;top:-2px;"> Sign up with Microsoft
                            </a>
                        </div>

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
