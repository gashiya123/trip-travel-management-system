<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

include '../includes/config.php';
include '../includes/functions.php';

// Establish database connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get notification ID from URL
$notification_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Update notification status to 'read'
$update_query = $conn->prepare("
    UPDATE tbl_notifications 
    SET status = 'read' 
    WHERE notification_id = ? AND user_id = ?
");
$update_query->bind_param("ii", $notification_id, $_SESSION['user_id']);
$update_query->execute();

// Close the connection
$update_query->close();
$conn->close();

// Redirect back to notifications page with success message
header("Location: notifications.php?success=1");
exit();
?>
