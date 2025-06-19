<?php 
require_once __DIR__ . '/layouts/config.php';
// [Add your PHP stat-gathering code here as needed, e.g. $contact_count, $company_count, etc.]
?>
<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>CRM Dashboard | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <link href="assets/vendor/remixicon/fonts/remixicon.css" rel="stylesheet">
    <style>
        .stat-card { border-radius: 13px; box-shadow: 0 2px 8px #0001; border: 1px solid #e6e9f1; background: #fff; text-align: center; padding: 1.2rem 0.6rem; min-height: 100px; }
        .stat-icon { font-size: 2rem; margin-bottom: 0.25rem; }
        .stat-num { font-size: 1.35rem; font-weight: 800; }
        .stat-label { font-size: .96rem; color: #7a8499; }
        .card-panel { border-radius: 13px; box-shadow: 0 2px 8px #0001; border: 1px solid #e6e9f1; background: #fff; padding: 1.2rem 1.1rem; }
        .h3-section { font-size: 1.19rem; font-weight: 700; margin-bottom: 1.05rem; }
        .quick-action-btn { min-width: 140px; margin-bottom: 0.3rem; }
        .recent-activity-list li { margin-bottom: .65rem; font-size: .99rem; }
        .leaderboard-list li { margin-bottom: .4rem; }
        @media (max-width: 991px) {
            .stat-card { margin-bottom: 1rem; }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <!-- Title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <h4 class="page-title">CRM Dashboard</h4>
                        </div>
                    </div>
                </div>

                <!-- Stat Cards -->
                <div class="row mb-4 g-3">
                    <div class="col-6 col-md-3 col-lg-2">
                        <div class="stat-card">
                            <div class="stat-icon text-primary"><i class="ri-contacts-book-2-line"></i></div>
                            <div class="stat-num"><?= (int)($contact_count ?? 112) ?></div>
                            <div class="stat-label">Contacts</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <div class="stat-card">
                            <div class="stat-icon text-success"><i class="ri-building-line"></i></div>
                            <div class="stat-num"><?= (int)($company_count ?? 27) ?></div>
                            <div class="stat-label">Companies</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <div class="stat-card">
                            <div class="stat-icon text-warning"><i class="ri-hand-coin-line"></i></div>
                            <div class="stat-num"><?= (int)($open_deals ?? 8) ?></div>
                            <div class="stat-label">Open Deals</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <div class="stat-card">
                            <div class="stat-icon text-info"><i class="ri-award-line"></i></div>
                            <div class="stat-num">₱<?= number_format($deals_won_amount ?? 420000) ?></div>
                            <div class="stat-label">Deals Won (Month)</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <div class="stat-card">
                            <div class="stat-icon text-secondary"><i class="ri-apps-line"></i></div>
                            <div class="stat-num"><?= (int)($active_apps ?? 6) ?></div>
                            <div class="stat-label">Active Apps</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <div class="stat-card">
                            <div class="stat-icon text-danger"><i class="ri-file-list-3-line"></i></div>
                            <div class="stat-num"><?= (int)($tasks_due ?? 5) ?></div>
                            <div class="stat-label">Tasks Due</div>
                        </div>
                    </div>
                </div>

                <!-- Chart, Activity, Pipeline, What's New -->
                <div class="row g-3 mb-3">
                    <div class="col-lg-8">
                        <div class="card-panel mb-4">
                            <h3 class="h3-section"><i class="ri-bar-chart-line text-primary"></i> Contacts Added Trend</h3>
                            <div style="height:230px;background:linear-gradient(90deg,#f4f9ff 65%,#e9f4ff 100%);border-radius:9px;display:flex;align-items:center;justify-content:center;color:#b2bac6;">
                                <canvas id="crmChart" height="80"></canvas>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-7">
                                <div class="card-panel mb-3">
                                    <h3 class="h3-section"><i class="ri-list-unordered text-secondary"></i> Recent Activity</h3>
                                    <ul class="recent-activity-list list-unstyled mb-0">
                                        <li><i class="ri-user-add-line text-success"></i> New contact <b>Juan dela Cruz</b> added <span class="text-muted small">3m ago</span></li>
                                        <li><i class="ri-hand-coin-line text-info"></i> Deal <b>Website Project</b> moved to <span class="text-success">Won</span> <span class="text-muted small">1h ago</span></li>
                                        <li><i class="ri-building-add-line text-primary"></i> Company <b>ABC Corp</b> imported <span class="text-muted small">2h ago</span></li>
                                        <li><i class="ri-mail-send-line text-secondary"></i> Email sent to <b>Acme Corp</b> <span class="text-muted small">5h ago</span></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div>
                                    <h3 class="h3-section"><i class="ri-trophy-line text-warning"></i> Deal Pipeline</h3>
                                    <div class="mb-2">
                                        <span class="badge bg-primary">4 New</span>
                                        <span class="badge bg-warning text-dark">2 Qualified</span>
                                        <span class="badge bg-info text-dark">1 Proposal</span>
                                        <span class="badge bg-success">1 Won</span>
                                        <span class="badge bg-danger">0 Lost</span>
                                    </div>
                                    <div class="mt-2">
                                        <a href="deals.php" class="small text-decoration-underline text-secondary">View full pipeline</a>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <h3 class="h3-section"><i class="ri-flashlight-line text-warning"></i> What's New</h3>
                                    <ul class="ps-3 mb-2 small">
                                        <li><b>[Jul 2024]</b> Added Mini Deal Pipeline preview widget</li>
                                        <li>Bulk import for companies now supports Excel files</li>
                                        <li>UI refresh: Cleaned up sidebar and badges</li>
                                    </ul>
                                    <a href="release-notes.php" class="small text-decoration-underline text-info">See all release notes</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Actions & Leaderboard -->
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <div class="card-panel mb-4">
                                <h3 class="h3-section"><i class="ri-lightbulb-flash-line text-success"></i> Quick Actions</h3>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="add_contact.php" class="btn btn-outline-primary quick-action-btn"><i class="ri-user-add-line"></i> Add Contact</a>
                                    <a href="add_company.php" class="btn btn-outline-success quick-action-btn"><i class="ri-building-add-line"></i> Add Company</a>
                                    <a href="add_deal.php" class="btn btn-outline-warning quick-action-btn"><i class="ri-hand-coin-line"></i> Create Deal</a>
                                    <a href="import.php" class="btn btn-outline-secondary quick-action-btn"><i class="ri-upload-2-line"></i> Import Data</a>
                                    <a href="integrations.php" class="btn btn-outline-dark quick-action-btn"><i class="ri-link"></i> Connect Integration</a>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="card-panel mb-4">
                                <h3 class="h3-section"><i class="ri-bar-chart-line text-success"></i> CRM Analytics</h3>
                                <div class="mb-2">[Chart here]</div>
                                <div class="small text-muted">New Contacts (last 4 weeks)</div>
                            </div>
                            <div class="card-panel">
                                <h3 class="h3-section"><i class="ri-medal-line text-warning"></i> Leaderboard</h3>
                                <ul class="leaderboard-list ps-2 mb-0 small">
                                    <li><b>Lester Caraan</b> — 5 deals closed</li>
                                    <li><b>Jane Smith</b> — 3 deals closed</li>
                                    <li><b>Juan dela Cruz</b> — 2 deals closed</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tip / Feedback -->
                <div class="row mb-2 align-items-center">
                    <div class="col-lg-8">
                        <div class="alert alert-info d-flex align-items-center mb-0 py-2 px-3" style="font-size:1.01rem;">
                            <i class="ri-lightbulb-flash-line fs-5 me-2"></i>
                            <span>
                                <strong>Tip of the Day:</strong> You can bulk-import contacts and companies from Excel or CSV. Try it in the Import Data section!
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-4 mt-2 mt-lg-0">
                        <a href="feedback.php" class="btn btn-outline-info w-100">
                            <i class="ri-chat-1-line"></i> Suggest a Feature / Feedback
                        </a>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// For CRM Chart
const crmLabels = <?= json_encode(['Mon','Tue','Wed','Thu','Fri','Sat','Sun']) ?>;
const crmData = <?= json_encode([12, 19, 7, 17, 23, 14, 18]) ?>;

document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('crmChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: crmLabels,
            datasets: [{
                label: 'Contacts',
                data: crmData,
                fill: true,
                borderColor: '#3e60d5',
                backgroundColor: 'rgba(62,96,213,0.09)',
                tension: 0.4,
                pointBackgroundColor: '#3e60d5',
                pointBorderColor: '#fff',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
</body>
</html>
