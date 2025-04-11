<?php
// db.php

$servername = "localhost";
$username = "root";
$password = "";
$database = "StAlphonsus_Primary_school_system";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->close();
?>


