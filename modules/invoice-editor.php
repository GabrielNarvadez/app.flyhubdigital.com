<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] == '1') {
    require_once __DIR__ . '/../layouts/config.php';
    header('Content-Type: application/json');

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
    $status          = 'sent';
    $contact_id      = $_POST['contact_id'];
    $invoice_number  = $_POST['invoice_number'];
    $issue_date      = $_POST['issue_date'];
    $due_date        = $_POST['due_date'];
    $notes           = $_POST['notes'] ?? '';
    $subtotal        = $_POST['subtotal'];
    $discount_total  = $_POST['discount_total'];
    $tax_total       = $_POST['tax_total'];
    $total           = $_POST['total'];

    // Insert invoice
    $stmt = $link->prepare("
        INSERT INTO invoices
          (company_id, contact_id, invoice_number, status, issue_date, due_date, currency, notes, subtotal, discount_total, tax_total, total, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
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
    $invoice_id = $stmt->insert_id;
    $stmt->close();

    // Insert line items
    $items = json_decode($_POST['line_items'], true);
    if (is_array($items) && count($items)) {
        $stmt = $link->prepare("
            INSERT INTO invoice_items
              (invoice_id, product_id, description, quantity, unit_price, discount, tax_id, total, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, 0, NULL, ?, NOW(), NOW())
        ");
        if (!$stmt) {
            echo json_encode(['success'=>false,'msg'=>'DB item prepare error: '.$link->error]);
            exit;
        }
        foreach ($items as $item) {
            $desc       = $item['description'] ?? '';
            $qty        = $item['quantity'] ?? 1;
            $unit       = $item['unit_price'] ?? 0;
            $prod_id    = $item['product_id'] ?? 0;
            $total_item = $qty * $unit;
            $stmt->bind_param("iisddd", $invoice_id, $prod_id, $desc, $qty, $unit, $total_item);
            $stmt->execute();
        }
        $stmt->close();
    }

    echo json_encode(['success'=>true,'msg'=>'Invoice saved!']);
    exit;
}

require_once __DIR__ . '/../layouts/config.php';
function esc($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }

// Fetch contacts
$contacts = [];
$res = $link->query("SELECT id, first_name, last_name, phone_number FROM contacts ORDER BY first_name, last_name");
while ($c = $res->fetch_assoc()) {
    $contacts[] = $c;
}

// Fetch products
$products = [];
$res = $link->query("SELECT id, name, description, price FROM products ORDER BY name");
while ($p = $res->fetch_assoc()) {
    $products[] = $p;
}

// Default invoice data
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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

<div class="row g-4">
  <div class="col-lg-6">
    <div class="card shadow-sm mb-0">
      <div class="card-body">
        <form id="invoice-form" autocomplete="off">
          <div class="row mb-2">
            <div class="col"><h5 class="mb-0 fw-bold">Invoice Editor</h5></div>
            <div class="col text-end">
              <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-print">
                <i class="ri-printer-line"></i> Print PDF
              </button>
            </div>
          </div>

          <div class="mb-2">
            <label class="form-label">Invoice #</label>
            <input type="text" name="invoice_number" class="form-control" id="invoice_number"
                   value="<?=esc($invoice['invoice_number'])?>" required>
          </div>

          <div class="row">
            <div class="col">
              <div class="mb-2">
                <label class="form-label">Customer</label>
                <select name="contact_id" id="contact_id" class="form-select" required>
                  <option value="">Select Customer</option>
                  <?php foreach ($contacts as $c): ?>
                    <option value="<?=$c['id']?>"
                            data-phone="<?=esc($c['phone_number'])?>"
                      <?=($invoice['contact_id']==$c['id']?'selected':'')?>
                    >
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
              <textarea name="billing_address" id="billing_address"
                        class="form-control"><?=esc($invoice['billing_address'])?></textarea>
            </div>
            <div class="col">
              <label class="form-label">Shipping Address</label>
              <textarea name="shipping_address" id="shipping_address"
                        class="form-control"><?=esc($invoice['shipping_address'])?></textarea>
            </div>
          </div>

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
                <!-- JS will populate -->
              </tbody>
            </table>
            <button type="button" class="btn btn-outline-primary btn-sm" id="add-item">
              <i class="ri-add-line"></i> Add Item
            </button>
          </div>

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

          <div class="d-flex gap-2 mt-2" id="invoice-actions">
            <button type="button" id="btn-confirm" class="btn btn-primary">Confirm</button>
            <button type="button" id="btn-draft" class="btn btn-outline-secondary d-none">Revert to Draft</button>
            <button type="button" id="btn-pay" class="btn btn-success d-none">Pay</button>
          </div>
        </form>
      </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:1055">
      <div id="actionToast" class="toast align-items-center text-bg-primary border-0" role="alert" data-bs-delay="2000">
        <div class="d-flex">
          <div class="toast-body"></div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
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
const AJAX_URL = 'modules/invoice-editor.php';
const products = <?= json_encode($products) ?>;
const contacts = <?= json_encode($contacts) ?>;
let lineItems = [];
let isConfirmed = false;

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
  } else {
    $('#btn-confirm').removeClass('d-none');
    $('#btn-draft, #btn-pay').addClass('d-none');
  }
}

function showActionToast(msg, type = 'primary') {
  const $toast = $('#actionToast');
  $toast
    .removeClass('text-bg-primary text-bg-success text-bg-danger text-bg-info text-bg-warning')
    .addClass('text-bg-' + type)
    .find('.toast-body').text(msg);
  new bootstrap.Toast($toast[0]).show();
}

function recalcTotals() {
  const sub = parseFloat($('#subtotal').val()) || 0;
  const dis = parseFloat($('#discount_total').val()) || 0;
  const tax = parseFloat($('#tax_total').val()) || 0;
  $('#total').val((sub - dis + tax).toFixed(2));
}

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
            <select class="form-select form-select-sm product-dropdown" data-idx="${i}" ${isConfirmed ? 'disabled' : ''}>
              <option value="">Select product</option>
              ${products.map(p => `
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
                   value="${esc(item.description)}" ${isConfirmed ? 'readonly' : ''}>
          </td>
          <td>
            <input type="number" class="form-control form-control-sm qty-input" min="1"
                   value="${item.quantity}" ${isConfirmed ? 'readonly' : ''}>
          </td>
          <td>
            <input type="number" class="form-control form-control-sm unit-input" min="0" step="0.01"
                   value="${item.unit_price}" ${isConfirmed ? 'readonly' : ''}>
          </td>
          <td class="text-end align-middle">₱${amt.toFixed(2)}</td>
          <td class="text-center">
            ${isConfirmed ? '' : `
              <button type="button" class="btn btn-link text-danger btn-sm p-0 remove-item" data-idx="${i}" style="text-decoration: none;">
                <i class="ri-delete-bin-line"></i>
              </button>
            `}
          </td>
        </tr>
      `);
    });
  }
  $('#subtotal').val(subtotal.toFixed(2));
  recalcTotals();
}

function renderPreview() {
  const f = $('#invoice-form');
  const num = f.find('[name="invoice_number"]').val();
  const status = isConfirmed ? 'confirmed' : 'draft';
  const cid = f.find('[name="contact_id"]').val();
  const c = contacts.find(x => x.id == cid) || {};
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
      <div class="float-start"><img src="/assets/images/logo-dark.png" height="28"></div>
      <div class="float-end"><h3 class="m-0">Invoice</h3></div>
    </div>
    <div class="row align-items-center">
      <div class="col-8">
        <p class="mb-1"><b>Hello, ${esc(c.first_name || '')} ${esc(c.last_name || '')}</b></p>
        <p class="text-muted small mb-1">Please find below a cost-breakdown for your transaction.</p>
      </div>
      <div class="col-4 text-end">
        <div class="mb-1 small">
          <strong>Status:</strong>
          <span class="badge bg-${status == 'paid' ? 'success' : status == 'draft' ? 'secondary' : 'info'}">
            ${status.charAt(0).toUpperCase() + status.slice(1)}
          </span>
        </div>
        <div class="mb-1 small"><strong>Invoice #:</strong> ${num}</div>
      </div>
    </div>
    <div class="row mt-3 small">
      <div class="col-6">
        <h6 class="fw-bold">Billing Address</h6>
        <address class="mb-0">
          ${esc(c.first_name || '')} ${esc(c.last_name || '')}<br>
          ${esc(ba)}<br>
          <abbr title="Phone">P:</abbr> ${esc(ph)}
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

$(function () {
  // initial render
  renderLineItemsTable();
  renderPreview();
  setFormEditable(true);
  showActions('draft');

  // Add / remove / update items
  $('#add-item').on('click', () => {
    lineItems.push({ product_id: '', description: '', quantity: 1, unit_price: 0.00 });
    renderLineItemsTable();
    renderPreview();
  });
  $('#line-items-table')
    .on('click', '.remove-item', function () {
      lineItems.splice($(this).data('idx'), 1);
      renderLineItemsTable();
      renderPreview();
    })
    .on('input change', '.qty-input, .unit-input, .desc-input, .product-dropdown', function () {
      const tr = $(this).closest('tr'), idx = tr.index();
      let item = lineItems[idx];
      if ($(this).hasClass('qty-input')) item.quantity = $(this).val();
      if ($(this).hasClass('unit-input')) item.unit_price = $(this).val();
      if ($(this).hasClass('desc-input')) item.description = $(this).val();
      if ($(this).hasClass('product-dropdown')) {
        const sel = $(this).find('option:selected');
        item.product_id = sel.val();
        item.description = sel.data('desc') || '';
        item.unit_price = sel.data('price') || 0;
        tr.find('.desc-input').val(item.description);
        tr.find('.unit-input').val(item.unit_price);
      }
      renderLineItemsTable();
      renderPreview();
    });

  // Contact autofill
  $('#contact_id').on('change', function () {
    const c = contacts.find(x => x.id == $(this).val()) || {};
    $('#phone').val(c.phone_number || '');
    // Clear address fields as no address in DB
    $('#billing_address, #shipping_address').val('');
    renderPreview();
  });

  // Totals change
  $('#discount_total, #tax_total').on('input change', recalcTotals);

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
      showActionToast('Customer and at least one line item are required.', 'danger');
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
      .done(resp => {
        if (resp.success) {
          isConfirmed = true;
          setFormEditable(false);
          showActions('confirmed');
          showActionToast('Invoice confirmed and saved!', 'success');
        } else {
          showActionToast(resp.msg || 'Server error', 'danger');
        }
      })
      .fail((_, status, err) => {
        showActionToast(`Server error: ${err || status}`, 'danger');
      });
  });

  // Revert to Draft
  $('#btn-draft').off('click').on('click', function () {
    isConfirmed = false;
    setFormEditable(true);
    showActions('draft');
    showActionToast('Invoice is now a draft and editable.', 'info');
  });

  // Pay action
  $('#btn-pay').off('click').on('click', function () {
    showActionToast('Pay action clicked! (Add your payment logic here.)', 'success');
  });
});
</script>
