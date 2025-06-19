<?php
require_once __DIR__ . '/../layouts/config.php';

// Set your actual logic for $tenant_id (e.g., from session)
$tenant_id = 1;

// Default to white logo
$default_white_logo = 'assets/images/flyhub-white-logo.png';
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

<style>
  #master-admin-bottom {
  position: absolute;
  bottom: 60px;  /* Space from bottom, adjust as needed */
  left: 0;
  width: 100%;
  z-index: 100;
  background: #2a3042; /* Match your sidebar bg color */
  border-top: 1px solid #222533;
  padding: 10px 0 8px 0;
}
#master-admin-bottom .side-nav-link {
  color: #fff;  /* Optional: ensure visible on dark bg */
  font-weight: 600;
}
#master-admin-bottom .side-nav-link i {
  font-size: 1.25rem;
}
</style>

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
  <a href="#realEstateMenu" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false">
    <i class="ri-home-8-line"></i>
    <span> Real Estate </span>
    <span class="menu-arrow"></span>
  </a>
  <ul class="side-nav-second-level collapse" id="realEstateMenu" style="padding-left:32px;">
    <li>
      <a href="real-estate-dashboard.php">
        <i class="ri-dashboard-2-line"></i>
        <span> Dashboard </span>
      </a>
    </li>
    <li>
      <a href="property-inventory.php">
        <i class="ri-building-2-line"></i>
        <span> Properties </span>
      </a>
    </li>
    <li>
      <a href="real-estate-crm.php">
        <i class="ri-contacts-book-line"></i>
        <span> CRM </span>
      </a>
    </li>
    <li>
      <a href="sales.php">
        <i class="ri-bill-line"></i>
        <span> Sales </span>
      </a>
    </li>
    <li>
      <a href="#">
        <i class="ri-folder-line"></i>
        <span> Documents </span>
      </a>
    </li>
    <li>
      <a href="#">
        <i class="ri-bar-chart-2-line"></i>
        <span> Reports </span>
      </a>
    </li>
    <li>
      <a href="#">
        <i class="ri-calendar-2-line"></i>
        <span> Notifications </span>
      </a>
    </li>
    <li>
      <a href="#">
        <i class="ri-shield-user-line"></i>
        <span> Users </span>
      </a>
    </li>
    <li>
      <a href="portal-dashboard.php">
        <i class="ri-door-open-line"></i>
        <span> Customer Portal </span>
      </a>
    </li>
  </ul>
</li>


      <li class="side-nav-item">
        <a href="#ecomSubmenu" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false">
          <i class="ri-store-2-line"></i>
          <span> E-Commerce </span>
          <span class="menu-arrow"></span>
        </a>
        <ul class="side-nav-second-level collapse" id="ecomSubmenu" style="padding-left:32px;">
          <li>
            <a href="ecom-dashboard.php">
              <i class="ri-shopping-bag-line"></i>
              <span> Dashboard </span>
            </a>
          </li>
          <li>
            <a href="products.php">
              <i class="ri-shopping-bag-line"></i>
              <span> Products </span>
            </a>
          </li>
          <li>
            <a href="ecom-invoicing.php">
              <i class="ri-bill-line"></i>
              <span> Orders </span>
            </a>
          </li>
          <li>
            <a href="POS.php" target="_blank">
              <i class="ri-terminal-box-line"></i>
              <span> Open POS </span>
            </a>
          </li>
        </ul>
      </li>

<li class="side-nav-item">
  <a href="#cateringSubmenu" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false">
    <i class="ri-restaurant-2-line"></i>
    <span> Catering </span>
    <span class="menu-arrow"></span>
  </a>
  <ul class="side-nav-second-level collapse" id="cateringSubmenu" style="padding-left:32px;">
    <li>
      <a href="catering-clients.php">
        <i class="ri-user-3-line"></i>
        <span> Clients & Accounts </span>
      </a>
    </li>
    <li>
      <a href="catering-orders.php">
        <i class="ri-calendar-check-line"></i>
        <span> Orders & Menu </span>
      </a>
    </li>
    <li>
      <a href="catering-billing.php">
        <i class="ri-bill-line"></i>
        <span> Billing & Receivables </span>
      </a>
    </li>
    <li>
      <a href="catering-reports.php">
        <i class="ri-bar-chart-box-line"></i>
        <span> Reports </span>
      </a>
    </li>
    <li>
      <a href="catering-users.php">
        <i class="ri-shield-user-line"></i>
        <span> User Management </span>
      </a>
    </li>
    <li>
      <a href="catering-delivery.php">
        <i class="ri-truck-line"></i>
        <span> Delivery & Schedule </span>
      </a>
    </li>
    <li>
      <a href="catering-docs.php">
        <i class="ri-file-list-3-line"></i>
        <span> Docs & Notes </span>
      </a>
    </li>
    <li>
      <a href="catering-notifications.php">
        <i class="ri-notification-2-line"></i>
        <span> Notifications </span>
      </a>
    </li>
  </ul>
</li>

<li class="side-nav-item">
  <a href="#fsmSubmenu" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false">
    <i class="ri-tools-line"></i>
    <span> Field Service </span>
    <span class="menu-arrow"></span>
  </a>
  <ul class="side-nav-second-level collapse" id="fsmSubmenu" style="padding-left:32px;">
    <li>
      <a href="fsm-dashboard.php">
        <i class="ri-dashboard-2-line"></i>
        <span> Dashboard </span>
      </a>
    </li>
    <li>
      <a href="fsm-clients.php">
        <i class="ri-group-line"></i>
        <span> Clients & Sites </span>
      </a>
    </li>
    <li>
      <a href="field-jobs.php">
        <i class="ri-calendar-check-line"></i>
        <span> Jobs </span>
      </a>
    </li>
    <li>
      <a href="fsm-team.php">
        <i class="ri-user-settings-line"></i>
        <span> Team & Users </span>
      </a>
    </li>
    <li>
      <a href="fsm-inventory.php">
        <i class="ri-archive-line"></i>
        <span> Inventory </span>
      </a>
    </li>
    <li>
      <a href="fsm-billing.php">
        <i class="ri-bill-line"></i>
        <span> Billing </span>
      </a>
    </li>
    <li>
      <a href="fsm-documents.php">
        <i class="ri-file-list-3-line"></i>
        <span> Documents </span>
      </a>
    </li>
    <li>
      <a href="fsm-reports.php">
        <i class="ri-bar-chart-2-line"></i>
        <span> Reports </span>
      </a>
    </li>
    <li>
      <a href="fsm-notifications.php">
        <i class="ri-notification-2-line"></i>
        <span> Notifications </span>
      </a>
    </li>
    <!-- Optional: Customer Portal (admin-only link) -->

    <li>
      <a href="fsm-portal.php">
        <i class="ri-door-open-line"></i>
        <span> Customer Portal </span>
      </a>
    </li>

  </ul>
</li>

<li class="side-nav-item">
  <a href="#recruitmentSubmenu" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false">
    <i class="ri-user-star-line"></i>
    <span> Recruitment </span>
    <span class="menu-arrow"></span>
  </a>
  <ul class="side-nav-second-level collapse" id="recruitmentSubmenu" style="padding-left:32px;">
    <li>
      <a href="ats-dashboard.php">
        <i class="ri-dashboard-2-line"></i>
        <span> Dashboard </span>
      </a>
    </li>
    <li>
      <a href="ats-jobs.php">
        <i class="ri-briefcase-4-line"></i>
        <span> Jobs </span>
      </a>
    </li>
    <li>
      <a href="ats-candidates.php">
        <i class="ri-user-search-line"></i>
        <span> Candidates </span>
      </a>
    </li>
    <li>
      <a href="ats-pipeline.php">
        <i class="ri-git-merge-line"></i>
        <span> Pipeline </span>
      </a>
    </li>
    <li>
      <a href="ats-calendar.php">
        <i class="ri-calendar-2-line"></i>
        <span> Calendar/Schedule </span>
      </a>
    </li>
    <li>
      <a href="ats-clients.php">
        <i class="ri-building-2-line"></i>
        <span> Clients & Contacts </span>
      </a>
    </li>
    <li>
      <a href="ats-documents.php">
        <i class="ri-file-list-3-line"></i>
        <span> Documents </span>
      </a>
    </li>
    <li>
      <a href="ats-reports.php">
        <i class="ri-bar-chart-2-line"></i>
        <span> Reports </span>
      </a>
    </li>
    <li>
      <a href="ats-users.php">
        <i class="ri-shield-user-line"></i>
        <span> Users/Settings </span>
      </a>
    </li>
  </ul>
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
        <!-- Pinned Master Admin Console at Bottom -->
      <div class="side-nav-item" id="master-admin-bottom">
        <a href="super-admin-dashboard.php" class="side-nav-link">
          <i class="ri-shield-star-line"></i>
          <span> Master Admin Console </span>
        </a>
      </div>

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
