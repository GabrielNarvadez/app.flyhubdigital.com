<!-- ========== Left Sidebar Start ========== -->
<div class="leftside-menu" style="background: #fff;">
  <!-- Brand Logo Light -->
  <a href="dashboard.php" class="logo logo-light">
    <span class="logo-lg">
      <img src="assets/images/flyhub_logo.webp" alt="logo" style="height: 42px;"/>
    </span>
    <span class="logo-sm">
      <img src="assets/images/flyhub_logo.webp" alt="small logo" style="height: 42px;"/>
    </span>
  </a>

  <!-- Brand Logo Dark (kept for fallback, but not used for light) -->
  <a href="dashboard.php" class="logo logo-dark d-none">
    <span class="logo-lg">
      <img src="assets/images/flyhub_logo.webp" alt="dark logo" style="height: 42px;"/>
    </span>
    <span class="logo-sm">
      <img src="assets/images/flyhub_logo.webp" alt="small logo" style="height: 42px;"/>
    </span>
  </a>

  <!-- Sidebar Hover Menu Toggle Button -->
  <div
    class="button-sm-hover"
    data-bs-toggle="tooltip"
    data-bs-placement="right"
    title="Show Full Sidebar"
  >
    <i class="ri-checkbox-blank-circle-line align-middle"></i>
  </div>

  <!-- Full Sidebar Menu Close Button -->
  <div class="button-close-fullsidebar">
    <i class="ri-close-fill align-middle"></i>
  </div>

  <!-- Sidebar -left -->
  <div class="h-100" id="leftside-menu-container" data-simplebar>
    <!-- Leftbar User -->
    <div class="leftbar-user">
      <a href="profile.php">
        <img
          src="assets/images/users/avatar-1.jpg"
          alt="user-image"
          height="42"
          class="rounded-circle shadow-sm"
        />
        <span class="leftbar-user-name mt-2">Ana Del Rosario</span>
      </a>
    </div>

    <!--- Sidemenu -->
    <ul class="side-nav">

<!--       <li class="side-nav-item">
        <a href="portal-dashboard.php" class="side-nav-link">
          <i class="ri-home-4-line"></i>
          <span> Dashboard </span>
        </a>
      </li> -->

      <li class="side-nav-item">
        <a href="portal-soa.php" class="side-nav-link">
          <i class="ri-file-list-3-line"></i>
          <span> Statements of Account </span>
        </a>
      </li>
      <li class="side-nav-item">
        <a href="portal-billing.php" class="side-nav-link">
          <i class="ri-bank-card-line"></i>
          <span> Billing & Payments </span>
        </a>
      </li>

<!--       <li class="side-nav-item">
        <a href="portal-property.php" class="side-nav-link">
          <i class="ri-building-2-line"></i>
          <span> Property Details </span>
        </a>
      </li> -->

      <li class="side-nav-item">
        <a href="portal-profile.php" class="side-nav-link">
          <i class="ri-user-3-line"></i>
          <span> Account Profile </span>
        </a>
      </li>

<!--       <li class="side-nav-item">
        <a href="portal-support.php" class="side-nav-link">
          <i class="ri-customer-service-2-line"></i>
          <span> Support</span>
        </a>
      </li>
      <li class="side-nav-item">
        <a href="portal-settings.php" class="side-nav-link">
          <i class="ri-settings-3-line"></i>
          <span> Settings </span>
        </a>
      </li>
       -->
    </ul>
    <!--- End Sidemenu -->

    <div class="clearfix"></div>
  </div>
</div>
<!-- ========== Left Sidebar End ========== -->

<!-- Inline CSS for Active Menu Indicator (Add below or in <head> of this page only) -->
<style>
  .side-nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.25rem;
    color: #111;
    background: #fff;
    text-decoration: none;
    border-radius: 0 2rem 2rem 0;
    transition: background 0.1s, color 0.1s;
    font-weight: 500;
    position: relative;
  }
  .side-nav-link:hover {
    background: #f5f5f5;
    color: #111;
  }
  .side-nav-link.active,
  .side-nav-link[aria-current="page"] {
    color: #111 !important;
    font-weight: 600;
    background: #fff !important; /* Background remains white */
  }
  .side-nav-link.active::before,
  .side-nav-link[aria-current="page"]::before {
    content: '';
    position: absolute;
    left: 0;
    top: 12px;
    bottom: 12px;
    width: 5px;
    border-radius: 8px;
    background: #0d6efd; /* Bootstrap primary color */
  }
  .side-nav-link i {
    font-size: 1.2rem;
    margin-right: 0.75rem;
  }
</style>
