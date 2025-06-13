<div class="row g-4">
    <!-- LEFT: Invoice Table -->
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="input-group" style="max-width:300px;">
                        <span class="input-group-text"><i class="ri-search-line"></i></span>
                        <input type="text" id="search-invoice" class="form-control" placeholder="Search invoices...">
                    </div>
                    <a href="app-invoicing.php" target="_blank" class="btn btn-primary"><i class="ri-add-line"></i> New Invoice</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="invoice-table">
                        <thead class="bg-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="invoice-tbody">
                            <!-- Populated by JS -->
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
// ==== SAMPLE INVOICE DATA (REAL ESTATE STYLE) ====
const invoiceList = [
    {
        inv_number: "INV-RE-0001",
        customer: "Benser Partoza",
        client_id: 101,
        bill_address: "B10 #13 Rambutan St., Doña Josefa Village, Almanza, Las Piñas City",
        ship_address: "Same as billing address",
        phone: "639999749709",
        items: [
            {
                name: "360 View Farm Lot",
                desc: "Project: Faith and Love - 360 VIEW FARM LOT<br>Block: 3 Lot: 9B<br>Lot Area: 500 sqm<br>Price per sqm: ₱2,500<br>Lot Class: Inner",
                qty: 1,
                price: 1250000 // 500*2500
            }
        ],
        notes: "Downpayment for farm lot reservation. Please pay remaining balance by the due date.",
        subtotal: 1250000,
        discount: 0,
        discountType: "percent",
        discountVal: 0,
        vat: 0,
        vatRate: 12,
        total: 1250000*1.12,
        inv_date: "2025-03-01",
        inv_due: "2025-03-15",
        status: "Paid"
    },
    {
        inv_number: "INV-RE-0002",
        customer: "Ana Del Rosario",
        client_id: 102,
        bill_address: "Blk 2 Lot 5, Sta. Maria Village, San Mateo, Rizal",
        ship_address: "Same as billing address",
        phone: "639171234567",
        items: [
            {
                name: "Parkside Residences",
                desc: "Project: Parkside Residences<br>Block: 2 Lot: 5<br>Lot Area: 300 sqm<br>Price per sqm: ₱3,000<br>Lot Class: Corner",
                qty: 1,
                price: 900000 // 300*3000
            }
        ],
        notes: "1st amortization payment for Parkside Residences lot.",
        subtotal: 900000,
        discount: 10000,
        discountType: "fixed",
        discountVal: 10000,
        vat: 0,
        vatRate: 12,
        total: (900000-10000)*1.12,
        inv_date: "2025-03-10",
        inv_due: "2025-03-24",
        status: "Unpaid"
    },
    {
        inv_number: "INV-RE-0003",
        customer: "Maria Santos",
        client_id: 103,
        bill_address: "Blk 1 Lot 2, Greenfield Heights, Sta. Rosa, Laguna",
        ship_address: "Same as billing address",
        phone: "639181234567",
        items: [
            {
                name: "Greenfield Heights",
                desc: "Project: Greenfield Heights<br>Block: 1 Lot: 2<br>Lot Area: 200 sqm<br>Price per sqm: ₱3,200<br>Lot Class: Prime",
                qty: 1,
                price: 640000 // 200*3200
            }
        ],
        notes: "Full payment for Greenfield Heights property.",
        subtotal: 640000,
        discount: 0,
        discountType: "percent",
        discountVal: 0,
        vat: 0,
        vatRate: 12,
        total: 640000*1.12,
        inv_date: "2025-02-15",
        inv_due: "2025-02-28",
        status: "Draft"
    }
];

// === Helper for status badge ===
function statusBadge(status) {
    let map = {
        Paid: 'bg-success',
        Unpaid: 'bg-warning',
        Overdue: 'bg-danger',
        Draft: 'bg-secondary'
    };
    return `<span class="badge ${map[status]||'bg-secondary'}">${status}</span>`;
}

// === Render Invoice Table ===
function renderInvoiceTable(data) {
    let html = '';
    data.forEach((inv, idx) => {
        let total = inv.items.reduce((sum, item) => sum + item.qty*item.price, 0);
        let discount = inv.discountType === "percent" ? total * (inv.discountVal||0) / 100 : (inv.discountVal||0);
        let vat = ((total - discount) * (inv.vatRate||0)) / 100;
        let totalDisplay = (total - discount + vat);

        html += `<tr data-inv="${inv.inv_number}">
            <td>
                <a href="#" class="fw-bold text-primary view-invoice">${inv.inv_number}</a>
            </td>
            <td>${inv.customer}</td>
            <td>₱${totalDisplay.toLocaleString(undefined, {minimumFractionDigits:2})}</td>
            <td>${statusBadge(inv.status)}</td>
            <td>
                <a href="#" class="me-2 print-invoice text-decoration-none"><i class="ri-printer-line"></i> Print</a>
                <a href="#" class="send-invoice text-decoration-none"><i class="ri-mail-line"></i> Email</a>
            </td>
        </tr>`;
    });
    $('#invoice-tbody').html(html);
    $('#no-results').toggleClass('d-none', data.length !== 0);
}

// === Render Invoice Preview (Right) ===
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

    $('#invoice-list-preview').html(`
        <!-- BUTTONS AT TOP -->
        <div class="d-flex flex-wrap gap-2 mb-3">
            <button class="btn btn-outline-secondary btn-sm" id="btn-preview-print"><i class="ri-printer-line"></i> Print</button>
            <button class="btn btn-outline-primary btn-sm" id="btn-preview-send"><i class="ri-mail-line"></i> Email</button>
            <a class="btn btn-outline-info btn-sm" id="btn-generate-soa" target="_blank" href="soa-manager.php?customer=${encodeURIComponent(inv.customer)}"><i class="ri-list-unordered"></i> Generate SOA</a>
        </div>
        <div class="clearfix mb-2">
            <div class="float-start"><img src="assets/images/logo-dark.png" alt="logo" height="28"></div>
            <div class="float-end"><h3 class="m-0">Invoice</h3></div>
        </div>
        <div class="row align-items-center">
            <div class="col-8">
                <p class="mb-1"><b>Hello, ${inv.customer}</b></p>
                <p class="text-muted small mb-1">Please find below a cost-breakdown for your real estate transaction.</p>
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
                    <p><b>Discount:</b> <span class="float-end">₱${discount.toLocaleString(undefined,{minimumFractionDigits:2})} (${inv.discountType === 'percent' ? inv.discountVal + '%' : '₱'})</span></p>
                    <p><b>VAT (${inv.vatRate||0}%):</b> <span class="float-end">₱${vat.toLocaleString(undefined,{minimumFractionDigits:2})}</span></p>
                    <h5 class="mt-2">₱${totalDisplay.toLocaleString(undefined,{minimumFractionDigits:2})}</h5>
                </div>
            </div>
        </div>
        <div class="mt-3"><small class="text-muted">${inv.notes||""}</small></div>
    `);
}

$(function(){
    // Render the full invoice table initially
    renderInvoiceTable(invoiceList);

    // Store the selected invoice
    let selectedInv = null;

    // === Invoice search
    $('#search-invoice').on('input', function(){
        let val = $(this).val().toLowerCase();
        let filtered = invoiceList.filter(inv =>
            inv.inv_number.toLowerCase().includes(val) ||
            inv.customer.toLowerCase().includes(val)
        );
        renderInvoiceTable(filtered);
        // If selection is now filtered out, clear preview
        if (selectedInv && !filtered.find(i => i.inv_number === selectedInv.inv_number)) {
            selectedInv = null;
            renderInvoicePreview(null);
        }
    });

    // === Select invoice row
    $('#invoice-tbody').on('click', '.view-invoice', function(e){
        e.preventDefault();
        let invNum = $(this).closest('tr').data('inv');
        let inv = invoiceList.find(i => i.inv_number === invNum);
        selectedInv = inv;
        renderInvoicePreview(inv);

        // Optional: highlight selected row
        $('#invoice-tbody tr').removeClass('table-active');
        $(this).closest('tr').addClass('table-active');
    });

    // === Print to PDF (row action)
    $('#invoice-tbody').on('click', '.print-invoice', function(e){
        e.preventDefault();
        let invNum = $(this).closest('tr').data('inv');
        let inv = invoiceList.find(i => i.inv_number === invNum);
        if (inv) printInvoice(inv);
    });

    // === Send (row action)
    $('#invoice-tbody').on('click', '.send-invoice', function(e){
        e.preventDefault();
        let invNum = $(this).closest('tr').data('inv');
        let inv = invoiceList.find(i => i.inv_number === invNum);
        if (inv) alert(`Pretend sending invoice to: ${inv.customer}`);
    });

    // === Print from preview
    $('#invoice-list-preview').on('click', '#btn-preview-print', function(e){
        e.preventDefault();
        if (!selectedInv) return;
        printInvoice(selectedInv);
    });

    // === Email from preview
    $('#invoice-list-preview').on('click', '#btn-preview-send', function(e){
        e.preventDefault();
        if (!selectedInv) return;
        alert(`Pretend sending invoice to: ${selectedInv.customer}`);
    });

    // No server yet, but SOA is just a link
    $('#invoice-list-preview').on('click', '#btn-generate-soa', function(e){
        // Demo: nothing special, just let the link open
    });

    // Helper for printing invoice preview
    function printInvoice(inv) {
        let prevHtml = $('#invoice-list-preview').html();
        renderInvoicePreview(inv);
        let printContent = document.getElementById('invoice-list-preview').innerHTML;
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
        $('#invoice-list-preview').html(prevHtml);
    }

    // Default: show no preview
    renderInvoicePreview(null);
});
</script>
