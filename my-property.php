<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>My Property | Client Portal</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
        .property-main-card {
            background: #f6f8fb;
            border-radius: 14px;
            box-shadow: 0 2px 10px #0001;
            margin-bottom: 2rem;
        }
        .property-badge {
            font-size: 1rem;
            font-weight: 600;
            border-radius: 6px;
            padding: 4px 14px;
        }
        .property-section-title {
            font-size: 1.08rem;
            font-weight: 700;
            color: #254680;
            margin-bottom: 1rem;
        }
        .card-property {
            border-radius: 12px;
            box-shadow: 0 2px 8px #0001;
        }
        .activity-dot {
            width:12px;height:12px;background:#385a99;border-radius:50%;display:inline-block;margin-right:7px;
        }
        .agent-avatar {width:46px;height:46px;object-fit:cover;border-radius:50%;}
    </style>
</head>
<?php include 'portal-nav.php'; ?>
<body class="authentication-bg">

<?php include 'layouts/background.php'; ?>

<div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
    <div class="container">
        <div class="row">
            <!-- Property Main Card -->
            <div class="col-12">
                <div class="property-main-card p-4 d-flex flex-wrap align-items-center gap-4 justify-content-between">
                    <div class="d-flex align-items-center gap-4 flex-wrap">
                        <img src="assets/images/lot.jpg" alt="Property" style="height:70px;width:120px;object-fit:cover;border-radius:10px;">
                        <div>
                            <div class="fs-5 fw-bold mb-1">Parkside Residences</div>
                            <div class="text-muted small mb-1">
                                Blk 5, Lot 8, Sta. Maria Village, San Mateo, Rizal
                            </div>
                            <span class="property-badge bg-success-subtle text-success">Reserved</span>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-2 align-items-end">
                        <div class="fw-medium text-muted small">Turnover Date</div>
                        <div class="fw-bold text-dark">January 2025</div>
                        <a href="#" class="btn btn-outline-primary btn-sm mt-2">Download Contract</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <!-- Property Details -->
            <div class="col-lg-5">
                <div class="card card-property mb-3 p-3">
                    <div class="property-section-title">Property Details</div>
                    <div class="row mb-2">
                        <div class="col-6 mb-2">
                            <span class="fw-semibold">Lot Area:</span> 350 sqm
                        </div>
                        <div class="col-6 mb-2">
                            <span class="fw-semibold">Type:</span> Lot Only
                        </div>
                        <div class="col-6 mb-2">
                            <span class="fw-semibold">Phase:</span> 2
                        </div>
                        <div class="col-6 mb-2">
                            <span class="fw-semibold">Block:</span> 5
                        </div>
                        <div class="col-6 mb-2">
                            <span class="fw-semibold">Lot:</span> 8
                        </div>
                        <div class="col-6 mb-2">
                            <span class="fw-semibold">Orientation:</span> Corner
                        </div>
                        <div class="col-6 mb-2">
                            <span class="fw-semibold">Price/sqm:</span> ₱3,000
                        </div>
                        <div class="col-6 mb-2">
                            <span class="fw-semibold">Contract Price:</span> ₱1,050,000
                        </div>
                        <div class="col-12 mb-2">
                            <span class="fw-semibold">Reservation Paid:</span> ₱20,000
                        </div>
                    </div>
                </div>
                <div class="card card-property mb-3 p-3">
                    <div class="property-section-title">Document Center</div>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2 d-flex align-items-center gap-2">
                            <i class="ri-file-pdf-line text-danger fs-4"></i>
                            <div>
                                <div class="fw-semibold">Contract to Sell</div>
                                <a href="#" class="small text-primary">Download PDF</a>
                            </div>
                        </li>
                        <li class="mb-2 d-flex align-items-center gap-2">
                            <i class="ri-file-list-2-line text-info fs-4"></i>
                            <div>
                                <div class="fw-semibold">Reservation Agreement</div>
                                <a href="#" class="small text-primary">View</a>
                            </div>
                        </li>
                        <li class="mb-2 d-flex align-items-center gap-2">
                            <i class="ri-file-list-3-line text-success fs-4"></i>
                            <div>
                                <div class="fw-semibold">Latest SOA</div>
                                <a href="#" class="small text-primary">Download PDF</a>
                            </div>
                        </li>
                        <li class="mb-2 d-flex align-items-center gap-2">
                            <i class="ri-file-check-line text-warning fs-4"></i>
                            <div>
                                <div class="fw-semibold">Official Receipt</div>
                                <a href="#" class="small text-primary">Download</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Payment Status & Timeline -->
            <div class="col-lg-4">
                <div class="card card-property mb-3 p-3">
                    <div class="property-section-title">Payment Status</div>
                    <div class="mb-1">
                        <span class="fw-semibold">Paid:</span> <span class="fw-bold text-success">₱66,000</span> <span class="ms-1 text-muted small">(3/48 months)</span>
                    </div>
                    <div class="progress mb-2" style="height:16px;">
                        <div class="progress-bar bg-primary" style="width:20%"></div>
                    </div>
                    <div class="mb-1">
                        <span class="fw-semibold">Outstanding Balance:</span> <span class="text-danger fw-bold">₱1,088,500</span>
                    </div>
                    <div class="mb-1">
                        <span class="fw-semibold">Next Due:</span> <span class="fw-bold">₱22,000</span> <span class="ms-1">on Aug 26, 2024</span>
                    </div>
                    <div class="d-flex gap-2 mt-2">
                        <a href="#" class="btn btn-outline-primary btn-sm">View SOA</a>
                        <a href="#" class="btn btn-success btn-sm">Pay Now</a>
                    </div>
                </div>
                <div class="card card-property mb-3 p-3">
                    <div class="property-section-title">Activity Timeline</div>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><span class="activity-dot"></span> Reserved: <span class="text-muted">Aug 1, 2023</span></li>
                        <li class="mb-2"><span class="activity-dot" style="background:#43c87a"></span> First Payment: <span class="text-muted">Aug 26, 2023</span></li>
                        <li class="mb-2"><span class="activity-dot" style="background:#ffaa00"></span> Next Due: <span class="text-danger">Aug 26, 2024</span></li>
                        <li class="mb-2"><span class="activity-dot" style="background:#385a99"></span> Turnover: <span class="text-primary">Jan 2025</span></li>
                    </ul>
                </div>
            </div>

            <!-- Agent Contact & Support -->
            <div class="col-lg-3">
                <div class="card card-property mb-3 p-3">
                    <div class="property-section-title">Assigned Agent</div>
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
                <div class="card card-property p-3">
                    <div class="property-section-title">Need Help?</div>
                    <div class="mb-2 text-muted small">
                        Contact support for urgent concerns or updates to your information.
                    </div>
                    <a href="mailto:support@company.com" class="btn btn-outline-primary btn-sm w-100 mb-2"><i class="ri-mail-send-line"></i> Email Support</a>
                    <a href="#" class="btn btn-outline-secondary btn-sm w-100"><i class="ri-question-line"></i> View FAQs</a>
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
