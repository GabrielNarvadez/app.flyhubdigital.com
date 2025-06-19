<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Company Profile | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .tab-custom .nav-link { color: #40516a; font-weight: 500; border: none; background: transparent; margin-right: 20px; padding-bottom: 8px; }
        .tab-custom .nav-link.active { color: #32475b; border-bottom: 4px solid #3d5a80; background: transparent; }
        .tab-content { background: #f7fafd; padding: 0px 0 0 0; min-height: 250px; }
        .activity-timeline .activity-card { border-radius: 12px; margin-bottom: 18px; padding: 20px 24px 16px 20px; background: #fff; box-shadow: 0 2px 8px 0 rgba(60,72,88,0.04); border-left: 5px solid #3d5a80; transition: box-shadow .2s; }
        .activity-timeline .activity-card .activity-type { font-size: 0.95rem; font-weight: 600; color: #3d5a80; text-transform: capitalize; margin-bottom: 2px; }
        .activity-timeline .activity-card .activity-title { font-weight: 500; color: #212b36; font-size: 1.09rem; }
        .activity-timeline .activity-card .activity-details { font-size: 0.98rem; color: #5d6d7e; margin-top: 6px; margin-bottom: 4px; }
        .activity-timeline .activity-card .activity-time { font-size: 0.95rem; color: #6c757d; margin-top: 0; margin-left: 12px; white-space: nowrap; }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="page-title-box">
                        <a href="companies.php" class="btn btn-outline-secondary btn-sm" style="margin: 25px 0;">
                            <i class="ri-arrow-go-back-line"></i> Back to Companies
                        </a>
                    </div>
                    <!-- Profile Card (3 columns) -->
                    <div class="col-md-3 mb-3">
                        <div class="card h-100" id="profileCard">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div id="profileAvatar" class="rounded-circle bg-secondary text-white fw-bold d-flex align-items-center justify-content-center"
                                         style="width:60px;height:60px;font-size:2rem;user-select:none;cursor:pointer;">
                                        AC
                                    </div>
                                    <input type="file" id="profileImageInput" accept="image/*" style="display:none;">
                                    <div class="ms-3 flex-grow-1">
                                        <span class="fw-bold fs-5" id="profileName">Acme Corporation</span>
                                        <button class="btn btn-outline-secondary btn-sm float-end" id="editProfileBtn">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                    </div>
                                </div>
                                <div id="profileDetailsView">
                                    <p>Website: <span id="profileWebsite">https://acme.com</span></p>
                                    <p>Email: <span id="profileEmail">info@acme.com</span></p>
                                    <p>Phone: <span id="profilePhone">+63 912 345 6789</span></p>
                                    <p>Address: <span id="profileAddress">Makati City, PH</span></p>
                                    <p>Industry: <span id="profileIndustry">Retail</span></p>
                                    <p>Type: <span id="profileType">Customer</span></p>
                                    <p>Created At: <span id="profileCreated">2024-07-01</span></p>
                                    <p>Notes: <span id="profileNotes">Top client for 2024.</span></p>
                                </div>
                                <!-- Inline edit form (hidden by default) -->
                                <form id="profileEditForm" class="d-none">
                                    <!-- Use your form here if needed -->
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Timeline (6 columns) -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body p-0" style="padding-left:30px; padding-right:30px; height: 700px; display: flex; flex-direction: column;">
                                <h4 class="pt-3 px-3">Activity Timeline</h4>
                                <div id="activity-timeline-root"
                                     style="flex: 1 1 auto; overflow-y: auto; min-height:0; padding: 0 16px 16px 16px;">
                                    <ul class="nav nav-tabs tab-custom mb-0" id="activityTabs" role="tablist">
                                        <li class="nav-item" role="presentation"><button class="nav-link active" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab">Activity</button></li>
                                        <li class="nav-item" role="presentation"><button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab">Notes</button></li>
                                        <li class="nav-item" role="presentation"><button class="nav-link" id="emails-tab" data-bs-toggle="tab" data-bs-target="#emails" type="button" role="tab">Emails</button></li>
                                        <li class="nav-item" role="presentation"><button class="nav-link" id="calls-tab" data-bs-toggle="tab" data-bs-target="#calls" type="button" role="tab">Calls</button></li>
                                        <li class="nav-item" role="presentation"><button class="nav-link" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button" role="tab">Tasks</button></li>
                                        <li class="nav-item" role="presentation"><button class="nav-link" id="meetings-tab" data-bs-toggle="tab" data-bs-target="#meetings" type="button" role="tab">Meetings</button></li>
                                    </ul>
                                    <div class="tab-content" id="activityTabsContent">
                                        <div class="tab-pane fade show active" id="activity" role="tabpanel">
                                            <div class="activity-timeline" id="timeline-activity">
                                                <div class="activity-card">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div class="activity-type"><span class="me-2"><i class="bi bi-journal-text text-primary"></i></span>Note</div>
                                                        <div class="activity-time text-end">2024-07-31 09:00</div>
                                                    </div>
                                                    <div class="activity-title">Initial meeting</div>
                                                    <div class="activity-details">Discussed 2025 contract renewal.</div>
                                                </div>
                                                <div class="activity-card">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div class="activity-type"><span class="me-2"><i class="bi bi-envelope-at text-success"></i></span>Email</div>
                                                        <div class="activity-time text-end">2024-07-25 14:15</div>
                                                    </div>
                                                    <div class="activity-title">Follow-up sent</div>
                                                    <div class="activity-details">Sent draft quotation via email.</div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- ...repeat structure for other tabs as needed... -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Associations (3 columns) -->
                    <div class="col-md-3 mb-3">
                        <div class="card h-100">
                            <div class="card-body px-2 py-3">
                                <h4 class="mb-3">Associations</h4>
                                <div class="accordion" id="associationAccordion">
                                    <!-- Contacts -->
                                    <div class="accordion-item mb-2">
                                        <h2 class="accordion-header" id="headingContacts">
                                            <button class="accordion-button py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseContacts" aria-expanded="true" aria-controls="collapseContacts">
                                                Contacts
                                            </button>
                                        </h2>
                                        <div id="collapseContacts" class="accordion-collapse collapse show" aria-labelledby="headingContacts" data-bs-parent="#associationAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-2">
                                                    <strong><a href="contact_view.php?id=1">Jane Dela Cruz</a></strong><br>
                                                    <small>jane@email.com</small>
                                                </div>
                                                <button class="btn btn-outline-primary btn-sm mt-2" id="btnAddContact">Add Contact</button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Deals -->
                                    <div class="accordion-item mb-2">
                                        <h2 class="accordion-header" id="headingDeals">
                                            <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDeals" aria-expanded="false" aria-controls="collapseDeals">
                                                Deals
                                            </button>
                                        </h2>
                                        <div id="collapseDeals" class="accordion-collapse collapse" aria-labelledby="headingDeals" data-bs-parent="#associationAccordion">
                                            <div class="accordion-body">
                                                <ul class="list-group list-group-flush mb-2">
                                                    <li class="list-group-item px-0 py-1">
                                                        <strong>Website Revamp</strong>
                                                        <span class="ms-1 text-success">₱250,000.00</span><br>
                                                        <span class="text-muted small">Proposal | Open</span>
                                                    </li>
                                                </ul>
                                                <a href="deals.php?company_id=1" class="btn btn-outline-success btn-sm mt-2">Add Deal</a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Invoices -->
                                    <div class="accordion-item mb-2">
                                        <h2 class="accordion-header" id="headingInvoices">
                                            <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseInvoices" aria-expanded="false" aria-controls="collapseInvoices">
                                                Invoices
                                            </button>
                                        </h2>
                                        <div id="collapseInvoices" class="accordion-collapse collapse" aria-labelledby="headingInvoices" data-bs-parent="#associationAccordion">
                                            <div class="accordion-body">
                                                <ul class="list-group list-group-flush mb-2">
                                                    <li class="list-group-item px-0 py-1">
                                                        <strong>#INV-00123</strong> - ₱99,000.00
                                                        <span class="ms-1 text-success">Paid</span>
                                                    </li>
                                                </ul>
                                                <a href="app-invoicing.php?company_id=1" class="btn btn-outline-primary btn-sm mt-2">Create Invoice</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end associations -->
                </div>
            </div>
        </div>
        <?php include 'layouts/footer.php'; ?>
    </div>
    <?php include 'layouts/right-sidebar.php'; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
