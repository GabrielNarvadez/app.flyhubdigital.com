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
  bottom: 60px;
  left: 0;
  width: 100%;
  z-index: 100;
  background: #2a3042;
  border-top: 1px solid #222533;
  padding: 10px 0 8px 0;
}
#master-admin-bottom .side-nav-link {
  color: #fff;
  font-weight: 600;
}
#master-admin-bottom .side-nav-link i {
  font-size: 1.25rem;
}

#sidebar-flyout {
  display: none;
  position: fixed;
  background: #242837;
  box-shadow: 2px 0 12px #0002;
  z-index: 1100;
  border-radius: 8px;
  border-left: 1px solid #232533;
  min-width: 220px;
  max-width: 260px;
  /* Only as tall as content */
  height: auto;
  overflow-y: visible;
  padding: 0;
  animation: fadeInSidebar .18s;
}
@keyframes fadeInSidebar {
  from { opacity: 0; transform: translateX(-10px);}
  to   { opacity: 1; transform: translateX(0);}
}
.side-flyout-link {
  color: #fff;
  padding: 10px 18px;
  border-radius: 6px;
  transition: background .15s;
  font-weight: 500;
  text-decoration: none;
  display: flex;
  align-items: center;
  min-width: 170px;
  font-size: 15px;
}
.side-flyout-link:hover {
  background: #323551;
  color: #fff;
  text-decoration: none;
}
.side-nav-link {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 4px;
}
.chevron {
  margin-left: 8px;
  font-size: 1.08em;
  opacity: 0.66;
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
    <a href="index.php" class="logo logo-dark">
      <span class="logo-lg">
        <img src="<?= htmlspecialchars($logo_url) ?>" alt="logo" style="height: 42px;" />
      </span>
      <span class="logo-sm">
        <img src="<?= htmlspecialchars($logo_url) ?>" alt="small logo" style="height: 42px;" />
      </span>
    </a>
  </div>
  <div class="button-sm-hover" onclick="toggleSidebarLogo()">
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
      <!-- Parent Menus with flyout (show chevron) -->
      <li class="side-nav-item">
        <a href="#" class="side-nav-link" data-flyout="crm">
          <span><i class="ri-user-3-line"></i> <span>CRM</span></span>
          <span class="chevron">&gt;</span>
        </a>
      </li>
      <li class="side-nav-item">
        <a href="#" class="side-nav-link" data-flyout="realestate">
          <span><i class="ri-home-8-line"></i> <span>Real Estate</span></span>
          <span class="chevron">&gt;</span>
        </a>
      </li>
      <li class="side-nav-item">
        <a href="#" class="side-nav-link" data-flyout="ecommerce">
          <span><i class="ri-store-2-line"></i> <span>E-Commerce</span></span>
          <span class="chevron">&gt;</span>
        </a>
      </li>
      <li class="side-nav-item">
        <a href="#" class="side-nav-link" data-flyout="catering">
          <span><i class="ri-restaurant-2-line"></i> <span>Catering</span></span>
          <span class="chevron">&gt;</span>
        </a>
      </li>
      <li class="side-nav-item">
        <a href="#" class="side-nav-link" data-flyout="fieldservice">
          <span><i class="ri-tools-line"></i> <span>Field Service</span></span>
          <span class="chevron">&gt;</span>
        </a>
      </li>
      <li class="side-nav-item">
        <a href="#" class="side-nav-link" data-flyout="recruitment">
          <span><i class="ri-user-star-line"></i> <span>Recruitment</span></span>
          <span class="chevron">&gt;</span>
        </a>
      </li>
      <!-- Single-link Menus (no chevron, no flyout) -->
      <li class="side-nav-item">
        <a href="apps.php" class="side-nav-link">
          <span><i class="ri-plug-line"></i> <span>Apps</span></span>
        </a>
      </li>
      <li class="side-nav-item">
        <a href="settings.php" class="side-nav-link">
          <span><i class="ri-settings-3-line"></i> <span>Settings</span></span>
        </a>
      </li>
      <!-- Master Admin pinned to bottom -->
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

<!-- Flyout panel for submenus -->
<div id="sidebar-flyout">
  <div id="flyout-content"></div>
</div>

<!-- Flyout Templates: each template contains submenu for each flyout parent -->
<template id="flyout-crm">
  <div>
    <a href="crm-dashboard.php" class="side-flyout-link"><i class="ri-dashboard-line me-2"></i>CRM Dashboard</a>
    <a href="contacts.php" class="side-flyout-link"><i class="ri-contacts-book-2-line me-2"></i>Contacts</a>
    <a href="companies.php" class="side-flyout-link"><i class="ri-building-line me-2"></i>Companies</a>
    <a href="deals.php" class="side-flyout-link"><i class="ri-hand-coin-line me-2"></i>Deals</a>
  </div>
</template>
<template id="flyout-realestate">
  <div>
    <a href="real-estate-dashboard.php" class="side-flyout-link"><i class="ri-dashboard-2-line me-2"></i>Dashboard</a>
    <a href="property-inventory.php" class="side-flyout-link"><i class="ri-building-2-line me-2"></i>Properties</a>
    <a href="real-estate-crm.php" class="side-flyout-link"><i class="ri-contacts-book-line me-2"></i>CRM</a>
    <a href="sales.php" class="side-flyout-link"><i class="ri-bill-line me-2"></i>Sales</a>
    <a href="#" class="side-flyout-link"><i class="ri-folder-line me-2"></i>Documents</a>
    <a href="#" class="side-flyout-link"><i class="ri-bar-chart-2-line me-2"></i>Reports</a>
    <a href="#" class="side-flyout-link"><i class="ri-calendar-2-line me-2"></i>Notifications</a>
    <a href="#" class="side-flyout-link"><i class="ri-shield-user-line me-2"></i>Users</a>
    <a href="portal-dashboard.php" class="side-flyout-link"><i class="ri-door-open-line me-2"></i>Customer Portal</a>
  </div>
</template>
<template id="flyout-ecommerce">
  <div>
    <a href="ecom-dashboard.php" class="side-flyout-link"><i class="ri-shopping-bag-line me-2"></i>Dashboard</a>
    <a href="products.php" class="side-flyout-link"><i class="ri-shopping-bag-line me-2"></i>Products</a>
    <a href="ecom-invoicing.php" class="side-flyout-link"><i class="ri-bill-line me-2"></i>Orders</a>
    <a href="POS.php" class="side-flyout-link" target="_blank"><i class="ri-terminal-box-line me-2"></i>Open POS</a>
  </div>
</template>
<template id="flyout-catering">
  <div>
    <a href="catering-clients.php" class="side-flyout-link"><i class="ri-user-3-line me-2"></i>Clients & Accounts</a>
    <a href="catering-orders.php" class="side-flyout-link"><i class="ri-calendar-check-line me-2"></i>Orders & Menu</a>
    <a href="catering-billing.php" class="side-flyout-link"><i class="ri-bill-line me-2"></i>Billing & Receivables</a>
    <a href="catering-reports.php" class="side-flyout-link"><i class="ri-bar-chart-box-line me-2"></i>Reports</a>
    <a href="catering-users.php" class="side-flyout-link"><i class="ri-shield-user-line me-2"></i>User Management</a>
    <a href="catering-delivery.php" class="side-flyout-link"><i class="ri-truck-line me-2"></i>Delivery & Schedule</a>
    <a href="catering-docs.php" class="side-flyout-link"><i class="ri-file-list-3-line me-2"></i>Docs & Notes</a>
    <a href="catering-notifications.php" class="side-flyout-link"><i class="ri-notification-2-line me-2"></i>Notifications</a>
  </div>
</template>
<template id="flyout-fieldservice">
  <div>
    <a href="fsm-dashboard.php" class="side-flyout-link"><i class="ri-dashboard-2-line me-2"></i>Dashboard</a>
    <a href="fsm-clients.php" class="side-flyout-link"><i class="ri-group-line me-2"></i>Clients & Sites</a>
    <a href="field-jobs.php" class="side-flyout-link"><i class="ri-calendar-check-line me-2"></i>Jobs</a>
    <a href="fsm-team.php" class="side-flyout-link"><i class="ri-user-settings-line me-2"></i>Team & Users</a>
    <a href="fsm-inventory.php" class="side-flyout-link"><i class="ri-archive-line me-2"></i>Inventory</a>
    <a href="fsm-billing.php" class="side-flyout-link"><i class="ri-bill-line me-2"></i>Billing</a>
    <a href="fsm-documents.php" class="side-flyout-link"><i class="ri-file-list-3-line me-2"></i>Documents</a>
    <a href="fsm-reports.php" class="side-flyout-link"><i class="ri-bar-chart-2-line me-2"></i>Reports</a>
    <a href="fsm-notifications.php" class="side-flyout-link"><i class="ri-notification-2-line me-2"></i>Notifications</a>
    <a href="fsm-portal.php" class="side-flyout-link"><i class="ri-door-open-line me-2"></i>Customer Portal</a>
  </div>
</template>
<template id="flyout-recruitment">
  <div>
    <a href="ats-dashboard.php" class="side-flyout-link"><i class="ri-dashboard-2-line me-2"></i>Dashboard</a>
    <a href="ats-jobs.php" class="side-flyout-link"><i class="ri-briefcase-4-line me-2"></i>Jobs</a>
    <a href="ats-candidates.php" class="side-flyout-link"><i class="ri-user-search-line me-2"></i>Candidates</a>
    <a href="ats-pipeline.php" class="side-flyout-link"><i class="ri-git-merge-line me-2"></i>Pipeline</a>
    <a href="ats-calendar.php" class="side-flyout-link"><i class="ri-calendar-2-line me-2"></i>Calendar/Schedule</a>
    <a href="ats-clients.php" class="side-flyout-link"><i class="ri-building-2-line me-2"></i>Clients & Contacts</a>
    <a href="ats-documents.php" class="side-flyout-link"><i class="ri-file-list-3-line me-2"></i>Documents</a>
    <a href="ats-reports.php" class="side-flyout-link"><i class="ri-bar-chart-2-line me-2"></i>Reports</a>
    <a href="ats-users.php" class="side-flyout-link"><i class="ri-shield-user-line me-2"></i>Users/Settings</a>
  </div>
</template>

<script>
// Sidebar Flyout Logic
let flyoutTimeout = null;

document.querySelectorAll('.side-nav-link[data-flyout]').forEach(link => {
  link.addEventListener('mouseenter', function(e) {
    clearTimeout(flyoutTimeout);
    showFlyout(this.getAttribute('data-flyout'), this);
  });
  link.addEventListener('mouseleave', function(e) {
    flyoutTimeout = setTimeout(() => { hideFlyout(); }, 120);
  });
  link.addEventListener('click', function(e) {
    e.preventDefault();
    showFlyout(this.getAttribute('data-flyout'), this);
  });
});

// Hide flyout when mouse leaves both parent and flyout
document.getElementById('sidebar-flyout').addEventListener('mouseenter', function(e) {
  clearTimeout(flyoutTimeout);
});
document.getElementById('sidebar-flyout').addEventListener('mouseleave', function(e) {
  flyoutTimeout = setTimeout(() => { hideFlyout(); }, 100);
});

// Make sure flyout only shows for one parent at a time
function showFlyout(flyoutName, parentEl) {
  let flyout = document.getElementById('sidebar-flyout');
  let tpl = document.getElementById('flyout-' + flyoutName);
  if (!tpl) return hideFlyout();

  document.getElementById('flyout-content').innerHTML = tpl.innerHTML;
  flyout.style.display = 'block';

  // Calculate flyout position just beside the sidebar, at the correct vertical offset
  let parentRect = parentEl.getBoundingClientRect();
  let sidebarRect = document.getElementById('leftside-menu').getBoundingClientRect();

  flyout.style.top = (window.scrollY + parentRect.top) + "px";
  flyout.style.left = (sidebarRect.right) + "px";

  // Set flyout height only as tall as content
  setTimeout(function() {
    flyout.style.height = "auto";
  }, 10);
}

function hideFlyout() {
  document.getElementById('sidebar-flyout').style.display = 'none';
}

// Sidebar logo toggle remains
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
