<?php
// 1. Define your apps here. You can expand as needed.
$apps = [
    [
        'name' => 'Customer Portal',
        'desc' => 'Secure client access to services and transactions.',
        'url' => 'portal-login.php',
        'icon' => 'ri-user-shared-line',
        'categories' => ['All', 'General'],
    ],
    [
        'name' => 'Invoicing',
        'desc' => 'Manage bills, payments, and generate invoices.',
        'url' => 'invoicing.php',
        'icon' => 'ri-file-list-3-line',
        'categories' => ['All', 'Finance'],
    ],
    [
        'name' => 'Property Maps',
        'desc' => 'Visualize property locations and geodata.',
        'url' => 'property-maps.php',
        'icon' => 'ri-map-pin-line',
        'categories' => ['All', 'General'],
    ],
    [
        'name' => 'Commissions',
        'desc' => 'Track earnings and agent commissions.',
        'url' => 'commissions.php',
        'icon' => 'ri-currency-line',
        'categories' => ['All', 'Sales', 'Finance'],
    ],
    [
        'name' => 'AR Tracker',
        'desc' => 'Monitor accounts receivable and collections.',
        'url' => 'ar-tracker.php',
        'icon' => 'ri-bar-chart-box-line',
        'categories' => ['All', 'Finance'],
    ],
    [
        'name' => 'SEO Tracker',
        'desc' => 'Track keyword rankings and site visibility.',
        'url' => 'seo-tracker.php',
        'icon' => 'ri-line-chart-line',
        'categories' => ['All', 'Marketing'],
    ],
    [
        'name' => 'Attendance Tracker',
        'desc' => 'Log and manage staff attendance records.',
        'url' => 'attendance-tracker.php',
        'icon' => 'ri-time-line',
        'categories' => ['All', 'General'],
    ],
    // Integrations
    [
        'name' => 'Shopify',
        'desc' => 'Sync products, orders, and customers with Shopify.',
        'url' => '#',
        'logo' => 'assets/images/brands/shopify.png',
        'categories' => ['All', 'Integrations', 'Sales'],
    ],
    [
        'name' => 'Mailchimp',
        'desc' => 'Automate email campaigns and sync contacts.',
        'url' => '#',
        'logo' => 'assets/images/brands/mailchimp.png',
        'categories' => ['All', 'Integrations', 'Marketing'],
    ],
    [
        'name' => 'HubSpot',
        'desc' => 'CRM and marketing automation integration.',
        'url' => '#',
        'logo' => 'assets/images/brands/hubspot.png',
        'categories' => ['All', 'Integrations', 'Sales', 'Marketing'],
    ],
    [
        'name' => 'Odoo',
        'desc' => 'Full-featured ERP integration.',
        'url' => '#',
        'logo' => 'assets/images/brands/odoo.png',
        'categories' => ['All', 'Integrations', 'Finance', 'General'],
    ],
];

// 2. Define your tabs here. Order as you want them to appear.
$tabs = [
    'All', 'General', 'Marketing', 'Sales', 'Finance', 'Integrations'
];
?>

<div class="col-xl-12">
    <div class="card">
        <div class="card-body">
            <h4 class="header-title">Enhance your Flyhub Business App by adding custom features tailored to your business needs or integrating with popular third-party applications</h4>
            <p class="text-muted fs-14 mb-3"></p>
            <div class="row">
                <div class="col-sm-3 mb-2 mb-sm-0">
                    <div class="nav flex-column nav-pills" id="apps-tab" role="tablist" aria-orientation="vertical">
                        <?php foreach ($tabs as $i => $tab): ?>
                        <a class="nav-link<?php if ($i==0) echo ' active'; ?>" id="apps-tab-<?= strtolower($tab) ?>" data-bs-toggle="pill"
                           href="#apps-pane-<?= strtolower($tab) ?>" role="tab" aria-controls="apps-pane-<?= strtolower($tab) ?>" aria-selected="<?= $i==0 ? 'true' : 'false' ?>">
                            <?= htmlspecialchars($tab) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="tab-content" id="apps-tabContent">
                        <?php foreach ($tabs as $i => $tab): ?>
                        <div class="tab-pane fade<?php if ($i==0) echo ' show active'; ?>" id="apps-pane-<?= strtolower($tab) ?>" role="tabpanel" aria-labelledby="apps-tab-<?= strtolower($tab) ?>">
                            <div class="row mx-n1 g-0">
                                <?php
                                $appsInTab = array_filter($apps, function($app) use ($tab) {
                                    return in_array($tab, $app['categories']);
                                });
                                if (count($appsInTab) == 0): ?>
                                    <div class="col-12">
                                        <div class="alert alert-warning mb-0">No apps yet for this category.</div>
                                    </div>
                                <?php endif; ?>
                                <?php foreach ($appsInTab as $app): ?>
                                <div class="col-xxl-3 col-lg-6">
                                    <div class="card m-1 shadow-none border">
                                        <div class="p-2">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <div class="avatar-sm">
                                                        <span class="avatar-title bg-light text-secondary rounded">
                                                            <?php if (isset($app['logo'])): ?>
                                                                <img src="<?= htmlspecialchars($app['logo']) ?>" alt="<?= htmlspecialchars($app['name']) ?>" style="height:26px;">
                                                            <?php else: ?>
                                                                <i class="<?= htmlspecialchars($app['icon']) ?> fs-20 fw-normal"></i>
                                                            <?php endif; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col ps-0">
                                                    <a href="<?= htmlspecialchars($app['url']) ?>" class="text-muted fw-bold"><?= htmlspecialchars($app['name']) ?></a>
                                                    <p class="mb-0 fs-13"><?= htmlspecialchars($app['desc']) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
