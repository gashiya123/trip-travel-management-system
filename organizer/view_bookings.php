<?php
// Include necessary files
include '../includes/config.php'; // Database configuration
include '../includes/functions.php'; // Common functions

session_start();

// Check if the user is logged in and is an organizer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'organizer') {
    header("Location: ../login.php"); // Redirect to login if not logged in as organizer
    exit;
}

// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Handle booking status update
if (isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['status'];

    // Prepare update query
    $update_query = "UPDATE tbl_booking SET status = ? WHERE booking_id = ?";
    $stmt = $conn->prepare($update_query);

    if ($stmt) {
        $stmt->bind_param('si', $new_status, $booking_id);

        // Execute query and check if successful
        if ($stmt->execute()) {
            echo "<p style='color: green;'>Booking status updated successfully!</p>";

            // Send notification to customer about booking status update
            $notification_query = "INSERT INTO tbl_notifications (user_id, message) VALUES (?, ?)";
            $message = "Your booking has been " . htmlspecialchars($new_status) . ".";
            $stmt_notification = $conn->prepare($notification_query);
            $stmt_notification->bind_param('is', $_POST['user_id'], $message);
            $stmt_notification->execute();
        } else {
            echo "<p style='color: red;'>Error updating booking status: " . $stmt->error . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Error preparing query: " . $conn->error . "</p>";
    }

    $stmt->close();
}

// Handle booking deletion
if (isset($_POST['confirm_delete'])) {
    $booking_id = $_POST['booking_id'];
    $delete_query = "DELETE FROM tbl_booking WHERE booking_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $booking_id);
    $stmt->execute();
    $stmt->close();
    echo "<p style='color: green;'>Booking deleted successfully!</p>";
} elseif (isset($_POST['delete_booking'])) {
    $booking_id = $_POST['booking_id'];
    $user_id = $_POST['user_id'];
    
    // Display confirmation message
    echo "<p>Are you sure you want to delete this booking?</p>";
    echo "<form method='post'>";
    echo "<input type='hidden' name='booking_id' value='$booking_id'>";
    echo "<input type='hidden' name='user_id' value='$user_id'>";
    echo "<button type='submit' name='confirm_delete'>Yes, delete it</button>";
    echo "<a href='view_bookings.php'>No, go back</a>"; // Link to go back to the bookings
    echo "</form>";
    exit; // Stop further processing to wait for confirmation
}

// Fetch all bookings with the package image
$query = "
    SELECT b.booking_id, b.status, u.user_id, u.user_name, u.user_email, u.user_phone, u.user_age, u.user_gender, a.rate, a.number_of_days, v.v_name, r.r_start, r.r_end, l.l_name, l.l_details, b.booking_date, b.booking_day, a.pack_img
    FROM tbl_booking b
    JOIN tbl_assign a ON b.as_id = a.as_id
    JOIN tbl_vehicle v ON a.v_id = v.v_id
    JOIN tbl_routes r ON a.r_id = r.r_id
    JOIN tbl_location l ON a.l_id = l.l_id
    JOIN tbl_user u ON b.user_id = u.user_id
    WHERE a.v_id IN (
        SELECT v_id FROM tbl_vehicle
    )";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings</title>
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        header h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: calc(33% - 40px); /* For 3 columns layout */
        }

        @media (max-width: 1024px) {
            .card {
                width: calc(50% - 40px); /* For 2 columns layout on medium screens */
            }
        }

        @media (max-width: 768px) {
            .card {
                width: 100%; /* For 1 column layout on smaller screens */
            }
        }

        .card h3 {
            margin-bottom: 10px;
            color: #007BFF;
        }

        .card p {
            margin-bottom: 8px;
            color: #555;
        }

        .card p strong {
            color: #333;
        }

        .status {
            margin-top: 10px;
            font-weight: bold;
        }

        form {
            margin-top: 20px;
        }

        select {
            padding: 8px;
            margin-right: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        button {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button[type="submit"] {
            background-color: #007BFF;
            color: white;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        button.delete {
            background-color: #dc3545;
            color: white;
            margin-left: 10px;
            padding:10px;
        }

        button.delete:hover {
            background-color: #c82333;
        }

        /* Additional styling */
        .container {
            margin-top: 20px;
        }

        /* Image styling */
        .card img {
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<header>
    <h1>View Bookings</h1>
</header>

<main>
    <div class="container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <img src="../uploads/<?php echo htmlspecialchars($row['pack_img']); ?>" alt="Package Image">
                    <h3><?php echo htmlspecialchars($row['user_name']); ?></h3>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($row['user_email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['user_phone']); ?></p>
                    <p><strong>Age:</strong> <?php echo htmlspecialchars($row['user_age']); ?></p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($row['user_gender']); ?></p>
                    <p><strong>Package Location:</strong> <?php echo htmlspecialchars($row['l_name']); ?></p>
                    <p><strong>Route:</strong> <?php echo htmlspecialchars($row['r_start'] . ' - ' . $row['r_end']); ?></p>
                    <p><strong>Vehicle:</strong> <?php echo htmlspecialchars($row['v_name']); ?></p>
                    <p><strong>Rate:</strong> <?php echo htmlspecialchars($row['rate']); ?></p>
                    <p><strong>Days:</strong> <?php echo htmlspecialchars($row['number_of_days']); ?></p>
                    <p><strong>Booking Date:</strong> <?php echo htmlspecialchars($row['booking_date']); ?></p>
                    <p><strong>Booking Day:</strong> <?php echo htmlspecialchars($row['booking_day']); ?></p>
                    <p class="status"><strong>Status:</strong> <?php echo htmlspecialchars($row['status'] ? $row['status'] : 'Pending'); ?></p>

                    <!-- Buttons for updating and deleting bookings -->
                    <form method="post">
                        <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                        <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                        <button type="submit" name="update_status">Update Status</button>
                        <select name="status">
                            <option value="">Select Status</option>
                            <option value="confirmed">Confirm</option>
                            <option value="canceled">Cancel</option>
                        </select>
                        
                        <button type="submit" name="delete_booking" class="delete">Delete Booking</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No bookings found.</p>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
