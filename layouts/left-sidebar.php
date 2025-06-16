<?php
require_once __DIR__ . '/../layouts/config.php';

// Set your actual logic for $tenant_id (e.g., from session)
$tenant_id = 1;

// Default to white logo
$default_white_logo = '/assets/images/flyhub-white-logo.png';
$logo_url = $default_white_logo;

// Try to fetch custom logo from tenants table
$sql = "SELECT logo_url FROM tenants WHERE id = ?";
$stmt = $link->prepare($sql);
$stmt->bind_param("i", $tenant_id);
$stmt->execute();
$stmt->bind_result($db_logo_url);
if ($stmt->fetch() && !empty($db_logo_url)) {
    $logo_url = $db_logo_url;
}
$stmt->close();
?>

<div class="leftside-menu" id="leftside-menu">
  <!-- LOGO always uses white version -->
  <div id="sidebarLogoContainer" style="height:60px;">
    <a href="index.php" class="logo logo-light">
      <span class="logo-lg">
        <img src="<?= htmlspecialchars($logo_url) ?>" alt="logo" style="height: 42px;" />
      </span>
      <span class="logo-sm">
        <img src="<?= htmlspecialchars($logo_url) ?>" alt="small logo" style="height: 42px;" />
      </span>
    </a>
    <!-- If you use a different logo for dark theme, change src here, otherwise use same -->
    <a href="index.php" class="logo logo-dark">
      <span class="logo-lg">
        <img src="<?= htmlspecialchars($logo_url) ?>" alt="logo" style="height: 42px;" />
      </span>
      <span class="logo-sm">
        <img src="<?= htmlspecialchars($logo_url) ?>" alt="small logo" style="height: 42px;" />
      </span>
    </a>
  </div>
  <div class="button-sm-hover" data-bs-toggle="tooltip" data-bs-placement="right" title="Show Full Sidebar" onclick="toggleSidebarLogo()">
    <i class="ri-checkbox-blank-circle-line align-middle"></i>
  </div>
  <div class="button-close-fullsidebar">
    <i class="ri-close-fill align-middle"></i>
  </div>
  <div class="h-100" id="leftside-menu-container" data-simplebar>
    <div class="leftbar-user">
      <a href="pages-profile.php">
        <img src="assets/images/users/avatar-1.jpg" alt="user-image" height="42" class="rounded-circle shadow-sm"/>
        <span class="leftbar-user-name mt-2">Tosha Minner</span>
      </a>
    </div>
    <ul class="side-nav">
      <li class="side-nav-item">
        <a href="index.php" class="side-nav-link">
          <i class="ri-dashboard-line"></i>
          <span> Dashboard </span>
        </a>
      </li>
      <li class="side-nav-item">
        <a href="contacts.php" class="side-nav-link">
          <i class="ri-user-line"></i>
          <span> Contacts </span>
        </a>
      </li>
      <li class="side-nav-item">
        <a href="apps-real-estate.php" class="side-nav-link">
          <i class="ri-building-line"></i>
          <span> Real Estate </span>
        </a>
      </li>
      <li class="side-nav-item">
        <a href="products.php" class="side-nav-link">
          <i class="ri-shopping-bag-line"></i>
          <span> Products </span>
        </a>
      </li>
      <li class="side-nav-item">
        <a href="apps.php" class="side-nav-link">
          <i class="ri-plug-line"></i>
          <span> Apps </span>
        </a>
      </li>
      <li class="side-nav-item">
        <a href="settings.php" class="side-nav-link">
          <i class="ri-settings-3-line"></i>
          <span> Settings </span>
        </a>
      </li>
    </ul>
    <div class="clearfix"></div>
  </div>
</div>

<script>
let sidebarLogoCollapsed = false;
function toggleSidebarLogo() {
    sidebarLogoCollapsed = !sidebarLogoCollapsed;
    var logoContainer = document.getElementById('sidebarLogoContainer');
    if (sidebarLogoCollapsed) {
        logoContainer.style.opacity = "0";
        logoContainer.style.pointerEvents = "none";
    } else {
        logoContainer.style.opacity = "1";
        logoContainer.style.pointerEvents = "";
    }
}
</script>
