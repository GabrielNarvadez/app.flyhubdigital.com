<?php
require_once __DIR__ . '/layouts/config.php';

// --- Bulk Delete (bypass foreign key constraint) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_delete']) && !empty($_POST['selected'])) {
    $ids = array_map('intval', $_POST['selected']);
    $in  = implode(',', $ids);
    if ($in) {
        mysqli_query($link, "SET FOREIGN_KEY_CHECKS=0");
        $del = mysqli_query($link, "DELETE FROM contacts WHERE id IN ($in)");
        mysqli_query($link, "SET FOREIGN_KEY_CHECKS=1");
    }
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// --- Add Contact ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_contact'])) {
    $first_name = mysqli_real_escape_string($link, $_POST['first_name'] ?? '');
    $last_name = mysqli_real_escape_string($link, $_POST['last_name'] ?? '');
    $position = mysqli_real_escape_string($link, $_POST['position'] ?? '');
    $email = mysqli_real_escape_string($link, $_POST['email'] ?? '');
    $phone_number = mysqli_real_escape_string($link, $_POST['phone_number'] ?? '');
    $company_name = mysqli_real_escape_string($link, $_POST['company_name'] ?? '');
    $city = mysqli_real_escape_string($link, $_POST['city'] ?? '');
    $contact_type = mysqli_real_escape_string($link, $_POST['contact_type'] ?? '');
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');

    $insert_sql = "INSERT INTO contacts (first_name, last_name, position, email, phone_number, company_name, city, contact_type, created_at, updated_at)
                   VALUES ('$first_name', '$last_name', '$position', '$email', '$phone_number', '$company_name', '$city', '$contact_type', '$created_at', '$updated_at')";
    mysqli_query($link, $insert_sql);
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// --- AJAX: Check if selected contacts have invoices ---
if (isset($_GET['ajax_check_invoices']) && isset($_GET['ids'])) {
    $ids = array_map('intval', explode(',', $_GET['ids']));
    $in = implode(',', $ids);
    $count = 0;
    if ($in) {
        $qry = mysqli_query($link, "SELECT COUNT(*) AS cnt FROM invoices WHERE contact_id IN ($in)");
        $row = mysqli_fetch_assoc($qry);
        $count = (int)($row['cnt'] ?? 0);
    }
    header('Content-Type: application/json');
    echo json_encode(['has_invoices' => $count > 0]);
    exit;
}

// --- Fetch Contacts ---
$sql = "SELECT * FROM contacts ORDER BY created_at DESC";
$result = mysqli_query($link, $sql);

// Prepare data for JS CSV export
$contacts = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $contacts[] = $row;
    }
}

$contact_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT * FROM activity_timeline WHERE entity_type='contact' AND entity_id=? ORDER BY created_at DESC";
$stmt = $link->prepare($sql);
$stmt->bind_param('i', $contact_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    // Output as timeline card
}
$stmt->close();

?>
<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Contacts Management | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <link href="assets/vendor/remixicon/fonts/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <?php include 'layouts/head-css.php'; ?>
    <style>
        body { background: #f8fafb; }
        .page-title-box { margin-bottom: 1rem; }
        .card-body { padding: 1.5rem !important; }
        .custom-datatable-controls {
            margin-bottom: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1.5rem;
        }
        .custom-datatable-searchrow {
            display: flex;
            align-items: center;
            gap: 0.7em;
            flex-wrap: nowrap;
            white-space: nowrap;
        }
        .custom-datatable-searchrow .input-group {
            width: 240px;
        }
        .custom-selected-actions {
            display: none;
            align-items: center;
            gap: 0.55em;
            margin-left: 0.5em;
            font-size: 15px;
            white-space: nowrap;
        }
        .custom-selected-actions.active { display: flex; }
        .custom-link {
            color: #1677ff;
            background: none;
            border: none;
            padding: 0;
            font-weight: 400;
            text-decoration: underline;
            cursor: pointer;
            transition: color 0.2s;
        }
        .custom-link:hover { color: #0d6efd; text-decoration: underline; }
        .custom-link.text-danger {
            color: #eb2f2f;
            text-decoration: underline;
        }
        .custom-link.text-danger:hover {
            color: #c82333;
        }
        .custom-datatable-length {
            min-width: 140px;
            text-align: right;
        }
        @media (max-width: 991.98px) {
            .custom-datatable-controls {
                flex-direction: column;
                align-items: stretch;
                gap: 0.5rem;
            }
            .custom-datatable-searchrow,
            .custom-datatable-length { width: 100%; }
            .custom-datatable-length { text-align: left; }
        }
        /* Modern tooltip look (optional override for Bootstrap) */
        .tooltip-inner {
            background-color: #222 !important;
            color: #fff !important;
            border-radius: 6px !important;
            font-size: 1rem;
            padding: 0.6em 1em;
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }
        .bs-tooltip-top .tooltip-arrow::before {
            border-top-color: #111 !important;
        }
        /* Draggable styling for export modal */
        #allFieldsList, #currentFieldsList {
            min-height: 250px;
            border: 1px solid #ddd;
            padding: 10px;
            background: #f8fafb;
        }
        #allFieldsList .list-group-item, #currentFieldsList .list-group-item {
            cursor: grab;
        }
        #allFieldsList .list-group-item:active, #currentFieldsList .list-group-item:active {
            cursor: grabbing;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="content-page">
        <div class="content">
            <div class="container-fluid py-4">

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h3 class="mb-0">Contacts</h3>
                                <div class="d-flex gap-2">
                                    <!-- Import button -->
                                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#importContactsModal" data-bs-toggle="tooltip" data-bs-placement="top" title="Import">
                                        <i class="ri-upload-2-line icon-bold"></i>
                                    </button>
                                    <!-- Export button -->
                                    <button class="btn btn-outline-secondary" type="button"
                                            data-bs-toggle="modal" data-bs-target="#exportContactsModal"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Export">
                                        <i class="ri-download-2-line icon-bold"></i>
                                    </button>
                                    <!-- Add Contact button -->
                                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addContactModal">
                                        <i class="ri-user-add-line icon-bold"></i> Add Contact
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom DataTable controls row -->
                <div class="custom-datatable-controls">
                    <div class="custom-datatable-searchrow">
                        <div class="input-group flex-nowrap">
                            <span class="input-group-text bg-white"><i class="ri-search-line"></i></span>
                            <input type="search" id="customSearchBox" class="form-control" placeholder="Search contacts...">
                        </div>
                        <div class="custom-selected-actions" id="selectedActions">
                            <span id="selectedCount">0 selected</span>
                            <a href="#" class="custom-link" id="selectAllBtn">Select all</a>
                            <a href="#" class="custom-link text-danger" id="deleteBtn" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Delete</a>
                        </div>
                    </div>
                    <div class="custom-datatable-length">
                        <label class="d-flex align-items-center mb-0" style="gap: .5em;">
                            Show
                            <select id="customLengthBox" class="form-select form-select-sm ms-2" style="width:auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            entries
                        </label>
                    </div>
                </div>

                <form method="post" id="contactsForm">
                    <input type="hidden" name="bulk_delete" value="1">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="datatable" class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th style="width:40px;"><input type="checkbox" id="selectAll"></th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Position</th>
                                        <th>City</th>
                                        <th>Created At</th>
                                        <th>Contact Type</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($contacts)): ?>
                                        <?php foreach ($contacts as $row): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="row-checkbox" name="selected[]" value="<?= (int)$row['id'] ?>">
                                                </td>
                                                <td>
                                                  <a href="contact_view.php?id=<?= (int)$row['id'] ?>">
                                                    <?= htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?>
                                                  </a>
                                                </td>
                                                <td>
                                                  <a href="contact_view.php?id=<?= (int)$row['id'] ?>">
                                                    <?= htmlspecialchars($row['email'] ?? '') ?>
                                                  </a>
                                                </td>
                                                <td><?= htmlspecialchars($row['phone_number'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['position'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['city'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['created_at'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['contact_type'] ?? '') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No contacts found.</td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php include 'layouts/footer.php'; ?>
    </div>
</div>

<!-- Import Contacts Modal -->
<div class="modal fade" id="importContactsModal" tabindex="-1" aria-labelledby="importContactsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="import_contacts.php" method="post" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importContactsModalLabel">Import Contacts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label for="importFile" class="form-label">Choose CSV file</label>
                    <input type="file" name="importFile" id="importFile" class="form-control" accept=".csv" required>
                </div>
                <small class="text-muted">CSV columns: first_name, last_name, email, phone_number, position, city, contact_type</small>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Import</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Contact Modal -->
<div class="modal fade" id="addContactModal" tabindex="-1" aria-labelledby="addContactModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addContactModalLabel">Add New Contact</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Position</label>
                    <input type="text" name="position" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone_number" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="company_name" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label">Contact Type</label>
                    <input type="text" name="contact_type" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="add_contact" class="btn btn-primary">Add Contact</button>
            </div>
        </form>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Contacts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="invoiceWarning" class="alert alert-warning d-none" style="font-size: 0.97em;">
                    <strong>Warning:</strong> One or more selected contacts have existing invoices. Deleting them may affect related records.
                </div>
                Are you sure you want to delete the selected contact(s)? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="modalConfirmDeleteBtn" class="btn btn-danger">Yes, Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Export Contacts Modal -->
<div class="modal fade" id="exportContactsModal" tabindex="-1" aria-labelledby="exportContactsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="exportFieldsForm" onsubmit="return false;">
        <div class="modal-header">
          <h5 class="modal-title" id="exportContactsModalLabel">Export Contacts</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <h6>All Fields</h6>
              <ul id="allFieldsList" class="list-group min-vh-50"></ul>
            </div>
            <div class="col-md-6">
              <h6>Current Fields (will be exported, drag to reorder)</h6>
              <ul id="currentFieldsList" class="list-group min-vh-50"></ul>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <small class="text-muted me-auto">Drag fields between lists and reorder as needed.</small>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="exportCSVBtn">Export</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'layouts/right-sidebar.php'; ?>
<?php include 'layouts/footer-scripts.php'; ?>

<script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var table = $('#datatable').DataTable({
        responsive: true,
        pageLength: 10,
        dom: 'tip' // removes the default search and show-entries controls!
    });

    $('#customSearchBox').on('keyup change', function () {
        table.search(this.value).draw();
    });

    $('#customLengthBox').on('change', function () {
        table.page.len(this.value).draw();
    });

    const checkboxes = () => Array.from(document.querySelectorAll('.row-checkbox'));
    const selectAllBox = document.getElementById('selectAll');
    const selectedActions = document.getElementById('selectedActions');
    const selectedCount = document.getElementById('selectedCount');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const deleteBtn = document.getElementById('deleteBtn');
    const contactsForm = document.getElementById('contactsForm');
    const modalConfirmDeleteBtn = document.getElementById('modalConfirmDeleteBtn');
    const invoiceWarning = document.getElementById('invoiceWarning');

    function updateSelectedActions() {
        let allRows = checkboxes();
        let checked = allRows.filter(cb => cb.checked);
        selectedCount.textContent = `${checked.length} selected`;
        if (checked.length > 0) {
            selectedActions.classList.add('active');
            selectAllBtn.style.display = (checked.length < allRows.length) ? 'inline' : 'none';
            selectAllBtn.textContent = `Select all ${allRows.length}`;
        } else {
            selectedActions.classList.remove('active');
        }
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('row-checkbox') || e.target === selectAllBox) {
            let all = checkboxes();
            let checked = all.filter(cb => cb.checked);
            if (e.target === selectAllBox) {
                for (let cb of all) cb.checked = selectAllBox.checked;
            } else if (!e.target.checked && selectAllBox.checked) {
                selectAllBox.checked = false;
            } else if (checked.length === all.length) {
                selectAllBox.checked = true;
            }
            updateSelectedActions();
        }
    });

    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            let all = checkboxes();
            for (let cb of all) cb.checked = true;
            selectAllBox.checked = true;
            updateSelectedActions();
        });
    }

    // Show modal and check if any selected have invoices
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            invoiceWarning.classList.add('d-none');
            let ids = checkboxes().filter(cb => cb.checked).map(cb => cb.value);
            if (ids.length > 0) {
                fetch(`?ajax_check_invoices=1&ids=${ids.join(",")}`)
                    .then(resp => resp.json())
                    .then(data => {
                        if (data.has_invoices) {
                            invoiceWarning.classList.remove('d-none');
                        }
                    });
            }
        });
    }

    if (modalConfirmDeleteBtn) {
        modalConfirmDeleteBtn.addEventListener('click', function() {
            contactsForm.submit();
        });
    }

    table.on('draw', function() {
        updateSelectedActions();
    });

    // Enable Bootstrap tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el);
    });

    // --- Export Modal logic ---
    // Fields with friendly names
    const allFields = [
        {field: 'id', label: 'ID'},
        {field: 'first_name', label: 'First Name'},
        {field: 'last_name', label: 'Last Name'},
        {field: 'email', label: 'Email'},
        {field: 'phone_number', label: 'Phone'},
        {field: 'position', label: 'Position'},
        {field: 'city', label: 'City'},
        {field: 'company_id', label: 'Company ID'},
        {field: 'company_name', label: 'Company Name'},
        {field: 'contact_type', label: 'Contact Type'},
        {field: 'created_at', label: 'Created At'},
        {field: 'updated_at', label: 'Updated At'}
    ];
    // These are the columns on the table by default
    const defaultCurrentFields = [
        {field: 'first_name', label: 'First Name'},
        {field: 'last_name', label: 'Last Name'},
        {field: 'email', label: 'Email'},
        {field: 'phone_number', label: 'Phone'},
        {field: 'position', label: 'Position'},
        {field: 'city', label: 'City'},
        {field: 'contact_type', label: 'Contact Type'},
        {field: 'created_at', label: 'Created At'}
    ];

    // Prepare fields in modal every time it's opened
    $('#exportContactsModal').on('show.bs.modal', function () {
        let current = JSON.parse(JSON.stringify(defaultCurrentFields));
        let currentFieldNames = current.map(f => f.field);
        let available = allFields.filter(f => !currentFieldNames.includes(f.field));
        $('#allFieldsList').empty();
        $('#currentFieldsList').empty();
        available.forEach(f => {
            $('#allFieldsList').append(
                `<li class="list-group-item" data-field="${f.field}">${f.label}</li>`
            );
        });
        current.forEach(f => {
            $('#currentFieldsList').append(
                `<li class="list-group-item" data-field="${f.field}">${f.label}</li>`
            );
        });
    });

    // Drag-and-drop between lists using SortableJS
    let sortable1, sortable2;
    $('#exportContactsModal').on('shown.bs.modal', function() {
        // Destroy previous if any
        if (sortable1) sortable1.destroy();
        if (sortable2) sortable2.destroy();
        sortable1 = Sortable.create(document.getElementById('allFieldsList'), {
            group: { name: 'fields', pull: 'clone', put: true },
            sort: false,
            animation: 150
        });
        sortable2 = Sortable.create(document.getElementById('currentFieldsList'), {
            group: { name: 'fields', pull: true, put: true },
            sort: true,
            animation: 150
        });
    });

    // Export CSV logic (client-side)
    document.getElementById('exportFieldsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        // Get fields in current
        let fields = [];
        $('#currentFieldsList li').each(function() {
            fields.push({
                field: $(this).data('field'),
                label: $(this).text()
            });
        });
        if (!fields.length) {
            alert('Please add at least one field to export.');
            return;
        }
        // Get data from PHP (injected as JS variable)
        let contacts = window.exportContactsData || [];
        if (!contacts.length) {
            alert('No contacts to export.');
            return;
        }
        // Compose CSV
        let csvRows = [];
        csvRows.push(fields.map(f => `"${f.label}"`).join(',')); // header
        contacts.forEach(row => {
            let vals = [];
            fields.forEach(f => {
                let val = row[f.field];
                if (typeof val === "undefined" || val === null) val = '';
                vals.push(`"${String(val).replace(/"/g,'""')}"`);
            });
            csvRows.push(vals.join(','));
        });
        let csvContent = csvRows.join('\r\n');
        // Download as file
        let blob = new Blob([csvContent], {type: 'text/csv'});
        let url = URL.createObjectURL(blob);
        let link = document.createElement('a');
        link.href = url;
        let dt = new Date();
        link.download = 'contacts_export_' + dt.getFullYear() + ('0'+(dt.getMonth()+1)).slice(-2) + ('0'+dt.getDate()).slice(-2) + '.csv';
        document.body.appendChild(link);
        link.click();
        setTimeout(function(){ document.body.removeChild(link); URL.revokeObjectURL(url); }, 200);
        // Hide modal
        $('#exportContactsModal').modal('hide');
    });

    // Provide contacts data to JS (for CSV export)
    window.exportContactsData = <?php echo json_encode($contacts, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
});
</script>
<!-- App js -->
<script src="assets/js/app.min.js"></script>
</body>
</html>
