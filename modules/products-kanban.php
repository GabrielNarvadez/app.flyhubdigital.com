<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container-fluid my-4">
  <div class="row">
    <!-- Filters Sidebar (Left) -->
    <div class="col-lg-2 mb-4">
      <div class="card shadow-sm">
        <div class="card-header bg-light fw-semibold">Filters</div>
        <div class="card-body">
          <form id="filterForm">
            <div class="mb-3">
              <label class="form-label">Project Name</label>
              <select class="form-select" name="project" id="filterProject">
                <option value="">All</option>
                <option value="Sunrise Estates">Sunrise Estates</option>
                <option value="Green Meadows">Green Meadows</option>
                <option value="Maple Residences">Maple Residences</option>
                <option value="Palm Gardens">Palm Gardens</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Lot Size (sqm)</label>
              <input type="number" class="form-control mb-1" placeholder="Min" name="minSize" id="filterMinSize">
              <input type="number" class="form-control" placeholder="Max" name="maxSize" id="filterMaxSize">
            </div>
            <div class="mb-3">
              <label class="form-label">Price (₱)</label>
              <input type="range" class="form-range" min="500000" max="6000000" step="50000" id="filterPrice" value="6000000">
              <div class="d-flex justify-content-between small">
                <span>₱500K</span>
                <span id="filterPriceValue" class="fw-bold">₱6,000,000</span>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Type of Lot</label>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="type" value="Inner" id="typeInner">
                <label class="form-check-label" for="typeInner">Inner</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="type" value="Corner" id="typeCorner">
                <label class="form-check-label" for="typeCorner">Corner</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="type" value="Premium" id="typePremium">
                <label class="form-check-label" for="typePremium">Premium</label>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Status</label>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="status" value="Available" id="statusAvailable">
                <label class="form-check-label" for="statusAvailable">Available</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="status" value="Reserved" id="statusReserved">
                <label class="form-check-label" for="statusReserved">Reserved</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="status" value="Sold" id="statusSold">
                <label class="form-check-label" for="statusSold">Sold</label>
              </div>
            </div>
            <button type="button" class="btn btn-primary w-100" onclick="applyFilters()">Apply Filters</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Main Product Grid (Right) -->
    <div class="col-lg-10">
      <div id="productGrid" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-6 g-3">

        <!-- Product Card Examples (data attributes used for filtering and details) -->

        <!-- Example 1 -->
        <div class="col product-card"
          data-project="Sunrise Estates"
          data-size="200"
          data-price="2500000"
          data-type="Corner"
          data-status="Available"
          data-title="Sunrise Estates - Lot 1"
          data-description="Spacious corner lot perfect for a large home or garden."
          data-img="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=600&q=80"
        >
          <div class="card h-100 shadow-sm">
            <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=600&q=80"
                 class="card-img-top" style="object-fit:cover;height:130px;">
            <div class="card-body p-2">
              <div class="fw-bold small">Sunrise Estates - Lot 1</div>
              <div class="mb-1 text-secondary small">Corner &bull; 200 sqm</div>
              <div class="mb-1 fw-bold">₱2,500,000</div>
              <span class="badge bg-success">Available</span>
            </div>
            <div class="card-footer bg-light py-2">
              <button class="btn btn-outline-primary btn-sm w-100" onclick="showDetails(this)">View Details</button>
            </div>
          </div>
        </div>

        <!-- Example 2 -->
        <div class="col product-card"
          data-project="Sunrise Estates"
          data-size="180"
          data-price="2000000"
          data-type="Inner"
          data-status="Reserved"
          data-title="Sunrise Estates - Lot 2"
          data-description="Cozy inner lot, ideal for starter homes. Near main entrance."
          data-img="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=600&q=80"
        >
          <div class="card h-100 shadow-sm">
            <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=600&q=80"
                 class="card-img-top" style="object-fit:cover;height:130px;">
            <div class="card-body p-2">
              <div class="fw-bold small">Sunrise Estates - Lot 2</div>
              <div class="mb-1 text-secondary small">Inner &bull; 180 sqm</div>
              <div class="mb-1 fw-bold">₱2,000,000</div>
              <span class="badge bg-warning text-dark">Reserved</span>
            </div>
            <div class="card-footer bg-light py-2">
              <button class="btn btn-outline-primary btn-sm w-100" onclick="showDetails(this)">View Details</button>
            </div>
          </div>
        </div>

        <!-- Example 3 -->
        <div class="col product-card"
          data-project="Green Meadows"
          data-size="250"
          data-price="3300000"
          data-type="Premium"
          data-status="Sold"
          data-title="Green Meadows - Premium Lot"
          data-description="A premium lot with lush surroundings and best community amenities."
          data-img="https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?auto=format&fit=crop&w=600&q=80"
        >
          <div class="card h-100 shadow-sm">
            <img src="https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?auto=format&fit=crop&w=600&q=80"
                 class="card-img-top" style="object-fit:cover;height:130px;">
            <div class="card-body p-2">
              <div class="fw-bold small">Green Meadows - Premium Lot</div>
              <div class="mb-1 text-secondary small">Premium &bull; 250 sqm</div>
              <div class="mb-1 fw-bold">₱3,300,000</div>
              <span class="badge bg-secondary">Sold</span>
            </div>
            <div class="card-footer bg-light py-2">
              <button class="btn btn-outline-primary btn-sm w-100" onclick="showDetails(this)">View Details</button>
            </div>
          </div>
        </div>

        <!-- Example 4 -->
        <div class="col product-card"
          data-project="Maple Residences"
          data-size="300"
          data-price="4100000"
          data-type="Premium"
          data-status="Available"
          data-title="Maple Residences - Lot 5"
          data-description="Large premium lot, best for upscale custom houses."
          data-img="https://images.unsplash.com/photo-1523217582562-09d0def993a6?auto=format&fit=crop&w=600&q=80"
        >
          <div class="card h-100 shadow-sm">
            <img src="https://images.unsplash.com/photo-1523217582562-09d0def993a6?auto=format&fit=crop&w=600&q=80"
                 class="card-img-top" style="object-fit:cover;height:130px;">
            <div class="card-body p-2">
              <div class="fw-bold small">Maple Residences - Lot 5</div>
              <div class="mb-1 text-secondary small">Premium &bull; 300 sqm</div>
              <div class="mb-1 fw-bold">₱4,100,000</div>
              <span class="badge bg-success">Available</span>
            </div>
            <div class="card-footer bg-light py-2">
              <button class="btn btn-outline-primary btn-sm w-100" onclick="showDetails(this)">View Details</button>
            </div>
          </div>
        </div>

        <!-- Example 5 -->
        <div class="col product-card"
          data-project="Palm Gardens"
          data-size="220"
          data-price="3200000"
          data-type="Corner"
          data-status="Reserved"
          data-title="Palm Gardens - Lot 8"
          data-description="Corner lot near the clubhouse and pool. Great for families."
          data-img="https://images.unsplash.com/photo-1472224371017-08207f84aaae?auto=format&fit=crop&w=600&q=80"
        >
          <div class="card h-100 shadow-sm">
            <img src="https://images.unsplash.com/photo-1472224371017-08207f84aaae?auto=format&fit=crop&w=600&q=80"
                 class="card-img-top" style="object-fit:cover;height:130px;">
            <div class="card-body p-2">
              <div class="fw-bold small">Palm Gardens - Lot 8</div>
              <div class="mb-1 text-secondary small">Corner &bull; 220 sqm</div>
              <div class="mb-1 fw-bold">₱3,200,000</div>
              <span class="badge bg-warning text-dark">Reserved</span>
            </div>
            <div class="card-footer bg-light py-2">
              <button class="btn btn-outline-primary btn-sm w-100" onclick="showDetails(this)">View Details</button>
            </div>
          </div>
        </div>

        <!-- Example 6 -->
        <div class="col product-card"
          data-project="Palm Gardens"
          data-size="150"
          data-price="1200000"
          data-type="Inner"
          data-status="Available"
          data-title="Palm Gardens - Lot 10"
          data-description="Affordable inner lot, perfect for compact house design."
          data-img="https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=600&q=80"
        >
          <div class="card h-100 shadow-sm">
            <img src="https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=600&q=80"
                 class="card-img-top" style="object-fit:cover;height:130px;">
            <div class="card-body p-2">
              <div class="fw-bold small">Palm Gardens - Lot 10</div>
              <div class="mb-1 text-secondary small">Inner &bull; 150 sqm</div>
              <div class="mb-1 fw-bold">₱1,200,000</div>
              <span class="badge bg-success">Available</span>
            </div>
            <div class="card-footer bg-light py-2">
              <button class="btn btn-outline-primary btn-sm w-100" onclick="showDetails(this)">View Details</button>
            </div>
          </div>
        </div>

        <!-- You can copy and adapt these blocks for more sample lots. -->
      </div>
    </div>
  </div>
</div>

<!-- Offcanvas Side Modal for Property Details -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="propertyDetailsOffcanvas" aria-labelledby="propertyDetailsLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="propertyDetailsLabel">Property Details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <img id="detailsImg" src="" alt="" class="w-100 rounded mb-3" style="max-height:180px;object-fit:cover;">
    <h5 id="detailsTitle"></h5>
    <div id="detailsDesc" class="mb-3"></div>
    <ul class="list-group list-group-flush mb-2">
      <li class="list-group-item"><b>Project:</b> <span id="detailsProject"></span></li>
      <li class="list-group-item"><b>Lot Size:</b> <span id="detailsSize"></span> sqm</li>
      <li class="list-group-item"><b>Type:</b> <span id="detailsType"></span></li>
      <li class="list-group-item"><b>Status:</b> <span id="detailsStatus"></span></li>
      <li class="list-group-item"><b>Price:</b> <span id="detailsPrice"></span></li>
    </ul>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const priceSlider = document.getElementById('filterPrice');
const priceValue = document.getElementById('filterPriceValue');
if (priceSlider) {
  priceSlider.addEventListener('input', function() {
    priceValue.textContent = '₱' + Number(priceSlider.value).toLocaleString();
  });
}

// Filtering Function
function applyFilters() {
  const project = document.getElementById('filterProject').value;
  const minSize = parseInt(document.getElementById('filterMinSize').value) || 0;
  const maxSize = parseInt(document.getElementById('filterMaxSize').value) || 10000;
  const maxPrice = parseInt(document.getElementById('filterPrice').value) || 6000000;

  // Collect checked types
  let types = [];
  ['typeInner', 'typeCorner', 'typePremium'].forEach(id => {
    if(document.getElementById(id).checked) types.push(document.getElementById(id).nextElementSibling.textContent);
  });
  // Collect checked status
  let statuses = [];
  ['statusAvailable', 'statusReserved', 'statusSold'].forEach(id => {
    if(document.getElementById(id).checked) statuses.push(document.getElementById(id).nextElementSibling.textContent);
  });

  document.querySelectorAll('.product-card').forEach(card => {
    const cardProject = card.getAttribute('data-project');
    const cardSize = parseInt(card.getAttribute('data-size'));
    const cardPrice = parseInt(card.getAttribute('data-price'));
    const cardType = card.getAttribute('data-type');
    const cardStatus = card.getAttribute('data-status');

    let show = true;
    if (project && cardProject !== project) show = false;
    if (cardSize < minSize || cardSize > maxSize) show = false;
    if (cardPrice > maxPrice) show = false;
    if (types.length && !types.includes(cardType)) show = false;
    if (statuses.length && !statuses.includes(cardStatus)) show = false;

    card.style.display = show ? '' : 'none';
  });
}

// Side Modal Show Details
function showDetails(btn) {
  const card = btn.closest('.product-card');
  document.getElementById('detailsImg').src = card.getAttribute('data-img');
  document.getElementById('detailsTitle').textContent = card.getAttribute('data-title');
  document.getElementById('detailsDesc').textContent = card.getAttribute('data-description');
  document.getElementById('detailsProject').textContent = card.getAttribute('data-project');
  document.getElementById('detailsSize').textContent = card.getAttribute('data-size');
  document.getElementById('detailsType').textContent = card.getAttribute('data-type');
  document.getElementById('detailsStatus').textContent = card.getAttribute('data-status');
  document.getElementById('detailsPrice').textContent = '₱' + Number(card.getAttribute('data-price')).toLocaleString();

  const offcanvas = new bootstrap.Offcanvas(document.getElementById('propertyDetailsOffcanvas'));
  offcanvas.show();
}

// Apply filters on load and on each change
document.getElementById('filterForm').addEventListener('change', applyFilters);
applyFilters();
</script>
