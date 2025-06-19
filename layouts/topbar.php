<?php

require_once __DIR__ . '/config.php'; // Adjust path to your config

$user_name = "User";
$user_role = "";
$user_avatar = "avatar-default.jpg"; // fallback if empty

if (isset($_SESSION['user_id'])) {
    $sql = "SELECT name, role, avatar FROM users WHERE id = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($name, $role, $avatar);
    if ($stmt->fetch()) {
        $user_name = $name;
        $user_role = $role;
        if ($avatar) $user_avatar = $avatar;
    }
    $stmt->close();
}

// Build the avatar image or icon
$user_avatar_img = '';
if (!empty($user_avatar) && $user_avatar !== 'avatar-default.jpg') {
    // Use uploaded avatar image
    $user_avatar_img = '<img src="assets/images/users/' . htmlspecialchars($user_avatar) . '" alt="user-image" width="32" class="rounded-circle">';
} else {
    // Use SVG icon as default
    $user_avatar_img = '
    <span class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width:32px; height:32px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#adb5bd" class="bi bi-person" viewBox="0 0 16 16">
            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm4-3a4 4 0 1 1-8 0 4 4 0 0 1 8 0z"/>
            <path fill-rule="evenodd" d="M14 14s-1-1.5-6-1.5S2 14 2 14v1h12v-1zm-1.5-.5c.276 0 .5.224.5.5v.5H3v-.5c0-.276.224-.5.5-.5 1.306 0 2.417.835 2.83 2h2.34c.413-1.165 1.524-2 2.83-2z"/>
        </svg>
    </span>
    ';
}
?>
<div class="navbar-custom">
    <div class="topbar container-fluid">
        <div class="d-flex align-items-center gap-lg-2 gap-1">

            <!-- Logo Section with ID for toggling -->
            <div class="logo-topbar" id="logoTopbar">
                <a href="index.php" class="logo-light">
                    <span class="logo-lg">
                        <img src="assets/images/flyhub_logo.webp" alt="logo" style="height: 42px;">
                    </span>
                    <span class="logo-sm">
                        <img src="assets/images/flyhub_logo.webp" alt="small logo">
                    </span>
                </a>
                <a href="index.php" class="logo-dark">
                    <span class="logo-lg">
                        <span><img src="assets/images/flyhub_logo.webp" alt="logo"></span>
                    </span>
                    <span class="logo-sm">
                        <img src="assets/images/flyhub_logo.webp" alt="small logo">
                    </span>
                </a>
            </div>

            <!-- Toggle Button triggers logo hide/show -->
            <button class="button-toggle-menu" onclick="toggleSidebarLogo()">
                <i class="ri-menu-2-fill"></i>
            </button>

            <button class="navbar-toggle" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <div class="lines">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>

            <!-- App Search -->
            <div class="app-search dropdown d-none d-lg-block">
                <!-- <form>
                    <div class="input-group">
                        <input type="search" class="form-control dropdown-toggle" placeholder="Search..." id="top-search">
                        <span class="ri-search-line search-icon"></span>
                    </div>
                </form> -->

                <div class="dropdown-menu dropdown-menu-animated dropdown-lg" id="search-dropdown">
                    <div class="dropdown-header noti-title">
                        <h5 class="text-overflow mb-1">Found <b class="text-decoration-underline">08</b> results</h5>
                    </div>
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="ri-file-chart-line fs-16 me-1"></i>
                        <span>Analytics Report</span>
                    </a>
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="ri-lifebuoy-line fs-16 me-1"></i>
                        <span>How can I help you?</span>
                    </a>
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="ri-user-settings-line fs-16 me-1"></i>
                        <span>User profile settings</span>
                    </a>
                    <div class="dropdown-header noti-title">
                        <h6 class="text-overflow mt-2 mb-1 text-uppercase">Users</h6>
                    </div>
                    <div class="notification-list">
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <div class="d-flex">
                                <img class="d-flex me-2 rounded-circle" src="assets/images/users/avatar-2.jpg" alt="Generic placeholder image" height="32">
                                <div class="w-100">
                                    <h5 class="m-0 fs-14">Erwin Brown</h5>
                                    <span class="fs-12 mb-0">UI Designer</span>
                                </div>
                            </div>
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <div class="d-flex">
                                <img class="d-flex me-2 rounded-circle" src="assets/images/users/avatar-5.jpg" alt="Generic placeholder image" height="32">
                                <div class="w-100">
                                    <h5 class="m-0 fs-14">Jacob Deo</h5>
                                    <span class="fs-12 mb-0">Developer</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <ul class="topbar-menu d-flex align-items-center gap-3">
            <li class="dropdown d-lg-none">
                <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <i class="ri-search-line fs-22"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-animated dropdown-lg p-0">
                    <form class="p-3">
                        <input type="search" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                    </form>
                </div>
            </li>

            <li class="d-none d-sm-inline-block">
                <div class="nav-link" id="light-dark-mode" data-bs-toggle="tooltip" data-bs-placement="left" title="Theme Mode">
                    <i class="ri-moon-line fs-22"></i>
                </div>
            </li>

            <li class="d-none d-md-inline-block">
                <a class="nav-link" href="" data-toggle="fullscreen">
                    <i class="ri-fullscreen-line fs-22"></i>
                </a>
            </li>

            <li class="dropdown">
                <a class="nav-link dropdown-toggle arrow-none nav-user px-2" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="account-user-avatar">
                        <?= $user_avatar_img ?>
                    </span>
                    <span class="d-lg-flex flex-column gap-1 d-none">
                        <h5 class="my-0"><?= htmlspecialchars($user_name) ?></h5>
                        <h6 class="my-0 fw-normal"><?= htmlspecialchars($user_role) ?></h6>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                   
                    <a href="user-account.php" class="dropdown-item">
                        <i class="ri-account-circle-line fs-18 align-middle me-1"></i>
                        <span>My Account</span>
                    </a>
                    <a href="settings.php" class="dropdown-item">
                        <i class="ri-settings-4-line fs-18 align-middle me-1"></i>
                        <span>Settings</span>
                    </a>
                    <a href="auth-logout.php" class="dropdown-item">
                        <i class="ri-logout-box-line fs-18 align-middle me-1"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</div>

<!-- Inline JS for logo hide/show -->
<script>
let sidebarCollapsed = false;
function toggleSidebarLogo() {
    sidebarCollapsed = !sidebarCollapsed;
    var logo = document.getElementById('logoTopbar');
    if (sidebarCollapsed) {
        logo.style.display = "none";
    } else {
        logo.style.display = "";
    }
}
</script>
