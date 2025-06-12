<?php
// modules/contact-card.php

// 0) If session isn’t already started by the parent, start it
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 1) Ensure $link is available: include your DB config if not already
if (!isset($link)) {
    require_once __DIR__ . '/../layouts/config.php';
}

// 2) Now you can safely use $link->prepare() below
$contact_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($contact_id < 1) {
    echo '<div class="alert alert-danger">Invalid contact ID.</div>';
    return;
}

$stmt = $link->prepare("
    SELECT 
      c.first_name, c.last_name, c.email, c.phone_number,
      c.country, c.address, co.name AS company_name
    FROM contacts AS c
    LEFT JOIN companies AS co ON c.company_id = co.id
    WHERE c.id = ?
");
$stmt->bind_param('i', $contact_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    echo '<div class="alert alert-danger">Contact not found.</div>';
    return;
}
$ct = $res->fetch_assoc();
$stmt->close();
?>

<div class="card text-center mx-auto" style="max-width:400px">
  <div class="card-body">
    <img src="assets/images/users/avatar-<?= $contact_id ?>.jpg"
         class="rounded-circle avatar-lg img-thumbnail" alt="profile-image">

    <h4 class="mb-1 mt-2">
      <?= htmlspecialchars($ct['first_name'].' '.$ct['last_name']) ?>
    </h4>
    <p class="text-muted">
      <?= htmlspecialchars($ct['company_name'] ?? 'No Company Assigned') ?>
    </p>

    <div class="btn-group mb-2">
      <a href="contact-edit.php?id=<?= $contact_id ?>"
         class="btn btn-primary">Edit Profile</a>
      <a href="mailto:<?= urlencode($ct['email']) ?>"
         class="btn btn-success">Send Email</a>
      <button type="button"
              class="btn btn-light dropdown-toggle"
              data-bs-toggle="dropdown">Actions</button>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">Customize Properties</a></li>
        <li><a class="dropdown-item" href="#">View All Properties</a></li>
      </ul>
    </div>

    <div class="text-start mt-3">
      <h4 class="text-primary mb-2"><strong>About this contact</strong></h4>
      <p class="text-muted mb-2"><strong>Mobile :</strong>
        <span class="ms-2"><?= htmlspecialchars($ct['phone_number'] ?? '–') ?></span>
      </p>
      <p class="text-muted mb-2"><strong>Email :</strong>
        <span class="ms-2"><?= htmlspecialchars($ct['email'] ?? '–') ?></span>
      </p>
      <p class="text-muted mb-2"><strong>Location :</strong>
        <span class="ms-2"><?= htmlspecialchars($ct['country'] ?? '–') ?></span>
      </p>
      <p class="text-muted mb-2"><strong>Address :</strong>
        <span class="ms-2"><?= nl2br(htmlspecialchars($ct['address'] ?? '–')) ?></span>
      </p>
      <p class="text-muted mb-2"><strong>Contact Owner :</strong>
        <span class="ms-2"><?= htmlspecialchars($_SESSION['user_name'] ?? '–') ?></span>
      </p>
    </div>

    <ul class="social-list list-inline mt-3 mb-0">
      <li class="list-inline-item">
        <a href="#" class="social-list-item border-primary text-primary">
          <i class="ri-facebook-circle-fill"></i>
        </a>
      </li>
      <li class="list-inline-item">
        <a href="#" class="social-list-item border-danger text-danger">
          <i class="ri-google-fill"></i>
        </a>
      </li>
      <li class="list-inline-item">
        <a href="#" class="social-list-item border-info text-info">
          <i class="ri-twitter-fill"></i>
        </a>
      </li>
    </ul>
  </div>
</div>
