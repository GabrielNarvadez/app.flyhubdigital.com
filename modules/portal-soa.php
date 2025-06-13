<div class="row g-4">
    <!-- LEFT: SOA Table and Filters -->
    <div class="col-lg-6">
        <div class="card shadow-sm mb-0">
            <div class="card-body">
                <h4 class="mb-3"><i class="ri-file-list-2-line align-middle"></i> My Statements of Account</h4>
                <!-- Filters -->
                <div class="row mb-3 g-2">
                    <div class="col">
                        <label class="form-label mb-1">From</label>
                        <select id="soa-from" class="form-select"></select>
                    </div>
                    <div class="col">
                        <label class="form-label mb-1">To</label>
                        <select id="soa-to" class="form-select"></select>
                    </div>
                </div>
                <!-- SOA Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="soa-table">
                        <thead class="bg-light">
                            <tr>
                                <th>SOA #</th>
                                <th>SOA Name</th>
                                <th>Issue Date</th>
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
.soa-info-section { margin-bottom: 1.3rem; }
.soa-info-section h6 { font-size: 1rem; color: #4c4c4c; margin-bottom: .6rem; font-weight: bold;}
.soa-info-table th, .soa-info-table td { padding: .4em .7em; vertical-align: top; }
.soa-info-table { font-size: 15px; }
</style>
<script>
// -------- DEMO DATA --------
const loggedInClient = {
    id: 1,
    name: "Ana Del Rosario",
    client: {
        first_name: "Ana", last_name: "Del Rosario",
        contact: "639171234567",
        email: "ana.rosario@email.com",
        perm_address: "Blk 2 Lot 5, Sta. Maria Village, San Mateo, Rizal",
        prov_address: "Barangay Malaya, Pililla, Rizal",
    }
};
// 6 Sample SOAs for demo
const soaFullData = [
{
    soa_number: "SOA-2024-1001",
    customer_id: 1,
    soa_name: "January 2024 SOA",
    issue_date: "2024-01-31",
    project: {
        project_title: "Parkside Residences",
        project_site: "Sta. Maria Village, San Mateo, Rizal",
        phase: "2", block: "5", lot: "8",
        lot_area: "350", lot_class: "Corner",
        price_per_sqm: "3,000"
    },
    terms: {
        months: 48, contract_price: 1050000, monthly_amort: 22000, misc_fee: 73500,
        reservation: 20000, total_payable: 1128500, balance: 1106500
    },
    payment_history: [
        { due: "2024-01-31", paid: "₱22,000.00", status: "Paid" }
    ],
    total_paid: "₱22,000.00",
    balance: "₱1,106,500.00"
},
{
    soa_number: "SOA-2024-1002",
    customer_id: 1,
    soa_name: "February 2024 SOA",
    issue_date: "2024-02-29",
    project: {
        project_title: "Parkside Residences",
        project_site: "Sta. Maria Village, San Mateo, Rizal",
        phase: "2", block: "5", lot: "8",
        lot_area: "350", lot_class: "Corner",
        price_per_sqm: "3,000"
    },
    terms: {
        months: 48, contract_price: 1050000, monthly_amort: 22000, misc_fee: 73500,
        reservation: 20000, total_payable: 1128500, balance: 1088500
    },
    payment_history: [
        { due: "2024-01-31", paid: "₱22,000.00", status: "Paid" },
        { due: "2024-02-29", paid: "₱22,000.00", status: "Paid" }
    ],
    total_paid: "₱44,000.00",
    balance: "₱1,088,500.00"
},
{
    soa_number: "SOA-2024-1003",
    customer_id: 1,
    soa_name: "March 2024 SOA",
    issue_date: "2024-03-31",
    project: {
        project_title: "Parkside Residences",
        project_site: "Sta. Maria Village, San Mateo, Rizal",
        phase: "2", block: "5", lot: "8",
        lot_area: "350", lot_class: "Corner",
        price_per_sqm: "3,000"
    },
    terms: {
        months: 48, contract_price: 1050000, monthly_amort: 22000, misc_fee: 73500,
        reservation: 20000, total_payable: 1128500, balance: 1066500
    },
    payment_history: [
        { due: "2024-01-31", paid: "₱22,000.00", status: "Paid" },
        { due: "2024-02-29", paid: "₱22,000.00", status: "Paid" },
        { due: "2024-03-31", paid: "₱22,000.00", status: "Paid" }
    ],
    total_paid: "₱66,000.00",
    balance: "₱1,066,500.00"
},
{
    soa_number: "SOA-2024-1004",
    customer_id: 1,
    soa_name: "April 2024 SOA",
    issue_date: "2024-04-30",
    project: {
        project_title: "Parkside Residences",
        project_site: "Sta. Maria Village, San Mateo, Rizal",
        phase: "2", block: "5", lot: "8",
        lot_area: "350", lot_class: "Corner",
        price_per_sqm: "3,000"
    },
    terms: {
        months: 48, contract_price: 1050000, monthly_amort: 22000, misc_fee: 73500,
        reservation: 20000, total_payable: 1128500, balance: 1044500
    },
    payment_history: [
        { due: "2024-01-31", paid: "₱22,000.00", status: "Paid" },
        { due: "2024-02-29", paid: "₱22,000.00", status: "Paid" },
        { due: "2024-03-31", paid: "₱22,000.00", status: "Paid" },
        { due: "2024-04-30", paid: "₱22,000.00", status: "Paid" }
    ],
    total_paid: "₱88,000.00",
    balance: "₱1,044,500.00"
},
{
    soa_number: "SOA-2024-1005",
    customer_id: 1,
    soa_name: "May 2024 SOA",
    issue_date: "2024-05-31",
    project: {
        project_title: "Parkside Residences",
        project_site: "Sta. Maria Village, San Mateo, Rizal",
        phase: "2", block: "5", lot: "8",
        lot_area: "350", lot_class: "Corner",
        price_per_sqm: "3,000"
    },
    terms: {
        months: 48, contract_price: 1050000, monthly_amort: 22000, misc_fee: 73500,
        reservation: 20000, total_payable: 1128500, balance: 1022500
    },
    payment_history: [
        { due: "2024-01-31", paid: "₱22,000.00", status: "Paid" },
        { due: "2024-02-29", paid: "₱22,000.00", status: "Paid" },
        { due: "2024-03-31", paid: "₱22,000.00", status: "Paid" },
        { due: "2024-04-30", paid: "₱22,000.00", status: "Paid" },
        { due: "2024-05-31", paid: "₱22,000.00", status: "Paid" }
    ],
    total_paid: "₱110,000.00",
    balance: "₱1,022,500.00"
},
{
    soa_number: "SOA-2024-1006",
    customer_id: 1,
    soa_name: "June 2024 SOA",
    issue_date: "2024-06-30",
    project: {
        project_title: "Parkside Residences",
        project_site: "Sta. Maria Village, San Mateo, Rizal",
        phase: "2", block: "5", lot: "8",
        lot_area: "350", lot_class: "Corner",
        price_per_sqm: "3,000"
    },
    terms: {
        months: 48, contract_price: 1050000, monthly_amort: 22000, misc_fee: 73500,
        reservation: 20000, total_payable: 1128500, balance: 1000500
    },
    payment_history: [
        { due: "2024-01-31", paid: "₱22,000.00", status: "Paid" },
        { due: "2024-02-29", paid: "₱22,000.00", status: "Paid" },
        { due: "2024-03-31", paid: "₱22,000.00", status: "Paid" },
        { due: "2024-04-30", paid: "₱22,000.00", status: "Paid" },
        { due: "2024-05-31", paid: "₱22,000.00", status: "Paid" },
        { due: "2024-06-30", paid: "₱22,000.00", status: "Paid" }
    ],
    total_paid: "₱132,000.00",
    balance: "₱1,000,500.00"
}
];

// ----------- FUNCTIONS -----------
function toProperCase(str) {
    return str.replace(/\w\S*/g, t => t.charAt(0).toUpperCase() + t.substr(1).toLowerCase());
}
function getUniqueDates() {
    let dates = soaFullData.map(s => s.issue_date);
    dates = [...new Set(dates)].sort();
    return dates;
}
function filterSoa(fromDate, toDate) {
    let records = soaFullData.slice();
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
    // --- Section 1: Customer Details ---
    let detailsCustomer = `
    <div class="soa-info-section">
        <h6>Customer Details</h6>
        <table class="table soa-info-table table-borderless w-100 mb-0">
            <tbody>
            <tr>
                <th width="38%">Name:</th>
                <td>${toProperCase(loggedInClient.client.first_name)} ${toProperCase(loggedInClient.client.last_name)}</td>
            </tr>
            <tr>
                <th>Contact Number:</th>
                <td>${loggedInClient.client.contact}</td>
            </tr>
            <tr>
                <th>Email:</th>
                <td>${loggedInClient.client.email}</td>
            </tr>
            <tr>
                <th>Permanent Address:</th>
                <td>${loggedInClient.client.perm_address}</td>
            </tr>
            <tr>
                <th>Provincial Address:</th>
                <td>${loggedInClient.client.prov_address}</td>
            </tr>
            </tbody>
        </table>
    </div>`;
    // --- Section 2: Property Details ---
    let detailsProperty = `
    <div class="soa-info-section">
        <h6>Property Details</h6>
        <table class="table soa-info-table table-borderless w-100 mb-0">
            <tbody>
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
            </tbody>
        </table>
    </div>`;
    // --- Section 3: SOA Details / Account Summary ---
    let detailsSOA = `
    <div class="soa-info-section">
        <h6>SOA / Account Summary</h6>
        <table class="table soa-info-table table-borderless w-100 mb-0">
            <tbody>
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
        </table>
    </div>`;
    // --- Section 4: Payment History ---
    let paymentRows = soa.payment_history.map((p, i) => `
        <tr>
            <td>${i+1}</td>
            <td>${p.due}</td>
            <td>${p.paid}</td>
            <td>${p.status == "Paid" ? '<span class="text-success">Paid</span>' : '<span class="text-danger">Unpaid</span>'}</td>
        </tr>
    `).join('');
    let detailsPayments = `
    <div class="soa-info-section">
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
        </table>
    </div>`;
    // --- Section 5: Legal Note & Portal ---
    let portalNote = `
    <div class="soa-info-section mb-2">
        <h6>Notes</h6>
        <div class="alert alert-info mt-1 mb-2 p-2" style="font-size:14px">
            <b>Tip:</b> You can view all your SOAs and invoices in your portal. Need help? Contact our customer service.
        </div>
        <div><small class="text-muted">
            By signing below you certify that all information provided is true and accurate. Furthermore, you authorize
            Faith and Love Realty and Development Co. to verify all information with any source and to obtain such other
            information as may be required for the purpose of evaluating your application for a purchase of a lot/house/
            farm lot property. Reservation Fee is non-refundable.
        </small></div>
        <div class="row mt-2">
            <div class="col-4"><strong>Prepared by:</strong><br><br>__________________________</div>
            <div class="col-4"><strong>Approved by:</strong><br><br>__________________________</div>
            <div class="col-4"><strong>Received by:</strong><br><br>__________________________</div>
        </div>
    </div>`;
    // --- Print block for print/pdf ---
    let printable = `
    <div id="printable-soa" style="display:none">
        <div class="clearfix mb-2">
            <div class="float-start"><img src="assets/images/logo-dark.png" alt="logo" height="28"></div>
            <div class="float-end"><h3 class="m-0">Statement of Account</h3></div>
        </div>
        <div class="mb-3">${detailsCustomer}</div>
        <div class="mb-3">${detailsProperty}</div>
        <div class="mb-3">${detailsSOA}</div>
        <div class="mb-3">${detailsPayments}</div>
        ${portalNote}
    </div>`;
    // --- Actual preview ---
    $('#soa-preview').html(`
        <div class="noprint d-flex flex-wrap gap-2 mb-3">
            <button class="btn btn-outline-secondary btn-sm" id="btn-soa-print"><i class="ri-printer-line"></i> Print to PDF</button>
        </div>
        <div class="clearfix mb-2">
            <div class="float-start"><img src="assets/images/logo-dark.png" alt="logo" height="28"></div>
            <div class="float-end"><h3 class="m-0">Statement of Account</h3></div>
        </div>
        <div class="mb-3">${detailsCustomer}</div>
        <div class="mb-3">${detailsProperty}</div>
        <div class="mb-3">${detailsSOA}</div>
        <div class="mb-3">${detailsPayments}</div>
        ${portalNote}
        ${printable}
    `);
}
// ----------- MAIN LOGIC -----------
$(function(){
    // Populate date filter options
    let dates = getUniqueDates();
    let dateOpts = '<option value="">All</option>' + dates.map(d => `<option value="${d}">${d}</option>`).join('');
    $('#soa-from, #soa-to').html(dateOpts).prop('disabled', false);
    let selectedSoa = null;

    function refreshTable() {
        let from = $('#soa-from').val();
        let to = $('#soa-to').val();
        let list = filterSoa(from, to);
        renderSoaTable(list);
        renderSoaPreview(null);
        $('#soa-tbody tr').removeClass('table-active');
    }
    $('#soa-from, #soa-to').on('change', refreshTable);

    $('#soa-tbody').on('click', '.soa-preview-link', function(e){
        e.preventDefault();
        let soaNum = $(this).closest('tr').data('soa');
        selectedSoa = soaFullData.find(s => s.soa_number === soaNum);
        renderSoaPreview(selectedSoa);
        $('#soa-tbody tr').removeClass('table-active');
        $(this).closest('tr').addClass('table-active');
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
    // Initial render
    renderSoaTable(soaFullData);
    renderSoaPreview(null);
});
</script>
