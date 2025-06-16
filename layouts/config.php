<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'flyhubapp');
define('DB_PASSWORD', 'KatieBruha_02');
define('DB_NAME', 'apps-flyhub');

/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

$gmailid = 'gab.narvadez@gmail.com'; // YOUR gmail email
$gmailpassword = 'Masterpassword1'; // YOUR gmail password
$gmailusername = 'GabrielNarvadez'; // YOUR gmail User name

?>
