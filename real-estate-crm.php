<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>CRM – Clients & Partners | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
</head>

<body>
<div class="wrapper">
    <?php include 'layouts/menu.php'; ?>

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <!-- PAGE TITLE & ACTIONS -->
                <div class="row mb-1">
                    <div class="col-8">
                        <div class="page-title-box">
                            <h4 class="page-title">CRM – Clients & Partners</h4>
                        </div>
                    </div>
                    <div class="col-4 text-end" style="padding-top: 30px;">
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addContactModal"><i class="ri-user-add-line"></i> Add Contact</button>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCompanyModal"><i class="ri-building-2-line"></i> Add Company</button>
                    </div>
                </div>

                <!-- CRM SUB-TABS -->
                <ul class="nav nav-tabs mb-3" id="crmTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="contacts-tab" data-bs-toggle="tab" data-bs-target="#contacts" type="button" role="tab" aria-controls="contacts" aria-selected="true">
                            Contacts / Leads
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="companies-tab" data-bs-toggle="tab" data-bs-target="#companies" type="button" role="tab" aria-controls="companies" aria-selected="false">
                            Companies / Partners
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="crmTabContent">

                    <!-- CONTACTS / LEADS TAB -->
                    <div class="tab-pane fade show active" id="contacts" role="tabpanel" aria-labelledby="contacts-tab">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" placeholder="Search contacts, buyers, brokers...">
                                    </div>
                                    <div class="col-md-8 text-end">
                                        <!-- Optional: filters by type, status, lead source, etc. -->
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Name</th>
                                                <th>Type</th>
                                                <th>Email</th>
                                                <th>Contact No.</th>
                                                <th>Status</th>
                                                <th>Lead Source</th>
                                                <th>Assigned Broker/Agent</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <a href="#" class="fw-bold text-primary" data-bs-toggle="modal" data-bs-target="#viewContactModal">Ana Del Rosario</a>
                                                </td>
                                                <td>Buyer</td>
                                                <td>ana.rosario@email.com</td>
                                                <td>0917-123-4567</td>
                                                <td><span class="badge bg-success">Active</span></td>
                                                <td>Facebook</td>
                                                <td>Ben Santos</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewContactModal"><i class="ri-eye-line"></i></button>
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"></button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item" href="#">Edit</a></li>
                                                            <li><a class="dropdown-item" href="#">Log Activity</a></li>
                                                            <li><a class="dropdown-item text-danger" href="#">Archive</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- More rows here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- COMPANIES / PARTNERS TAB -->
                    <div class="tab-pane fade" id="companies" role="tabpanel" aria-labelledby="companies-tab">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" placeholder="Search companies, partners...">
                                    </div>
                                    <div class="col-md-8 text-end"></div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Company Name</th>
                                                <th>Type</th>
                                                <th>Contact Person</th>
                                                <th>Email</th>
                                                <th>Contact No.</th>
                                                <th>Status</th>
                                                <th>Relationship</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <a href="#" class="fw-bold text-primary" data-bs-toggle="modal" data-bs-target="#viewCompanyModal">Acme Realty Inc.</a>
                                                </td>
                                                <td>Developer</td>
                                                <td>Jane Dela Cruz</td>
                                                <td>info@acmerealty.com</td>
                                                <td>02-8822-3000</td>
                                                <td><span class="badge bg-success">Active</span></td>
                                                <td>Partner</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewCompanyModal"><i class="ri-eye-line"></i></button>
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"></button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item" href="#">Edit</a></li>
                                                            <li><a class="dropdown-item" href="#">View Contracts</a></li>
                                                            <li><a class="dropdown-item text-danger" href="#">Archive</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- More rows here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> <!-- tab-content -->

                <!-- MODALS -->
                <!-- Add Contact Modal -->
                <div class="modal fade" id="addContactModal" tabindex="-1">
                  <div class="modal-dialog modal-md">
                    <div class="modal-content">
                      <form>
                        <div class="modal-header">
                          <h5 class="modal-title">Add New Contact</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <!-- Form fields here -->
                          <div class="mb-2">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control">
                          </div>
                          <div class="mb-2">
                            <label class="form-label">Type</label>
                            <select class="form-select">
                              <option>Buyer</option>
                              <option>Broker</option>
                              <option>Tenant</option>
                              <option>Agent</option>
                              <option>Other</option>
                            </select>
                          </div>
                          <div class="mb-2">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control">
                          </div>
                          <div class="mb-2">
                            <label class="form-label">Contact No.</label>
                            <input type="text" class="form-control">
                          </div>
                          <!-- Add more as needed -->
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <!-- Add Company Modal -->
                <div class="modal fade" id="addCompanyModal" tabindex="-1">
                  <div class="modal-dialog modal-md">
                    <div class="modal-content">
                      <form>
                        <div class="modal-header">
                          <h5 class="modal-title">Add New Company</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <div class="mb-2">
                            <label class="form-label">Company Name</label>
                            <input type="text" class="form-control">
                          </div>
                          <div class="mb-2">
