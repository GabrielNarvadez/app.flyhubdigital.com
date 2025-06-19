<?php
require_once __DIR__ . '/layouts/config.php';

// --- Bulk Delete (bypass foreign key constraint) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_delete']) && !empty($_POST['selected'])) {
    $ids = array_map('intval', $_POST['selected']);
    $in  = implode(',', $ids);
    if ($in) {
        mysqli_query($link, "SET FOREIGN_KEY_CHECKS=0");
        $del = mysqli_query($link, "DELETE FROM companies WHERE id IN ($in)");
        mysqli_query($link, "SET FOREIGN_KEY_CHECKS=1");
    }
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// --- Add Company ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_company'])) {
    $company_name = mysqli_real_escape_string($link, $_POST['company_name'] ?? '');
    $email = mysqli_real_escape_string($link, $_POST['email'] ?? '');
    $phone = mysqli_real_escape_string($link, $_POST['phone'] ?? '');
    $city = mysqli_real_escape_string($link, $_POST['city'] ?? '');
    $industry = mysqli_real_escape_string($link, $_POST['industry'] ?? '');
    $company_type = mysqli_real_escape_string($link, $_POST['company_type'] ?? '');
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');

    $insert_sql = "INSERT INTO companies (company_name, email, phone, city, industry, company_type, created_at, updated_at)
                   VALUES ('$company_name', '$email', '$phone', '$city', '$industry', '$company_type', '$created_at', '$updated_at')";
    mysqli_query($link, $insert_sql);
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// --- Fetch Companies ---
$sql = "SELECT * FROM companies ORDER BY created_at DESC";
$result = mysqli_query($link, $sql);

$companies = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $companies[] = $row;
    }
}
?>

<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Companies Management | Flyhub Business Apps</title>
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
                                <h3 class="mb-0">Companies</h3>
                                <div class="d-flex gap-2">
                                    <!-- Import button (placeholder for now) -->
                                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#importCompaniesModal" data-bs-toggle="tooltip" data-bs-placement="top" title="Import">
                                        <i class="ri-upload-2-line icon-bold"></i>
                                    </button>
                                    <!-- Export button (placeholder for now) -->
                                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#exportCompaniesModal" data-bs-toggle="tooltip" data-bs-placement="top" title="Export">
                                        <i class="ri-download-2-line icon-bold"></i>
                                    </button>
                                    <!-- Add Company button -->
                                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
                                        <i class="ri-building-line icon-bold"></i> Add Company
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
                            <input type="search" id="customSearchBox" class="form-control" placeholder="Search companies...">
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

                <form method="post" id="companiesForm">
                    <input type="hidden" name="bulk_delete" value="1">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="datatable" class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th style="width:40px;"><input type="checkbox" id="selectAll"></th>
                                        <th>Company Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>City</th>
                                        <th>Industry</th>
                                        <th>Company Type</th>
                                        <th>Created At</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($companies)): ?>
                                        <?php foreach ($companies as $row): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="row-checkbox" name="selected[]" value="<?= (int)$row['id'] ?>">
                                                </td>
                                                <td>
                                                    <a href="company_view.php?id=<?= (int)$row['id'] ?>">
                                                        <?= htmlspecialchars($row['company_name'] ?? '') ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="company_view.php?id=<?= (int)$row['id'] ?>">
                                                        <?= htmlspecialchars($row['email'] ?? '') ?>
                                                    </a>
                                                </td>
                                                <td><?= htmlspecialchars($row['phone'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['city'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['industry'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['company_type'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['created_at'] ?? '') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No companies found.</td>
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

<!-- Add Company Modal -->
<div class="modal fade" id="addCompanyModal" tabindex="-1" aria-labelledby="addCompanyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCompanyModalLabel">Add New Company</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="company_name" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label">Industry</label>
                    <input type="text" name="industry" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label">Company Type</label>
                    <input type="text" name="company_type" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="add_company" class="btn btn-primary">Add Company</button>
            </div>
        </form>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Companies</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the selected company(ies)? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="modalConfirmDeleteBtn" class="btn btn-danger">Yes, Delete</button>
            </div>
        </div>
    </div>
</div>

<?php include 'layouts/right-sidebar.php'; ?>
<?php include 'layouts/footer-scripts.php'; ?>

<script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var table = $('#datatable').DataTable({
        responsive: true,
        pageLength: 10,
        dom: 'tip'
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
    const companiesForm = document.getElementById('companiesForm');
    const modalConfirmDeleteBtn = document.getElementById('modalConfirmDeleteBtn');

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

    if (deleteBtn) {
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
        });
    }

    if (modalConfirmDeleteBtn) {
        modalConfirmDeleteBtn.addEventListener('click', function() {
            companiesForm.submit();
        });
    }

    table.on('draw', function() {
        updateSelectedActions();
    });
});
</script>
<script src="assets/js/app.min.js"></script>
</body>
</html>
