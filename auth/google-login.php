<?php
require_once __DIR__ . '/../layouts/config.php';
require_once __DIR__ . '/../config/google.php';

$client = new Google\Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);
$client->addScope(['openid', 'email', 'profile']);

header('Location: ' . $client->createAuthUrl());
exit;
