<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Admin Dashboard | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
        /* Only minimal tweaks for icon backgrounds */
        .dashboard-app-icon {
            font-size: 2rem;
            color: #385a99;
            background: #eaf0fa;
            border-radius: .6rem;
            padding: .7rem .9rem;
            margin-bottom: .6rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include 'layouts/menu.php'; ?>

        <div class="container content-page">
            <div class="content">

                <div class="container-fluid">

                    <!-- PAGE TITLE -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-flex align-items-center justify-content-between">
                                <h4 class="page-title">Admin Dashboard</h4>
                            </div>
                        </div>
                    </div>

                    <!-- METRICS/KEY STATS -->
                    <div class="row g-3 mb-2">
                        <div class="col-md-3">
                            <div class="card text-center shadow-sm">
                                <div class="card-body">
                                    <div class="text-muted small mb-1"><h4>Units Sold (MTD)</h4></div>
                                    <div class="display-6 fw-bold text-success">18</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center shadow-sm">
                                <div class="card-body">
                                    <div class="text-muted small mb-1"><h4>Outstanding Receivables</h4></div>
                                    <div class="display-6 fw-bold text-danger">₱2,120,000</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center shadow-sm">
                                <div class="card-body">
                                    <div class="text-muted small mb-1"><h4>New Clients (This Month)</h4></div>
                                    <div class="display-6 fw-bold text-primary">6</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center shadow-sm">
                                <div class="card-body">
                                    <div class="text-muted small mb-1"><h4>Inventory</h4></div>
                                    <div>
                                        <span class="fw-bold text-success">7</span>
                                        <span class="small text-muted">Available</span> /
                                        <span class="fw-bold text-warning">16</span>
                                        <span class="small text-muted">Reserved</span> /
                                        <span class="fw-bold text-primary">49</span>
                                        <span class="small text-muted">Sold</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ALERTS & REMINDERS -->
                    <div class="row mb-3">
                        <div class="col-lg-8">
                            <div class="alert alert-warning d-flex align-items-center mb-2" role="alert">
                                <i class="ri-alert-line me-2 fs-4"></i>
                                <div>
                                    <strong>3 invoices overdue</strong> – total of ₱44,000. 
                                    <a href="#" class="fw-bold text-primary ms-2">View Invoices</a>
                                </div>
                            </div>
                            <div class="alert alert-info d-flex align-items-center mb-0" role="alert">
                                <i class="ri-calendar-event-line me-2 fs-4"></i>
                                <div>
                                    <strong>Turnover scheduled</strong> for 2 units this week.
                                    <a href="#" class="fw-bold text-primary ms-2">See Schedule</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card shadow-sm">
                                <div class="card-body p-3">
                                    <div class="fw-bold mb-2">Quick Actions</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="#" class="btn btn-outline-primary btn-sm"><i class="ri-user-add-line"></i> Add Contact</a>
                                        <a href="#" class="btn btn-outline-success btn-sm"><i class="ri-home-4-line"></i> Add Unit</a>
                                        <a href="#" class="btn btn-outline-warning btn-sm"><i class="ri-file-list-3-line"></i> New Invoice</a>
                                        <a href="#" class="btn btn-outline-info btn-sm"><i class="ri-link-unlink"></i> Import Data</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- APP MODULES -->
                    <div class="row g-3 mb-3">
                        <div class="col-12 mb-2">
                            <div class="fw-bold fs-5">Your Apps & Modules</div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card text-center shadow-sm p-3 mb-2 h-100">
                                <div class="dashboard-app-icon mb-2"><i class="ri-user-3-line"></i></div>
                                <div class="fw-semibold">Contacts</div>
                                <div class="text-muted small mb-2">Manage all client & broker contacts.</div>
                                <a href="contacts.php" class="btn btn-outline-primary btn-sm">Open</a>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card text-center shadow-sm p-3 mb-2 h-100">
                                <div class="dashboard-app-icon mb-2"><i class="ri-building-2-line"></i></div>
                                <div class="fw-semibold">Companies</div>
                                <div class="text-muted small mb-2">Manage developer & partner companies.</div>
                                <a href="companies.php" class="btn btn-outline-primary btn-sm">Open</a>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card text-center shadow-sm p-3 mb-2 h-100">
                                <div class="dashboard-app-icon mb-2"><i class="ri-home-8-line"></i></div>
                                <div class="fw-semibold">Property Inventory</div>
                                <div class="text-muted small mb-2">View & update available units & lots.</div>
                                <a href="inventory.php" class="btn btn-outline-primary btn-sm">Open</a>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card text-center shadow-sm p-3 mb-2 h-100">
                                <div class="dashboard-app-icon mb-2"><i class="ri-file-list-3-line"></i></div>
                                <div class="fw-semibold">Invoices & SOA</div>
                                <div class="text-muted small mb-2">Track billing, payments, and SOAs.</div>
                                <a href="invoices.php" class="btn btn-outline-primary btn-sm">Open</a>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card text-center shadow-sm p-3 mb-2 h-100">
                                <div class="dashboard-app-icon mb-2"><i class="ri-folder-line"></i></div>
                                <div class="fw-semibold">Document Center</div>
                                <div class="text-muted small mb-2">All contracts, forms, receipts, and docs.</div>
                                <a href="documents.php" class="btn btn-outline-primary btn-sm">Open</a>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card text-center shadow-sm p-3 mb-2 h-100">
                                <div class="dashboard-app-icon mb-2"><i class="ri-bar-chart-2-line"></i></div>
                                <div class="fw-semibold">Reports</div>
                                <div class="text-muted small mb-2">Sales, receivables, inventory & more.</div>
                                <a href="reports.php" class="btn btn-outline-primary btn-sm">Open</a>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card text-center shadow-sm p-3 mb-2 h-100">
                                <div class="dashboard-app-icon mb-2"><i class="ri-settings-3-line"></i></div>
                                <div class="fw-semibold">Settings</div>
                                <div class="text-muted small mb-2">System, branding & integrations.</div>
                                <a href="settings.php" class="btn btn-outline-primary btn-sm">Open</a>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card text-center shadow-sm p-3 mb-2 h-100">
                                <div class="dashboard-app-icon mb-2"><i class="ri-group-line"></i></div>
                                <div class="fw-semibold">Customer Portal</div>
                                <div class="text-muted small mb-2">Client-side view for your customers.</div>
                                <a href="customer-portal.php" class="btn btn-outline-primary btn-sm">Open</a>
                            </div>
                        </div>
                    </div>

                    <!-- RECENT ACTIVITY -->
                    <div class="row g-3">
                        <div class="col-lg-7 mb-3">
                            <div class="fw-bold fs-6 mb-2">Recent Activity</div>
                            <table class="table table-bordered table-sm mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Activity</th>
                                        <th>User</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>2024-08-10</td>
                                        <td>New reservation: Blk 7, Lot 13</td>
                                        <td>Jane S.</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                    </tr>
                                    <tr>
                                        <td>2024-08-09</td>
                                        <td>Payment posted: ₱22,000</td>
                                        <td>Maria T.</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                    </tr>
                                    <tr>
                                        <td>2024-08-09</td>
                                        <td>Invoice overdue: SOA-102</td>
                                        <td>System</td>
                                        <td><span class="badge bg-warning text-dark">Alert</span></td>
                                    </tr>
                                    <tr>
                                        <td>2024-08-08</td>
                                        <td>Contact added: Mike D.</td>
                                        <td>Lester</td>
                                        <td><span class="badge bg-info">New</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Shortcuts to Reports -->
                        <div class="col-lg-5">
                            <div class="fw-bold fs-6 mb-2">Reports & Tools</div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="reports.php?type=sales" class="btn btn-primary btn-sm"><i class="ri-bar-chart-box-line"></i> Sales Summary</a>
                                <a href="reports.php?type=receivables" class="btn btn-warning btn-sm"><i class="ri-coins-line"></i> Receivables Aging</a>
                                <a href="reports.php?type=inventory" class="btn btn-success btn-sm"><i class="ri-database-2-line"></i> Inventory Report</a>
                                <a href="reports.php?type=download" class="btn btn-secondary btn-sm"><i class="ri-download-2-line"></i> Download Data</a>
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
