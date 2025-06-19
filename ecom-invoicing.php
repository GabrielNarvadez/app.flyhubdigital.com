<?php
require_once __DIR__ . '/layouts/config.php';

// --- FETCH MANUAL INVOICES ---
$invoices = [];
$sql = "
    SELECT 
        i.id,
        i.invoice_number,
        i.total,
        i.status,
        i.fulfillment,
        i.issue_date,
        i.channel,
        c.first_name,
        c.last_name
    FROM invoices i
    LEFT JOIN contacts c ON c.id = i.contact_id
    ORDER BY i.issue_date DESC, i.id DESC
";
$result = $link->query($sql);
while ($row = $result->fetch_assoc()) {
    $invoices[] = [
        'id'         => $row['id'],
        'number'     => $row['invoice_number'],
        'customer'   => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
        'source'     => $row['channel'] ?? 'Manual',
        'amount'     => $row['total'],
        'status'     => $row['status'] ? ucfirst($row['status']) : 'Due',
        'fulfillment'=> $row['fulfillment'] ? ucfirst($row['fulfillment']) : 'Unfulfilled',
        'date'       => $row['issue_date'],
        'type'       => 'Invoice'
    ];
}

// --- FETCH POS SALES ---
$sales = [];
$sql2 = "
    SELECT 
        s.id,
        s.total,
        s.status,
        s.fulfillment,
        s.sale_datetime,
        s.source,
        s.customer_name,
        c.first_name,
        c.last_name
    FROM sales s
    LEFT JOIN contacts c ON c.id = s.contact_id
    ORDER BY s.sale_datetime DESC, s.id DESC
";
$result2 = $link->query($sql2);
while ($row = $result2->fetch_assoc()) {
    // Use customer_name if present, else join from contacts
    $customer = $row['customer_name'] ?: trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
    $sales[] = [
        'id'         => $row['id'],
        'number'     => 'ORD-' . str_pad($row['id'], 8, '0', STR_PAD_LEFT),
        'customer'   => $customer,
        'source'     => $row['source'] ?? 'POS',
        'amount'     => $row['total'],
        'status'     => $row['status'] ? ucfirst($row['status']) : 'Paid',
        'fulfillment'=> $row['fulfillment'] ? ucfirst($row['fulfillment']) : 'Fulfilled',
        'date'       => $row['sale_datetime'] ? date('Y-m-d', strtotime($row['sale_datetime'])) : '',
        'type'       => 'Order'
    ];
}

// --- UNIFY BOTH INTO ONE ARRAY AND SORT BY DATE DESC ---
$orders = array_merge($invoices, $sales);
usort($orders, function($a, $b) {
    // Sort by date DESC, then by type (Invoices before Orders on same date)
    if ($a['date'] === $b['date']) {
        return strcmp($a['type'], $b['type']);
    }
    return strcmp($b['date'], $a['date']);
});
?>

<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Orders & Invoices | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
        .stat-card { border-radius: 13px; box-shadow: 0 2px 8px #0001; border: 1px solid #e6e9f1; background: #fff; text-align: center; padding: 1.2rem 0.6rem; min-height: 100px; }
        .stat-icon { font-size: 2rem; margin-bottom: 0.25rem; }
        .stat-num { font-size: 1.35rem; font-weight: 800; }
        .stat-label { font-size: .96rem; color: #7a8499; }
        .order-row { cursor: pointer; }
        .order-row.selected { background: #eaf1ff !important; }
        .badge-fulfillment { font-size: .9em; }
        .order-source-shopify { color: #1ab774; }
        .order-source-pos { color: #2672ef; }
        .order-source-manual { color: #666; }
        .order-status-paid { background: #eaf8ec; color: #1ac374; }
        .order-status-pending { background: #fffbe7; color: #edb100; }
        .order-status-due { background: #ffe3e3; color: #f54248; }
        .order-status-cancelled { background: #e3e3e3; color: #888; }
        .order-status-draft { background: #f6f7fa; color: #777; }
        .order-status-shipped { background: #eaf4ff; color: #228be6; }
        .order-status-fulfilled { background: #eaf8ec; color: #1ac374; }
        .order-status-processing { background: #fafdff; color: #2672ef; }
        .order-status-partial { background: #ffe3a3; color: #e5a100; }
        .table td, .table th { vertical-align: middle; }
        .quick-action-btn { min-width: 130px; margin-bottom: 0.2rem; }
        .filter-bar .form-select, .filter-bar .form-control { font-size: .98rem; }
        .right-preview { min-height: 420px; display: flex; align-items: center; justify-content: center; color: #a3aac2; }
        .right-preview .icon { font-size: 3rem; margin-bottom: 0.8rem; color: #c7d2e6; }
        @media (max-width: 991px) {
            .right-preview { min-height: 240px; }
        }
                .filter-bar .form-control.search-input {
            max-width: 320px;  /* Adjust width as needed, e.g., 320px */
            min-width: 220px;
            flex: 0 0 auto;
        }
        /* Confirmed Status (status column) */
        .order-status-confirmed {
            background: #f0f5ff !important; /* Soft blue */
            color: #2663d8 !important;      /* Blue text */
            border: 1px solid #d6e0f7;
        }

        /* Unfulfilled Fulfillment */
        .order-status-unfulfilled {
            background: #ffe3e3 !important; /* Light red */
            color: #f54248 !important;      /* Red text */
            border: 1px solid #fad2d2;
        }
        .flyhub-floating-alert {
          position: fixed;
          top: 150px;
          right: 20px;
          max-width: 600px;     /* Adjust width as needed */
          z-index: 9999;        /* Ensure it floats above everything */
          box-shadow: 0 4px 20px #0002;
        }
        @media (max-width: 800px) {
          .flyhub-floating-alert {
            right: 10px;
            left: 10px;
            max-width: unset;
          }
        }
        .order-status-completed {
            background: #eaf8ec !important;  /* Light green like Paid */
            color: #1ac374 !important;        /* Green text */
            border: 1px solid #bee4ce;
        }

    </style>
</head>

<body>
<div class="wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <!-- Page Title -->
                    <div class="row mb-3" style="padding-top: 30px;">
                        <div class="col-12 d-flex align-items-start justify-content-between">
                            <!-- Left: Page Title -->
                            <div>
                                <h4 class="page-title mb-0">Orders & Invoices</h4>
                            </div>
                            <!-- Right: Buttons and Notice stacked vertically -->
                            <div class="d-flex flex-column align-items-end">
                                <div>
                                    <a href="pos-dashboard.php" target="_blank">
                                        <button class="btn btn-outline-primary quick-action-btn">
                                        <i class="ri-add-line"></i> New Order
                                        </button>
                                    </a>
                                    <a href="app-invoicing.php" target="_blank">
                                        <button class="btn btn-success quick-action-btn">
                                        <i class="ri-file-list-3-line"></i> Create Invoice
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="alert alert-info alert-dismissible fade show flyhub-floating-alert" role="alert">
                        <b>Not sure what to use?</b><br>
                        If you’re processing a sale in person or online, click
                        <b><a href="pos-dashboard.php" class="alert-link">New Order</a></b>.<br>
                        If you need to send a customer a bill, click
                        <b><a href="app-invoicing.php" class="alert-link">Create Invoice</a></b>.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                <!-- Filters -->
                <!-- Filter Bar (Two Rows: Search above, Filters below) -->

                    <div class="mb-2">
                      <div class="row g-2">
                        <div class="col-12">
                          <input type="text" id="filterSearch" class="form-control search-input" placeholder="Search orders, invoices..." style="max-width: 340px;">
                        </div>
                        <div class="col-12">
                          <div class="d-flex flex-wrap gap-2 align-items-center">
                            <select class="form-select" id="filterChannel" style="max-width:150px;">
                                <option>All Channels</option>
                                <option>POS</option>
                                <option>Shopify</option>
                                <option>Manual</option>
                            </select>
                            <select class="form-select" id="filterStatus" style="max-width:130px;">
                                <option>All Status</option>
                                <option>Paid</option>
                                <option>Pending</option>
                                <option>Due</option>
                                <option>Draft</option>
                                <option>Shipped</option>
                                <option>Cancelled</option>
                                <option>Completed</option>
                            </select>
                            <input type="date" id="filterFrom" class="form-control" style="max-width:160px;">
                            <input type="date" id="filterTo" class="form-control" style="max-width:160px;">
                            <select class="form-select" id="filterCustomer" style="max-width:150px;">
                                <option>All Customers</option>
                                <option>Juan Dela Cruz</option>
                                <option>Anna Lim</option>
                                <option>Mike Tan</option>
                            </select>
                            <button class="btn btn-outline-secondary me-2" id="filterReset" style="margin-right: 0 !important;"><i class="ri-refresh-line"></i> Reset</button>
                            <div id="top-actions" class="d-inline-flex align-items-center" style="display:none;">
                                <button class="btn btn-outline-danger me-2" id="deleteSelectedBtn"><i class="ri-delete-bin-line"></i> Delete</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                <!-- Orders & Invoices Table + Preview -->

                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card p-2">

                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:35px;">
                                        <input type="checkbox" class="form-check-input" id="selectAllOrdersTop">
                                    </th>
                                    <th style="width:120px;">Order/Inv #</th>
                                    <th>Customer</th>
                                    <th>Source</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Fulfillment</th>
                                    <th>Date</th>
                                    <th style="width:70px;"></th>
                                </tr>
                            </thead>
                            <tbody id="orderTableBody"></tbody>
                        </table>

                        </div>
                    </div>
                    <!-- Order/Invoice Preview Panel -->
                    <div class="col-lg-4">
                        <div class="card h-100 p-4" id="orderPreviewPanel">
                            <div class="right-preview text-center">
                                <div>
                                    <div class="icon"><i class="ri-file-text-line"></i></div>
                                    <div class="fw-bold mb-2">Select an order or invoice to preview</div>
                                    <div class="text-muted">Order/invoice details will appear here.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- End row -->

            </div><!-- container-fluid -->
        </div><!-- content -->
        <?php include 'layouts/footer.php'; ?>
    </div>
</div>
<?php include 'layouts/right-sidebar.php'; ?>
<?php include 'layouts/footer-scripts.php'; ?>

<script>
const flyhubOrders = <?php echo json_encode($orders); ?>;
const flyhubOrdersIndexed = flyhubOrders.map((o, idx) => ({...o, _originalIndex: idx}));

// ---- Render Table ----
function renderOrderTable(filteredOrders) {
    const tbody = document.getElementById('orderTableBody');
    if (!tbody) return;
    if (!filteredOrders.length) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No orders or invoices found.</td></tr>';
        document.getElementById('orderPreviewPanel').innerHTML = `
            <div class="right-preview text-center">
                <div>
                    <div class="icon"><i class="ri-file-text-line"></i></div>
                    <div class="fw-bold mb-2">Select an order or invoice to preview</div>
                    <div class="text-muted">Order/invoice details will appear here.</div>
                </div>
            </div>`;
        return;
    }
    tbody.innerHTML = filteredOrders.map(o => `
        <tr class="order-row" onclick="showOrderDetails(${o._originalIndex})">
            <td>
              <input type="checkbox"
                     class="order-checkbox"
                     data-type="${o.type}"
                     data-id="${o.id}">
            </td>
            <td><a href="#">${o.number}</a></td>
            <td>${o.customer}</td>
            <td>
                <i class="${
                    o.source && o.source.toLowerCase().includes('shopify') ? 'ri-store-line order-source-shopify' :
                    o.source && o.source.toLowerCase().includes('manual') ? 'ri-edit-box-line order-source-manual' :
                    'ri-terminal-box-line order-source-pos'
                }"></i> ${o.source}
            </td>
            <td>₱${Number(o.amount).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
            <td><span class="badge order-status-${o.status ? o.status.toLowerCase() : ''}">${o.status || ''}</span></td>
            <td><span class="badge badge-fulfillment order-status-${o.fulfillment ? o.fulfillment.toLowerCase() : ''}">${o.fulfillment || ''}</span></td>
            <td>${o.date || ''}</td>
            <td><button class="btn btn-light btn-sm"><i class="ri-eye-line"></i></button></td>
        </tr>
    `).join('');
    // Re-initialize select/delete logic
    bindOrderCheckboxEvents();
}

// ---- Filter Logic ----
function filterOrders() {
    let search   = document.getElementById('filterSearch').value.trim().toLowerCase();
    let channel  = document.getElementById('filterChannel').value;
    let status   = document.getElementById('filterStatus').value;
    let from     = document.getElementById('filterFrom').value;
    let to       = document.getElementById('filterTo').value;
    let customer = document.getElementById('filterCustomer').value;

    let filtered = flyhubOrdersIndexed.filter(o => {
        let matches = true;
        if (search) {
            matches = (
                (o.number && o.number.toLowerCase().includes(search)) ||
                (o.customer && o.customer.toLowerCase().includes(search)) ||
                (o.source && o.source.toLowerCase().includes(search))
            );
        }
        if (matches && channel && channel !== 'All Channels') {
            matches = o.source === channel;
        }
        if (matches && status && status !== 'All Status') {
            matches = o.status === status;
        }
        if (matches && customer && customer !== 'All Customers') {
            matches = o.customer === customer;
        }
        if (matches && from) {
            matches = o.date && o.date >= from;
        }
        if (matches && to) {
            matches = o.date && o.date <= to;
        }
        return matches;
    });
    renderOrderTable(filtered);
    // If at least one row, select first for preview
    if (filtered.length) showOrderDetails(filtered[0]._originalIndex);
}

// ---- Preview Panel ----
function showOrderDetails(idx) {
    // Remove .selected from all rows
    document.querySelectorAll('.order-row').forEach(row => row.classList.remove('selected'));
    // Add .selected to clicked row (only if rows exist)
    let rows = document.querySelectorAll('.order-row');
    for (let i = 0; i < rows.length; i++) {
        if (parseInt(rows[i].getAttribute('onclick').match(/\d+/)) === idx) {
            rows[i].classList.add('selected');
            break;
        }
    }
    const o = flyhubOrders[idx];
    if (!o) return;

    let html = `
        <div>
            <div class="mb-3">
                <span class="badge bg-primary">${o.type}</span>
                <span class="ms-2 fw-bold">${o.number}</span>
            </div>
            <div class="mb-2"><b>Customer:</b> ${o.customer || ''}</div>
            <div class="mb-2"><b>Source:</b> ${o.source || ''}</div>
            <div class="mb-2"><b>Amount:</b> ₱${parseFloat(o.amount).toLocaleString()}</div>
            <div class="mb-2"><b>Status:</b> <span class="badge order-status-${(o.status || '').toLowerCase()}">${o.status || ''}</span></div>
            <div class="mb-2"><b>Fulfillment:</b> <span class="badge badge-fulfillment order-status-${(o.fulfillment || '').toLowerCase()}">${o.fulfillment || ''}</span></div>
            <div class="mb-2"><b>Date:</b> ${o.date || ''}</div>
        </div>
    `;
    document.getElementById('orderPreviewPanel').innerHTML = html;
}

// ---- Select All, Delete, and Checkbox Logic ----
const selectAllTop = document.getElementById('selectAllOrdersTop');
const deleteBtn = document.getElementById('deleteSelectedBtn');
const topActions = document.getElementById('top-actions');

function updateTopActions() {
    const checkboxes = document.querySelectorAll('.order-checkbox');
    const checked = document.querySelectorAll('.order-checkbox:checked');
    if (topActions) topActions.style.display = checked.length ? 'inline-flex' : 'none';
    if (selectAllTop) {
        selectAllTop.checked = checkboxes.length && checked.length === checkboxes.length;
    }
}

function bindOrderCheckboxEvents() {
    // Re-attach all events after table re-render
    document.querySelectorAll('.order-checkbox').forEach(cb => {
        cb.addEventListener('change', updateTopActions);
    });
    updateTopActions();
}

if (selectAllTop) {
    selectAllTop.addEventListener('change', function() {
        document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = this.checked);
        updateTopActions();
    });
}
if (deleteBtn) {
    deleteBtn.addEventListener('click', function() {
        // Gather IDs and types of all checked
        const items = [];
        document.querySelectorAll('.order-checkbox:checked').forEach(cb => {
            items.push({
                type: cb.dataset.type,
                id: cb.dataset.id
            });
        });
        if (items.length === 0) return;
        if (!confirm(`Delete ${items.length} selected order(s)? This cannot be undone.`)) return;
        // Call backend to delete
        fetch('delete-orders.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({items})
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert("Error: " + (data.error || "Delete failed"));
            }
        });
    });
}

// ---- Bind Filter Events ----
document.getElementById('filterSearch').addEventListener('input', filterOrders);
document.getElementById('filterChannel').addEventListener('change', filterOrders);
document.getElementById('filterStatus').addEventListener('change', filterOrders);
document.getElementById('filterFrom').addEventListener('change', filterOrders);
document.getElementById('filterTo').addEventListener('change', filterOrders);
document.getElementById('filterCustomer').addEventListener('change', filterOrders);
document.getElementById('filterReset').addEventListener('click', function() {
    document.getElementById('filterSearch').value = '';
    document.getElementById('filterChannel').selectedIndex = 0;
    document.getElementById('filterStatus').selectedIndex = 0;
    document.getElementById('filterFrom').value = '';
    document.getElementById('filterTo').value = '';
    document.getElementById('filterCustomer').selectedIndex = 0;
    filterOrders();
});

// ---- Initial Render & Select First Row ----
document.addEventListener('DOMContentLoaded', function() {
    renderOrderTable(flyhubOrdersIndexed);
    // Select first row for preview
    if (flyhubOrdersIndexed.length) showOrderDetails(0);
});
</script>


<script src="assets/js/app.min.js"></script>
</body>
</html>
