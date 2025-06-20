<?php
// Google Client Info
$client_id = 'YOUR_GOOGLE_CLIENT_ID'; // Replace with your client ID
$redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/auth-google-callback.php';
$scope = 'email profile';
$auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
    'client_id' => $client_id,
    'redirect_uri' => $redirect_uri,
    'response_type' => 'code',
    'scope' => $scope,
    'access_type' => 'online',
    'prompt' => 'select_account'
]);
header('Location: ' . $auth_url);
exit;
?>
