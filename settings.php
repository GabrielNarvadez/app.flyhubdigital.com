<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'layouts/config.php';

// Get current tenant id (replace with your logic if needed)
$tenant_id = $_SESSION['tenant_id'] ?? 1;

// === 1. Process FORM POST FIRST ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenant_name = $_POST['tenant_name'] ?? '';
    $contact_email = $_POST['contact_email'] ?? '';
    $address = $_POST['address'] ?? '';
    $tax_id = $_POST['tax_id'] ?? '';

    // Handle logo upload if provided
    $new_logo_url = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $uploads_dir = 'uploads/logos';
        if (!is_dir($uploads_dir)) mkdir($uploads_dir, 0777, true);
        $tmp_name = $_FILES['logo']['tmp_name'];
        $filename = 'tenant_' . $tenant_id . '_' . basename($_FILES['logo']['name']);
        $destination = $uploads_dir . '/' . $filename;
        move_uploaded_file($tmp_name, $destination);
        $new_logo_url = $destination;
    }

    // If no new logo, keep the previous logo
    if (!$new_logo_url) {
        $sql = "SELECT logo_url FROM tenants WHERE id = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("i", $tenant_id);
        $stmt->execute();
        $stmt->bind_result($existing_logo_url);
        $stmt->fetch();
        $stmt->close();
        $new_logo_url = $existing_logo_url;
    }

    // Update the DB
    $sql = "UPDATE tenants SET tenant_name=?, contact_email=?, address=?, tax_id=?, logo_url=? WHERE id=?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("sssssi", $tenant_name, $contact_email, $address, $tax_id, $new_logo_url, $tenant_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to the same page (best practice for form handling)
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// === 2. FETCH tenant info AFTER processing any POST ===
$sql = "SELECT tenant_name, contact_email, address, tax_id, logo_url FROM tenants WHERE id = ?";
$stmt = $link->prepare($sql);
$stmt->bind_param("i", $tenant_id);
$stmt->execute();
$stmt->bind_result($tenant_name, $contact_email, $address, $tax_id, $logo_url);
$stmt->fetch();
$stmt->close();
?>

<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Settings | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
        .settings-tab-pane { padding: 32px 16px 16px 16px; background: #fff; border-radius: 12px; box-shadow: 0 1px 8px #0001;}
        .settings-form-label { font-weight: 500; }
        .settings-form-section { border-bottom: 1px solid #e7eaf3; margin-bottom: 1.5rem; padding-bottom: 1.2rem; }
        .settings-section-title { font-size: 1.08rem; font-weight: 600; margin-bottom: 1rem; }
        .input-note { font-size: .92rem; color: #888; }
        .form-switch .form-check-input {margin-top: 6px;}
    </style>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">
        <?php include 'layouts/menu.php'; ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="#">Settings</a></li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Settings</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3 mb-3">
                            <ul class="nav flex-column nav-pills" id="settings-tabs" role="tablist" aria-orientation="vertical">
                                <li class="nav-item mb-2" role="presentation">
                                    <a class="nav-link active" id="tab-company" data-bs-toggle="pill" href="#company" role="tab">Company & Account</a>
                                </li>
                                <li class="nav-item mb-2" role="presentation">
                                    <a class="nav-link" id="tab-users" data-bs-toggle="pill" href="#users" role="tab">Users & Access</a>
                                </li>
                                <li class="nav-item mb-2" role="presentation">
                                    <a class="nav-link" id="tab-modules" data-bs-toggle="pill" href="#modules" role="tab">Modules & Data</a>
                                </li>
                                <li class="nav-item mb-2" role="presentation">
                                    <a class="nav-link" id="tab-integrations" data-bs-toggle="pill" href="#integrations" role="tab">Integrations</a>
                                </li>
                                <li class="nav-item mb-2" role="presentation">
                                    <a class="nav-link" id="tab-notifications" data-bs-toggle="pill" href="#notifications" role="tab">Notifications & Preferences</a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-lg-9">
                            <div class="tab-content">
                                <!-- === COMPANY & ACCOUNT TAB === -->
                                <div class="tab-pane fade show active settings-tab-pane" id="company" role="tabpanel">
                                    <!-- Company Info -->
                                    <div class="settings-form-section">
                                        <div class="settings-section-title">Company Information</div>

                                        <form method="POST" enctype="multipart/form-data">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Company Name</label>
                                                    <input type="text" class="form-control" name="tenant_name" value="<?= htmlspecialchars($tenant_name ?? '') ?>" placeholder="e.g. Flyhub Digital" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Primary Contact Email</label>
                                                    <input type="email" class="form-control" name="contact_email" value="<?= htmlspecialchars($contact_email ?? '') ?>" placeholder="info@company.com" />
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Business Address</label>
                                                    <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($address ?? '') ?>" placeholder="Address" />
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="settings-form-label">Tax ID / Registration #</label>
                                                    <input type="text" class="form-control" name="tax_id" value="<?= htmlspecialchars($tax_id ?? '') ?>" placeholder="TIN / Reg #" />
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="settings-form-label">Company Logo</label>
                                                    <input type="file" class="form-control" name="logo" />
                                                    <?php if (!empty($logo_url)): ?>
                                                        <img src="<?= htmlspecialchars($logo_url) ?>" alt="Logo" style="max-height:40px;margin-top:4px;" />
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </form>

                                    </div>
                                    <!-- Branches -->
                                    <div class="settings-form-section">
                                        <div class="settings-section-title">Branches / Locations</div>
                                        <form>
                                            <div class="mb-2">
                                                <button class="btn btn-outline-primary btn-sm">Add Branch</button>
                                            </div>
                                            <table class="table table-sm table-borderless align-middle">
                                                <thead>
                                                    <tr>
                                                        <th>Branch Name</th>
                                                        <th>Address</th>
                                                        <th>Manager</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Main HQ</td>
                                                        <td>123 Business St.</td>
                                                        <td>John Doe</td>
                                                        <td>
                                                            <button class="btn btn-light btn-sm">Edit</button>
                                                            <button class="btn btn-danger btn-sm">Delete</button>
                                                        </td>
                                                    </tr>
                                                    <!-- more branches -->
                                                </tbody>
                                            </table>
                                        </form>
                                    </div>
                                    <!-- Subscription -->
                                    <div class="settings-form-section">
                                        <div class="settings-section-title">Subscription & Billing</div>
                                        <form>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Plan</label>
                                                    <input type="text" class="form-control" value="Free Tier" disabled />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Billing Email</label>
                                                    <input type="email" class="form-control" placeholder="billing@company.com" />
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Next Billing Date</label>
                                                    <input type="date" class="form-control" disabled />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Payment Method</label>
                                                    <select class="form-control">
                                                        <option>Credit Card</option>
                                                        <option>Bank Transfer</option>
                                                        <option>PayPal</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <a href="#" class="btn btn-outline-secondary btn-sm">View Invoices</a>
                                                <a href="#" class="btn btn-outline-primary btn-sm">Upgrade Plan</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- === USERS & ACCESS TAB === -->
                                <div class="tab-pane fade settings-tab-pane" id="users" role="tabpanel">
                                    <!-- Users -->
                                    <div class="settings-form-section">
                                        <div class="settings-section-title">Users</div>
                                        <div class="mb-2">
                                            <button class="btn btn-outline-primary btn-sm">Invite User</button>
                                        </div>
                                        <table class="table table-sm table-borderless align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Role</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Jane Smith</td>
                                                    <td>jane@company.com</td>
                                                    <td>Admin</td>
                                                    <td><span class="badge bg-success">Active</span></td>
                                                    <td>
                                                        <button class="btn btn-light btn-sm">Edit</button>
                                                        <button class="btn btn-danger btn-sm">Delete</button>
                                                    </td>
                                                </tr>
                                                <!-- more users -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Roles & Permissions -->
                                    <div class="settings-form-section">
                                        <div class="settings-section-title">Roles & Permissions</div>
                                        <form>
                                            <div class="mb-2">
                                                <button class="btn btn-outline-primary btn-sm">Add Role</button>
                                            </div>
                                            <table class="table table-sm table-borderless align-middle">
                                                <thead>
                                                    <tr>
                                                        <th>Role</th>
                                                        <th>Contacts</th>
                                                        <th>Companies</th>
                                                        <th>Products</th>
                                                        <th>Invoices</th>
                                                        <th>POS</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Admin</td>
                                                        <td>
                                                            <span class="badge bg-success">All</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-success">All</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-success">All</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-success">All</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-success">All</span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-light btn-sm">Edit</button>
                                                        </td>
                                                    </tr>
                                                    <!-- more roles -->
                                                </tbody>
                                            </table>
                                        </form>
                                    </div>
                                    <!-- Security & Audit -->
                                    <div class="settings-form-section">
                                        <div class="settings-section-title">Security</div>
                                        <form>
                                            <div class="row mb-2">
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Password Policy</label>
                                                    <select class="form-control">
                                                        <option>Standard</option>
                                                        <option>Strong</option>
                                                        <option>Custom</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Session Timeout</label>
                                                    <input type="number" class="form-control" placeholder="Minutes" min="5" />
                                                </div>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="enable2fa" />
                                                <label class="form-check-label" for="enable2fa">Enable Two-Factor Authentication</label>
                                            </div>
                                        </form>
                                        <div class="settings-section-title mt-4">Audit Logs</div>
                                        <div style="max-height:150px;overflow:auto;background:#f9fafb;border-radius:5px;">
                                            <table class="table table-sm table-borderless">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>User</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>2024-06-18</td>
                                                        <td>Jane Smith</td>
                                                        <td>Updated product pricing</td>
                                                    </tr>
                                                    <!-- more logs -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- === MODULES & DATA TAB === -->
                                <div class="tab-pane fade settings-tab-pane" id="modules" role="tabpanel">
                                    <!-- Product & Inventory -->
                                    <div class="settings-form-section">
                                        <div class="settings-section-title">Product & Inventory Settings</div>
                                        <form>
                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <label class="settings-form-label">Default Category</label>
                                                    <input type="text" class="form-control" placeholder="e.g. Bags, Accessories" />
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="settings-form-label">Unit of Measure</label>
                                                    <input type="text" class="form-control" placeholder="e.g. pcs, box" />
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="settings-form-label">Low Stock Threshold</label>
                                                    <input type="number" class="form-control" min="1" placeholder="e.g. 5" />
                                                </div>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="enableInventory" checked />
                                                <label class="form-check-label" for="enableInventory">Enable Inventory Tracking</label>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- Invoicing & Payments -->
                                    <div class="settings-form-section">
                                        <div class="settings-section-title">Invoicing & Payment</div>
                                        <form>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Default Invoice Note</label>
                                                    <input type="text" class="form-control" placeholder="Thank you for your business!" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Due Days</label>
                                                    <input type="number" class="form-control" min="1" placeholder="e.g. 30" />
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="settings-form-label">Allowed Payment Methods</label>
                                                <div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" id="pay_cash" checked>
                                                        <label class="form-check-label" for="pay_cash">Cash</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" id="pay_bank">
                                                        <label class="form-check-label" for="pay_bank">Bank</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" id="pay_gcash">
                                                        <label class="form-check-label" for="pay_gcash">GCash</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" id="pay_card">
                                                        <label class="form-check-label" for="pay_card">Credit Card</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="settings-form-label">Invoice Template</label>
                                                <input type="file" class="form-control" />
                                                <div class="input-note">Upload a logo/header or custom template (PDF/DOCX).</div>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- Customer Portal -->
                                    <div class="settings-form-section">
                                        <div class="settings-section-title">Customer Portal</div>
                                        <form>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="enablePortal" checked />
                                                <label class="form-check-label" for="enablePortal">Enable Customer Portal</label>
                                            </div>
                                            <div class="mb-3">
                                                <label class="settings-form-label">Portal Welcome Message</label>
                                                <input type="text" class="form-control" placeholder="Welcome to your customer dashboard!" />
                                            </div>
                                            <div class="mb-3">
                                                <label class="settings-form-label">Portal Banner/Logo</label>
                                                <input type="file" class="form-control" />
                                            </div>
                                        </form>
                                    </div>
                                    <!-- Data Import/Export -->
                                    <div class="settings-form-section">
                                        <div class="settings-section-title">Data Import & Export</div>
                                        <form>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Import Data</label>
                                                    <input type="file" class="form-control" />
                                                    <div class="input-note">Accepts CSV for contacts, products, invoices, etc.</div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Export Data</label>
                                                    <select class="form-control">
                                                        <option>Contacts</option>
                                                        <option>Companies</option>
                                                        <option>Products</option>
                                                        <option>Invoices</option>
                                                    </select>
                                                    <button class="btn btn-outline-primary btn-sm mt-2">Export</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- === INTEGRATIONS TAB === -->
                                <div class="tab-pane fade settings-tab-pane" id="integrations" role="tabpanel">
                                    <!-- Connected Apps -->
                                    <div class="settings-form-section">
                                        <div class="settings-section-title">Connected Apps & Integrations</div>
                                        <form>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Shopify API Key</label>
                                                    <input type="text" class="form-control" placeholder="Paste Shopify API Key" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">HubSpot API Key</label>
                                                    <input type="text" class="form-control" placeholder="Paste HubSpot API Key" />
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Odoo API Key</label>
                                                    <input type="text" class="form-control" placeholder="Paste Odoo API Key" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Mailchimp API Key</label>
                                                    <input type="text" class="form-control" placeholder="Paste Mailchimp API Key" />
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- Sync Settings -->
                                    <div class="settings-form-section">
                                        <div class="settings-section-title">Sync Settings</div>
                                        <form>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Sync Products</label>
                                                    <select class="form-control">
                                                        <option>Manual</option>
                                                        <option>Hourly</option>
                                                        <option>Daily</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Sync Contacts</label>
                                                    <select class="form-control">
                                                        <option>Manual</option>
                                                        <option>Hourly</option>
                                                        <option>Daily</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <button class="btn btn-outline-secondary btn-sm">Sync Now</button>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- Developer/API Access -->
                                    <div class="settings-form-section">
                                        <div class="settings-section-title">Developer/API Access</div>
                                        <form>
                                            <div class="mb-2">
                                                <label class="settings-form-label">Generate API Key</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" value="APIKEY-xxxxxxx" readonly>
                                                    <button class="btn btn-outline-primary">Generate</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- === NOTIFICATIONS & PREFERENCES TAB === -->
                                <div class="tab-pane fade settings-tab-pane" id="notifications" role="tabpanel">
                                    <!-- Alerts -->
                                    <div class="settings-form-section">
                                        <div class="settings-section-title">Notifications & Alerts</div>
                                        <form>
                                            <div class="mb-3">
                                                <label class="settings-form-label">Notification Types</label>
                                                <div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="notif-payments" checked>
                                                        <label class="form-check-label" for="notif-payments">Payments Received</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="notif-invoices">
                                                        <label class="form-check-label" for="notif-invoices">New Invoice Issued</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="notif-stock">
                                                        <label class="form-check-label" for="notif-stock">Low Stock Alert</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="notif-reminders">
                                                        <label class="form-check-label" for="notif-reminders">Payment Reminders</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="settings-form-label">Recipients</label>
                                                <select class="form-control" multiple>
                                                    <option>Jane Smith (Admin)</option>
                                                    <option>John Doe (Finance)</option>
                                                    <option>May Reyes (Manager)</option>
                                                    <!-- more -->
                                                </select>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- Preferences -->
                                    <div class="settings-form-section">
                                        <div class="settings-section-title">Preferences</div>
                                        <form>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Timezone</label>
                                                    <select class="form-control">
                                                        <option value="Asia/Manila">Asia/Manila</option>
                                                        <option value="Asia/Singapore">Asia/Singapore</option>
                                                        <option value="Australia/Sydney">Australia/Sydney</option>
                                                        <option value="US/Pacific">US/Pacific</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Default Currency</label>
                                                    <select class="form-control">
                                                        <option value="PHP">PHP</option>
                                                        <option value="USD">USD</option>
                                                        <option value="AUD">AUD</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Date Format</label>
                                                    <select class="form-control">
                                                        <option value="Y-m-d">YYYY-MM-DD</option>
                                                        <option value="m/d/Y">MM/DD/YYYY</option>
                                                        <option value="d/m/Y">DD/MM/YYYY</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="settings-form-label">Language</label>
                                                    <select class="form-control">
                                                        <option>English</option>
                                                        <option>Filipino</option>
                                                        <option>Chinese</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!-- END TAB PANES -->
                            </div>
                        </div>
                    </div>

                </div> <!-- container -->
            </div> <!-- content -->
            <?php include 'layouts/footer.php'; ?>
        </div>
    </div>
    <!-- END wrapper -->

    <?php include 'layouts/right-sidebar.php'; ?>
    <?php include 'layouts/footer-scripts.php'; ?>
    <script src="assets/js/app.min.js"></script>
</body>
</html>
