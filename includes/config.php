<?php
// Database connection parameters
$servername = "localhost"; // Change as necessary
$dbusername = "root"; // Change as necessary
$dbpassword = ""; // Change as necessary
$dbname = "trip_and_travel_management"; // Change as necessary

// Create a database connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
