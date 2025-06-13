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
    <style>
.avatar-wrapper {
  position: relative;
  display: inline-block;
  width: 56px;
  height: 56px;
}

.avatar-img {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  object-fit: cover;
  display: block;
}

.avatar-fallback {
  position: absolute;
  top: 0;
  left: 0;
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: #e0e0e0;
  color: #444;
  font-weight: 600;
  font-size: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  display: none; /* hidden by default */
}
.avatar-fallback svg {
  width: 24px;
  height: 24px;
  margin-bottom: 2px;
}
</style>

<div class="avatar-wrapper">
  <img
    src="assets/images/users/avatar-1.jpg"
    alt="profile-image"
    class="avatar-img"
    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
  >
  <span class="avatar-fallback">
    <!-- Example icon (Bootstrap person icon SVG) -->
    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
      <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm4-3a4 4 0 1 1-8 0 4 4 0 0 1 8 0z"/>
      <path fill-rule="evenodd" d="M14 14s-1-1.5-6-1.5S2 14 2 14s1-4 6-4 6 4 6 4zm-1-.995c-.001-.25-.26-.49-.782-.784C11.048 11.553 9.663 11 8 11c-1.663 0-3.048.553-4.218 1.221-.522.293-.781.533-.782.783C2.001 13.563 3.236 14 8 14s5.999-.437 5.999-.995z"/>
    </svg>
    <span>AB</span>
  </span>
</div>


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
