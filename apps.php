<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Applications | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
        .app-card {
            min-height: 124px;
            position: relative;
            padding: 12px 12px 38px 12px;
            transition: box-shadow .15s;
            border-radius: 7px;
            background: #fff;
        }
        .app-card .dropdown {
            position: absolute;
            top: 8px;
            right: 9px;
            z-index: 2;
        }
        .app-card .status-badge {
            position: absolute;
            right: 13px;
            bottom: 9px;
            font-size: 12px;
            padding: 0.21em 0.7em;
            border-radius: 10px;
            font-weight: 600;
            color: #fff !important;
            box-shadow: 0 1px 2px rgba(80,80,80,0.07);
            pointer-events: none;
        }
        .status-live {
            background-color: #b5ecc7 !important;
            color: #26734d !important;
        }
        .status-beta {
            background-color: #ffe7a8 !important;
            color: #bc8b11 !important;
        }
        .status-dev {
            background-color: #e3e5ea !important;
            color: #505868 !important;
        }
        .app-card .avatar-title {
            height: 38px !important;
            width: 38px !important;
            font-size: 1.35rem;
            margin-right: 0.65rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .app-card .d-flex.align-items-center {
            gap: 0.5rem;
        }
        .app-card:hover {
            box-shadow: 0 4px 18px 0 rgba(85,110,230,.09);
            border-color: #d3dae3;
        }
        .app-card .p-2,
        .app-card .pt-3 {
            padding: 8px 0 0 0 !important;
            margin-bottom: 0 !important;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'layouts/menu.php'; ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Lists</a></li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Apps</h4>

                                <?php
                                // ---- Apps List with explicit status (Live, Beta, Development) ----
                                $apps = [
                                    [
                                        'name' => 'AR Tracker',
                                        'desc' => 'Track invoice receivables and payments for all your clients in one place. Get a quick view of aging and outstanding balances.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/app-details.php',
                                        'icon' => 'ri-coins-line',
                                        'icon_color' => 'text-warning',
                                        'categories' => ['All', 'Finance'],
                                        'status' => 'Live'
                                    ],
                                    [
                                        'name' => 'SOA Manager',
                                        'desc' => 'Manage Statements of Account and keep your records organized. Issue, review, and track SOAs with history and notes.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/soa-manager.php',
                                        'icon' => 'ri-file-list-3-line',
                                        'icon_color' => 'text-info',
                                        'categories' => ['All', 'Finance'],
                                        'status' => 'Beta'
                                    ],
                                    [
                                        'name' => 'Contacts Management',
                                        'desc' => 'Centralized contacts database for storing, searching, and linking clients and stakeholders across your business modules.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/contacts',
                                        'icon' => 'ri-contacts-book-2-line',
                                        'icon_color' => 'text-success',
                                        'categories' => ['All', 'General'],
                                        'status' => 'Live'
                                    ],
                                    [
                                        'name' => 'Companies Management',
                                        'desc' => 'Store and manage company profiles, link contacts, view transaction history, and handle corporate records with ease.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/companies',
                                        'icon' => 'ri-building-line',
                                        'icon_color' => 'text-primary',
                                        'categories' => ['All', 'General'],
                                        'status' => 'Live'
                                    ],
                                    [
                                        'name' => 'Products/Units Inventory',
                                        'desc' => 'Track all your products, real estate units, SKUs, and stock levels, including categories and status. View inventory at a glance.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/inventory',
                                        'icon' => 'ri-box-3-line',
                                        'icon_color' => 'text-secondary',
                                        'categories' => ['All', 'General'],
                                        'status' => 'Beta'
                                    ],
                                    [
                                        'name' => 'Invoices Module',
                                        'desc' => 'Create, send, and track invoices. Get paid faster with payment logging and automated invoice reminders for overdue balances.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/invoices',
                                        'icon' => 'ri-bill-line',
                                        'icon_color' => 'text-danger',
                                        'categories' => ['All', 'Finance'],
                                        'status' => 'Live'
                                    ],
                                    [
                                        'name' => 'Customer Portal',
                                        'desc' => 'A self-service portal where clients can securely view their SOA, invoices, and transaction history 24/7 online.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/customer-portal',
                                        'icon' => 'ri-door-open-line',
                                        'icon_color' => 'text-success',
                                        'categories' => ['All', 'General'],
                                        'status' => 'Beta'
                                    ],
                                    [
                                        'name' => 'Petty Cash / POS Lite',
                                        'desc' => 'Simple point of sale for cash and petty cash tracking. Record daily sales, cash-ins/outs, and generate reports easily.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/pos-lite',
                                        'icon' => 'ri-cash-line',
                                        'icon_color' => 'text-warning',
                                        'categories' => ['All', 'Finance'],
                                        'status' => 'Beta'
                                    ],
                                    [
                                        'name' => 'User Management',
                                        'desc' => 'Admin panel for managing user accounts, permissions, access rights, and user role assignments within the platform.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/user-management',
                                        'icon' => 'ri-user-settings-line',
                                        'icon_color' => 'text-primary',
                                        'categories' => ['All', 'General'],
                                        'status' => 'Beta'
                                    ],
                                    [
                                        'name' => 'Session Booking App',
                                        'desc' => 'Let clients book and manage appointments or sessions, with automated reminders and calendar syncing for your business.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/session-booking',
                                        'icon' => 'ri-calendar-check-line',
                                        'icon_color' => 'text-info',
                                        'categories' => ['All', 'General'],
                                        'status' => 'Beta'
                                    ],
                                    [
                                        'name' => 'Client Progress Tracker',
                                        'desc' => 'Visual tracker for client sessions, milestones, and feedback. Great for coaches, consultants, and account managers.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/progress-tracker',
                                        'icon' => 'ri-bar-chart-box-line',
                                        'icon_color' => 'text-warning',
                                        'categories' => ['All', 'General'],
                                        'status' => 'Development'
                                    ],
                                    [
                                        'name' => 'Custom Plan Builder',
                                        'desc' => 'Create and assign custom plans or programs for each client. Supports fitness, business, and goal planning workflows.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/plan-builder',
                                        'icon' => 'ri-draft-line',
                                        'icon_color' => 'text-secondary',
                                        'categories' => ['All', 'General'],
                                        'status' => 'Development'
                                    ],
                                    [
                                        'name' => 'Note Logger',
                                        'desc' => 'Log private notes, session summaries, or share notes with your team. Supports confidential and collaborative entries.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/note-logger',
                                        'icon' => 'ri-sticky-note-line',
                                        'icon_color' => 'text-warning',
                                        'categories' => ['All', 'General'],
                                        'status' => 'Development'
                                    ],
                                    [
                                        'name' => 'Proposal Generator',
                                        'desc' => 'Easily create and send professional client proposals. Export as PDF or track status and responses in-app.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/proposal-generator',
                                        'icon' => 'ri-file-paper-2-line',
                                        'icon_color' => 'text-danger',
                                        'categories' => ['All', 'Sales'],
                                        'status' => 'Development'
                                    ],
                                    [
                                        'name' => 'Project Tracker (Kanban Lite)',
                                        'desc' => 'Drag and drop tasks to manage projects and deliverables using a simple, visual kanban board that’s easy to use.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/project-tracker',
                                        'icon' => 'ri-todo-line',
                                        'icon_color' => 'text-primary',
                                        'categories' => ['All', 'General'],
                                        'status' => 'Development'
                                    ],
                                    [
                                        'name' => 'Client Onboarding App',
                                        'desc' => 'Automated onboarding with checklists, document uploads, e-signatures, and task assignments for new clients.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/client-onboarding',
                                        'icon' => 'ri-login-box-line',
                                        'icon_color' => 'text-success',
                                        'categories' => ['All', 'General'],
                                        'status' => 'Development'
                                    ],
                                    [
                                        'name' => 'Feedback Collector / NPS',
                                        'desc' => 'Gather feedback, testimonials, and NPS scores from clients after sessions or milestones for service improvement.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/feedback-collector',
                                        'icon' => 'ri-feedback-line',
                                        'icon_color' => 'text-info',
                                        'categories' => ['All', 'General'],
                                        'status' => 'Development'
                                    ],
                                    [
                                        'name' => 'Time Log Tracker',
                                        'desc' => 'Track consulting or project hours by client and export timesheets for payroll or reporting as needed.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/time-log',
                                        'icon' => 'ri-time-line',
                                        'icon_color' => 'text-warning',
                                        'categories' => ['All', 'General'],
                                        'status' => 'Development'
                                    ],
                                    [
                                        'name' => 'Workout Plan Builder',
                                        'desc' => 'Design, assign, and monitor workout routines for clients with completion tracking and progress overview.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/workout-plan',
                                        'icon' => 'ri-heart-pulse-line',
                                        'icon_color' => 'text-danger',
                                        'categories' => ['All', 'General'],
                                        'status' => 'Development'
                                    ],
                                    [
                                        'name' => 'Habit Tracker',
                                        'desc' => 'Log and analyze daily or weekly habits like sleep, nutrition, exercise, and productivity for coaching clients.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/habit-tracker',
                                        'icon' => 'ri-checkbox-multiple-line',
                                        'icon_color' => 'text-primary',
                                        'categories' => ['All', 'General'],
                                        'status' => 'Development'
                                    ],
                                    [
                                        'name' => 'Meal Plan Logger',
                                        'desc' => 'Clients log meals, view meal plans, and receive coach feedback. Useful for fitness, nutrition, and wellness services.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/meal-plan-logger',
                                        'icon' => 'ri-restaurant-line',
                                        'icon_color' => 'text-success',
                                        'categories' => ['All', 'General'],
                                        'status' => 'Development'
                                    ],
                                    [
                                        'name' => 'Goal Visualizer',
                                        'desc' => 'Set, visualize, and celebrate client goals with charts and milestone markers. Track all progress in one spot.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/goal-visualizer',
                                        'icon' => 'ri-line-chart-line',
                                        'icon_color' => 'text-info',
                                        'categories' => ['All', 'General'],
                                        'status' => 'Development'
                                    ],
                                    [
                                        'name' => 'Automated Check-in Bot',
                                        'desc' => 'Schedule automatic weekly check-ins via email or SMS. Log responses and keep clients accountable.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/checkin-bot',
                                        'icon' => 'ri-robot-line',
                                        'icon_color' => 'text-warning',
                                        'categories' => ['All', 'Integrations'],
                                        'status' => 'Development'
                                    ],
                                    [
                                        'name' => 'Content Library',
                                        'desc' => 'Upload and share content like PDFs, videos, and templates. Clients access resources via their portal.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/content-library',
                                        'icon' => 'ri-folder-line',
                                        'icon_color' => 'text-secondary',
                                        'categories' => ['All', 'General'],
                                        'status' => 'Development'
                                    ],
                                    [
                                        'name' => 'Group Coaching Space',
                                        'desc' => 'Create groups, assign homework, and let clients see shared progress, chat, and wins in one space.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/group-coaching',
                                        'icon' => 'ri-group-line',
                                        'icon_color' => 'text-success',
                                        'categories' => ['All', 'General'],
                                        'status' => 'Development'
                                    ],
                                    // Integrations - using RemixIcon only
                                    [
                                        'name' => 'Shopify',
                                        'desc' => 'Connect, sync, and manage your Shopify products, orders, and customers directly from the Flyhub platform.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/shopify',
                                        'icon' => 'ri-shopping-bag-3-line',
                                        'icon_color' => 'text-success',
                                        'categories' => ['All', 'Integrations', 'Sales'],
                                        'status' => 'Beta'
                                    ],
                                    [
                                        'name' => 'Mailchimp',
                                        'desc' => 'Integrate Mailchimp for automated marketing emails and sync contact lists with your CRM instantly.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/mailchimp',
                                        'icon' => 'ri-mail-line',
                                        'icon_color' => 'text-warning',
                                        'categories' => ['All', 'Integrations', 'Marketing'],
                                        'status' => 'Beta'
                                    ],
                                    [
                                        'name' => 'HubSpot',
                                        'desc' => 'Bring HubSpot CRM and automation features into Flyhub for unified marketing and sales tracking.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/hubspot',
                                        'icon' => 'ri-customer-service-2-line',
                                        'icon_color' => 'text-danger',
                                        'categories' => ['All', 'Integrations', 'Sales', 'Marketing'],
                                        'status' => 'Beta'
                                    ],
                                    [
                                        'name' => 'Odoo',
                                        'desc' => 'Full-featured ERP integration with Odoo. Sync your business workflows, invoices, and inventory with ease.',
                                        'url' => 'http://localhost/app.flyhubdigital.com/odoo',
                                        'icon' => 'ri-apps-2-line',
                                        'icon_color' => 'text-info',
                                        'categories' => ['All', 'Integrations', 'Finance', 'General'],
                                        'status' => 'Development'
                                    ],
                                ];

                                // Status CSS map
                                function statusClass($status) {
                                    if ($status === 'Live') return 'status-live';
                                    if ($status === 'Beta') return 'status-beta';
                                    return 'status-dev';
                                }

                                $tabs = ['All', 'General', 'Marketing', 'Sales', 'Finance', 'Integrations'];
                                ?>

                                <div class="col-xl-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="header-title">
                                                Enhance your Flyhub Business App by adding custom features tailored to your business needs or integrating with popular third-party applications
                                            </h4>
                                            <p class="text-muted fs-14 mb-3"></p>
                                            <div class="row">
                                                <div class="col-sm-3 mb-2 mb-sm-0">
                                                    <div class="nav flex-column nav-pills" id="apps-tab" role="tablist" aria-orientation="vertical">
                                                        <?php foreach ($tabs as $i => $tab): ?>
                                                            <a class="nav-link<?php if ($i==0) echo ' active'; ?>"
                                                               id="apps-tab-<?= strtolower($tab) ?>"
                                                               data-bs-toggle="pill"
                                                               href="#apps-pane-<?= strtolower($tab) ?>"
                                                               role="tab"
                                                               aria-controls="apps-pane-<?= strtolower($tab) ?>"
                                                               aria-selected="<?= $i==0 ? 'true' : 'false' ?>">
                                                                <?= htmlspecialchars($tab) ?>
                                                            </a>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                                <div class="col-sm-9">
                                                    <div class="tab-content" id="apps-tabContent">
                                                        <?php foreach ($tabs as $i => $tab): ?>
                                                            <div class="tab-pane fade<?php if ($i==0) echo ' show active'; ?>"
                                                                 id="apps-pane-<?= strtolower($tab) ?>"
                                                                 role="tabpanel"
                                                                 aria-labelledby="apps-tab-<?= strtolower($tab) ?>">
                                                                <div class="row mx-n1 g-0">
                                                                    <?php
                                                                    $appsInTab = array_filter($apps, function($app) use ($tab) {
                                                                        return in_array($tab, $app['categories']);
                                                                    });
                                                                    if (count($appsInTab) == 0): ?>
                                                                        <div class="col-12">
                                                                            <div class="alert alert-warning mb-0">No apps yet for this category.</div>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                    <?php foreach ($appsInTab as $app): ?>
                                                                        <div class="col-xxl-3 col-lg-6 d-flex">
                                                                            <div class="card m-1 shadow-none border app-card flex-fill">
                                                                                <div class="dropdown">
                                                                                    <button class="btn btn-link btn-sm text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                        <i class="ri-more-2-fill fs-18"></i>
                                                                                    </button>
                                                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                                                        <li>
                                                                                            <button class="dropdown-item view-details-btn" type="button" data-app="<?= htmlspecialchars($app['name']) ?>">View Details</button>
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>
                                                                                <div class="p-2 pt-3">
                                                                                    <div class="d-flex align-items-center">
                                                                                        <div class="avatar-sm flex-shrink-0">
                                                                                            <span class="avatar-title bg-light rounded" style="height:46px;width:46px;">
                                                                                                <i class="<?= htmlspecialchars($app['icon']) ?> <?= htmlspecialchars($app['icon_color']) ?> fs-24 fw-normal"></i>
                                                                                            </span>
                                                                                        </div>
                                                                                        <div>
                                                                                            <a href="<?= htmlspecialchars($app['url']) ?>" class="fw-bold text-primary fs-15" target="_blank" rel="noopener">
                                                                                                <?= htmlspecialchars($app['name']) ?>
                                                                                            </a>
                                                                                            <div class="mb-0 fs-13 text-muted" style="max-width:190px;">
                                                                                                <?= htmlspecialchars($app['desc']) ?>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <span class="status-badge <?= statusClass($app['status']) ?>"><?= htmlspecialchars($app['status']) ?></span>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- container -->
            </div>
            <!-- content -->

            <?php include 'layouts/footer.php'; ?>
        </div>
        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    <?php include 'layouts/right-sidebar.php'; ?>
    <?php include 'layouts/footer-scripts.php'; ?>
    <script src="assets/js/app.min.js"></script>
    <script>
        // Dropdown "View Details" placeholder
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.view-details-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var appName = btn.getAttribute('data-app');
                    alert("View Details for " + appName + " (coming soon!)");
                });
            });
        });
    </script>
</body>
</html>
