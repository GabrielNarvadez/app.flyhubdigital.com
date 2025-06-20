<?php
require_once __DIR__ . '/../layouts/config.php';
require_once __DIR__ . '/../config/google.php';
session_start();

<<<<<<< HEAD
/* 1. Google client */
=======
>>>>>>> 4b2c63115e716a24c849473c7eb09ea277a18027
$client = new Google\Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);
<<<<<<< HEAD
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
=======

if (!isset($_GET['code'])) {
    exit('Missing OAuth code');
}

$token   = $client->fetchAccessTokenWithAuthCode($_GET['code']);
$payload = $client->verifyIdToken($token['id_token']);     // signature, aud, exp all verified

$email    = $payload['email'];
$googleId = $payload['sub'];
$name     = $payload['name'] ?? '';

/* ---- look up or auto-create user ----- */
require_once __DIR__ . '/../config/db.php';   // $link (mysqli)
$stmt = $link->prepare("SELECT id FROM users WHERE email=?");
$stmt->bind_param('s', $email);
$stmt->execute();
$res  = $stmt->get_result();
$user = $res->fetch_assoc();

if ($user) {
    $userId = $user['id'];
    $link->query("UPDATE users SET google_id='$googleId', is_verified=1 WHERE id=$userId");
} else {
    // minimalist tenant creation; adjust for your schema
    $link->query("INSERT INTO tenants(name) VALUES('". $link->real_escape_string(explode('@',$email)[0])."-tenant')");
    $tenantId = $link->insert_id;

    $link->query("INSERT INTO users(full_name,email,password,google_id,is_verified,tenant_id)
                  VALUES('".$link->real_escape_string($name)."','$email',NULL,'$googleId',1,$tenantId)");
    $userId = $link->insert_id;
}

$_SESSION['user_id'] = $userId;
header('Location: /dashboard.php');
>>>>>>> 4b2c63115e716a24c849473c7eb09ea277a18027
exit;
