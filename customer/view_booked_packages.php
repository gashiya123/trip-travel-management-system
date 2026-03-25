<?php
// Include necessary files
include '../includes/config.php'; // Database configuration
include '../includes/functions.php'; // Common functions

session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: ../login.php"); // Redirect to login if not logged in as customer
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch booked packages
$query = "SELECT b.*, a.pack_img, l.l_name 
          FROM tbl_booking b
          JOIN tbl_assign a ON b.as_id = a.as_id
          JOIN tbl_location l ON a.l_id = l.l_id
          WHERE b.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    
    // Perform cancellation
    $cancel_query = "DELETE FROM tbl_booking WHERE booking_id = ?";
    $cancel_stmt = $conn->prepare($cancel_query);
    $cancel_stmt->bind_param("i", $booking_id);
    
    if ($cancel_stmt->execute()) {
        echo "<script>alert('Booking cancelled successfully.');</script>";
    } else {
        echo "<script>alert('Failed to cancel booking. Please try again.');</script>";
    }

    // After processing the cancellation, refresh the bookings again
    header("Location: view_booked_packages.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booked Packages</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        header {
            background-color: rgba(0, 128, 0, 0.8);;
            color: white;
            padding: 10px 0;
            text-align: center;
        }

        .container {
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between; /* Distributes space evenly */
        }

        .package-card {
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            flex: 1 1 calc(30% - 20px); /* Responsive card sizing */
            max-width: calc(30% - 20px); /* Limit max width */
            display: flex;
            flex-direction: column;
            align-items: center; /* Center content */
            transition: transform 0.3s; /* Transition effect on hover */
        }

        .package-card:hover {
            transform: scale(1.05); /* Scale effect on hover */
        }

        .package-card img {
            max-width: 100%; /* Image responsiveness */
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .package-card h3 {
            margin: 10px 0;
        }

        .package-card p {
            margin: 5px 0;
            text-align: center; /* Center align text */
        }

        .cancel-button {
            background-color: #ff4c4c;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            text-align: center;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        .cancel-button:hover {
            background-color: #ff1a1a; /* Darker shade on hover */
        }
    </style>
    <script>
        // Function to confirm cancellation
        function confirmCancellation() {
            return confirm('Do you really want to cancel this booking?');
        }
    </script>
</head>
<body>

<header>
    <h1>Your Booked Packages</h1>
</header>

<main class="container">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='package-card'>";
            // Display the package image
            if (!empty($row['pack_img'])) {
                echo "<img src='../uploads/" . htmlspecialchars($row['pack_img']) . "' alt='Package Image'>";
            }
            echo "<h3>" . htmlspecialchars($row['l_name']) . "</h3>";
            echo "<p><strong>Booking Date:</strong> " . htmlspecialchars($row['booking_date']) . "</p>";
            echo "<p><strong>Booking Day:</strong> " . htmlspecialchars($row['booking_day']) . "</p>";
            echo "<p><strong>Status:</strong> " . htmlspecialchars($row['status']) . "</p>";
            echo "<form method='POST' action='view_booked_packages.php' onsubmit='return confirmCancellation();'>";
            echo "<input type='hidden' name='booking_id' value='" . htmlspecialchars($row['booking_id']) . "'>";
            echo "<button type='submit' name='cancel_booking' class='cancel-button'>Cancel Booking</button>";
            echo "</form>";
            echo "</div>";
        }
    } else {
        echo "<p>You have not booked any packages yet.</p>";
    }
    ?>
</main>

</body>
</html>
