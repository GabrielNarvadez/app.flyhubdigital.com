<?php
require_once __DIR__ . '/layouts/config.php';

// 1. Get code from Google
if (!isset($_GET['code'])) {
    header("Location: auth-login.php");
    exit;
}

$client_id = 'YOUR_GOOGLE_CLIENT_ID';
$client_secret = 'YOUR_GOOGLE_CLIENT_SECRET';
$redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/auth-google-callback.php';

// 2. Exchange code for token
$token_url = "https://oauth2.googleapis.com/token";
$post_data = [
    'code' => $_GET['code'],
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'redirect_uri' => $redirect_uri,
    'grant_type' => 'authorization_code',
];

$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$token_data = json_decode($response, true);
if (!isset($token_data['access_token'])) {
    die('Google login failed.');
}

// 3. Get user info
$userinfo_url = "https://www.googleapis.com/oauth2/v2/userinfo?access_token=" . $token_data['access_token'];
$userinfo = json_decode(file_get_contents($userinfo_url), true);

if (!isset($userinfo['email'])) {
    die('Unable to get email from Google.');
}

// 4. Check if user exists
$email = $userinfo['email'];
$name = $userinfo['name'] ?? '';
$google_id = $userinfo['id'];

$sql = "SELECT id, is_verified FROM users WHERE email = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        // Existing user: log in
        mysqli_stmt_bind_result($stmt, $user_id, $is_verified);
        mysqli_stmt_fetch($stmt);
        // Optional: Only allow login if verified
        if ($is_verified) {
            $_SESSION['user_id'] = $user_id;
            header('Location: dashboard.php');
            exit;
        } else {
            echo "Please verify your email address before logging in.";
            exit;
        }
    } else {
        // New user: create a new tenant and register user
        $company = $userinfo['hd'] ?? 'Google User'; // Use Google Apps domain if available
        $sql_tenant = "INSERT INTO tenants (tenant_name) VALUES (?)";
        $stmt_tenant = mysqli_prepare($link, $sql_tenant);
        mysqli_stmt_bind_param($stmt_tenant, 's', $company);
        mysqli_stmt_execute($stmt_tenant);
        $tenant_id = mysqli_insert_id($link);
        mysqli_stmt_close($stmt_tenant);

        $sql_user = "INSERT INTO users (name, email, tenant_id, is_verified, google_id) VALUES (?, ?, ?, 1, ?)";
        $stmt_user = mysqli_prepare($link, $sql_user);
        mysqli_stmt_bind_param($stmt_user, 'ssis', $name, $email, $tenant_id, $google_id);
        mysqli_stmt_execute($stmt_user);
        $user_id = mysqli_insert_id($link);
        mysqli_stmt_close($stmt_user);

        $_SESSION['user_id'] = $user_id;
        header('Location: dashboard.php');
        exit;
    }
    mysqli_stmt_close($stmt);
}
?>
