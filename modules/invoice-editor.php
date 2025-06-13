<?php
require_once __DIR__ . '/../layouts/config.php';
function esc($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }

// --- Fetch contacts for dropdown ---
$contacts = [];
$res = $link->query("SELECT id, first_name, last_name, phone_number, address FROM contacts ORDER BY first_name, last_name");
while ($c = $res->fetch_assoc()) $contacts[] = $c;

// --- Fetch products for dropdown ---
$products = [];
$res = $link->query("SELECT id, name, description, price FROM products ORDER BY name");
while ($p = $res->fetch_assoc()) $products[] = $p;

// --- Default empty invoice (for new) ---
$invoice = [
    'invoice_number' => 'INV-'.date('Ymd').rand(1000,9999),
    'issue_date' => date('Y-m-d'),
    'due_date' => date('Y-m-d', strtotime('+7 days')),
    'status' => 'draft',
    'contact_id' => '',
    'billing_address' => '',
    'shipping_address' => '',
    'phone' => '',
    'notes' => '',
    'subtotal' => 0.00,
    'discount_total' => 0.00,
    'tax_total' => 0.00,
    'total' => 0.00,
    'line_items' => []
];

// --- On POST, handle save/confirm/pay/reset ---
$is_confirmed = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'confirm') {
        $is_confirmed = true;
    }
    if (isset($_POST['action']) && $_POST['action'] === 'reset') {
        $is_confirmed = false;
    }
    if (isset($_POST['action']) && $_POST['action'] === 'pay') {
        $is_confirmed = true;
        $invoice['status'] = 'paid';
    }
    // Safe array merge using null coalescing (no warnings if not set)
    $invoice = array_merge($invoice, [
        'invoice_number'    => $_POST['invoice_number'] ?? $invoice['invoice_number'],
        'issue_date'        => $_POST['issue_date'] ?? $invoice['issue_date'],
        'due_date'          => $_POST['due_date'] ?? $invoice['due_date'],
        'contact_id'        => $_POST['contact_id'] ?? $invoice['contact_id'],
        'billing_address'   => $_POST['billing_address'] ?? $invoice['billing_address'],
        'shipping_address'  => $_POST['shipping_address'] ?? $invoice['shipping_address'],
        'phone'             => $_POST['phone'] ?? $invoice['phone'],
        'notes'             => $_POST['notes'] ?? $invoice['notes'],
        'subtotal'          => $_POST['subtotal'] ?? $invoice['subtotal'],
        'discount_total'    => $_POST['discount_total'] ?? $invoice['discount_total'],
        'tax_total'         => $_POST['tax_total'] ?? $invoice['tax_total'],
        'total'             => $_POST['total'] ?? $invoice['total'],
        'line_items'        => $_POST['line_items'] ?? $invoice['line_items'],
    ]);
}
?>
<div class="row g-4">
    <!-- LEFT PANEL: Invoice Editor Form (Card) -->
    <div class="col-lg-6">
        <div class="card shadow-sm mb-0">
            <div class="card-body">
                <a href="invoicing.php" class="btn btn-link mb-2"><i class="ri-arrow-left-line"></i> Back to all invoices</a>
                <form id="invoice-form" method="post" autocomplete="off">
                    <div class="row mb-2">
                        <div class="col">
                            <h5 class="mb-0 fw-bold">Invoice Editor</h5>
                        </div>
                        <div class="col text-end">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-print"><i class="ri-printer-line"></i> Print PDF</button>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Invoice #</label>
                        <input type="text" name="invoice_number" class="form-control" id="invoice_number"
                            value="<?=esc($invoice['invoice_number'])?>" <?= $is_confirmed ? 'readonly' : '' ?> required>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label class="form-label">Invoice Date</label>
                            <input type="date" name="issue_date" class="form-control" value="<?=esc($invoice['issue_date'])?>" <?= $is_confirmed ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date" class="form-control" value="<?=esc($invoice['due_date'])?>" <?= $is_confirmed ? 'readonly' : '' ?>>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Customer</label>
                        <select name="contact_id" id="contact_id" class="form-select" <?= $is_confirmed ? 'disabled' : '' ?> required>
                            <option value="">Select Customer</option>
                            <?php foreach ($contacts as $c): ?>
                                <option value="<?=$c['id']?>" data-phone="<?=esc($c['phone_number'])?>" data-address="<?=esc($c['address'])?>"
                                    <?=($invoice['contact_id']==$c['id']?'selected':'')?>><?=esc($c['first_name'].' '.$c['last_name'])?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Billing Address</label>
                        <textarea name="billing_address" id="billing_address" class="form-control" <?= $is_confirmed ? 'readonly' : '' ?>><?=esc($invoice['billing_address'])?></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Shipping Address</label>
                        <textarea name="shipping_address" id="shipping_address" class="form-control" <?= $is_confirmed ? 'readonly' : '' ?>><?=esc($invoice['shipping_address'])?></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="<?=esc($invoice['phone'])?>" <?= $is_confirmed ? 'readonly' : '' ?>>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" <?= $is_confirmed ? 'readonly' : '' ?>><?=esc($invoice['notes'])?></textarea>
                    </div>
                    <!-- LINE ITEMS -->
                    <div class="mb-2">
                        <h6 class="fw-bold">Line Items</h6>
                        <table class="table table-bordered table-sm mb-2" id="line-items-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:32%">Product</th>
                                    <th>Description</th>
                                    <th style="width:10%">Qty</th>
                                    <th style="width:14%">Unit Price</th>
                                    <th style="width:14%">Amount</th>
                                    <th style="width:7%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- JS will handle items -->
                            </tbody>
                        </table>
                        <?php if (!$is_confirmed): ?>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-item"><i class="ri-add-line"></i> Add Item</button>
                        <?php endif; ?>
                    </div>
                    <div class="row mb-2">
                        <div class="col text-end small">
                            <label>Subtotal:</label>
                        </div>
                        <div class="col-3">
                            <input type="text" name="subtotal" id="subtotal" class="form-control form-control-sm text-end" value="<?=number_format($invoice['subtotal'],2)?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col text-end small">
                            <label>Discount:</label>
                        </div>
                        <div class="col-3">
                            <input type="text" name="discount_total" id="discount_total" class="form-control form-control-sm text-end" value="<?=number_format($invoice['discount_total'],2)?>" <?= $is_confirmed ? 'readonly' : '' ?>>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col text-end small">
                            <label>Tax:</label>
                        </div>
                        <div class="col-3">
                            <input type="text" name="tax_total" id="tax_total" class="form-control form-control-sm text-end" value="<?=number_format($invoice['tax_total'],2)?>" <?= $is_confirmed ? 'readonly' : '' ?>>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col text-end small fw-bold">
                            <label>Total:</label>
                        </div>
                        <div class="col-3">
                            <input type="text" name="total" id="total" class="form-control form-control-sm text-end fw-bold" value="<?=number_format($invoice['total'],2)?>" readonly>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-2">
                        <button type="submit" name="action" value="confirm" class="btn btn-primary" <?= $is_confirmed ? 'disabled' : '' ?>>Confirm</button>
                        <button type="submit" name="action" value="pay" id="pay-btn" class="btn btn-success d-none">Pay</button>
                        <button type="submit" name="action" value="reset" id="reset-btn" class="btn btn-secondary d-none">Reset to Draft</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- RIGHT PANEL: Invoice Preview (Card) -->
    <div class="col-lg-6">
        <div class="card invoice-preview mb-0">
            <div class="card-body" id="invoice-preview-panel">
                <!-- JS preview will be loaded here -->
            </div>
        </div>
    </div>
</div>
<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
const products = <?=json_encode($products)?>;
const contacts = <?=json_encode($contacts)?>;
let lineItems = [];
let isConfirmed = <?= $is_confirmed ? 'true' : 'false' ?>;

// --- Populate line items table ---
function renderLineItemsTable() {
    let tbody = $('#line-items-table tbody');
    tbody.empty();
    let subtotal = 0;
    if (!lineItems.length) {
        tbody.append('<tr class="text-muted"><td colspan="6" class="text-center">No items added</td></tr>');
    } else {
        lineItems.forEach((item, i) => {
            let amount = parseFloat(item.quantity) * parseFloat(item.unit_price);
            subtotal += amount;
            tbody.append(`<tr>
                <td>
                    <select class="form-select form-select-sm product-dropdown" data-idx="${i}" ${isConfirmed ? 'disabled' : ''}>
                        <option value="">Select product</option>
                        ${products.map(p => `<option value="${p.id}" data-price="${p.price}" data-desc="${esc(p.description)}" ${item.product_id==p.id?'selected':''}>${esc(p.name)}</option>`).join('')}
                    </select>
                </td>
                <td><input type="text" class="form-control form-control-sm desc-input" value="${esc(item.description)}" ${isConfirmed ? 'readonly' : ''}></td>
                <td><input type="number" class="form-control form-control-sm qty-input" min="1" value="${item.quantity}" ${isConfirmed ? 'readonly' : ''}></td>
                <td><input type="number" class="form-control form-control-sm unit-input" min="0" value="${item.unit_price}" step="0.01" ${isConfirmed ? 'readonly' : ''}></td>
                <td class="text-end align-middle">₱${amount.toFixed(2)}</td>
                <td>${isConfirmed ? '' : `<button type="button" class="btn btn-link text-danger btn-sm p-0 remove-item" data-idx="${i}"><i class="ri-close-line"></i></button>`}</td>
            </tr>`);
        });
    }
    $('#subtotal').val(subtotal.toFixed(2));
    recalcTotals();
}

// --- Add new line item ---
$('#add-item').on('click', function(){
    lineItems.push({
        product_id: '',
        description: '',
        quantity: 1,
        unit_price: 0.00
    });
    renderLineItemsTable();
    renderPreview();
});

// --- Remove line item ---
$('#line-items-table').on('click', '.remove-item', function(){
    const idx = $(this).data('idx');
    lineItems.splice(idx,1);
    renderLineItemsTable();
    renderPreview();
});

// --- Update line item (qty, unit price, desc) ---
$('#line-items-table').on('input change', '.qty-input, .unit-input, .desc-input, .product-dropdown', function(){
    const tr = $(this).closest('tr');
    const idx = tr.index();
    let item = lineItems[idx];
    if ($(this).hasClass('qty-input')) item.quantity = $(this).val();
    if ($(this).hasClass('unit-input')) item.unit_price = $(this).val();
    if ($(this).hasClass('desc-input')) item.description = $(this).val();
    if ($(this).hasClass('product-dropdown')) {
        const sel = $(this).find('option:selected');
        item.product_id = $(this).val();
        item.description = sel.data('desc') || '';
        item.unit_price = sel.data('price') || 0.00;
        tr.find('.desc-input').val(item.description);
        tr.find('.unit-input').val(item.unit_price);
    }
    renderLineItemsTable();
    renderPreview();
});

// --- Contact auto-fill ---
$('#contact_id').on('change', function(){
    const id = $(this).val();
    const contact = contacts.find(c => c.id == id);
    if (contact) {
        $('#phone').val(contact.phone_number || '');
        $('#billing_address').val(contact.address || '');
        $('#shipping_address').val(contact.address || '');
    }
    renderPreview();
});

// --- Form field changes: live update preview & totals ---
$('#invoice-form').on('input change', 'input, textarea, select', function(){
    renderPreview();
    recalcTotals();
});

// --- Totals calculation ---
function recalcTotals() {
    let subtotal = parseFloat($('#subtotal').val()) || 0;
    let discount = parseFloat($('#discount_total').val()) || 0;
    let tax = parseFloat($('#tax_total').val()) || 0;
    let total = subtotal - discount + tax;
    $('#total').val(total.toFixed(2));
}

// --- Invoice Preview ---
function renderPreview() {
    const form = $('#invoice-form');
    const invoice_number = form.find('[name="invoice_number"]').val();
    const issue_date = form.find('[name="issue_date"]').val();
    const due_date = form.find('[name="due_date"]').val();
    const status = isConfirmed ? (form.find('[name="pay"]').length ? 'paid' : 'confirmed') : 'draft';
    const contact_id = form.find('[name="contact_id"]').val();
    const contact = contacts.find(c => c.id == contact_id) || {};
    const billing_address = form.find('[name="billing_address"]').val();
    const shipping_address = form.find('[name="shipping_address"]').val();
    const phone = form.find('[name="phone"]').val();
    const notes = form.find('[name="notes"]').val();
    const subtotal = $('#subtotal').val();
    const discount_total = $('#discount_total').val();
    const tax_total = $('#tax_total').val();
    const total = $('#total').val();

    let previewHtml = `
    <div class="clearfix mb-2">
        <div class="float-start"><img src="/assets/images/logo-dark.png" alt="logo" height="28"></div>
        <div class="float-end"><h3 class="m-0">Invoice</h3></div>
    </div>
    <div class="row align-items-center">
        <div class="col-8">
            <p class="mb-1"><b>Hello, ${esc(contact.first_name||'')} ${esc(contact.last_name||'')}</b></p>
            <p class="text-muted small mb-1">Please find below a cost-breakdown for your transaction.</p>
        </div>
        <div class="col-4 text-end">
            <div class="mb-1 small"><strong>Status:</strong> <span class="badge bg-${status=='paid'?'success':(status=='sent'?'info':(status=='draft'?'secondary':'warning'))}">${status.charAt(0).toUpperCase()+status.slice(1)}</span></div>
            <div class="mb-1 small"><strong>Invoice #:</strong> ${invoice_number}</div>
        </div>
    </div>
    <div class="row mt-3 small">
        <div class="col-6">
            <h6 class="fw-bold">Billing Address</h6>
            <address class="mb-0">
                ${esc(contact.first_name||'')} ${esc(contact.last_name||'')}<br>
                ${esc(billing_address)}<br>
                <abbr title="Phone">P:</abbr> ${esc(phone)}
            </address>
        </div>
        <div class="col-6">
            <h6 class="fw-bold">Shipping Address</h6>
            <address class="mb-0">${esc(shipping_address)}</address>
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
                ${lineItems.length ? lineItems.map((item,i)=>`
                    <tr>
                        <td>${i+1}</td>
                        <td>${esc(products.find(p=>p.id==item.product_id)?.name||'')}</td>
                        <td>${esc(item.description)}</td>
                        <td>${item.quantity}</td>
                        <td>₱${parseFloat(item.unit_price).toFixed(2)}</td>
                        <td>₱${(parseFloat(item.quantity)*parseFloat(item.unit_price)).toFixed(2)}</td>
                    </tr>
                `).join('') : `<tr><td colspan="6" class="text-muted text-center">No items added</td></tr>`}
            </tbody>
        </table>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <div class="float-end small">
                <p><b>Sub-total:</b> <span class="float-end">₱${parseFloat(subtotal).toFixed(2)}</span></p>
                <p><b>Discount:</b> <span class="float-end">₱${parseFloat(discount_total).toFixed(2)}</span></p>
                <p><b>Tax:</b> <span class="float-end">₱${parseFloat(tax_total).toFixed(2)}</span></p>
                <h5 class="mt-2">₱${parseFloat(total).toFixed(2)}</h5>
            </div>
        </div>
    </div>
    <div class="mt-3"><small class="text-muted">${esc(notes)}</small></div>
    `;
    $('#invoice-preview-panel').html(previewHtml);
}

// --- Print only the right panel ---
$('#btn-print').on('click', function(){
    let printContent = $('#invoice-preview-panel').html();
    let win = window.open('', '_blank', 'width=900,height=600');
    win.document.write(`
        <html>
        <head>
            <title>Print Invoice</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
            <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
        </head>
        <body style="padding:2em;">
        <div class="container">
            ${printContent}
        </div>
        </body>
        </html>
    `);
    win.document.close();
    win.focus();
    setTimeout(()=>win.print(),500);
});

// --- Utility: Escape HTML (for JS-generated content) ---
function esc(str){
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, function(m){
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m];
    });
}

// --- On document ready ---
$(function(){
    renderLineItemsTable();
    renderPreview();
    if (isConfirmed) {
        $('#pay-btn').removeClass('d-none');
        $('#reset-btn').removeClass('d-none');
        $('#invoice-form input, #invoice-form textarea, #invoice-form select').attr('readonly', true).attr('disabled', true);
    }
});
</script>
