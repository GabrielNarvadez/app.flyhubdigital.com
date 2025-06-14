<?php
require_once __DIR__ . '/../layouts/config.php';

// Fetch all heroes for display and for filters
$query = "SELECT hero_id, hero_name, type, role FROM mlbb_heroes ORDER BY hero_name ASC";
$result = mysqli_query($link, $query);

// Build arrays for filters
$types = [];
$all_roles = [];
$heroes = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $heroes[] = $row;
        if (!in_array($row['type'], $types)) $types[] = $row['type'];
        foreach (explode(',', $row['role']) as $r) {
            $r = trim($r);
            if ($r && !in_array($r, $all_roles)) $all_roles[] = $r;
        }
    }
}

// Helper: icon for hero type
function hero_type_icon($type) {
    switch (strtolower($type)) {
        case 'marksman': return '<i class="ri-crosshair-2-line text-warning"></i>';
        case 'mage':     return '<i class="ri-magic-line text-primary"></i>';
        case 'fighter':  return '<i class="ri-sword-line text-danger"></i>';
        case 'tank':     return '<i class="ri-shield-line text-secondary"></i>';
        case 'assassin': return '<i class="ri-ghost-line text-dark"></i>';
        case 'support':  return '<i class="ri-heart-2-line text-success"></i>';
        default:         return '<i class="ri-question-line text-muted"></i>';
    }
}
?>
<!-- Bootstrap CSS (required) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- RemixIcon CDN for icons -->
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<style>
.hero-card-inner {
    background: #f9f9fa;
    min-height: 130px;
    border-radius: .6rem;
    transition: box-shadow 0.12s;
}
.hero-card-inner:hover {
    box-shadow: 0 2px 12px 0 rgba(120, 117, 210, 0.10);
    background: #f5f8fe;
}
/* Modal 70% style */
.modal-70 {
    max-width: 70vw;
    max-height: 70vh;
    width: 70vw;
}
.modal-70 .modal-content {
    height: 70vh;
    min-height: 0;
    border-radius: 1rem;
}
@media (max-width: 991px) {
    .modal-70 {
        max-width: 98vw;
        width: 98vw;
        max-height: 90vh;
    }
    .modal-70 .modal-content {
        height: 90vh;
    }
}
/* Floating Center Button */
#openGalleryBtn {
    position: fixed;
    left: 50%;
    bottom: 32px;
    z-index: 1055;
    transform: translateX(-50%);
    border-radius: 50%;
    width: 72px;
    height: 72px;
    font-size: 2.1rem;
    background: #4636c6;
    color: #fff;
    border: none;
    box-shadow: 0 6px 24px 0 rgba(50,50,70,0.13);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .1s, box-shadow .1s;
}
#openGalleryBtn:hover, #openGalleryBtn:focus {
    background: #291fa1;
    color: #fff;
    box-shadow: 0 10px 32px 0 rgba(70,54,198,0.22);
}
@media (max-width: 576px) {
    #openGalleryBtn {
        width: 56px;
        height: 56px;
        font-size: 1.3rem;
        bottom: 16px;
    }
}
</style>

<div class="py-5">
    <!-- 3-6-3 Grid Placeholder -->
    <div class="row mb-5">
        
        <div class="col-3">
            <div class="bg-light border rounded-3 p-3">
                <div class="fw-semibold text-muted mb-3 text-center">Placeholder Left (3)</div>
                <!-- 5 cards vertically stacked -->
                <div class="d-flex flex-column gap-2">
                    <div class="card shadow-sm">
                        <div class="card-body p-2 text-center">
                            <div class="fw-bold">Slot 1</div>
                            <small class="text-muted">Subtitle</small>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-body p-2 text-center">
                            <div class="fw-bold">Slot 2</div>
                            <small class="text-muted">Subtitle</small>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-body p-2 text-center">
                            <div class="fw-bold">Slot 3</div>
                            <small class="text-muted">Subtitle</small>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-body p-2 text-center">
                            <div class="fw-bold">Slot 4</div>
                            <small class="text-muted">Subtitle</small>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-body p-2 text-center">
                            <div class="fw-bold">Slot 5</div>
                            <small class="text-muted">Subtitle</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-6">
            <div class="bg-light border rounded-3 p-5 text-center text-muted fw-semibold">Placeholder Center (6)</div>
        </div>
    

        <div class="col-3">
            <div class="bg-light border rounded-3 p-3">
                <div class="fw-semibold text-muted mb-3 text-center">Placeholder Left (3)</div>
                <!-- 5 cards vertically stacked -->
                <div class="d-flex flex-column gap-2">
                    <div class="card shadow-sm">
                        <div class="card-body p-2 text-center">
                            <div class="fw-bold">Slot 1</div>
                            <small class="text-muted">Subtitle</small>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-body p-2 text-center">
                            <div class="fw-bold">Slot 2</div>
                            <small class="text-muted">Subtitle</small>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-body p-2 text-center">
                            <div class="fw-bold">Slot 3</div>
                            <small class="text-muted">Subtitle</small>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-body p-2 text-center">
                            <div class="fw-bold">Slot 4</div>
                            <small class="text-muted">Subtitle</small>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-body p-2 text-center">
                            <div class="fw-bold">Slot 5</div>
                            <small class="text-muted">Subtitle</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<!-- Floating Button (Bottom Center) to open the gallery modal -->
<button id="openGalleryBtn" data-bs-toggle="modal" data-bs-target="#heroGalleryModal" title="Show Hero Gallery">
    <i class="ri-arrow-up-double-line"></i>
</button>

<!-- Centered, 70% Modal for Hero Gallery -->
<div class="modal fade" id="heroGalleryModal" tabindex="-1" aria-labelledby="heroGalleryLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-70">
    <div class="modal-content">
      <div class="modal-header border-0 bg-primary text-white">
        <h4 class="modal-title" id="heroGalleryLabel">Mobile Legends Hero Gallery</h4>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body py-4">
        <!-- Type Filter Tabs -->
        <ul class="nav nav-tabs mb-4" id="type-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-type="">All Types</a>
            </li>
            <?php foreach($types as $type): ?>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-type="<?= htmlspecialchars($type) ?>">
                        <?= hero_type_icon($type) ?>
                        <span class="ms-1"><?= htmlspecialchars(ucfirst($type)) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Hero Grid: 4 per row desktop, 3 per row tablet, 2 per row mobile -->
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3" id="hero-grid">
            <?php if (!empty($heroes)): ?>
                <?php foreach($heroes as $hero): ?>
                    <div class="col hero-card"
                        data-type="<?= htmlspecialchars($hero['type']) ?>"
                        data-role="<?= htmlspecialchars($hero['role']) ?>">
                        <div class="hero-card-inner d-flex flex-column align-items-center justify-content-center p-2 h-100">
                            <div style="font-size: 2.3rem; line-height:1;" class="mb-1">
                                <?= hero_type_icon($hero['type']) ?>
                            </div>
                            <div class="fw-bold mb-1 text-center" style="font-size: 1.08rem;"><?= htmlspecialchars($hero['hero_name']) ?></div>
                            <div class="text-muted small text-center" style="font-size: .88rem;"><?= htmlspecialchars($hero['role']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted">No heroes found.</div>
            <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS (required for modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Gallery modal tab filtering
    const tabs = document.querySelectorAll('#heroGalleryModal #type-tabs .nav-link');
    const cards = document.querySelectorAll('#heroGalleryModal .hero-card');
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const type = this.getAttribute('data-type');
            cards.forEach(card => {
                card.style.display = (!type || card.getAttribute('data-type') === type) ? '' : 'none';
            });
        });
    });
});
</script>
