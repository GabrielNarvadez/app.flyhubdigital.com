<!-- Lot Map Legend -->
<div class="mb-3 d-flex justify-content-center gap-4">
  <span><span class="legend-dot" style="background:#4caf50"></span>Available</span>
  <span><span class="legend-dot" style="background:#e53935"></span>Sold</span>
  <span><span class="legend-dot" style="background:#ffc107"></span>Reserved</span>
</div>

<!-- Map Container -->
<div class="map-container position-relative" style="max-width:500px;margin:2rem auto;">
  <!-- SVG Map -->
  <svg id="franceMap" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 450 400" style="width:100%; height:auto;">
    <path id="lot1" class="lot-region lot-available" data-lot="1" d="M50,100 L130,80 L150,140 L110,180 L70,160 Z"></path>
    <path id="lot2" class="lot-region lot-sold" data-lot="2" d="M140,70 L200,60 L230,130 L150,140 Z"></path>
    <path id="lot3" class="lot-region lot-reserved" data-lot="3" d="M230,130 L320,110 L310,170 L230,170 Z"></path>
    <path id="lot4" class="lot-region lot-available" data-lot="4" d="M70,160 L110,180 L90,240 L60,220 Z"></path>
    <path id="lot5" class="lot-region lot-sold" data-lot="5" d="M150,140 L230,170 L200,230 L110,180 Z"></path>
  </svg>
  <!-- Tooltip -->
  <div id="mapTooltip" class="map-tooltip"></div>
</div>

<!-- Custom Styles (these can be placed in your main CSS) -->
<style>
  .map-container {
    max-width: 500px;
    margin: 2rem auto;
    position: relative;
  }
  .lot-region {
    cursor: pointer;
    transition: fill 0.2s;
    stroke: #fff;
    stroke-width: 2;
  }
  .lot-available { fill: #4caf50; }
  .lot-sold { fill: #e53935; }
  .lot-reserved { fill: #ffc107; }
  .lot-selected { filter: drop-shadow(0 0 6px #333); }
  .map-tooltip {
    position: absolute;
    pointer-events: none;
    background: #fff;
    border: 1px solid #ccc;
    padding: .5rem .8rem;
    border-radius: 8px;
    min-width: 170px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    z-index: 10;
    font-size: 0.95rem;
    display: none;
  }
  .legend-dot {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 7px;
    vertical-align: middle;
  }
</style>

<!-- Map Script -->
<script>
  // Demo lot data
  const lotData = {
    1: { owner: "Juan dela Cruz", purchaseDate: "2024-02-15", lot: "A1", block: "Block 1", status: "Available" },
    2: { owner: "Maria Santos", purchaseDate: "2023-10-10", lot: "A2", block: "Block 1", status: "Sold" },
    3: { owner: "N/A", purchaseDate: "N/A", lot: "A3", block: "Block 2", status: "Reserved" },
    4: { owner: "Pedro Lopez", purchaseDate: "2024-05-01", lot: "B1", block: "Block 2", status: "Available" },
    5: { owner: "Jose Ramos", purchaseDate: "2023-07-30", lot: "B2", block: "Block 2", status: "Sold" }
  };

  const tooltip = document.getElementById('mapTooltip');
  let selectedLot = null;

  document.querySelectorAll('.lot-region').forEach(region => {
    // Hover event
    region.addEventListener('mousemove', function(e) {
      const lotId = this.getAttribute('data-lot');
      const data = lotData[lotId];
      tooltip.innerHTML =
        `<b>Status:</b> ${data.status}<br>
         <b>Owner:</b> ${data.owner !== 'N/A' ? data.owner : '-'}<br>
         <b>Purchase Date:</b> ${data.purchaseDate !== 'N/A' ? data.purchaseDate : '-'}<br>
         <b>Lot:</b> ${data.lot}<br>
         <b>Block:</b> ${data.block}`;
      tooltip.style.display = 'block';
      // Position tooltip near cursor
      tooltip.style.left = (e.offsetX + 30) + 'px';
      tooltip.style.top = (e.offsetY + 30) + 'px';
    });

    region.addEventListener('mouseleave', function() {
      tooltip.style.display = 'none';
    });

    // Click to select
    region.addEventListener('click', function() {
      if (selectedLot) selectedLot.classList.remove('lot-selected');
      this.classList.add('lot-selected');
      selectedLot = this;
    });
  });
</script>
