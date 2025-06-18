<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>
<head>
    <title>POS | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .product-icon {
            font-size: 2.6rem;
            border-radius: 0.75rem;
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px auto;
        }
        .icon-tote { background: #4e73df; color: #fff; }
        .icon-backpack { background: #f6c23e; color: #fff; }
        .icon-messenger { background: #1cc88a; color: #fff; }
        .icon-sling { background: #e74a3b; color: #fff; }
        .icon-duffle { background: #36b9cc; color: #fff; }
        .icon-wallet { background: #a569bd; color: #fff; }
        .product-card:hover { box-shadow: 0 4px 12px #0002; }
        .cart-panel { min-width: 340px; max-width: 380px; border-left: 1px solid #e9ecef; background: #f9fafb; }
        .input-group-lg > .form-control { font-size: 1.15rem; }
    </style>
</head>
<body>
    <div class="d-flex flex-row vh-100">
        <!-- Sidebar -->
        <aside class="d-flex flex-column p-3 bg-white shadow-sm" style="width: 70px;">
            <a href="#" class="mb-4 text-center d-block"><i class="ri-store-3-line fs-2 text-primary"></i></a>
            <nav class="nav flex-column gap-3 align-items-center">
            <a href="#" class="nav-link p-0" title="Home">
                <span class="bg-warning bg-opacity-75 text-white rounded-3 d-inline-flex align-items-center justify-content-center" style="width:42px; height:42px;">
                    <i class="ri-home-5-line fs-4"></i>
                </span>
            </a>
            <!-- Other icons unchanged, but add a hover state if you like -->
                <a href="#" class="nav-link p-0 text-secondary" title="Customers"><i class="ri-user-3-line fs-4"></i></a>
                <a href="#" class="nav-link p-0 text-secondary" title="Orders"><i class="ri-archive-2-line fs-4"></i></a>
                <a href="#" class="nav-link p-0 text-secondary" title="Reports"><i class="ri-bar-chart-line fs-4"></i></a>
                <a href="#" class="nav-link p-0 text-secondary" title="Settings"><i class="ri-settings-3-line fs-4"></i></a>
            </nav>
            <div class="mt-auto text-center">
                <a href="#" class="nav-link p-0 text-secondary" title="Logout"><i class="ri-logout-box-r-line fs-4"></i></a>
            </div>
        </aside>

        <!-- Main POS Content -->
        <main class="flex-grow-1 bg-light px-4 py-3">
            <!-- Header -->
            <div class="d-flex align-items-center justify-content-between mb-4 px-1">
                <div class="d-flex align-items-center gap-3">
                    <!-- Add a logo or icon here -->
                    <i class="ri-store-3-line fs-2 text-primary me-2"></i>
                    <span class="fw-bold fs-3 mb-0">Restro POS</span>
                </div>
                <div class="flex-grow-1 mx-4">
                    <div class="input-group input-group-lg" style="max-width:420px; margin:0 auto;">
                        <span class="input-group-text bg-white border-end-0"><i class="ri-search-2-line"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Search products..." />
                    </div>
                </div>
                <!-- Placeholder for future table/select buttons or user profile -->
                <button class="btn btn-warning px-4 ms-2 rounded-2" style="font-weight:500;">
                    <i class="ri-user-3-line me-1"></i> User
                </button>
            </div>
            <!-- Categories Tabs -->
            <ul class="nav nav-pills mb-4 gap-2" id="bagCategories" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active d-flex align-items-center gap-1 px-3 py-2 rounded-pill"
                            id="totes-tab" data-bs-toggle="tab" data-bs-target="#totes" type="button" role="tab"
                            style="background:#ffa500; color:white;">
                        <i class="ri-shopping-bag-3-line"></i> Totes
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                <button class="nav-link d-flex align-items-center gap-1 px-3 py-2 rounded-pill"
                        id="backpacks-tab" data-bs-toggle="tab" data-bs-target="#backpacks" type="button" role="tab">
            <i class="ri-backpack-4-line"></i> Backpacks</button>
                </li>
                <li class="nav-item" role="presentation">
                <button class="nav-link d-flex align-items-center gap-1 px-3 py-2 rounded-pill"
                        id="backpacks-tab" data-bs-toggle="tab" data-bs-target="#backpacks" type="button" role="tab">
            <i class="ri-backpack-4-line"></i> Messengers</button>
                </li>
                <li class="nav-item" role="presentation">
                <button class="nav-link d-flex align-items-center gap-1 px-3 py-2 rounded-pill"
                        id="backpacks-tab" data-bs-toggle="tab" data-bs-target="#backpacks" type="button" role="tab">
            <i class="ri-backpack-4-line"></i> Duffles</button>
                </li>
                <li class="nav-item" role="presentation">
                <button class="nav-link d-flex align-items-center gap-1 px-3 py-2 rounded-pill"
                        id="backpacks-tab" data-bs-toggle="tab" data-bs-target="#backpacks" type="button" role="tab">
            <i class="ri-backpack-4-line"></i> Wallets</button>
                </li>
            </ul>
            <!-- Product Grid -->
            <div class="tab-content">
                <div class="tab-pane fade show active" id="totes" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="card product-card p-3 text-center h-100 shadow-sm">
                                <span class="product-icon icon-tote"><i class="ri-shopping-bag-3-line"></i></span>
                                <div class="fw-semibold mb-1">Classic Tote Bag</div>
                                <div class="text-muted small mb-2">Totes</div>
                                <div class="fw-bold fs-5 mb-2">₱1,295</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card product-card p-3 text-center h-100 shadow-sm">
                                <span class="product-icon icon-tote"><i class="ri-shopping-bag-4-line"></i></span>
                                <div class="fw-semibold mb-1">Everyday Canvas Tote</div>
                                <div class="text-muted small mb-2">Totes</div>
                                <div class="fw-bold fs-5 mb-2">₱899</div>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card product-card p-3 text-center h-100 shadow-sm">
                                <span class="product-icon icon-tote"><i class="ri-shopping-bag-line"></i></span>
                                <div class="fw-semibold mb-1">Eco Leather Tote</div>
                                <div class="text-muted small mb-2">Totes</div>
                                <div class="fw-bold fs-5 mb-2">₱1,799</div>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card product-card p-3 text-center h-100 shadow-sm">
                                <span class="product-icon icon-tote"><i class="ri-handbag-line"></i></span>
                                <div class="fw-semibold mb-1">Minimalist Tote</div>
                                <div class="text-muted small mb-2">Totes</div>
                                <div class="fw-bold fs-5 mb-2">₱1,499</div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- More tab-panes for Backpacks, Messengers, etc. For demo, one more category below -->
                <div class="tab-pane fade" id="backpacks" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="card product-card p-3 text-center h-100 shadow-sm">
                                <span class="product-icon icon-backpack"><i class="ri-backpack-4-line"></i></span>
                                <div class="fw-semibold mb-1">Urban Backpack</div>
                                <div class="text-muted small mb-2">Backpacks</div>
                                <div class="fw-bold fs-5 mb-2">₱1,899</div>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card product-card p-3 text-center h-100 shadow-sm">
                                <span class="product-icon icon-backpack"><i class="ri-backpack-line"></i></span>
                                <div class="fw-semibold mb-1">Traveler's Pack</div>
                                <div class="text-muted small mb-2">Backpacks</div>
                                <div class="fw-bold fs-5 mb-2">₱2,499</div>
                                <button class="btn btn-primary btn-sm w-100"><i class="ri-add-line"></i> Add</button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card product-card p-3 text-center h-100 shadow-sm">
                                <span class="product-icon icon-backpack"><i class="ri-backpack-3-line"></i></span>
                                <div class="fw-semibold mb-1">Casual Backpack</div>
                                <div class="text-muted small mb-2">Backpacks</div>
                                <div class="fw-bold fs-5 mb-2">₱1,299</div>
                                <button class="btn btn-primary btn-sm w-100"><i class="ri-add-line"></i> Add</button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card product-card p-3 text-center h-100 shadow-sm">
                                <span class="product-icon icon-backpack"><i class="ri-backpack-2-line"></i></span>
                                <div class="fw-semibold mb-1">Tech Backpack</div>
                                <div class="text-muted small mb-2">Backpacks</div>
                                <div class="fw-bold fs-5 mb-2">₱2,199</div>
                                <button class="btn btn-primary btn-sm w-100"><i class="ri-add-line"></i> Add</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Add other tab-panes for Messengers, Duffles, Wallets if needed -->
            </div>
        </main>

        <!-- Cart/Order Panel (Right Side) -->
        <aside class="cart-panel bg-white shadow rounded-4 px-4 py-4 ms-3 d-flex flex-column" style="max-width:400px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="ri-shopping-cart-2-line text-primary me-2"><div class="d-flex gap-2"></i>Order</h5>
                <button class="btn btn-outline-secondary btn-sm"><i class="ri-user-add-line me-1"></i>Add Customer</button>
            </div>
            <!-- Sample Cart Items -->
            <ul class="list-group mb-3">
                <li class="list-group-item d-flex flex-column gap-1 border-0 pb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>
                            <span class="badge bg-primary bg-opacity-25 text-primary me-2"><i class="ri-shopping-bag-3-line"></i></span>
                            Classic Tote Bag
                        </span>
                        <span class="fw-bold">₱1,295</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <div class="input-group input-group-sm" style="width:90px;">
                            <button class="btn btn-outline-secondary btn-sm" type="button"><i class="ri-subtract-line"></i></button>
                            <input type="text" class="form-control text-center" value="1" style="width:32px;">
                            <button class="btn btn-outline-secondary btn-sm" type="button"><i class="ri-add-line"></i></button>
                        </div>
                        <select class="form-select form-select-sm w-auto" style="min-width:80px;">
                            <option selected>Standard</option>
                            <option>Large</option>
                        </select>
                        <input type="text" class="form-control form-control-sm w-auto" placeholder="Note" style="min-width:70px;">
                        <button class="btn btn-link text-danger btn-sm px-2" title="Remove"><i class="ri-close-line"></i></button>
                    </div>
                </li>
                <li class="list-group-item d-flex flex-column gap-1 border-0 pb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>
                            <span class="badge bg-warning bg-opacity-25 text-warning me-2"><i class="ri-backpack-4-line"></i></span>
                            Urban Backpack
                        </span>
                        <span class="fw-bold">₱1,899</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <div class="input-group input-group-sm" style="width:90px;">
                            <button class="btn btn-outline-secondary btn-sm" type="button"><i class="ri-subtract-line"></i></button>
                            <input type="text" class="form-control text-center" value="2" style="width:32px;">
                            <button class="btn btn-outline-secondary btn-sm" type="button"><i class="ri-add-line"></i></button>
                        </div>
                        <select class="form-select form-select-sm w-auto" style="min-width:80px;">
                            <option selected>Standard</option>
                            <option>XL</option>
                        </select>
                        <input type="text" class="form-control form-control-sm w-auto" placeholder="Note" style="min-width:70px;">
                        <button class="btn btn-link text-danger btn-sm px-2" title="Remove"><i class="ri-close-line"></i></button>
                    </div>
                </li>
            </ul>
            <!-- Discount / Coupon / Note -->
            <div class="mb-3">
                <div class="input-group input-group-sm mb-2">
                    <span class="input-group-text"><i class="ri-discount-percent-line text-success"></i></span>
                    <input type="text" class="form-control" placeholder="Discount code">
                    <button class="btn btn-outline-success" type="button">Apply</button>
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="ri-sticky-note-line text-info"></i></span>
                    <input type="text" class="form-control" placeholder="Order note">
                </div>
            </div>
            <!-- Totals -->
            <div class="border-top pt-3 mb-3">
                <div class="d-flex justify-content-between">
                    <span>Subtotal</span>
                    <span class="fw-bold">₱5,093</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Tax (12%)</span>
                    <span class="fw-bold">₱611</span>
                </div>
                <div class="d-flex justify-content-between fs-4 fw-bolder text-success mt-2">
                    <span>Total</span>
                    <span>₱5,704</span>
                </div>
            </div>
            <!-- Action Buttons -->
            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-warning flex-fill rounded-pill fw-bold py-2" style="font-size:1.1rem;">
                    <i class="ri-pause-line"></i> Hold Order
                </button>
                <button class="btn btn-success flex-fill rounded-pill fw-bold py-2" style="font-size:1.1rem;">
                    <i class="ri-check-line"></i> Proceed
                </button>
            </div>
        </aside>
    </div>

    <?php include 'layouts/footer-scripts.php'; ?>
</body>
</html>
