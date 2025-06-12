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

// 4. Simple page router
$allowed = ['dashboard','contacts','companies','products','invoices','customer-portal','inventory','pos','apps','settings'];
$page    = $_GET['page'] ?? 'dashboard';
if (! in_array($page, $allowed, true)) {
    $page = 'dashboard';
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Flyhub CRM-ERP Lite</title>

  <!-- Envato Bootstrap theme CSS -->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <!-- Your custom styles -->
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
  <!-- 5. Header -->
  <?php include __DIR__ . '/includes/header.php'; ?>

  <div class="container-fluid">
    <div class="row">
      <!-- 6. Sidebar (with active highlight) -->
      <?php 
        // let sidebar know which page is active
        $activePage = $page;
        include __DIR__ . '/includes/sidebar.php'; 
      ?>

      <!-- 7. Main content -->
      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <?php
          $modulePath = __DIR__ . "/modules/{$page}/index.php";
          if (file_exists($modulePath)) {
              include $modulePath;
          } else {
              echo "<h1>Page not found</h1>";
          }
        ?>
      </main>
    </div>
  </div>

  <!-- 8. Footer -->
  <?php include __DIR__ . '/includes/footer.php'; ?>

  <!-- Bootstrap JS (bundle includes Popper) -->
  <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
