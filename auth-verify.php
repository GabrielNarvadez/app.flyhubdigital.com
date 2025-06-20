<?php
require_once __DIR__ . '/layouts/config.php';

$verified = false;
$message = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Find user with this token
    $sql = "SELECT id FROM users WHERE verification_token = ? AND is_verified = 0 LIMIT 1";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, 's', $token);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) === 1) {
            // Token found! Verify user
            mysqli_stmt_bind_result($stmt, $user_id);
            mysqli_stmt_fetch($stmt);

            // Update to verified
            $sql_update = "UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?";
            if ($stmt_update = mysqli_prepare($link, $sql_update)) {
                mysqli_stmt_bind_param($stmt_update, 'i', $user_id);
                mysqli_stmt_execute($stmt_update);
                mysqli_stmt_close($stmt_update);
            }
            $verified = true;
            $message = "Your email has been verified! You can now log in.";
        } else {
            $message = "Invalid or expired verification link.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "Database error. Please try again.";
    }
} else {
    $message = "Missing verification token.";
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'layouts/main.php'; ?>
<head>
    <title>Email Verification | Flyhub Digital</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
</head>
<body class="authentication-bg position-relative">
<?php include 'layouts/background.php'; ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-xxl-4 col-lg-5">
            <div class="card">
                <div class="card-body p-4 text-center">
                    <?php if ($verified): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                        <a href="auth-login.php" class="btn btn-primary w-100">Go to Login</a>
                    <?php else: ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
                        <a href="auth-login.php" class="btn btn-outline-secondary w-100">Back to Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'layouts/footer-scripts.php'; ?>
<script src="assets/js/app.min.js"></script>
</body>
</html>
