<?php
// config/db.php

$mysqli = new mysqli(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME
);

// Check connection
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

// Optional: set charset to UTF-8
$mysqli->set_charset("utf8mb4");

// Make it globally accessible if needed
// $GLOBALS['db'] = $mysqli;
