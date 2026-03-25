<?php
include '../includes/config.php'; // Database configuration
include '../includes/functions.php'; // Common functions

session_start();

// Check if user is logged in and is an organizer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'organizer') {
    header("Location: ../login.php"); // Redirect to login if not logged in as organizer
    exit;
}

// Check if the package ID is passed via GET
if (isset($_GET['as_id'])) {
    $as_id = intval($_GET['as_id']);

    // SQL query to delete the package from tbl_assign (or relevant table)
    $query = "DELETE FROM tbl_assign WHERE as_id = ?";
    
    // Prepare and execute the statement
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $as_id);
        
        if ($stmt->execute()) {
            // Set success message and redirect back to package list
            $_SESSION['message'] = "Package deleted successfully!";
            header("Location: package_list.php");
        } else {
            // Set failure message
            $_SESSION['message'] = "Failed to delete the package. Please try again.";
            header("Location: package_list.php");
        }
        
        $stmt->close();
    }
} else {
    // No package ID provided
    $_SESSION['message'] = "No package specified for deletion.";
    header("Location: package_list.php");
}

$conn->close();
exit;
?>
