<div class="container-fluid py-4">
  <div class="row g-4">
    <!-- Panel 1: App Gallery (col-3) -->
    <div class="col-lg-3">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-bold">Apps</div>
        <div class="card-body">
          <div class="row row-cols-2 g-3">
            <div class="col">
              <a href="products.php" class="text-decoration-none">
                <div class="card border-0 text-center p-3 h-100 shadow-sm">
                  <div class="mb-2 display-6 text-primary"><i class="ri-building-2-line"></i></div>
                  <div class="fw-semibold small">All Units</div>
                </div>
              </a>
            </div>
            <div class="col">
              <a href="soa-manager.php" class="text-decoration-none">
                <div class="card border-0 text-center p-3 h-100 shadow-sm">
                  <div class="mb-2 display-6 text-success"><i class="ri-file-list-3-line"></i></div>
                  <div class="fw-semibold small">SOA Manager</div>
                </div>
              </a>
            </div>
            <div class="col">
              <a href="http://localhost/app.flyhubdigital.com/invoicing.php" class="text-decoration-none">
                <div class="card border-0 text-center p-3 h-100 shadow-sm">
                  <div class="mb-2 display-6 text-info"><i class="ri-bill-line"></i></div>
                  <div class="fw-semibold small">Invoicing</div>
                </div>
              </a>
            </div>
            <div class="col">
              <a href="#" class="text-decoration-none">
                <div class="card border-0 text-center p-3 h-100 shadow-sm">
                  <div class="mb-2 display-6 text-warning"><i class="ri-cash-line"></i></div>
                  <div class="fw-semibold small">Commissions</div>
                </div>
              </a>
            </div>
            <div class="col">
              <a href="property-maps.php" class="text-decoration-none">
                <div class="card border-0 text-center p-3 h-100 shadow-sm">
                  <div class="mb-2 display-6 text-danger"><i class="ri-map-pin-line"></i></div>
                  <div class="fw-semibold small">Property Maps</div>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Panel 2: Banner + Invoice List (col-6) -->
    <div class="col-lg-6">
      <!-- Banner -->
      <div class="card shadow-sm mb-4 overflow-hidden">
        <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80"
             class="w-100" alt="Aerial Property View" style="max-height:180px;object-fit:cover;">
        <div class="card-img-overlay p-3 d-flex flex-column justify-content-end align-items-end">
          <span class="badge bg-primary shadow">Featured Property</span>
        </div>
      </div>
      <!-- Invoice List -->
      <div class="card shadow-sm">
        <div class="card-header bg-white d-flex align-items-center flex-wrap gap-2">
          <span class="fw-bold flex-grow-1">Invoices</span>
          <div>
            <select id="status-filter" class="form-select form-select-sm" style="width:120px;display:inline-block;">
              <option value="">All Status</option>
              <option value="Paid">Paid</option>
              <option value="Unpaid">Unpaid</option>
              <option value="Overdue">Overdue</option>
            </select>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" id="btn-print-invoices"><i class="ri-printer-line"></i> Print PDF</button>
            <button class="btn btn-outline-secondary btn-sm" id="btn-export-invoices"><i class="ri-download-2-line"></i> Export CSV</button>
          </div>
        </div>
        <div class="card-body">
          <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
            <label class="form-label mb-0 me-2 fw-normal">Filter:</label>
            <select id="invoice-filter" class="form-select form-select-sm" style="width:120px;">
              <option value="today">Today</option>
              <option value="week">This Week</option>
              <option value="month" selected>This Month</option>
              <option value="year">This Year</option>
              <option value="custom">Custom</option>
            </select>
            <input type="date" id="invoice-date-from" class="form-control form-control-sm" style="width:130px;" disabled>
            <input type="date" id="invoice-date-to" class="form-control form-control-sm" style="width:130px;" disabled>
          </div>
          <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0" id="invoice-table">
              <thead class="bg-light">
                <tr>
                  <th>#</th>
                  <th>Client</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <!-- Populated by JS -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Panel 3: Widgets & Leaderboards (col-3) -->
    <div class="col-lg-3">
      <div class="row g-3">
        <!-- Sold Units -->
        <div class="col-12">
          <div class="card shadow-sm text-center">
            <div class="card-body py-3">
              <div class="display-6 text-success mb-1"><i class="ri-home-8-line"></i></div>
              <div class="fw-bold fs-4" id="widget-sold-units">78</div>
              <div class="text-muted small">Sold Units</div>
            </div>
          </div>
        </div>
        <!-- Revenue YTD -->
        <div class="col-12">
          <div class="card shadow-sm text-center">
            <div class="card-body py-3">
              <div class="display-6 text-info mb-1"><i class="ri-money-dollar-circle-line"></i></div>
              <div class="fw-bold fs-4" id="widget-revenue-ytd">₱3,500,000</div>
              <div class="text-muted small">Revenue YTD</div>
            </div>
          </div>
        </div>
        <!-- Top Brokers Table -->
        <div class="col-12">
          <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold"><i class="ri-user-star-line text-warning"></i> Top Brokers</div>
            <div class="card-body p-2">
              <div class="table-responsive">
                <table class="table table-sm mb-0" id="brokers-table">
                  <thead class="bg-light">
                    <tr>
                      <th>Rank</th>
                      <th>Name</th>
                      <th>Sales</th>
                      <th>Units</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- JS Fill -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <!-- Top Agents Table -->
        <div class="col-12">
          <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold"><i class="ri-user-smile-line text-primary"></i> Top Agents</div>
            <div class="card-body p-2">
              <div class="table-responsive">
                <table class="table table-sm mb-0" id="agents-table">
                  <thead class="bg-light">
                    <tr>
                      <th>Rank</th>
                      <th>Name</th>
                      <th>Sales</th>
                      <th>Units</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- JS Fill -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- end widgets/leaderboards -->
    </div>
  </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
// ==== Sample Invoice Data (35 EXAMPLES, 8 for today, varying months/days/years/statuses) ====
// Get today's date in YYYY-MM-DD
const today = new Date();
const todayStr = today.toISOString().slice(0,10);

const invoices = [
  // 8 invoices for today (simulate mix of statuses)
  {id:"INV-101", client:"Ana Del Rosario", amount:27000, status:"Paid",   date:todayStr},
  {id:"INV-102", client:"Maria Santos", amount:29000, status:"Paid",   date:todayStr},
  {id:"INV-103", client:"Benser Partoza", amount:18000, status:"Unpaid",   date:todayStr},
  {id:"INV-104", client:"Carlos Lim", amount:21500, status:"Unpaid",   date:todayStr},
  {id:"INV-105", client:"Angela Yu", amount:31000, status:"Overdue",   date:todayStr},
  {id:"INV-106", client:"Mario Rivera", amount:34000, status:"Overdue",   date:todayStr},
  {id:"INV-107", client:"Linda Chua", amount:25000, status:"Paid",   date:todayStr},
  {id:"INV-108", client:"Liza Manalo", amount:26000, status:"Paid",   date:todayStr},

  // More for this week (subtract days from today)
  {id:"INV-109", client:"Ana Del Rosario", amount:25000, status:"Paid",   date:getDateNDaysAgo(1)},
  {id:"INV-110", client:"Benser Partoza", amount:30000, status:"Unpaid", date:getDateNDaysAgo(2)},
  {id:"INV-111", client:"Maria Santos", amount:28000, status:"Overdue",date:getDateNDaysAgo(3)},
  {id:"INV-112", client:"Ana Del Rosario",  amount:15000, status:"Paid",   date:getDateNDaysAgo(4)},
  {id:"INV-113", client:"Benser Partoza",   amount:42000, status:"Paid",   date:getDateNDaysAgo(5)},
  {id:"INV-114", client:"Maria Santos",     amount:35000, status:"Unpaid", date:getDateNDaysAgo(6)},
  {id:"INV-115", client:"Carlos Lim",       amount:18000, status:"Paid",   date:getDateNDaysAgo(7)},

  // Earlier in this month
  {id:"INV-116", client:"Angela Yu",        amount:22000, status:"Overdue",date:getDateNDaysAgo(8)},
  {id:"INV-117", client:"Mario Rivera",     amount:40000, status:"Paid",   date:getDateNDaysAgo(9)},
  {id:"INV-118", client:"Ana Del Rosario",  amount:31000, status:"Unpaid", date:getDateNDaysAgo(10)},
  {id:"INV-119", client:"Linda Chua", amount:29500, status:"Paid", date:getDateNDaysAgo(11)},
  {id:"INV-120", client:"Liza Manalo", amount:18500, status:"Overdue", date:getDateNDaysAgo(12)},

  // Last month
  {id:"INV-121", client:"Ramon Santos", amount:33000, status:"Paid", date:getDateNMonthsAgo(1, 2)},
  {id:"INV-122", client:"Ana Del Rosario", amount:38000, status:"Paid", date:getDateNMonthsAgo(1, 7)},
  {id:"INV-123", client:"Benser Partoza", amount:17000, status:"Unpaid", date:getDateNMonthsAgo(1, 12)},
  {id:"INV-124", client:"Maria Santos", amount:21000, status:"Overdue", date:getDateNMonthsAgo(1, 13)},
  {id:"INV-125", client:"Linda Chua", amount:27000, status:"Paid", date:getDateNMonthsAgo(1, 15)},
  {id:"INV-126", client:"Mario Rivera", amount:24000, status:"Paid", date:getDateNMonthsAgo(1, 19)},

  // Previous months
  {id:"INV-127", client:"Liza Manalo", amount:21000, status:"Paid", date:getDateNMonthsAgo(2, 5)},
  {id:"INV-128", client:"Rico Dela Cruz", amount:36000, status:"Paid", date:getDateNMonthsAgo(2, 9)},
  {id:"INV-129", client:"Angela Yu", amount:19500, status:"Overdue", date:getDateNMonthsAgo(3, 3)},
  {id:"INV-130", client:"Ramon Santos", amount:37500, status:"Paid", date:getDateNMonthsAgo(3, 8)},
  {id:"INV-131", client:"Benser Partoza", amount:26500, status:"Paid", date:getDateNMonthsAgo(4, 2)},

  // Last year
  {id:"INV-132", client:"Ana Del Rosario", amount:21000, status:"Paid", date:getDateNYearsAgo(1, 5)},
  {id:"INV-133", client:"Angela Yu", amount:32000, status:"Paid", date:getDateNYearsAgo(1, 22)},
  {id:"INV-134", client:"Carlos Lim", amount:37000, status:"Unpaid", date:getDateNYearsAgo(1, 24)},
  {id:"INV-135", client:"Maria Santos", amount:41000, status:"Overdue", date:getDateNYearsAgo(2, 17)},
];

// Utility functions for date simulation
function getDateNDaysAgo(n) {
  const d = new Date();
  d.setDate(d.getDate() - n);
  return d.toISOString().slice(0,10);
}
function getDateNMonthsAgo(months, days=0) {
  const d = new Date();
  d.setMonth(d.getMonth() - months);
  d.setDate(d.getDate() - days);
  return d.toISOString().slice(0,10);
}
function getDateNYearsAgo(years, days=0) {
  const d = new Date();
  d.setFullYear(d.getFullYear() - years);
  d.setDate(d.getDate() - days);
  return d.toISOString().slice(0,10);
}

// ==== Sample Brokers/Agents Data ====
const brokers = [
  {name: "Ramon Santos", sales: 530000, units: 8},
  {name: "Linda Chua", sales: 490000, units: 7},
  {name: "Liza Manalo", sales: 420000, units: 6},
  {name: "Mario Rivera", sales: 350000, units: 5},
  {name: "Rico Dela Cruz", sales: 280000, units: 4}
];
const agents = [
  {name: "Angela Yu", sales: 310000, units: 7},
  {name: "Carlos Lim", sales: 290000, units: 6},
  {name: "Ana Del Rosario", sales: 250000, units: 5},
  {name: "Benser Partoza", sales: 200000, units: 4},
  {name: "Maria Santos", sales: 180000, units: 3}
];

// ==== Utilities ====
function badge(status) {
  if(status==="Paid") return '<span class="badge bg-success">Paid</span>';
  if(status==="Unpaid") return '<span class="badge bg-warning text-dark">Unpaid</span>';
  if(status==="Overdue") return '<span class="badge bg-danger">Overdue</span>';
  return `<span class="badge bg-secondary">${status}</span>`;
}
function toPeso(val) {
  return "₱" + (val || 0).toLocaleString();
}
function formatDate(dateStr) {
  return dateStr;
}

// ==== Invoice Filtering ====
function filterInvoices() {
  const filter = $("#invoice-filter").val();
  const status = $("#status-filter").val();
  const today = new Date();
  let from = null, to = null;
  if(filter === "today") {
    from = to = today.toISOString().slice(0,10);
  } else if(filter === "week") {
    const start = new Date(today); start.setDate(today.getDate() - today.getDay());
    const end = new Date(start); end.setDate(start.getDate() + 6);
    from = start.toISOString().slice(0,10);
    to = end.toISOString().slice(0,10);
  } else if(filter === "month") {
    from = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-01`;
    to = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-31`;
  } else if(filter === "year") {
    from = `${today.getFullYear()}-01-01`;
    to = `${today.getFullYear()}-12-31`;
  } else if(filter === "custom") {
    from = $("#invoice-date-from").val();
    to = $("#invoice-date-to").val();
  }
  // Enable/disable date fields
  $("#invoice-date-from, #invoice-date-to").prop("disabled", filter!=="custom");

  // Filtering logic
  return invoices.filter(inv => {
    if(from && inv.date < from) return false;
    if(to && inv.date > to) return false;
    if(status && inv.status !== status) return false;
    return true;
  });
}

// ==== Render Invoice Table ====
function renderInvoicesTable() {
  const filtered = filterInvoices();
  const $tbody = $("#invoice-table tbody");
  $tbody.empty();
  filtered.forEach(inv => {
    $tbody.append(`
      <tr>
        <td>${inv.id}</td>
        <td>${inv.client}</td>
        <td>${toPeso(inv.amount)}</td>
        <td>${badge(inv.status)}</td>
        <td>${formatDate(inv.date)}</td>
      </tr>
    `);
  });
}

// ==== Print Invoices Table ====
function printInvoicesTable() {
  const tableHtml = $("#invoice-table").parent().html();
  const w = window.open();
  w.document.write('<html><head><title>Invoices</title>');
  w.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">');
  w.document.write('</head><body>');
  w.document.write(tableHtml);
  w.document.write('</body></html>');
  w.document.close();
  setTimeout(()=>w.print(), 300);
}

// ==== Export CSV ====
function exportInvoicesCSV() {
  const filtered = filterInvoices();
  let csv = "Invoice #,Client,Amount,Status,Date\n";
  csv += filtered.map(inv =>
    [inv.id, inv.client, inv.amount, inv.status, inv.date].join(",")
  ).join("\n");
  const blob = new Blob([csv], {type: "text/csv"});
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url; a.download = "invoices.csv";
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}

// ==== Render Brokers/Agents Tables ====
function renderLeaderboard(tableId, data) {
  const $tbody = $(tableId + " tbody");
  $tbody.empty();
  data.forEach((row, idx) => {
    $tbody.append(`
      <tr>
        <td>${idx+1}</td>
        <td>${row.name}</td>
        <td>${toPeso(row.sales)}</td>
        <td>${row.units}</td>
      </tr>
    `);
  });
}

// ==== INIT ====
$(function() {
  // Init Invoice Filter logic
  $("#invoice-filter, #status-filter").on("change", function() {
    renderInvoicesTable();
    // Enable date fields for custom
    if($("#invoice-filter").val()==="custom") {
      $("#invoice-date-from,#invoice-date-to").prop("disabled",false);
    } else {
      $("#invoice-date-from,#invoice-date-to").val("").prop("disabled",true);
    }
  });
  $("#invoice-date-from,#invoice-date-to").on("change", renderInvoicesTable);

  // Print/Export
  $("#btn-print-invoices").on("click", printInvoicesTable);
  $("#btn-export-invoices").on("click", exportInvoicesCSV);

  // Leaderboards
  renderLeaderboard("#brokers-table", brokers);
  renderLeaderboard("#agents-table", agents);

  // Initial Invoice Table
  renderInvoicesTable();
});
</script>
