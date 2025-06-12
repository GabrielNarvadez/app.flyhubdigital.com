<?php
// Load Composer's autoloader
require __DIR__ . '/vendor/autoload.php';

// Load environment variables
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Define global constants from env
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'crm_erp');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('BASE_URL', $_ENV['BASE_URL'] ?? 'http://localhost/project-root');

// Include any reusable config or helper functions
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/functions.php';

// (Optional) Start session if needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
