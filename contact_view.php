<?php
require_once __DIR__ . '/layouts/config.php';
session_start();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) die('Invalid contact ID.');
$contact_id = $id;

$invoice_rows = [];
$q_invoices = mysqli_query($link, "SELECT * FROM invoices WHERE contact_id = $contact_id ORDER BY issue_date DESC, id DESC");
if ($q_invoices) {
    while ($inv = mysqli_fetch_assoc($q_invoices)) $invoice_rows[] = $inv;
}
function invoiceStatusColor($s) {
    $s = strtolower(trim($s));
    if ($s == "paid") return "text-success";
    if ($s == "unpaid") return "text-warning";
    if ($s == "overdue") return "text-danger";
    return "text-secondary";
}

// ========== AJAX HANDLER FOR COMPANY ASSOCIATION ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'associate_company') {
    $contact_id = intval($_POST['contact_id'] ?? 0);
    $company_id = intval($_POST['company_id'] ?? 0);
    if ($contact_id && $company_id) {
        $stmt = $link->prepare("INSERT IGNORE INTO contact_companies (contact_id, company_id) VALUES (?, ?)");
        $stmt->bind_param('ii', $contact_id, $company_id);
        $ok = $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => $ok ? 'success' : 'error']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        exit;
    }
}

// ========== END ASSOCIATION HANDLER ==========

// ========== AJAX HANDLER FOR CREATING & ASSOCIATING COMPANY ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_and_associate_company') {
    $contact_id = intval($_POST['contact_id'] ?? 0);
    $company_name = trim($_POST['company_name'] ?? '');
    $website_url = trim($_POST['website_url'] ?? '');
    if ($contact_id && $company_name != '') {
        // Create company
        $stmt = $link->prepare("INSERT INTO companies (company_name, website_url) VALUES (?, ?)");
        $stmt->bind_param('ss', $company_name, $website_url);
        $ok = $stmt->execute();
        $new_company_id = $stmt->insert_id;
        $stmt->close();

        if ($ok && $new_company_id) {
            // Associate company to contact
            $stmt2 = $link->prepare("INSERT IGNORE INTO contact_companies (contact_id, company_id) VALUES (?, ?)");
            $stmt2->bind_param('ii', $contact_id, $new_company_id);
            $ok2 = $stmt2->execute();
            $stmt2->close();
            echo json_encode(['status' => $ok2 ? 'success' : 'error']);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Could not create company.']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        exit;
    }
}



// ========== AJAX HANDLER FOR ADDING ACTIVITY ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_activity') {
    header('Content-Type: application/json');
    if (!isset($_SESSION['user_id']) || !$_SESSION['user_id']) {
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        exit;
    }
    $user_id = intval($_SESSION['user_id']);
    $entity_type = 'contact';
    $entity_id = intval($_POST['entity_id'] ?? 0);
    $activity_type = trim($_POST['activity_type'] ?? 'note');
    $title = trim($_POST['title'] ?? '');
    $details = trim($_POST['details'] ?? '');
    if ($entity_id <= 0 || $title == '' || $details == '') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        exit;
    }
    $stmt = $link->prepare("INSERT INTO activity_timeline (entity_type, entity_id, activity_type, title, details, user_id, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param('sisssi', $entity_type, $entity_id, $activity_type, $title, $details, $user_id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'DB error']);
    }
    exit;

// ===== [ASSOCIATION] Fetch Company for Contact =====
// ===== [ASSOCIATION] Fetch Company for Contact =====
$company = null;
if (!empty($row['company_id'])) { // was $contact, should be $row
    $company_id = intval($row['company_id']); // was $contact, should be $row
    $company_q = mysqli_query($link, "SELECT * FROM companies WHERE id = $company_id");
    $company = mysqli_fetch_assoc($company_q);
}

// ===== [END ASSOCIATION] =====


}

// ========== AJAX HANDLER FOR FETCHING TIMELINE ==========
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch_timeline'])) {
    header('Content-Type: application/json');
    $entity_type = 'contact';
    $entity_id = intval($_GET['entity_id'] ?? 0);
    $data = [];
    if ($entity_id > 0) {
        $sql = "SELECT * FROM activity_timeline WHERE entity_type=? AND entity_id=? ORDER BY created_at DESC";
        $stmt = $link->prepare($sql);
        $stmt->bind_param('si', $entity_type, $entity_id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) $data[] = $row;
        $stmt->close();
    }
    echo json_encode($data);
    exit;
}

// ========== PAGE SETUP & CONTACT FETCH ==========
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) die('Invalid contact ID.');

// Fetch contact
$q = mysqli_query($link, "SELECT * FROM contacts WHERE id = $id");
$row = mysqli_fetch_assoc($q);
if (!$row) die('Contact not found.');

// Fetch all companies associated with this contact
$companies = [];
$q_companies = mysqli_query($link, "
    SELECT c.* FROM companies c
    INNER JOIN contact_companies cc ON c.id = cc.company_id
    WHERE cc.contact_id = $id
    ORDER BY c.company_name
");

if (!$q_companies) {
    die("Query failed: " . mysqli_error($link));
}

while ($c = mysqli_fetch_assoc($q_companies)) {
    $companies[] = $c;
}


// ===== [END ASSOCIATION] =====


$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$contact_id = $id;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Contact Profile | Flyhub Business Apps</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .tab-custom .nav-link { color: #40516a; font-weight: 500; border: none; background: transparent; margin-right: 20px; padding-bottom: 8px; }
        .tab-custom .nav-link.active { color: #32475b; border-bottom: 4px solid #3d5a80; background: transparent; }
        .tab-content { background: #f7fafd; padding: 0px 0 0 0; min-height: 250px; }
        .activity-timeline .activity-card { border-radius: 12px; margin-bottom: 18px; padding: 20px 24px 16px 20px; background: #fff; box-shadow: 0 2px 8px 0 rgba(60,72,88,0.04); border-left: 5px solid #3d5a80; transition: box-shadow .2s; }
        .activity-timeline .activity-card .activity-type { font-size: 0.95rem; font-weight: 600; color: #3d5a80; text-transform: capitalize; margin-bottom: 2px; }
        .activity-timeline .activity-card .activity-title { font-weight: 500; color: #212b36; font-size: 1.09rem; }
        .activity-timeline .activity-card .activity-details { font-size: 0.98rem; color: #5d6d7e; margin-top: 6px; margin-bottom: 4px; }
        .activity-timeline .activity-card .activity-time { font-size: 0.95rem; color: #6c757d; margin-top: 0; margin-left: 12px; white-space: nowrap; }
        .accordion-button {
            font-weight: bold !important;
        }
        .invoice-list-item {
            border-bottom: 1px solid #f0f1f3;
            transition: background 0.15s;
            cursor: pointer;
            padding: 0.7em 0.5em 0.5em 0.5em;
        }
        .invoice-list-item:last-child {
            border-bottom: none;
        }
        .invoice-list-item:hover {
            background: #f8fafc;
        }
        .invoice-details {
            font-size: 0.93em;
            color: #7c8fa6;
        }
        .list-group, .list-group-flush, .list-group-flush .list-group-item {
            list-style-type: none !important;
        }
        .invoice-list-item {
            list-style-type: none !important;
        }

    </style>

</head>
<body>
<div class="wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- Contact Profile (3 columns) -->
                    <div class="page-title-box">
                        <h4 class="page-title"><a href="contacts.php">Back to Contacts</a></h4>
                    </div>
                    <div class="col-md-3 mb-3">

                        <div class="card h-100" id="profileCard">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div id="profileAvatar" class="rounded-circle bg-secondary text-white fw-bold d-flex align-items-center justify-content-center"
                                         style="width:60px;height:60px;font-size:2rem;user-select:none;cursor:pointer;">
                                        <?= strtoupper(substr($row['first_name'],0,1).substr($row['last_name'],0,1)) ?>
                                    </div>
                                        <input type="file" id="profileImageInput" accept="image/*" style="display:none;">
                                    <div class="ms-3 flex-grow-1">
                                        <span class="fw-bold fs-5" id="profileName"><?= htmlspecialchars(($row['first_name'] ?? '').' '.($row['last_name'] ?? '')) ?></span>
                                        <button class="btn btn-outline-secondary btn-sm float-end" id="editProfileBtn">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                    </div>
                                </div>
                                <div id="profileDetailsView">
                                    <p>Email: <span id="profileEmail"><?= htmlspecialchars($row['email'] ?? '') ?></span></p>
                                    <p>Phone: <span id="profilePhone"><?= htmlspecialchars($row['phone_number'] ?? '') ?></span></p>
                                    <p>Position: <span id="profilePosition"><?= htmlspecialchars($row['position'] ?? '') ?></span></p>
                                    <p>City: <span id="profileCity"><?= htmlspecialchars($row['city'] ?? '') ?></span></p>
                                    <p>Company: <span id="profileCompany"><?= htmlspecialchars($row['company_name'] ?? '') ?></span></p>
                                    <p>Type: <span id="profileType"><?= htmlspecialchars($row['contact_type'] ?? '') ?></span></p>
                                    <p>Created At: <span id="profileCreated"><?= htmlspecialchars($row['created_at'] ?? '') ?></span></p>
                                </div>
                                <form id="profileEditForm" class="d-none">
                                    <input type="hidden" name="contact_id" value="<?= $contact_id ?>">
                                    <div class="mb-2">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="first_name" value="<?= htmlspecialchars($row['first_name'] ?? '') ?>" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="last_name" value="<?= htmlspecialchars($row['last_name']) ?>" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($row['email']) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" name="phone_number" value="<?= htmlspecialchars($row['phone_number']) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Position</label>
                                        <input type="text" class="form-control" name="position" value="<?= htmlspecialchars($row['position']) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">City</label>
                                        <input type="text" class="form-control" name="city" value="<?= htmlspecialchars($row['city']) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Company Name</label>
                                        <input type="text" class="form-control" name="company_name" value="<?= htmlspecialchars($row['company_name']) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Type</label>
                                        <input type="text" class="form-control" name="contact_type" value="<?= htmlspecialchars($row['contact_type']) ?>">
                                    </div>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-light" id="cancelEditBtn">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>

                    <!-- Activity Timeline (6 columns) -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body p-0" style="padding-left:30px; padding-right:30px; height: 700px; display: flex; flex-direction: column;">
                                <h4 class="pt-3 px-3">Activity Timeline</h4>
                                <div id="activity-timeline-root" 
                                     style="flex: 1 1 auto; overflow-y: auto; min-height:0; padding: 0 16px 16px 16px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Association (3 columns) -->
                    <div class="col-md-3 mb-3">
                        <div class="card h-100">
                            <div class="card-body px-2 py-3">
                                <h4 class="mb-3">Associations</h4>
                                <div class="accordion" id="associationAccordion">

                                    <!-- ===== [ASSOCIATION] Company Card ===== -->
                                    <div class="accordion-item mb-2">
                                        <h2 class="accordion-header" id="headingCompany">
                                            <button class="accordion-button py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCompany" aria-expanded="true" aria-controls="collapseCompany">
                                                Companies
                                            </button>
                                        </h2>
                                        <div id="collapseCompany" class="accordion-collapse collapse show" aria-labelledby="headingCompany" data-bs-parent="#associationAccordion">
                                            <div class="accordion-body">
                                                <?php if (!empty($companies)): ?>
                                                    <?php foreach ($companies as $c): ?>
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span>
                                                                <strong>Company Name:</strong> <?= htmlspecialchars($c['company_name'] ?? '') ?><br>
                                                                <?php if (!empty($c['address'])): ?>
                                                                    <small>Address: <?= htmlspecialchars($c['address'] ?? '') ?></small><br>
                                                                <?php endif; ?>
                                                                <?php if (!empty($c['email'])): ?>
                                                                    <small>Email: <?= htmlspecialchars($c['email'] ?? '') ?></small><br>
                                                                <?php endif; ?>
                                                                <?php if (!empty($c['phone_number'])): ?>
                                                                    <small>Phone: <?= htmlspecialchars($c['phone_number'] ?? '') ?></small>
                                                                <?php endif; ?>
                                                            </span>
                                                            <!-- Add a remove/disassociate button here if you want -->
                                                        </div>
                                                    <?php endforeach; ?>
                                                    <button class="btn btn-outline-primary btn-sm" id="btnAddCompany" data-bs-toggle="modal" data-bs-target="#companyAssocModal">Add</button>
                                                <?php else: ?>
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span>No company associated.</span>
                                                        <button class="btn btn-outline-primary btn-sm" id="btnAddCompany" data-bs-toggle="modal" data-bs-target="#companyAssocModal">Add</button>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- ===== [END ASSOCIATION] Company Card ===== -->

                                    <!-- Invoices -->
                                    <div class="accordion-item mb-2">

                                        <h2 class="accordion-header" id="headingInvoices">
                                            <div class="d-flex align-items-center">
                                                <button class="accordion-button collapsed py-2 flex-grow-1 text-start" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseInvoices" aria-expanded="false" aria-controls="collapseInvoices">
                                                    Invoices
                                                </button>
                                            </div>
                                        </h2>
                                        <div id="collapseInvoices" class="accordion-collapse collapse" aria-labelledby="headingInvoices" data-bs-parent="#associationAccordion">
                                            <div class="accordion-body">
                                                <!-- No Add button for auto-generated -->

                                                    <div class="d-flex justify-content-end mb-2">
                                                        <a href="app-invoicing.php?contact_id=<?= $contact_id ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            </i> Create Invoice
                                                        </a>
                                                    </div>

                                        <?php if (count($invoice_rows) === 0): ?>
                                            <div class="text-center text-muted py-4 small" style="font-size:1.05rem;letter-spacing:0.01em;">
                                                No invoice issued yet.
                                            </div>
                                        <?php else: ?>
                                            <ul class="list-group list-group-flush mb-2">
                                                <?php foreach ($invoice_rows as $inv): ?>
                                                <li class="invoice-list-item" data-invoice-id="<?= $inv['id'] ?>">
                                                    <div class="fw-semibold" style="font-size:1.07rem;">
                                                        #<?= htmlspecialchars($inv['invoice_number']) ?>
                                                        <span class="ms-1"><?= '₱' . number_format((float)$inv['total'], 2) ?></span>
                                                    </div>
                                                    <div class="invoice-details">
                                                        <span class="<?= invoiceStatusColor($inv['status']) ?> fw-semibold"><?= ucfirst($inv['status']) ?></span>
                                                        <span class="text-muted ms-2">
                                                            | Issued: <?= date('M-d-Y', strtotime($inv['issue_date'])) ?>
                                                        </span>
                                                    </div>
                                                </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                        <!-- Optional: View All Invoices link -->
                                        <div class="mt-1 mb-0 ms-1">
                                            <a href="invoicing.php?contact_id=<?= $contact_id ?>"
                                               class="text-decoration-underline small text-primary"
                                               style="font-size: 0.97rem;">
                                                <i class="bi bi-box-arrow-up-right"></i> View all invoices
                                            </a>
                                        </div>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- SOA -->
                                    <div class="accordion-item mb-2">
                                        <h2 class="accordion-header" id="headingSOA">
                                            <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSOA" aria-expanded="false" aria-controls="collapseSOA">
                                                Statement of Accounts (SOA)
                                            </button>
                                        </h2>
                                        <div id="collapseSOA" class="accordion-collapse collapse" aria-labelledby="headingSOA" data-bs-parent="#associationAccordion">
                                            <div class="accordion-body">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item py-1 px-0">
                                                        <strong>#SOA-023</strong> - ₱8,000 <br>
                                                        <span class="text-success">Settled</span> | <small>Created: 2025-06-10</small>
                                                    </li>
                                                    <li class="list-group-item py-1 px-0">
                                                        <strong>#SOA-024</strong> - ₱10,500 <br>
                                                        <span class="text-danger">Overdue</span> | <small>Created: 2025-07-02</small>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bookings -->
                                    <div class="accordion-item mb-2">
                                        <h2 class="accordion-header" id="headingBookings">
                                            <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBookings" aria-expanded="false" aria-controls="collapseBookings">
                                                Bookings
                                            </button>
                                        </h2>
                                        <div id="collapseBookings" class="accordion-collapse collapse" aria-labelledby="headingBookings" data-bs-parent="#associationAccordion">
                                            <div class="accordion-body">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span><strong>Room 201</strong> | <small>From 2025-06-20 to 2025-07-10</small></span>
                                                    <button class="btn btn-outline-primary btn-sm">Add</button>
                                                </div>
                                                <div>
                                                    <small class="text-muted">Status: Reserved</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Subscriptions -->
                                    <div class="accordion-item mb-2">
                                        <h2 class="accordion-header" id="headingSubscriptions">
                                            <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSubscriptions" aria-expanded="false" aria-controls="collapseSubscriptions">
                                                Subscriptions
                                            </button>
                                        </h2>
                                        <div id="collapseSubscriptions" class="accordion-collapse collapse" aria-labelledby="headingSubscriptions" data-bs-parent="#associationAccordion">
                                            <div class="accordion-body">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span><strong>Premium Plan</strong> | <small>Active</small></span>
                                                    <button class="btn btn-outline-primary btn-sm">Add</button>
                                                </div>
                                                <div>
                                                    <small class="text-muted">Renewal: 2025-12-31</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Add more association cards as needed -->
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <?php include 'layouts/footer.php'; ?>
    </div>
    <?php include 'layouts/right-sidebar.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
    $(document).ready(function() {
        // Build the Timeline UI
        $('#activity-timeline-root').html(`
            <ul class="nav nav-tabs tab-custom mb-0" id="activityTabs" role="tablist">
                <li class="nav-item" role="presentation"><button class="nav-link active" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab">Activity</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab">Notes</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" id="emails-tab" data-bs-toggle="tab" data-bs-target="#emails" type="button" role="tab">Emails</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" id="calls-tab" data-bs-toggle="tab" data-bs-target="#calls" type="button" role="tab">Calls</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button" role="tab">Tasks</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" id="meetings-tab" data-bs-toggle="tab" data-bs-target="#meetings" type="button" role="tab">Meetings</button></li>
            </ul>
            <div class="row mb-3">
                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary create-btn" id="createBtn" style="margin-top:20px;">New Activity</button>
                </div>
            </div>
            <div class="tab-content" id="activityTabsContent">
                <div class="tab-pane fade show active" id="activity" role="tabpanel">
                    <div class="activity-timeline" id="timeline-activity"></div>
                </div>
                <div class="tab-pane fade" id="notes" role="tabpanel">
                    <div class="activity-timeline" id="timeline-notes"></div>
                </div>
                <div class="tab-pane fade" id="emails" role="tabpanel">
                    <div class="activity-timeline" id="timeline-emails"></div>
                </div>
                <div class="tab-pane fade" id="calls" role="tabpanel">
                    <div class="activity-timeline" id="timeline-calls"></div>
                </div>
                <div class="tab-pane fade" id="tasks" role="tabpanel">
                    <div class="activity-timeline" id="timeline-tasks"></div>
                </div>
                <div class="tab-pane fade" id="meetings" role="tabpanel">
                    <div class="activity-timeline" id="timeline-meetings"></div>
                </div>
            </div>
            <!-- Modal for Creating Activity -->
            <div class="modal fade" id="activityModal" tabindex="-1" aria-labelledby="activityModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <form id="activityForm" class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="activityModalLabel">New Activity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                      <input type="hidden" id="activity_type" name="activity_type" value="note">
                      <input type="hidden" name="entity_type" value="contact">
                      <input type="hidden" name="entity_id" value="<?= $contact_id ?>">
                      <div class="mb-3">
                        <label for="activity_title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="activity_title" name="title" required>
                      </div>
                      <div class="mb-3">
                        <label for="activity_details" class="form-label">Details</label>
                        <textarea class="form-control" id="activity_details" name="details" rows="4" required></textarea>
                      </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Activity</button>
                  </div>
                </form>
              </div>
            </div>
        `);

        // Tab/label logic
        const labels = {
            'activity': 'New Activity',
            'notes': 'Create Note',
            'emails': 'Log Email',
            'calls': 'Log Call',
            'tasks': 'Create Task',
            'meetings': 'Log Meeting'
        };

        function updateButtonLabel() {
            let activeTab = $('.tab-custom .nav-link.active').attr('id').replace('-tab','');
            $('#createBtn').text(labels[activeTab]);
            $('#activityModalLabel').text(labels[activeTab]);
            let atype = (activeTab === 'activity') ? 'note' : activeTab.slice(0, -1);
            $('#activity_type').val(atype);
        }
        $('.tab-custom .nav-link').on('shown.bs.tab', function () { updateButtonLabel(); });
        updateButtonLabel();

        // Fetch and render timeline
        function loadTimeline(callback) {
            $.get('', { fetch_timeline: 1, entity_id: <?= $contact_id ?> }, function(data) {
                callback(data);
            }, 'json');
        }
        function iconForType(type) {
            switch(type) {
                case 'note': return '<span class="me-2"><i class="bi bi-journal-text text-primary"></i></span>';
                case 'email': return '<span class="me-2"><i class="bi bi-envelope-at text-success"></i></span>';
                case 'call': return '<span class="me-2"><i class="bi bi-telephone text-info"></i></span>';
                case 'task': return '<span class="me-2"><i class="bi bi-list-task text-warning"></i></span>';
                case 'meeting': return '<span class="me-2"><i class="bi bi-calendar-event text-danger"></i></span>';
                case 'lifecycle_change': return '<span class="me-2"><i class="bi bi-arrow-repeat text-secondary"></i></span>';
                case 'form_submission': return '<span class="me-2"><i class="bi bi-ui-checks-grid text-secondary"></i></span>';
                default: return '<span class="me-2"><i class="bi bi-dot text-muted"></i></span>';
            }
        }
        function timelineCard(item) {
            let type = item.activity_type.replace('_', ' ');
            return `
            <div class="activity-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="activity-type">${iconForType(item.activity_type)}${type.charAt(0).toUpperCase() + type.slice(1)}</div>
                    <div class="activity-time text-end">${item.created_at ? new Date(item.created_at).toLocaleString() : ''}</div>
                </div>
                <div class="activity-title">${item.title ? item.title : ''}</div>
                <div class="activity-details">${item.details ? item.details : ''}</div>
            </div>
            `;
        }
        function renderTimeline(timelineData) {
            function render(filterType, containerId) {
                let items = (filterType === 'all') ? timelineData : timelineData.filter(item => item.activity_type === filterType);
                let html = (items.length > 0) ? items.map(timelineCard).join('') : '<div class="text-center text-muted pt-4 pb-5">No activities yet.</div>';
                $(containerId).html(html);
            }
            render('all', '#timeline-activity');
            render('note', '#timeline-notes');
            render('email', '#timeline-emails');
            render('call', '#timeline-calls');
            render('task', '#timeline-tasks');
            render('meeting', '#timeline-meetings');
        }
        function reloadTimeline() {
            loadTimeline(renderTimeline);
        }
        loadTimeline(renderTimeline);

        // Modal logic
        $('#createBtn').on('click', function() {
            $('#activity_title').val('');
            $('#activity_details').val('');
            $('#activityModal').modal('show');
            updateButtonLabel();
        });

        // Handle Activity Form Submission (AJAX)
        $('#activityForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '', // post to same page
                type: 'POST',
                data: $(this).serialize() + '&action=add_activity',
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        $('#activityModal').modal('hide');
                        reloadTimeline();
                    } else {
                        alert(res.message || 'Could not save activity. Please try again.');
                    }
                },
                error: function() {
                    alert('Error while saving. Please try again.');
                }
            });
        });
    });
    </script>
</div>

<!-- [ASSOCIATION] Company: Modal for Add/Change -->
<div class="modal fade" id="companyAssocModal" tabindex="-1" aria-labelledby="companyAssocModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="companyAssocModalLabel">Associate Company</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs mb-3" id="companyAssocTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="select-tab" data-bs-toggle="tab" data-bs-target="#select-company" type="button" role="tab">Select Existing</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="create-tab" data-bs-toggle="tab" data-bs-target="#create-company" type="button" role="tab">Create New</button>
          </li>
        </ul>
        <div class="tab-content" id="companyAssocTabsContent">
          <!-- TAB 1: Select Company -->
          <div class="tab-pane fade show active" id="select-company" role="tabpanel">
            <form id="formAssociateCompany">
              <div class="mb-3">
                <label for="company_select" class="form-label">Choose company to associate:</label>
                <select class="form-select" id="company_select" name="company_id" required>
                  <option value="">-- Select company --</option>
                  <?php
                  // Fetch all companies for dropdown
                  $all_companies = [];
                  $q_c = mysqli_query($link, "SELECT id, company_name FROM companies ORDER BY company_name ASC");
                  while ($c = mysqli_fetch_assoc($q_c)) {
                      $all_companies[] = $c;
                  }
                  foreach ($all_companies as $c) {
                      echo '<option value="'.(int)$c['id'].'">'.htmlspecialchars($c['company_name'] ?? '').'</option>';
                  }
                  ?>
                </select>
              </div>
              <input type="hidden" name="action" value="associate_company">
              <input type="hidden" name="contact_id" value="<?= $contact_id ?>">
              <button type="submit" class="btn btn-primary">Associate</button>
            </form>
          </div>
          <!-- TAB 2: Create New Company -->
          <div class="tab-pane fade" id="create-company" role="tabpanel">
            <form id="formCreateCompany">
              <div class="mb-3">
                <label class="form-label">Company Name <span class="text-danger">*</span></label>
                <input type="text" name="company_name" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Website URL</label>
                <input type="text" name="website_url" class="form-control" placeholder="https://example.com">
              </div>
              <div class="mb-2 text-muted" style="font-size: 0.97em;">
                <i class="bi bi-info-circle"></i>
                You can update other company details later.
              </div>
              <input type="hidden" name="action" value="create_and_associate_company">
              <input type="hidden" name="contact_id" value="<?= $contact_id ?>">
              <button type="submit" class="btn btn-success">Create & Associate</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- [END ASSOCIATION] Company Modal -->

<script>
$(function() {
    // Submit: Associate Existing Company
    $('#formAssociateCompany').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        $.post('', $form.serialize(), function(res) {
            if (res.status === 'success') {
                location.reload();
            } else {
                alert('Failed to associate company.');
            }
        }, 'json');
    });
    // Submit: Create & Associate New Company
    $('#formCreateCompany').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        $.post('', $form.serialize(), function(res) {
            if (res.status === 'success') {
                location.reload();
            } else {
                alert('Failed to create or associate company.');
            }
        }, 'json');
    });
    // Reset forms when modal opens
    $('#companyAssocModal').on('show.bs.modal', function() {
        $('#formAssociateCompany')[0].reset();
        $('#formCreateCompany')[0].reset();
    });


    // Click on avatar opens file dialog
    $('#profileAvatar').on('click', function() {
        $('#profileImageInput').click();
    });

    // On file select, upload via AJAX
    $('#profileImageInput').on('change', function(){
        var formData = new FormData();
        formData.append('contact_id', <?= $contact_id ?>); // PHP echo for current contact
        formData.append('profile_image', this.files[0]);
        $.ajax({
            url: 'upload-profile.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp) {
                try {
                    var data = JSON.parse(resp);
                    if (data.status === 'success') {
                        location.reload();
                    } else {
                        alert(data.message || 'Upload failed.');
                    }
                } catch(e) { alert('Upload error.'); }
            },
            error: function() { alert('Failed to upload image.'); }
        });
    });

    $(function() {
        // Show edit form, hide details
        $('#editProfileBtn').on('click', function() {
            $('#profileDetailsView').addClass('d-none');
            $('#editProfileBtn').addClass('d-none');
            $('#profileEditForm').removeClass('d-none');
        });
        // Cancel edit
        $('#cancelEditBtn').on('click', function() {
            $('#profileEditForm').addClass('d-none');
            $('#profileDetailsView').removeClass('d-none');
            $('#editProfileBtn').removeClass('d-none');
        });

        // AJAX submit
        $('#profileEditForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'update-contact.php', // create this backend script!
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    if(res.status === 'success') {
                        // Optionally: fetch new data via AJAX or just update values in place
                        location.reload();
                    } else {
                        alert(res.message || 'Failed to save');
                    }
                },
                error: function() {
                    alert('Save failed. Try again.');
                }
            });
        });
    });


});
</script>
<!-- App js -->
<script src="assets/js/app.min.js"></script>
<script>
$(function() {
    // Click on avatar opens file dialog
    $('#profileAvatar').on('click', function() {
        $('#profileImageInput').click();
    });

    // On file select, upload via AJAX
    $('#profileImageInput').on('change', function(){
        var formData = new FormData();
        formData.append('contact_id', <?= $contact_id ?>);
        formData.append('profile_image', this.files[0]);
        $.ajax({
            url: 'upload-profile.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp) {
                try {
                    var data = JSON.parse(resp);
                    if (data.status === 'success') {
                        location.reload();
                    } else {
                        alert(data.message || 'Upload failed.');
                    }
                } catch(e) { alert('Upload error.'); }
            },
            error: function() { alert('Failed to upload image.'); }
        });
    });

});

</script>

</body>
</html>
