<div class="row g-4">
    <!-- LEFT: Invoice Table with Filters -->
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-3"><i class="ri-file-text-line align-middle"></i> My Invoices</h4>
                <!-- Top Filters & Search -->
                <div class="d-flex flex-wrap gap-2 mb-3 align-items-end">
                    <div class="input-group" style="max-width:250px;">
                        <span class="input-group-text"><i class="ri-search-line"></i></span>
                        <input type="text" id="search-invoice" class="form-control" placeholder="Search my invoices...">
                    </div>
                    <div class="ms-2">
                        <label class="form-label mb-1 small">Status</label>
                        <select id="status-filter" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="Paid">Paid</option>
                            <option value="Unpaid">Unpaid</option>
                            <option value="Overdue">Overdue</option>
                        </select>
                    </div>
                    <div class="ms-2">
                        <label class="form-label mb-1 small">From</label>
                        <input type="date" id="filter-from" class="form-control form-control-sm">
                    </div>
                    <div class="ms-2">
                        <label class="form-label mb-1 small">To</label>
                        <input type="date" id="filter-to" class="form-control form-control-sm">
                    </div>
                    <div class="ms-auto">
                        <button class="btn btn-outline-secondary btn-sm" id="download-invoices">
                            <i class="ri-download-2-line"></i> Download (Excel)
                        </button>
                    </div>
                </div>
                <!-- Invoice Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="invoice-table">
                        <thead class="bg-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="invoice-tbody"></tbody>
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
            </div>
        </div>
    </div>
</div>

<!-- BOOTSTRAP, REMIXICON, JQUERY -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
// ==== SAMPLE INVOICE DATA FOR "Ana Del Rosario" ====
const loggedInClient = {
    name: "Ana Del Rosario",
    id: 102,
    bill_address: "Blk 2 Lot 5, Sta. Maria Village, San Mateo, Rizal",
    ship_address: "Same as billing address",
    phone: "639171234567"
};

const invoiceList = [
    {
        inv_number: "INV-2024-001",
        customer: "Ana Del Rosario",
        client_id: 102,
        bill_address: loggedInClient.bill_address,
        ship_address: loggedInClient.ship_address,
        phone: loggedInClient.phone,
        items: [
            {
                name: "Parkside Residences",
                desc: "Block: 2 Lot: 5<br>Lot Area: 300 sqm<br>Price per sqm: ₱3,000<br>Lot Class: Corner",
                qty: 1,
                price: 900000
            }
        ],
        notes: "1st amortization payment.",
        subtotal: 900000,
        discount: 0,
        discountType: "fixed",
        discountVal: 0,
        vat: 0,
        vatRate: 12,
        total: 900000*1.12,
        inv_date: "2024-03-10",
        inv_due: "2024-03-24",
        status: "Paid"
    },
    {
        inv_number: "INV-2024-002",
        customer: "Ana Del Rosario",
        client_id: 102,
        bill_address: loggedInClient.bill_address,
        ship_address: loggedInClient.ship_address,
        phone: loggedInClient.phone,
        items: [
            {
                name: "Parkside Residences",
                desc: "Block: 2 Lot: 5<br>Lot Area: 300 sqm<br>Price per sqm: ₱3,000<br>Lot Class: Corner",
                qty: 1,
                price: 900000
            }
        ],
        notes: "2nd amortization payment.",
        subtotal: 900000,
        discount: 0,
        discountType: "fixed",
        discountVal: 0,
        vat: 0,
        vatRate: 12,
        total: 900000*1.12,
        inv_date: "2024-04-10",
        inv_due: "2024-04-24",
        status: "Paid"
    },
    {
        inv_number: "INV-2024-003",
        customer: "Ana Del Rosario",
        client_id: 102,
        bill_address: loggedInClient.bill_address,
        ship_address: loggedInClient.ship_address,
        phone: loggedInClient.phone,
        items: [
            {
                name: "Parkside Residences",
                desc: "Block: 2 Lot: 5<br>Lot Area: 300 sqm<br>Price per sqm: ₱3,000<br>Lot Class: Corner",
                qty: 1,
                price: 900000
            }
        ],
        notes: "3rd amortization payment.",
        subtotal: 900000,
        discount: 0,
        discountType: "fixed",
        discountVal: 0,
        vat: 0,
        vatRate: 12,
        total: 900000*1.12,
        inv_date: "2024-05-10",
        inv_due: "2024-05-24",
        status: "Unpaid"
    },
    {
        inv_number: "INV-2024-004",
        customer: "Ana Del Rosario",
        client_id: 102,
        bill_address: loggedInClient.bill_address,
        ship_address: loggedInClient.ship_address,
        phone: loggedInClient.phone,
        items: [
            {
                name: "Parkside Residences",
                desc: "Block: 2 Lot: 5<br>Lot Area: 300 sqm<br>Price per sqm: ₱3,000<br>Lot Class: Corner",
                qty: 1,
                price: 900000
            }
        ],
        notes: "4th amortization payment.",
        subtotal: 900000,
        discount: 0,
        discountType: "fixed",
        discountVal: 0,
        vat: 0,
        vatRate: 12,
        total: 900000*1.12,
        inv_date: "2024-06-10",
        inv_due: "2024-06-24",
        status: "Unpaid"
    },
    {
        inv_number: "INV-2024-005",
        customer: "Ana Del Rosario",
        client_id: 102,
        bill_address: loggedInClient.bill_address,
        ship_address: loggedInClient.ship_address,
        phone: loggedInClient.phone,
        items: [
            {
                name: "Parkside Residences",
                desc: "Block: 2 Lot: 5<br>Lot Area: 300 sqm<br>Price per sqm: ₱3,000<br>Lot Class: Corner",
                qty: 1,
                price: 900000
            }
        ],
        notes: "5th amortization payment.",
        subtotal: 900000,
        discount: 0,
        discountType: "fixed",
        discountVal: 0,
        vat: 0,
        vatRate: 12,
        total: 900000*1.12,
        inv_date: "2024-07-10",
        inv_due: "2024-07-24",
        status: "Overdue"
    }
];

// Helper for status badge
function statusBadge(status) {
    let map = {
        Paid: 'bg-success',
        Unpaid: 'bg-warning',
        Overdue: 'bg-danger'
    };
    return `<span class="badge ${map[status]||'bg-secondary'}">${status}</span>`;
}

// Filter invoices by all current filter criteria
function getFilteredInvoices() {
    let val = $('#search-invoice').val().toLowerCase();
    let stat = $('#status-filter').val();
    let from = $('#filter-from').val();
    let to = $('#filter-to').val();

    return invoiceList.filter(inv =>
        (inv.customer === loggedInClient.name) &&
        (!val || inv.inv_number.toLowerCase().includes(val)) &&
        (!stat || inv.status === stat) &&
        (!from || inv.inv_date >= from) &&
        (!to || inv.inv_date <= to)
    );
}

// Render Invoice Table
function renderInvoiceTable(data) {
    if (!data.length) {
        $('#invoice-tbody').html('');
        $('#no-results').removeClass('d-none');
        return;
    }
    $('#no-results').addClass('d-none');
    let html = '';
    data.forEach(inv => {
        let total = inv.items.reduce((sum, item) => sum + item.qty*item.price, 0);
        let discount = inv.discountType === "percent" ? total * (inv.discountVal||0) / 100 : (inv.discountVal||0);
        let vat = ((total - discount) * (inv.vatRate||0)) / 100;
        let totalDisplay = (total - discount + vat);

        html += `<tr data-inv="${inv.inv_number}">
            <td><a href="#" class="fw-bold text-primary view-invoice">${inv.inv_number}</a></td>
            <td>₱${totalDisplay.toLocaleString(undefined, {minimumFractionDigits:2})}</td>
            <td>${statusBadge(inv.status)}</td>
            <td>${inv.inv_date}</td>
            <td>
                <a href="#" class="me-2 print-invoice text-decoration-none" title="Print"><i class="ri-printer-line"></i></a>
                <a href="#" class="download-invoice text-decoration-none" title="Download PDF"><i class="ri-download-2-line"></i></a>
            </td>
        </tr>`;
    });
    $('#invoice-tbody').html(html);
}

// Render Invoice Preview (Right)
function renderInvoicePreview(inv) {
    if (!inv) {
        $('#invoice-list-preview').html(`
            <div class="text-center text-muted py-5" id="preview-placeholder">
                <i class="ri-file-text-line" style="font-size: 2.5rem;"></i>
                <div class="mt-3">Select an invoice to preview</div>
            </div>
        `);
        return;
    }
    let total = inv.items.reduce((sum, item) => sum + item.qty*item.price, 0);
    let discount = inv.discountType === "percent" ? total * (inv.discountVal||0) / 100 : (inv.discountVal||0);
    let vat = ((total - discount) * (inv.vatRate||0)) / 100;
    let totalDisplay = (total - discount + vat);

    let statusClass = inv.status === "Paid" ? "bg-success"
        : inv.status === "Overdue" ? "bg-danger"
        : inv.status === "Unpaid" ? "bg-warning"
        : "bg-secondary";

    let paymentBlock = "";
    if (inv.status === "Unpaid" || inv.status === "Overdue") {
        paymentBlock = `
        <div class="alert alert-warning mt-3 mb-2">
            <strong>Amount Due:</strong> ₱${totalDisplay.toLocaleString(undefined,{minimumFractionDigits:2})}<br>
            <span class="small">Due Date: ${inv.inv_due}</span><br>
            <span class="small">Please pay via GCash 0917-xxxxxxx or bank transfer. 
            <a href="mailto:billing@example.com">Send proof</a> after payment.</span>
            <!-- Optional: <button class="btn btn-primary btn-sm mt-2">Pay Now</button> -->
        </div>`;
    }

    $('#invoice-list-preview').html(`
        <div class="d-flex flex-wrap gap-2 mb-3">
            <button class="btn btn-outline-secondary btn-sm" id="btn-preview-print"><i class="ri-printer-line"></i> Print</button>
            <button class="btn btn-outline-secondary btn-sm" id="btn-preview-download"><i class="ri-download-2-line"></i> Download PDF</button>
        </div>
        <div class="clearfix mb-2">
            <div class="float-start"><img src="assets/images/logo-dark.png" alt="logo" height="28"></div>
            <div class="float-end"><h3 class="m-0">Invoice</h3></div>
        </div>
        <div class="row align-items-center">
            <div class="col-8">
                <p class="mb-1"><b>Hello, ${inv.customer}</b></p>
                <p class="text-muted small mb-1">See your invoice details below.</p>
            </div>
            <div class="col-4 text-end">
                <div class="mb-1 small"><strong>Status:</strong> <span class="badge ${statusClass}">${inv.status}</span></div>
                <div class="mb-1 small"><strong>Invoice #:</strong> ${inv.inv_number}</div>
            </div>
        </div>
        <div class="row mt-3 small">
            <div class="col-6">
                <h6 class="fw-bold">Billing Address</h6>
                <address class="mb-0">
                    ${inv.customer}<br>
                    ${inv.bill_address}<br>
                    <abbr title="Phone">P:</abbr> ${inv.phone}
                </address>
            </div>
            <div class="col-6">
                <h6 class="fw-bold">Shipping Address</h6>
                <address class="mb-0">
                    ${inv.customer}<br>
                    ${inv.ship_address}<br>
                    <abbr title="Phone">P:</abbr> ${inv.phone}
                </address>
            </div>
        </div>
        <div class="mt-3 table-responsive">
            <table class="table table-sm table-centered table-hover table-borderless mb-0">
                <thead class="border-top border-bottom bg-light-subtle border-light">
                    <tr>
                        <th>#</th>
                        <th>Property</th>
                        <th>Details</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    ${inv.items.map((item,i) => `
                        <tr>
                            <td>${i+1}</td>
                            <td><b>${item.name||''}</b></td>
                            <td>${item.desc||''}</td>
                            <td>₱${(item.qty*item.price)?.toLocaleString(undefined,{minimumFractionDigits:2})}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="float-end small">
                    <p><b>Sub-total:</b> <span class="float-end">₱${total.toLocaleString(undefined,{minimumFractionDigits:2})}</span></p>
                    <p><b>Discount:</b> <span class="float-end">₱${discount.toLocaleString(undefined,{minimumFractionDigits:2})}</span></p>
                    <p><b>VAT (${inv.vatRate||0}%):</b> <span class="float-end">₱${vat.toLocaleString(undefined,{minimumFractionDigits:2})}</span></p>
                    <h5 class="mt-2">₱${totalDisplay.toLocaleString(undefined,{minimumFractionDigits:2})}</h5>
                </div>
            </div>
        </div>
        ${paymentBlock}
        <div class="mt-3"><small class="text-muted">${inv.notes||""}</small></div>
    `);
}

$(function(){
    let selectedInv = null;

    function refreshInvoices() {
        let filtered = getFilteredInvoices();
        renderInvoiceTable(filtered);
        if (!selectedInv || !filtered.find(i => i.inv_number === selectedInv.inv_number)) {
            selectedInv = null;
            renderInvoicePreview(null);
        }
    }

    // Wire up all filters
    $('#search-invoice, #status-filter, #filter-from, #filter-to').on('input change', refreshInvoices);

    // Invoice row click = preview
    $('#invoice-tbody').on('click', '.view-invoice', function(e){
        e.preventDefault();
        let invNum = $(this).closest('tr').data('inv');
        let inv = invoiceList.find(i => i.inv_number === invNum);
        selectedInv = inv;
        renderInvoicePreview(inv);
        $('#invoice-tbody tr').removeClass('table-active');
        $(this).closest('tr').addClass('table-active');
    });

    // Print action
    $('#invoice-tbody, #invoice-list-preview').on('click', '.print-invoice, #btn-preview-print', function(e){
        e.preventDefault();
        if (!selectedInv) return;
        let printContent = $('#invoice-list-preview').html();
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

    // Download as PDF (placeholder: triggers print dialog)
    $('#invoice-tbody, #invoice-list-preview').on('click', '.download-invoice, #btn-preview-download', function(e){
        e.preventDefault();
        if (!selectedInv) return;
        let printContent = $('#invoice-list-preview').html();
        let win = window.open('', '', 'height=700,width=900');
        win.document.write('<html><head><title>Download Invoice</title>');
        win.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">');
        win.document.write('<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">');
        win.document.write('<style>body{padding:2em;} .table th,.table td{padding:0.6em 0.7em;} </style>');
        win.document.write('</head><body>');
        win.document.write(printContent);
        win.document.write('</body></html>');
        win.document.close();
        setTimeout(function(){ win.print(); win.close(); }, 500);
    });

    // Download as Excel (CSV)
    $('#download-invoices').on('click', function(e){
        e.preventDefault();
        let data = getFilteredInvoices();
        let csv = 'Invoice #,Amount,Status,Date\n' + data.map(inv =>
            [inv.inv_number, inv.total.toLocaleString(), inv.status, inv.inv_date].join(',')
        ).join('\n');
        let blob = new Blob([csv], {type:'text/csv'});
        let a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = "my-invoices.csv";
        a.click();
    });

    // Initial render
    refreshInvoices();
    renderInvoicePreview(null);
});
</script>
