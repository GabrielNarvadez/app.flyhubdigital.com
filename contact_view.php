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

        /* Timeline notification style */
        .timeline-list {
            list-style: none;
            margin: 0;
            padding: 5px 40px;
        }

        .timeline-item {
            display: flex;
            align-items: flex-start;
            padding: 16px 0;
            border-bottom: 1px solid #f0f1f3;
        }

        .timeline-item:last-child {
            border-bottom: none;
        }

        .timeline-icon {
            flex-shrink: 0;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35em;
            margin-right: 14px;
            margin-top: 2px;
        }

        .timeline-content {
            flex: 1;
            min-width: 0;
        }

        .timeline-title {
            font-weight: 600;
            color: #2474e5;
            font-size: 1.05em;
            margin-bottom: 2px;
            display: block;
            text-decoration: none;
        }

        .timeline-message {
            color: #444;
            font-size: 0.97em;
            margin-bottom: 2px;
            word-break: break-word;
        }

        .timeline-time {
            color: #a0a0a0;
            font-size: 0.93em;
            margin-top: 1px;
            font-weight: 400;
        }
        /* Make the tab headers sticky inside the timeline card */
        #activity-timeline-tabs-sticky {
            position: sticky;
            top: 0;
            background: #f7fafd;
            border-bottom: 1px solid #ececec;
            z-index: 2;
            /* padding-bottom: 5px; */
        }

        /* Scrollable area stretches to card bottom (no gap) */
        #activity-timeline-scrollable {
            height: 100%;
            min-height: 0;
            max-height: none;
            padding: 0;
            margin: 0;
        }

        /* Timeline activity item layout */
        .timeline-list {
            list-style: none;
            margin: 0;
            padding: 5px 24px 40px 24px;
        }
        .timeline-item {
            display: flex;
            align-items: flex-start;
            padding: 20px 0 16px 0;
            border-bottom: 1px solid #f0f1f3;
            position: relative;
        }
        .timeline-item:last-child {
            border-bottom: none;
        }
        .timeline-icon {
            flex-shrink: 0;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35em;
            margin-right: 14px;
            margin-top: 2px;
        }
        .timeline-content {
            flex: 1;
            min-width: 0;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        .timeline-title {
            font-weight: 600;
            color: #2474e5;
            font-size: 1.05em;
            margin-bottom: 2px;
            display: block;
            text-decoration: none;
        }
        .timeline-message {
            color: #444;
            font-size: 0.97em;
            margin-bottom: 2px;
            word-break: break-word;
        }
        /* Date & Time top-right */
        .timeline-time {
            color: #a0a0a0;
            font-size: 0.93em;
            font-weight: 400;
            position: absolute;
            top: 0;
            right: 0;
            margin: 0;
            padding: 0;
        }
        .tab-custom .nav-link {
            color: #40516a;
            font-weight: 500;
            border: none;
            background: #fff;     /* was transparent */
            margin-right: 20px;
            padding-bottom: 8px;
        }
        .tab-custom .nav-link.active {
            color: #32475b;
            border-bottom: 4px solid #3d5a80;
            background: #fff;     /* was transparent */
        }
        #activity-timeline-tabs-sticky {
            position: sticky;
            top: 0;
            background: #fff;     /* was #f7fafd */
            border-bottom: 1px solid #ececec;
            z-index: 2;
        }
    </style>

</head>
<body>
<div class="wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row" style="padding-top: 20px;">
                    <!-- Contact Profile (3 columns) -->
                    <div class="col-md-3 mb-3">

                        <div class="card h-100" id="profileCard">
                            <div class="card-body pb-2 pt-3">
                                <div>
                                    <a href="contacts.php" style="font-size:1rem;text-decoration:none;font-weight:700;display:inline-block;margin-bottom:25px;">
                                        &lt; Contacts
                                    </a>
                                </div>
                                <div class="d-flex align-items-start mb-2" style="gap: 12px;">
                                    <!-- Avatar -->
                                    <div id="profileAvatar" class="rounded-circle bg-secondary text-white fw-bold d-flex align-items-center justify-content-center"
                                         style="width:63px;height:63px;font-size:1.6rem;user-select:none;cursor:pointer;">
                                        <?= strtoupper(substr($row['first_name'],0,1).substr($row['last_name'],0,1)) ?>
                                    </div>
                                    <!-- Name, Company, Position -->

                                    <hr>

                                        <div class="flex-grow-1">
                                            <form id="topProfileForm" class="w-100" style="display:flex;flex-direction:column;gap:2px;">
                                                <!-- DISPLAY VIEW -->
                                                <div id="topProfileDisplay" class="d-flex align-items-center">
                                                    <h5 class="fw-bold mb-0" id="profileName" style="font-size:30px;">
                                                        <?= htmlspecialchars(($row['first_name'] ?? '').' '.($row['last_name'] ?? '')) ?>
                                                    </h5>
                                                    <span class="mx-2 text-secondary" id="editTopProfileBtn" style="cursor:pointer;"><i class="bi bi-pencil"></i></span>
                                                </div>
                                                <!-- EDIT VIEW -->

                                                <div id="topProfileEdit" style="display:none;">
                                                    <input type="text" class="form-control form-control-sm mb-1" name="full_name" value="<?= htmlspecialchars(($row['first_name'] ?? '').' '.($row['last_name'] ?? '')) ?>" style="max-width:180px;display:inline-block;">
                                                    <input type="text" class="form-control form-control-sm mb-1" name="company_name" value="<?= htmlspecialchars($row['company_name'] ?? '') ?>" placeholder="Company" style="max-width:180px;display:inline-block;">
                                                    <input type="text" class="form-control form-control-sm mb-1" name="position" value="<?= htmlspecialchars($row['position'] ?? '') ?>" placeholder="Position" style="max-width:180px;display:inline-block;">
                                                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                                                    <button type="button" class="btn btn-link btn-sm text-secondary" id="cancelTopProfileEdit">Cancel</button>
                                                </div>
                                                <!-- Company & Position Display -->
                                                <div id="topProfileCompanyDisplay">
                                                    <a href="company-profile.php?name=<?= urlencode($row['company_name']) ?>" class="fw-semibold text-primary text-decoration-none" style="font-size:1rem;">
                                                        <?= htmlspecialchars($row['company_name']) ?>
                                                    </a>
                                                </div>
                                                <div class="text-muted" style="font-size:13px;" id="topProfilePositionDisplay">
                                                    <?= htmlspecialchars($row['position']) ?>
                                                </div>
                                            </form>
                                        </div>
                                </div>

                                <!-- Other Details Below, unchanged -->
                                <div id="profileDetailsView" class="pt-2">
                                    <div class="profile-field mb-2" data-field="email">
                                        <strong>Email:</strong>
                                        <span class="profile-value"><?= htmlspecialchars($row['email'] ?? '') ?></span>
                                        <i class="bi bi-pencil-square ms-2 text-secondary edit-icon" style="cursor:pointer;font-size:1em;"></i>
                                    </div>
                                    <div class="profile-field mb-2" data-field="phone_number">
                                        <strong>Phone:</strong>
                                        <span class="profile-value"><?= htmlspecialchars($row['phone_number'] ?? '') ?></span>
                                        <i class="bi bi-pencil-square ms-2 text-secondary edit-icon" style="cursor:pointer;font-size:1em;"></i>
                                    </div>
                                    <div class="profile-field mb-2" data-field="city">
                                        <strong>City:</strong>
                                        <span class="profile-value"><?= htmlspecialchars($row['city'] ?? '') ?></span>
                                        <i class="bi bi-pencil-square ms-2 text-secondary edit-icon" style="cursor:pointer;font-size:1em;"></i>
                                    </div>
                                    <div class="profile-field mb-2" data-field="contact_type">
                                        <strong>Type:</strong>
                                        <span class="profile-value"><?= htmlspecialchars($row['contact_type'] ?? '') ?></span>
                                        <i class="bi bi-pencil-square ms-2 text-secondary edit-icon" style="cursor:pointer;font-size:1em;"></i>
                                    </div>
                                    <div class="profile-field mb-2">
                                        <strong>Created At:</strong>
                                        <span class="profile-value"><?= htmlspecialchars($row['created_at'] ?? '') ?></span>
                                    </div>
                                </div>
                                <!-- Success Alert (add just below the profile card, outside the card if you want floating style) -->
                                <div id="saveAlert" class="alert alert-success" style="display:none;position:fixed;top:20px;right:35px;z-index:1050;">Saved successfully!</div>

                                <!-- (Your edit form and file input stays the same as your previous code, just below here if you use it) -->
                                <input type="file" id="profileImageInput" accept="image/*" style="display:none;">
                                <!-- Your form goes here if needed -->
                            </div>
                        </div>

                    </div>

                    <!-- Activity Timeline (6 columns) -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100" style="display: flex; flex-direction: column;">
                            <div class="card-body p-0 d-flex flex-column" style="flex: 1 1 auto; min-height: 0;">
                                <h4 class="pt-3 px-3">Activity Timeline</h4>
                                <!-- Sticky tab headers -->
                                <div id="activity-timeline-tabs-sticky" style="z-index:2;"></div>
                                <!-- The scrollable timeline panel (fills remaining height, scrolls to footer) -->
                                <div id="activity-timeline-scrollable"
                                     style="flex:1 1 auto; overflow-y:auto; min-height:0; padding:0;">
                                    <!-- Will be populated by JS -->
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

        $(document).ready(function() {
    // Insert tab headers into sticky header container
$('#activity-timeline-tabs-sticky').html(`
    <div class="d-flex align-items-center justify-content-between" style="padding-left:16px;padding-right:16px;background:#fff;">
        <ul class="nav nav-tabs tab-custom mb-0 flex-grow-1" id="activityTabs" role="tablist" style="background:#fff;">
                <li class="nav-item" role="presentation"><button class="nav-link active" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab">Activity</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab">Notes</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" id="emails-tab" data-bs-toggle="tab" data-bs-target="#emails" type="button" role="tab">Emails</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" id="calls-tab" data-bs-toggle="tab" data-bs-target="#calls" type="button" role="tab">Calls</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button" role="tab">Tasks</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" id="meetings-tab" data-bs-toggle="tab" data-bs-target="#meetings" type="button" role="tab">Meetings</button></li>
            </ul>
        <a href="#" id="createBtn" style="color:#2474e5;font-weight:500;font-size:1rem;margin-left:10px;text-decoration:none;white-space:nowrap;padding-top:2px;padding-right:12px;">+Note</a>
    </div>
    `);

    // The scrollable timeline area
    $('#activity-timeline-scrollable').html(`
        <div class="tab-content" id="activityTabsContent" style="background:#f7fafd;min-height:250px;">
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
        <!-- Modal for Creating Activity (leave your modal code as is) -->
    `);

    // Tab/label logic
    const labels = {
        'activity': '+Activity',
        'notes': '+Note',
        'emails': '+Email',
        'calls': '+Log Call',
        'tasks': '+Task',
        'meetings': '+Meeting'
    };

    function updateButtonLabel() {
        let activeTab = $('.tab-custom .nav-link.active').attr('id').replace('-tab','');
        $('#createBtn').text(labels[activeTab] || '+Activity');
        $('#activityModalLabel').text(labels[activeTab].replace('+','Create '));
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
            case 'note': return `<span class="timeline-icon" style="background:#e3f0fd;color:#1a74e5;"><i class="bi bi-journal-text"></i></span>`;
            case 'email': return `<span class="timeline-icon" style="background:#eafcf4;color:#25b676;"><i class="bi bi-envelope-at"></i></span>`;
            case 'call': return `<span class="timeline-icon" style="background:#e8f5fa;color:#20a6e6;"><i class="bi bi-telephone"></i></span>`;
            case 'task': return `<span class="timeline-icon" style="background:#fff6e1;color:#f3a01a;"><i class="bi bi-list-task"></i></span>`;
            case 'meeting': return `<span class="timeline-icon" style="background:#fae7f7;color:#e357c6;"><i class="bi bi-calendar-event"></i></span>`;
            default: return `<span class="timeline-icon" style="background:#ededed;color:#aaa;"><i class="bi bi-dot"></i></span>`;
        }
    }

    function timelineCard(item) {
        // Main label (Note, Email, etc.)
        let mainTitle = '';
        switch(item.activity_type) {
            case 'note': mainTitle = 'Note'; break;
            case 'email': mainTitle = 'Email'; break;
            case 'call': mainTitle = 'Call'; break;
            case 'task': mainTitle = 'Task'; break;
            case 'meeting': mainTitle = 'Meeting'; break;
            default: mainTitle = (item.activity_type || 'Activity').replace('_',' ');
        }
        let msgTitle = (item.title ? `<span class="timeline-title">${item.title}</span>` : '');
        let msgDetail = (item.details ? `<div class="timeline-message">${item.details}</div>` : '');
        let timeStr = item.created_at ? new Date(item.created_at).toLocaleString() : '';
        // TOP RIGHT timestamp, then the rest
        return `
            <li class="timeline-item">
                ${iconForType(item.activity_type)}
                <div class="timeline-content">
                    <span class="timeline-time">${timeStr}</span>
                    <span class="timeline-title">${mainTitle}</span>
                    ${msgTitle}
                    ${msgDetail}
                </div>
            </li>
        `;
    }

    function renderTimeline(timelineData) {
        function render(filterType, containerId) {
            let items = (filterType === 'all') ? timelineData : timelineData.filter(item => item.activity_type === filterType);
            let html = (items.length > 0)
              ? `<ul class="timeline-list">` + items.map(timelineCard).join('') + `</ul>`
              : '<div class="text-center text-muted pt-4 pb-5">No activities yet.</div>';
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
    $('#createBtn').on('click', function(e) {
        e.preventDefault();
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

        // Tab/label logic
        const labels = {
            'activity': '+Activity',
            'notes': '+Note',
            'emails': '+Email',
            'calls': '+Log Call',
            'tasks': '+Task',
            'meetings': '+Meeting'
        };

        function updateButtonLabel() {
            let activeTab = $('.tab-custom .nav-link.active').attr('id').replace('-tab','');
            $('#createBtn').text(labels[activeTab] || '+Activity');
            $('#activityModalLabel').text(labels[activeTab].replace('+','Create '));
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
            function iconForType(type) {
                switch(type) {
                    case 'note': return `<span class="timeline-icon" style="background:#e3f0fd;color:#1a74e5;"><i class="bi bi-journal-text"></i></span>`;
                    case 'email': return `<span class="timeline-icon" style="background:#eafcf4;color:#25b676;"><i class="bi bi-envelope-at"></i></span>`;
                    case 'call': return `<span class="timeline-icon" style="background:#e8f5fa;color:#20a6e6;"><i class="bi bi-telephone"></i></span>`;
                    case 'task': return `<span class="timeline-icon" style="background:#fff6e1;color:#f3a01a;"><i class="bi bi-list-task"></i></span>`;
                    case 'meeting': return `<span class="timeline-icon" style="background:#fae7f7;color:#e357c6;"><i class="bi bi-calendar-event"></i></span>`;
                    default: return `<span class="timeline-icon" style="background:#ededed;color:#aaa;"><i class="bi bi-dot"></i></span>`;
                }
            }

            function timelineCard(item) {
                // Title: use 'Note', 'Email', etc. as title (same as screenshot)
                let mainTitle = '';
                switch(item.activity_type) {
                    case 'note': mainTitle = 'Note'; break;
                    case 'email': mainTitle = 'Email'; break;
                    case 'call': mainTitle = 'Call'; break;
                    case 'task': mainTitle = 'Task'; break;
                    case 'meeting': mainTitle = 'Meeting'; break;
                    default: mainTitle = (item.activity_type || 'Activity').replace('_',' ');
                }
                let msgTitle = (item.title ? `<span class="timeline-title">${item.title}</span>` : '');
                let msgDetail = (item.details ? `<div class="timeline-message">${item.details}</div>` : '');
                let timeStr = item.created_at ? new Date(item.created_at).toLocaleString() : '';
                return `
                    <li class="timeline-item">
                        ${iconForType(item.activity_type)}
                        <div class="timeline-content">
                            <span class="timeline-title">${mainTitle}</span>
                            ${msgTitle}
                            ${msgDetail}
                            <div class="timeline-time">${timeStr}</div>
                        </div>
                    </li>
                `;
            }


            function renderTimeline(timelineData) {
                function render(filterType, containerId) {
                    let items = (filterType === 'all') ? timelineData : timelineData.filter(item => item.activity_type === filterType);
                    let html = (items.length > 0)
                      ? `<ul class="timeline-list">` + items.map(timelineCard).join('') + `</ul>`
                      : '<div class="text-center text-muted pt-4 pb-5">No activities yet.</div>';
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
        $('#createBtn').on('click', function(e) {
            e.preventDefault();
            $('#activity_title').val('');
            $('#activity_details').val('');

                // NEW: choose default based on active tab
    const tab = $('.tab-custom .nav-link.active')
                .attr('id').replace('-tab','');   // notes / emails / …
    const defaultType = (tab === 'activity') ? 'note'
                     : (tab.endsWith('s') ? tab.slice(0,-1) : tab); // emails→email
    $('#activity_type_select').val(defaultType);
    
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

$(function() {
    // Inline editing logic for profile fields
    $('.profile-field').each(function() {
        var $row = $(this);
        var $value = $row.find('.profile-value');
        var $icon = $row.find('.edit-icon');
        var field = $row.data('field');
        if (!field) return; // Skip static fields (e.g., Created At)

        function makeEditable() {
            var origText = $value.text().trim();
            var $input = $('<input type="text" class="form-control form-control-sm" style="display:inline;width:auto;min-width:120px;">').val(origText);
            $value.replaceWith($input);
            $input.focus().select();

            $input.on('blur', saveValue);
            $input.on('keydown', function(e) {
                if (e.key === 'Enter') $input.blur();
                if (e.key === 'Escape') $input.replaceWith('<span class="profile-value">'+origText+'</span>');
            });
        }
        function saveValue() {
            var $input = $row.find('input');
            var newValue = $input.val();
            var origValue = $input.attr('value');
            if (newValue === origValue) {
                $input.replaceWith('<span class="profile-value">'+origValue+'</span>');
                return;
            }
            // AJAX save to backend
            $.ajax({
                url: 'update-contact-inline.php', // You'll create this file below
                type: 'POST',
                data: {
                    contact_id: <?= $contact_id ?>,
                    field: field,
                    value: newValue
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        $input.replaceWith('<span class="profile-value">'+newValue+'</span>');
                        $('#saveAlert').fadeIn(100).delay(800).fadeOut(300);
                    } else {
                        alert(res.message || 'Failed to save');
                        $input.replaceWith('<span class="profile-value">'+origValue+'</span>');
                    }
                },
                error: function() {
                    alert('Error saving. Try again.');
                    $input.replaceWith('<span class="profile-value">'+origValue+'</span>');
                }
            });
        }
        $icon.on('click', function(e) {
            e.stopPropagation();
            if ($row.find('input').length) return;
            makeEditable();
        });
    });
});

$(function(){
    // Show edit fields
    $('#editTopProfileBtn').on('click', function() {
        $('#topProfileDisplay, #topProfileCompanyDisplay, #topProfilePositionDisplay').hide();
        $('#topProfileEdit').show();
    });
    // Cancel edit
    $('#cancelTopProfileEdit').on('click', function(){
        $('#topProfileEdit').hide();
        $('#topProfileDisplay, #topProfileCompanyDisplay, #topProfilePositionDisplay').show();
    });
    // Save top profile fields
    $('#topProfileForm').on('submit', function(e){
        e.preventDefault();
        var $form = $(this);
        var fullName = $form.find('[name="full_name"]').val().trim();
        var company = $form.find('[name="company_name"]').val().trim();
        var position = $form.find('[name="position"]').val().trim();
        // Split full name
        var arr = fullName.split(' ');
        var fname = arr.shift();
        var lname = arr.join(' ');

        $.ajax({
            url: 'update-contact-inline.php',
            type: 'POST',
            data: {
                contact_id: <?= $contact_id ?>,
                field: 'top_profile',
                first_name: fname,
                last_name: lname,
                company_name: company,
                position: position
            },
            dataType: 'json',
            success: function(res){
                if (res.status === 'success') {
                    location.reload();
                } else {
                    alert(res.message || 'Failed to save');
                }
            },
            error: function(){ alert('Failed to save'); }
        });
    });
});
</script>

<!-- Begin: Activity / Note / Email / Call / Task / Meeting Modal -->
<div class="modal fade" id="activityModal" tabindex="-1"
     aria-labelledby="activityModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <form id="activityForm">
        <div class="modal-header">
          <h5 class="modal-title" id="activityModalLabel">Create Activity</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <!-- hidden values that JS will fill in -->
          <input type="hidden" name="action"        value="add_activity">
          <input type="hidden" name="entity_id"     value="<?= $contact_id ?>">
          <input type="hidden" name="activity_type" id="activity_type"  value="note">

          <!-- Title --><!-- Activity Type selector -->
<div class="mb-3">
  <label class="form-label">Activity type</label>
  <select class="form-select" id="activity_type_select" name="activity_type" required>
    <option value="note">Note</option>
    <option value="email">Email</option>
    <option value="call">Call</option>
    <option value="task">Task</option>
    <option value="meeting">Meeting</option>
  </select>
</div>


          <div class="mb-3">
            <label class="form-label">Title</label>
            <input class="form-control" name="title" id="activity_title"
                   type="text" required>
          </div>

          <!-- Details -->
          <div class="mb-3">
            <label class="form-label">Details</label>
            <textarea class="form-control" name="details" id="activity_details"
                      rows="4" required></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-primary" type="submit">Save</button>
          <button class="btn btn-light"   type="button"
                  data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>

    </div>
  </div>
</div>
<!-- End: Activity Modal -->


</body>
</html>
