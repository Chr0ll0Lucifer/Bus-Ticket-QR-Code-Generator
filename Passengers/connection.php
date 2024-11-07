<?php
// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection settings
$host = "localhost";
$user = "root";
$password = "";
$db_name = "bus ticket qr code generator";

// Create connection
$con = mysqli_connect($host, $user, $password, $db_name);

// Check connection
if (mysqli_connect_errno()) {
    die("Failed to connect with MYSQL: " . mysqli_connect_error());
}
?>
