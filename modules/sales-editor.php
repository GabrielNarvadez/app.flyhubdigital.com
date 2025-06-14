
<div class="container-fluid py-4">
  <?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <div class="row g-4">
    <!-- Left: Sales Entry Form -->
    <div class="col-lg-6">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
          <span class="fw-bold">New Real Estate Sale</span>
          <div id="actionButtons">
            <!-- Confirm button here -->
          </div>
        </div>
        <div class="card-body">
          <form id="saleForm" method="POST" autocomplete="off">
            <!-- Customer -->
            <div class="mb-3">
              <label for="customer" class="form-label fw-semibold">Customer Name</label>
              <select class="form-select" id="customer" name="contact_id" required>
                <option value="" disabled selected>Select customer</option>
                <?php foreach ($contacts as $c): ?>
                  <option value="<?= htmlspecialchars($c['id']) ?>"
                    data-email="<?= htmlspecialchars($c['email']) ?>"
                    data-phone="<?= htmlspecialchars($c['phone_number']) ?>"
                  >
                    <?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?>
                  </option>
                <?php endforeach; ?>
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
                <select class="form-select" id="project" name="project_title" required>
                  <option value="" disabled selected>Select project</option>
                  <?php foreach ($projects as $p): ?>
                    <option value="<?= htmlspecialchars($p['project_title']) ?>">
                      <?= htmlspecialchars($p['project_title']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col">
                <label for="unit" class="form-label fw-semibold">Unit</label>
                <select class="form-select" id="unit" name="unit_id" required>
                  <option value="" disabled selected>Select unit</option>
                  <?php foreach ($units as $u): ?>
                    <option 
                      value="<?= htmlspecialchars($u['id']) ?>"
                      data-project="<?= htmlspecialchars($u['project_title']) ?>"
                      data-site="<?= htmlspecialchars($u['site']) ?>"
                      data-block="<?= htmlspecialchars($u['block_no']) ?>"
                      data-lot="<?= htmlspecialchars($u['lot_no']) ?>"
                      data-phase="<?= htmlspecialchars($u['phase_no']) ?>"
                      data-class="<?= htmlspecialchars($u['lot_class']) ?>"
                      data-area="<?= htmlspecialchars($u['lot_area']) ?>"
                      data-ppsqm="<?= htmlspecialchars($u['price_per_sqm']) ?>"
                    >
                      Blk <?= htmlspecialchars($u['block_no']) ?> Lot <?= htmlspecialchars($u['lot_no']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>

                </select>
              </div>
            </div>
            <!-- Date & Reservation Row -->
            <div class="row g-2 mb-3">
              <div class="col">
                <label for="reservationDate" class="form-label fw-semibold">Date of Reservation</label>
                <input type="date" class="form-control" id="reservationDate" name="reservationDate" required>
              </div>
              <div class="col">
                <label for="reservationAmount" class="form-label fw-semibold">Reservation Amount (₱)</label>
                <input type="number" class="form-control" id="reservationAmount" name="reservationAmount" value="" min="0" step="1000">
              </div>
            </div>
            <!-- Terms & Misc Row -->
            <div class="row g-2 mb-4 align-items-end">
              <div class="col">
                <label for="terms" class="form-label fw-semibold">Payment Terms (months)</label>
                <select class="form-select" id="terms" name="terms" required>
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
            <!-- The real submit button for PHP POST (hidden, auto-triggered by Confirm button) -->
            <button class="d-none" type="submit" id="phpRealSubmit" name="confirm_sale"></button>
          </form>
        </div>
        <div class="card-footer bg-white border-0">
          <button class="btn btn-success w-100" id="confirmSaleBtn">
            <i class="ri-checkbox-circle-line"></i> Reserve Unit
          </button>
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
function peso(val) { return "₱" + (parseFloat(val) || 0).toLocaleString(); }
function fmt(val) { return val || ""; }
function todayStr() { return new Date().toISOString().slice(0,10); }
function asWords(str) { return str ? str.toLowerCase().replace(/(^|\s)\S/g, l => l.toUpperCase()) : ''; }
const unitsJS = <?php echo json_encode($units); ?>;
const contactsJS = <?php echo json_encode($contacts); ?>;
const projectsJS = <?php echo json_encode($projects); ?>;

$(function(){
  $("#saleForm").on("change input", "select, input", recalc);
  $("#customer").on("change", function(){ $("#newCustomerArea").toggleClass("d-none", $(this).val() !== "new"); recalc(); });
  $("input[name='miscOption']").on("change", function(){
    let miscOpt = $("input[name='miscOption']:checked").val();
    let text = miscOpt === "upfront" ? "Miscellaneous fee must be paid in full in advance."
             : miscOpt === "monthly" ? "Miscellaneous fee will be distributed equally across the amortization period."
             : "Miscellaneous fee will be paid in full after all monthly amortizations are completed.";
    $("#miscExplanation").text(text); recalc();
  });
  $("#reservationDate").val(todayStr());
  $("#printContract").on("click", function(){
    let content = $("#contractPreview").html();
    let w = window.open();
    w.document.write('<html><head><title>Contract Preview</title>');
    w.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">');
    w.document.write('</head><body style="background:#fff;font-family:Segoe UI,sans-serif;">');
    w.document.write(content); w.document.write('</body></html>');
    w.document.close();
    setTimeout(()=>w.print(), 400);
  });
  $("#sendContract").on("click", function(){
    alert("Pretend to send contract via email...");
  });

  // Handle Confirm Sale
  $("#confirmSaleBtn").on("click", function(e){
    e.preventDefault();
    if (confirm("Are you sure you want to record this sale? This cannot be undone.")) {
      $("#phpRealSubmit").click();
      $(this).prop("disabled", true);
    }
  });
  recalc();
});

function recalc() {
  let customerId = $("#customer").val();
  let unitId = $("#unit").val();
  let reservationDate = $("#reservationDate").val() || todayStr();
  let terms = +($("#terms").val()) || 12;
  let reservationFee = +($("#reservationAmount").val()) || 0;
  let miscOption = $("input[name='miscOption']:checked").val();

  let customer = contactsJS.find(c => c.id == customerId) || {};
  let unit = unitsJS.find(u => u.id == unitId) || {};

  let lotArea = parseFloat(unit.lot_area) || 0;
  let ppsqm = parseFloat(unit.price_per_sqm) || 0;
  let phase = fmt(unit.phase);
  let block = fmt(unit.block);
  let lot = fmt(unit.lot);
  let lotClass = fmt(unit.lot_class);
  let contractPrice = lotArea * ppsqm;
  let miscFee = Math.round(contractPrice * 0.07);
  let totalPayable = contractPrice + miscFee;
  let netSelling = contractPrice;
  let balancePayable = totalPayable - reservationFee;

  let monthlyAmort, miscUpfront=0, miscMonthly=0, miscEnd=0;
  if (miscOption === "upfront") {
    miscUpfront = miscFee;
    monthlyAmort = Math.round((contractPrice - reservationFee) / terms);
    miscMonthly = 0; miscEnd = 0;
  } else if (miscOption === "monthly") {
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
  for(let i=1; i<=terms; i++) {
    let date = new Date(reservationDate);
    date.setMonth(date.getMonth() + i);
    let due = date.toISOString().slice(0,10);
    amortRows += `<tr>
      <td>${i}</td>
      <td>${due}</td>
      <td>${peso(monthlyAmort)}</td>
      <td>${miscMonthly ? peso(miscMonthly) : '-'}</td>
      <td>${peso(monthlyAmort + (miscMonthly||0))}</td>
    </tr>`;
  }

  $("#summary-lot-area").text(lotArea || "");
  $("#summary-ppsqm").text(ppsqm ? peso(ppsqm) : "");
  $("#summary-pblc").text([phase,block,lot,lotClass].filter(x=>x).join(", "));
  $("#summary-contract").text(peso(contractPrice));
  $("#summary-misc").text(peso(miscFee));
  $("#summary-reservation").text(peso(reservationFee));
  $("#summary-amort").text(peso(monthlyAmort));
  $("#summary-terms").text(terms + " months");
  $("#summary-total").text(peso(totalPayable));
  $("#summary-net").text(peso(netSelling));
  $("#summary-balance").text(peso(balancePayable));

  // Contract preview section
  let preview = `
  <div class="mb-3 card p-3">
    <h5 class="text-primary mb-2">Client Information</h5>
    <div><b>Name:</b> ${fmt(customer.first_name)} ${fmt(customer.last_name)}</div>
    <div><b>Email:</b> ${fmt(customer.email)}</div>
    <div><b>Contact:</b> ${fmt(customer.phone_number)}</div>
    <div><b>Date of Reservation:</b> ${reservationDate}</div>
  </div>
  <div class="mb-3 card p-3">
    <h5 class="text-primary mb-2">Property Details</h5>
    <div><b>Project Title:</b> ${fmt(unit.project_title)}</div>
    <div><b>Project Site:</b> ${fmt(unit.project_site)}</div>
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
  `;
  $("#contractPreview").html(preview);
}
</script>
