<?php
session_start();
require_once __DIR__ . '/layouts/config.php';

// 1) Fetch active, in-stock products
$products = [];
$sql = "SELECT p.id, p.name, c.name AS category, p.price, p.stock
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.status = 'active' AND p.stock > 0
        ORDER BY p.name ASC";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $name, $category, $price, $stock);
    while (mysqli_stmt_fetch($stmt)) {
        $products[] = [
            'id'       => $id,
            'name'     => $name,
            'category' => $category,
            'price'    => $price,
            'stock'    => $stock
        ];
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Flyhub POS | Attex</title>
  <?php include 'layouts/title-meta.php'; ?>
  <?php include 'layouts/head-css.php'; ?>
  <!-- Remixicon for icons -->
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <!-- ===== Custom number‐pad styling ===== -->
<style>
  /* Number‐pad buttons: bold, big, square, with border + shadow */
  .num-btn,
  .backspace-btn,
  .cancel-btn-modal {
    font-size: 1.75rem;
    font-weight: 700;
    width: 100%;             /* fill its column */
    margin-bottom: 0.5rem;   /* vertical spacing */
    padding: 0.5rem 0;       /* vertical padding */
    border: 1px solid rgba(0,0,0,0.1);
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border-radius: 4px;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
  }
</style>
  <!-- ===== End custom styling ===== -->

</head>
<body>

<?php include 'layouts/background.php'; ?>

<!-- ========= HEADER ONLY ========= -->
<header class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#">Point of Sale</a>
    <form class="d-flex mx-auto w-50">
      <div class="input-group">
        <span class="input-group-text bg-white border-end-0"><i class="ri-search-line"></i></span>
        <input type="text" class="form-control border-start-0" placeholder="Search for food, drinks...">
      </div>
    </form>
    <button class="btn btn-primary" id="logoutBtn">
      <i class="ri-logout-box-r-line me-2"></i>Logout
    </button>
  </div>
</header>
<!-- ===== END HEADER ===== -->

<!-- ===== PAGE CONTENT ===== -->
<div class="page-content py-4">
  <div class="container-fluid">

    <div class="row">
      <!-- PRODUCTS GRID -->
      <div class="col-lg-8">
        <div class="row">
          <?php if (empty($products)): ?>
            <div class="col-12">
              <div class="alert alert-warning text-center">No products available.</div>
            </div>
          <?php else: ?>
            <?php foreach ($products as $p): ?>
              <div class="col-6 col-md-4 col-lg-3 mb-4">
                <div class="card h-100 shadow-sm">
                  <div class="card-body text-center">
                    <i class="ri-restaurant-line fs-1 text-primary mb-2"></i>
                    <h5 class="card-title"><?php echo htmlspecialchars($p['name']); ?></h5>
                    <p class="text-muted small mb-2"><?php echo htmlspecialchars($p['category']); ?></p>
                    <div class="h5 text-primary mb-3">
                      ₱<?php echo number_format($p['price'], 0); ?>
                    </div>
                    <button
                      class="btn btn-outline-primary w-100 add-to-cart-btn"
                      data-product-id="<?php echo $p['id']; ?>"
                      data-name="<?php echo htmlspecialchars($p['name']); ?>"
                      data-price="<?php echo $p['price']; ?>"
                      data-stock="<?php echo $p['stock']; ?>"
                      data-attributes='<?php echo htmlspecialchars(json_encode(["Color"=>"Black","Size"=>"Standard"])); ?>'
                      <?php if ($p['price'] <= 0 || $p['stock'] <= 0) echo 'disabled'; ?>
                    >Add</button>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- CART / ORDER PANEL -->
      <div class="col-lg-4">
        <div class="card p-3 shadow-sm" id="cartPanel">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="ri-shopping-cart-2-line me-1"></i>Order</h5>
            <button class="btn btn-outline-primary btn-sm" id="addCustomerBtn">
              <i class="ri-user-add-line me-1"></i>Add Customer
            </button>
          </div>
          <ul class="list-group mb-3" id="cartItems"></ul>
          <div class="mb-3">
            <input type="text" class="form-control mb-2" id="discountInput" placeholder="Discount (%)">
            <input type="text" class="form-control"      id="orderNoteInput" placeholder="Order note">
          </div>
          <div class="mb-3">
            <div class="d-flex justify-content-between"><span>Subtotal</span><span id="subtotal">₱0</span></div>
            <div class="d-flex justify-content-between"><span>Tax</span><span id="tax">₱0</span></div>
            <hr>
            <div class="d-flex justify-content-between fw-bold"><span>Total</span><span id="total">₱0</span></div>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary flex-fill" id="holdOrderBtn">Hold Order</button>
            <button class="btn btn-success flex-fill"id="proceedBtn"data-bs-toggle="modal"data-bs-target="#checkoutModal">Checkout</button>
          </div>
        </div>
      </div>
    </div><!-- end row -->

    <!-- ===== MODALS ===== -->
    <!-- Add Customer Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1">
      <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Select/Add Customer</h5></div>
        <div class="modal-body">
          <input type="text" id="customerName" class="form-control" placeholder="Enter customer name">
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" id="saveCustomerBtn">Save</button>
        </div>
      </div></div>
    </div>

    <!-- Checkout Modal -->
<!-- ===== Checkout Modal (Attex style) ===== -->
<div class="modal fade checkout-modal-modern" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 880px;">

    <div class="modal-content">

      <!-- Header -->
      <div class="modal-header border-0">
        <h3 class="modal-title" id="checkoutModalLabel">Checkout</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Body -->
      <div class="modal-body py-4">
        <div class="row">

          <!-- Left Column -->
          <div class="col-md-6 mb-4 mb-md-0">
            <h5 class="mb-3">Total Amount: <strong id="checkoutTotal">₱0</strong></h5>
            <input
              type="number"
              id="amountReceived"
              class="form-control form-control-lg border border-warning mb-3"
              placeholder="Amount received"
            >
            <p class="mb-3">
              Current Payment Method:
              <span id="currentPaymentMethod" class="text-primary">Cash</span>
            </p>
            <div id="checkoutChange" class="mb-2"></div>
            <div id="checkoutError" class="text-danger small mb-2"></div>
          </div>

          <!-- Right Column -->
          <div class="col-md-6">

            <!-- Payment Tabs -->
            <div class="btn-group mb-3" role="group">
              <button type="button" class="btn btn-outline-primary active payment-method-btn" data-method="Cash">Cash</button>
              <button type="button" class="btn btn-outline-primary payment-method-btn" data-method="Gcash">Gcash</button>
              <button type="button" class="btn btn-outline-primary payment-method-btn" data-method="Credit Card">Credit Card</button>
              <button type="button" class="btn btn-outline-primary payment-method-btn" data-method="Debit Card">Debit Card</button>
            </div>

            <!-- Quick Add Buttons -->
            <div class="mb-4">
              <button type="button" class="btn btn-warning btn-sm rounded-pill me-2 increment-cash" data-inc="50">+₱50</button>
              <button type="button" class="btn btn-warning btn-sm rounded-pill me-2 increment-cash" data-inc="100">+₱100</button>
              <button type="button" class="btn btn-warning btn-sm rounded-pill me-2 increment-cash" data-inc="500">+₱500</button>
              <button type="button" class="btn btn-warning btn-sm rounded-pill me-2 increment-cash" data-inc="1000">+₱1000</button>
              <button type="button" class="btn btn-light btn-sm rounded-pill" id="clearAmountBtn">Clear</button>
            </div>

<!-- Number Pad -->
<div class="row g-3 mb-3">
  <div class="col-4">
    <button type="button" class="btn btn-lg btn-light num-btn" data-num="7">7</button>
  </div>
  <div class="col-4">
    <button type="button" class="btn btn-lg btn-light num-btn" data-num="8">8</button>
  </div>
  <div class="col-4">
    <button type="button" class="btn btn-lg btn-light num-btn" data-num="9">9</button>
  </div>

  <div class="col-4">
    <button type="button" class="btn btn-lg btn-light num-btn" data-num="4">4</button>
  </div>
  <div class="col-4">
    <button type="button" class="btn btn-lg btn-light num-btn" data-num="5">5</button>
  </div>
  <div class="col-4">
    <button type="button" class="btn btn-lg btn-light num-btn" data-num="6">6</button>
  </div>

  <div class="col-4">
    <button type="button" class="btn btn-lg btn-light num-btn" data-num="1">1</button>
  </div>
  <div class="col-4">
    <button type="button" class="btn btn-lg btn-light num-btn" data-num="2">2</button>
  </div>
  <div class="col-4">
    <button type="button" class="btn btn-lg btn-light num-btn" data-num="3">3</button>
  </div>

  <div class="col-4">
    <button type="button" class="btn btn-lg btn-light num-btn" data-num="0">0</button>
  </div>
  <div class="col-4">
    <button type="button" class="btn btn-lg btn-light backspace-btn">
      <i class="ri-arrow-left-line"></i>
    </button>
  </div>
  <div class="col-4">
    <button type="button" class="btn btn-danger btn-lg cancel-btn-modal" data-bs-dismiss="modal">
      Cancel
    </button>
  </div>
</div>


            <!-- Confirm Button -->
            <button type="button" class="btn btn-success btn-lg w-100" id="confirmCheckoutBtn" disabled>
              Confirm Payment
            </button>

          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- ===== End Checkout Modal ===== -->

    <!-- Alert Modal -->
    <div class="modal fade" id="alertModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered"><div class="modal-content text-center p-4">
        <i class="ri-error-warning-line fs-2 text-warning mb-2"></i>
        <div id="alertModalMsg" class="fs-5 mb-3">Please add at least one product before checkout.</div>
        <button class="btn btn-primary" data-bs-dismiss="modal">OK</button>
      </div></div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered"><div class="modal-content p-4 text-center">
        <i class="ri-logout-box-r-line fs-2 text-warning mb-2"></i>
        <h5 class="mb-3">Confirm Logout</h5>
        <div class="d-flex justify-content-center gap-2">
          <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-danger" id="confirmLogoutBtn">Logout</button>
        </div>
      </div></div>
    </div>
    <!-- ===== END MODALS ===== -->

  </div><!-- end container-fluid -->
</div><!-- end page-content -->

<?php include 'layouts/footer-scripts.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.min.js"></script>

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

// Hide modal when the red "Cancel" button is clicked
const cancelModalBtn = document.querySelector('.cancel-btn-modal');
if (cancelModalBtn) {
  cancelModalBtn.onclick = () => {
    bootstrap.Modal.getInstance(
      document.getElementById('checkoutModal')
    ).hide();
  };
}

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
