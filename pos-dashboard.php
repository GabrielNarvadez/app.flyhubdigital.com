<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    // Temporary fallback for demo purposes
    $_SESSION['user_id'] = 1;
    $_SESSION['name'] = 'Cashier 1';
}
require_once __DIR__ . '/layouts/config.php';

// Handle checkout POST (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'], $_POST['payment_method'], $_POST['amount_paid'])) {
    $cart = json_decode($_POST['checkout'], true);
    $payment_method = $_POST['payment_method'];
    $amount_paid = (float) $_POST['amount_paid'];

    if (!$cart || count($cart) === 0) {
        exit(json_encode(['status' => 'error', 'message' => 'Cart is empty.']));
    }
    $total = 0;
    foreach ($cart as $item) $total += $item['price'] * $item['qty'];
    if ($amount_paid < $total) exit(json_encode(['status' => 'error', 'message' => 'Insufficient payment.']));

    $stmt = $link->prepare("INSERT INTO sales (total, payment_method, amount_paid, change_given, user_id) VALUES (?, ?, ?, ?, ?)");
    $change = $amount_paid - $total;
    $user_id = $_SESSION['user_id'];
    $stmt->bind_param("dsddi", $total, $payment_method, $amount_paid, $change, $user_id);
    $stmt->execute();
    $sale_id = $stmt->insert_id;
    $stmt->close();

    $stmt = $link->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
    foreach ($cart as $item) {
        $line_total = $item['qty'] * $item['price'];
        $stmt->bind_param("iiidd", $sale_id, $item['id'], $item['qty'], $item['price'], $line_total);
        $stmt->execute();
    }
    $stmt->close();

    exit(json_encode(['status' => 'success', 'sale_id' => $sale_id, 'change' => $change]));
}

// Fetch products
$result = $link->query("SELECT id, name, price FROM products ORDER BY name ASC");
$products = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>POS Interface</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: #f9fafb; }
    .product-card {
      min-height: 220px;
      transition: transform 0.2s;
      cursor: pointer;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .product-card:hover {
      transform: scale(1.02);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .product-icon-wrapper {
      font-size: 3rem;
      color: #6c757d;
      padding: 20px 0;
    }
    .cart-item { font-size: 0.95rem; }
    .btn-outline-secondary {
      border-radius: 50%;
      padding: 0.3rem 0.5rem;
    }
    .modal-lg-custom { max-width: 600px; }
    .pay-amount-input { font-size: 2.2rem; font-weight: bold; text-align: right; }
    .num-keypad button {
      font-size: 1.7rem; width: 70px; height: 70px; margin: 8px 7px 4px 0;
    }
    .bills-row button { width: 65px; font-size: 1.1rem; margin: 0 4px 7px 0; }
    .payment-methods .btn { min-width: 108px; margin-right: 8px; margin-bottom: 8px; }
  </style>
</head>
<body>
<div class="container-fluid p-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="m-0 text-primary fw-bold">Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h4>
    <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</button>
  </div>

  <div class="row">
    <!-- Cart Left -->
    <div class="col-lg-4 mb-3 order-lg-1 order-2">
      <div class="card shadow-sm border-0 p-3" id="cart-box">
        <h5 class="mb-3 text-primary">🛒 Cart</h5>
        <ul class="list-group mb-3" id="cart-items"></ul>
        <div class="d-flex justify-content-between fw-bold fs-5">
          <span>Total:</span>
          <span id="cart-total">₱0.00</span>
        </div>
        <button class="btn btn-primary w-100 mt-3" onclick="showPaymentModal()" id="checkoutBtn">Checkout</button>
      </div>
    </div>

    <!-- Products Right -->
    <div class="col-lg-8 mb-3 order-lg-2 order-1">
      <div class="mb-3">
        <input type="text" class="form-control" id="search-input" placeholder="Search products..." oninput="filterProducts()">
      </div>
      <div class="row g-3" id="product-grid">
        <?php foreach ($products as $product): ?>
          <div class="col-6 col-sm-4 col-md-3">
            <div class="card product-card shadow-sm border-0 p-2 text-center"
                 data-name="<?= htmlspecialchars(strtolower($product['name'])) ?>"
                 onclick="addToCart(<?= $product['id'] ?>)">
              <div class="product-icon-wrapper">
                <i class="bi bi-bag"></i>
              </div>
              <div class="fw-semibold small text-truncate"><?= htmlspecialchars($product['name']) ?></div>
              <div class="text-success fw-bold">₱<?= number_format($product['price'], 2) ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg-custom">
    <div class="modal-content">
      <form id="paymentForm" autocomplete="off">
        <div class="modal-header">
          <h5 class="modal-title" id="paymentModalLabel">Enter Payment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" tabindex="-1" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <div class="row">
              <div class="col-md-6">
                <label class="mb-2">Amount Due:</label>
                <div class="display-6 text-primary fw-bold mb-2" id="payAmountDue">₱0.00</div>
                <label class="mb-2">Amount Paid:</label>
                <input type="text" id="payInput" name="amount_paid" class="form-control pay-amount-input mb-2" value="0" readonly style="background:#f4f4f4;">
                <div class="bills-row mb-3">
                  <button type="button" class="btn btn-outline-secondary" onclick="incBill(50)">₱50</button>
                  <button type="button" class="btn btn-outline-secondary" onclick="incBill(100)">₱100</button>
                  <button type="button" class="btn btn-outline-secondary" onclick="incBill(500)">₱500</button>
                  <button type="button" class="btn btn-outline-secondary" onclick="incBill(1000)">₱1000</button>
                  <button type="button" class="btn btn-outline-warning" onclick="clearPayInput()">Clear</button>
                </div>
                <div class="num-keypad d-flex flex-wrap justify-content-start">
                  <?php foreach ([1,2,3,4,5,6,7,8,9,0] as $n) {
                    echo "<button type='button' class='btn btn-outline-dark m-1' onclick='appendPay(\"$n\")'>$n</button>";
                  } ?>
                  <button type="button" class="btn btn-outline-dark m-1" onclick="appendPay('.')">.</button>
                  <button type="button" class="btn btn-outline-danger m-1" onclick="backspacePay()"><i class="bi bi-backspace"></i></button>
                </div>
              </div>
              <div class="col-md-6">
                <label class="mb-2">Payment Method:</label>
                <div class="payment-methods mb-3">
                  <button type="button" class="btn btn-outline-primary" onclick="selectMethod('Cash')">Cash</button>
                  <button type="button" class="btn btn-outline-info" onclick="selectMethod('GCash')">GCash</button>
                  <button type="button" class="btn btn-outline-success" onclick="selectMethod('Credit Card')">Credit Card</button>
                  <button type="button" class="btn btn-outline-warning" onclick="selectMethod('Debit Card')">Debit Card</button>
                </div>
                <input type="hidden" name="payment_method" id="paymentMethod" value="Cash">
                <div class="mt-4">
                  <label class="mb-2">Change:</label>
                  <div class="display-6 fw-bold" id="changeAmount">₱0.00</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer flex-column flex-sm-row justify-content-between">
          <div>
            <span class="text-muted">Method: <span id="currentMethod">Cash</span></span>
          </div>
          <div>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success btn-lg" id="payBtn" disabled>Confirm Payment</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutLabel">Confirm Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
const products = <?= json_encode($products) ?>;
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
  document.querySelectorAll('.product-card').forEach(card => {
    const name = card.getAttribute('data-name');
    card.parentElement.style.display = name.includes(keyword) ? '' : 'none';
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
