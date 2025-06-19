<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Clients & Sites | FSM App</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
      .client-avatar {
        width: 36px; height: 36px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 0.7rem;
      }
      .add-btn { min-width: 130px; }
      .site-badge { background: #eaf1ff; color: #356ad5; font-size: 0.87rem; }
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

                <!-- Start Content-->
                <div class="container-fluid">

                    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top:24px;">
                      <h4 class="fw-bold mb-0">Clients & Sites</h4>
                      <button class="btn btn-primary add-btn" data-bs-toggle="modal" data-bs-target="#addClientModal">
                        <i class="ri-user-add-line"></i> Add Client
                      </button>
                    </div>

                    <!-- Tabs: All Clients | All Sites -->
                    <ul class="nav nav-tabs mb-3" id="clientsTab" role="tablist">
                      <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="clients-list-tab" data-bs-toggle="tab" data-bs-target="#clients-list" type="button" role="tab">Clients</button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sites-list-tab" data-bs-toggle="tab" data-bs-target="#sites-list" type="button" role="tab">Sites</button>
                      </li>
                    </ul>
                    <div class="tab-content" id="clientsTabContent">
                      <!-- Clients Tab -->
                      <div class="tab-pane fade show active" id="clients-list" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                          <div class="card-body p-0">
                            <table class="table align-middle mb-0">
                              <thead class="table-light">
                                <tr>
                                  <th>Client</th>
                                  <th>Contact</th>
                                  <th>Sites</th>
                                  <th>Email</th>
                                  <th>Phone</th>
                                  <th style="width:90px"></th>
                                </tr>
                              </thead>
                              <tbody>
                                <!-- Example Row -->
                                <tr>
                                  <td>
                                    <div class="d-flex align-items-center">
                                      <img src="assets/images/users/avatar-5.jpg" class="client-avatar" alt="">
                                      <span class="fw-semibold">Acme Corp</span>
                                    </div>
                                  </td>
                                  <td>
                                    <div>Lester Caraan<br>
                                      <small class="text-muted">Owner</small>
                                    </div>
                                  </td>
                                  <td>
                                    <span class="badge site-badge me-1">Main Office</span>
                                    <span class="badge site-badge">Warehouse</span>
                                  </td>
                                  <td>info@acme.com</td>
                                  <td>+63 912 123 4567</td>
                                  <td>
                                    <button class="btn btn-sm btn-light" title="View/Edit"><i class="ri-eye-line"></i></button>
                                  </td>
                                </tr>
                                <!-- End Example Row -->
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <!-- Sites Tab -->
                      <div class="tab-pane fade" id="sites-list" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                          <div class="card-body p-0">
                            <table class="table align-middle mb-0">
                              <thead class="table-light">
                                <tr>
                                  <th>Site Name</th>
                                  <th>Address</th>
                                  <th>Client</th>
                                  <th>Contact</th>
                                  <th style="width:90px"></th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>Main Office</td>
                                  <td>123 P. Burgos St., Makati</td>
                                  <td>Acme Corp</td>
                                  <td>Lester Caraan</td>
                                  <td>
                                    <button class="btn btn-sm btn-light"><i class="ri-eye-line"></i></button>
                                  </td>
                                </tr>
                                <!-- End Example Row -->
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Add Client Modal -->
                    <div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
                      <div class="modal-dialog">
                        <form class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="addClientModalLabel">Add New Client</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div class="mb-2">
                              <label class="form-label">Client Name</label>
                              <input type="text" class="form-control" required>
                            </div>
                            <div class="mb-2">
                              <label class="form-label">Contact Person</label>
                              <input type="text" class="form-control">
                            </div>
                            <div class="mb-2">
                              <label class="form-label">Email</label>
                              <input type="email" class="form-control">
                            </div>
                            <div class="mb-2">
                              <label class="form-label">Phone</label>
                              <input type="text" class="form-control">
                            </div>
                            <div class="mb-2">
                              <label class="form-label">Add Sites (addresses)</label>
                              <textarea class="form-control" rows="2" placeholder="Ex: 123 P. Burgos St., Makati"></textarea>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="submit" class="btn btn-primary w-100">Add Client</button>
                          </div>
                        </form>
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
