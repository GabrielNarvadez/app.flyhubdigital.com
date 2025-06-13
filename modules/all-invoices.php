<?php
// modules/all-invoices.php
require_once __DIR__ . '/../layouts/config.php';
function esc($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }

// ---- AJAX HANDLER for Invoice Preview ----
if (isset($_GET['ajax']) && $_GET['ajax'] && isset($_GET['id'])) {
    $inv_id = intval($_GET['id']);
    // Fetch invoice header and contact info
    $stmt = $link->prepare("SELECT i.*, c.first_name, c.last_name, c.email, c.phone_number, c.address 
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

    // If no items, add a dummy
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
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()"><i class="ri-printer-line"></i> Print</button>
        <button class="btn btn-outline-primary btn-sm" onclick="alert('Pretend sending email to <?=esc($inv['email'])?>')"><i class="ri-mail-line"></i> Email</button>
        <a class="btn btn-outline-info btn-sm" id="btn-generate-soa" target="_blank" href="soa-manager.php?customer=<?=urlencode(trim($inv['first_name'].' '.$inv['last_name']))?>"><i class="ri-list-unordered"></i> Generate SOA</a>
    </div>
    <div class="clearfix mb-2">
        <div class="float-start"><img src="/assets/images/logo-dark.png" alt="logo" height="28"></div>
        <div class="float-end"><h3 class="m-0">Invoice</h3></div>
    </div>
    <div class="row align-items-center">
        <div class="col-8">
            <p class="mb-1"><b>Hello, <?=esc(trim($inv['first_name'].' '.$inv['last_name']))?></b></p>
            <p class="text-muted small mb-1">Please find below a cost-breakdown for your transaction.</p>
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
                <?=esc($inv['address'])?><br>
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
    <?php
    exit;
}
// ---- END AJAX HANDLER ----


// --- FILTERS: Get values from GET
$status = $_GET['status'] ?? '';
$date   = $_GET['date']   ?? '';
$where = [];
$params = [];
$types = "";

// Build filter SQL
if ($status && in_array($status, ['draft','sent','paid','void','canceled'])) {
    $where[] = "i.status=?";
    $params[] = $status;
    $types .= "s";
}
if ($date && preg_match('/^\d{4}-\d{2}$/', $date)) {
    $where[] = "DATE_FORMAT(i.issue_date, '%Y-%m')=?";
    $params[] = $date;
    $types .= "s";
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

// For filter dropdowns (status, date)
$status_opts = ['draft','sent','paid','void','canceled'];
$date_opts = [];
$res2 = $link->query("SELECT DISTINCT DATE_FORMAT(issue_date, '%Y-%m') as ym FROM invoices ORDER BY ym DESC");
while ($r = $res2->fetch_assoc()) {
    if ($r['ym']) $date_opts[] = $r['ym'];
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
<div class="row g-4">
    <!-- LEFT: Invoice Table -->
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <form class="row mb-3 gx-2 gy-2 align-items-center" id="filter-form">
                    <div class="col-auto">
                        <select name="status" id="filter-status" class="form-select">
                            <option value="">All Status</option>
                            <?php foreach ($status_opts as $s): ?>
                                <option value="<?=$s?>" <?=($status==$s?'selected':'')?>><?=ucfirst($s)?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <select name="date" id="filter-date" class="form-select">
                            <option value="">All Dates</option>
                            <?php foreach ($date_opts as $d): ?>
                                <option value="<?=$d?>" <?=($date==$d?'selected':'')?>>
                                    <?=date('F Y', strtotime($d.'-01'))?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-outline-primary btn-sm"><i class="ri-filter-line"></i> Filter</button>
                        <?php if ($status||$date): ?>
                            <a href="all-invoices.php" class="btn btn-link btn-sm">Reset</a>
                        <?php endif; ?>
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
                        <?php if (count($invoices)): foreach ($invoices as $inv): ?>
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
    <div class="col-lg-6">
        <div class="invoice-preview card mb-0">
            <div class="card-body" id="invoice-list-preview">
                <div class="text-center text-muted py-5" id="preview-placeholder">
                    <i class="ri-file-text-line" style="font-size: 2.5rem;"></i>
                    <div class="mt-3">Select an invoice to preview</div>
                </div>
                <!-- Preview filled by JS -->
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$('#filter-form').on('submit', function(e){
    e.preventDefault();
    var params = $(this).serialize();
    window.location = "all-invoices.php?" + params;
});
$('#invoice-table tbody').on('click', 'tr[data-id]', function(){
    var id = $(this).data('id');
    $('#invoice-table tr').removeClass('table-active');
    $(this).addClass('table-active');
    $('#invoice-list-preview').html('<div class="text-center text-muted py-5"><span class="spinner-border"></span><div class="mt-2">Loading...</div></div>');
    $.get('modules/all-invoices.php', { ajax: 1, id: id }, function(html){
        $('#invoice-list-preview').html(html);
    });
});
</script>
