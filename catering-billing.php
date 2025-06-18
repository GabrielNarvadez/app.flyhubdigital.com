<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Billing & Receivables | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
      .table th, .table td { vertical-align: middle !important; }
      .icon-action { font-size: 1.15rem; }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'layouts/menu.php'; ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">

                    <!-- PAGE TITLE & TOP ACTIONS -->
                    <div class="row mb-1">
                        <div class="col-md-8">
                            <div class="page-title-box">
                                <h4 class="page-title">Billing & Receivables</h4>
                            </div>
                        </div>
                        <div class="col-md-4 text-end d-flex align-items-center gap-2 justify-content-end">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNewInvoice"><i class="ri-file-add-line"></i> New Invoice</button>
                            <button class="btn btn-outline-secondary">Export</button>
                        </div>
                    </div>
                    <!-- FILTERS & SEARCH -->
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <select class="form-select">
                                <option>All Statuses</option>
                                <option>Unpaid</option>
                                <option>Partial</option>
                                <option>Paid</option>
                                <option>Overdue</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" placeholder="From">
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" placeholder="To">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" placeholder="Search client, invoice, event...">
                        </div>
                    </div>

                    <!-- QUICK STATS -->
                    <div class="row mb-3 g-2">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="text-muted small"><h4>Total Receivables</h4></div>
                                    <div class="fs-5 fw-bold text-danger"></div><h3>₱120,000</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="text-muted small"><h4>Overdue</h4></div>
                                    <div class="fs-5 fw-bold text-warning"><h3>₱23,000</h3></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="text-muted small"><h4>Paid This Month</h4></div>
                                    <div class="fs-5 fw-bold text-success"><h3>₱65,000</h3></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="text-muted small"><h4>Invoices</h4></div>
                                    <div class="fs-5 fw-bold text-primary"><h3>12</h3></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MAIN TABLE -->
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Client</th>
                                            <th>Event / Order</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Due</th>
                                            <th>Balance</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><a href="#" data-bs-toggle="modal" data-bs-target="#modalViewInvoice">INV-2024-012</a></td>
                                            <td>2024-08-10</td>
                                            <td>Acme Corp</td>
                                            <td>Christmas Party</td>
                                            <td>₱45,000</td>
                                            <td><span class="badge bg-warning">Unpaid</span></td>
                                            <td>2024-08-24</td>
                                            <td>₱45,000</td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-outline-primary btn-sm icon-action" data-bs-toggle="modal" data-bs-target="#modalViewInvoice"><i class="ri-eye-line"></i></button>
                                                    <button class="btn btn-outline-success btn-sm icon-action" data-bs-toggle="modal" data-bs-target="#modalReceivePayment"><i class="ri-bank-card-line"></i></button>
                                                    <button class="btn btn-outline-secondary btn-sm icon-action"><i class="ri-send-plane-line"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">INV-2024-011</a></td>
                                            <td>2024-08-05</td>
                                            <td>Beta Ltd.</td>
                                            <td>Board Meeting</td>
                                            <td>₱12,000</td>
                                            <td><span class="badge bg-success">Paid</span></td>
                                            <td>2024-08-19</td>
                                            <td>₱0</td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-outline-primary btn-sm icon-action" data-bs-toggle="modal" data-bs-target="#modalViewInvoice"><i class="ri-eye-line"></i></button>
                                                    <button class="btn btn-outline-info btn-sm icon-action"><i class="ri-printer-line"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- Add more rows as needed -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- PAGINATION (if needed) -->
                    <div class="d-flex justify-content-end mt-3">
                        <nav>
                            <ul class="pagination pagination-sm">
                                <li class="page-item disabled"><span class="page-link">Prev</span></li>
                                <li class="page-item active"><span class="page-link">1</span></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item"><a class="page-link" href="#">Next</a></li>
                            </ul>
                        </nav>
                    </div>

                </div> <!-- container-fluid -->
            </div> <!-- content -->
            <?php include 'layouts/footer.php'; ?>
        </div>
    </div>

    <!-- VIEW INVOICE MODAL -->
    <div class="modal fade" id="modalViewInvoice" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Invoice Details – INV-2024-012</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row mb-2">
              <div class="col-md-6">
                <b>Client:</b> Acme Corp<br>
                <b>Event:</b> Christmas Party<br>
                <b>Status:</b> <span class="badge bg-warning">Unpaid</span><br>
                <b>Date Issued:</b> 2024-08-10<br>
                <b>Due Date:</b> 2024-08-24
              </div>
              <div class="col-md-6">
                <b>Total:</b> ₱45,000<br>
                <b>Paid:</b> ₱0<br>
                <b>Balance:</b> ₱45,000
              </div>
            </div>
            <hr>
            <div class="table-responsive mb-2">
              <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Lunch Buffet Package</td>
                    <td>100</td>
                    <td>₱400</td>
                    <td>₱40,000</td>
                  </tr>
                  <tr>
                    <td>Beverage Add-on</td>
                    <td>100</td>
                    <td>₱50</td>
                    <td>₱5,000</td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <th colspan="3" class="text-end">Total</th>
                    <th>₱45,000</th>
                  </tr>
                </tfoot>
              </table>
            </div>
            <b>Payment History</b>
            <div class="table-responsive">
              <table class="table table-sm table-striped mb-0">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Receipt</th>
                    <th>Notes</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="5" class="text-center text-muted">No payments yet.</td>
                  </tr>
                  <!-- Sample:
                  <tr>
                    <td>2024-08-12</td>
                    <td>₱10,000</td>
                    <td>Bank</td>
                    <td><a href="#">View</a></td>
                    <td>Downpayment</td>
                  </tr>
                  -->
                </tbody>
              </table>
            </div>
            <div class="mt-3 d-flex gap-2">
              <button class="btn btn-outline-success"><i class="ri-bank-card-line"></i> Record Payment</button>
              <button class="btn btn-outline-secondary"><i class="ri-send-plane-line"></i> Send Invoice</button>
              <button class="btn btn-outline-info"><i class="ri-printer-line"></i> Print</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- RECEIVE PAYMENT MODAL -->
    <div class="modal fade" id="modalReceivePayment" tabindex="-1">
      <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
          <form>
            <div class="modal-header">
              <h5 class="modal-title">Record Payment for INV-2024-012</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-2">
                <label class="form-label">Date</label>
                <input type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
              </div>
              <div class="mb-2">
                <label class="form-label">Amount Paid</label>
                <input type="number" class="form-control">
              </div>
              <div class="mb-2">
                <label class="form-label">Payment Method</label>
                <select class="form-select">
                  <option>Bank Deposit</option>
                  <option>Check</option>
                  <option>Online Transfer</option>
                  <option>Cash</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label">Official Receipt (optional)</label>
                <input type="file" class="form-control">
              </div>
              <div class="mb-2">
                <label class="form-label">Notes</label>
                <input type="text" class="form-control" placeholder="e.g. Downpayment, full, partial...">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Save Payment</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- NEW INVOICE MODAL (stub only) -->
    <div class="modal fade" id="modalNewInvoice" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <form>
            <div class="modal-header">
              <h5 class="modal-title">New Invoice</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <!-- Your invoice creation form here -->
              <div class="alert alert-secondary">Invoice creation form goes here…</div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Create Invoice</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <?php include 'layouts/right-sidebar.php'; ?>
    <?php include 'layouts/footer-scripts.php'; ?>
    <script src="assets/js/app.min.js"></script>
</body>
</html>
