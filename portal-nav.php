
<!-- Portal Top Navigation -->
<nav class="navbar client-portal-nav navbar-expand-lg navbar-light bg-white border-bottom sticky-top" style="min-height:64px; box-shadow: 0 1px 8px #0001;">
    <div class="container-fluid px-3">
        <!-- Company Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="portal-dashboard.php">
            <img src="assets/images/flyhub-logo.png" alt="Logo" height="40" style="border-radius:6px;object-fit:contain;">
        </a>
        <!-- Hamburger for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#clientPortalMenu" aria-controls="clientPortalMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Menu items -->
        <div class="collapse navbar-collapse" id="clientPortalMenu">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-2">
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="portal-dashboard.php"></i>Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="my-property.php"></i>My Property</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="my-invoices.php"></i>My Invoices</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="my-account.php"></i>My Account</a>
                </li>
            </ul>
            <!-- Right side: Greeting & Logout -->
            <ul class="navbar-nav ms-auto gap-2 align-items-center">
                <li class="nav-item d-none d-lg-block">
                    <span class="nav-link text-primary fw-semibold">Hi, <span id="portalClientName">Lester Caraan</span>!</span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-danger btn-sm" href="portal-logout.php"><i class="ri-logout-box-r-line"></i> Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<script>
    // You can set this dynamically after login/session
    $("#portalClientName").text("Ana M.");
</script>
