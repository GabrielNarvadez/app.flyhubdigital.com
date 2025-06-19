<?php
session_start();
require_once __DIR__ . '/layouts/config.php';

$products = [];
$sql = "SELECT p.id, p.name, c.name as category, p.price, p.stock
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.status = 'active' AND p.stock > 0
        ORDER BY p.name ASC";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $name, $category, $price, $stock);
    while (mysqli_stmt_fetch($stmt)) {
        $products[] = [
            'id' => $id,
            'name' => $name,
            'category' => $category,
            'price' => $price,
            'stock' => $stock
        ];
    }
    mysqli_stmt_close($stmt);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Flyhub POS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Remixicon for icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --coca-orange: #FF7C34;
            --coca-yellow: #FFD600;
            --coca-green: #39C36E;
            --coca-gray: #F5F6FA;
            --coca-dark: #2C3246;
            --coca-card: #fff;
        }
        body {
            background: var(--coca-gray);
            font-family: 'Inter', Arial, sans-serif;
        }
        /* Sidebar */
        .sidebar-coca {
            background: var(--coca-card);
            min-height: 100vh;
            border-right: 1px solid #ececec;
            padding-top: 1.5rem;
        }
        .sidebar-coca .nav-link.active, .sidebar-coca .nav-link:focus {
            background: var(--coca-orange);
            color: #fff !important;
        }
        .sidebar-coca .nav-link {
            color: var(--coca-dark);
            font-weight: 500;
            border-radius: 0.75rem;
            margin-bottom: 0.3rem;
            transition: background .18s;
        }
        .sidebar-coca .nav-link:hover {
            background: #ffe3d0;
            color: var(--coca-orange);
        }
        .sidebar-coca .logo {
            font-size: 1.65rem;
            font-weight: bold;
            color: var(--coca-orange);
            letter-spacing: 2px;
            margin-bottom: 2.5rem;
        }

        /* Header */
        .coca-header {
            background: var(--coca-card);
            border-bottom: 1px solid #ececec;
            min-height: 64px;
            padding: 0.75rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
        }
        .coca-header .search-group {
            max-width: 400px;
            flex: 1 1 auto;
        }
        .coca-header .btn-dining {
            background: var(--coca-yellow);
            color: #222;
            border: none;
            font-weight: 600;
            border-radius: 8px;
            padding: 0.45rem 1.6rem;
        }
        .coca-header .btn-dining i {
            color: var(--coca-orange);
        }

        /* Category Nav */
        .coca-category {
            background: var(--coca-card);
            border-radius: 1rem;
            padding: 0.5rem 1.5rem;
        }
        .coca-category .nav-link {
            color: #333;
            font-weight: 500;
            border-radius: 1rem;
            padding: 0.55rem 1.5rem;
            margin: 0 0.3rem;
            transition: background .18s;
        }
        .coca-category .nav-link.active {
            background: var(--coca-orange);
            color: #fff;
        }
        .coca-category .nav-link i {
            margin-right: 0.5rem;
            font-size: 1.35rem;
        }

        /* Product Card */
        .product-card-coca {
            min-height: 275px;  /* adjust as needed */
            background: var(--coca-card);
            border-radius: 1rem;
            box-shadow: 0 2px 12px #24264909;
            padding: 1.4rem 1rem 1rem 1rem;
            margin-bottom: 1.5rem;
            transition: box-shadow .18s;
            min-height: 210px;
        }
        .product-card-coca:hover {
            box-shadow: 0 4px 24px #FF7C3440;
        }
        .product-card-coca .prod-img {
            background: #FFEEE5;
            border-radius: 50%;
            width: 62px;
            height: 62px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem auto;
            font-size: 2.1rem;
            color: var(--coca-orange);
        }
        .product-card-coca .prod-title {
            min-height: 48px; /* adjust this value if your longest product name needs more space */
            font-weight: 600;
            color: var(--coca-dark);
            font-size: 1.07rem;
        }
        .product-card-coca .prod-cat {
            color: #aaa;
            font-size: 0.96rem;
            margin-bottom: 2px;
        }
        .product-card-coca .prod-price {
            font-size: 1.23rem;
            color: var(--coca-orange);
            font-weight: bold;
        }
        .product-card-coca .btn {
            font-weight: 600;
            border-radius: 8px;
            margin-top: 0.2rem;
        }

        /* Cart / Order Panel */
        .cart-coca-panel {
            background: var(--coca-card);
            border-radius: 1rem;
            box-shadow: 0 2px 16px #24264914;
            padding: 1.7rem 1.2rem 1.1rem 1.2rem;
            min-width: 325px;
            max-width: 100%;
            margin-left: 2rem;
            margin-top: 1.1rem;
        }
        .cart-coca-panel .cart-title {
            font-weight: 700;
            font-size: 1.23rem;
            color: var(--coca-dark);
            margin-bottom: 1.25rem;
        }
        .cart-coca-panel .btn-customer {
            background: var(--coca-orange);
            color: #fff;
            border-radius: 7px;
            font-weight: 600;
        }
        .cart-coca-panel .list-group-item {
            border: none;
            padding: 0.7rem 0.3rem;
            border-radius: 8px;
            margin-bottom: 0.55rem;
            background: #FFEEE5;
        }
        .cart-coca-panel .cart-actions .btn {
            border-radius: 7px;
            font-weight: 600;
        }
        .cart-coca-panel .totals-row {
            font-weight: 600;
        }
        .cart-coca-panel .totals-final {
            color: var(--coca-orange);
            font-size: 1.35rem;
            font-weight: 700;
        }

        /* Responsive */
        @media (max-width: 1199.98px) {
            .cart-coca-panel {
                margin-left: 1rem;
            }
        }
        @media (max-width: 991.98px) {
            .sidebar-coca {
                display: none !important;
            }
            .cart-coca-panel {
                margin-left: 0;
                max-width: 100%;
                margin-top: 2.5rem;
            }
        }
        @media (max-width: 767.98px) {
            .coca-header {
                flex-direction: column;
                gap: 0.7rem;
                padding: 0.6rem 1rem;
            }
            .cart-coca-panel {
                margin: 1.2rem 0 0 0;
                padding: 1.2rem 0.5rem 1rem 0.5rem;
                border-radius: 0.7rem;
            }
        }
        @media (max-width: 575.98px) {
            .coca-header .search-group { max-width: 99%; }
        }

            /* Modern Checkout Modal Styling */
.checkout-modal-modern .modal-content {
    border-radius: 1.2rem;
    box-shadow: 0 6px 36px #FF7C3444, 0 2px 12px #2C324628;
    border: none;
    background: var(--coca-card);
    animation: modalpop .19s cubic-bezier(.28,1.08,.34,.94);
}
@keyframes modalpop {
  0% { transform: scale(.93) translateY(25px); opacity: 0.6; }
  100% { transform: scale(1) translateY(0); opacity: 1; }
}
.checkout-modal-modern .modal-header {
    border: none;
    padding-bottom: 0.5rem;
}
.checkout-modal-modern .modal-title {
    font-weight: 700;
    color: var(--coca-dark);
}
.checkout-modal-modern .amount-increment-row {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0.5rem;
    margin-bottom: 1rem;
}
.checkout-modal-modern .amount-increment-row .btn {
    font-weight: 700;
    border-radius: 10px;
    font-size: 1rem;
    transition: background .15s, box-shadow .15s;
    border: 1.5px solid #FFD60022;
    background: #FFF8EA;
}
.checkout-modal-modern .amount-increment-row .btn:hover,
.checkout-modal-modern .amount-increment-row .btn:active {
    background: var(--coca-yellow);
    color: #333;
    box-shadow: 0 2px 10px #FFD60040;
}
.checkout-modal-modern .form-control-lg {
    font-size: 1.35rem;
    border-radius: 9px;
    border: 2px solid #FF7C3440;
    box-shadow: none;
}
.checkout-modal-modern .payment-method-btn {
    font-weight: 600;
    border-radius: 8px;
    min-width: 100px;
    border: 1.5px solid #FF7C3440;
    background: #FFF6F2;
    color: var(--coca-dark);
    transition: background .14s, border .14s, color .14s;
}
.checkout-modal-modern .payment-method-btn.active,
.checkout-modal-modern .payment-method-btn:focus,
.checkout-modal-modern .payment-method-btn:hover {
    background: var(--coca-orange);
    color: #fff;
    border-color: var(--coca-orange);
}
.checkout-modal-modern #currentPaymentMethod {
    font-size: 1.1rem;
    letter-spacing: .3px;
}
.checkout-modal-modern .modern-numpad {
    width: 100%;
    margin: 1.2rem 0 0.5rem 0;
    user-select: none;
}
.checkout-modal-modern .modern-numpad-row {
    display: flex;
    gap: 0.8rem;
    margin-bottom: 0.75rem;
}
.checkout-modal-modern .modern-numpad-btn {
    flex: 1 1 0;
    font-size: 1.52rem;
    padding: 1.18rem 0;
    background: #F7F8FB;
    border: 2.5px solid #E6E7EC;
    border-radius: 12px;
    text-align: center;
    font-weight: 700;
    color: #313249;
    transition: background .16s, border .16s, color .16s;
    box-shadow: 0 2px 12px #0001;
    outline: none;
    cursor: pointer;
}
.checkout-modal-modern .modern-numpad-btn:active,
.checkout-modal-modern .modern-numpad-btn:hover {
    background: var(--coca-orange);
    color: #fff;
    border-color: var(--coca-orange);
}
.checkout-modal-modern .modern-numpad-btn.cancel-btn {
    background: #FFDADA;
    color: #e12c2c;
    border: 2.5px solid #ff6b6b70;
    font-size: 1.15rem;
    font-weight: 700;
    letter-spacing: 1px;
    border-radius: 12px;
    transition: background .14s, color .14s, border .14s;
}
.checkout-modal-modern .modern-numpad-btn.cancel-btn:hover,
.checkout-modal-modern .modern-numpad-btn.cancel-btn:active {
    background: #e12c2c;
    color: #fff;
    border-color: #e12c2c;
}
.checkout-modal-modern .checkout-change-area {
    text-align: center;
    font-size: 1.45rem;
    font-weight: 700;
    border-radius: 8px;
    margin-bottom: 1.2rem;
    padding: 0.65rem 0.2rem 0.3rem 0.2rem;
    background: #FFF8EA;
    color: var(--coca-green);
    border: 2px dashed #FFD60060;
    box-shadow: 0 2px 8px #FFD60011;
    min-height: 42px;
    transition: background .16s, color .16s;
}
.checkout-modal-modern .checkout-change-area.error {
    background: #FFDADA;
    color: #c03131;
    border-color: #e12c2c77;
}
.checkout-modal-modern .btn-success {
    background: var(--coca-green);
    border: none;
    border-radius: 9px;
    font-weight: 700;
    font-size: 1.24rem;
    padding: 1rem 0;
    letter-spacing: 1px;
    transition: background .16s, box-shadow .16s;
}
.checkout-modal-modern .btn-success:disabled {
    background: #b8dfca;
    color: #fff;
    border: none;
}
.checkout-modal-modern .modal-footer {
    border: none;
    padding-top: 0;
}
@media (max-width: 575.98px) {
    .checkout-modal-modern .modal-content {
        min-width: 95vw;
        padding: 0 4px;
    }
    .checkout-modal-modern .modern-numpad-btn { font-size: 1.05rem; padding: 0.8rem 0; }
}

#amountReceived {
    font-size: 2.1rem !important;
    font-weight: 700 !important;
    color: #222 !important;
    letter-spacing: 1.5px;
    /* Keep the border and background as is for design consistency */
}

    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">

        <!-- SIDEBAR -->
        <nav class="col-auto sidebar-coca d-none d-lg-flex flex-column align-items-center px-3">
            <div class="logo">FLYHUB</div>
            <ul class="nav flex-column w-100 align-items-stretch">
                <li class="nav-item mb-2">
                    <a class="nav-link active" href="#"><i class="ri-home-5-line me-2"></i> Home</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link" href="#"><i class="ri-user-3-line me-2"></i> Customers</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link" href="#"><i class="ri-shopping-bag-3-line me-2"></i> Orders</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link" href="#"><i class="ri-bar-chart-2-line me-2"></i> Reports</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link" href="#"><i class="ri-settings-3-line me-2"></i> Settings</a>
                </li>
            </ul>
        </nav>
        <!-- END SIDEBAR -->

        <div class="col p-0 d-flex flex-column min-vh-100">

            <!-- HEADER -->
            <header class="coca-header">
                <div class="fw-bold fs-4" style="letter-spacing:1.3px;">
                    Point of Sale
                </div>
                <div class="search-group">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="ri-search-line"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Search for food, drinks...">
                    </div>
                </div>
                    <button class="btn btn-dining ms-2" id="logoutBtn">
                      <i class="ri-logout-box-r-line me-2"></i> Logout
                    </button>
            </header>
            <!-- END HEADER -->

            <!-- MAIN BODY -->
            <main class="flex-grow-1 p-3 p-md-4">
                <!-- CATEGORIES -->
                <nav class="coca-category mb-4 d-flex flex-row justify-content-center flex-wrap">
                    <a class="nav-link active" href="#">
                      <i class="ri-handbag-line"></i> All Bags
                    </a>
                    <a class="nav-link" href="#">
                      <i class="ri-handbag-line"></i> Backpacks
                    </a>
                    <a class="nav-link" href="#">
                      <i class="ri-shopping-bag-3-line"></i> Shoulder & Crossbody Bags
                    </a>
                    <a class="nav-link" href="#">
                      <i class="ri-briefcase-3-line"></i> Totes & Satchels
                    </a>
                    <a class="nav-link" href="#">
                      <i class="ri-more-2-line"></i> Others
                    </a>
                </nav>
                <div class="row">
                    <!-- PRODUCT GRID -->
                    <div class="col-lg-8">
                        <div class="row">
                            <?php if (empty($products)): ?>
                                <div class="col-12">
                                    <div class="alert alert-warning text-center">
                                        No products available.
                                    </div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($products as $p): ?>
                                <div class="col-12 col-md-6 col-lg-3">
                                    <div class="product-card-coca mb-4">
                                        <div class="prod-img">
                                            <i class="ri-restaurant-line"></i>
                                        </div>
                                            <div class="prod-title"><?php echo htmlspecialchars($p['name'] ?? ''); ?></div>
                                            <div class="prod-cat mb-1"><?php echo htmlspecialchars($p['category'] ?? ''); ?></div>
                                        <div class="prod-price mb-2">
                                            ₱<?php echo number_format($p['price'], 0); ?>
                                        </div>
                                            <button class="btn btn-outline-primary w-100 add-to-cart-btn"
                                                data-product-id="<?php echo $p['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($p['name'] ?? ''); ?>"
                                                data-price="<?php echo $p['price']; ?>"
                                                data-stock="<?php echo $p['stock']; ?>"
                                                data-attributes='<?php echo htmlspecialchars(json_encode(["Color" => "Black", "Size" => "Standard"])); // change as needed ?>'
                                                <?php if($p['price'] <= 0 || $p['stock'] <= 0) echo 'disabled'; ?>>
                                                Add
                                            </button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- CART/ORDER PANEL -->
                    <div class="col-lg-4">
                        <div class="cart-coca-panel mt-0" id="cartPanel">
                          <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="cart-title"><i class="ri-shopping-cart-2-line me-1"></i> Order</div>
                            <button class="btn btn-customer btn-sm" id="addCustomerBtn"><i class="ri-user-add-line me-1"></i> Add Customer</button>
                          </div>
                          <!-- Cart Items -->
                          <ul class="list-group mb-3" id="cartItems"></ul>
                          <!-- Discount/Note/Etc -->
                          <div class="mb-2">
                            <input type="text" class="form-control mb-2" id="discountInput" placeholder="Discount (%)">
                            <input type="text" class="form-control" id="orderNoteInput" placeholder="Order note">
                          </div>
                          <!-- Totals -->
                          <div class="mb-3">
                            <div class="d-flex justify-content-between totals-row">
                              <span>Subtotal</span>
                              <span id="subtotal">₱0</span>
                            </div>
                            <div class="d-flex justify-content-between totals-row">
                              <span>Tax</span>
                              <span id="tax">₱0</span>
                            </div>
                            <div class="d-flex justify-content-between totals-final">
                              <span>Total</span>
                              <span id="total">₱0</span>
                            </div>
                          </div>
                          <!-- Actions -->
                          <div class="d-flex gap-2 cart-actions">
                            <button class="btn btn-outline-secondary w-50" id="holdOrderBtn">Hold Order</button>
                            <button class="btn btn-success w-50" id="proceedBtn">Checkout</button>
                          </div>
                        </div>
                </div>
            </main>
            <!-- END MAIN BODY -->

        </div>
    </div>
</div>
<!-- Bootstrap JS (for dropdowns/tabs if needed) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Add Customer Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="customerModalLabel">Select/Add Customer</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="text" id="customerName" class="form-control" placeholder="Enter customer name">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="saveCustomerBtn">Save</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Checkout Modal -->

<!-- Checkout Modal -->
<div class="modal fade checkout-modal-modern" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content shadow p-3">
      <div class="modal-header">
        <h5 class="modal-title" id="checkoutModalLabel">Checkout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-4 align-items-start">
          <!-- LEFT COLUMN -->
          <div class="col-12 col-md-6">
            <div class="fw-bold mb-1">Total Amount: <span id="checkoutTotal" class="fs-3 text-dark"></span></div>
            <input type="number" min="0" class="form-control form-control-lg mb-3" id="amountReceived" placeholder="Amount received" style="min-height: 70px;">
            <div class="mb-1">Current Payment Method: <span id="currentPaymentMethod" class="fw-bold text-primary">Cash</span></div>
            <div class="checkout-change-area mb-2" id="checkoutChange"></div>
            <div class="small text-danger mt-2" id="checkoutError" style="min-height:18px;text-align:left"></div>
          </div>
          <!-- RIGHT COLUMN -->
          <div class="col-12 col-md-6">
            <div class="d-flex gap-2 mb-2 flex-wrap">
              <button type="button" class="btn payment-method-btn active" data-method="Cash">Cash</button>
              <button type="button" class="btn payment-method-btn" data-method="Gcash">Gcash</button>
              <button type="button" class="btn payment-method-btn" data-method="Credit Card">Credit Card</button>
              <button type="button" class="btn payment-method-btn" data-method="Debit Card">Debit Card</button>
            </div>
            <div class="amount-increment-row mb-3">
              <button class="btn increment-cash" data-inc="50">+₱50</button>
              <button class="btn increment-cash" data-inc="100">+₱100</button>
              <button class="btn increment-cash" data-inc="500">+₱500</button>
              <button class="btn increment-cash" data-inc="1000">+₱1000</button>
              <button class="btn btn-light" id="clearAmountBtn">Clear</button>
            </div>
            <div class="modern-numpad mb-3">
              <div class="modern-numpad-row">
                <button type="button" class="modern-numpad-btn num-btn" data-num="7">7</button>
                <button type="button" class="modern-numpad-btn num-btn" data-num="8">8</button>
                <button type="button" class="modern-numpad-btn num-btn" data-num="9">9</button>
              </div>
              <div class="modern-numpad-row">
                <button type="button" class="modern-numpad-btn num-btn" data-num="4">4</button>
                <button type="button" class="modern-numpad-btn num-btn" data-num="5">5</button>
                <button type="button" class="modern-numpad-btn num-btn" data-num="6">6</button>
              </div>
              <div class="modern-numpad-row">
                <button type="button" class="modern-numpad-btn num-btn" data-num="1">1</button>
                <button type="button" class="modern-numpad-btn num-btn" data-num="2">2</button>
                <button type="button" class="modern-numpad-btn num-btn" data-num="3">3</button>
              </div>
              <div class="modern-numpad-row">
                <button type="button" class="modern-numpad-btn num-btn" data-num="0">0</button>
                <button type="button" class="modern-numpad-btn backspace-btn"><i class="ri-arrow-left-line"></i></button>
                <button type="button" class="modern-numpad-btn cancel-btn cancel-btn-modal">Cancel</button>
              </div>
            </div>
            <button type="button" class="btn btn-success btn-lg w-100" id="confirmCheckoutBtn" disabled>Confirm Payment</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


    <!-- New Transaction Modal -->
    <div class="modal fade" id="newTransModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Transaction Complete</h5>
          </div>
          <div class="modal-body">
            <p>Order has been recorded. Start a new transaction?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="startNewTransBtn">Start New Transaction</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Alert Modal -->
<div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center py-4">
        <div class="mb-2 fs-2 text-warning"><i class="ri-error-warning-line"></i></div>
        <div class="mb-1 fs-5" id="alertModalMsg">No orders in cart!</div>
        <button type="button" class="btn btn-primary mt-2 px-4" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-4">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <div class="mb-3 fs-4 text-warning"><i class="ri-logout-box-r-line"></i></div>
        <div class="mb-2 fs-5">Are you sure you want to logout?</div>
      </div>
      <div class="modal-footer border-0 justify-content-center">
        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger px-4" id="confirmLogoutBtn">Logout</button>
      </div>
    </div>
  </div>
</div>

<script>


let cart = [];
let customer = '';
let discountPercent = 0;
const TAX_RATE = 0.12;

// Add to Cart logic
document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if (document.getElementById('cartPanel').classList.contains('locked')) return;
        const id = this.dataset.productId;
        const name = this.dataset.name;
        const price = parseFloat(this.dataset.price);
        const stock = parseInt(this.dataset.stock,10);
        const attrs = JSON.parse(this.dataset.attributes || '{}');
        let cartItem = cart.find(i => i.id === id && JSON.stringify(i.attrs) === JSON.stringify(attrs));
        if(cartItem) {
            if(cartItem.qty < stock) cartItem.qty++;
        } else {
            if(stock > 0) cart.push({id, name, price, stock, qty:1, attrs});
        }
        renderCart();
    });
});

function renderCart() {
    const list = document.getElementById('cartItems');
    list.innerHTML = '';
    let subtotal = 0;
    cart.forEach((item, idx) => {
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex align-items-center justify-content-between gap-2 flex-wrap';
        // Attributes
        let attrsText = '';
        if(Object.keys(item.attrs).length) {
            attrsText = '<small class="d-block text-muted">'+Object.entries(item.attrs).map(([k,v])=>`${k}: ${v}`).join(', ')+'</small>';
        }
        li.innerHTML = `
            <div>
                <span>${item.name}</span>
                <span class="badge bg-warning ms-2">x${item.qty}</span>
                ${attrsText}
            </div>
            <div class="d-flex align-items-center gap-1">
                <button class="btn btn-sm btn-light px-2 minus-btn" data-idx="${idx}">-</button>
                <button class="btn btn-sm btn-light px-2 plus-btn" data-idx="${idx}">+</button>
                <button class="btn btn-sm btn-link text-danger px-2 remove-btn" data-idx="${idx}"><i class="ri-close-line"></i></button>
                <span class="fw-bold ms-2">₱${(item.price*item.qty).toLocaleString()}</span>
            </div>
        `;
        list.appendChild(li);
        subtotal += item.price*item.qty;
    });
    // Totals
    document.getElementById('subtotal').textContent = '₱'+subtotal.toLocaleString();
    let discountVal = discountPercent ? subtotal * discountPercent / 100 : 0;
    let taxed = subtotal - discountVal;
    let tax = Math.round(taxed * TAX_RATE);
    let total = taxed + tax;
    document.getElementById('tax').textContent = '₱'+tax.toLocaleString();
    document.getElementById('total').textContent = '₱'+total.toLocaleString();

    // Minus/Plus/Remove logic
    document.querySelectorAll('.minus-btn').forEach(b=>b.onclick=e=>{
        const i = +b.dataset.idx;
        if(cart[i].qty > 1) cart[i].qty--;
        renderCart();
    });
    document.querySelectorAll('.plus-btn').forEach(b=>b.onclick=e=>{
        const i = +b.dataset.idx;
        if(cart[i].qty < cart[i].stock) cart[i].qty++;
        renderCart();
    });
    document.querySelectorAll('.remove-btn').forEach(b=>b.onclick=e=>{
        cart.splice(+b.dataset.idx,1); renderCart();
    });
}

// Discount logic
document.getElementById('discountInput').addEventListener('input', function() {
    let v = parseFloat(this.value);
    discountPercent = (v > 0 && v <= 100) ? v : 0;
    renderCart();
});

// Add Customer Modal
document.getElementById('addCustomerBtn').onclick = () => {
    if(document.getElementById('cartPanel').classList.contains('locked')) return;
    new bootstrap.Modal(document.getElementById('customerModal')).show();
};
document.getElementById('saveCustomerBtn').onclick = () => {
    customer = document.getElementById('customerName').value.trim();
    bootstrap.Modal.getInstance(document.getElementById('customerModal')).hide();
};

// Proceed/Checkout logic
document.getElementById('proceedBtn').onclick = () => {
    if(document.getElementById('cartPanel').classList.contains('locked')) return;
    if (!cart.length) {
        document.getElementById('alertModalMsg').textContent = "Please add at least one product before checkout.";
        new bootstrap.Modal(document.getElementById('alertModal')).show();
        return;
    }
    document.getElementById('checkoutTotal').textContent = document.getElementById('total').textContent;
    document.getElementById('amountReceived').value = '';
    document.getElementById('checkoutChange').innerHTML = '';
    document.getElementById('checkoutError').textContent = '';
    paymentMethod = 'Cash';
    document.querySelectorAll('.payment-method-btn').forEach(b=>b.classList.remove('active'));
    document.querySelector('.payment-method-btn[data-method="Cash"]').classList.add('active');
    document.getElementById('currentPaymentMethod').textContent = paymentMethod;
    updateCheckoutChange();
    new bootstrap.Modal(document.getElementById('checkoutModal')).show();
};

document.getElementById('amountReceived').oninput = function() {
    const total = Number(document.getElementById('total').textContent.replace(/[₱,]/g,''));
    const received = Number(this.value);
    document.getElementById('checkoutChange').innerHTML = received >= total
      ? `<span class="text-success">Change: ₱${(received-total).toLocaleString()}</span>`
      : `<span class="text-danger">Insufficient amount</span>`;
};

document.getElementById('confirmCheckoutBtn').onclick = () => {
    const total = Number(document.getElementById('total').textContent.replace(/[₱,]/g,''));
    const received = Number(document.getElementById('amountReceived').value);
    if(received < total) return;

    // Gather data for saving
    let items = cart.map(i => ({
        id: i.id,
        name: i.name,
        price: i.price,
        qty: i.qty,
        attrs: i.attrs
    }));
    let tax = Number(document.getElementById('tax').textContent.replace(/[₱,]/g,''));
    let discount = discountPercent || 0;
    let customerName = customer;
    let note = document.getElementById('orderNoteInput').value || '';
    let payment = paymentMethod;

    // Save to DB via AJAX
    fetch('save-sale.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'items='+encodeURIComponent(JSON.stringify(items))+'&total='+total+'&tax='+tax+'&discount='+discount+'&customer='+encodeURIComponent(customerName)+'&note='+encodeURIComponent(note)+'&payment='+encodeURIComponent(payment)
    }).then(res=>res.json()).then(data=>{
        if(data.success) {
            document.getElementById('cartPanel').classList.add('locked');
            document.querySelectorAll('.add-to-cart-btn').forEach(b=>b.disabled=true);
            bootstrap.Modal.getInstance(document.getElementById('checkoutModal')).hide();
            new bootstrap.Modal(document.getElementById('newTransModal')).show();
        } else {
            alert('Error saving sale. Please try again.');
        }
    });
};

// Start new transaction logic
document.getElementById('startNewTransBtn').onclick = () => {
    cart = [];
    customer = '';
    discountPercent = 0;
    document.getElementById('discountInput').value = '';
    document.getElementById('orderNoteInput').value = '';
    document.getElementById('cartPanel').classList.remove('locked');
    document.querySelectorAll('.add-to-cart-btn').forEach(b=>b.disabled=false);
    renderCart();
    bootstrap.Modal.getInstance(document.getElementById('newTransModal')).hide();
};

// Initial render
renderCart();

let paymentMethod = 'Cash';

function updateCheckoutChange() {
  const total = Number(document.getElementById('checkoutTotal').textContent.replace(/[₱,]/g, ''));
  const received = Number(document.getElementById('amountReceived').value);
  let change = received - total;
  const error = document.getElementById('checkoutError');
  const btn = document.getElementById('confirmCheckoutBtn');
  if (isNaN(received) || received === 0) {
    document.getElementById('checkoutChange').textContent = '';
    btn.disabled = true;
    error.textContent = '';
    return;
  }
  if (change < 0) {
    document.getElementById('checkoutChange').textContent = '';
    error.textContent = 'Insufficient amount';
    btn.disabled = true;
  } else {
    document.getElementById('checkoutChange').textContent = `Change: ₱${change.toLocaleString()}`;
    error.textContent = '';
    btn.disabled = false;
  }
}

// Quick add cash buttons
document.querySelectorAll('.increment-cash').forEach(btn => {
  btn.onclick = function() {
    const inc = parseInt(this.dataset.inc, 10);
    let field = document.getElementById('amountReceived');
    let val = Number(field.value) || 0;
    field.value = val + inc;
    updateCheckoutChange();
  }
});
document.getElementById('clearAmountBtn').onclick = () => {
  document.getElementById('amountReceived').value = '';
  updateCheckoutChange();
};

// Payment methods
document.querySelectorAll('.payment-method-btn').forEach(btn => {
  btn.onclick = function() {
    document.querySelectorAll('.payment-method-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    paymentMethod = this.dataset.method;
    document.getElementById('currentPaymentMethod').textContent = paymentMethod;
  }
});

// On-screen number pad
document.querySelectorAll('.num-btn').forEach(btn => {
  btn.onclick = function() {
    let field = document.getElementById('amountReceived');
    field.value = (field.value || '') + btn.dataset.num;
    updateCheckoutChange();
  }
});
document.querySelector('.backspace-btn').onclick = function() {
  let field = document.getElementById('amountReceived');
  field.value = field.value.slice(0, -1);
  updateCheckoutChange();
};
document.querySelector('.cancel-btn').onclick = function() {
  bootstrap.Modal.getInstance(document.getElementById('checkoutModal')).hide();
};

// Amount received live input
document.getElementById('amountReceived').oninput = updateCheckoutChange;


document.getElementById('logoutBtn').onclick = function() {
  new bootstrap.Modal(document.getElementById('logoutModal')).show();
};

document.getElementById('confirmLogoutBtn').onclick = function() {
  window.location.href = "pos.php"; // <-- Redirect to POS or your logout logic
};
</script>

</body>
</html>
