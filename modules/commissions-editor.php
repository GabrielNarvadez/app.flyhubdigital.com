<div class="container-fluid py-4">
  <div class="row g-4">
    <!-- Left: Commission Calculator Form -->
    <div class="col-lg-6">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-bold d-flex align-items-center justify-content-between">
          <span>Commission Calculator</span>
          <button class="btn btn-success" id="confirmCommissionBtn"><i class="ri-checkbox-circle-line"></i> Confirm Commission</button>
        </div>
        <div class="card-body">
          <form id="commissionForm" autocomplete="off">
            <!-- Recipient & Role -->
            <div class="row g-2 mb-3">
              <div class="col-7">
                <label class="form-label fw-semibold">Recipient</label>
                <select class="form-select" id="recipient" required></select>
              </div>
              <div class="col-5">
                <label class="form-label fw-semibold">Role</label>
                <select class="form-select" id="role" required>
                  <option value="Agent">Agent</option>
                  <option value="Broker">Broker</option>
                  <option value="Manager">Manager</option>
                </select>
              </div>
            </div>
            <!-- Project & Unit -->
            <div class="row g-2 mb-3">
              <div class="col">
                <label class="form-label fw-semibold">Project</label>
                <select class="form-select" id="project" required></select>
              </div>
              <div class="col">
                <label class="form-label fw-semibold">Unit</label>
                <select class="form-select" id="unit" required></select>
              </div>
            </div>
            <!-- Transaction Type & Reservation -->
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label fw-semibold">Transaction Type</label>
                <select class="form-select" id="txnType" required>
                  <option value="HouseLot">House & Lot</option>
                  <option value="LotOnly">Lot Only</option>
                </select>
              </div>
              <div class="col-6">
                <label class="form-label fw-semibold">Reservation Amount</label>
                <input type="number" class="form-control" id="reservation" min="0" value="50000">
              </div>
            </div>
            <!-- Downpayment & TCP -->
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label fw-semibold">Downpayment Amount</label>
                <input type="number" class="form-control" id="downpayment" min="0" value="250000">
              </div>
              <div class="col-6">
                <label class="form-label fw-semibold">Total Contract Price</label>
                <input type="number" class="form-control" id="tcp" min="0" value="1250000" readonly>
              </div>
            </div>
            <!-- Notes -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Custom Notes</label>
              <input type="text" class="form-control" id="customNotes" placeholder="(Optional)">
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Right: Preview/Agreement -->
    <div class="col-lg-6">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <span class="fw-bold">Commission Agreement Preview</span>
          <div>
            <button class="btn btn-outline-secondary btn-sm me-1" id="printAgreement"><i class="ri-printer-line"></i> Print PDF</button>
            <button class="btn btn-outline-primary btn-sm" id="sendAgreement"><i class="ri-mail-line"></i> Send</button>
          </div>
        </div>
        <div class="card-body" id="agreementPreview" style="background:#f7f7f9;min-height:450px; font-family: 'Segoe UI', sans-serif;">
          <!-- Agreement details rendered by JS -->
        </div>
      </div>
    </div>
  </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
// DUMMY DATA FOR DEMO
const recipients = [
  { id:1, name:"Ramon Santos", role: "Broker", email:"ramon.broker@email.com" },
  { id:2, name:"Linda Chua", role: "Agent", email:"linda.agent@email.com" },
  { id:3, name:"Liza Manalo", role: "Manager", email:"liza.manager@email.com" }
];
const projects = [
  { 
    id:1, name:"Parkside Residences",
    site:"Sta. Maria Village, San Mateo, Rizal",
    units:[
      {id:1, label:"Blk 2 Lot 5 (Corner)", phase:2, block:5, lot:5, area:350, class:"Corner", price_per_sqm:3000, tcp: 12500000},
      {id:2, label:"Blk 2 Lot 8 (Inner)", phase:2, block:5, lot:8, area:290, class:"Inner", price_per_sqm:2900, tcp: 8700000}
    ]
  },
  {
    id:2, name:"Faith and Love Farm Estates",
    site:"Madilay-Dilay, Tanay, Rizal",
    units:[
      {id:1, label:"Blk 3 Lot 9B (Inner)", phase:1, block:3, lot:"9B", area:500, class:"Inner", price_per_sqm:2500, tcp: 1500000},
      {id:2, label:"Blk 3 Lot 10A (Prime)", phase:1, block:3, lot:"10A", area:400, class:"Prime", price_per_sqm:3100, tcp: 1240000}
    ]
  }
];

function peso(val) { return "₱" + (val || 0).toLocaleString(); }
function fmt(val) { return val||""; }
function todayStr() { return new Date().toISOString().slice(0,10); }
function asWords(str) { return str.toLowerCase().replace(/(^|\s)\S/g, l => l.toUpperCase()); }

$(function(){
  // Recipients dropdown
  $("#recipient").html('<option value="" disabled selected>Select recipient</option>' +
    recipients.map(r=>`<option value="${r.id}">${r.name} (${r.role})</option>`).join('')
  );
  // Projects
  let projOpts = projects.map(p=>`<option value="${p.id}">${p.name}</option>`).join('');
  $("#project").html('<option value="" disabled selected>Select project</option>'+projOpts);

  function refreshUnitDropdown() {
    let pid = +$("#project").val();
    if(!pid) { $("#unit").html('<option value="">Select unit</option>'); return; }
    const p = projects.find(x=>x.id===pid);
    let uopts = p.units.map(u=>`<option value="${u.id}">${u.label}</option>`).join('');
    $("#unit").html('<option value="" disabled selected>Select unit</option>'+uopts);
    // TCP auto-fill
    $("#tcp").val('');
    $("#downpayment").val('');
    $("#reservation").val('');
  }
  $("#project").on("change", function(){ refreshUnitDropdown(); recalc(); });
  $("#unit").on("change", function(){
    const pid = +$("#project").val();
    const uid = +$("#unit").val();
    const proj = projects.find(x=>x.id===pid) || {};
    const unit = proj.units ? proj.units.find(u=>u.id===uid) : null;
    if(unit){
      $("#tcp").val(unit.tcp || unit.area*unit.price_per_sqm || 0);
      if($("#txnType").val() === "HouseLot") {
        $("#downpayment").val(250000); $("#reservation").val(50000);
      } else {
        $("#downpayment").val(0); $("#reservation").val(5000);
      }
    }
    recalc();
  });

  $("#txnType").on("change", function(){
    // Update default DP/reservation for each transaction type
    const t = $(this).val();
    if(t === "HouseLot") {
      $("#downpayment").val(250000); $("#reservation").val(50000);
    } else {
      $("#downpayment").val(0); $("#reservation").val(5000);
    }
    recalc();
  });

  $("#recipient, #role, #project, #unit, #reservation, #downpayment, #tcp, #customNotes").on("input change", recalc);

  // Confirm Commission state
  $("#confirmCommissionBtn").on("click", function(){
    $(this).hide();
    $(this).after(`
      <button class="btn btn-outline-danger me-2" id="cancelCommissionBtn"><i class="ri-close-line"></i> Cancel</button>
      <button class="btn btn-outline-secondary" id="resetCommissionBtn"><i class="ri-arrow-go-back-line"></i> Reset to Draft</button>
    `);
    $("#commissionForm input, #commissionForm select").prop("disabled", true);
  });
  $("#commissionForm").on("click", "#cancelCommissionBtn", function(){
    $("#commissionForm input, #commissionForm select").prop("disabled", false);
    $("#cancelCommissionBtn, #resetCommissionBtn").remove();
    $("#confirmCommissionBtn").show();
  });
  $("#commissionForm").on("click", "#resetCommissionBtn", function(){
    $("#commissionForm")[0].reset();
    $("#commissionForm input, #commissionForm select").prop("disabled", false);
    $("#cancelCommissionBtn, #resetCommissionBtn").remove();
    $("#confirmCommissionBtn").show();
    recalc();
  });

  // Print & Send
  $("#printAgreement").on("click", function(){
    let content = $("#agreementPreview").html();
    let w = window.open();
    w.document.write('<html><head><title>Commission Agreement</title>');
    w.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">');
    w.document.write('</head><body style="background:#fff;font-family:Segoe UI,sans-serif;">');
    w.document.write(content);
    w.document.write('</body></html>');
    w.document.close();
    setTimeout(()=>w.print(), 400);
  });
  $("#sendAgreement").on("click", function(){
    alert("Pretend to send agreement via email...");
  });

  // Init
  refreshUnitDropdown();
  recalc();
});

function recalc(){
  const recipientId = +$("#recipient").val();
  const recipient = recipients.find(r=>r.id===recipientId) || {};
  const role = $("#role").val() || recipient.role || "";
  const projId = +$("#project").val();
  const proj = projects.find(p=>p.id===projId) || {};
  const unitId = +$("#unit").val();
  const unit = proj.units ? proj.units.find(u=>u.id===unitId) : null;
  const txnType = $("#txnType").val() || "HouseLot";
  const reservation = +$("#reservation").val()||0;
  const downpayment = +$("#downpayment").val()||0;
  const tcp = +$("#tcp").val()||0;
  const notes = $("#customNotes").val();

  // Commission logic from sample
  let commRate = (txnType==="HouseLot") ? 0.05 : 0.10;
  let fullComm = tcp * commRate;
  let releaseScheme = '', vat = 0, cwt = 0, netComm = 0, firstRelease = 0, monthlyRelease = 0, nMonths = 1;
  if(txnType==="HouseLot"){
    releaseScheme = `
      <li><b>20%</b> of total commission (₱${(fullComm*0.20).toLocaleString()}) is released after full downpayment.</li>
      <li><b>80%</b> (₱${(fullComm*0.80).toLocaleString()}) is released after complete DP.</li>
      <li>Kaliwaan: ₱5,000 upon contract signing.</li>
    `;
    vat = 0; cwt = 0; netComm = fullComm;
    firstRelease = fullComm*0.2 + 5000;
    monthlyRelease = 0;
    nMonths = 0;
  } else {
    vat = fullComm/1.12 * 0.12;
    cwt = (fullComm/1.12) * 0.10;
    netComm = fullComm - vat - cwt;
    monthlyRelease = netComm/25;
    nMonths = 25;
    releaseScheme = `
      <li>Commission (net of VAT and CWT) will be released in <b>25 monthly tranches</b> of <b>₱${monthlyRelease.toLocaleString(undefined,{minimumFractionDigits:2})}</b> each.</li>
      <li>Only released if no payment is overdue for buyer.</li>
    `;
  }

  // Agreement preview (sections/cards)
  let html = `
  <div class="mb-3 card p-3">
    <h5 class="text-primary mb-2">Recipient Information</h5>
    <div><b>Name:</b> ${fmt(recipient.name)}</div>
    <div><b>Email:</b> ${fmt(recipient.email)}</div>
    <div><b>Role:</b> ${fmt(role)}</div>
    <div><b>Date:</b> ${todayStr()}</div>
  </div>
  <div class="mb-3 card p-3">
    <h5 class="text-primary mb-2">Transaction Details</h5>
    <div><b>Project:</b> ${fmt(proj.name)}</div>
    <div><b>Unit:</b> ${unit ? unit.label : ""}</div>
    <div><b>Site:</b> ${fmt(proj.site)}</div>
    <div><b>Transaction Type:</b> ${txnType==="HouseLot"?"House & Lot":"Lot Only"}</div>
    <div><b>TCP:</b> ${peso(tcp)}</div>
    <div><b>Reservation:</b> ${peso(reservation)}</div>
    <div><b>Downpayment:</b> ${peso(downpayment)}</div>
  </div>
  <div class="mb-3 card p-3">
    <h5 class="text-primary mb-2">Commission Computation</h5>
    <div><b>Commission Rate:</b> ${(commRate*100).toFixed(0)}%</div>
    <div><b>Gross Commission:</b> ${peso(fullComm)}</div>
    ${txnType==="LotOnly" ? `
      <div><b>Net of VAT (12%):</b> ${peso(vat)}</div>
      <div><b>Less CWT (10%):</b> ${peso(cwt)}</div>
      <div><b>Net Commission:</b> ${peso(netComm)}</div>
    ` : ""}
    <ul class="mb-1">${releaseScheme}</ul>
    ${monthlyRelease ? `<div><b>First Release:</b> ${peso(monthlyRelease)}</div>` : ""}
    ${firstRelease ? `<div><b>First Release:</b> ${peso(firstRelease)}</div>` : ""}
    ${notes ? `<div><b>Custom Notes:</b> ${notes}</div>` : ""}
  </div>
  <div class="mb-3 card p-3">
    <h5 class="text-primary mb-2">Signatories & Approvals</h5>
    <div class="row mb-2 mt-2">
      <div class="col-4">
        <strong>Prepared by:</strong><br><br>__________________________
      </div>
      <div class="col-4">
        <strong>Approved by:</strong><br><br>__________________________
      </div>
      <div class="col-4">
        <strong>Recipient:</strong><br><br>__________________________
      </div>
    </div>
    <div class="small text-muted">Faith and Love Realty and Development Co. &copy; 2024. All Rights Reserved.</div>
  </div>
  `;
  $("#agreementPreview").html(html);
}
</script>
