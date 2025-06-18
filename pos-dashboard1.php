<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['name']    = 'Cashier 1';
}
require_once __DIR__ . '/layouts/config.php';

// Handle checkout POST (AJAX)
if (
  $_SERVER['REQUEST_METHOD'] === 'POST' &&
  isset($_POST['checkout'], $_POST['payment_method'], $_POST['amount_paid'])
) {
  $cart           = json_decode($_POST['checkout'], true);
  $payment_method = $_POST['payment_method'];
  $amount_paid    = (float) $_POST['amount_paid'];
  if (!$cart || count($cart) === 0) {
      exit(json_encode(['status' => 'error', 'message' => 'Cart is empty.']));
  }
  $total = 0;
  foreach ($cart as $item) {
      $price = isset($item['price']) ? (float)$item['price'] : 0;
      $qty   = isset($item['qty'])   ? (int)$item['qty']     : 0;
      $total += $price * $qty;
  }
  if ($amount_paid < $total) {
      exit(json_encode(['status' => 'error', 'message' => 'Insufficient payment.']));
  }
  $stmt   = $link->prepare(
      "INSERT INTO sales
         (total, payment_method, amount_paid, change_given, user_id)
       VALUES (?,?,?,?,?)"
  );
  $change  = $amount_paid - $total;
  $user_id = $_SESSION['user_id'];
  $stmt->bind_param("dsddi", $total, $payment_method, $amount_paid, $change, $user_id);
  $stmt->execute();
  $sale_id = $stmt->insert_id;
  $stmt->close();
  $itemStmt    = $link->prepare(
      "INSERT INTO sale_items
         (sale_id, product_id, quantity, price, total)
       VALUES (?,?,?,?,?)"
  );
  $decStockStmt = $link->prepare(
      "UPDATE products
          SET stock = GREATEST(stock - ?, 0)
        WHERE id = ?"
  );
  foreach ($cart as $item) {
      $pid   = isset($item['id'])    ? (int)$item['id']    : 0;
      $qty   = isset($item['qty'])   ? (int)$item['qty']   : 0;
      $price = isset($item['price']) ? (float)$item['price'] : 0;
      $line_total = $qty * $price;
      $itemStmt->bind_param("iiidd", $sale_id, $pid, $qty, $price, $line_total);
      $itemStmt->execute();
      $decStockStmt->bind_param("ii", $qty, $pid);
      $decStockStmt->execute();
  }
  $itemStmt->close();
  $decStockStmt->close();
  exit(json_encode([
      'status'  => 'success',
      'sale_id' => $sale_id,
      'change'  => $change
  ]));
}

$result   = $link->query("
   SELECT
     id,
     name,
     price,
     category_id
   FROM products
   ORDER BY name ASC
 ");
$products = $result->fetch_all(MYSQLI_ASSOC);

// Fetch categories for the filter dropdown
$cat_result = $link->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
$categories = [];
$res_cat = mysqli_query($link, "SELECT id, name FROM categories ORDER BY name");
while ($cat = mysqli_fetch_assoc($res_cat)) {
    $categories[$cat['id']] = $cat['name'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>POS Interface</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="pos.css">
</head>
<!-- POS HEADER with Burger Menu -->
<nav class="pos-header navbar navbar-dark fixed-top" style="background:#326d61;min-height:60px;z-index:1050;">
  <div class="container-fluid px-3 d-flex align-items-center justify-content-between">
    <!-- Burger Menu Button -->
    <div class="dropdown">
      <button class="btn btn-outline-light border-0 fs-2 lh-1 px-2 py-1" type="button" id="posBurgerMenu" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="navbar-toggler-icon"></span>
      </button>
      <ul class="dropdown-menu" aria-labelledby="posBurgerMenu">
        <li>
          <a class="dropdown-item" href="admin-dashboard.php">
            <i class="bi bi-arrow-left me-2"></i>Back to Admin
          </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
            <i class="bi bi-box-arrow-right me-2"></i>Logout
          </a>
        </li>
      </ul>
    </div>
    <!-- POS Title (Centered) -->
    <span class="navbar-brand mx-auto fs-4 fw-bold text-white">Point of Sale</span>
    <!-- Placeholder for spacing (keeps title centered) -->
    <span style="width:44px;"></span>
  </div>
</nav>
<!-- Spacer for fixed nav -->
<div style="height:60px;"></div>

<body>
<div class="container-fluid py-3">
  <div class="row justify-content-center align-items-start">
    <!-- Cart Left -->
    <div class="col-lg-4 mb-4">
      <div class="cart-card p-3 shadow-sm">
        <div class="cart-header mb-3 d-flex align-items-center">
          <i class="bi bi-cart-check-fill me-2"></i> Cart
        </div>
        <ul class="list-group mb-2" id="cart-items"></ul>
        <div class="cart-total-bar d-flex justify-content-between">
          <span>Total:</span>
          <span id="cart-total">₱0.00</span>
        </div>
        <button
          class="btn checkout-btn w-100"
          onclick="showPaymentModal()"
          id="checkoutBtn"
        ><i class="bi bi-cash-coin me-1"></i> Checkout</button>
      </div>
    </div>
    <!-- Products Grid Right -->
    <div class="col-lg-8">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="fw-bold text-success fs-5">
          Welcome, <?= htmlspecialchars($_SESSION['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>!
        </div>
        <button
          class="btn btn-outline-dark btn-sm"
          data-bs-toggle="modal"
          data-bs-target="#logoutModal"
        >Logout</button>
      </div>

      <div class="d-flex gap-2 mb-3"">

<select id="category-filter" class="form-select form-select-lg fw-semibold" onchange="filterProducts()">
  <option value="">All Categories</option>
  <?php foreach ($categories as $cat_id => $cat_name): ?>
    <option value="<?= htmlspecialchars($cat_id, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($cat_name, ENT_QUOTES, 'UTF-8') ?></option>
  <?php endforeach; ?>
</select>

        <input
          type="text"
          class="form-control form-control-lg"
          id="search-input"
          placeholder="Search products..."
          oninput="filterProducts()"
          style="flex:1 1 0;min-width:140px;"
        >
      </div>

      <div id="product-scroll-area">
        <div class="row g-2" id="product-grid">
          <?php foreach ($products as $product): ?>
            <?php
              $name = $product['name'] ?? '';
              // Choose a color/icon based on product type
              $icon = 'bi-bag'; $color = '#47b6a8';
              if (stripos($name, 'bag') !== false)        { $icon = 'bi-bag'; $color = '#47b6a8'; }
              elseif (stripos($name, 'coffee') !== false) { $icon = 'bi-cup-hot'; $color = '#a87945'; }
              elseif (stripos($name, 'pizza') !== false)  { $icon = 'bi-pizza'; $color = '#ea6835'; }
              elseif (stripos($name, 'drink') !== false)  { $icon = 'bi-cup-straw'; $color = '#5bb4fa'; }
              elseif (stripos($name, 'hot dog') !== false){ $icon = 'bi-egg-fried'; $color = '#faad3b'; }
              elseif (stripos($name, 'burger') !== false) { $icon = 'bi-egg-fried'; $color = '#b17e57'; }
              elseif (stripos($name, 'charm') !== false)  { $icon = 'bi-gem'; $color = '#cc5ec2'; }
              elseif (stripos($name, 'utility') !== false){ $icon = 'bi-backpack'; $color = '#4b80fa'; }
              elseif (stripos($name, 'mini') !== false)   { $icon = 'bi-bag-heart'; $color = '#c04d45'; }
              // Add more as needed for your actual products
            ?>
            <div class="col">
                <div
                  class="product-card text-center"
                  data-name="<?= htmlspecialchars(strtolower($name), ENT_QUOTES, 'UTF-8') ?>"
                  data-category="<?= htmlspecialchars($product['category_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                  onclick="addToCart(<?= (int)$product['id'] ?>)"
                >
                <div class="product-icon-wrapper">
                  <i class="bi <?= $icon ?>" style="color: <?= $color ?>; font-size: 2.1rem;"></i>
                </div>
                <div class="product-name"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></div>
                <div class="text-success fw-bold">
                  ₱<?= number_format((float)$product['price'], 2) ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <!-- Bottom Action Buttons UI -->
      <div class="action-footer d-flex flex-wrap justify-content-center fixed-bottom shadow-lg" id="action-footer">
        <button class="action-btn" type="button"><i class="bi bi-lightning"></i> Speed Key</button>
        <button class="action-btn" type="button"><i class="bi bi-list-task"></i> Orders</button>
        <button class="action-btn" type="button"><i class="bi bi-table"></i> Table Orders</button>
        <button class="action-btn" type="button"><i class="bi bi-pause-circle"></i> Hold</button>
        <button class="action-btn" type="button"><i class="bi bi-x-octagon"></i> Void</button>
        <button class="action-btn" type="button"><i class="bi bi-x-circle"></i> No Sales</button>
        <button class="action-btn" type="button"><i class="bi bi-arrow-counterclockwise"></i> Refund</button>
        <button class="action-btn" type="button"><i class="bi bi-qr-code-scan"></i> Price Check</button>
      </div>
    </div>
  </div>
</div>
<!-- Payment Modal -->

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 750px;">
    <div class="modal-content rounded-4">
      <form id="paymentForm" autocomplete="off">
        <div class="modal-header pb-2 border-0">
          <h5 class="modal-title fw-bold">Enter Payment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body pt-0">
          <div class="row g-4 align-items-start">
            <!-- LEFT: Amount/Bills/Keypad -->
            <div class="col-12 col-md-7">
              <label class="mb-1 fw-medium">Amount Due:</label>
              <div class="display-5 text-primary fw-bold mb-2" id="payAmountDue">₱0.00</div>
              <label class="mb-1 fw-medium">Amount Paid:</label>
              <input type="text" id="payInput" name="amount_paid" class="form-control form-control-lg pay-amount-input mb-2 fw-bold text-end" value="0" readonly style="font-size:1.6rem;">
              <div class="d-flex flex-wrap gap-2 mb-3">
                <button type="button" class="btn bill-btn" onclick="incBill(50)">₱50</button>
                <button type="button" class="btn bill-btn" onclick="incBill(100)">₱100</button>
                <button type="button" class="btn bill-btn" onclick="incBill(500)">₱500</button>
                <button type="button" class="btn bill-btn" onclick="incBill(1000)">₱1000</button>
                <button type="button" class="btn btn-outline-danger bill-btn" onclick="clearPayInput()">Clear</button>
              </div>
              <div class="num-keypad-grid mt-2 mb-1">
                <!-- Number keypad buttons (see CSS below for grid!) -->
                <button type="button" class="btn num-btn" onclick="appendPay('1')">1</button>
                <button type="button" class="btn num-btn" onclick="appendPay('2')">2</button>
                <button type="button" class="btn num-btn" onclick="appendPay('3')">3</button>
                <button type="button" class="btn num-btn" onclick="appendPay('4')">4</button>
                <button type="button" class="btn num-btn" onclick="appendPay('5')">5</button>
                <button type="button" class="btn num-btn" onclick="appendPay('6')">6</button>
                <button type="button" class="btn num-btn" onclick="appendPay('7')">7</button>
                <button type="button" class="btn num-btn" onclick="appendPay('8')">8</button>
                <button type="button" class="btn num-btn" onclick="appendPay('9')">9</button>
                <button type="button" class="btn num-btn" onclick="appendPay('.')">.</button>
                <button type="button" class="btn num-btn" onclick="appendPay('0')">0</button>
                <button type="button" class="btn btn-outline-danger num-btn" onclick="backspacePay()"><i class="bi bi-backspace"></i></button>
              </div>
            </div>
            <!-- RIGHT: Payment Methods/Change -->
            <div class="col-12 col-md-5">
              <label class="mb-1 fw-medium">Payment Method:</label>
              <div class="payment-methods mb-3 d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-outline-primary method-btn" onclick="selectMethod('Cash')">Cash</button>
                <button type="button" class="btn btn-outline-primary method-btn" onclick="selectMethod('GCash')">GCash</button>
                <button type="button" class="btn btn-outline-primary method-btn" onclick="selectMethod('Credit Card')">Credit Card</button>
                <button type="button" class="btn btn-outline-primary method-btn" onclick="selectMethod('Debit Card')">Debit Card</button>
              </div>
              <input type="hidden" name="payment_method" id="paymentMethod" value="Cash">
              <label class="mb-1 fw-medium">Change:</label>
              <div class="display-4 fw-bold mb-4" id="changeAmount">₱0.00</div>
              <div class="text-muted mt-3" style="font-size:1rem;">Method: <span id="currentMethod">Cash</span></div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 justify-content-between">
          <button type="button" class="btn btn-secondary btn-lg px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success btn-lg px-4" id="payBtn" disabled>Confirm Payment</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to log out?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <a href="pos.php" class="btn btn-danger">Logout</a>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const products = <?= json_encode($products, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
let cart = {};
let lastTotalDue = 0;

// --- Cart Logic ---
function addToCart(id) {
  const product = products.find(p => p.id == id);
  if (!cart[id]) cart[id] = { ...product, qty: 1 };
  else cart[id].qty++;
  renderCart();
}
function removeFromCart(id) {
  delete cart[id];
  renderCart();
}
function changeQty(id, delta) {
  cart[id].qty += delta;
  if (cart[id].qty <= 0) removeFromCart(id);
  else renderCart();
}
function renderCart() {
  let cartItems = '', total = 0;
  for (let id in cart) {
    const item = cart[id], line = item.price * item.qty; total += line;
    cartItems += `
      <li class="list-group-item d-flex justify-content-between align-items-center cart-item">
        <div>
          <div class="fw-semibold">${item.name}</div>
          <div>
            <button class="btn btn-sm btn-outline-secondary" onclick="changeQty(${id}, -1)">−</button>
            <span class="mx-2">${item.qty}</span>
            <button class="btn btn-sm btn-outline-secondary" onclick="changeQty(${id}, 1)">+</button>
          </div>
        </div>
        <div class="text-end">
          <div>₱${(line).toFixed(2)}</div>
          <a href="#" class="text-danger small" onclick="removeFromCart(${id})">Remove</a>
        </div>
      </li>`;
  }
  document.getElementById('cart-items').innerHTML = cartItems || '<li class="list-group-item text-center text-muted">No items</li>';
  document.getElementById('cart-total').innerText = `₱${total.toFixed(2)}`;
  lastTotalDue = total;
}

function filterProducts() {
  const keyword = document.getElementById('search-input').value.toLowerCase();
  const category = document.getElementById('category-filter').value;
  document.querySelectorAll('.product-card').forEach(card => {
    const name = card.getAttribute('data-name');
    const prodCat = card.getAttribute('data-category') || '';
    // Show if matches search and category filter
    const matchName = !keyword || name.includes(keyword);
    const matchCat = !category || prodCat === category;
    card.parentElement.style.display = (matchName && matchCat) ? '' : 'none';
  });
}

// --- Payment Modal Logic ---
let payInput = "0", payMethod = "Cash";
function showPaymentModal() {
  if (Object.keys(cart).length === 0) {
    alert("Your cart is empty.");
    return;
  }
  document.getElementById('payAmountDue').textContent = '₱' + lastTotalDue.toLocaleString(undefined, {minimumFractionDigits:2});
  payInput = "0";
  updatePayDisplay();
  selectMethod('Cash');
  let modal = new bootstrap.Modal(document.getElementById('paymentModal'));
  modal.show();
}
function appendPay(num) {
  if (payInput === "0" && num !== ".") payInput = "";
  if (num === "." && payInput.includes(".")) return;
  payInput += num;
  payInput = payInput.replace(/^0+([1-9])/, "$1");
  updatePayDisplay();
}
function incBill(val) {
  let v = parseFloat(payInput) || 0;
  payInput = (v + val).toString();
  updatePayDisplay();
}
function backspacePay() {
  payInput = payInput.slice(0, -1) || "0";
  updatePayDisplay();
}
function clearPayInput() {
  payInput = "0";
  updatePayDisplay();
}
function updatePayDisplay() {
  document.getElementById('payInput').value = payInput;
  updateChange();
}

function selectMethod(method) {
  payMethod = method;
  document.getElementById('paymentMethod').value = method;
  document.getElementById('currentMethod').textContent = method;
  // Highlight active button
  document.querySelectorAll('.method-btn').forEach(btn => {
    if (btn.textContent.trim() === method) btn.classList.add('active', 'selected');
    else btn.classList.remove('active', 'selected');
  });
}

function updateChange() {
  let amtPaid = parseFloat(payInput) || 0;
  let change = Math.max(amtPaid - lastTotalDue, 0);
  document.getElementById('changeAmount').textContent = '₱' + change.toLocaleString(undefined, {minimumFractionDigits:2});
  document.getElementById('payBtn').disabled = (amtPaid < lastTotalDue);
}
document.getElementById('payInput').addEventListener('input', updateChange);

document.getElementById('paymentForm').onsubmit = function(e) {
  e.preventDefault();
  let amtPaid = parseFloat(payInput) || 0;
  if (amtPaid < lastTotalDue) {
    alert("Amount paid must be at least equal to the amount due.");
    return false;
  }
  const formData = new FormData();
  formData.append('checkout', JSON.stringify(cart));
  formData.append('payment_method', payMethod);
  formData.append('amount_paid', amtPaid);

  fetch('', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        alert("Payment received!\nSale ID: " + data.sale_id + "\nChange: ₱" + data.change.toFixed(2));
        cart = {};
        renderCart();
        bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
      } else {
        alert("Checkout failed: " + data.message);
      }
    })
    .catch(err => {
      console.error(err);
      alert("Error during checkout.");
    });
};

window.onload = renderCart;
</script>
</body>
</html>
