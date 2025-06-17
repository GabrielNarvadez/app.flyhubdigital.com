

<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="container-fluid py-4">
  <div class="row g-4">
    <!-- Left: Form -->
    <div class="col-lg-6">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <span class="fw-bold">New Real Estate Sale</span>
          <button class="btn btn-success" id="confirmSaleBtn"><i class="ri-checkbox-circle-line"></i> Reserve Unit</button>
        </div>
        <div class="card-body">
        <div id="contactAlert"></div>
          <form id="saleForm" autocomplete="off">
            <!-- Customer -->

            <div class="mb-3">
              <label for="contactSelect" class="form-label">Customer Name</label>
              <div class="d-flex" style="gap:8px">
                <select class="form-select" id="contactSelect" style="width:100%" required>
                  <option value="" disabled selected>Select customer...</option>
                  <?php foreach ($contacts as $c): ?>
                    <option value="<?= $c['id'] ?>"
                      data-email="<?= htmlspecialchars($c['email']) ?>"
                      data-phone="<?= htmlspecialchars($c['phone_number']) ?>"
                    >
                      <?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <button type="button" class="btn btn-outline-primary" id="newContactBtn" style="white-space:nowrap;">
                  <i class="ri-user-add-line"></i> Create New Contact 
                </button>
              </div>
            </div>

            <!-- Project/Unit -->
            <div class="row g-2 mb-3">
              <div class="col">
                <label for="unitSelect" class="form-label">Unit</label>
                <select class="form-select" id="unitSelect" required>
                  <option value="" disabled selected>Select unit...</option>
                  <?php foreach ($units as $u): ?>
                    <option value="<?= $u['id'] ?>"
                      data-proj="<?= htmlspecialchars($u['project_title']) ?>"
                      data-site="<?= htmlspecialchars($u['project_site']) ?>"
                      data-block="<?= htmlspecialchars($u['block']) ?>"
                      data-lot="<?= htmlspecialchars($u['lot']) ?>"
                      data-phase="<?= htmlspecialchars($u['phase']) ?>"
                      data-class="<?= htmlspecialchars($u['lot_class']) ?>"
                      data-area="<?= htmlspecialchars($u['lot_area']) ?>"
                      data-ppsqm="<?= htmlspecialchars($u['price_per_sqm']) ?>"
                    >
                      <?= htmlspecialchars($u['project_title'] . " | Blk " . $u['block'] . " Lot " . $u['lot'] . " (" . $u['lot_class'] . ")") ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <!-- Date/Reservation -->
            <div class="row g-2 mb-3">
              <div class="col">
                <label for="reservationDate" class="form-label">Date of Reservation</label>
                <input type="date" class="form-control" id="reservationDate" value="<?= date('Y-m-d') ?>" required>
              </div>
              <div class="col">
                <label for="reservationAmount" class="form-label">Reservation Amount (₱)</label>
                <input type="number" class="form-control" id="reservationAmount" value="0" min="0" step="1000">
              </div>
            </div>
            <!-- Terms/Misc Fee -->
            <div class="row g-2 mb-4 align-items-end">
              <div class="col">
                <label for="terms" class="form-label">Payment Terms (months)</label>
                <select class="form-select" id="terms" required>
                  <option value="12">12 months</option>
                  <option value="24">24 months</option>
                  <option value="36">36 months</option>
                  <option value="48">48 months</option>
                </select>
              </div>
              <div class="col">
                <label class="form-label">Miscellaneous Fee</label>
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
            <!-- Summary Card (auto-filled) -->
            <div class="mb-2 border rounded p-3 bg-light">
              <div class="fw-semibold">Summary</div>
              <div class="row">
                <div class="col-6 small">
                  <div>Lot Area: <span id="summary-lot-area">0</span> sqm</div>
                  <div>Price per sqm: <span id="summary-ppsqm">₱0</span></div>
                  <div>Phase/Block/Lot/Class: <span id="summary-pblc"></span></div>
                  <div>Total Contract Price: <span id="summary-contract">₱0</span></div>
                  <div>Misc Fee (7%): <span id="summary-misc">₱0</span></div>
                  <div>Reservation fee: <span id="summary-reservation">₱0</span></div>
                </div>
                <div class="col-6 small">
                  <div>Monthly Amortization: <span id="summary-amort">₱0</span></div>
                  <div>Terms: <span id="summary-terms">0 months</span></div>
                  <div>Total Amount Payable: <span id="summary-total">₱0</span></div>
                  <div>Net Selling Price: <span id="summary-net">₱0</span></div>
                  <div>Balance Payable: <span id="summary-balance">₱0</span></div>
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
        <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
          <span>Sale Contract Preview</span>
          <div>
            <button class="btn btn-outline-secondary btn-sm me-1" id="printContract"><i class="ri-printer-line"></i> Print PDF</button>
            <button class="btn btn-outline-primary btn-sm" id="sendContract"><i class="ri-mail-line"></i> Send</button>
          </div>
        </div>
        <div class="card-body" id="contractPreview" style="background:#f7f7f9;min-height:550px; font-family: 'Segoe UI', sans-serif;">
          <!-- Preview rendered by JS -->
        </div>
      </div>
    </div>
  </div>
</div>

<!-- New Contact Modal -->
<div class="modal fade" id="newContactModal" tabindex="-1" aria-labelledby="newContactModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="newContactForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newContactModalLabel">Create New Contact</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2 mb-2">
          <div class="col">
            <label class="form-label">First Name</label>
            <input type="text" class="form-control" name="first_name" required>
          </div>
          <div class="col">
            <label class="form-label">Last Name</label>
            <input type="text" class="form-control" name="last_name" required>
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email">
        </div>
        <div class="mb-2">
          <label class="form-label">Phone</label>
          <input type="text" class="form-control" name="phone">
        </div>
        <div class="mb-2">
          <label class="form-label">City</label>
          <input type="text" class="form-control" name="city">
        </div>
        <div class="alert alert-info small mt-2 mb-0">
          You can add the rest of the contact info in their profile after creation.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Create Contact</button>
      </div>
    </form>
  </div>
</div>

<script>
// --- FORM FIELD LOGIC & SELECT2
$('#contactSelect').select2({ width: '100%', placeholder: "Select or search contact..." });
$('#unitSelect').select2({ width: '100%', placeholder: "Select or search unit..." });

function peso(val) {
  let v = Math.round(val||0); return "₱" + v.toLocaleString();
}

function updatePreviewAndSummary() {
  // Get values from form
  let c = $("#contactSelect option:selected");
  let u = $("#unitSelect option:selected");
  let resDate = $("#reservationDate").val() || '';
  let resAmt = parseFloat($("#reservationAmount").val()) || 0;
  let terms = parseInt($("#terms").val(),10) || 12;
  let miscOption = $("input[name='miscOption']:checked").val() || "upfront";

  // Contact info
  let customer = c.val() ? {
    name: c.text(),
    email: c.data("email"),
    phone: c.data("phone")
  } : {name: "", email: "", phone: ""};

  // Unit info
  let unit = u.val() ? {
    proj: u.data("proj"),
    site: u.data("site"),
    block: u.data("block"),
    lot: u.data("lot"),
    phase: u.data("phase"),
    class: u.data("class"),
    area: parseFloat(u.data("area"))||0,
    ppsqm: parseFloat(u.data("ppsqm"))||0
  } : {proj:"",site:"",block:"",lot:"",phase:"",class:"",area:0,ppsqm:0};

  // Financials
  let contractPrice = unit.area * unit.ppsqm;
  let miscFee = contractPrice * 0.07;
  let totalPayable = contractPrice + miscFee;
  let netSelling = contractPrice;
  let balancePayable = totalPayable - resAmt;
  let monthlyAmort = (contractPrice - resAmt)/terms;
  let miscUpfront=0, miscMonthly=0, miscEnd=0;
  if (miscOption=="upfront") {
    miscUpfront = miscFee;
  } else if (miscOption=="monthly") {
    miscMonthly = miscFee/terms;
    monthlyAmort += miscMonthly;
  } else {
    miscEnd = miscFee;
  }

  // Summary update
  $("#summary-lot-area").text(unit.area);
  $("#summary-ppsqm").text(peso(unit.ppsqm));
  $("#summary-pblc").text([unit.phase,unit.block,unit.lot,unit.class].filter(x=>x).join(", "));
  $("#summary-contract").text(peso(contractPrice));
  $("#summary-misc").text(peso(miscFee));
  $("#summary-reservation").text(peso(resAmt));
  $("#summary-amort").text(peso(monthlyAmort));
  $("#summary-terms").text(terms+" months");
  $("#summary-total").text(peso(totalPayable));
  $("#summary-net").text(peso(netSelling));
  $("#summary-balance").text(peso(balancePayable));

  // Amortization table
  let amortRows = '';
  let date = resDate ? new Date(resDate) : new Date();
  for(let i=1;i<=terms;i++) {
    let payDate = new Date(date); payDate.setMonth(payDate.getMonth()+i);
    let due = payDate.toISOString().slice(0,10);
    let miscThis = miscMonthly ? peso(miscMonthly) : (i===terms && miscEnd ? peso(miscFee): "-");
    let total = peso(monthlyAmort + (miscMonthly ? miscMonthly : (i===terms&&miscEnd?miscEnd:0)));
    amortRows += `<tr>
      <td>${i}</td>
      <td>${due}</td>
      <td>${peso(monthlyAmort)}</td>
      <td>${miscThis}</td>
      <td>${total}</td>
    </tr>`;
  }

  // --- Contract Preview
  let contract = `
    <div class="card mb-2 p-3">
      <h6 class="text-primary mb-2">Client Information</h6>
      <div><b>Name:</b> ${customer.name}</div>
      <div><b>Email:</b> ${customer.email}</div>
      <div><b>Contact:</b> ${customer.phone}</div>
      <div><b>Date of Reservation:</b> ${resDate}</div>
    </div>
    <div class="card mb-2 p-3">
      <h6 class="text-primary mb-2">Property Details</h6>
      <div><b>Project Title:</b> ${unit.proj}</div>
      <div><b>Project Site:</b> ${unit.site}</div>
      <div><b>Lot Area:</b> ${unit.area} sqm</div>
      <div><b>Price per sqm:</b> ${peso(unit.ppsqm)}</div>
      <div><b>Phase, Block, Lot, Class:</b> ${[unit.phase,unit.block,unit.lot,unit.class].filter(x=>x).join(", ")}</div>
    </div>
    <div class="card mb-2 p-3">
      <h6 class="text-primary mb-2">Financial Summary</h6>
      <div><b>Total Contract Price:</b> ${peso(contractPrice)}</div>
      <div><b>Miscellaneous Fee (7%):</b> ${peso(miscFee)}</div>
      <div><b>Reservation Fee:</b> ${peso(resAmt)}</div>
      <div><b>Total Amount Payable:</b> ${peso(totalPayable)}</div>
      <div><b>Net Selling Price:</b> ${peso(netSelling)}</div>
      <div><b>Balance Payable:</b> ${peso(balancePayable)}</div>
      <div><b>Payment Terms:</b> ${terms} months</div>
      <div><b>Monthly Amortization:</b> ${peso(monthlyAmort)}</div>
      <div><b>Misc Fee Payment Option:</b> ${miscOption.charAt(0).toUpperCase()+miscOption.slice(1)}</div>
    </div>
    <div class="card mb-2 p-3">
      <h6 class="text-primary mb-2">Amortization Payment Schedule</h6>
      <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0">
          <thead class="table-light">
            <tr><th>#</th><th>Due Date</th><th>Principal</th><th>Misc</th><th>Total</th></tr>
          </thead>
          <tbody>${amortRows}</tbody>
        </table>
      </div>
    </div>
    <div class="card mb-2 p-3">
      <h6 class="text-primary mb-2">Notes & Legal</h6>
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

// Trigger update on all relevant form fields
$("#contactSelect, #unitSelect, #reservationDate, #reservationAmount, #terms, input[name='miscOption']").on("change input", updatePreviewAndSummary);

// Initialize with default values
$(function(){
  $("#reservationDate").val(new Date().toISOString().slice(0,10));
  updatePreviewAndSummary();
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

// Show the modal when "Create New Contact" button is clicked
$('#newContactBtn').on('click', function() {
  $('#newContactModal').modal('show');
  $('#newContactForm')[0].reset();
});

// Handle form submission
$('#newContactForm').on('submit', function(e) {
  e.preventDefault();
  var data = $(this).serialize() + '&action=create_contact';
  $.post('', data, function(res) {
    try { res = JSON.parse(res); } catch(e) { res = {}; }
    if (res.success) {
      // Add new option to select, select it, and trigger change
      var newOption = new Option(res.name, res.id, true, true);
      $('#contactSelect').append(newOption).val(res.id).trigger('change');
      $('#contactSelect').select2('close');
      // Hide the modal using Bootstrap 5's JS API:
      var modalEl = document.getElementById('newContactModal');
      var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
      modal.hide();

      // Optional: Show success alert (add a <div id="contactAlert"></div> in your HTML above the form)
      $('#contactAlert').html('<div class="alert alert-success alert-dismissible fade show" role="alert">Contact added successfully!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
    } else {
      alert(res.msg || "Could not add contact.");
    }
  });
});

</script>

