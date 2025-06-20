<?php
/* 1. Composer autoloader */
$vendorAutoload = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require $vendorAutoload;
}

/* 2. Load .env */
if (class_exists(\Dotenv\Dotenv::class)) {
    // pick one of the two lines below

    // a) need getenv() later – not thread-safe on FPM/Apache workers
    $dotenv = Dotenv\Dotenv::createUnsafeImmutable(dirname(__DIR__));

    // b) fine with $_ENV / $_SERVER – safest (comment the other out)
    // $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));

    $dotenv->safeLoad();           // skip silently if .env is missing
}

/* 3. Application config */
define('DB_SERVER', $_ENV['DB_SERVER'] ?? 'localhost');
define('DB_USERNAME', $_ENV['DB_USERNAME'] ?? 'root');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'test');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if (!$link) {
    die('ERROR: Could not connect. ' . mysqli_connect_error());
}

/* 4. Gmail creds (taken from .env) */
define('GMAIL_ID',       $_ENV['GMAIL_ID']       ?? '');
define('GMAIL_PASSWORD', $_ENV['GMAIL_PASSWORD'] ?? '');
define('GMAIL_USERNAME', $_ENV['GMAIL_USERNAME'] ?? '');
