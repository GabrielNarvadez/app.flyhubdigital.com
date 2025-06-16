<?php
session_start();
require_once __DIR__ . '/layouts/config.php'; // Use your existing config

// --- LOGIN LOGIC ---
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $link->prepare("SELECT id, password, name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hash, $name);
        $stmt->fetch();
        if (password_verify($password, $hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['name'] = $name;
            header("Location: pos-dashboard.php"); // ✅ Redirect to dashboard
            exit;
        } else {
            $error = 'Invalid password.';
        }
    } else {
        $error = 'Email not found.';
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>POS Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-11 col-sm-8 col-md-5 col-lg-4">
        <div class="card shadow-sm rounded-4 p-4">
          <h4 class="mb-3 text-center fw-semibold">Login to POS</h4>
          <?php if ($error): ?>
            <div class="alert alert-danger small"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>
          <form method="POST" autocomplete="off">
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
