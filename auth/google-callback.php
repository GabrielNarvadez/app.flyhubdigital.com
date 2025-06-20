<?php
require_once __DIR__ . '/../layouts/config.php';
require_once __DIR__ . '/../config/google.php';
session_start();

/* 1. Google client */
$client = new Google\Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);
$client->addScope(['openid', 'email', 'profile']);   // for completeness

/* 2. Basic error handling */
if (isset($_GET['error'])) {
    echo 'Google OAuth error: ' . htmlspecialchars($_GET['error']);
    exit;
}
if (!isset($_GET['code'])) {
    echo 'Missing OAuth code';
    exit;
}

/* 3. Exchange code → tokens */
$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
if (isset($token['error'])) {
    echo 'Token exchange failed: ' . htmlspecialchars($token['error']);
    exit;
}
if (empty($token['id_token'])) {
    echo 'No id_token returned. Check that the "openid" scope is included.';
    exit;
}

/* 4. Validate id_token and extract user info */
$payload  = $client->verifyIdToken($token['id_token']);
$email    = $payload['email'];
$googleId = $payload['sub'];
$fullName = $payload['name'] ?? '';

/* 5. Look up or create the user — NO tenant creation */
$stmt = $link->prepare("SELECT id FROM users WHERE email=?");
$stmt->bind_param('s', $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user) {
    /* update Google ID if newly linked */
    $link->query("UPDATE users
                  SET google_id='$googleId',
                      is_verified=1
                  WHERE id={$user['id']}");
    $userId = $user['id'];
} else {
    /* password column is NOT NULL — store an empty string or random hash */
    $emptyPw = '';
    $sql = "INSERT INTO users (tenant_id, name, email, password,
                               google_id, is_verified, created_at)
            VALUES (NULL, ?, ?, ?, ?, 1, NOW())";
    $stmt = $link->prepare($sql);
    $stmt->bind_param('ssss', $fullName, $email, $emptyPw, $googleId);
    $stmt->execute();
    $userId = $stmt->insert_id;
}

/* 6. Log the user in */
$_SESSION['user_id'] = $userId;
header('Location: ../index.php');
exit;
