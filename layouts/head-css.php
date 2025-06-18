<!-- Theme Config Js -->
<script src="assets/js/config.js"></script>

<!-- App css -->
<link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

<!-- Icons css -->
<link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />


<!-- Custom css -->
<link href="assets/css/custom.css" rel="stylesheet" type="text/css" />


<style>
	/* --- Sidebar Main Container --- */
.leftside-menu {
    background: #29303b !important;
    min-width: 220px;
    max-width: 240px;
    padding-top: 0;
    padding-bottom: 0;
    box-shadow: 2px 0 10px rgba(44, 62, 80, 0.03);
    border-right: 1px solid #23272f;
    z-index: 101;
}

/* --- Logo Block --- */
.leftside-menu .logo,
.leftside-menu .logo-light,
.leftside-menu .logo-dark {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px 0 16px 0;
    background: transparent !important;
}
.leftside-menu .logo img {
    max-height: 36px;
    width: auto;
    margin: 0 auto;
    display: block;
}

/* --- Main Nav List --- */
.leftside-menu .side-nav {
    margin-top: 18px;
}
.leftside-menu .side-nav-title {
    font-size: 11px;
    color: #8c98b6;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    padding: 16px 28px 4px 28px;
    margin-top: 10px;
    margin-bottom: 2px;
}

/* --- Sidebar Menu Items --- */
.leftside-menu .side-nav-link,
.leftside-menu .side-nav-link:visited {
    display: flex;
    align-items: center;
    gap: 13px;
    padding: 12px 28px;
    font-size: 15px;
    color: #e7eaf3 !important;
    border-radius: 7px;
    margin: 2px 0;
    transition: background 0.13s, color 0.13s;
}

.leftside-menu .side-nav-link:hover,
.leftside-menu .side-nav-link.active {
    background: #353b49;
    color: #fff !important;
}

.leftside-menu .side-nav-link i {
    font-size: 19px;
    min-width: 20px;
    color: #89a1c4;
    opacity: 0.8;
    margin-right: 5px;
}

/* --- Submenu --- */
.leftside-menu .side-nav-second-level li a,
.leftside-menu .side-nav-third-level li a {
    font-size: 14px;
    padding: 8px 38px;
    color: #b1b7c6 !important;
    border-radius: 5px;
    transition: background 0.13s, color 0.13s;
}
.leftside-menu .side-nav-second-level li a:hover {
    background: #363c48;
    color: #fff !important;
}

/* --- Add vertical spacing between items --- */
.leftside-menu .side-nav-item {
    margin-bottom: 8px;
}

/* --- Remove border from menu (modern look) --- */
.leftside-menu .h-100 {
    border: none;
    background: transparent;
}

/* --- Sidebar User Section --- */
.leftbar-user {
    padding: 12px 28px 16px 28px;
    text-align: left;
    background: none !important;
    border-bottom: 1px solid #22252b;
    margin-bottom: 6px;
}
.leftbar-user img {
    height: 40px;
    width: 40px;
    border-radius: 50%;
    margin-bottom: 4px;
    border: 2px solid #29303b;
}
.leftbar-user-name {
    font-size: 13px;
    font-weight: 600;
    color: #8c98b6;
    margin-top: 4px;
    display: block;
}

/* --- Hide scrollbars for aesthetics --- */
#leftside-menu-container[data-simplebar] .simplebar-scrollbar::before {
    opacity: 0;
}

</style>
