<?php
require_once __DIR__ . '/layouts/config.php';

// --- Handle room CRUD ---
$room_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_room'])) {
    $sql = "INSERT INTO rooms (room_number, room_name, type, description, price, status)
            VALUES (?, ?, ?, ?, ?, 'available')";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("ssssd",
        $_POST['room_number'], $_POST['room_name'], $_POST['type'], $_POST['description'], $_POST['price']);
    $stmt->execute();
    $stmt->close();
    $room_msg = "Room added!";
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_room'])) {
    $sql = "UPDATE rooms SET room_number=?, room_name=?, type=?, description=?, price=?, status=? WHERE id=?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("ssssdsi",
        $_POST['room_number'], $_POST['room_name'], $_POST['type'], $_POST['description'], $_POST['price'], $_POST['status'], $_POST['room_id']);
    $stmt->execute();
    $stmt->close();
    $room_msg = "Room updated!";
}
if (isset($_GET['delete_room'])) {
    $room_id = intval($_GET['delete_room']);
    $link->query("DELETE FROM rooms WHERE id = $room_id");
    $room_msg = "Room deleted!";
}

// --- Handle booking status update ---
$booking_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $sql = "UPDATE bookings SET status=?, notes=? WHERE id=?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("ssi", $_POST['status'], $_POST['notes'], $_POST['booking_id']);
    $stmt->execute();
    $stmt->close();
    $booking_msg = "Booking updated!";
}

// --- Room form editing ---
$edit_room = null;
if (isset($_GET['edit_room'])) {
    $room_id = intval($_GET['edit_room']);
    $q = mysqli_query($link, "SELECT * FROM rooms WHERE id = $room_id");
    if ($q && $row = mysqli_fetch_assoc($q)) {
        $edit_room = $row;
    }
}

// --- Fetch rooms ---
$res_rooms = mysqli_query($link, "SELECT * FROM rooms ORDER BY room_number ASC");

// --- BOOKINGS FILTERING ---
$filter_date = $_GET['filter_date'] ?? 'all';
$filter_status = $_GET['filter_status'] ?? '';

$date_sql = "";
if ($filter_date == 'today') {
    $date_sql = "AND DATE(b.created_at) = CURDATE()";
} elseif ($filter_date == 'week') {
    $date_sql = "AND YEARWEEK(b.created_at, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($filter_date == 'month') {
    $date_sql = "AND YEAR(b.created_at) = YEAR(CURDATE()) AND MONTH(b.created_at) = MONTH(CURDATE())";
} elseif ($filter_date == 'year') {
    $date_sql = "AND YEAR(b.created_at) = YEAR(CURDATE())";
}

$status_sql = "";
if ($filter_status && in_array($filter_status, ['pending','confirmed','checked_in','checked_out','cancelled'])) {
    $status_sql = "AND b.status = '".mysqli_real_escape_string($link, $filter_status)."'";
}

$sql = "SELECT b.*, r.room_name, r.room_number, c.first_name, c.last_name
        FROM bookings b
        JOIN rooms r ON r.id = b.room_id
        JOIN contacts c ON c.id = b.contact_id
        WHERE 1 $date_sql $status_sql
        ORDER BY b.created_at DESC";
$res_bookings = mysqli_query($link, $sql);
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

<div class="container-fluid py-5">
  <div class="row g-4">
    <!-- LEFT: ROOMS FORM + LIST -->
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-4">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><?= $edit_room ? 'Edit Room' : 'Add Room' ?></h4>
            <?php if ($room_msg): ?><div class="alert alert-info py-1 px-3 mb-0"><?= $room_msg ?></div><?php endif; ?>
          </div>
          <form method="post">
            <?php if ($edit_room): ?>
              <input type="hidden" name="edit_room" value="1">
              <input type="hidden" name="room_id" value="<?= $edit_room['id'] ?>">
            <?php else: ?>
              <input type="hidden" name="add_room" value="1">
            <?php endif; ?>
            <div class="mb-2">
              <label class="form-label">Room Number</label>
              <input type="text" name="room_number" class="form-control" required value="<?= htmlspecialchars($edit_room['room_number'] ?? '') ?>">
            </div>
            <div class="mb-2">
              <label class="form-label">Room Name</label>
              <input type="text" name="room_name" class="form-control" value="<?= htmlspecialchars($edit_room['room_name'] ?? '') ?>">
            </div>
            <div class="mb-2">
              <label class="form-label">Type</label>
              <input type="text" name="type" class="form-control" required value="<?= htmlspecialchars($edit_room['type'] ?? '') ?>">
            </div>
            <div class="mb-2">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control"><?= htmlspecialchars($edit_room['description'] ?? '') ?></textarea>
            </div>
            <div class="mb-2">
              <label class="form-label">Price (per night)</label>
              <input type="number" step="0.01" min="0" name="price" class="form-control" required value="<?= htmlspecialchars($edit_room['price'] ?? '') ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="available" <?= (isset($edit_room['status']) && $edit_room['status'] == 'available') ? 'selected' : '' ?>>Available</option>
                <option value="maintenance" <?= (isset($edit_room['status']) && $edit_room['status'] == 'maintenance') ? 'selected' : '' ?>>Maintenance</option>
                <option value="out_of_service" <?= (isset($edit_room['status']) && $edit_room['status'] == 'out_of_service') ? 'selected' : '' ?>>Out of Service</option>
              </select>
            </div>
            <div class="text-end">
              <button type="submit" class="btn btn-primary"><?= $edit_room ? 'Update Room' : 'Add Room' ?></button>
              <?php if ($edit_room): ?>
                <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-secondary">Cancel</a>
              <?php endif; ?>
            </div>
          </form>
        </div>
      </div>
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3">Rooms</h5>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Price</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php if ($res_rooms && mysqli_num_rows($res_rooms) > 0):
                  while ($r = mysqli_fetch_assoc($res_rooms)): ?>
                  <tr>
                    <td><?= htmlspecialchars($r['room_number']) ?></td>
                    <td><?= htmlspecialchars($r['room_name']) ?></td>
                    <td><?= htmlspecialchars($r['type']) ?></td>
                    <td>
                      <span class="badge bg-<?= 
                        $r['status'] == 'available' ? 'success' :
                        ($r['status'] == 'maintenance' ? 'warning' : 'danger')
                      ?>">
                        <?= ucwords(str_replace('_', ' ', $r['status'])) ?>
                      </span>
                    </td>
                    <td>₱<?= number_format($r['price'],2) ?></td>
                    <td>
                      <a href="<?= $_SERVER['PHP_SELF'] ?>?edit_room=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="ri-edit-line"></i></a>
                      <a href="<?= $_SERVER['PHP_SELF'] ?>?delete_room=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this room?');"><i class="ri-delete-bin-2-line"></i></a>
                    </td>
                  </tr>
                <?php endwhile; else: ?>
                  <tr>
                    <td colspan="6" class="text-center text-muted">No rooms found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- RIGHT: BOOKINGS TABLE -->
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-4">
          <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0">Bookings</h4>
            <?php if ($booking_msg): ?><div class="alert alert-success py-1 px-3 mb-0"><?= $booking_msg ?></div><?php endif; ?>
            <div class="d-flex gap-2">
              <!-- Date Filter -->
              <form class="d-flex align-items-center gap-1" method="get" id="bookingFiltersForm">
                <select name="filter_date" class="form-select form-select-sm">
                  <option value="all" <?= ($filter_date == 'all') ? 'selected' : '' ?>>All Dates</option>
                  <option value="today" <?= ($filter_date == 'today') ? 'selected' : '' ?>>Today</option>
                  <option value="week" <?= ($filter_date == 'week') ? 'selected' : '' ?>>This Week</option>
                  <option value="month" <?= ($filter_date == 'month') ? 'selected' : '' ?>>This Month</option>
                  <option value="year" <?= ($filter_date == 'year') ? 'selected' : '' ?>>This Year</option>
                </select>
                <select name="filter_status" class="form-select form-select-sm">
                  <option value="">All Status</option>
                  <?php foreach(['pending','confirmed','checked_in','checked_out','cancelled'] as $s): ?>
                  <option value="<?= $s ?>" <?= ($filter_status == $s) ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-sm btn-outline-primary">Apply</button>
              </form>
              <button class="btn btn-sm btn-outline-secondary" onclick="window.print()"><i class="ri-printer-line"></i> Print</button>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Date</th>
                  <th>Customer</th>
                  <th>Room</th>
                  <th>Amount Due</th>
                  <th>Remarks</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($res_bookings && mysqli_num_rows($res_bookings) > 0):
                  while ($b = mysqli_fetch_assoc($res_bookings)): ?>
                  <tr>
                    <td><?= htmlspecialchars($b['created_at']) ?></td>
                    <td><?= htmlspecialchars($b['first_name'] . ' ' . $b['last_name']) ?></td>
                    <td><?= htmlspecialchars($b['room_name'] ?: $b['room_number']) ?></td>
                    <td>₱<?= number_format($b['total_amount'], 2) ?></td>
                    <td><?= htmlspecialchars($b['notes']) ?></td>
                    <td>
                      <span class="badge bg-<?= 
                        $b['status'] == 'confirmed' ? 'success' :
                        ($b['status'] == 'cancelled' ? 'danger' :
                        ($b['status'] == 'checked_in' ? 'info' :
                        ($b['status'] == 'checked_out' ? 'secondary' : 'warning')))
                      ?>">
                        <?= ucfirst($b['status']) ?>
                      </span>
                    </td>
                    <td>
                      <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#statusModal<?= $b['id'] ?>">
                        <i class="ri-edit-line"></i>
                      </button>
                    </td>
                  </tr>
                  <!-- Booking Status Modal -->
                  <div class="modal fade" id="statusModal<?= $b['id'] ?>" tabindex="-1" aria-labelledby="statusModalLabel<?= $b['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content rounded-4">
                        <form method="post">
                          <input type="hidden" name="update_status" value="1">
                          <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                          <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="statusModalLabel<?= $b['id'] ?>">Update Booking Status</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                            <div class="mb-3">
                              <label class="form-label">Status</label>
                              <select name="status" class="form-select" required>
                                <?php
                                $statuses = ['pending','confirmed','checked_in','checked_out','cancelled'];
                                foreach ($statuses as $status) {
                                  $sel = ($b['status'] == $status) ? 'selected' : '';
                                  echo "<option value='$status' $sel>".ucfirst($status)."</option>";
                                }
                                ?>
                              </select>
                            </div>
                            <div class="mb-3">
                              <label class="form-label">Remarks</label>
                              <textarea name="notes" class="form-control"><?= htmlspecialchars($b['notes']) ?></textarea>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                <?php endwhile; else: ?>
                  <tr>
                    <td colspan="7" class="text-center text-muted">No bookings found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
