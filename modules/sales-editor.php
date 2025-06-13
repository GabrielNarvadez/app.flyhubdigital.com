<div class="container-fluid py-4">
  <div class="row g-4">
    <!-- Left: Sales Entry Form -->
    <div class="col-lg-6">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
          <span class="fw-bold">New Real Estate Sale</span>
          <div id="actionButtons">
            <button class="btn btn-success" id="confirmSaleBtn"><i class="ri-checkbox-circle-line"></i> Confirm Sale</button>
          </div>
        </div>
        <div class="card-body">
          <form id="saleForm" autocomplete="off">
            <!-- Customer -->
            <div class="mb-3">
              <label for="customer" class="form-label fw-semibold">Customer Name</label>
              <select class="form-select" id="customer" required>
                <!-- Populated by JS -->
              </select>
              <div id="newCustomerArea" class="mt-2 d-none">
                <input type="text" class="form-control mb-1" id="newCustomerName" placeholder="Full Name">
                <input type="email" class="form-control mb-1" id="newCustomerEmail" placeholder="Email">
                <input type="text" class="form-control mb-1" id="newCustomerPhone" placeholder="Phone">
                <button type="button" class="btn btn-outline-primary btn-sm" id="addCustomerBtn">Add Customer</button>
              </div>
            </div>
            <!-- Project & Unit Row -->
            <div class="row g-2 mb-3">
              <div class="col">
                <label for="project" class="form-label fw-semibold">Project</label>
                <select class="form-select" id="project" required>
                  <!-- Populated by JS -->
                </select>
              </div>
              <div class="col">
                <label for="unit" class="form-label fw-semibold">Unit</label>
                <select class="form-select" id="unit" required>
                  <!-- Populated by JS -->
                </select>
              </div>
            </div>
            <!-- Date & Reservation Row -->
            <div class="row g-2 mb-3">
              <div class="col">
                <label for="reservationDate" class="form-label fw-semibold">Date of Reservation</label>
                <input type="date" class="form-control" id="reservationDate" required>
              </div>
              <div class="col">
                <label for="reservationAmount" class="form-label fw-semibold">Reservation Amount (₱)</label>
                <input type="number" class="form-control" id="reservationAmount" value="" min="0" step="1000">
              </div>
            </div>
            <!-- Terms & Misc Row -->
            <div class="row g-2 mb-4 align-items-end">
              <div class="col">
                <label for="terms" class="form-label fw-semibold">Payment Terms (months)</label>
                <select class="form-select" id="terms" required>
                  <option value="12">12 months</option>
                  <option value="24">24 months</option>
                  <option value="36">36 months</option>
                  <option value="48">48 months</option>
                </select>
              </div>
              <div class="col">
                <label class="form-label fw-semibold">Miscellaneous Fee</label>
                <div class="bg-light rounded p-2 d-flex gap-2">
                  <div>
                    <input class="form-check-input" type="radio" name="miscOption" id="miscUpfront" value="upfront" checked>
                    <label class="form-check-label" for="miscUpfront">Upfront</label>
                  </div>
                  <div>
                    <input class="form-check-input" type="radio" name="miscOption" id="miscMonthly" value="monthly">
                    <label class="form-check-label" for="miscMonthly">Monthly</label>
                  </div>
                  <div>
                    <input class="form-check-input" type="radio" name="miscOption" id="miscEnd" value="end">
                    <label class="form-check-label" for="miscEnd">End of Terms</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="small text-muted mb-3" id="miscExplanation"></div>
            <!-- Summary (auto) -->
            <div class="mb-2 border rounded p-3 bg-light">
              <div class="fw-semibold">Summary</div>
              <div class="row">
                <div class="col-6 small">
                  <div>Lot Area: <span id="summary-lot-area"></span> sqm</div>
                  <div>Price per sqm: <span id="summary-ppsqm"></span></div>
                  <div>Phase/Block/Lot/Class: <span id="summary-pblc"></span></div>
                  <div>Total Contract Price: <span id="summary-contract"></span></div>
                  <div>Misc Fee (7%): <span id="summary-misc"></span></div>
                  <div>Reservation Fee: <span id="summary-reservation"></span></div>
                </div>
                <div class="col-6 small">
                  <div>Monthly Amortization: <span id="summary-amort"></span></div>
                  <div>Terms: <span id="summary-terms"></span></div>
                  <div>Total Amount Payable: <span id="summary-total"></span></div>
                  <div>Net Selling Price: <span id="summary-net"></span></div>
                  <div>Balance Payable: <span id="summary-balance"></span></div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Right: Contract Preview -->
    <div class="col-lg-6">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <span class="fw-bold">Sale Contract Preview</span>
          <div>
            <button class="btn btn-outline-secondary btn-sm me-1" id="printContract"><i class="ri-printer-line"></i> Print PDF</button>
            <button class="btn btn-outline-primary btn-sm" id="sendContract"><i class="ri-mail-line"></i> Send</button>
          </div>
        </div>
        <div class="card-body" id="contractPreview" style="background:#f7f7f9;min-height:550px; font-family: 'Segoe UI', sans-serif;">
          <!-- Contract details rendered by JS -->
        </div>
      </div>
    </div>
  </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
// DUMMY DATA FOR DEMO
const contacts = [
  { id:1, name:"Ana Del Rosario", email:"ana@email.com", phone:"09171234567" },
  { id:2, name:"Benser Partoza", email:"benser@email.com", phone:"09174567891" },
  { id:3, name:"Maria Santos", email:"maria@email.com", phone:"09178912345" },
];
const projects = [
  { 
    id:1, name:"Parkside Residences",
    site:"Sta. Maria Village, San Mateo, Rizal",
    units:[
      {id:1, label:"Blk 2 Lot 5 (Corner)", phase:2, block:5, lot:5, area:350, class:"Corner", price_per_sqm:3000},
      {id:2, label:"Blk 2 Lot 8 (Inner)", phase:2, block:5, lot:8, area:290, class:"Inner", price_per_sqm:2900}
    ]
  },
  {
    id:2, name:"Faith and Love Farm Estates",
    site:"Madilay-Dilay, Tanay, Rizal",
    units:[
      {id:1, label:"Blk 3 Lot 9B (Inner)", phase:1, block:3, lot:"9B", area:500, class:"Inner", price_per_sqm:2500},
      {id:2, label:"Blk 3 Lot 10A (Prime)", phase:1, block:3, lot:"10A", area:400, class:"Prime", price_per_sqm:3100}
    ]
  },
  {
    id:3, name:"Greenfield Heights",
    site:"Sta. Rosa, Laguna",
    units:[
      {id:1, label:"Blk 1 Lot 2 (Prime)", phase:1, block:1, lot:2, area:250, class:"Prime", price_per_sqm:3200},
      {id:2, label:"Blk 1 Lot 5 (Corner)", phase:1, block:1, lot:5, area:210, class:"Corner", price_per_sqm:3000}
    ]
  }
];
function peso(val) { return "₱" + (val || 0).toLocaleString(); }
function fmt(val) { return val||""; }
function todayStr() { return new Date().toISOString().slice(0,10); }
function asWords(str) { return str.toLowerCase().replace(/(^|\s)\S/g, l => l.toUpperCase()); }

// UI INIT
$(function(){
  function refreshCustomerDropdown() {
    let opts = contacts.map(c=>`<option value="${c.id}">${c.name}</option>`).join('');
    opts += `<option value="new">+ Create new customer</option>`;
    $("#customer").html('<option value="" disabled selected>Select customer</option>'+opts);
  }
  refreshCustomerDropdown();
  $("#customer").on("change", function(){
    $("#newCustomerArea").toggleClass("d-none", $(this).val()!=="new");
  });
  $("#addCustomerBtn").on("click", function(){
    const name = $("#newCustomerName").val().trim();
    const email = $("#newCustomerEmail").val().trim();
    const phone = $("#newCustomerPhone").val().trim();
    if(!name) return alert("Enter customer name.");
    contacts.push({id: contacts.length+1, name, email, phone});
    refreshCustomerDropdown();
    $("#customer").val(contacts.length); // select new
    $("#newCustomerArea").addClass("d-none");
    $("#newCustomerName, #newCustomerEmail, #newCustomerPhone").val("");
    recalc();
  });

  // --- Projects
  let projOpts = projects.map(p=>`<option value="${p.id}">${p.name}</option>`).join('');
  $("#project").html('<option value="" disabled selected>Select project</option>'+projOpts);

  function refreshUnitDropdown() {
    let pid = +$("#project").val();
    if(!pid) { $("#unit").html('<option value="">Select unit</option>'); return; }
    const p = projects.find(x=>x.id===pid);
    let uopts = p.units.map(u=>`<option value="${u.id}">${u.label}</option>`).join('');
    $("#unit").html('<option value="" disabled selected>Select unit</option>'+uopts);
  }
  $("#project").on("change", function(){ refreshUnitDropdown(); recalc(); });
  $("#unit").on("change", recalc);

  $("#customer, #reservationDate, #terms, #reservationAmount, input[name='miscOption']").on("change input", recalc);

  $("input[name='miscOption']").on("change", function(){
    let miscOpt = $("input[name='miscOption']:checked").val();
    let text = miscOpt==="upfront" ? "Miscellaneous fee must be paid in full in advance." :
               miscOpt==="monthly" ? "Miscellaneous fee will be distributed equally across the amortization period." :
               "Miscellaneous fee will be paid in full after all monthly amortizations are completed.";
    $("#miscExplanation").text(text);
    recalc();
  });

  $("#reservationDate").val(todayStr());
  $("#terms, #reservationAmount").on("input", recalc);

  refreshUnitDropdown();
  recalc();

  // Confirm Sale logic
  $("#actionButtons").on("click", "#confirmSaleBtn", function(){
    $(this).hide();
    $("#actionButtons").append(`
      <button class="btn btn-outline-danger me-2" id="cancelSaleBtn"><i class="ri-close-line"></i> Cancel</button>
      <button class="btn btn-outline-secondary" id="resetDraftBtn"><i class="ri-arrow-go-back-line"></i> Reset to Draft</button>
    `);
    $("#saleForm input, #saleForm select").prop("disabled", true);
  });
  // Cancel Sale
  $("#actionButtons").on("click", "#cancelSaleBtn", function(){
    $("#saleForm input, #saleForm select").prop("disabled", false);
    $("#actionButtons").html(`
      <button class="btn btn-success" id="confirmSaleBtn"><i class="ri-checkbox-circle-line"></i> Confirm Sale</button>
    `);
  });
  // Reset to Draft
  $("#actionButtons").on("click", "#resetDraftBtn", function(){
    $("#saleForm")[0].reset();
    $("#saleForm input, #saleForm select").prop("disabled", false);
    $("#actionButtons").html(`
      <button class="btn btn-success" id="confirmSaleBtn"><i class="ri-checkbox-circle-line"></i> Confirm Sale</button>
    `);
    recalc();
  });

  // Print & Send
  $("#printContract").on("click", function(){
    let content = $("#contractPreview").html();
    let w = window.open();
    w.document.write('<html><head><title>Contract Preview</title>');
    w.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">');
    w.document.write('</head><body style="background:#fff;font-family:Segoe UI,sans-serif;">');
    w.document.write(content);
    w.document.write('</body></html>');
    w.document.close();
    setTimeout(()=>w.print(), 400);
  });
  $("#sendContract").on("click", function(){
    alert("Pretend to send contract via email...");
  });
});

// DYNAMIC CALCULATOR
function recalc(){
  const customerId = +$("#customer").val();
  const customer = contacts.find(c=>c.id===customerId) || {};
  const projId = +$("#project").val();
  const proj = projects.find(p=>p.id===projId) || {};
  const unitId = +$("#unit").val();
  const unit = proj.units ? proj.units.find(u=>u.id===unitId) : null;
  const reservationDate = $("#reservationDate").val()||todayStr();
  const terms = +$("#terms").val()||12;
  const reservationFee = +$("#reservationAmount").val()||0;
  const miscOption = $("input[name='miscOption']:checked").val();

  let lotArea = unit ? unit.area : "";
  let ppsqm = unit ? unit.price_per_sqm : "";
  let phase = unit ? unit.phase : "";
  let block = unit ? unit.block : "";
  let lot = unit ? unit.lot : "";
  let lotClass = unit ? unit.class : "";
  let contractPrice = unit ? unit.area * unit.price_per_sqm : 0;
  let miscFee = Math.round(contractPrice * 0.07);
  let totalPayable = contractPrice + miscFee;
  let netSelling = contractPrice;
  let balancePayable = totalPayable - reservationFee;

  let monthlyAmort, miscUpfront=0, miscMonthly=0, miscEnd=0;
  if (miscOption==="upfront") {
    miscUpfront = miscFee;
    monthlyAmort = Math.round((contractPrice - reservationFee) / terms);
    miscMonthly = 0; miscEnd = 0;
  } else if (miscOption==="monthly") {
    miscUpfront = 0;
    miscMonthly = Math.round(miscFee / terms);
    monthlyAmort = Math.round((contractPrice - reservationFee) / terms) + miscMonthly;
    miscEnd = 0;
  } else {
    miscUpfront = 0; miscMonthly = 0;
    miscEnd = miscFee;
    monthlyAmort = Math.round((contractPrice - reservationFee) / terms);
  }

  let amortRows = '';
  for(let i=1;i<=terms;i++) {
    let date = new Date($("#reservationDate").val()||todayStr());
    date.setMonth(date.getMonth()+i);
    let due = date.toISOString().slice(0,10);
    let row = `<tr>
      <td>${i}</td>
      <td>${due}</td>
      <td>${peso(monthlyAmort)}</td>
      <td>${miscMonthly ? peso(miscMonthly) : '-'}</td>
      <td>${peso(monthlyAmort+(miscMonthly||0))}</td>
    </tr>`;
    amortRows += row;
  }
  $("#summary-lot-area").text(lotArea);
  $("#summary-ppsqm").text(peso(ppsqm));
  $("#summary-pblc").text([phase,block,lot,lotClass].filter(x=>x).join(", "));
  $("#summary-contract").text(peso(contractPrice));
  $("#summary-misc").text(peso(miscFee));
  $("#summary-reservation").text(peso(reservationFee));
  $("#summary-amort").text(peso(monthlyAmort));
  $("#summary-terms").text(terms + " months");
  $("#summary-total").text(peso(totalPayable));
  $("#summary-net").text(peso(netSelling));
  $("#summary-balance").text(peso(balancePayable));

  // --- Contract Preview with Sections ---
  let contract = `
  <div class="mb-3 card p-3">
    <h5 class="text-primary mb-2">Client Information</h5>
    <div><b>Name:</b> ${fmt(customer.name)}</div>
    <div><b>Email:</b> ${fmt(customer.email)}</div>
    <div><b>Contact:</b> ${fmt(customer.phone)}</div>
    <div><b>Date of Reservation:</b> ${reservationDate}</div>
  </div>
  <div class="mb-3 card p-3">
    <h5 class="text-primary mb-2">Property Details</h5>
    <div><b>Project Title:</b> ${fmt(proj.name)}</div>
    <div><b>Project Site:</b> ${fmt(proj.site)}</div>
    <div><b>Lot Area:</b> ${lotArea} sqm</div>
    <div><b>Price per sqm:</b> ${peso(ppsqm)}</div>
    <div><b>Phase, Block, Lot, Class:</b> ${[phase,block,lot,lotClass].filter(x=>x).join(", ")}</div>
  </div>
  <div class="mb-3 card p-3">
    <h5 class="text-primary mb-2">Financial Summary</h5>
    <div><b>Total Contract Price:</b> ${peso(contractPrice)}</div>
    <div><b>Miscellaneous Fee (7%):</b> ${peso(miscFee)}</div>
    <div><b>Reservation Fee:</b> ${peso(reservationFee)}</div>
    <div><b>Total Amount Payable:</b> ${peso(totalPayable)}</div>
    <div><b>Net Selling Price:</b> ${peso(netSelling)}</div>
    <div><b>Balance Payable:</b> ${peso(balancePayable)}</div>
    <div><b>Payment Terms:</b> ${terms} months</div>
    <div><b>Monthly Amortization:</b> ${peso(monthlyAmort)}</div>
    <div><b>Misc Fee Payment Option:</b> ${asWords(miscOption || '')}</div>
    ${miscUpfront ? `<div class="text-success mt-2"><b>Miscellaneous fee is to be paid upfront: ${peso(miscFee)}</b></div>` : ''}
    ${miscMonthly ? `<div class="text-success mt-2"><b>Miscellaneous fee of ${peso(miscMonthly)} will be added to monthly amortization.</b></div>` : ''}
    ${miscEnd ? `<div class="text-success mt-2"><b>Miscellaneous fee is to be paid at the end of terms: ${peso(miscFee)}</b></div>` : ''}
  </div>
  <div class="mb-3 card p-3">
    <h5 class="text-primary mb-2">Amortization Payment Schedule</h5>
    <div class="table-responsive">
      <table class="table table-sm table-bordered mb-0">
        <thead class="table-light">
          <tr><th>#</th><th>Due Date</th><th>Principal</th><th>Misc</th><th>Total</th></tr>
        </thead>
        <tbody>${amortRows}</tbody>
      </table>
    </div>
  </div>
  <div class="mb-3 card p-3">
    <h5 class="text-primary mb-2">Notes & Legal</h5>
    <div class="small">
      <ul>
        <li>Reservation fee is strictly non-refundable and non-transferable.</li>
        <li>Monthly amortization must be paid on or before the due date indicated above. Late payments are subject to a penalty of 3% per month on the outstanding amount.</li>
        <li>Ownership of the property will only be transferred upon full payment of the total contract price, including all fees and charges.</li>
        <li>Miscellaneous fee covers documentation, transfer, and processing costs.</li>
        <li>Client authorizes Faith and Love Realty and Development Co. to process personal data and perform necessary verification, in accordance with the Data Privacy Act of 2012 (RA 10173).</li>
      </ul>
    </div>
    <hr>
    <div class="row mb-2 mt-2">
      <div class="col-4">
        <strong>Prepared by:</strong><br><br>__________________________
      </div>
      <div class="col-4">
        <strong>Approved by:</strong><br><br>__________________________
      </div>
      <div class="col-4">
        <strong>Received by:</strong><br><br>__________________________
      </div>
    </div>
    <div class="text-muted small">Faith and Love Realty and Development Co. &copy; 2024. All Rights Reserved.</div>
  </div>
  `;
  $("#contractPreview").html(contract);
}
</script>
