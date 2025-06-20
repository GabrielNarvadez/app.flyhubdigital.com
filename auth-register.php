<?php
session_start();
require_once __DIR__ . '/layouts/config.php';   // must set $link = mysqli_connect(...)

$error   = '';
$name    = '';
$email   = '';
$success = '';

/* ------------------------------------------------------------------
   Handle the form submit
------------------------------------------------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name            = trim($_POST['name'] ?? '');
    $email_input     = trim($_POST['email'] ?? '');
    $email           = filter_var($email_input, FILTER_VALIDATE_EMAIL);
    $password        = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // basic validation
    if ($name === '' || !$email || $password === '' || $password_confirm === '') {
        $error = 'Please fill in every field';
    } elseif ($password !== $password_confirm) {
        $error = 'Passwords do not match';
    } else {

        /* ---------- check whether the e-mail is already used ---------- */
        $sql_check = 'SELECT 1 FROM users WHERE email = ? LIMIT 1';
        if ($stmt = mysqli_prepare($link, $sql_check)) {

            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                $error = 'That e-mail is already registered';
                mysqli_stmt_close($stmt);
            } else {
                mysqli_stmt_close($stmt);

                /* ---------- insert the new account ---------- */
                $hash      = password_hash($password, PASSWORD_DEFAULT);
                $sql_insert = 'INSERT INTO users (name, email, password) VALUES (?, ?, ?)';

                if ($ins = mysqli_prepare($link, $sql_insert)) {
                    mysqli_stmt_bind_param($ins, 'sss', $name, $email, $hash);

                    if (mysqli_stmt_execute($ins)) {

                        // Auto-log the user in, or redirect to login if you prefer
                        session_regenerate_id(true);
                        $_SESSION['user_id']   = mysqli_insert_id($link);
                        $_SESSION['user_name'] = $name;

                        header('Location: index.php');
                        exit;

                    } else {
                        $error = 'Could not create the account';
                    }
                    mysqli_stmt_close($ins);
                } else {
                    $error = 'Database error. Please try again.';
                }
            }
        } else {
            $error = 'Database error. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'layouts/main.php'; ?>
<head>
    <title>Sign Up | Flyhub Digital</title>
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

                    <div class="card-header py-4 text-center bg-primary">
                        <a href="index.php">
                            <img src="assets/images/flyhub_logo.webp" alt="logo" height="50">
                        </a>
                    </div>

                    <div class="card-body p-4">

                        <div class="text-center w-75 m-auto">
                            <h4 class="text-dark-50 pb-0 fw-bold">Create Account</h4>
                            <p class="text-muted mb-4">Enter your details to sign up.</p>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <form method="post" action="">

                            <div class="mb-3">
                                <label for="fullname" class="form-label">Full Name</label>
                                <input name="name" type="text" class="form-control" id="fullname"
                                       value="<?php echo htmlspecialchars($name); ?>" required placeholder="Enter your name">
                            </div>

                            <div class="mb-3">
                                <label for="emailaddress" class="form-label">Email address</label>
                                <input name="email" type="email" class="form-control" id="emailaddress"
                                       value="<?php echo htmlspecialchars($email); ?>" required placeholder="Enter your email">
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

                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirm Password</label>
                                <div class="input-group input-group-merge">
                                    <input name="password_confirm" type="password" class="form-control" id="password_confirm" required placeholder="Confirm your password">
                                    <div class="input-group-text" data-password="false">
                                        <span class="password-eye"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-0 text-center">
                                <button class="btn btn-primary" type="submit">Sign Up</button>
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
                            <a href="/app.flyhubdigital.com/auth/google-login.php" class="btn btn-lg w-100" style="background-color:#4285F4;color:#fff;">
                                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" width="22" class="me-2" style="position:relative;top:-2px;"> Sign up with Google
                            </a>
                        </div>

                    </div> <!-- end card-body -->
                </div> <!-- end card -->

                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <p class="text-muted">Already have an account? <a href="auth-login.php" class="text-decoration-underline"><b>Sign In</b></a></p>
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
