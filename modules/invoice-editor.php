<div class="row g-4">
    <!-- Invoice Editor (Left) -->
    <div class="col-lg-6">
        <div class="card shadow-sm mb-0">
            <div class="card-body">

                <!-- ACTION BUTTONS -->
                <div class="mb-3 d-flex gap-2 flex-wrap">
                    <a href="invoicing.php" class="btn btn-light border"><i class="ri-arrow-left-line"></i> Back to all invoice</a>
                    <button type="button" class="btn btn-outline-secondary" id="btn-print"><i class="ri-printer-line"></i> Print to PDF</button>
                    <button type="button" class="btn btn-outline-primary" id="btn-email"><i class="ri-mail-line"></i> Email</button>
                    <button type="button" class="btn btn-success" id="btn-confirm">Confirm</button>
                    <button type="button" class="btn btn-warning d-none" id="btn-pay">Pay</button>
                    <button type="button" class="btn btn-dark d-none" id="btn-reset">Reset to Draft</button>
                </div>

                <h5 class="mb-3"><i class="ri-edit-box-line"></i> Invoice Editor</h5>
                <form id="invoiceForm" autocomplete="off">
                    <!-- Invoice Meta -->
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label">Invoice #</label>
                            <input type="text" class="form-control" id="inv-number" value="INV-10001">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Invoice Date</label>
                            <input type="date" class="form-control" id="inv-date">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Due Date</label>
                            <input type="date" class="form-control" id="inv-due">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="inv-status">
                                <option value="Paid">Paid</option>
                                <option value="Unpaid" selected>Unpaid</option>
                                <option value="Overdue">Overdue</option>
                                <option value="Draft">Draft</option>
                            </select>
                        </div>
                    </div>
                    <!-- Customer -->
                    <div class="mb-3">
                        <label class="form-label">Customer</label>
                        <select class="form-select" id="inv-customer">
                            <!-- Populated by JS -->
                        </select>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-12">
                            <label class="form-label">Billing Address</label>
                            <input type="text" class="form-control" id="inv-bill-address">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Shipping Address</label>
                            <input type="text" class="form-control" id="inv-ship-address">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" id="inv-phone">
                        </div>
                    </div>
                    <!-- Products Table -->
                    <div class="mb-2">
                        <label class="form-label">Line Items</label>
                        <table class="table table-sm table-bordered mb-2" id="line-items-table">
                            <thead>
                            <tr>
                                <th style="width:28%">Product</th>
                                <th style="width:22%">Description</th>
                                <th style="width:12%">Qty</th>
                                <th style="width:18%">Unit Price</th>
                                <th style="width:18%">Amount</th>
                                <th style="width:2%"></th>
                            </tr>
                            </thead>
                            <tbody id="line-items-body">
                            <!-- Populated by JS -->
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-line-btn"><i class="ri-add-line"></i> Add Item</button>
                    </div>
                    <!-- Discount/Tax -->
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label">Discount</label>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control" id="inv-discount" value="0">
                                <select class="form-select" id="inv-discount-type" style="max-width:90px;">
                                    <option value="percent">%</option>
                                    <option value="fixed">₱</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">VAT (%)</label>
                            <input type="number" min="0" class="form-control" id="inv-vat" value="12">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" id="inv-notes" rows="2">All accounts are to be paid within 7 days from receipt of invoice.</textarea>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Invoice Preview (Right) -->
    <div class="col-lg-6">
        <div class="invoice-preview card mb-0">
            <div class="card-body" id="invoice-preview">
                <!-- Preview rendered by JS -->
            </div>
        </div>
    </div>
</div>

<!-- Sample Data & Main Script -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
// ====== SAMPLE DATA =======
const customers = [
    {
        id: 1,
        name: "Tosha Minner",
        bill_address: "795 Folsom Ave, Suite 600, San Francisco, CA 94107",
        ship_address: "795 Folsom Ave, Suite 600, San Francisco, CA 94107",
        phone: "(123) 456-7890"
    },
    {
        id: 2,
        name: "Lynne K. Higby",
        bill_address: "421 Mission Blvd, Los Angeles, CA 90001",
        ship_address: "421 Mission Blvd, Los Angeles, CA 90001",
        phone: "(321) 654-0987"
    }
];

const products = [
    {
        id: 1,
        name: "Laptop",
        desc: "Brand Model VGN-TXN27N/B 11.1\" Notebook PC",
        price: 1799.00
    },
    {
        id: 2,
        name: "Warranty",
        desc: "Two Year Extended Warranty - Parts and Labor",
        price: 499.00
    },
    {
        id: 3,
        name: "LED",
        desc: "80cm (32) HD Ready LED TV",
        price: 412.00
    }
];
// ===========================

function populateCustomers() {
    let options = customers.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
    $('#inv-customer').html('<option value="">Select Customer</option>' + options);
}

function populateLineItemRow(idx, selectedId = "", qty = 1, price = "", desc = "") {
    let prodOptions = products.map(p =>
        `<option value="${p.id}" ${selectedId == p.id ? "selected" : ""}>${p.name}</option>`
    ).join('');
    let selectedProd = products.find(p => p.id == selectedId);
    return `
    <tr class="product-row" data-row="${idx}">
        <td>
            <select class="form-select form-select-sm item-product">
                <option value="">Select</option>${prodOptions}
            </select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm item-desc" value="${desc || (selectedProd ? selectedProd.desc : '')}">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm item-qty" min="1" value="${qty}">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm item-price" min="0" step="0.01" value="${price || (selectedProd ? selectedProd.price : '')}">
        </td>
        <td class="item-amount text-end pt-2">
            ₱0.00
        </td>
        <td>
            <button type="button" class="btn btn-link btn-sm text-danger remove-row" title="Remove"><i class="ri-delete-bin-line"></i></button>
        </td>
    </tr>`;
}

function updateLineAmounts() {
    $('#line-items-body tr').each(function(){
        let qty = parseFloat($(this).find('.item-qty').val() || 0);
        let price = parseFloat($(this).find('.item-price').val() || 0);
        let amt = qty * price;
        $(this).find('.item-amount').text("₱" + amt.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}));
    });
}

function gatherInvoiceData() {
    let items = [];
    $('#line-items-body tr').each(function(){
        let prodId = $(this).find('.item-product').val();
        let prod = products.find(p => p.id == prodId) || {};
        items.push({
            name: prod.name || '',
            desc: $(this).find('.item-desc').val() || '',
            qty: parseFloat($(this).find('.item-qty').val() || 0),
            price: parseFloat($(this).find('.item-price').val() || 0),
            amt: parseFloat($(this).find('.item-qty').val() || 0) * parseFloat($(this).find('.item-price').val() || 0)
        });
    });
    let subtotal = items.reduce((sum, i) => sum + i.amt, 0);
    let discountVal = parseFloat($('#inv-discount').val() || 0);
    let discountType = $('#inv-discount-type').val();
    let discount = discountType === "percent" ? subtotal * discountVal / 100 : discountVal;
    let vatRate = parseFloat($('#inv-vat').val() || 0);
    let vat = ((subtotal - discount) * vatRate) / 100;
    let total = subtotal - discount + vat;
    return {
        inv_number: $('#inv-number').val(),
        inv_date: $('#inv-date').val(),
        inv_due: $('#inv-due').val(),
        inv_status: $('#inv-status').val(),
        customer: customers.find(c => c.id == $('#inv-customer').val()) || {},
        bill_address: $('#inv-bill-address').val(),
        ship_address: $('#inv-ship-address').val(),
        phone: $('#inv-phone').val(),
        items,
        notes: $('#inv-notes').val(),
        subtotal, discount, discountType, discountVal, vat, vatRate, total
    };
}

function renderInvoicePreview() {
    let data = gatherInvoiceData();
    let statusClass = data.inv_status === "Paid" ? "bg-success" :
        data.inv_status === "Overdue" ? "bg-danger" :
        data.inv_status === "Unpaid" ? "bg-warning" : "bg-secondary";
    $('#invoice-preview').html(`
        <div class="clearfix mb-2">
            <div class="float-start"><img src="assets/images/logo-dark.png" alt="logo" height="28"></div>
            <div class="float-end"><h3 class="m-0">Invoice</h3></div>
        </div>
        <div class="row">
            <div class="col-7">
                <p class="mb-1"><b>Hello, ${data.customer.name || '[Customer]'}</b></p>
                <p class="text-muted small mb-1">Please find below a cost-breakdown for the recent work completed. Please make payment at your earliest convenience.</p>
            </div>
            <div class="col-5 text-end">
                <div class="mb-1 small"><strong>Order Date:</strong> ${data.inv_date || '-'}</div>
                <div class="mb-1 small"><strong>Order Status:</strong> <span class="badge ${statusClass}">${data.inv_status}</span></div>
                <div class="mb-1 small"><strong>Invoice #:</strong> ${data.inv_number}</div>
            </div>
        </div>
        <div class="row mt-3 small">
            <div class="col-4">
                <h6 class="fw-bold">Billing Address</h6>
                <address class="mb-0">
                    ${data.customer.name || ''}<br>
                    ${data.bill_address || ''}<br>
                    <abbr title="Phone">P:</abbr> ${data.phone || ''}
                </address>
            </div>
            <div class="col-4">
                <h6 class="fw-bold">Shipping Address</h6>
                <address class="mb-0">
                    ${data.customer.name || ''}<br>
                    ${data.ship_address || ''}<br>
                    <abbr title="Phone">P:</abbr> ${data.phone || ''}
                </address>
            </div>
            <div class="col-4 text-end">
                <img src="assets/images/barcode.png" alt="barcode-image" class="img-fluid" style="max-width:90px;">
            </div>
        </div>
        <div class="mt-3 table-responsive">
            <table class="table table-sm table-centered table-hover table-borderless mb-0">
                <thead class="border-top border-bottom bg-light-subtle border-light">
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Unit Cost</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.items.map((item,i) => `
                        <tr>
                            <td>${i+1}</td>
                            <td>
                                <b>${item.name||''}</b><br>
                                <span class="small text-muted">${item.desc||''}</span>
                            </td>
                            <td>${item.qty||0}</td>
                            <td>₱${item.price?.toLocaleString(undefined,{minimumFractionDigits:2})}</td>
                            <td class="text-end">₱${item.amt?.toLocaleString(undefined,{minimumFractionDigits:2})}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        <div class="row mt-3">
            <div class="col-6">
                <h6 class="text-muted">Notes:</h6>
                <small>${data.notes||''}</small>
            </div>
            <div class="col-6">
                <div class="float-end small">
                    <p><b>Sub-total:</b> <span class="float-end">₱${data.subtotal.toLocaleString(undefined,{minimumFractionDigits:2})}</span></p>
                    <p><b>Discount:</b> <span class="float-end">₱${data.discount.toLocaleString(undefined,{minimumFractionDigits:2})} (${data.discountType === 'percent' ? data.discountVal + '%' : '₱'})</span></p>
                    <p><b>VAT (${data.vatRate}%):</b> <span class="float-end">₱${data.vat.toLocaleString(undefined,{minimumFractionDigits:2})}</span></p>
                    <h5 class="mt-2">₱${data.total.toLocaleString(undefined,{minimumFractionDigits:2})}</h5>
                </div>
            </div>
        </div>
    `);
}

// ====== DYNAMIC BEHAVIOR =======
$(function(){
    // Populate customers dropdown
    populateCustomers();

    // On customer select, autofill address/phone
    $('#inv-customer').on('change', function(){
        let cust = customers.find(c => c.id == $(this).val());
        $('#inv-bill-address').val(cust ? cust.bill_address : '');
        $('#inv-ship-address').val(cust ? cust.ship_address : '');
        $('#inv-phone').val(cust ? cust.phone : '');
        renderInvoicePreview();
    });

    // Default 2 rows
    let rowIdx = 1;
    function addLineRow(prodId="", qty=1, price="", desc="") {
        $('#line-items-body').append(populateLineItemRow(rowIdx++, prodId, qty, price, desc));
        updateLineAmounts();
    }
    addLineRow(products[0].id, 1, products[0].price, products[0].desc);
    addLineRow(products[1].id, 3, products[1].price, products[1].desc);

    // Add row button
    $('#add-line-btn').click(function(){
        addLineRow();
        renderInvoicePreview();
    });

    // Delegate events for line items
    $('#line-items-body')
        .on('change', '.item-product', function(){
            let row = $(this).closest('tr');
            let prod = products.find(p => p.id == $(this).val());
            row.find('.item-desc').val(prod ? prod.desc : '');
            row.find('.item-price').val(prod ? prod.price : '');
            updateLineAmounts();
            renderInvoicePreview();
        })
        .on('input', '.item-qty, .item-price, .item-desc', function(){
            updateLineAmounts();
            renderInvoicePreview();
        })
        .on('click', '.remove-row', function(){
            $(this).closest('tr').remove();
            updateLineAmounts();
            renderInvoicePreview();
        });

    // Watch all input fields for live preview
    $('#invoiceForm input, #invoiceForm textarea, #invoiceForm select').on('input change', function(){
        updateLineAmounts();
        renderInvoicePreview();
    });

    // ===== ACTION BUTTONS =====
    $('#btn-print').click(function(){
        // Print only the preview area
        let printContent = document.getElementById('invoice-preview').innerHTML;
        let win = window.open('', '', 'height=700,width=900');
        win.document.write('<html><head><title>Print Invoice</title>');
        win.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">');
        win.document.write('<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">');
        win.document.write('<style>body{padding:2em;} .table th,.table td{padding:0.6em 0.7em;} </style>');
        win.document.write('</head><body>');
        win.document.write(printContent);
        win.document.write('</body></html>');
        win.document.close();
        setTimeout(function(){ win.print(); win.close(); }, 500);
    });

    $('#btn-email').click(function(){
        let customer = $('#inv-customer option:selected').text() || "[Customer]";
        alert("Pretend emailing invoice to: " + customer);
    });

    $('#btn-confirm').click(function(){
        $(this).addClass('d-none');
        $('#btn-pay, #btn-reset').removeClass('d-none');
    });

    $('#btn-pay').click(function(){
        alert("Payment process initiated (demo only).");
    });

    $('#btn-reset').click(function(){
        $('#inv-status').val('Draft').trigger('change');
        $('#btn-pay, #btn-reset').addClass('d-none');
        $('#btn-confirm').removeClass('d-none');
    });

    // Initial update
    updateLineAmounts();
    renderInvoicePreview();
});
</script>
