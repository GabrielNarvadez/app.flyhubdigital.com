<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>My Invoices | Client Portal</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
        .invoice-summary-card {
            background: #f6f8fb;
            border-radius: 12px;
            box-shadow: 0 2px 12px #0001;
            padding: 1.3rem 1.3rem 1rem 1.3rem;
            text-align: center;
        }
        .invoice-summary-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: #254680;
            margin-bottom: .3rem;
        }
        .invoice-summary-value {
            font-size: 2rem;
            font-weight: 700;
            color: #385a99;
            line-height: 1.1;
        }
        .card-invoice-table {
            border-radius: 12px;
            box-shadow: 0 2px 8px #0001;
            background: #fff;
        }
        .status-badge {
            padding: 5px 12px;
            font-size: .97rem;
            border-radius: 16px;
            font-weight: 600;
        }
        .status-paid {
            background: #eafae6;
            color: #4ca944;
        }
        .status-unpaid {
            background: #fffbe6;
            color: #a48415;
        }
        .status-overdue {
            background: #ffeaea;
            color: #bb2525;
        }
        .status-partial {
            background: #e8eafd;
            color: #204094;
        }
        .table-invoices th, .table-invoices td {
            vertical-align: middle !important;
        }
        .table-invoices tr:hover {
            background: #f3f6fd;
            cursor: pointer;
        }
        .download-link {
            font-size: 1.02rem;
        }
        @media (max-width: 991px) {
            .invoice-summary-value { font-size: 1.3rem; }
        }
    </style>
</head>
<?php include 'portal-nav.php'; ?>
<body class="authentication-bg">

<?php include 'layouts/background.php'; ?>

<div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
    <div class="container">

        <!-- Summary Cards -->
        <div class="row g-3 mb-3">
            <div class="col-6 col-md-3">
                <div class="invoice-summary-card">
                    <div class="invoice-summary-title">Total Paid</div>
                    <div class="invoice-summary-value text-success">₱66,000</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="invoice-summary-card">
                    <div class="invoice-summary-title">Outstanding</div>
                    <div class="invoice-summary-value text-danger">₱44,000</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="invoice-summary-card">
                    <div class="invoice-summary-title">Next Due</div>
                    <div class="invoice-summary-value">₱22,000</div>
                    <div class="text-muted small">Aug 26, 2024</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="invoice-summary-card">
                    <div class="invoice-summary-title">Payments Made</div>
                    <div class="invoice-summary-value">3 / 48</div>
                    <div class="progress" style="height:7px;">
                        <div class="progress-bar bg-primary" style="width:6%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Table Card -->
        <div class="card card-invoice-table p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
                <div>
                    <h4 class="mb-0">My Invoices & Statements</h4>
                    <div class="text-muted small">View all your SOA, invoices, and payment records.</div>
                </div>
                <form class="d-flex align-items-center gap-2">
                    <input type="text" class="form-control form-control-sm" style="width:180px;" placeholder="Search...">
                    <select class="form-select form-select-sm" style="width:120px;">
                        <option value="">All Status</option>
                        <option value="Paid">Paid</option>
                        <option value="Unpaid">Unpaid</option>
                        <option value="Overdue">Overdue</option>
                        <option value="Partial">Partial</option>
                    </select>
                    <button class="btn btn-outline-secondary btn-sm" type="button"><i class="ri-search-line"></i></button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-invoices table-bordered table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>SOA/Invoice #</th>
                            <th>Issue Date</th>
                            <th>Due Date</th>
                            <th>Amount Due</th>
                            <th>Amount Paid</th>
                            <th>Status</th>
                            <th>Download/View</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2024-06</td>
                            <td>06/05/2024</td>
                            <td>06/26/2024</td>
                            <td>₱22,000</td>
                            <td>₱22,000</td>
                            <td><span class="status-badge status-paid">Paid</span></td>
                            <td>
                                <a href="#" class="download-link text-primary"><i class="ri-file-pdf-line"></i> PDF</a>
                            </td>
                        </tr>
                        <tr>
                            <td>2024-07</td>
                            <td>07/05/2024</td>
                            <td>07/26/2024</td>
                            <td>₱22,000</td>
                            <td></td>
                            <td><span class="status-badge status-unpaid">Unpaid</span></td>
                            <td>
                                <a href="#" class="download-link text-primary"><i class="ri-file-pdf-line"></i> PDF</a>
                            </td>
                        </tr>
                        <tr>
                            <td>2024-08</td>
                            <td>08/05/2024</td>
                            <td>08/26/2024</td>
                            <td>₱22,000</td>
                            <td></td>
                            <td><span class="status-badge status-overdue">Overdue</span></td>
                            <td>
                                <a href="#" class="download-link text-primary"><i class="ri-file-pdf-line"></i> PDF</a>
                                <a href="#" class="btn btn-sm btn-success ms-2">Pay Now</a>
                            </td>
                        </tr>
                        <tr>
                            <td>2024-09</td>
                            <td>09/05/2024</td>
                            <td>09/26/2024</td>
                            <td>₱22,000</td>
                            <td>₱10,000</td>
                            <td><span class="status-badge status-partial">Partial</span></td>
                            <td>
                                <a href="#" class="download-link text-primary"><i class="ri-file-pdf-line"></i> PDF</a>
                            </td>
                        </tr>
                        <!-- more rows as needed -->
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex flex-wrap align-items-center gap-2">
                <span class="text-muted small">Need help? <a href="mailto:support@company.com" class="text-primary">Contact support</a></span>
            </div>
        </div>

        <!-- Optionally: FAQ/help section at the bottom -->
        <div class="row mt-4">
            <div class="col-lg-7 mb-3">
                <div class="alert alert-info border-0 mb-0">
                    <strong>Need to pay online?</strong> Click "Pay Now" on any unpaid invoice or email <a href="mailto:billing@company.com">billing@company.com</a> for other options.
                </div>
            </div>
            <div class="col-lg-5">
                <div class="alert alert-secondary border-0 mb-0">
                    <strong>Questions?</strong> See our <a href="#" class="text-primary">billing FAQ</a> or chat with your agent.
                </div>
            </div>
        </div>

    </div><!-- end container -->
</div><!-- end account-pages -->

<footer class="footer footer-alt fw-medium">
    <span class="bg-body"><script>document.write(new Date().getFullYear())</script> © Flyhub Digital Inc.</span>
</footer>

<?php include 'layouts/footer-scripts.php'; ?>
<script src="assets/js/app.min.js"></script>
</body>
</html>
