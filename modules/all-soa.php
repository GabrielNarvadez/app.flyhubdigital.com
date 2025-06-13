<div class="row g-4">
    <!-- LEFT: Filters and SOA Table -->
    <div class="col-lg-6">
        <div class="card shadow-sm mb-0">
            <div class="card-body">
                <div class="row mb-3 g-2">
                    <div class="col">
                        <label class="form-label mb-1">Customer</label>
                        <select id="soa-customer" class="form-select"></select>
                    </div>
                    <div class="col">
                        <label class="form-label mb-1">From</label>
                        <select id="soa-from" class="form-select"></select>
                    </div>
                    <div class="col">
                        <label class="form-label mb-1">To</label>
                        <select id="soa-to" class="form-select"></select>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="soa-table">
                        <thead class="bg-light">
                            <tr>
                                <th>SOA #</th>
                                <th>SOA Name</th>
                                <th>Issue Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="soa-tbody"></tbody>
                    </table>
                </div>
                <div id="no-soa-results" class="text-center text-muted py-4 d-none">No SOA records found.</div>
            </div>
        </div>
    </div>
    <!-- RIGHT: SOA Preview -->
    <div class="col-lg-6">
        <div class="invoice-preview card mb-0">
            <div class="card-body" id="soa-preview">
                <div class="text-center text-muted py-5" id="preview-placeholder">
                    <i class="ri-file-list-2-line" style="font-size: 2.5rem;"></i>
                    <div class="mt-3">Select an SOA to preview</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DEMO CSS/JS CDN (Bootstrap/RemixIcon/jQuery) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<style>
@media print {
    body * { visibility: hidden !important; }
    #printable-soa { visibility: visible !important; position: absolute !important; left: 0; top: 0; width: 100% !important; background: #fff !important;}
    .noprint { display: none !important; }
}
.soa-info-table th, .soa-info-table td { padding: .4em .7em; vertical-align: top; }
.soa-info-table { font-size: 15px; }
</style>
<script>
// -------- DEMO DATA --------
const soaCustomers = [
    { id: 1, name: "Ana Del Rosario" },
    { id: 2, name: "Benser Partoza" },
    { id: 3, name: "Maria Santos" }
];
const soaFullData = [
{
    soa_number: "SOA-2024-1001",
    customer_id: 1,
    customer: "Ana Del Rosario",
    client: {
        first_name: "Ana", last_name: "Del Rosario",
        contact: "639171234567",
        email: "ana.rosario@email.com",
        perm_address: "Blk 2 Lot 5, Sta. Maria Village, San Mateo, Rizal",
        prov_address: "Barangay Malaya, Pililla, Rizal",
    },
    project: {
        project_title: "Parkside Residences",
        project_site: "Sta. Maria Village, San Mateo, Rizal",
        phase: "2", block: "5", lot: "8",
        lot_area: "350", lot_class: "Corner",
        price_per_sqm: "3,000"
    },
    soa_name: "March 2024 SOA",
    issue_date: "2024-03-31",
    terms: {
        months: 48,
        contract_price: 1050000,
        monthly_amort: 22000,
        misc_fee: 73500,
        reservation: 20000,
        total_payable: 1128500,
        balance: 1088500
    },
    payment_history: [
        { due: "2024-01-31", paid: "₱22,000.00", status: "Paid" },
        { due: "2024-02-29", paid: "₱22,000.00", status: "Paid" },
        { due: "2024-03-31", paid: "₱0.00", status: "Unpaid" }
    ],
    total_paid: "₱44,000.00",
    balance: "₱1,088,500.00"
},
{
    soa_number: "SOA-2024-1002",
    customer_id: 1,
    customer: "Ana Del Rosario",
    client: {
        first_name: "Ana", last_name: "Del Rosario",
        contact: "639171234567",
        email: "ana.rosario@email.com",
        perm_address: "Blk 2 Lot 5, Sta. Maria Village, San Mateo, Rizal",
        prov_address: "Barangay Malaya, Pililla, Rizal"
    },
    project: {
        project_title: "Parkside Residences",
        project_site: "Sta. Maria Village, San Mateo, Rizal",
        phase: "2", block: "5", lot: "8",
        lot_area: "350", lot_class: "Corner",
        price_per_sqm: "3,000"
    },
    soa_name: "April 2024 SOA",
    issue_date: "2024-04-30",
    terms: {
        months: 48,
        contract_price: 1050000,
        monthly_amort: 22000,
        misc_fee: 73500,
        reservation: 20000,
        total_payable: 1128500,
        balance: 1066500
    },
    payment_history: [
        { due: "2024-01-31", paid: "₱22,000.00", status: "Paid" },
        { due: "2024-02-29", paid: "₱22,000.00", status: "Paid" },
        { due: "2024-03-31", paid: "₱22,000.00", status: "Paid" },
        { due: "2024-04-30", paid: "₱22,000.00", status: "Paid" }
    ],
    total_paid: "₱88,000.00",
    balance: "₱1,040,500.00"
},
{
    soa_number: "SOA-2024-2001",
    customer_id: 2,
    customer: "Benser Partoza",
    client: {
        first_name: "Benser", last_name: "Partoza",
        contact: "639999749709",
        email: "benserpartoza@yahoo.com",
        perm_address: "B10 No. 13 Rambutan St, Doña Josefa Village, Almanza Las Pinas City",
        prov_address: "Same as Above"
    },
    project: {
        project_title: "Faith and Love - 360 VIEW FARM LOT",
        project_site: "Madilay-Dilay, Tanay Rizal",
        phase: "1", block: "3", lot: "9B",
        lot_area: "500", lot_class: "Inner",
        price_per_sqm: "2,500"
    },
    soa_name: "May 2024 SOA",
    issue_date: "2024-05-31",
    terms: {
        months: 36,
        contract_price: 1250000,
        monthly_amort: 36875,
        misc_fee: 87500,
        reservation: 10000,
        total_payable: 1327500,
        balance: 1100000
    },
    payment_history: [
        { due: "2024-03-31", paid: "₱36,875.00", status: "Paid" },
        { due: "2024-04-30", paid: "₱36,875.00", status: "Paid" },
        { due: "2024-05-31", paid: "₱36,875.00", status: "Paid" }
    ],
    total_paid: "₱110,625.00",
    balance: "₱1,100,000.00"
},
{
    soa_number: "SOA-2024-2002",
    customer_id: 2,
    customer: "Benser Partoza",
    client: {
        first_name: "Benser", last_name: "Partoza",
        contact: "639999749709",
        email: "benserpartoza@yahoo.com",
        perm_address: "B10 No. 13 Rambutan St, Doña Josefa Village, Almanza Las Pinas City",
        prov_address: "Same as Above"
    },
    project: {
        project_title: "Faith and Love - 360 VIEW FARM LOT",
        project_site: "Madilay-Dilay, Tanay Rizal",
        phase: "1", block: "3", lot: "9B",
        lot_area: "500", lot_class: "Inner",
        price_per_sqm: "2,500"
    },
    soa_name: "June 2024 SOA",
    issue_date: "2024-06-30",
    terms: {
        months: 36,
        contract_price: 1250000,
        monthly_amort: 36875,
        misc_fee: 87500,
        reservation: 10000,
        total_payable: 1327500,
        balance: 1063125
    },
    payment_history: [
        { due: "2024-03-31", paid: "₱36,875.00", status: "Paid" },
        { due: "2024-04-30", paid: "₱36,875.00", status: "Paid" },
        { due: "2024-05-31", paid: "₱36,875.00", status: "Paid" },
        { due: "2024-06-30", paid: "₱36,875.00", status: "Paid" }
    ],
    total_paid: "₱147,500.00",
    balance: "₱1,063,125.00"
},
{
    soa_number: "SOA-2024-3001",
    customer_id: 3,
    customer: "Maria Santos",
    client: {
        first_name: "Maria", last_name: "Santos",
        contact: "639181234567",
        email: "maria.santos@email.com",
        perm_address: "Blk 1 Lot 2, Greenfield Heights, Sta. Rosa, Laguna",
        prov_address: ""
    },
    project: {
        project_title: "Greenfield Heights",
        project_site: "Greenfield Heights, Sta. Rosa, Laguna",
        phase: "1", block: "2", lot: "6",
        lot_area: "250", lot_class: "Prime",
        price_per_sqm: "3,200"
    },
    soa_name: "February 2024 SOA",
    issue_date: "2024-02-29",
    terms: {
        months: 24,
        contract_price: 800000,
        monthly_amort: 35000,
        misc_fee: 56000,
        reservation: 15000,
        total_payable: 856000,
        balance: 841000
    },
    payment_history: [
        { due: "2024-01-31", paid: "₱35,000.00", status: "Paid" },
        { due: "2024-02-29", paid: "₱0.00", status: "Unpaid" }
    ],
    total_paid: "₱35,000.00",
    balance: "₱841,000.00"
}
];

// ----------- FUNCTIONS ------------
function toProperCase(str) {
    return str.replace(/\w\S*/g, t => t.charAt(0).toUpperCase() + t.substr(1).toLowerCase());
}
function populateCustomers() {
    let opts = '<option value="">Select Customer...</option>';
    soaCustomers.forEach(c => opts += `<option value="${c.id}">${c.name}</option>`);
    $('#soa-customer').html(opts);
    $('#soa-from, #soa-to').html('<option value="">--</option>').prop('disabled', true);
}
function getSoaByCustomer(customerId) {
    return soaFullData.filter(s => s.customer_id == customerId);
}
function getUniqueDatesForCustomer(customerId) {
    let dates = getSoaByCustomer(customerId).map(s => s.issue_date);
    dates = [...new Set(dates)].sort();
    return dates;
}
function filterSoa(customerId, fromDate, toDate) {
    let records = getSoaByCustomer(customerId);
    if (fromDate) records = records.filter(s => s.issue_date >= fromDate);
    if (toDate) records = records.filter(s => s.issue_date <= toDate);
    return records;
}
function renderSoaTable(soaList) {
    if (!soaList.length) {
        $('#soa-tbody').html('');
        $('#no-soa-results').removeClass('d-none');
        return;
    }
    $('#no-soa-results').addClass('d-none');
    let html = '';
    soaList.forEach(soa => {
        html += `
            <tr data-soa="${soa.soa_number}">
                <td><a href="#" class="fw-bold text-primary soa-preview-link">${soa.soa_number}</a></td>
                <td>${soa.soa_name}</td>
                <td>${soa.issue_date}</td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary print-soa" title="Print"><i class="ri-printer-line"></i></button>
                    <button class="btn btn-sm btn-outline-primary send-soa" title="Send"><i class="ri-mail-line"></i></button>
                </td>
            </tr>
        `;
    });
    $('#soa-tbody').html(html);
}
function renderSoaPreview(soa) {
    if (!soa) {
        $('#soa-preview').html(`
            <div class="text-center text-muted py-5" id="preview-placeholder">
                <i class="ri-file-list-2-line" style="font-size: 2.5rem;"></i>
                <div class="mt-3">Select an SOA to preview</div>
            </div>
        `);
        return;
    }
    // --- Details Table ---
    let details = `
    <table class="table soa-info-table table-borderless w-100 mb-3">
        <tbody>
        <tr>
            <th width="38%">Client Name:</th>
            <td>${toProperCase(soa.client.first_name)} ${toProperCase(soa.client.last_name)}</td>
        </tr>
        <tr>
            <th>Contact Number:</th>
            <td>${soa.client.contact}</td>
        </tr>
        <tr>
            <th>Email:</th>
            <td>${soa.client.email}</td>
        </tr>
        <tr>
            <th>Permanent Address:</th>
            <td>${soa.client.perm_address}</td>
        </tr>
        <tr>
            <th>Provincial Address:</th>
            <td>${soa.client.prov_address}</td>
        </tr>
        <tr>
            <th>Project:</th>
            <td>${soa.project.project_title}</td>
        </tr>
        <tr>
            <th>Site/Location:</th>
            <td>${soa.project.project_site}</td>
        </tr>
        <tr>
            <th>Block / Lot / Phase:</th>
            <td>Block ${soa.project.block}, Lot ${soa.project.lot}, Phase ${soa.project.phase}</td>
        </tr>
        <tr>
            <th>Area (sqm):</th>
            <td>${soa.project.lot_area} sqm (${soa.project.lot_class})</td>
        </tr>
        <tr>
            <th>Price per sqm:</th>
            <td>₱${parseFloat(soa.project.price_per_sqm).toLocaleString()}</td>
        </tr>
        <tr>
            <th>SOA #:</th>
            <td>${soa.soa_number}</td>
        </tr>
        <tr>
            <th>SOA Name:</th>
            <td>${soa.soa_name}</td>
        </tr>
        <tr>
            <th>Issue Date:</th>
            <td>${soa.issue_date}</td>
        </tr>
        <tr>
            <th>Payment Terms:</th>
            <td>${soa.terms.months} months</td>
        </tr>
        <tr>
            <th>Total Contract Price:</th>
            <td>₱${soa.terms.contract_price.toLocaleString()}</td>
        </tr>
        <tr>
            <th>Monthly Amortization:</th>
            <td>₱${soa.terms.monthly_amort.toLocaleString()}</td>
        </tr>
        <tr>
            <th>Reservation Fee:</th>
            <td>₱${soa.terms.reservation.toLocaleString()}</td>
        </tr>
        <tr>
            <th>Miscellaneous Fee:</th>
            <td>₱${soa.terms.misc_fee.toLocaleString()}</td>
        </tr>
        <tr>
            <th>Total Amount Payable:</th>
            <td>₱${soa.terms.total_payable.toLocaleString()}</td>
        </tr>
        <tr>
            <th>Balance Payable:</th>
            <td>₱${soa.terms.balance.toLocaleString()}</td>
        </tr>
        </tbody>
    </table>`;
    // --- Payment Table ---
    let paymentRows = soa.payment_history.map((p, i) => `
        <tr>
            <td>${i+1}</td>
            <td>${p.due}</td>
            <td>${p.paid}</td>
            <td>${p.status == "Paid" ? '<span class="text-success">Paid</span>' : '<span class="text-danger">Unpaid</span>'}</td>
        </tr>
    `).join('');
    let paymentTable = `
    <h6>Payment History</h6>
    <table class="table table-sm table-centered table-bordered mb-0">
        <thead class="border-top border-bottom bg-light-subtle border-light">
            <tr>
                <th>#</th>
                <th>Due Date</th>
                <th>Amount Paid</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>${paymentRows}</tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-end">Total Paid:</th>
                <th>${soa.total_paid}</th>
                <th></th>
            </tr>
            <tr>
                <th colspan="2" class="text-end">Balance:</th>
                <th>${soa.balance}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>`;
    // --- Legal Note & Portal ---
    let portalNote = `
    <div class="alert alert-info mt-3 mb-1 p-2" style="font-size:14px">
        <div>
            <b>Tip:</b> You can log in to your Customer Portal at <a href="/customer-portal" target="_blank">/customer-portal</a> to view all your SOAs, invoices, and update your personal details anytime.
        </div>
    </div>
    <div class="mt-2 mb-2"><small class="text-muted">
        By signing below you certify that all information provided is true and accurate. Furthermore, you authorize
        Faith and Love Realty and Development Co. to verify all information with any source and to obtain such other
        information as may be required for the purpose of evaluating your application for a purchase of a lot/house/
        farm lot property. Please take note that Reservation Fee is non-refundable. Please take note that any Reservation Fee that has been made upon acquiring a property shall be non-refundable.
    </small></div>
    <div class="row mt-2 mb-2">
        <div class="col-4"><strong>Prepared by:</strong><br><br>__________________________</div>
        <div class="col-4"><strong>Approved by:</strong><br><br>__________________________</div>
        <div class="col-4"><strong>Received by:</strong><br><br>__________________________</div>
    </div>`;
    // --- Print block for print/pdf ---
    let printable = `
    <div id="printable-soa" style="display:none">
        <div class="mb-4">${details}</div>
        <div class="mb-3">${paymentTable}</div>
        ${portalNote}
    </div>`;
    // --- Actual preview ---
    $('#soa-preview').html(`
        <div class="noprint d-flex flex-wrap gap-2 mb-3">
            <button class="btn btn-outline-secondary btn-sm" id="btn-soa-print"><i class="ri-printer-line"></i> Print to PDF</button>
            <button class="btn btn-outline-primary btn-sm" id="btn-soa-send"><i class="ri-mail-line"></i> Email</button>
        </div>
        <div class="clearfix mb-2">
            <div class="float-start"><img src="assets/images/logo-dark.png" alt="logo" height="28"></div>
            <div class="float-end"><h3 class="m-0">Statement of Account</h3></div>
        </div>
        <div class="mb-4">${details}</div>
        <div class="mb-3">${paymentTable}</div>
        ${portalNote}
        ${printable}
    `);
}
// ----------- MAIN LOGIC -----------
$(function(){
    populateCustomers();
    let selectedCustomer = null;
    let selectedSoa = null;
    $('#soa-customer').on('change', function(){
        selectedCustomer = $(this).val();
        if (!selectedCustomer) {
            $('#soa-from, #soa-to').html('<option value="">--</option>').prop('disabled', true);
            renderSoaTable([]);
            renderSoaPreview(null);
            return;
        }
        let dates = getUniqueDatesForCustomer(selectedCustomer);
        let dateOpts = '<option value="">All</option>' + dates.map(d => `<option value="${d}">${d}</option>`).join('');
        $('#soa-from, #soa-to').html(dateOpts).prop('disabled', false);
        let list = getSoaByCustomer(selectedCustomer);
        renderSoaTable(list);
        renderSoaPreview(null);
        $('#soa-from, #soa-to').val('');
    });
    $('#soa-from, #soa-to').on('change', function(){
        let from = $('#soa-from').val();
        let to = $('#soa-to').val();
        let list = filterSoa(selectedCustomer, from, to);
        renderSoaTable(list);
        renderSoaPreview(null);
    });
    $('#soa-tbody').on('click', '.soa-preview-link', function(e){
        e.preventDefault();
        let soaNum = $(this).closest('tr').data('soa');
        selectedSoa = soaFullData.find(s => s.soa_number === soaNum);
        renderSoaPreview(selectedSoa);
        $('#soa-tbody tr').removeClass('table-active');
        $(this).closest('tr').addClass('table-active');
    });
    $('#soa-preview').on('click', '#btn-soa-send', function(){
        alert("Demo only: Send SOA via Email");
    });
    $('#soa-preview').on('click', '#btn-soa-print', function(){
        if (!selectedSoa) return;
        renderSoaPreview(selectedSoa); // Ensure print block is up to date
        let printable = document.getElementById('printable-soa').cloneNode(true);
        printable.style.display = "block";
        let win = window.open('', '', 'height=900,width=1100');
        win.document.write('<html><head><title>Print SOA</title>');
        win.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">');
        win.document.write('<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">');
        win.document.write('<style>@media print{.noprint{display:none !important;}}</style>');
        win.document.write('</head><body style="background:#fff;">');
        win.document.write(printable.outerHTML);
        win.document.write('</body></html>');
        win.document.close();
        setTimeout(function(){ win.print(); win.close(); }, 500);
    });
    renderSoaTable([]);
    renderSoaPreview(null);
});
</script>
