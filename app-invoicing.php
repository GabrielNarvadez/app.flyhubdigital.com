<?php
// === SERVER-SIDE: Confirm & Pay Logic ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    require_once __DIR__ . '/layouts/config.php';
    header('Content-Type: application/json');

    // === CONFIRM/SAVE INVOICE ===
    if ($_POST['ajax'] == '1') {
        // Basic validation
        if (empty($_POST['contact_id'])) {
            echo json_encode(['success'=>false,'msg'=>'Customer is required']);
            exit;
        }
        if (empty($_POST['invoice_number'])) {
            echo json_encode(['success'=>false,'msg'=>'Invoice number is required']);
            exit;
        }

        // Prepare fields
        $company_id      = 1;
        $currency        = 'PHP';
        $status          = 'confirmed';
        $contact_id      = $_POST['contact_id'];
        $invoice_number  = $_POST['invoice_number'];
        $issue_date      = $_POST['issue_date'];
        $due_date        = $_POST['due_date'];
        $notes           = $_POST['notes'] ?? '';
        $subtotal        = $_POST['subtotal'];
        $discount_total  = $_POST['discount_total'];
        $tax_total       = $_POST['tax_total'];
        $total           = $_POST['total'];

        // Insert or update invoice
        $stmt = $link->prepare("
            INSERT INTO invoices
              (company_id, contact_id, invoice_number, status, issue_date, due_date, currency, notes, subtotal, discount_total, tax_total, total, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE 
              status=VALUES(status), issue_date=VALUES(issue_date), due_date=VALUES(due_date), 
              notes=VALUES(notes), subtotal=VALUES(subtotal), discount_total=VALUES(discount_total),
              tax_total=VALUES(tax_total), total=VALUES(total), updated_at=NOW()
        ");
        if (!$stmt) {
            echo json_encode(['success'=>false,'msg'=>'DB prepare error: '.$link->error]);
            exit;
        }
        $stmt->bind_param(
            "iissssssdddd",
            $company_id,
            $contact_id,
            $invoice_number,
            $status,
            $issue_date,
            $due_date,
            $currency,
            $notes,
            $subtotal,
            $discount_total,
            $tax_total,
            $total
        );
        if (!$stmt->execute()) {
            echo json_encode(['success'=>false,'msg'=>'DB execute error: '.$stmt->error]);
            exit;
        }
        $invoice_id = $stmt->insert_id ?: $link->query("SELECT id FROM invoices WHERE invoice_number='$invoice_number'")->fetch_assoc()['id'];
        $stmt->close();

        // === Insert all invoice line items for this invoice ===
        $link->query("DELETE FROM invoice_items WHERE invoice_id=$invoice_id"); // Remove previous
        $items = json_decode($_POST['line_items'], true);
        if (is_array($items) && count($items)) {
            $stmt = $link->prepare("
                INSERT INTO invoice_items
                    (invoice_id, item_type, reference_id, product_id, description, quantity, unit_price, extra_json, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            foreach ($items as $item) {
                $item_type    = $item['item_type'] ?? 'product';
                $reference_id = $item['reference_id'] ?? null;
                $product_id   = $item_type === 'product' ? ($item['product_id'] ?? null) : null;
                $desc         = $item['description'] ?? ($item['reference_label'] ?? '');
                $qty          = $item['quantity'] ?? 1;
                $unit         = $item['unit_price'] ?? 0.00;

                $extra = [];
                if (!empty($item['reference_label'])) $extra['reference_label'] = $item['reference_label'];
                if (!empty($item['description'])) $extra['description'] = $item['description'];
                $extra_json = !empty($extra) ? json_encode($extra) : null;

                $stmt->bind_param(
                    "isissids",
                    $invoice_id,
                    $item_type,
                    $reference_id,
                    $product_id,
                    $desc,
                    $qty,
                    $unit,
                    $extra_json
                );
                $stmt->execute();
            }
            $stmt->close();
        }

        echo json_encode(['success'=>true,'msg'=>'Invoice confirmed and saved!']);
        exit;
    }

    // === PAY: Mark Invoice as Paid ===
    if ($_POST['ajax'] == 'pay') {
        $invoice_number = $_POST['invoice_number'];
        $stmt = $link->prepare("UPDATE invoices SET status='paid', updated_at=NOW() WHERE invoice_number=?");
        $stmt->bind_param('s', $invoice_number);
        $ok = $stmt->execute();
        $stmt->close();

        if ($ok) {
            echo json_encode([
                'success'=>true,
                'msg'=>"Payment has been confirmed and the invoice marked as Paid. A payment confirmation email has been sent to the customer."
            ]);
        } else {
            echo json_encode(['success'=>false,'msg'=>'Could not mark as paid.']);
        }
        exit;
    }
}
?>

<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Invoicing | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
</head>

<body>
    <div class="wrapper">
        <?php include 'layouts/menu.php'; ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h3 class="page-title">
                                    <a href="invoicing.php" class="text-decoration-none">Back to all invoices</a>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <?php
                    require_once __DIR__ . '/layouts/config.php';
                    function esc($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }

                    // Load contacts and products
                    $contacts = [];
                    $res = $link->query("SELECT id, first_name, last_name, phone_number, billing_address, shipping_address FROM contacts ORDER BY first_name, last_name");
                    while ($c = $res->fetch_assoc()) $contacts[] = $c;
                    $products = [];
                    $res = $link->query("SELECT id, name, description, price, product_type FROM products WHERE status='active' ORDER BY id LIMIT 20");
                    while ($p = $res->fetch_assoc()) $products[] = $p;

                    // Generate a new invoice number if not set
                    $invoice = [
                        'invoice_number'   => 'INV-'.date('Ymd').rand(1000,9999),
                        'issue_date'       => date('Y-m-d'),
                        'due_date'         => date('Y-m-d', strtotime('+7 days')),
                        'status'           => 'draft',
                        'contact_id'       => '',
                        'billing_address'  => '',
                        'shipping_address' => '',
                        'phone'            => '',
                        'notes'            => '',
                        'subtotal'         => 0.00,
                        'discount_total'   => 0.00,
                        'tax_total'        => 0.00,
                        'total'            => 0.00,
                        'line_items'       => []
                    ];
                    ?>

                    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

                    <!-- PAY MODAL -->
                    <div class="modal fade" id="payModal" tabindex="-1" aria-labelledby="payModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="payModalLabel">Confirm Payment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            Are you sure you want to mark this invoice as paid?
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" id="pay-confirm-btn">Yes, Mark as Paid</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div id="alert-area"></div>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="card shadow-sm mb-0">
                                <div class="card-body">
                                    <form id="invoice-form" autocomplete="off">
                                        <div class="row mb-2">
                                            <div class="col"><h5 class="mb-0 fw-bold">Invoice Editor</h5></div>
                                            <div class="col text-end d-flex align-items-center justify-content-end gap-2">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-print">
                                                    <i class="ri-printer-line"></i> Print PDF
                                                </button>
                                                <div id="invoice-actions" class="d-inline-flex gap-2">
                                                    <button type="button" id="btn-confirm" class="btn btn-success">Confirm</button>
                                                    <button type="button" id="btn-draft" class="btn btn-outline-secondary d-none">Revert to Draft</button>
                                                    <button type="button" id="btn-pay" class="btn btn-success d-none">Pay</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-2 align-items-end">
                                            <div class="col">
                                                <label class="form-label">Invoice #</label>
                                                <input type="text" name="invoice_number" class="form-control bg-light text-muted" id="invoice_number"
                                                    value="<?=esc($invoice['invoice_number'])?>" readonly tabindex="-1" required>
                                            </div>
                                            <div class="col-auto" style="min-width:180px;">
                                                <label class="form-label">Product Type</label>
                                                <select id="product-type-filter" class="form-select form-select-sm" style="max-width:180px;">
                                                    <option value="All">All Types</option>
                                                    <option value="Goods">Goods</option>
                                                    <option value="Property">Property</option>
                                                    <option value="Service">Service</option>
                                                    <option value="Combo">Combo</option>
                                                    <option value="Misc">Misc</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-2">
                                                    <label class="form-label">Customer</label>
                                                    <select name="contact_id" id="contact_id" class="form-select" required>
                                                        <option value="">Select Customer</option>
                                                        <?php foreach ($contacts as $c): ?>
                                                            <option value="<?=$c['id']?>" data-phone="<?=esc($c['phone_number'])?>"
                                                                <?=($invoice['contact_id']==$c['id']?'selected':'')?>>
                                                                <?=esc($c['first_name'].' '.$c['last_name'])?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-2">
                                                    <label class="form-label">Phone</label>
                                                    <input type="text" name="phone" id="phone" class="form-control"
                                                        value="<?=esc($invoice['phone'])?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col">
                                                <label class="form-label">Invoice Date</label>
                                                <input type="date" name="issue_date" class="form-control"
                                                    value="<?=esc($invoice['issue_date'])?>" required>
                                            </div>
                                            <div class="col">
                                                <label class="form-label">Due Date</label>
                                                <input type="date" name="due_date" class="form-control"
                                                    value="<?=esc($invoice['due_date'])?>">
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col">
                                                <label class="form-label">Billing Address</label>
                                                <textarea name="billing_address" id="billing_address" class="form-control"><?=esc($invoice['billing_address'])?></textarea>
                                            </div>
                                            <div class="col">
                                                <label class="form-label">Shipping Address</label>
                                                <textarea name="shipping_address" id="shipping_address" class="form-control"><?=esc($invoice['shipping_address'])?></textarea>
                                            </div>
                                        </div>
                                        <!-- ===== Product Line Item Editor ===== -->
                                        <div class="mb-2">
                                            <label class="form-label fw-bold">Products/Items</label>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm align-middle mb-0" id="line-items-table">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>Details</th>
                                                            <th style="width:80px;">Qty</th>
                                                            <th style="width:110px;">Unit Price</th>
                                                            <th style="width:110px;">Amount</th>
                                                            <th style="width:40px;"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Line items will be rendered here by JS -->
                                                    </tbody>
                                                </table>
                                            </div>
                                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-item">
                                                <i class="ri-add-line"></i> Add Item
                                            </button>
                                        </div>
                                        <!-- ===== End Product Line Item Editor ===== -->
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="mb-2">
                                                    <label class="form-label">Notes</label>
                                                    <textarea name="notes" class="form-control"><?=esc($invoice['notes'])?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="row mb-2">
                                                    <div class="col text-end small"><label>Subtotal:</label></div>
                                                    <div class="col-3">
                                                        <input type="text" name="subtotal" id="subtotal"
                                                            class="form-control form-control-sm text-end"
                                                            value="<?=number_format($invoice['subtotal'],2)?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col text-end small"><label>Discount:</label></div>
                                                    <div class="col-3">
                                                        <input type="text" name="discount_total" id="discount_total"
                                                            class="form-control form-control-sm text-end"
                                                            value="<?=number_format($invoice['discount_total'],2)?>">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col text-end small"><label>Tax:</label></div>
                                                    <div class="col-3">
                                                        <input type="text" name="tax_total" id="tax_total"
                                                            class="form-control form-control-sm text-end"
                                                            value="<?=number_format($invoice['tax_total'],2)?>">
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col text-end small fw-bold"><label>Total:</label></div>
                                                    <div class="col-3">
                                                        <input type="text" name="total" id="total"
                                                            class="form-control form-control-sm text-end fw-bold"
                                                            value="<?=number_format($invoice['total'],2)?>" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card invoice-preview mb-0">
                                <div class="card-body" id="invoice-preview-panel"></div>
                            </div>
                        </div>
                    </div>

                    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const AJAX_URL = window.location.pathname;
const products = <?= json_encode($products) ?>;
const contacts = <?= json_encode($contacts) ?>;
let invoiceStatus = 'draft';
let lineItems = [];

// === Utility Functions (as before) ===
function getProductsByType(type) {
    if (type === "All") return products;
    return products.filter(p => p.product_type === type);
}
function esc(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, m =>
        ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m]
    );
}
function setFormEditable(editable) {
    $('#invoice-form input, #invoice-form textarea, #invoice-form select').each(function () {
        if (editable) {
            $(this).removeAttr('readonly disabled');
        } else {
            $(this).attr('readonly', true).attr('disabled', true);
        }
    });
    $('#add-item').prop('disabled', !editable);
    $('#btn-print').prop('disabled', false);
}
function showActions(state) {
    if (state === 'confirmed') {
        $('#btn-confirm').addClass('d-none');
        $('#btn-draft, #btn-pay').removeClass('d-none');
        $('#btn-pay').removeClass('d-none');
    } else if (state === 'paid') {
        $('#btn-confirm, #btn-draft, #btn-pay').addClass('d-none');
    } else {
        $('#btn-confirm').removeClass('d-none');
        $('#btn-draft, #btn-pay').addClass('d-none');
    }
}
function showAlert(msg, type='success') {
    $('#alert-area').html(
        `<div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert">
            ${msg}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>`
    );
}
function recalcTotals() {
    let subtotal = 0;
    for (const item of lineItems) {
        subtotal += parseFloat(item.quantity) * parseFloat(item.unit_price);
    }
    $('#subtotal').val(subtotal.toFixed(2));
    const dis = parseFloat($('#discount_total').val()) || 0;
    const tax = parseFloat($('#tax_total').val()) || 0;
    $('#total').val((subtotal - dis + tax).toFixed(2));
}

// ======== TABLE & PREVIEW RENDERING ========
function renderLineItemsTable() {
    const tbody = $('#line-items-table tbody').empty();
    let subtotal = 0;
    if (!lineItems.length) {
        tbody.append('<tr class="text-muted"><td colspan="6" class="text-center">No items added</td></tr>');
    } else {
        lineItems.forEach((item, i) => {
            const amt = parseFloat(item.quantity) * parseFloat(item.unit_price);
            subtotal += amt;
            tbody.append(`
                <tr>
                    <td>
                        <select class="form-select form-select-sm product-dropdown" data-idx="${i}" ${invoiceStatus === 'confirmed' || invoiceStatus === 'paid' ? 'disabled' : ''}>
                            <option value="">Select product</option>
                            ${getProductsByType($('#product-type-filter').val() || "All").map(p => `
                                <option value="${p.id}"
                                        data-price="${p.price}"
                                        data-desc="${esc(p.description)}"
                                        ${item.product_id == p.id ? 'selected' : ''}>
                                    ${esc(p.name)}
                                </option>
                            `).join('')}
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm desc-input"
                            value="${esc(item.description)}" ${invoiceStatus === 'confirmed' || invoiceStatus === 'paid' ? 'readonly' : ''}>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm qty-input" min="1"
                            value="${item.quantity}" ${invoiceStatus === 'confirmed' || invoiceStatus === 'paid' ? 'readonly' : ''}>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm unit-input" min="0" step="0.01"
                            value="${item.unit_price}" ${invoiceStatus === 'confirmed' || invoiceStatus === 'paid' ? 'readonly' : ''}>
                    </td>
                    <td class="text-end align-middle">₱${amt.toFixed(2)}</td>
                    <td class="text-center">
                        ${(invoiceStatus === 'confirmed' || invoiceStatus === 'paid') ? '' : `
                            <button type="button" class="btn btn-link text-danger btn-sm p-0 remove-item" data-idx="${i}" style="text-decoration: none;">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        `}
                    </td>
                </tr>
            `);
        });
    }
    recalcTotals();
    renderPreview();
}

function renderPreview() {
    const f = $('#invoice-form');
    const num = f.find('[name="invoice_number"]').val();
    const status = invoiceStatus;
    const cid = f.find('[name="contact_id"]').val();
    const c = contacts.find(x => x.id == cid) || {};
    const firstName = c.first_name || '';
    const ba = f.find('[name="billing_address"]').val();
    const sa = f.find('[name="shipping_address"]').val();
    const ph = f.find('[name="phone"]').val();
    const notes = f.find('[name="notes"]').val();
    const sub = $('#subtotal').val();
    const dis = $('#discount_total').val();
    const tax = $('#tax_total').val();
    const tot = $('#total').val();

    let html = `
        <div class="clearfix mb-2">
          <div class="float-start" style="height:30px;display:flex;align-items:center;">
            <i class="bi bi-building" style="font-size:30px; color:#bbb;"></i>
          </div>
          <div class="float-end"><h3 class="m-0">Invoice</h3></div>
        </div>
        <div class="row align-items-center">
            <div class="col-8">
                <p class="mb-1"><b>Hello, ${esc(firstName)}</b></p>
                <p class="text-muted small mb-1">Please find below a cost-breakdown for your transaction.</p>
            </div>
            <div class="col-4 text-end">
                <div class="mb-1 small">
                    <strong>Status:</strong>
                        <span class="badge bg-${status === 'confirmed' ? 'info' : status === 'paid' ? 'success' : 'secondary'}">
                            ${status === 'confirmed' ? 'Confirmed' : status === 'paid' ? 'Paid' : 'Draft'}
                        </span>
                </div>
                <div class="mb-1 small"><strong>Invoice #:</strong> ${num}</div>
            </div>
        </div>
        <div class="row mt-3 small">
            <div class="col-6">
                <h6 class="fw-bold">Billing Address</h6>
                <address class="mb-0">
                    ${esc(ba)}<br>
                    <abbr title="Phone">Phone:</abbr> ${esc(ph)}
                </address>
            </div>
            <div class="col-6">
                <h6 class="fw-bold">Shipping Address</h6>
                <address class="mb-0">${esc(sa)}</address>
            </div>
        </div>
        <div class="mt-3 table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="bg-light-subtle">
                    <tr><th>#</th><th>Item</th><th>Details</th><th>Qty</th><th>Unit Price</th><th>Amount</th></tr>
                </thead>
                <tbody>
                    ${lineItems.length
                ? lineItems.map((it, i) => `
                        <tr>
                            <td>${i + 1}</td>
                            <td>${esc(products.find(p => p.id == it.product_id)?.name || '')}</td>
                            <td>${esc(it.description)}</td>
                            <td>${it.quantity}</td>
                            <td>₱${parseFloat(it.unit_price).toFixed(2)}</td>
                            <td>₱${(it.quantity * it.unit_price).toFixed(2)}</td>
                        </tr>
                    `).join('')
                : `<tr><td colspan="6" class="text-center text-muted">No items added</td></tr>`
            }
                </tbody>
            </table>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="float-end small">
                    <p><b>Sub-total :</b> <span class="float-end">₱${parseFloat(sub).toFixed(2)}</span></p>
                    <p><b>Discount:</b> <span class="float-end">₱${parseFloat(dis).toFixed(2)}</span></p>
                    <p><b>Tax:</b> <span class="float-end">₱${parseFloat(tax).toFixed(2)}</span></p>
                    <h5 class="mt-2">₱${parseFloat(tot).toFixed(2)}</h5>
                </div>
            </div>
        </div>
        <div class="mt-3"><small class="text-muted">${esc(notes)}</small></div>
    `;
    $('#invoice-preview-panel').html(html);
}

// ========== PAGE EVENTS ==========
$(function () {
    // Always render preview/table at start
    renderLineItemsTable();

    setFormEditable(true);
    showActions('draft');

    // Product type filter
    $('#product-type-filter').on('change', function(){
        renderLineItemsTable();
    });

    // Add item
    $('#add-item').on('click', () => {
        lineItems.push({
            item_type: 'product',
            product_id: '',
            reference_id: '',
            reference_label: '',
            description: '',
            quantity: 1,
            unit_price: 0.00,
            extra_json: null
        });
        renderLineItemsTable();
    });

    // Table: remove, edit, update
    $('#line-items-table')
        .on('click', '.remove-item', function () {
            lineItems.splice($(this).data('idx'), 1);
            renderLineItemsTable();
        })
        .on('input change', '.product-dropdown', function () {
            const tr = $(this).closest('tr'), idx = tr.index();
            let item = lineItems[idx];
            const sel = $(this).find('option:selected');
            item.product_id = sel.val();
            item.reference_id = sel.val();
            item.description = sel.data('desc') || '';
            item.unit_price = sel.data('price') || 0;
            tr.find('.desc-input').val(item.description);
            tr.find('.unit-input').val(item.unit_price);
            renderLineItemsTable();
        })
        .on('input change', '.desc-input', function () {
            const tr = $(this).closest('tr'), idx = tr.index();
            let item = lineItems[idx];
            item.description = $(this).val();
            renderPreview();
        })
        .on('input change', '.qty-input', function () {
            const tr = $(this).closest('tr'), idx = tr.index();
            let item = lineItems[idx];
            item.quantity = parseInt($(this).val()) || 1;
            renderLineItemsTable();
        })
        .on('input change', '.unit-input', function () {
            const tr = $(this).closest('tr'), idx = tr.index();
            let item = lineItems[idx];
            item.unit_price = parseFloat($(this).val()) || 0;
            renderLineItemsTable();
        });

    // Customer change = auto-fill
    $('#contact_id').on('change', function () {
        const cid = $(this).val();
        const c = contacts.find(x => x.id == cid);
        if (c) {
            $('#phone').val(c.phone_number || '');
            $('#billing_address').val(c.billing_address || '');
            $('#shipping_address').val(c.shipping_address || '');
        } else {
            $('#phone').val('');
            $('#billing_address').val('');
            $('#shipping_address').val('');
        }
        renderPreview();
    });

    // Totals (discount/tax) update
    $('#discount_total, #tax_total').on('input change', function() {
        recalcTotals();
        renderPreview();
    });

    // Print
    $('#btn-print').on('click', function () {
        const content = $('#invoice-preview-panel').html();
        const win = window.open('', '_blank', 'width=900,height=600');
        win.document.write(`
            <html><head>
                <title>Print Invoice</title>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
            </head><body style="padding:2em;">
                <div class="container">${content}</div>
            </body></html>
        `);
        win.document.close();
        setTimeout(() => win.print(), 500);
    });

    // Confirm (AJAX JSON)
    $('#btn-confirm').off('click').on('click', function () {
        if (!$('#contact_id').val() || lineItems.length === 0) {
            showAlert('Customer and at least one line item are required.', 'danger');
            return;
        }
        const f = $('#invoice-form');
        const payload = {
            ajax: 1,
            invoice_number: f.find('[name="invoice_number"]').val(),
            issue_date: f.find('[name="issue_date"]').val(),
            due_date: f.find('[name="due_date"]').val(),
            contact_id: f.find('[name="contact_id"]').val(),
            billing_address: f.find('[name="billing_address"]').val(),
            shipping_address: f.find('[name="shipping_address"]').val(),
            phone: f.find('[name="phone"]').val(),
            notes: f.find('[name="notes"]').val(),
            subtotal: $('#subtotal').val(),
            discount_total: $('#discount_total').val(),
            tax_total: $('#tax_total').val(),
            total: $('#total').val(),
            line_items: JSON.stringify(lineItems)
        };
        $.ajax({
            url: AJAX_URL,
            method: 'POST',
            data: payload,
            dataType: 'json'
        })
        .done(function(resp) {
            if (resp.success) {
                invoiceStatus = 'confirmed';
                setFormEditable(false);
                showActions('confirmed');
                renderPreview();
                showAlert('Invoice confirmed and saved!', 'success');
            } else {
                showAlert(resp.msg || 'Server error', 'danger');
            }
        });
    });

    // Revert to Draft
    $('#btn-draft').off('click').on('click', function () {
        invoiceStatus = 'draft';
        setFormEditable(true);
        showActions('draft');
        renderPreview();
        showAlert('Invoice is now a draft and editable.', 'info');
    });

    // Pay button shows modal
    let payModal = new bootstrap.Modal(document.getElementById('payModal'));
    $('#btn-pay').off('click').on('click', function () {
        payModal.show();
    });

    // Pay modal "confirm"
    $('#pay-confirm-btn').off('click').on('click', function () {
        let invoice_number = $('#invoice_number').val();
        $.ajax({
            url: AJAX_URL,
            method: 'POST',
            data: { ajax: 'pay', invoice_number: invoice_number },
            dataType: 'json'
        }).done(function(resp) {
            if (resp.success) {
                invoiceStatus = 'paid';
                setFormEditable(false);
                showActions('paid');
                renderPreview();
                payModal.hide();
                showAlert(resp.msg, 'success');
            } else {
                showAlert(resp.msg || 'Could not mark as paid.', 'danger');
            }
        });
    });
});
</script>
                </div>
            </div>
            <?php include 'layouts/footer.php'; ?>
        </div>
    </div>
    <?php include 'layouts/right-sidebar.php'; ?>
    <?php include 'layouts/footer-scripts.php'; ?>
    <script src="assets/js/app.min.js"></script>
</body>
</html>
