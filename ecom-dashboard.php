<?php
require_once __DIR__ . '/layouts/config.php';
date_default_timezone_set('Asia/Manila');

// 1. Sales Today
$stmt = $link->prepare("SELECT COALESCE(SUM(total),0) FROM sales WHERE DATE(sale_datetime) = CURDATE() AND (status IS NULL OR status = 'completed')");
$stmt->execute();
$stmt->bind_result($sales_today);
$stmt->fetch();
$stmt->close();

// 2. Orders Today
$stmt = $link->prepare("SELECT COUNT(*) FROM sales WHERE DATE(sale_datetime) = CURDATE()");
$stmt->execute();
$stmt->bind_result($orders_today);
$stmt->fetch();
$stmt->close();

// 3. Low Stock Items
$stmt = $link->prepare("SELECT COUNT(*) FROM products WHERE stock <= COALESCE(min_stock, 3)");
$stmt->execute();
$stmt->bind_result($low_stock_count);
$stmt->fetch();
$stmt->close();

// 4. Failed Orders (POS + Invoices)
$stmt = $link->prepare("SELECT COUNT(*) FROM sales WHERE DATE(sale_datetime) = CURDATE() AND status = 'failed'");
$stmt->execute();
$stmt->bind_result($failed_orders_pos);
$stmt->fetch();
$stmt->close();

$stmt = $link->prepare("SELECT COUNT(*) FROM invoices WHERE DATE(issue_date) = CURDATE() AND status = 'failed'");
$stmt->execute();
$stmt->bind_result($failed_orders_inv);
$stmt->fetch();
$stmt->close();

$failed_orders_total = $failed_orders_pos + $failed_orders_inv;

// 5. Active Channels
$channels = ['shopify' => 0, 'shopee' => 0, 'pos' => 0];
foreach (['shopify', 'shopee', 'pos'] as $ch) {
    $q = "SELECT COUNT(*) FROM products WHERE channel_{$ch} = 'ok'";
    $stmt = $link->prepare($q);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    if ($count > 0) $channels[$ch] = 1;
}
$active_channels = array_sum($channels);

// 6. Unfulfilled Orders (invoices)
$stmt = $link->prepare("SELECT COUNT(*) FROM invoices WHERE status = 'unfulfilled'");
$stmt->execute();
$stmt->bind_result($unfulfilled_orders);
$stmt->fetch();
$stmt->close();

// --- Stat cards now ready for use in HTML ---
?>

<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
        .stat-card { border-radius: 13px; box-shadow: 0 2px 8px #0001; border: 1px solid #e6e9f1; background: #fff; text-align: center; padding: 1.2rem 0.6rem; min-height: 100px; }
        .stat-icon { font-size: 2rem; margin-bottom: 0.25rem; }
        .stat-num { font-size: 1.35rem; font-weight: 800; }
        .stat-label { font-size: .96rem; color: #7a8499; }

        .alert-card { border-radius: 10px; box-shadow: 0 1px 6px #0001; border-left: 5px solid #ff4d4f; background: #fff6f5; padding: .85rem 1.2rem; margin-bottom: .5rem; }
        .alert-card.warning { border-left-color: #ffd600; background: #fffbe7; }
        .alert-card.info { border-left-color: #36cfc9; background: #f4fffb; }

        .chart-card { border-radius: 13px; box-shadow: 0 2px 8px #0001; border: 1px solid #e6e9f1; padding: 1.2rem; background: #fff; }
        .quick-action-btn { min-width: 140px; margin-bottom: 0.3rem; }
        .recent-activity-list li { margin-bottom: .65rem; font-size: .99rem; }

        .channel-status-table td, .channel-status-table th { font-size: .99rem; }
        .seo-score { font-size: 1.45rem; font-weight: 900; color: #21c87a; }
        .text-blast-btn { min-width: 130px; }
        .seo-todo li { font-size: .98rem; }
        .h3-section { font-size: 1.19rem; font-weight: 700; margin-bottom: 1.05rem; }
        @media (max-width: 991px) {
            .stat-card { margin-bottom: 1rem; }
        }
    </style>
</head>

<body>
<div class="wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <h4 class="page-title">The Look Officiel Dashboard</h4>
                        </div>
                    </div>
                </div>

                <!-- Stat Cards -->

                    <div class="row mb-4 g-3">
                        <div class="col-6 col-md-3 col-lg-2">
                            <div class="stat-card">
                                <div class="stat-icon text-primary"><i class="ri-bar-chart-grouped-line"></i></div>
                                <div class="stat-num">₱<?= number_format($sales_today) ?></div>
                                <div class="stat-label">Sales Today</div>
                            </div>
                        </div>
                        <div class="col-6 cl-md-3 col-lg-2">
                            <div class="stat-card">
                                <div class="stat-icon text-success"><i class="ri-shopping-bag-3-line"></i></div>
                                <div class="stat-num"><?= (int)$orders_today ?></div>
                                <div class="stat-label">Orders Today</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <div class="stat-card">
                                <div class="stat-icon text-danger"><i class="ri-archive-line"></i></div>
                                <div class="stat-num"><?= (int)$low_stock_count ?></div>
                                <div class="stat-label">Low Stock Items</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <div class="stat-card">
                                <div class="stat-icon text-warning"><i class="ri-close-circle-line"></i></div>
                                <div class="stat-num"><?= (int)$failed_orders_total ?></div>
                                <div class="stat-label">Failed Orders</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <div class="stat-card">
                                <div class="stat-icon text-info"><i class="ri-store-2-line"></i></div>
                                <div class="stat-num"><?= (int)$active_channels ?></div>
                                <div class="stat-label">Active Channels</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <div class="stat-card">
                                <div class="stat-icon text-secondary"><i class="ri-inbox-archive-line"></i></div>
                                <div class="stat-num"><?= (int)$unfulfilled_orders ?></div>
                                <div class="stat-label">Unfulfilled Orders</div>
                            </div>
                        </div>
                    </div>

                <!-- Sales Chart, Activity, Alerts, SEO, Text Blast -->
                <div class="row g-3 mb-3">
                    <div class="col-lg-8">
                        <div class="chart-card mb-4">
                            <h3 class="h3-section"><i class="ri-bar-chart-line text-primary"></i> Sales Trend</h3>
                            <!-- Placeholder for chart -->
                            <div style="height:230px;background:linear-gradient(90deg,#f4f9ff 65%,#e9f4ff 100%);border-radius:9px;display:flex;align-items:center;justify-content:center;color:#b2bac6;">
                                [Sales Chart Here: Last 7 Days]
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-7">
                                <div class="chart-card mb-3">
                                    <h3 class="h3-section"><i class="ri-list-unordered text-secondary"></i> Recent Activity</h3>
                                    <ul class="recent-activity-list list-unstyled mb-0">
                                        <li><i class="ri-shopping-cart-2-line text-primary"></i> Order #1234 (Shopify) delivered <span class="text-muted small">2m ago</span></li>
                                        <li><i class="ri-cash-line text-success"></i> POS Sale ₱1,200 completed <span class="text-muted small">5m ago</span></li>
                                        <li><i class="ri-add-line text-info"></i> New product added <span class="text-muted small">20m ago</span></li>
                                        <li><i class="ri-close-circle-line text-danger"></i> Order #1209 failed (Shopee) <span class="text-muted small">34m ago</span></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div>
                                    <h3 class="h3-section"><i class="ri-error-warning-line text-danger"></i> Alerts & Warnings</h3>
                                    <div class="alert-card">
                                        <i class="ri-alert-line text-danger"></i> 4 products below minimum stock
                                    </div>
                                    <div class="alert-card warning">
                                        <i class="ri-cloud-off-line text-warning"></i> Shopee channel: Sync error. <a href="#" class="fw-bold">Retry</a>
                                    </div>
                                    <div class="alert-card info">
                                        <i class="ri-inbox-line text-info"></i> 1 overdue invoice: <a href="#" class="fw-bold">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Right side: Marketing & SEO -->
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <div class="chart-card mb-4">
                                <h3 class="h3-section"><i class="ri-message-2-line text-primary"></i> Text Blast</h3>
                                <div class="mb-2">
                                    <span class="text-muted">Last Campaign:</span> <b>Weekend Promo</b><br>
                                    <span class="small text-muted">Sent to: <b>421</b> | Delivered: <span class="text-success">399</span> | Failed: <span class="text-danger">22</span></span>
                                </div>
                                <div class="mb-3 small text-muted">Sent: July 19, 2024 - 10:25AM</div>
                                <button class="btn btn-primary text-blast-btn"><i class="ri-send-plane-line"></i> New Text Blast</button>
                            </div>
                        </div>
                        <div>
                            <div class="chart-card mb-4">
                                <h3 class="h3-section"><i class="ri-bar-chart-line text-success"></i> SEO Efforts</h3>
                                <div class="d-flex gap-4 mb-2 flex-wrap">
                                    <div>
                                        <div class="seo-label small">SEO Health Score</div>
                                        <div class="seo-score">82</div>
                                    </div>
                                    <div>
                                        <div class="seo-label small">Products Optimized</div>
                                        <div class="fw-bold text-info">243 / 300</div>
                                    </div>
                                </div>
                                <div class="mb-2 small text-muted">Meta Issues: <span class="fw-bold text-warning">12</span> | Broken Links: <span class="fw-bold text-danger">3</span></div>
                                <div>
                                    <span class="text-muted small">To-Do's by SEO Team:</span>
                                    <ul class="seo-todo ms-3">
                                        <li>Add meta descriptions to 7 products</li>
                                        <li>Fix broken links on product pages</li>
                                        <li>Review duplicate product titles</li>
                                    </ul>
                                </div>
                                <div class="mt-2 small text-muted">
                                    Source: Shopify Catalog | Last synced: 4 hours ago
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row my-4">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-3">
                            <button class="btn btn-outline-primary quick-action-btn"><i class="ri-add-line"></i> New Order</button>
                            <button class="btn btn-outline-success quick-action-btn"><i class="ri-arrow-down-line"></i> Receive Stock</button>
                            <button class="btn btn-outline-info quick-action-btn"><i class="ri-cash-line"></i><a href="POS.php" target="_blank"> Open POS</a></button>
                            <button class="btn btn-outline-secondary quick-action-btn"><i class="ri-link"></i> Connect Channel</button>
                            <button class="btn btn-outline-dark quick-action-btn"><i class="ri-refresh-line"></i> Manual Sync</button>
                            <button class="btn btn-outline-warning quick-action-btn"><i class="ri-file-list-3-line"></i> Create Invoice</button>
                        </div>
                    </div>
                </div>

                <!-- Connected Channels + Quick Stats -->
                <div class="row g-3 mb-2">
                    <div class="col-lg-6">
                        <div class="chart-card h-100">
                            <h3 class="h3-section"><i class="ri-store-2-line text-info"></i> Connected Channels</h3>
                            <table class="table table-sm channel-status-table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Channel</th>
                                        <th>Status</th>
                                        <th>Last Sync</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><i class="ri-store-line text-primary"></i> Shopify</td>
                                        <td><span class="badge bg-success">OK</span></td>
                                        <td>2m ago</td>
                                        <td><button class="btn btn-outline-secondary btn-sm"><i class="ri-refresh-line"></i> Sync Now</button></td>
                                    </tr>
                                    <tr>
                                        <td><i class="ri-shopping-bag-2-line text-warning"></i> Shopee</td>
                                        <td><span class="badge bg-danger">Sync Error</span></td>
                                        <td>10m ago</td>
                                        <td><button class="btn btn-outline-danger btn-sm"><i class="ri-refresh-line"></i> Retry</button></td>
                                    </tr>
                                    <tr>
                                        <td><i class="ri-terminal-box-line text-info"></i> POS</td>
                                        <td><span class="badge bg-success">OK</span></td>
                                        <td>Just now</td>
                                        <td><button class="btn btn-outline-secondary btn-sm"><i class="ri-refresh-line"></i> Sync Now</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="chart-card h-100">
                            <h3 class="h3-section"><i class="ri-information-line text-secondary"></i> Quick Stats</h3>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="fw-bold fs-5 text-primary">539</div>
                                    <div class="small text-muted">Total Products</div>
                                </div>
                                <div class="col-6">
                                    <div class="fw-bold fs-5 text-info">5</div>
                                    <div class="small text-muted">Unfulfilled Orders</div>
                                </div>
                                <div class="col-6">
                                    <div class="fw-bold fs-5 text-danger">4</div>
                                    <div class="small text-muted">Low Stock</div>
                                </div>
                                <div class="col-6">
                                    <div class="fw-bold fs-5 text-warning">2</div>
                                    <div class="small text-muted">Failed Orders</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- container-fluid -->
        </div><!-- content -->
        <?php include 'layouts/footer.php'; ?>
    </div>
</div>
<?php include 'layouts/right-sidebar.php'; ?>
<?php include 'layouts/footer-scripts.php'; ?>

<script src="assets/js/app.min.js"></script>

<script>
function refreshStats() {
    fetch('dashboard-stats.php')
        .then(res => res.json())
        .then(data => {
            document.getElementById('salesToday').textContent = '₱' + Number(data.sales_today).toLocaleString();
            document.getElementById('ordersToday').textContent = data.orders_today;
            document.getElementById('lowStock').textContent = data.low_stock_count;
            document.getElementById('failedOrders').textContent = data.failed_orders_total;
            document.getElementById('activeChannels').textContent = data.active_channels;
            document.getElementById('unfulfilledOrders').textContent = data.unfulfilled_orders;
        });
}
setInterval(refreshStats, 7000);
refreshStats();
</script>
</body>
</html>
