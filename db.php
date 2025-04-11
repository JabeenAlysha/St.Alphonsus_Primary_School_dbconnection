<?php
// Set database connection details
$servername = "localhost";       // Server name 
$username = "root";              // Default username for XAMPP
$password = "";                  // Default password is usually blank
$database = "StAlphonsus_Primary_school_system"; // Database name

// Create a new connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $database);

// Check if the connection was successful
if ($conn->connect_error) {
    // If connection failed, stop and show error
    die("Connection failed: " . $conn->connect_error);
}

// Close the connection
$conn->close();
?>
