<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>App Detail | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
        body {
            background: #f9fafc !important;
            font-family: 'Inter', Arial, sans-serif;
        }
        .content-page {
            padding-top: 0;
        }
        .container {
            max-width: 1200px;
            padding-left: 32px;
            padding-right: 32px;
        }
        .app-detail-banner {
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 1.7rem;
            background: #fff;
            border: 1px solid #ebeff4;
            min-height: 180px;
            box-shadow: 0 1px 6px 0 #f4f5f8;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .app-detail-banner img {
            max-width: 75%;
            min-height: 160px;
            object-fit: cover;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .app-detail-icon {
            height: 54px;
            width: 54px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f7fa;
            margin-right: 1.1rem;
            font-size: 2.15rem;
            color: #3b82f6;
            box-shadow: 0 1px 5px 0 #f4f5f8;
        }
        .app-meta-badges .badge {
            font-size: 0.93em;
            margin-right: 0.3em;
            padding: 0.4em 1em;
            border-radius: 7px;
            font-weight: 500;
            vertical-align: middle;
            background: #f5f7fa;
            color: #444;
        }
        .badge.bg-info { background: #e3f2fd !important; color: #0d72ba !important; }
        .badge.bg-success { background: #e6f9ef !important; color: #299764 !important; }
        .badge.bg-warning { background: #fff9e3 !important; color: #b88607 !important; }
        .rating-stars { color: #ffc107; font-size: 1.1em; vertical-align: middle; }
        .app-page-section, .card, .alert, .app-detail-banner {
            background: #fff;
            border-radius: 9px;
            border: 1px solid #e6ebf0;
            margin-bottom: 1.2rem;
            box-shadow: 0 1px 6px 0 #f4f5f8;
        }
        .alert-warning {
            border-radius: 6px;
            border: 1px solid #ffe9ae;
            background: #fff8e1;
            color: #826100;
            font-size: 1rem;
            margin-bottom: 1.5rem;
            padding: 12px 22px;
        }
        h3, h4, h5 { font-weight: 600; color: #1d232b; }
        h3 { font-size: 1.36rem; }
        h4 { font-size: 1.12rem; margin-top: 1.7rem; margin-bottom: 1.15rem; }
        h5 { font-size: 1.06rem; margin-bottom: 0.52rem; }
        .app-page-section {
            border: none;
            box-shadow: none;
            padding: 20px 20px 20px 20px;
        }
        .feature-check-table th {
            background: #f4f6fa !important;
            border-top: none;
            font-size: 15px;
            font-weight: 600;
            padding-top: 20px;
            padding-bottom: 18px;
        }
        .feature-check-table td, .feature-check-table th {
            font-size: 15px;
            vertical-align: middle;
            border-color: #ececec !important;
            text-align: center;
            padding-top: 18px;
            padding-bottom: 18px;
            padding-left: 30px;
            padding-right: 30px;
        }
        .feature-check-table tr td:first-child {
            text-align: left;
            font-size: 15px;
            color: #3a3a3a;
            font-weight: 500;
        }
        .feature-check-table td[data-check="yes"] { color: #299764; font-weight: 600; font-size: 1.23rem; }
        .feature-check-table td[data-check="no"] { color: #e44d44; font-weight: 600; font-size: 1.23rem; }
        .feature-check-table tbody tr { border-radius: 6px; }

        .plan-card {
            border-radius: 9px;
            border: 1px solid #e6ebf0;
            background: #f9fafc;
            box-shadow: 0 1px 6px 0 #f3f5fa;
            margin-bottom: 0.9rem;
        }
        .plan-card .card-title { font-size: 1.13rem; font-weight: 600; }
        .plan-card .fs-3 { font-size: 1.48rem; }

        .sidebar-widgets {
            border-left: 1px solid #f0f0f0;
            padding-left: 20px;
            min-height: 600px;
        }
        @media (max-width:991px) {
            .sidebar-widgets { border-left: none; padding-left: 0; margin-top: 32px; min-height: auto; }
        }
        .card .card-body {
            padding: 1.12rem 1.22rem;
            background: #fff;
        }
        .card {
            box-shadow: 0 1px 4px 0 #f3f5fa;
            border-radius: 9px;
        }
        .btn { border-radius: 5px; font-weight: 500; }
        .btn-sm { font-size: 0.99em; padding: 4px 18px 4px 18px; }
        .btn-outline-secondary { border-color: #e1e4e8; color: #999; }
        .btn-outline-secondary:hover { background: #e1e4e8; color: #999; }
        .btn-outline-primary { border-color: #afd4f6; color: #999; }
        .btn-primary { background: #2176bd; border: none; }
        .btn-success:hover { background: #1d4b6a; }
        a:hover { color: #133960; }
        .text-muted { color: #8a98a8 !important; }
        .fw-bold { font-weight: 600 !important; }
        .shadow-sm { box-shadow: 0 1px 5px rgba(180, 190, 210, 0.09) !important; }
        .gap-2 { gap: 0.54rem !important; }
        .me-2 { margin-right: 0.53rem !important; }
        .me-3 { margin-right: 1.02rem !important; }
        .ms-auto { margin-left: auto !important; }
        .d-none { display: none !important; }
        .d-lg-flex { display: flex !important; }
        .align-items-center { align-items: center !important; }
        .rounded { border-radius: 8px !important; }
        img.img-fluid { border-radius: 8px; }
        .btn-group-tight .btn { margin-right: 0.13rem; }
        .btn-group-tight .btn:last-child { margin-right: 0; }
        @media (min-width:1400px) {
            .container { max-width: 1370px; }
        }
        .app-page-section p, .app-page-section ul, .app-page-section li {
            font-size: 15.5px;
            color: #36383a;
            line-height: 1.75;
            margin-bottom: 9px;
        }
        .app-page-section ul { padding-left: 1.4rem; margin-bottom: 1.3rem; }
        .review-block {
            background: #f8fafc;
            border: 1px solid #edf2f6;
            border-radius: 7px;
            margin-bottom: 1.2rem;
            padding: 1rem 1.3rem 0.9rem 1.3rem;
            min-height: 90px;
        }
        .review-block .fw-bold { font-size: 1rem; }
        .review-block .rating-stars { font-size: 1.1rem; }
        .review-block .text-muted { font-size: 0.97rem; }
        .table-responsive { margin-bottom: 1.3rem; }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'layouts/menu.php'; ?>

        <div class="content-page">
            <div class="content" style="padding-top: 35px;">
                <div class="container">

                    <!-- App Header/Meta + Sidebar Layout -->
                    <div class="row">
                        <!-- Main Content -->
                        <div class="col-lg-9">
                            <!-- App Title & Meta -->
                            <div class="d-flex align-items-center mb-3">
                                <span class="app-detail-icon">
                                    <i class="ri-calculator-line"></i>
                                </span>
                                <div>
                                    <h3 class="mb-1">Accounts Receivable Tracker</h3>
                                    <div class="text-muted mb-1">
                                        Track all your customer invoices, outstanding balances, and payment collection from a single dashboard.
                                    </div>
                                    <div class="app-meta-badges mt-1">
                                        <span class="badge bg-info">Category: Finance</span>
                                        <span class="badge bg-success">Live</span>
                                        <span class="rating-stars ms-1">
                                            ★★★★☆ <span class="text-muted">(23 reviews)</span>
                                        </span>
                                    </div>
                                </div>
                                <div class="ms-auto d-none d-lg-flex align-items-center btn-group-tight">
                                    <a href="#" class="btn btn-success btn-sm">Install</a>
                                    <a href="#" class="btn btn-outline-secondary btn-sm">Demo</a>
                                    <button class="btn btn-link btn-sm text-muted px-2"><i class="ri-heart-line"></i></button>
                                </div>
                            </div>

                            <!-- Banner/Hero -->
                            <div class="app-detail-banner mb-3">
                                <img src="https://images.unsplash.com/photo-1556740772-1a741367b93e?auto=format&fit=crop&w=900&q=80"
                                    alt="Accounts Receivable Banner"
                                    class="img-fluid mx-auto d-block" />
                            </div>

                            <!-- Notification Banner (if any) -->
                            <div class="alert alert-warning" role="alert">
                                <strong>New!</strong> Enjoy advanced aging reports and automatic follow-ups in this latest release!
                            </div>

                            <!-- Overview & Features -->
                            <div class="app-page-section mb-4">
                                <h4>Overview</h4>
                                <p>
                                    Accounts Receivable Tracker helps you monitor, organize, and follow up on all customer payments. 
                                    See at a glance which invoices are unpaid, overdue, or partially paid. Easily integrate with your invoicing, inventory, and CRM modules.
                                </p>
                                <ul>
                                    <li>Real-time dashboard for invoice statuses and outstanding balances</li>
                                    <li>Automatic aging reports (current, 30, 60, 90+ days)</li>
                                    <li>Payment history logs and custom reminders</li>
                                    <li>Bulk updates and activity tracking</li>
                                    <li>Seamless integration with other business modules</li>
                                </ul>
                                <div class="row g-2 mt-2">
                                    <div class="col-md-4">
                                        <img src="https://dummyimage.com/480x300/ededed/5a5a5a&text=AR+Tracker+Dashboard"
                                            class="img-fluid rounded shadow-sm"
                                            alt="AR Dashboard Screenshot">
                                    </div>
                                    <div class="col-md-4">
                                        <img src="https://dummyimage.com/480x300/ededed/5a5a5a&text=Aging+Report"
                                            class="img-fluid rounded shadow-sm"
                                            alt="Aging Report Screenshot">
                                    </div>
                                    <div class="col-md-4">
                                        <img src="https://dummyimage.com/480x300/ededed/5a5a5a&text=Payment+Log"
                                            class="img-fluid rounded shadow-sm"
                                            alt="Payment Log Screenshot">
                                    </div>
                                </div>
                            </div>

                            <!-- Feature Table -->
                            <div class="app-page-section mb-4">
                                <h4>Features Comparison</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered feature-check-table mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Feature</th>
                                                <th>AR Tracker</th>
                                                <th>Manual Spreadsheet</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Automatic Aging Reports</td>
                                                <td data-check="yes">✔</td>
                                                <td data-check="no">✘</td>
                                            </tr>
                                            <tr>
                                                <td>Bulk Payment Updates</td>
                                                <td data-check="yes">✔</td>
                                                <td data-check="no">✘</td>
                                            </tr>
                                            <tr>
                                                <td>Email Reminders &amp; Follow-ups</td>
                                                <td data-check="yes">✔</td>
                                                <td data-check="no">✘</td>
                                            </tr>
                                            <tr>
                                                <td>Real-Time Dashboard</td>
                                                <td data-check="yes">✔</td>
                                                <td data-check="no">✘</td>
                                            </tr>
                                            <tr>
                                                <td>Integrated with CRM/Invoices</td>
                                                <td data-check="yes">✔</td>
                                                <td data-check="no">✘</td>
                                            </tr>
                                            <tr>
                                                <td>Multi-user Access</td>
                                                <td data-check="yes">✔</td>
                                                <td data-check="yes">✔</td>
                                            </tr>
                                            <tr>
                                                <td>Export to Excel/PDF</td>
                                                <td data-check="yes">✔</td>
                                                <td data-check="no">✘</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Compatibility / Integration -->
                            <div class="app-page-section mb-4">
                                <h4>Compatibility & Integrations</h4>
                                <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                    <span class="badge bg-secondary px-3 py-2"><i class="ri-file-list-2-line me-1"></i> Invoicing Module</span>
                                    <span class="badge bg-primary px-3 py-2"><i class="ri-archive-drawer-line me-1"></i> Inventory Module</span>
                                    <span class="badge bg-warning px-3 py-2"><i class="ri-contacts-book-line me-1"></i> CRM</span>
                                    <span class="badge bg-info px-3 py-2"><i class="ri-mail-send-line me-1"></i> Email API</span>
                                </div>
                            </div>

                            <!-- Pricing Plans -->
                            <div class="app-page-section mb-4">
                                <h4>Pricing</h4>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card plan-card">
                                            <div class="card-body">
                                                <h5 class="card-title">Free</h5>
                                                <p class="mb-1">Track up to 30 active invoices and basic reports.</p>
                                                <ul class="mb-2">
                                                    <li>Unlimited users</li>
                                                    <li>Basic dashboard</li>
                                                    <li>Email support</li>
                                                </ul>
                                                <span class="fs-3 fw-bold">₱0</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card plan-card border-primary">
                                            <div class="card-body">
                                                <h5 class="card-title text-primary">Pro</h5>
                                                <p class="mb-1">Advanced aging, reminders, and CRM integration.</p>
                                                <ul class="mb-2">
                                                    <li>Automatic aging & follow-ups</li>
                                                    <li>Export to Excel/PDF</li>
                                                    <li>Priority support</li>
                                                </ul>
                                                <span class="fs-3 fw-bold">₱550/mo</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card plan-card border-warning">
                                            <div class="card-body">
                                                <h5 class="card-title text-warning">Enterprise</h5>
                                                <p class="mb-1">Unlimited accounts, custom roles, and API access.</p>
                                                <ul class="mb-2">
                                                    <li>Custom user permissions</li>
                                                    <li>API & integrations</li>
                                                    <li>Dedicated account manager</li>
                                                </ul>
                                                <span class="fs-3 fw-bold">₱3,400/mo</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reviews Section -->
                            <div class="app-page-section mb-5">
                                <h4>Reviews</h4>
                                <div class="mb-3">
                                    <span class="rating-stars">★★★★☆</span>
                                    <span class="text-muted">(23 reviews)</span>
                                </div>
                                <div class="mb-2">
                                    <div class="review-block">
                                        <div class="fw-bold">Arlene S.</div>
                                        <span class="rating-stars">★★★★★</span>
                                        <div class="text-muted small mb-1">1 day ago</div>
                                        <div>“Tracking overdue and partial payments is now so easy. Love the reminders feature!”</div>
                                    </div>
                                    <div class="review-block">
                                        <div class="fw-bold">Kevin L.</div>
                                        <span class="rating-stars">★★★★☆</span>
                                        <div class="text-muted small mb-1">5 days ago</div>
                                        <div>“Simple dashboard, fast support, and exports make reporting much easier.”</div>
                                    </div>
                                </div>
                                <a href="#" class="btn btn-outline-primary btn-sm">See all reviews</a>
                            </div>
                        </div>

                        <!-- Sidebar Widgets (right) -->
                        <div class="col-lg-3 sidebar-widgets">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title mb-2">App Info</h5>
                                    <div><strong>Developer:</strong> Flyhub Digital</div>
                                    <div><strong>Website:</strong> <a href="https://flyhubdigital.com/" target="_blank" rel="noopener">flyhubdigital.com</a></div>
                                    <div><strong>Email:</strong> <a href="mailto:support@flyhubdigital.com">support@flyhubdigital.com</a></div>
                                    <div><strong>Requires:</strong> Active Flyhub account</div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title mb-2">Resources</h5>
                                    <ul class="mb-0 ps-3">
                                        <li><a href="#">Documentation</a></li>
                                        <li><a href="#">Support Center</a></li>
                                        <li><a href="#">FAQ</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title mb-2">Other Apps by Flyhub</h5>
                                    <ul class="list-unstyled mb-0">
                                        <li><a href="#">Invoices Module</a></li>
                                        <li><a href="#">Products Kanban</a></li>
                                        <li><a href="#">Odoo Integration</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-2">Related Apps</h5>
                                    <ul class="list-unstyled mb-0">
                                        <li><a href="#">Expense Tracker</a></li>
                                        <li><a href="#">SOA Manager</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end row (main + sidebar) -->

                </div> <!-- container -->
            </div> <!-- content -->

            <?php include 'layouts/footer.php'; ?>
        </div>
        <!-- End Page Content -->

    </div>
    <!-- END wrapper -->

    <?php include 'layouts/right-sidebar.php'; ?>
    <?php include 'layouts/footer-scripts.php'; ?>
    <script src="assets/js/app.min.js"></script>
</body>
</html>
