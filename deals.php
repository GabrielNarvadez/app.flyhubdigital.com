<?php
// Existing config, etc.
require_once __DIR__ . '/layouts/config.php';

// --- FETCH CONTACTS (for Customer Name) ---
$contactRows = [];
$qc = $link->query("SELECT id, first_name, last_name FROM contacts ORDER BY first_name, last_name");
while ($r = $qc->fetch_assoc()) $contactRows[] = $r;

// --- FETCH UNITS (for Unit select) ---
$unitRows = [];
$qu = $link->query("SELECT u.id, CONCAT(p.project_title, ' | ', u.phase, ' | Block ', u.block, ' | Lot ', u.lot) as unit_label FROM units u LEFT JOIN projects p ON u.project_id = p.id ORDER BY p.project_title, u.phase, u.block, u.lot");
while ($r = $qu->fetch_assoc()) $unitRows[] = $r;

// Adjust table and field names if needed
$sql = "
    SELECT 
        us.id AS sale_id,
        us.status AS sale_status,
        us.sale_date,
        us.sale_price,
        us.monthly_payment,
        us.created_at AS sale_created,
        u.id AS unit_id,
        u.project_title,
        u.phase,
        u.block,
        u.lot,
        u.lot_class,
        u.lot_area,
        u.price_per_sqm,
        u.total_price,
        u.status AS unit_status,
        u.owner_contact_id,
        c.id AS contact_id,
        c.first_name,
        c.last_name,
        c.email,
        c.phone_number,
        c.city,
        p.id AS project_id,
        p.project_title AS project_name,
        p.location AS project_location
    FROM unit_sales us
    LEFT JOIN units u ON us.unit_id = u.id
    LEFT JOIN contacts c ON us.contact_id = c.id
    LEFT JOIN projects p ON u.project_id = p.id
    ORDER BY us.created_at DESC
";

$result = $link->query($sql);

if (!$result) {
    die("Database query failed: " . $link->error);
}

// Prepare arrays to hold cards per Kanban stage
$kanban = [
    'task-list-one'   => [], // New Inquiry
    'task-list-two'   => [], // Reserved
    'task-list-three' => [], // Sold
    'task-list-four'  => [], // Turned Over
];

// Helper to clean output
function esc($val) { return htmlspecialchars($val ?? ''); }

while ($row = $result->fetch_assoc()) {
    // Map status to Kanban column
    $status = strtolower($row['sale_status'] ?? '');
    if ($status == 'reserved') {
        $list = 'task-list-two';
    } elseif ($status == 'sold') {
        $list = 'task-list-three';
    } elseif (in_array($status, ['turned over', 'completed'])) {
        $list = 'task-list-four';
    } else {
        $list = 'task-list-one';
    }

    // Format reservation date
    $resDate = htmlspecialchars(
        date('M d, Y', strtotime($row['sale_created'] ?? $row['sale_date'] ?? 'now'))
    );

    // Now use $resDate in your HEREDOC
    $kanban[$list][] = <<<CARD
<div class="card mb-0">
    <div class="card-body p-3">
        <span class="float-end badge bg-primary-subtle text-primary">{$status}</span>
        <small class="text-muted">{$resDate}</small>
        <h5 class="my-2 fs-16">{$row['first_name']} {$row['last_name']}</h5>
        <p class="mb-0">
            <span class="pe-2 text-nowrap mb-2 d-inline-block">
                <i class="ri-briefcase-2-line text-muted"></i>
                {$row['project_name']}
            </span>
            <span class="pe-2 text-nowrap mb-2 d-inline-block">
                <i class="ri-map-pin-line text-muted"></i>
                {$row['phase']} | {$row['block']} | {$row['lot']}
            </span>
            <span class="text-nowrap mb-2 d-inline-block">
                <i class="ri-money-dollar-circle-line text-muted"></i>
                <b>{$row['total_price']}</b>
            </span>
        </p>
        <div class="dropdown float-end mt-2">
            <a href="#" class="dropdown-toggle text-muted arrow-none" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ri-more-2-fill fs-18"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <a href="javascript:void(0);" class="dropdown-item"><i class="ri-edit-box-line me-1"></i>Edit</a>
                <a href="javascript:void(0);" class="dropdown-item"><i class="ri-delete-bin-line me-1"></i>Delete</a>
                <a href="javascript:void(0);" class="dropdown-item"><i class="ri-user-add-line me-1"></i>Add People</a>
            </div>
        </div>
        <div class="avatar-group mt-2">
            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-placement="top" title="{$row['first_name']} {$row['last_name']}">
                <img src="assets/images/users/avatar-1.jpg" alt="" class="rounded-circle avatar-xs">
            </a>
        </div>
    </div>
</div>
CARD;
}


?>
<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Products Kanban | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>

<style>
/* Kanban Column (Board) Tweaks */
.board {
    gap: 12px !important;
    padding: 6px 0 !important;
}
.tasks {
    margin-right: 8px !important;
    min-width: 270px !important;
    max-width: 300px !important;
    padding: 6px 4px 0 4px !important;
}
.task-header {
    font-size: 13px !important;
    margin-bottom: 2px !important;
    padding-left: 2px !important;
    letter-spacing: 0.5px;
    font-weight: 600;
    color: #8b9199;
}

/* Card Tweaks */
.task-list-items .card,
.tasks .card {
    margin-bottom: 8px !important;
    border-radius: 8px !important;
    border: 1px solid #e2e5e8 !important;
    box-shadow: none !important;
    background: #fff !important;
    min-height: 75px !important;
    /* Remove extra spacing */
}
.card-body {
    padding: 12px 10px 8px 10px !important;
}
.card .badge {
    font-size: 11px !important;
    padding: 1px 6px !important;
    border-radius: 8px !important;
    vertical-align: middle;
}
.card small,
.card .text-muted {
    font-size: 11px !important;
    color: #9ba4ad !important;
}
.card h5,
.card .fs-16 {
    font-size: 14px !important;
    font-weight: 600 !important;
    margin: 2px 0 3px 0 !important;
    line-height: 1.3 !important;
}
.card p,
.card .mb-0 {
    font-size: 12px !important;
    margin-bottom: 2px !important;
    margin-top: 2px !important;
    color: #353942;
}

/* Reduce icon/text spacing in card details */
.card-body .pe-2,
.card-body .mb-2 {
    padding-right: 5px !important;
    margin-bottom: 0 !important;
}
.card-body i {
    font-size: 13px !important;
    vertical-align: middle;
    margin-right: 2px !important;
}

/* Avatar group size + margin reduction */
.avatar-group {
    margin-top: 5px !important;
    margin-bottom: 0 !important;
}
.avatar-group-item .avatar-xs,
.card .avatar-xs {
    width: 28px !important;
    height: 28px !important;
}

/* Dropdown & icons */
.dropdown.float-end.mt-2 {
    margin-top: 0 !important;
}

/* Responsive tweaks: fit more columns/cards on smaller screens */
@media (max-width: 1600px) {
    .tasks { min-width: 220px !important; }
    .task-list-items .card, .tasks .card { min-height: 60px !important; }
}
@media (max-width: 1200px) {
    .tasks { min-width: 180px !important; }
    .card h5, .card .fs-16 { font-size: 12px !important; }
    .card p, .card .mb-0 { font-size: 11px !important; }
}

/* Board horizontal scroll always visible on desktop */
.board {
    overflow-x: auto !important;
    overflow-y: visible !important;
    white-space: nowrap;
}
.tasks {
    display: inline-block;
    vertical-align: top;
}

/* Hide any empty whitespace in columns */
.task-list-items:empty::after {
    content: 'No Tasks';
    color: #adb5bd;
    font-size: 11px;
    display: block;
    margin: 14px 0 0 6px;
}

/* General: reduce all excessive vertical padding */
[class*="col-"] > .tasks {
    margin-top: 0 !important;
    margin-bottom: 0 !important;
}
</style>


</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include 'layouts/menu.php'; ?>

        <div class="content-page">
            <div class="content">

                <div class="container-fluid">

                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right"></div>
                                <h4 class="page-title">Sales Dashboard 
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#add-new-task-modal" class="btn btn-success btn-sm ms-3">Add New</a>
                                </h4>
                            </div>
                        </div>
                    </div>     

                    <div class="row">
                        <div class="col-12">
                            <div class="board">
                                <div class="tasks" data-plugin="dragula" data-containers='["task-list-one", "task-list-two", "task-list-three", "task-list-four"]'>
                                    <h5 class="mt-0 task-header">NEW INQUIRY</h5>

                                <div id="task-list-one" class="task-list-items">
                                    <?= implode("\n", $kanban['task-list-one']) ?>
                                </div>

                                </div>

                                <div class="tasks">
                                    <h5 class="mt-0 task-header text-uppercase">RESERVED</h5>
                                    <div id="task-list-two" class="task-list-items">
                                        <?= implode("\n", $kanban['task-list-two']) ?>
                                    </div>
                                </div>

                                <div class="tasks">
                                    <h5 class="mt-0 task-header text-uppercase">SOLD</h5>
                                    <div id="task-list-three" class="task-list-items">
                                        <?= implode("\n", $kanban['task-list-three']) ?>
                                    </div>
                                </div>

                                <div class="tasks">
                                    <h5 class="mt-0 task-header text-uppercase">TURNED OVER</h5>
                                    <div id="task-list-four" class="task-list-items">
                                        <?= implode("\n", $kanban['task-list-four']) ?>
                                    </div>
                                </div>
                            </div> <!-- end .board-->
                        </div> <!-- end col -->
                    </div>
                </div> <!-- container -->

            </div> <!-- content -->
            <?php include 'layouts/footer.php'; ?>
        </div> <!-- content-page -->

    </div> <!-- wrapper -->

    <?php include 'layouts/right-sidebar.php'; ?>

    <!--  Add new task modal -->
    <div class="modal fade" id="add-new-task-modal" tabindex="-1" aria-labelledby="AddSaleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form class="p-2">
                    <div class="modal-header">
                        <h4 class="modal-title" id="AddSaleModalLabel">Add New Sale / Reservation</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sale-contact" class="form-label">Customer Name</label>
                                <select class="form-select" id="sale-contact" name="contact_id" required>
                                    <option value="">Select or search contact...</option>
                                    <?php foreach ($contactRows as $row): ?>
                                        <option value="<?= htmlspecialchars($row['id']) ?>">
                                            <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sale-unit" class="form-label">Unit</label>
                                <select class="form-select" id="sale-unit" name="unit_id" required>
                                    <option value="">Select or search unit...</option>
                                    <?php foreach ($unitRows as $row): ?>
                                        <option value="<?= htmlspecialchars($row['id']) ?>">
                                            <?= htmlspecialchars($row['unit_label']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sale-date" class="form-label">Date of Reservation</label>
                                <input type="date" class="form-control" id="sale-date" name="reservation_date" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sale-amount" class="form-label">Reservation Amount (₱)</label>
                                <input type="number" class="form-control" id="sale-amount" name="reservation_amount" min="0" step="0.01" value="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sale-terms" class="form-label">Payment Terms (months)</label>
                                <select class="form-select" id="sale-terms" name="payment_terms">
                                    <option value="12">12 months</option>
                                    <option value="24">24 months</option>
                                    <option value="36">36 months</option>
                                    <option value="48">48 months</option>
                                    <option value="60">60 months</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Miscellaneous Fee</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="misc_fee" id="fee-upfront" value="upfront" checked>
                                    <label class="form-check-label" for="fee-upfront">Upfront</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="misc_fee" id="fee-monthly" value="monthly">
                                    <label class="form-check-label" for="fee-monthly">Monthly</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="misc_fee" id="fee-end" value="end">
                                    <label class="form-check-label" for="fee-end">End of Terms</label>
                                </div>
                            </div>
                        </div>
                        <!-- No summary box for now as per instruction -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /.modal -->

    <!--  Task details modal -->
    <div class="modal fade task-modal-content" id="task-detail-modal" tabindex="-1" role="dialog" aria-labelledby="TaskDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="TaskDetailModalLabel">Sample Task Title <span class="badge bg-danger ms-2">High</span></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Modal body content unchanged for details/comments/files tabs -->
                    <div class="p-2">
                        <h5 class="mt-0">Description:</h5>
                        <p class="text-muted mb-4">Description goes here.</p>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <h5>Create Date</h5>
                                    <p>17 March 2023 <small class="text-muted">1:00 PM</small></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <h5>Due Date</h5>
                                    <p>22 December 2023 <small class="text-muted">1:00 PM</small></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4" id="tooltip-container">
                                    <h5>Asignee:</h5>
                                    <div class="avatar-group mt-1">
                                        <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-placement="top" title="Person A">
                                            <img src="assets/images/users/avatar-1.jpg" alt="" class="rounded-circle avatar-xs">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <ul class="nav nav-tabs nav-bordered mb-3">
                            <li class="nav-item">
                                <a href="#home-b1" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">Comments</a>
                            </li>
                            <li class="nav-item">
                                <a href="#profile-b1" data-bs-toggle="tab" aria-expanded="true" class="nav-link">Files</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane show active" id="home-b1">
                                <textarea class="form-control mb-2" placeholder="Write message" id="example-textarea" rows="3"></textarea>
                                <div class="text-end">
                                    <div class="btn-group mb-2 d-none d-sm-inline-block">
                                        <button type="button" class="btn btn-link btn-sm text-muted fs-18"><i class="ri-attachment-2"></i></button>
                                    </div>
                                    <div class="btn-group mb-2 d-none d-sm-inline-block">
                                        <button type="button" class="btn btn-primary btn-sm">Submit</button>
                                    </div>
                                </div>
                                <!-- Example comment section, keep for layout -->
                                <div class="d-flex mt-2">
                                    <img class="me-3 avatar-sm rounded-circle" src="assets/images/users/avatar-3.jpg" alt="Generic placeholder image">
                                    <div class="w-100">
                                        <h5 class="mt-0">Jeremy Tomlinson</h5>
                                        Comment text here.
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="profile-b1">
                                <div class="card mb-1 shadow-none border">
                                    <div class="p-2">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <div class="avatar-sm">
                                                    <span class="avatar-title rounded">.ZIP</span>
                                                </div>
                                            </div>
                                            <div class="col ps-0">
                                                <a href="javascript:void(0);" class="text-muted fw-bold">-admin-design.zip</a>
                                                <p class="mb-0">2.3 MB</p>
                                            </div>
                                            <div class="col-auto">
                                                <a href="javascript:void(0);" class="btn btn-link btn-lg text-muted"><i class="ri-download-2-line"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Example file section, keep for layout -->
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-body -->
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <?php include 'layouts/footer-scripts.php'; ?>
    <script src="assets/vendor/dragula/dragula.min.js"></script>
    <script src="assets/js/pages/component.dragula.js"></script>
    <script src="assets/js/app.min.js"></script>
</body>
</html>
