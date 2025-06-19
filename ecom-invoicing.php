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
    </style>
</head>

<body>
<div class="wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <!-- Page Title -->
                <div class="row mb-3">
                    <div class="col-12 d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="page-title mb-0">Orders & Invoices</h4>
                        </div>
                        <div style="padding-top: 30px;">
                            <button class="btn btn-outline-primary quick-action-btn"><i class="ri-add-line"></i> New Order</button>
                            <a href="app-invoicing.php" target="_blank">
                                <button class="btn btn-success quick-action-btn">
                                <i class="ri-file-list-3-line"></i> Create Invoice
                                </button>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="row mb-2">
                    <div class="col-lg-9">
                        <div class="d-flex flex-wrap gap-2 filter-bar">
                            <input type="text" class="form-control" placeholder="Search orders, invoices...">
                            <select class="form-select" style="max-width:150px;">
                                <option>All Channels</option>
                                <option>POS</option>
                                <option>Shopify</option>
                                <option>Manual</option>
                            </select>
                            <select class="form-select" style="max-width:130px;">
                                <option>All Status</option>
                                <option>Paid</option>
                                <option>Pending</option>
                                <option>Due</option>
                                <option>Draft</option>
                                <option>Shipped</option>
                                <option>Cancelled</option>
                            </select>
                            <input type="date" class="form-control" style="max-width:160px;">
                            <input type="date" class="form-control" style="max-width:160px;">
                            <select class="form-select" style="max-width:150px;">
                                <option>All Customers</option>
                                <option>Juan Dela Cruz</option>
                                <option>Anna Lim</option>
                                <option>Mike Tan</option>
                            </select>
                            <button class="btn btn-outline-secondary"><i class="ri-refresh-line"></i> Reset</button>
                        </div>
                    </div>
                    <div class="col-lg-3 text-lg-end mt-2 mt-lg-0">
                        <button class="btn btn-outline-dark btn-sm"><i class="ri-upload-line"></i> Export</button>
                        <button class="btn btn-outline-secondary btn-sm"><i class="ri-printer-line"></i> Print</button>
                        <button class="btn btn-outline-info btn-sm"><i class="ri-mail-send-line"></i> Send</button>
                    </div>
                </div>

                <!-- Orders & Invoices Table + Preview -->
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card p-2">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
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
                                <tbody id="orderTableBody">
                                    <!-- Sample data; replace with dynamic data in production -->
                                    <tr class="order-row selected" onclick="showOrderDetails(0)">
                                        <td><a href="#">INV-39319158</a></td>
                                        <td>Juan Dela Cruz</td>
                                        <td><i class="ri-store-line order-source-shopify"></i> Shopify</td>
                                        <td>₱3,400.00</td>
                                        <td><span class="badge order-status-paid">Paid</span></td>
                                        <td><span class="badge badge-fulfillment order-status-fulfilled">Fulfilled</span></td>
                                        <td>2025-06-19</td>
                                        <td><button class="btn btn-light btn-sm"><i class="ri-eye-line"></i></button></td>
                                    </tr>
                                    <tr class="order-row" onclick="showOrderDetails(1)">
                                        <td><a href="#">ORD-40118557</a></td>
                                        <td>Anna Lim</td>
                                        <td><i class="ri-terminal-box-line order-source-pos"></i> POS</td>
                                        <td>₱2,999.00</td>
                                        <td><span class="badge order-status-pending">Pending</span></td>
                                        <td><span class="badge badge-fulfillment order-status-processing">Processing</span></td>
                                        <td>2025-06-18</td>
                                        <td><button class="btn btn-light btn-sm"><i class="ri-eye-line"></i></button></td>
                                    </tr>
                                    <tr class="order-row" onclick="showOrderDetails(3)">
                                        <td><a href="#">INV-202506178764</a></td>
                                        <td>Juan Dela Cruz</td>
                                        <td><i class="ri-terminal-box-line order-source-pos"></i> POS</td>
                                        <td>₱15,000.00</td>
                                        <td><span class="badge order-status-due">Due</span></td>
                                        <td><span class="badge badge-fulfillment order-status-processing">Unfulfilled</span></td>
                                        <td>2025-06-17</td>
                                        <td><button class="btn btn-light btn-sm"><i class="ri-eye-line"></i></button></td>
                                    </tr>
                                    <tr class="order-row" onclick="showOrderDetails(4)">
                                        <td><a href="#">INV-202506177308</a></td>
                                        <td>Anna Lim</td>
                                        <td><i class="ri-store-line order-source-shopify"></i> Shopify</td>
                                        <td>₱15,000.00</td>
                                        <td><span class="badge order-status-paid">Paid</span></td>
                                        <td><span class="badge badge-fulfillment order-status-fulfilled">Fulfilled</span></td>
                                        <td>2025-06-17</td>
                                        <td><button class="btn btn-light btn-sm"><i class="ri-eye-line"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <input type="checkbox" class="form-check-input" id="selectAllOrders">
                            <label for="selectAllOrders" class="form-label mb-0">Select All</label>
                            <button class="btn btn-outline-secondary btn-sm ms-2"><i class="ri-mail-send-line"></i> Send Selected</button>
                            <button class="btn btn-outline-danger btn-sm"><i class="ri-delete-bin-line"></i> Delete</button>
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
<script src="assets/js/app.min.js"></script>
<script>
    // --- SAMPLE PREVIEW DATA ---
    const orders = [
        {
            id: 0,
            type: "Invoice",
            number: "INV-39319158",
            customer: "Juan Dela Cruz",
            channel: "Shopify",
            amount: "₱3,400.00",
            status: "Paid",
            fulfillment: "Fulfilled",
            date: "2025-06-19",
            items: [
                {name:"Leather Backpack", qty:1, price:"₱2,200"},
                {name:"Travel Organizer", qty:1, price:"₱1,200"},
            ],
            timeline: [
                {event:"Invoice Created", time:"2025-06-19 10:22"},
                {event:"Payment Received", time:"2025-06-19 10:24"},
                {event:"Order Fulfilled", time:"2025-06-19 11:08"},
            ]
        },
        {
            id: 1,
            type: "Order",
            number: "ORD-40118557",
            customer: "Anna Lim",
            channel: "POS",
            amount: "₱2,999.00",
            status: "Pending",
            fulfillment: "Processing",
            date: "2025-06-18",
            items: [
                {name:"Crossbody Bag", qty:2, price:"₱2,500"},
            ],
            timeline: [
                {event:"Order Placed", time:"2025-06-18 13:10"},
                {event:"Payment Pending", time:"2025-06-18 13:12"},
            ]
        },
        {
            id: 3,
            type: "Invoice",
            number: "INV-202506178764",
            customer: "Juan Dela Cruz",
            channel: "POS",
            amount: "₱15,000.00",
            status: "Due",
            fulfillment: "Unfulfilled",
            date: "2025-06-17",
            items: [
                {name:"Business Bag", qty:3, price:"₱15,000"},
            ],
            timeline: [
                {event:"Invoice Created", time:"2025-06-17 09:00"},
                {event:"Payment Due", time:"2025-06-22 23:59"},
            ]
        },
        {
            id: 4,
            type: "Invoice",
            number: "INV-202506177308",
            customer: "Anna Lim",
            channel: "Shopify",
            amount: "₱15,000.00",
            status: "Paid",
            fulfillment: "Fulfilled",
            date: "2025-06-17",
            items: [
                {name:"Handbag", qty:1, price:"₱10,000"},
                {name:"Wallet", qty:2, price:"₱5,000"},
            ],
            timeline: [
                {event:"Invoice Created", time:"2025-06-17 08:45"},
                {event:"Payment Received", time:"2025-06-17 09:12"},
                {event:"Order Fulfilled", time:"2025-06-17 14:33"},
            ]
        }
    ];

    function showOrderDetails(idx) {
        // Remove .selected from all rows
        document.querySelectorAll('.order-row').forEach(row => row.classList.remove('selected'));
        // Add .selected to clicked row
        document.querySelectorAll('.order-row')[idx].classList.add('selected');

        let o = orders[idx];
        let html = `
        <div>
            <div class="mb-3">
                <span class="badge bg-primary">${o.type}</span>
                <span class="ms-2 fw-bold">${o.number}</span>
            </div>
            <div class="mb-2"><b>Customer:</b> ${o.customer}</div>
            <div class="mb-2"><b>Channel:</b> ${o.channel}</div>
            <div class="mb-2"><b>Amount:</b> ${o.amount}</div>
            <div class="mb-2"><b>Status:</b> <span class="badge order-status-${o.status.toLowerCase()}">${o.status}</span></div>
            <div class="mb-2"><b>Fulfillment:</b> <span class="badge badge-fulfillment order-status-${o.fulfillment.toLowerCase()}">${o.fulfillment}</span></div>
            <div class="mb-3"><b>Date:</b> ${o.date}</div>
            <div class="mb-3">
                <b>Items:</b>
                <ul class="ms-2 mb-2">
                    ${o.items.map(i=>`<li>${i.qty}x ${i.name} <span class="text-muted small">(${i.price})</span></li>`).join('')}
                </ul>
            </div>
            <div>
                <b>Timeline:</b>
                <ul class="ms-2">
                    ${o.timeline.map(t=>`<li>${t.event} <span class="text-muted small">(${t.time})</span></li>`).join('')}
                </ul>
            </div>
        </div>
        `;
        document.getElementById('orderPreviewPanel').innerHTML = html;
    }
</script>
</body>
</html>
