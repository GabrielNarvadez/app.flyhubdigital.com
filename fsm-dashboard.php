<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Super Admin Dashboard | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
        /* DASHBOARD MODERNIZATION */
        .card,
        .card-body,
        .card-header {
            border-radius: 12px !important;
        }
        .card {
            box-shadow: 0 1px 7px #0001 !important;
            margin-bottom: 1.2rem !important;
            border: 1px solid #e6e9f1 !important;
        }
        .card-body, .card-header {
            padding: 1rem 1.2rem !important;
        }
        .card-header {
            font-size: 1.05rem !important;
            font-weight: 600;
            background: #fafdff !important;
            border-bottom: 1px solid #f1f3fa !important;
        }
        .row.g-3 .col-md-2 .card {
            padding: 0 !important;
            margin-bottom: 0 !important;
        }
        .row.g-3 .card-body {
            padding: 0.8rem 0.6rem !important;
        }
        .row.g-3 .fs-5 {
            font-size: 1.32rem !important;
            margin-bottom: 0.15rem !important;
        }
        .row.g-3 .text-muted {
            font-size: 0.96rem !important;
        }
        .card .btn,
        .card .btn-sm {
            font-size: 1.01rem !important;
            padding: 0.36rem 0.85rem !important;
            border-radius: 8px !important;
            margin-bottom: 0.12rem !important;
        }
        .list-unstyled li,
        .card-body ul li {
            font-size: 1rem !important;
            margin-bottom: 0.38rem !important;
        }
        .card-header i {
            margin-right: 7px;
            font-size: 1.1rem;
            vertical-align: -2px;
        }
        .mb-3 {
            margin-bottom: 1.08rem !important;
        }
        .mb-2 {
            margin-bottom: 0.55rem !important;
        }
        .d-flex.align-items-center.gap-2 {
            gap: 1.2rem !important;
        }
        .fw-bold.fs-5 {
            font-size: 1.23rem !important;
            margin-bottom: 0.14rem;
        }
        .text-muted.small {
            font-size: 0.97rem !important;
        }
        .badge.bg-success, .badge.bg-info {
            font-size: 0.89rem !important;
            border-radius: 6px !important;
            padding: 0.22em 0.65em !important;
            letter-spacing: 0.01em;
        }
        .dashboard-table td, .dashboard-table th {
            font-size: 0.97rem !important;
            padding: 0.38rem 0.65rem !important;
        }
        .row > [class*='col-'] > .card {
            margin-bottom: 0.9rem !important;
        }
    </style>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include 'layouts/menu.php'; ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">

                    <!-- PAGE TITLE + PROFILE + TENANT SWITCH -->
                    <div class="row align-items-center mb-2" style="margin-top: 15px;">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center gap-2">
                                <img src="assets/images/users/avatar-1.jpg" class="rounded-circle" width="44" height="44" alt="avatar">
                                <div>
                                    <div class="fw-bold fs-5">Welcome, Lester!</div>
                                    <div class="text-muted small">Super Admin, Flyhub Digital</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-2 mt-md-0">
                            <select class="form-select w-auto d-inline-block" style="min-width:220px;">
                                <option>Switch to Tenant/Company...</option>
                                <option>Green Meadows Realty</option>
                                <option>Jane's Catering</option>
                                <option>Breeze Aircon Solutions</option>
                                <option>...</option>
                            </select>
                            <a href="#" class="btn btn-outline-secondary btn-sm ms-2"><i class="ri-user-search-line"></i> Impersonate</a>
                        </div>
                    </div>

                    <!-- METRICS & STATS CARDS -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="fs-5 text-primary fw-bold">143</div>
                                    <div class="text-muted small">Active Clients</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="fs-5 text-success fw-bold">9,215</div>
                                    <div class="text-muted small">Active Users</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="fs-5 text-info fw-bold">₱425K</div>
                                    <div class="text-muted small">MRR (Monthly)</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="fs-5 text-warning fw-bold">12</div>
                                    <div class="text-muted small">Open Tickets</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="fs-5 text-danger fw-bold">3</div>
                                    <div class="text-muted small">Platform Alerts</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="fs-5 text-secondary fw-bold">7</div>
                                    <div class="text-muted small">Pending Approvals</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2 COLS: FEATURED APPS + WHAT'S NEW -->
                    <div class="row mb-3">
                        <!-- FEATURED MODULES -->
                        <div class="col-lg-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-white fw-bold">
                                    <i class="ri-apps-2-line"></i> Featured Apps & Modules
                                </div>
                                <div class="card-body pb-2">
                                    <div class="d-flex flex-wrap gap-3">
                                        <a href="real-estate.php" class="btn btn-outline-primary btn-sm"><i class="ri-building-2-line"></i> Real Estate</a>
                                        <a href="catering.php" class="btn btn-outline-success btn-sm"><i class="ri-restaurant-2-line"></i> Catering</a>
                                        <a href="field-service.php" class="btn btn-outline-warning btn-sm"><i class="ri-tools-line"></i> Field Service</a>
                                        <a href="recruitment.php" class="btn btn-outline-info btn-sm"><i class="ri-profile-line"></i> Recruitment</a>
                                        <a href="crm.php" class="btn btn-outline-secondary btn-sm"><i class="ri-contacts-book-line"></i> CRM</a>
                                        <a href="analytics.php" class="btn btn-outline-dark btn-sm"><i class="ri-bar-chart-2-line"></i> Analytics</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- WHAT'S NEW -->
                        <div class="col-lg-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-white fw-bold">
                                    <i class="ri-megaphone-line"></i> What's New & Announcements
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-2">
                                        <li class="mb-2">
                                            <strong class="text-primary">[Aug 2024]</strong> Launched "Job Pipeline" Kanban for Recruitment <span class="badge bg-success ms-1">New</span>
                                        </li>
                                        <li class="mb-2">
                                            <strong class="text-primary">[Jul 2024]</strong> Catering AR Aging Report added <span class="badge bg-success ms-1">New</span>
                                        </li>
                                        <li>
                                            <strong class="text-primary">[Jul 2024]</strong> API integration monitor live! <span class="badge bg-info ms-1">Update</span>
                                        </li>
                                    </ul>
                                    <a href="#" class="btn btn-link btn-sm p-0">See all updates</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SYSTEM STATUS, BILLING, USAGE, SUPPORT -->
                    <div class="row mb-3">
                        <!-- SYSTEM STATUS & INTEGRATIONS -->
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-white fw-bold">
                                    <i class="ri-shield-check-line"></i> Platform Health
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-2">
                                        <li>Uptime: <span class="fw-bold text-success">99.98%</span></li>
                                        <li>API Errors: <span class="fw-bold text-danger">2</span></li>
                                        <li>Integrations: <span class="text-success">All OK</span></li>
                                    </ul>
                                    <a href="#" class="btn btn-link btn-sm p-0">View System Logs</a>
                                </div>
                            </div>
                        </div>
                        <!-- BILLING -->
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-white fw-bold">
                                    <i class="ri-wallet-3-line"></i> Billing & Revenue
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-2">
                                        <li>MRR: <span class="fw-bold text-info">₱425,000</span></li>
                                        <li>Overdue: <span class="fw-bold text-danger">₱26,000</span></li>
                                        <li>Trial-to-Paid: <span class="fw-bold text-success">87%</span></li>
                                    </ul>
                                    <a href="#" class="btn btn-link btn-sm p-0">See Details</a>
                                </div>
                            </div>
                        </div>
                        <!-- APP USAGE -->
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-white fw-bold">
                                    <i class="ri-bar-chart-box-line"></i> App Usage
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-2">
                                        <li>Most Used: <span class="fw-bold">CRM</span></li>
                                        <li>Growth: <span class="text-success">+12% this month</span></li>
                                        <li>New Apps: <span class="fw-bold text-primary">Recruitment</span></li>
                                    </ul>
                                    <a href="#" class="btn btn-link btn-sm p-0">View Analytics</a>
                                </div>
                            </div>
                        </div>
                        <!-- SUPPORT -->
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-white fw-bold">
                                    <i class="ri-customer-service-2-line"></i> Support & Incidents
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-2">
                                        <li>Open Tickets: <span class="fw-bold text-warning">12</span></li>
                                        <li>Avg. Time to Close: <span class="fw-bold">2.2h</span></li>
                                        <li>Escalations: <span class="fw-bold text-danger">1</span></li>
                                    </ul>
                                    <a href="#" class="btn btn-link btn-sm p-0">Go to Support</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CLIENTS & ACTIVITY -->
                    <div class="row mb-3">
                        <!-- CLIENTS SNAPSHOT -->
                        <div class="col-lg-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-white fw-bold">
                                    <i class="ri-briefcase-4-line"></i> Clients & Tenants Overview
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <span class="fw-bold">143</span> total clients /
                                        <span class="fw-bold">11</span> new this month /
                                        <span class="fw-bold text-danger">3</span> at risk
                                    </div>
                                    <div>
                                        <a href="#" class="btn btn-outline-primary btn-sm">View All Clients</a>
                                        <a href="#" class="btn btn-outline-secondary btn-sm">Signups & Churn</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ACTIVITY TIMELINE -->
                        <div class="col-lg-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-white fw-bold">
                                    <i class="ri-timeline-view"></i> Recent Activity
                                </div>
                                <div class="card-body" style="max-height:210px;overflow:auto;">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2"><span class="fw-bold">Jane's Catering</span> upgraded to Pro Plan <span class="text-muted small">2 min ago</span></li>
                                        <li class="mb-2"><span class="fw-bold">Breeze Aircon Solutions</span> payment posted <span class="text-muted small">8 min ago</span></li>
                                        <li class="mb-2"><span class="fw-bold">Green Meadows Realty</span> new user added <span class="text-muted small">24 min ago</span></li>
                                        <li class="mb-2"><span class="fw-bold text-danger">Platform alert</span> Shopify sync error <span class="text-muted small">30 min ago</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TASKS, ANNOUNCEMENTS, SECURITY -->
                    <div class="row">
                        <!-- TASKS / APPROVALS -->
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-white fw-bold">
                                    <i class="ri-task-line"></i> Tasks & Approvals
                                </div>
                                <div class="card-body">
                                    <ul class="mb-3">
                                        <li>3 API keys expiring soon</li>
                                        <li>7 module enablement requests</li>
                                        <li>2 white-label requests</li>
                                    </ul>
                                    <a href="#" class="btn btn-link btn-sm p-0">View All Tasks</a>
                                </div>
                            </div>
                        </div>
                        <!-- COMPLIANCE & SECURITY -->
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-white fw-bold">
                                    <i class="ri-lock-line"></i> Compliance & Security
                                </div>
                                <div class="card-body">
                                    <ul class="mb-3">
                                        <li>0 failed logins (last 24h)</li>
                                        <li>2 permission escalations</li>
                                        <li>All tenants up-to-date</li>
                                    </ul>
                                    <a href="#" class="btn btn-link btn-sm p-0">Security Settings</a>
                                </div>
                            </div>
                        </div>
                        <!-- TEAM ANNOUNCEMENTS -->
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-white fw-bold">
                                    <i class="ri-group-line"></i> Team Announcements
                                </div>
                                <div class="card-body">
                                    <ul class="mb-3">
                                        <li>DevOps training Friday 3pm</li>
                                        <li>Changelog updates now live</li>
                                    </ul>
                                    <a href="#" class="btn btn-link btn-sm p-0">View All Announcements</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> <!-- container-fluid -->
            </div> <!-- content -->
            <?php include 'layouts/footer.php'; ?>
        </div>
        <!-- End Page content -->
    </div>
    <!-- END wrapper -->

    <?php include 'layouts/right-sidebar.php'; ?>
    <?php include 'layouts/footer-scripts.php'; ?>
    <script src="assets/js/app.min.js"></script>
</body>
</html>
