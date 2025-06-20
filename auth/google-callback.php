<?php
require_once __DIR__ . '/../layouts/config.php';
require_once __DIR__ . '/../config/google.php';
session_start();

$client = new Google\Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);

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
exit;
