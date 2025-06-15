<?php
// Make sure your DB connection ($link) is ready before this include

// Fetch contacts
$contacts = [];
$sql = "SELECT id, first_name, last_name, email, phone_number FROM contacts ORDER BY first_name, last_name";
if ($result = mysqli_query($link, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $contacts[] = $row;
    }
    mysqli_free_result($result);
}

// Fetch units
$units = [];
$sql = "SELECT id, project_title, project_site, block, lot, phase, lot_class, lot_area, price_per_sqm
        FROM units
        ORDER BY project_title, block, lot";
if ($result = mysqli_query($link, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $units[] = $row;
    }
    mysqli_free_result($result);
}
?>

<div class="container py-4">
  <h3>Contacts</h3>
  <table class="table table-bordered table-sm">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($contacts as $c): ?>
        <tr>
          <td><?= htmlspecialchars($c['id']) ?></td>
          <td><?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?></td>
          <td><?= htmlspecialchars($c['email']) ?></td>
          <td><?= htmlspecialchars($c['phone_number']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <h3 class="mt-5">Units</h3>
  <table class="table table-bordered table-sm">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Project</th>
        <th>Site</th>
        <th>Block</th>
        <th>Lot</th>
        <th>Phase</th>
        <th>Class</th>
        <th>Area (sqm)</th>
        <th>Price per sqm</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($units as $u): ?>
        <tr>
          <td><?= htmlspecialchars($u['id']) ?></td>
          <td><?= htmlspecialchars($u['project_title']) ?></td>
          <td><?= htmlspecialchars($u['project_site']) ?></td>
          <td><?= htmlspecialchars($u['block']) ?></td>
          <td><?= htmlspecialchars($u['lot']) ?></td>
          <td><?= htmlspecialchars($u['phase']) ?></td>
          <td><?= htmlspecialchars($u['lot_class']) ?></td>
          <td><?= htmlspecialchars($u['lot_area']) ?></td>
          <td><?= htmlspecialchars($u['price_per_sqm']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
