<?php
require_once __DIR__ . '/../../layouts/config.php';

// Handle booking submission
if (isset($_POST['confirm_booking'])) {
    $room_id     = $_POST['room_id'];
    $checkin     = $_POST['checkin'];
    $checkout    = $_POST['checkout'];
    $adults      = $_POST['adults'];
    $children    = $_POST['children'];
    $guest_name  = $_POST['guest_name'];
    $guest_email = $_POST['guest_email'];
    $guest_phone = $_POST['guest_phone'];
    $status      = 'pending';

    $stmt = $link->prepare(
        "INSERT INTO bookings (room_id, guest_name, guest_email, guest_phone, checkin_date, checkout_date, adults, children, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "isssssiss",
        $room_id, $guest_name, $guest_email, $guest_phone, $checkin, $checkout, $adults, $children, $status
    );
    if ($stmt->execute()) {
        $booking_message = '<div class="alert alert-success mb-4">Booking submitted! We will contact you soon.</div>';
    } else {
        $booking_message = '<div class="alert alert-danger mb-4">Error saving booking. Please try again.</div>';
    }
    $stmt->close();
} else {
    $booking_message = '';
}

// Handle booking form input for availability
$checkin  = isset($_GET['checkin'])  ? $_GET['checkin']  : null;
$checkout = isset($_GET['checkout']) ? $_GET['checkout'] : null;
$adults   = isset($_GET['adults'])   ? (int)$_GET['adults'] : 1;
$children = isset($_GET['children']) ? (int)$_GET['children'] : 0;

// If form submitted, filter available rooms
if ($checkin && $checkout) {
    $sql = "
        SELECT * FROM rooms
        WHERE capacity >= ?
          AND status = 'available'
          AND id NOT IN (
              SELECT room_id FROM bookings
              WHERE status IN ('pending', 'confirmed', 'checked_in')
                AND (
                    (checkin_date < ? AND checkout_date > ?)   -- Overlap
                    OR (checkin_date >= ? AND checkin_date < ?)
                    OR (checkout_date > ? AND checkout_date <= ?)
                )
          )
        ORDER BY price ASC
    ";
    $stmt = $link->prepare($sql);
    $totalGuests = $adults + $children;
    $stmt->bind_param("issssss", $totalGuests, $checkout, $checkin, $checkin, $checkout, $checkin, $checkout);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Default: show all rooms
    $sql = "SELECT * FROM rooms";
    $result = $link->query($sql);
}
?>

<!-- BOOTSTRAP & FLATPICKR CDN (if not globally loaded) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Booking Widget -->
<div class="my-4">
  <?= $booking_message ?>
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-success text-white fw-semibold">
      Room Booking
    </div>
    <div class="card-body">
      <form id="booking-form" method="get" action="">
        <div class="row g-3 align-items-end">
          <div class="col-md-3">
            <label for="checkin" class="form-label fw-semibold">Check-in Date</label>
            <input type="text" id="checkin" name="checkin" class="form-control" placeholder="Select date" required autocomplete="off"
              value="<?= isset($_GET['checkin']) ? htmlspecialchars($_GET['checkin']) : '' ?>">
          </div>
          <div class="col-md-3">
            <label for="checkout" class="form-label fw-semibold">Check-out Date</label>
            <input type="text" id="checkout" name="checkout" class="form-control" placeholder="Select date" required autocomplete="off"
              value="<?= isset($_GET['checkout']) ? htmlspecialchars($_GET['checkout']) : '' ?>">
          </div>
          <div class="col-md-2">
            <label for="adults" class="form-label fw-semibold">Adults</label>
            <input type="number" id="adults" name="adults" class="form-control" min="1" max="10" value="<?= isset($_GET['adults']) ? htmlspecialchars($_GET['adults']) : 1 ?>" required>
          </div>
          <div class="col-md-2">
            <label for="children" class="form-label fw-semibold">Children</label>
            <input type="number" id="children" name="children" class="form-control" min="0" max="10" value="<?= isset($_GET['children']) ? htmlspecialchars($_GET['children']) : 0 ?>">
          </div>
          <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-primary btn-lg">Check Availability</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Rooms Table -->
  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white fw-semibold">
      Rooms List
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-light">
            <tr>
              <th>Room Number</th>
              <th>Property Name</th>
              <th>Type</th>
              <th>Description</th>
              <th>Status</th>
              <th>Price</th>
              <th>Floor</th>
              <th>Capacity</th>
              <?php if ($checkin && $checkout): ?>
                <th>Action</th>
              <?php endif; ?>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($row['room_number']) ?></td>
                  <td><?= htmlspecialchars($row['property_name']) ?></td>
                  <td><?= htmlspecialchars($row['type']) ?></td>
                  <td><?= htmlspecialchars($row['description']) ?></td>
                  <td>
                    <span class="badge 
                      <?= $row['status'] == 'available' ? 'bg-success' : 
                           ($row['status'] == 'occupied' ? 'bg-secondary' : 
                           ($row['status'] == 'reserved' ? 'bg-warning text-dark' : 'bg-danger')) ?>">
                      <?= ucfirst($row['status']) ?>
                    </span>
                  </td>
                  <td>₱<?= number_format($row['price'], 2) ?></td>
                  <td><?= htmlspecialchars($row['floor']) ?></td>
                  <td><?= htmlspecialchars($row['capacity']) ?></td>
                  <?php if ($checkin && $checkout && $row['status'] == 'available'): ?>
                    <td>
                      <button class="btn btn-success btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#bookRoomModal"
                        data-room-id="<?= $row['id'] ?>"
                        data-room="<?= htmlspecialchars($row['room_number']) ?>"
                        data-property="<?= htmlspecialchars($row['property_name']) ?>"
                        data-checkin="<?= htmlspecialchars($checkin) ?>"
                        data-checkout="<?= htmlspecialchars($checkout) ?>"
                        data-adults="<?= htmlspecialchars($adults) ?>"
                        data-children="<?= htmlspecialchars($children) ?>">
                        Book
                      </button>
                    </td>
                  <?php elseif ($checkin && $checkout): ?>
                    <td></td>
                  <?php endif; ?>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="<?= $checkin && $checkout ? 9 : 8 ?>" class="text-center">No rooms found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Booking Modal -->
<div class="modal fade" id="bookRoomModal" tabindex="-1" aria-labelledby="bookRoomModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" id="confirm-booking-form">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="bookRoomModalLabel">Book Room</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="room_id" id="modal-room-id">
          <input type="hidden" name="checkin" id="modal-checkin">
          <input type="hidden" name="checkout" id="modal-checkout">
          <input type="hidden" name="adults" id="modal-adults">
          <input type="hidden" name="children" id="modal-children">

          <div class="mb-2">
            <strong id="modal-room-label"></strong><br>
            <span id="modal-dates"></span>
          </div>
          <div class="mb-3">
            <label class="form-label">Guest Name</label>
            <input type="text" class="form-control" name="guest_name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email (optional)</label>
            <input type="email" class="form-control" name="guest_email">
          </div>
          <div class="mb-3">
            <label class="form-label">Phone (optional)</label>
            <input type="text" class="form-control" name="guest_phone">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="confirm_booking" class="btn btn-success">Confirm Booking</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  // Datepickers
  flatpickr("#checkin", {
    dateFormat: "Y-m-d",
    minDate: "today",
    onChange: function(selectedDates, dateStr, instance) {
      flatpickr("#checkout", {
        dateFormat: "Y-m-d",
        minDate: dateStr
      });
    }
  });
  flatpickr("#checkout", {
    dateFormat: "Y-m-d",
    minDate: "today"
  });

  // Modal booking fill
  var bookRoomModal = document.getElementById('bookRoomModal');
  bookRoomModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    document.getElementById('modal-room-id').value = button.getAttribute('data-room-id');
    document.getElementById('modal-room-label').innerText =
      'Room: ' + button.getAttribute('data-room') + ' - ' + button.getAttribute('data-property');
    document.getElementById('modal-checkin').value = button.getAttribute('data-checkin');
    document.getElementById('modal-checkout').value = button.getAttribute('data-checkout');
    document.getElementById('modal-dates').innerText =
      'Check-in: ' + button.getAttribute('data-checkin') +
      ' | Check-out: ' + button.getAttribute('data-checkout') +
      ' | Adults: ' + button.getAttribute('data-adults') +
      ' | Children: ' + button.getAttribute('data-children');
    document.getElementById('modal-adults').value = button.getAttribute('data-adults');
    document.getElementById('modal-children').value = button.getAttribute('data-children');
  });
</script>

<?php $link->close(); ?>
