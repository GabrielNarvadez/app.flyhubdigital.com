<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Dashboard | Client Portal</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
        .dashboard-welcome-card {
            background: #f6f8fb;
            border-radius: 12px;
            box-shadow: 0 2px 12px #0001;
        }
        .dashboard-property-status {
            font-size: 1.15rem;
            font-weight: 600;
        }
        .dashboard-progress-bar {
            height: 18px;
            background: #eaf0fa;
            border-radius: 8px;
        }
        .dashboard-section-title {
            font-size: 1.08rem;
            font-weight: 700;
            color: #254680;
            margin-bottom: .9rem;
        }
        .agent-avatar {
            width:48px;height:48px;object-fit:cover;border-radius:50%;
        }
        .card-dashboard {
            border-radius: 12px;
            box-shadow: 0 2px 8px #0001;
        }
    </style>
</head>
<?php include 'portal-nav.php'; ?>
<body class="authentication-bg">

<?php include 'layouts/background.php'; ?>

<div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
    <div class="container">
        <div class="row g-4 align-items-stretch">

            <!-- WELCOME + PROPERTY SNAPSHOT -->
            <div class="col-12">
                <div class="dashboard-welcome-card p-4 mb-2 d-flex align-items-center justify-content-between flex-wrap gap-4">
                    <div>
                        <h2 class="mb-1">Welcome back, <span class="text-primary">Lester!</span></h2>
                        <div class="mb-1 text-muted">Your property at <span class="fw-bold text-dark">Parkside Residences</span>:</div>
                        <div class="dashboard-property-status badge bg-success-subtle text-success px-3 py-2">Reserved</div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <img src="assets/images/lot.jpg" alt="Property" style="height:60px;width:100px;object-fit:cover;border-radius:8px;">
                        <div class="text-end">
                            <div class="fw-semibold text-dark">Blk 5, Lot 8</div>
                            <div class="text-muted small">Sta. Maria Village, San Mateo, Rizal</div>
                            <a href="my-property.php" class="btn btn-sm btn-outline-primary mt-2">View Property Details</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PAYMENT PROGRESS + NEXT DUE -->
            <div class="col-lg-6">
                <div class="card card-dashboard mb-3 p-3">
                    <div class="dashboard-section-title">Payment Progress</div>
                    <div class="mb-2">
                        <span class="fw-semibold">Total Paid:</span>
                        <span class="fs-5 fw-bold text-success">₱66,000</span>
                        <span class="ms-2 text-muted">(3/48 months)</span>
                    </div>
                    <div class="dashboard-progress-bar mb-2">
                        <div style="width:20%;background:#385a99;height:100%;border-radius:8px;"></div>
                    </div>
                    <div class="d-flex flex-wrap gap-4 align-items-center mb-1">
                        <div>
                            <span class="fw-semibold">Outstanding Balance:</span> <span class="text-danger fw-bold">₱1,088,500</span>
                        </div>
                        <div>
                            <span class="fw-semibold">Next Payment:</span>
                            <span class="text-dark">Aug 26, 2024</span>
                            <span class="text-primary fw-bold ms-1">₱22,000</span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="my-invoices.php" class="btn btn-outline-primary btn-sm">View Payment Schedule</a>
                        <a href="#" class="btn btn-success btn-sm ms-1">Pay Now</a>
                    </div>
                </div>
            </div>

            <!-- AMORTIZATION SCHEDULE (SUMMARY) -->
            <div class="col-lg-6">
                <div class="card card-dashboard mb-3 p-3">
                    <div class="dashboard-section-title">Amortization Schedule</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-2">
                            <thead class="table-light">
                                <tr>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2024-06-26</td>
                                    <td>₱22,000</td>
                                    <td><span class="badge bg-success">Paid</span></td>
                                </tr>
                                <tr>
                                    <td>2024-07-26</td>
                                    <td>₱22,000</td>
                                    <td><span class="badge bg-success">Paid</span></td>
                                </tr>
                                <tr>
                                    <td>2024-08-26</td>
                                    <td>₱22,000</td>
                                    <td><span class="badge bg-warning text-dark">Due</span></td>
                                </tr>
                            </tbody>
                        </table>
                        <a href="my-invoices.php" class="btn btn-outline-primary btn-sm">See Full Statement</a>
                    </div>
                </div>
            </div>

            <!-- RECENT DOCUMENTS -->
            <div class="col-lg-4">
                <div class="card card-dashboard mb-3 p-3 h-100">
                    <div class="dashboard-section-title">Recent Documents</div>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2 d-flex align-items-center gap-2">
                            <i class="ri-file-pdf-line text-danger fs-4"></i>
                            <div>
                                <div class="fw-semibold">SOA - Jun 2024</div>
                                <a href="#" class="small text-primary">Download PDF</a>
                            </div>
                        </li>
                        <li class="mb-2 d-flex align-items-center gap-2">
                            <i class="ri-file-pdf-line text-danger fs-4"></i>
                            <div>
                                <div class="fw-semibold">Official Receipt #101</div>
                                <a href="#" class="small text-primary">Download PDF</a>
                            </div>
                        </li>
                        <li class="mb-2 d-flex align-items-center gap-2">
                            <i class="ri-file-text-line text-info fs-4"></i>
                            <div>
                                <div class="fw-semibold">Contract to Sell</div>
                                <a href="#" class="small text-primary">View Document</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- REMINDERS / ALERTS -->
            <div class="col-lg-4">
                <div class="card card-dashboard mb-3 p-3 h-100">
                    <div class="dashboard-section-title">Reminders & Alerts</div>
                    <ul class="mb-2 ps-3">
                        <li>Next payment due in <span class="fw-bold text-danger">8 days</span>.</li>
                        <li>Please submit post-dated checks to your agent.</li>
                        <li>Turnover scheduled for <span class="fw-bold">Jan 2025</span>.</li>
                        <li>Contact us if you need to update your information.</li>
                    </ul>
                </div>
            </div>

            <!-- SUPPORT / AGENT CONTACT -->
            <div class="col-lg-4">
                <div class="card card-dashboard mb-3 p-3 h-100">
                    <div class="dashboard-section-title">Your Agent</div>
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <img src="assets/images/users/lester.png" class="agent-avatar" alt="Agent">
                        <div>
                            <div class="fw-semibold">Lester Caraan</div>
                            <div class="small text-muted">Relationship Manager</div>
                            <div class="small text-muted"><i class="ri-phone-line"></i> 0918 123 4567</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="mailto:liza@email.com" class="btn btn-outline-primary btn-sm"><i class="ri-mail-line"></i> Email</a>
                        <a href="tel:09181234567" class="btn btn-outline-success btn-sm"><i class="ri-phone-line"></i> Call</a>
                        <a href="#" class="btn btn-outline-info btn-sm"><i class="ri-chat-3-line"></i> Chat</a>
                    </div>
                </div>
            </div>

        </div><!-- end row -->
    </div><!-- end container -->
</div><!-- end account-pages -->

<footer class="footer footer-alt fw-medium">
    <span class="bg-body"><script>document.write(new Date().getFullYear())</script> © Flyhub Digital Inc.</span>
</footer>

<?php include 'layouts/footer-scripts.php'; ?>
<script src="assets/js/app.min.js"></script>
</body>
</html>
