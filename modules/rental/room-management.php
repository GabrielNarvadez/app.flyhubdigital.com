

<!-- Bootstrap CSS/JS CDN if not loaded globally -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<div class="my-4">
  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white fw-semibold d-flex justify-content-between align-items-center">
      <span>Rooms Management</span>
      <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addEditRoomModal" onclick="openAddRoomModal()">+ Add Room</button>
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
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rooms as $room): ?>
              <tr>
                <td>
                  <a href="#"
                    class="fw-bold text-decoration-underline text-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#addEditRoomModal"
                    onclick='openEditRoomModal(<?= json_encode($room) ?>)'
                  >
                    <?= htmlspecialchars($room['room_number']) ?>
                  </a>
                </td>
                <td><?= htmlspecialchars($room['property_name']) ?></td>
                <td><?= htmlspecialchars($room['type']) ?></td>
                <td><?= htmlspecialchars($room['description']) ?></td>
                <td>
                  <span class="badge 
                    <?= $room['status'] == 'available' ? 'bg-success' : 
                         ($room['status'] == 'occupied' ? 'bg-secondary' : 
                         ($room['status'] == 'reserved' ? 'bg-warning text-dark' : 'bg-danger')) ?>">
                    <?= ucfirst($room['status']) ?>
                  </span>
                </td>
                <td>₱<?= number_format($room['price'], 2) ?></td>
                <td><?= htmlspecialchars($room['floor']) ?></td>
                <td><?= htmlspecialchars($room['capacity']) ?></td>
                <td>
                  <form method="post" onsubmit="return confirm('Delete this room?');" class="d-inline">
                    <input type="hidden" name="delete_room" value="<?= $room['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                  </form>
                  <button class="btn btn-primary btn-sm ms-1"
                    data-bs-toggle="modal"
                    data-bs-target="#addEditRoomModal"
                    onclick='openEditRoomModal(<?= json_encode($room) ?>)'
                  >Edit</button>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($rooms)): ?>
              <tr>
                <td colspan="9" class="text-center">No rooms found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Add/Edit Room Modal -->
<div class="modal fade" id="addEditRoomModal" tabindex="-1" aria-labelledby="addEditRoomModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <input type="hidden" name="room_id" id="room_id">
      <div class="modal-header">
        <h5 class="modal-title" id="addEditRoomModalLabel">Add/Edit Room</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body row g-3">
        <div class="col-6">
          <label class="form-label">Room Number</label>
          <input type="text" class="form-control" name="room_number" id="room_number" required>
        </div>
        <div class="col-6">
          <label class="form-label">Property Name</label>
          <input type="text" class="form-control" name="property_name" id="property_name" required>
        </div>
        <div class="col-6">
          <label class="form-label">Type</label>
          <input type="text" class="form-control" name="type" id="type" required>
        </div>
        <div class="col-6">
          <label class="form-label">Status</label>
          <select class="form-select" name="status" id="status" required>
            <option value="available">Available</option>
            <option value="occupied">Occupied</option>
            <option value="reserved">Reserved</option>
            <option value="maintenance">Maintenance</option>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea class="form-control" name="description" id="description" rows="2"></textarea>
        </div>
        <div class="col-4">
          <label class="form-label">Price (₱)</label>
          <input type="number" class="form-control" name="price" id="price" min="0" step="0.01" required>
        </div>
        <div class="col-4">
          <label class="form-label">Floor</label>
          <input type="number" class="form-control" name="floor" id="floor" min="0" required>
        </div>
        <div class="col-4">
          <label class="form-label">Capacity</label>
          <input type="number" class="form-control" name="capacity" id="capacity" min="1" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="save_room" class="btn btn-success">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
function openAddRoomModal() {
  document.getElementById('addEditRoomModalLabel').innerText = "Add Room";
  document.getElementById('room_id').value = '';
  document.getElementById('room_number').value = '';
  document.getElementById('property_name').value = '';
  document.getElementById('type').value = '';
  document.getElementById('description').value = '';
  document.getElementById('status').value = 'available';
  document.getElementById('price').value = '';
  document.getElementById('floor').value = '';
  document.getElementById('capacity').value = '';
}

function openEditRoomModal(room) {
  document.getElementById('addEditRoomModalLabel').innerText = "Edit Room";
  document.getElementById('room_id').value = room.id;
  document.getElementById('room_number').value = room.room_number;
  document.getElementById('property_name').value = room.property_name;
  document.getElementById('type').value = room.type;
  document.getElementById('description').value = room.description;
  document.getElementById('status').value = room.status;
  document.getElementById('price').value = room.price;
  document.getElementById('floor').value = room.floor;
  document.getElementById('capacity').value = room.capacity;
}
</script>

<?php $link->close(); ?>
