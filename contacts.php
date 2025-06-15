<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<?php
require_once __DIR__ . '/layouts/config.php';

// Helper function for dynamic binding (for delete))
function refValues($arr) {
    $refs = [];
    foreach ($arr as $key => $value) {
        $refs[$key] = &$arr[$key];
    }
    return $refs;
}

// Handle Add/Edit/Delete AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    $action = $_POST['ajax_action'];

    if ($action === 'add' || $action === 'edit') {
        $first_name   = trim($_POST['first_name'] ?? '');
        $last_name    = trim($_POST['last_name'] ?? '');
        $email        = trim($_POST['email'] ?? '');
        $company_name = trim($_POST['company_name'] ?? '');
        $phone_number = trim($_POST['phone_number'] ?? '');
        $id           = intval($_POST['id'] ?? 0);

        if (!$email) {
            echo json_encode(['success' => false, 'msg' => 'Email is required.']);
            exit;
        }
        if ($action === 'add') {
            $stmt = $link->prepare("INSERT INTO contacts (first_name, last_name, email, company_name, phone_number) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $first_name, $last_name, $email, $company_name, $phone_number);
        } else {
            $stmt = $link->prepare("UPDATE contacts SET first_name=?, last_name=?, email=?, company_name=?, phone_number=? WHERE id=?");
            $stmt->bind_param("sssssi", $first_name, $last_name, $email, $company_name, $phone_number, $id);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'msg' => 'Failed to save contact.']);
        }
        $stmt->close();
        exit;
    }

    if ($action === 'delete') {
        $ids = $_POST['ids'] ?? [];
        if (!is_array($ids) || empty($ids)) {
            echo json_encode(['success' => false, 'msg' => 'No contacts selected.']);
            exit;
        }

        // Prepare placeholders for IN clause
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $stmt = $link->prepare("DELETE FROM contacts WHERE id IN ($placeholders)");

        if ($stmt === false) {
            echo json_encode(['success' => false, 'msg' => 'Failed to prepare delete statement.']);
            exit;
        }

        // Bind params dynamically
        $params = array_merge([$types], $ids);
        $bindParams = refValues($params);
        call_user_func_array([$stmt, 'bind_param'], $bindParams);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'msg' => 'Failed to delete contact(s).']);
        }
        $stmt->close();
        exit;
    }
}

// Fetch all contacts
$contacts = [];
$sql      = "SELECT id, first_name, last_name, email, phone_number, company_name FROM contacts ORDER BY first_name ASC";
if ($result = mysqli_query($link, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $contacts[] = $row;
    }
}
$totalContacts = count($contacts);
?>

<head>
    <title>Contacts | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <link href="assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link href="assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" />
    <link href="assets/vendor/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" />
    <style>
        body { background: #f6f7fa; }
        .card-soft { border-radius: 10px; border: none; background: #fff; box-shadow: none; }
        .bulk-bar {
            background: #f5f6fb; padding: 14px 16px; border-radius: 8px; margin-bottom: 0;
            display: flex; align-items: center; gap: 16px; font-size: 15px;
        }
        .bulk-bar .btn-link {
            color: #555cc7; text-decoration: none; padding: 0 4px; cursor: pointer;
        }
        .bulk-bar .btn-link:hover { text-decoration: underline; }
        .bulk-bar .btn-link.text-danger { color: #e55353; }
        .btn-green { background: #23c483; border: none; color: #fff; }
        .btn-green:hover { background: #20b275; }
        .table td, .table th { vertical-align: middle; }
        /* Right slide modal */
        .modal.right .modal-dialog {
            position: fixed; right: 0; margin: 0; width: 100%; height: 100%; max-width: 420px;
            pointer-events: auto; transform: translateX(100%);
            transition: transform 0.3s ease-out; will-change: transform; z-index: 1050;
        }
        .modal.right.show .modal-dialog { transform: translateX(0); }
        .modal.right .modal-content {
            height: 100%; border-radius: 0;
            box-shadow: 0 0 10px rgba(0,0,0,0.3); overflow-y: auto;
        }
        .modal-backdrop.show { opacity: 0.5; }
        .form-label { font-size: 15px; font-weight: 500; }
    </style>
    <?php include 'layouts/head-css.php'; ?>
</head>

<body>
<div class="wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <!-- Top Card -->
                <div class="card card-soft mt-4 mb-0 px-0 py-4">
                    <div class="d-flex justify-content-between align-items-center px-4 pb-2">
                        <div><h5 class="fw-semibold mb-0">Contacts List</h5></div>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addContactModal"><i class="ri-user-add-line"></i> + Add Contact</button>
                    </div>

                    <!-- Bulk Action Bar (hidden by default) -->
                    <div id="bulk-bar" class="bulk-bar px-4" style="display:none;">
                        <span id="selected-count">0 selected</span>
                        <a href="#" class="btn-link" id="select-all-action">Select all <?= $totalContacts ?></a>
                        <a href="#" class="btn-link" id="edit-action">Edit</a>
                        <a href="#" class="btn-link text-danger" id="delete-action">Delete</a>
                    </div>

                    <!-- Table -->
                    <div class="px-4 pt-2 pb-3">
                        <div class="table-responsive">
                            <table id="contacts-table" class="table align-middle table-borderless">
                                <thead>
                                    <tr>
                                        <th style="width:34px;">
                                            <input type="checkbox" id="select-all">
                                        </th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Company</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($contacts as $c): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="row-checkbox" value="<?= htmlspecialchars($c['id']) ?>"
                                                data-row='<?= htmlspecialchars(json_encode($c), ENT_QUOTES, "UTF-8") ?>'>
                                        </td>
                                        <td><?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?></td>
                                        <td><?= htmlspecialchars($c['email']) ?></td>
                                        <td><?= htmlspecialchars($c['phone_number']) ?></td>
                                        <td><?= htmlspecialchars($c['company_name']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Add Contact Modal -->
                <div class="modal right fade" id="addContactModal" tabindex="-1" aria-labelledby="addContactModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="addContactForm" autocomplete="off">
                                <div class="modal-header">
                                    <h5 class="modal-title"><i class="ri-user-add-line"></i> Add Contact</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3"><label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="first_name"></div>
                                    <div class="mb-3"><label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="last_name"></div>
                                    <div class="mb-3"><label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email" required></div>
                                    <div class="mb-3"><label class="form-label">Company</label>
                                        <input type="text" class="form-control" name="company_name"></div>
                                    <div class="mb-3"><label class="form-label">Phone</label>
                                        <input type="text" class="form-control" name="phone_number"></div>
                                    <div id="add-contact-error" class="alert alert-danger d-none py-1"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-green">Add Contact</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Edit Contact Modal -->
                <div class="modal right fade" id="editContactModal" tabindex="-1" aria-labelledby="editContactModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="editContactForm" autocomplete="off">
                                <div class="modal-header">
                                    <h5 class="modal-title"><i class="ri-edit-line"></i> Edit Contact</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" id="edit-id">
                                    <div class="mb-3"><label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="first_name" id="edit-first_name"></div>
                                    <div class="mb-3"><label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="last_name" id="edit-last_name"></div>
                                    <div class="mb-3"><label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email" id="edit-email" required></div>
                                    <div class="mb-3"><label class="form-label">Company</label>
                                        <input type="text" class="form-control" name="company_name" id="edit-company_name"></div>
                                    <div class="mb-3"><label class="form-label">Phone</label>
                                        <input type="text" class="form-control" name="phone_number" id="edit-phone_number"></div>
                                    <div id="edit-contact-error" class="alert alert-danger d-none py-1"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-green">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Delete Confirm Modal -->
                <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="ri-alert-line"></i> Confirm Delete</h5>
                            </div>
                            <div class="modal-body">
                                <span id="delete-confirm-msg"></span>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="delete-confirm-btn">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Modals -->

                <!-- Add Success Modal -->
                <div class="modal fade" id="addSuccessModal" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center p-4">
                      <div class="mb-3">
                        <i class="ri-checkbox-circle-line" style="font-size: 48px; color: #23c483;"></i>
                      </div>
                      <h5>New contact added successfully!</h5>
                      <button type="button" class="btn btn-green mt-3" data-bs-dismiss="modal">OK</button>
                    </div>
                  </div>
                </div>

                <!-- Edit Success Modal -->
                <div class="modal fade" id="editSuccessModal" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center p-4">
                      <div class="mb-3">
                        <i class="ri-checkbox-circle-line" style="font-size: 48px; color: #23c483;"></i>
                      </div>
                      <h5>Saved changes successfully!</h5>
                      <button type="button" class="btn btn-green mt-3" data-bs-dismiss="modal">OK</button>
                    </div>
                  </div>
                </div>


            </div>
        </div>
        <?php include 'layouts/footer.php'; ?>
    </div>
</div>
<?php include 'layouts/right-sidebar.php'; ?>
<?php include 'layouts/footer-scripts.php'; ?>

<script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="assets/vendor/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="assets/vendor/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
<script src="assets/js/app.min.js"></script>
<script>
    $(document).ready(function() {
        var table = $('#contacts-table').DataTable({
            paging: true,
            info: true,
            searching: true,
            ordering: true,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            order: []
        });

        function updateBulkBar() {
            var $rows = $('.row-checkbox:checked');
            var count = $rows.length;
            $('#selected-count').text(count + " selected");
            $('#select-all-action').text("Select all <?= $totalContacts ?>");
            if (count > 0) $('#bulk-bar').show();
            else $('#bulk-bar').hide();
        }

        // Checkbox selection logic
        $('#contacts-table').on('change', '.row-checkbox', function() {
            updateBulkBar();
            var all = $('.row-checkbox').length;
            var checked = $('.row-checkbox:checked').length;
            $('#select-all').prop('checked', all === checked);
        });

        // Table header select-all
        $('#select-all').on('change', function() {
            $('.row-checkbox').prop('checked', this.checked);
            updateBulkBar();
        });

        // Bulk Select All action link (acts like table header)
        $('#select-all-action').on('click', function(e) {
            e.preventDefault();
            $('#select-all').prop('checked', true).trigger('change');
        });

        // Add Contact AJAX
        $('#addContactForm').on('submit', function(e) {
            e.preventDefault();
            $('#add-contact-error').addClass('d-none').text('');
            var form = $(this);
            var data = form.serialize() + "&ajax_action=add";
            $.post(window.location.pathname, data, function(res) {
                if (res.success) {
                    // Hide add modal using jQuery Bootstrap API
                    $('#addContactModal').modal('hide');

                    // Show success modal
                    $('#addSuccessModal').modal('show');

                    // Reload page when success modal closes
                    $('#addSuccessModal').off('hidden.bs.modal').on('hidden.bs.modal', function () {
                        location.reload();
                    });
                } else {
                    $('#add-contact-error').removeClass('d-none').text(res.msg || "Failed to add contact.");
                }
            }, 'json');
        });

        // Edit Contact logic
        $('#edit-action').on('click', function(e) {
            e.preventDefault();
            var $first = $('.row-checkbox:checked').first();
            if (!$first.length) return;
            var row = $first.data('row');
            $('#edit-id').val(row.id);
            $('#edit-first_name').val(row.first_name);
            $('#edit-last_name').val(row.last_name);
            $('#edit-email').val(row.email);
            $('#edit-company_name').val(row.company_name);
            $('#edit-phone_number').val(row.phone_number);
            $('#edit-contact-error').addClass('d-none').text('');
            $('#editContactModal').modal('show');
        });

        // Edit Contact AJAX
        $('#editContactForm').on('submit', function(e) {
            e.preventDefault();
            $('#edit-contact-error').addClass('d-none').text('');
            var data = $(this).serialize() + "&ajax_action=edit";
            $.post(window.location.pathname, data, function(res) {
                if (res.success) {
                    $('#editContactModal').modal('hide');
                    $('#editSuccessModal').modal('show');
                    $('#editSuccessModal').off('hidden.bs.modal').on('hidden.bs.modal', function () {
                        location.reload();
                    });
                } else {
                    $('#edit-contact-error').removeClass('d-none').text(res.msg || "Failed to save contact.");
                }
            }, 'json');
        });

        // Delete action
        $('#delete-action').on('click', function(e) {
            e.preventDefault();
            var $selected = $('.row-checkbox:checked');
            if (!$selected.length) return;
            $('#delete-confirm-msg').html('Are you sure you want to delete <b>' + $selected.length + '</b> contact(s)?');
            $('#deleteConfirmModal').modal('show');
        });

        $('#delete-confirm-btn').on('click', function() {
            var ids = [];
            $('.row-checkbox:checked').each(function() {
                ids.push($(this).val());
            });
            $.post(window.location.pathname, {
                ajax_action: 'delete',
                ids: ids
            }, function(res) {
                if (res.success) location.reload();
                else alert(res.msg || "Failed to delete contacts.");
            }, 'json');
        });

    });
</script>
</body>

</html>
