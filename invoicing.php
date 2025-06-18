<?php
// --- Session and Layout Includes ---
include 'layouts/session.php';
include 'layouts/main.php';

// --- DB Config ---
require_once __DIR__ . '/layouts/config.php';
function esc($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }

// --- AJAX HANDLERS ---
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {

    // --- INVOICE PREVIEW ---
    if (isset($_GET['id'])) {
        $inv_id = intval($_GET['id']);
        $stmt = $link->prepare("SELECT i.*, c.first_name, c.last_name, c.email, c.phone_number, c.company_name 
            FROM invoices i LEFT JOIN contacts c ON i.contact_id = c.id WHERE i.id=?");
        $stmt->bind_param("i", $inv_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $inv = $res->fetch_assoc();
        $stmt->close();

        if (!$inv) {
            echo '<div class="alert alert-warning">Invoice not found.</div>';
            exit;
        }

        // Fetch line items
        $items = [];
        $stmt2 = $link->prepare("SELECT ii.*, p.name as product_name, p.description as product_desc 
            FROM invoice_items ii LEFT JOIN products p ON ii.product_id = p.id WHERE ii.invoice_id=?");
        $stmt2->bind_param("i", $inv_id);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        while ($row = $res2->fetch_assoc()) $items[] = $row;
        $stmt2->close();

        if (!$items) {
            $items[] = [
                'product_name' => 'Sample Product',
                'product_desc' => 'Sample description.',
                'quantity' => 1,
                'unit_price' => $inv['subtotal'] ?? 0,
                'discount' => 0,
                'tax_id' => null,
                'total' => $inv['total'] ?? 0,
            ];
        }

        $total = $inv['subtotal'];
        $discount = $inv['discount_total'];
        $tax = $inv['tax_total'];
        $grand = $inv['total'];
        ?>


        <div class="d-flex flex-wrap gap-2 mb-3">
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ri-more-2-line"></i> Actions
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="#" onclick="window.print(); return false;">
                            <i class="ri-printer-line"></i> Print
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" onclick="alert('Pretend sending email to <?=esc($inv['email'])?>'); return false;">
                            <i class="ri-mail-line"></i> Send
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" id="btn-generate-soa" target="_blank"
                           href="soa-manager.php?customer=<?=urlencode(trim($inv['first_name'].' '.$inv['last_name']))?>">
                            <i class="ri-list-unordered"></i> Generate SOA
                        </a>
                    </li>
                </ul>
            </div>
            <?php if ($inv['status'] === 'draft'): ?>
                <button class="btn btn-outline-success btn-sm" onclick="confirmInvoice()">
                    <i class="ri-check-line"></i> Confirm
                </button>
            <?php elseif ($inv['status'] === 'confirmed'): ?>
                <button class="btn btn-success btn-sm" onclick="confirmPayment()">
                    Confirm Payment
                </button>
            <?php endif; ?>
        </div>
        <script>
        function confirmPayment() {
            if (confirm('Confirm payment for this invoice?')) {
                alert('Payment confirmed! (Replace with actual payment logic)');
            }
        }
        function confirmInvoice() {
            if (confirm('Confirm this invoice?')) {
                alert('Invoice confirmed! (Replace with actual confirm logic)');
            }
        }
        </script>
        <div class="clearfix mb-2">
            <div class="float-start"><img src="/assets/images/logo-dark.png" alt="logo" height="28"></div>
            <div class="float-end"><h3 class="m-0">Invoice</h3></div>
        </div>
        <div class="row align-items-center">
            <div class="col-8">
                <p class="mb-1"><b>Hello<?=esc(trim($inv['first_name'].' '.$inv['last_name']))?>,</b></p>
                <p class="text-muted small mb-1"> find below a cost-breakdown for your transaction.</p>
            </div>
            <div class="col-4 text-end">
                <div class="mb-1 small"><strong>Status:</strong> <span class="badge bg-<?= $inv['status']=='paid'?'success':($inv['status']=='sent'?'info':($inv['status']=='draft'?'secondary':'warning')) ?>"><?=ucfirst($inv['status'])?></span></div>
                <div class="mb-1 small"><strong>Invoice #:</strong> <?=esc($inv['invoice_number'])?></div>
            </div>
        </div>
        <div class="row mt-3 small">
            <div class="col-6">
                <h6 class="fw-bold">Billing Address</h6>
                <address class="mb-0">
                    <?=esc(trim($inv['first_name'].' '.$inv['last_name']))?><br>
                    <?php if (!empty($inv['company_name'])): ?>
                        <?=esc($inv['company_name'])?><br>
                    <?php endif; ?>
                    <abbr title="Phone">P:</abbr> <?=esc($inv['phone_number'])?>
                </address>
            </div>
            <div class="col-6">
                <h6 class="fw-bold">Contact Email</h6>
                <address class="mb-0">
                    <?=esc($inv['email'])?>
                </address>
            </div>
        </div>
        <div class="mt-3 table-responsive">
            <table class="table table-sm table-centered table-hover table-borderless mb-0">
                <thead class="border-top border-bottom bg-light-subtle border-light">
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Details</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $i=>$item): ?>
                    <tr>
                        <td><?=($i+1)?></td>
                        <td><?=esc($item['product_name'])?></td>
                        <td><?=esc($item['product_desc']??'')?></td>
                        <td><?=esc($item['quantity']??1)?></td>
                        <td>₱<?=number_format($item['unit_price']??0,2)?></td>
                        <td>₱<?=number_format(($item['quantity']??1)*($item['unit_price']??0),2)?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="float-end small">
                    <p><b>Sub-total:</b> <span class="float-end">₱<?=number_format($total,2)?></span></p>
                    <p><b>Discount:</b> <span class="float-end">₱<?=number_format($discount,2)?></span></p>
                    <p><b>Tax:</b> <span class="float-end">₱<?=number_format($tax,2)?></span></p>
                    <h5 class="mt-2">₱<?=number_format($grand,2)?></h5>
                </div>
            </div>
        </div>
        <div class="mt-3"><small class="text-muted"><?=esc($inv['notes']??"")?></small></div>

        <!-- THIS IS THE IMPORTANT LINE YOU NEED FOR METADATA -->
        <script type="application/json"><?=json_encode([
            'id'     => $inv['id'],
            'status' => $inv['status']
        ])?></script>
        <?php
        exit;
    }

    // --- AJAX: Delete Invoice ---
    if (isset($_GET['delete_id'])) {
        $del_id = intval($_GET['delete_id']);
        // Only allow delete if status is not paid
        $stmt = $link->prepare("SELECT status FROM invoices WHERE id=?");
        $stmt->bind_param("i", $del_id);
        $stmt->execute();
        $stmt->bind_result($del_status);
        $stmt->fetch();
        $stmt->close();

        if (!$del_status) {
            echo json_encode(['success'=>false,'msg'=>'Invoice not found']);
            exit;
        }
        if ($del_status === 'paid') {
            echo json_encode(['success'=>false,'msg'=>'Cannot delete paid invoice.']);
            exit;
        }
        $stmt = $link->prepare("DELETE FROM invoices WHERE id=? LIMIT 1");
        $stmt->bind_param("i", $del_id);
        $ok = $stmt->execute();
        $stmt->close();
        echo json_encode(['success'=>$ok]);
        exit;
    }

    // --- AJAX: Table (kept the same) ---
    if (isset($_GET['table']) && $_GET['table']=='1') {
        $search = trim($_GET['search'] ?? '');
        $status = $_GET['status'] ?? '';
        $from   = $_GET['from'] ?? '';
        $to     = $_GET['to'] ?? '';
        $customer_id = intval($_GET['customer_id'] ?? 0);
        $where = [];
        $params = [];
        $types = "";

        if ($status && in_array($status, ['draft','sent','paid','void','canceled'])) {
            $where[] = "i.status=?";
            $params[] = $status;
            $types .= "s";
        }
        if ($customer_id) {
            $where[] = "i.contact_id=?";
            $params[] = $customer_id;
            $types .= "i";
        }
        if ($from && preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) {
            $where[] = "i.issue_date>=?";
            $params[] = $from;
            $types .= "s";
        }
        if ($to && preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
            $where[] = "i.issue_date<=?";
            $params[] = $to;
            $types .= "s";
        }
        if ($search) {
            $where[] = "(i.invoice_number LIKE ? OR c.first_name LIKE ? OR c.last_name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $types .= "sss";
        }

        $sql = "SELECT i.*, c.first_name, c.last_name FROM invoices i
            LEFT JOIN contacts c ON i.contact_id = c.id";
        if ($where) $sql .= " WHERE ".implode(' AND ', $where);
        $sql .= " ORDER BY i.issue_date DESC";
        $stmt = $link->prepare($sql);
        if ($where) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $invoices = [];
        while ($row = $result->fetch_assoc()) $invoices[] = $row;
        $stmt->close();

        if (count($invoices)) {
            foreach ($invoices as $inv) {
                ?>
                <tr data-id="<?=$inv['id']?>" style="cursor:pointer">
                    <td class="fw-bold text-primary"><?=$inv['invoice_number']?></td>
                    <td><?=esc(trim($inv['first_name'].' '.$inv['last_name']))?></td>
                    <td>₱<?=number_format($inv['total'],2)?></td>
                    <td>
                        <span class="badge bg-<?=$inv['status']=='paid'?'success':($inv['status']=='sent'?'info':($inv['status']=='draft'?'secondary':'warning')) ?>">
                            <?=ucfirst($inv['status'])?>
                        </span>
                    </td>
                    <td><?=esc($inv['issue_date'])?></td>
                </tr>
                <?php
            }
        } else {
            echo '<tr><td colspan="5" class="text-center text-muted">No invoices found.</td></tr>';
        }
        exit;
    }
} // End AJAX handlers

$status_opts = ['draft','sent','paid','void','canceled'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Invoicing | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <style>
        .form-control:focus, .form-select:focus { box-shadow: 0 0 0 0.15rem #4285f4 !important; }
        .datepicker::-webkit-input-placeholder { color: #bbb !important; }
        .datepicker { min-width: 120px; }
        @media print {
            body * { visibility: hidden !important; }
            .invoice-preview, .invoice-preview * { visibility: visible !important; }
            .invoice-preview {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100vw !important;
                z-index: 9999 !important;
                background: #fff !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .col-lg-6:first-child, .card.shadow-sm { display: none !important; }
            .invoice-preview.card {
                border: none !important;
                box-shadow: none !important;
                background: #fff !important;
            }
            .invoice-preview button,
            .invoice-preview a.btn {
                display: none !important;
            }
        }
        .alert-fixed-top {
            position: fixed;
            top: 15px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            min-width: 350px;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <!-- ALERT -->
                <div id="alert-area"></div>
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <h3 class="page-title">Invoice</h3>
                        </div>
                    </div>
                </div>
                <!-- ==== BEGIN INVOICE TABLE & PREVIEW ==== -->
                <div class="row g-4">
                    <!-- LEFT: Invoice Table -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <form class="row mb-3 gx-2 gy-2 align-items-center" id="filter-form" autocomplete="off">
                                    <div class="col-12">
                                        <input type="text" name="search" id="filter-search" class="form-control" placeholder="Search invoices..." autocomplete="off">
                                    </div>
                                    <!-- Customer Dropdown -->
                                    <div class="col-auto">
                                        <select name="customer_id" id="filter-customer" class="form-select">
                                            <option value="">All Customers</option>
                                            <?php
                                            $sql_cust = "SELECT DISTINCT c.id, c.first_name, c.last_name FROM invoices i LEFT JOIN contacts c ON i.contact_id = c.id ORDER BY c.first_name, c.last_name";
                                            $res_cust = $link->query($sql_cust);
                                            while ($row = $res_cust->fetch_assoc()) {
                                                $fullname = trim($row['first_name'].' '.$row['last_name']);
                                                echo '<option value="'.$row['id'].'">'.esc($fullname ?: 'Unknown').'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <select name="status" id="filter-status" class="form-select">
                                            <option value="">All Status</option>
                                            <?php foreach ($status_opts as $s): ?>
                                                <option value="<?=$s?>"><?=ucfirst($s)?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <input type="date" name="from" id="filter-from" class="form-control datepicker" placeholder="From">
                                    </div>
                                    <div class="col-auto">
                                        <input type="date" name="to" id="filter-to" class="form-control datepicker" placeholder="To">
                                    </div>
                                    <div class="col-auto">
                                        <a href="app-invoicing.php" class="btn btn-success d-flex align-items-center">
                                            <i class="ri-add-line me-1"></i>Create Invoice
                                        </a>
                                    </div>
                                    <div class="col-auto d-flex align-items-center" id="invoice-actions-bar" style="gap:8px; display:none;">
                                        <!-- Edit/Delete will be injected here -->
                                    </div>
                                </form>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="invoice-table">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Invoice #</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Issued</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $sql = "SELECT i.*, c.first_name, c.last_name FROM invoices i
                                                LEFT JOIN contacts c ON i.contact_id = c.id
                                                ORDER BY i.issue_date DESC";
                                        $result = $link->query($sql);
                                        if ($result->num_rows): foreach ($result as $inv): ?>
                                            <tr data-id="<?=$inv['id']?>" style="cursor:pointer">
                                                <td class="fw-bold text-primary"><?=$inv['invoice_number']?></td>
                                                <td><?=esc(trim($inv['first_name'].' '.$inv['last_name']))?></td>
                                                <td>₱<?=number_format($inv['total'],2)?></td>
                                                <td>
                                                    <span class="badge bg-<?=$inv['status']=='paid'?'success':($inv['status']=='sent'?'info':($inv['status']=='draft'?'secondary':'warning')) ?>">
                                                        <?=ucfirst($inv['status'])?>
                                                    </span>
                                                </td>
                                                <td><?=esc($inv['issue_date'])?></td>
                                            </tr>
                                        <?php endforeach; else: ?>
                                            <tr><td colspan="5" class="text-center text-muted">No invoices found.</td></tr>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="no-results" class="text-center text-muted py-4 d-none">No invoices found.</div>
                            </div>
                        </div>
                    </div>
                    <!-- RIGHT: Invoice Preview -->
                    <div class="col-lg-4">
                        <div class="invoice-preview card mb-0">
                            <div class="card-body" id="invoice-list-preview">
                                <div class="text-center text-muted py-5" id="preview-placeholder">
                                    <i class="ri-file-text-line" style="font-size: 2.5rem;"></i>
                                    <div class="mt-3">Select an invoice to preview</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ==== END INVOICE TABLE & PREVIEW ==== -->
            </div>
        </div>
        <?php include 'layouts/footer.php'; ?>
    </div>
</div>
<?php include 'layouts/right-sidebar.php'; ?>
<?php include 'layouts/footer-scripts.php'; ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.min.js"></script>
<script>
// ====== FILTERS AND LIVE SEARCH ======
function loadInvoiceTable(callback) {
    var params = {
        ajax: 1,
        table: 1,
        customer_id: $('#filter-customer').val(),
        search: $('#filter-search').val(),
        status: $('#filter-status').val(),
        from: $('#filter-from').val(),
        to: $('#filter-to').val()
    };
    $.get(window.location.pathname, params, function(html) {
        $('#invoice-table tbody').html(html);
        if (typeof callback === "function") callback();
    });
}
$('#filter-search').on('input', function() { loadInvoiceTable(); });
$('#filter-status').on('change', function() { loadInvoiceTable(); });
$('#filter-customer').on('change', function() { loadInvoiceTable(); });
$('#filter-from, #filter-to').on('change', function() { loadInvoiceTable(); });

// Row click loads invoice preview and updates actions bar
$('#invoice-table tbody').on('click', 'tr[data-id]', function(){
    var id = $(this).data('id');
    $('#invoice-table tr').removeClass('table-active');
    $(this).addClass('table-active');
    $('#invoice-list-preview').html('<div class="text-center text-muted py-5"><span class="spinner-border"></span><div class="mt-2">Loading...</div></div>');
    $.get(window.location.pathname, { ajax: 1, id: id }, function(html){
        $('#invoice-list-preview').html(html);

        // Re-initialize Bootstrap dropdown after AJAX inject
        if (typeof bootstrap !== "undefined" && bootstrap.Dropdown) {
            $('#invoice-list-preview .dropdown-toggle').each(function() {
                new bootstrap.Dropdown(this);
            });
        }

        // Read meta from injected <script type="application/json">
        let meta = $('#invoice-list-preview').find('script[type="application/json"]').html();
        let inv = null;
        try { inv = JSON.parse(meta); } catch(e) {}
        updateInvoiceActionsBar(inv);
    });
});

// Enter to auto-select first row
$('#filter-search').on('keydown', function(e){
    if(e.key === 'Enter') {
        $('#invoice-table tbody tr:first').trigger('click');
    }
});

// Update Edit/Delete links beside Create Invoice
function updateInvoiceActionsBar(inv) {
    const bar = $('#invoice-actions-bar');
    if (!inv || inv.status === 'paid') {
        bar.hide().empty();
        return;
    }
    let links = '';
links  = `<a href="app-invoicing.php?id=${inv.id}" class="btn btn-outline-primary btn-sm me-2"  style="margin-right: 0px !important; id="edit-link"></i>Edit</a>`;
links += `<button type="button" class="btn btn-outline-danger btn-sm" data-id="${inv.id}" id="del-link">Delete</button>`;
    bar.html(links).show();
}

// --- DELETE FUNCTIONALITY ---
$(document).on('click', '#del-link', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    // Show Bootstrap modal for confirmation
    $('#deleteModal').remove(); // Remove if exists
    $('body').append(`
    <div class="modal fade" id="deleteModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-danger">Delete Invoice</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to delete this invoice? This cannot be undone.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirm-delete-btn">Delete</button>
          </div>
        </div>
      </div>
    </div>`);
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();

    $(document).off('click', '#confirm-delete-btn').on('click', '#confirm-delete-btn', function(){
        $.get(window.location.pathname, {ajax: 1, delete_id: id}, function(res){
            var result = {};
            try { result = JSON.parse(res); } catch(e) { result = {}; }
            if (result.success) {
                showAlert('Invoice deleted successfully.', 'success');
                // After table is reloaded, auto-select the first row if exists
                loadInvoiceTable(function() {
                    var $first = $('#invoice-table tbody tr:first');
                    if ($first.length) {
                        $first.trigger('click');
                    } else {
                        // No more invoices: clear preview and actions bar
                        $('#invoice-list-preview').html('<div class="text-center text-muted py-5"><i class="ri-file-text-line" style="font-size: 2.5rem;"></i><div class="mt-3">Select an invoice to preview</div></div>');
                        $('#invoice-actions-bar').hide().empty();
                    }
                });
            } else {
                showAlert(result.msg || 'Delete failed.', 'danger');
            }
            modal.hide();
        });
    });
});

// Alert Helper
function showAlert(msg, type) {
    $('#alert-area').html(`
        <div class="alert alert-`+type+` alert-dismissible fade show alert-fixed-top" role="alert">
          ${msg}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    setTimeout(function(){ $('.alert-fixed-top').alert('close'); }, 4000);
}
</script>
