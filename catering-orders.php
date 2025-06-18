<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Orders & Menu | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
      .icon-action { font-size: 1.1rem; }
      .table th, .table td { vertical-align: middle !important; }
    </style>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include 'layouts/menu.php'; ?>

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">

                <div class="container-fluid">

                    <!-- PAGE TITLE & ACTIONS -->
                    <div class="row mb-1">
                        <div class="col-8">
                            <div class="page-title-box">
                                <h4 class="page-title">Orders & Menu</h4>
                            </div>
                        </div>
                        <div class="col-4 text-end" style="padding-top: 30px;">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNewOrder"><i class="ri-calendar-check-line"></i> New Booking</button>
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalNewMenu"><i class="ri-restaurant-line"></i> New Menu Item</button>
                        </div>
                    </div>

                    <!-- TABS -->
                    <ul class="nav nav-tabs mb-3" id="ordersMenuTabs" role="tablist">
                      <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab" aria-controls="orders" aria-selected="true">
                          Orders / Bookings
                        </button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button class="nav-link" id="menu-tab" data-bs-toggle="tab" data-bs-target="#menu" type="button" role="tab" aria-controls="menu" aria-selected="false">
                          Menu Catalog
                        </button>
                      </li>
                    </ul>

                    <div class="tab-content" id="ordersMenuTabsContent">
                      <!-- ORDERS TAB -->
                      <div class="tab-pane fade show active" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                        <div class="card">
                          <div class="card-body">
                            <!-- Filters -->
                            <div class="row mb-2">
                              <div class="col-md-3">
                                <input type="date" class="form-control" placeholder="Date">
                              </div>
                              <div class="col-md-3">
                                <select class="form-select">
                                  <option>All Status</option>
                                  <option>Booked</option>
                                  <option>Confirmed</option>
                                  <option>Completed</option>
                                  <option>Cancelled</option>
                                </select>
                              </div>
                              <div class="col-md-4">
                                <input type="text" class="form-control" placeholder="Search client, event...">
                              </div>
                              <div class="col-md-2 text-end">
                                <!-- empty for alignment -->
                              </div>
                            </div>
                            <!-- Table -->
                            <div class="table-responsive">
                              <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                  <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Event Name</th>
                                    <th>Package/Menu</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td><a href="#" data-bs-toggle="modal" data-bs-target="#modalOrderDetail">ORD-2024-009</a></td>
                                    <td>2024-08-15</td>
                                    <td>Acme Corp</td>
                                    <td>Seminar Lunch</td>
                                    <td>Buffet Plus</td>
                                    <td><span class="badge bg-primary">Confirmed</span></td>
                                    <td>₱15,000</td>
                                    <td>
                                      <div class="btn-group">
                                        <button class="btn btn-outline-primary btn-sm icon-action" data-bs-toggle="modal" data-bs-target="#modalOrderDetail"><i class="ri-eye-line"></i></button>
                                        <button class="btn btn-outline-secondary btn-sm icon-action"><i class="ri-edit-2-line"></i></button>
                                        <button class="btn btn-outline-danger btn-sm icon-action"><i class="ri-close-line"></i></button>
                                      </div>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td><a href="#">ORD-2024-008</a></td>
                                    <td>2024-08-10</td>
                                    <td>Beta Ltd.</td>
                                    <td>Board Meeting</td>
                                    <td>Lunch Box</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td>₱7,500</td>
                                    <td>
                                      <div class="btn-group">
                                        <button class="btn btn-outline-primary btn-sm icon-action"><i class="ri-eye-line"></i></button>
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
                      </div>
                      <!-- MENU TAB -->
                      <div class="tab-pane fade" id="menu" role="tabpanel" aria-labelledby="menu-tab">
                        <div class="card">
                          <div class="card-body">
                            <div class="row mb-2">
                              <div class="col-md-4">
                                <input type="text" class="form-control" placeholder="Search menu or package...">
                              </div>
                              <div class="col-md-4">
                                <select class="form-select">
                                  <option>All Status</option>
                                  <option>Available</option>
                                  <option>Out of Stock</option>
                                </select>
                              </div>
                            </div>
                            <div class="table-responsive">
                              <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                  <tr>
                                    <th>Menu/Package</th>
                                    <th>Type</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td>
                                      <span class="fw-semibold">Buffet Plus</span><br>
                                      <span class="small text-muted">Includes 3 viands, 1 pasta, rice, drinks</span>
                                    </td>
                                    <td>Package</td>
                                    <td>₱350/head</td>
                                    <td><span class="badge bg-success">Available</span></td>
                                    <td>
                                      <div class="btn-group">
                                        <button class="btn btn-outline-primary btn-sm icon-action" data-bs-toggle="modal" data-bs-target="#modalMenuDetail"><i class="ri-eye-line"></i></button>
                                        <button class="btn btn-outline-secondary btn-sm icon-action"><i class="ri-edit-2-line"></i></button>
                                        <button class="btn btn-outline-danger btn-sm icon-action"><i class="ri-archive-2-line"></i></button>
                                      </div>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>
                                      <span class="fw-semibold">Veggie Platter</span><br>
                                      <span class="small text-muted">Assorted fresh vegetables with dip</span>
                                    </td>
                                    <td>Ala Carte</td>
                                    <td>₱180/plate</td>
                                    <td><span class="badge bg-danger">Out of Stock</span></td>
                                    <td>
                                      <div class="btn-group">
                                        <button class="btn btn-outline-primary btn-sm icon-action" data-bs-toggle="modal" data-bs-target="#modalMenuDetail"><i class="ri-eye-line"></i></button>
                                        <button class="btn btn-outline-secondary btn-sm icon-action"><i class="ri-edit-2-line"></i></button>
                                        <button class="btn btn-outline-danger btn-sm icon-action"><i class="ri-archive-2-line"></i></button>
                                      </div>
                                    </td>
                                  </tr>
                                  <!-- Add more menu rows as needed -->
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- MODALS -->
                    <!-- Order Detail Modal -->
                    <div class="modal fade" id="modalOrderDetail" tabindex="-1">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Booking Details – ORD-2024-009</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                            <div class="row mb-2">
                              <div class="col-md-6">
                                <b>Client:</b> Acme Corp<br>
                                <b>Event Name:</b> Seminar Lunch<br>
                                <b>Date:</b> 2024-08-15<br>
                                <b>Status:</b> <span class="badge bg-primary">Confirmed</span><br>
                                <b>Location:</b> 8/F Boardroom, Acme Tower<br>
                                <b>Contact:</b> Jane Dela Cruz – 0917-123-4567
                              </div>
                              <div class="col-md-6">
                                <b>Menu/Package:</b> Buffet Plus<br>
                                <b>Notes:</b> Please prepare for 5 vegetarian guests.<br>
                                <b>Amount:</b> ₱15,000
                              </div>
                            </div>
                            <div class="table-responsive">
                              <table class="table table-bordered table-sm mb-0">
                                <thead>
                                  <tr>
                                    <th>Menu Item</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Subtotal</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td>Chicken Cordon Bleu</td>
                                    <td>30</td>
                                    <td>₱120</td>
                                    <td>₱3,600</td>
                                  </tr>
                                  <tr>
                                    <td>Spaghetti</td>
                                    <td>30</td>
                                    <td>₱80</td>
                                    <td>₱2,400</td>
                                  </tr>
                                </tbody>
                                <tfoot>
                                  <tr>
                                    <th colspan="3" class="text-end">Total</th>
                                    <th>₱15,000</th>
                                  </tr>
                                </tfoot>
                              </table>
                            </div>
                            <hr>
                            <b>Special Instructions / Attachments:</b>
                            <ul>
                              <li>Set up by 10:00am</li>
                              <li>Vegetarian meals for 5 guests</li>
                              <li><a href="#">Event Layout.pdf</a></li>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- Menu Detail Modal -->
                    <div class="modal fade" id="modalMenuDetail" tabindex="-1">
                      <div class="modal-dialog modal-md">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Menu Details – Buffet Plus</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                            <b>Type:</b> Package<br>
                            <b>Price:</b> ₱350/head<br>
                            <b>Status:</b> <span class="badge bg-success">Available</span><br>
                            <b>Description:</b>
                            <p>
                              Buffet Plus includes 3 main viands, 1 pasta, rice, dessert, and drinks.
                            </p>
                            <b>Inclusions:</b>
                            <ul>
                              <li>Chicken Cordon Bleu</li>
                              <li>Pork Barbecue</li>
                              <li>Mixed Vegetables</li>
                              <li>Spaghetti</li>
                              <li>Rice, Drinks, Dessert</li>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- New Booking Modal (Stub) -->
                    <div class="modal fade" id="modalNewOrder" tabindex="-1">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <form>
                            <div class="modal-header">
                              <h5 class="modal-title">New Booking / Event</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                              <div class="alert alert-secondary">Booking creation form goes here…</div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-primary">Save Booking</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                    <!-- New Menu Item Modal (Stub) -->
                    <div class="modal fade" id="modalNewMenu" tabindex="-1">
                      <div class="modal-dialog modal-md">
                        <div class="modal-content">
                          <form>
                            <div class="modal-header">
                              <h5 class="modal-title">New Menu Item / Package</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                              <div class="alert alert-secondary">Menu item creation form goes here…</div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-primary">Save Menu Item</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                    <!-- END MODALS -->

                </div> <!-- container-fluid -->
            </div> <!-- content -->
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

</body>
</html>
