<?php
require_once __DIR__ . '/layouts/config.php';

// Helper: days difference (exclude last night)
function getNights($in, $out) {
    $d1 = new DateTime($in);
    $d2 = new DateTime($out);
    return max(1, $d2->diff($d1)->days);
}

// Booking submission
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_now'])) {
    $room_id    = intval($_POST['room_id']);
    $check_in   = $_POST['check_in'];
    $check_out  = $_POST['check_out'];
    $adults     = intval($_POST['adults']);
    $kids       = intval($_POST['kids']);
    $fullname   = trim($_POST['fullname']);
    $email      = trim($_POST['email']);
    $phone      = trim($_POST['phone']);
    $payment_method = $_POST['payment_method'];
    $nights = getNights($check_in, $check_out);

    // Find room price
    $q = $link->prepare("SELECT price FROM rooms WHERE id=?");
    $q->bind_param("i", $room_id);
    $q->execute();
    $q->bind_result($price);
    $q->fetch(); $q->close();
    $amount_due = $price * $nights;

    // Insert customer/contact (if not yet exists)
    $contact_id = null;
    $q = $link->prepare("SELECT id FROM contacts WHERE email=? LIMIT 1");
    $q->bind_param("s", $email);
    $q->execute();
    $q->bind_result($cid);
    if ($q->fetch()) $contact_id = $cid;
    $q->close();

    if (!$contact_id) {
        $q = $link->prepare("INSERT INTO contacts (first_name, email, phone_number) VALUES (?, ?, ?)");
        $q->bind_param("sss", $fullname, $email, $phone);
        $q->execute();
        $contact_id = $q->insert_id;
        $q->close();
    }

    // Handle payment proof upload
    $proof_url = '';
    if (!empty($_FILES['payment_proof']['name'])) {
        $upload_dir = __DIR__ . '/uploads/payments/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename = 'proof_' . $room_id . '_' . time() . '.' . pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION);
        $target = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $target)) {
            $proof_url = '/uploads/payments/' . $filename;
        }
    }

    // Add booking (status = pending)
    $q = $link->prepare("INSERT INTO bookings 
        (room_id, contact_id, check_in, check_out, status, total_amount, notes, created_at)
        VALUES (?, ?, ?, ?, 'pending', ?, ?, NOW())");
    $notes = "Payment method: $payment_method";
    if ($proof_url) $notes .= " | Proof: $proof_url";
    $q->bind_param("isssds", $room_id, $contact_id, $check_in, $check_out, $amount_due, $notes);
    $q->execute();
    $q->close();
    $success = "Booking received! Our team will contact you to confirm. Reference #: ".$link->insert_id;
}

// --- Handle form submission ---
$available_rooms = [];
$form_error = '';
$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';
$adults = $_GET['adults'] ?? 1;
$kids = $_GET['kids'] ?? 0;

if (isset($_GET['check'])) {
    // Validate input
    if (!$check_in || !$check_out || ($check_in > $check_out)) {
        $form_error = "Please select a valid check-in and check-out date.";
    } else {
        // Get all rooms not booked for the given dates
        $sql = "
            SELECT r.*
            FROM rooms r
            WHERE r.status = 'available'
            AND r.id NOT IN (
                SELECT b.room_id FROM bookings b
                WHERE b.status IN ('pending','confirmed','checked_in')
                AND NOT (
                    b.check_out <= ? OR b.check_in >= ?
                )
            )
            ORDER BY r.room_number ASC
        ";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("ss", $check_in, $check_out);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $available_rooms[] = $row;
        }
        $stmt->close();
    }
} else {
    // Show all available rooms by default
    $sql = "SELECT * FROM rooms WHERE status = 'available' ORDER BY room_number ASC";
    $res = mysqli_query($link, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        $available_rooms[] = $row;
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

<div class="container py-5">
    <div class="row justify-content-center mb-4">
        <div class="col-lg-8 col-xl-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h3 class="mb-4 fw-bold">Book a Room</h3>
                    <?php if ($form_error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($form_error) ?></div>
                    <?php elseif ($success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>
                    <form method="get" class="row g-3 align-items-end" enctype="multipart/form-data">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Check In</label>
                            <input type="date" name="check_in" class="form-control" required value="<?= htmlspecialchars($check_in) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Check Out</label>
                            <input type="date" name="check_out" class="form-control" required value="<?= htmlspecialchars($check_out) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Adults</label>
                            <input type="number" name="adults" class="form-control" min="1" max="10" value="<?= htmlspecialchars($adults) ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Kids</label>
                            <input type="number" name="kids" class="form-control" min="0" max="10" value="<?= htmlspecialchars($kids) ?>">
                        </div>
                        <div class="col-md-2 d-grid">
                            <button type="submit" name="check" class="btn btn-primary">Check Availability</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Display rooms in grid -->
    <div class="row">
        <?php if (count($available_rooms) == 0): ?>
            <div class="col-12 text-center text-muted py-4">
                <i class="ri-hotel-bed-line" style="font-size: 2rem;"></i><br>
                No available rooms for selected dates.
            </div>
        <?php else: ?>
            <?php foreach ($available_rooms as $room): ?>
                <?php
                // Calculate total
                $nights = ($check_in && $check_out) ? getNights($check_in, $check_out) : 1;
                $total = $room['price'] * $nights;
                // Modal ID
                $modal_id = "bookRoomModal" . $room['id'];
                // Pick icon
                $type = strtolower($room['type']);
                $icon = "ri-hotel-bed-line";
                if (strpos($type, "suite") !== false) $icon = "ri-cup-line";
                elseif (strpos($type, "deluxe") !== false) $icon = "ri-star-line";
                elseif (strpos($type, "family") !== false) $icon = "ri-team-line";
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="<?= $icon ?>" style="font-size:2.5rem;"></i>
                            </div>
                            <h5 class="fw-bold mb-2"><?= htmlspecialchars($room['room_name'] ?: $room['room_number']) ?></h5>
                            <div class="mb-1 text-muted"><?= htmlspecialchars($room['type']) ?></div>
                            <div class="mb-2"><?= nl2br(htmlspecialchars($room['description'])) ?></div>
                            <div class="mb-2 fw-semibold">
                                ₱<?= number_format($room['price'],2) ?> / night
                            </div>
                            <?php if ($check_in && $check_out): ?>
                            <button class="btn btn-outline-success mt-2" data-bs-toggle="modal" data-bs-target="#<?= $modal_id ?>">
                                Book Now
                            </button>
                            <?php else: ?>
                            <button class="btn btn-outline-secondary mt-2" disabled>
                                Select Dates to Book
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- BOOKING MODAL -->
                <div class="modal fade" id="<?= $modal_id ?>" tabindex="-1" aria-labelledby="<?= $modal_id ?>Label" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content rounded-4">
                      <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="book_now" value="1">
                        <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                        <input type="hidden" name="check_in" value="<?= htmlspecialchars($check_in) ?>">
                        <input type="hidden" name="check_out" value="<?= htmlspecialchars($check_out) ?>">
                        <input type="hidden" name="adults" value="<?= htmlspecialchars($adults) ?>">
                        <input type="hidden" name="kids" value="<?= htmlspecialchars($kids) ?>">
                        <div class="modal-header">
                          <h5 class="modal-title fw-bold" id="<?= $modal_id ?>Label">Confirm Booking</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <div class="mb-2">
                            <strong>Room:</strong> <?= htmlspecialchars($room['room_name'] ?: $room['room_number']) ?><br>
                            <strong>Type:</strong> <?= htmlspecialchars($room['type']) ?><br>
                            <strong>Check In:</strong> <?= htmlspecialchars($check_in) ?><br>
                            <strong>Check Out:</strong> <?= htmlspecialchars($check_out) ?><br>
                            <strong>Nights:</strong> <?= $nights ?><br>
                            <strong>Adults:</strong> <?= htmlspecialchars($adults) ?>,
                            <strong>Kids:</strong> <?= htmlspecialchars($kids) ?>
                          </div>
                          <div class="mb-3">
                            <span class="fw-semibold">Total Amount Due:</span>
                            <span class="text-success fw-bold">₱<?= number_format($total, 2) ?></span>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="fullname" class="form-control" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Payment Method</label>
                            <select name="payment_method" class="form-select" required>
                              <option value="Cash">Cash on Check-In</option>
                              <option value="GCash">GCash</option>
                              <option value="Bank Transfer">Bank Transfer</option>
                            </select>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Upload Payment Proof (optional)</label>
                            <input type="file" name="payment_proof" class="form-control" accept="image/*,application/pdf">
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" class="btn btn-primary">Checkout & Book</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
