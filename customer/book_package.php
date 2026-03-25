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

// Fetch all available packages
$query = "SELECT a.as_id, a.pack_img, a.seats, a.rate, a.number_of_days, v.v_name, r.r_start, r.r_end, l.l_name, l.l_details 
          FROM tbl_assign a
          JOIN tbl_vehicle v ON a.v_id = v.v_id
          JOIN tbl_routes r ON a.r_id = r.r_id
          JOIN tbl_location l ON a.l_id = l.l_id";
$result = $conn->query($query);

// Handling the package booking
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $package_id = $_POST['package_id'];
    $booking_day = $_POST['booking_day'];
    $booking_date = $_POST['booking_date'];

    // Check if the user has booked any package within the last 10 days
    $last_booking_query = "SELECT booking_date FROM tbl_booking WHERE user_id = ? ORDER BY booking_date DESC LIMIT 1";
    $stmt = $conn->prepare($last_booking_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($last_booking_date);
    $stmt->fetch();
    $stmt->close();

    // Check if the user has made a booking before
    if ($last_booking_date) {
        // Calculate the difference between today and the last booking date
        $last_booking_timestamp = strtotime($last_booking_date);
        $current_timestamp = time();
        $days_difference = ($current_timestamp - $last_booking_timestamp) / (60 * 60 * 24); // Convert seconds to days

        if ($days_difference < 10) {
            echo "<script>alert('You cannot book another package within 10 days of your last booking. Please wait for " . (10 - round($days_difference)) . " more days.');</script>";
        } else {
            // Proceed with booking if 10 days have passed
            $booking_query = "INSERT INTO tbl_booking (user_id, as_id, status, booking_date, booking_day) VALUES (?, ?, 'pending', ?, ?)";
            $stmt = $conn->prepare($booking_query);
            $stmt->bind_param("iiss", $user_id, $package_id, $booking_date, $booking_day);
            
            if ($stmt->execute()) {
                $booking_id = $stmt->insert_id; // Get the booking ID of the newly inserted booking

                // Prepare the notification message
                $message = "Customer has booked a package. Booking ID: $booking_id.";

                // Send notification to all organizers and admins
                $admin_and_organizer_query = "SELECT user_id FROM tbl_user WHERE user_role IN ('organizer', 'admin')";
                $result = $conn->query($admin_and_organizer_query);
                
                while ($row = $result->fetch_assoc()) {
                    $recipient_id = $row['user_id'];

                    // Insert the notification
                    $notification_query = "INSERT INTO tbl_notifications (user_id, booking_id, message) VALUES (?, ?, ?)";
                    $notif_stmt = $conn->prepare($notification_query);
                    $notif_stmt->bind_param("iis", $recipient_id, $booking_id, $message);
                    $notif_stmt->execute();
                }

                echo "<script>alert('Package booked successfully and notification sent to admin and organizer.');</script>";
                // Redirect to customer dashboard or bookings page
                header("Location: customer_dashboard.php");
                exit;
            } else {
                echo "<script>alert('Booking failed. Please try again.');</script>";
            }
        }
    } else {
        // If the user has never booked before, allow the booking
        $booking_query = "INSERT INTO tbl_booking (user_id, as_id, status, booking_date, booking_day) VALUES (?, ?, 'pending', ?, ?)";
        $stmt = $conn->prepare($booking_query);
        $stmt->bind_param("iiss", $user_id, $package_id, $booking_date, $booking_day);

        if ($stmt->execute()) {
            $booking_id = $stmt->insert_id; // Get the booking ID of the newly inserted booking

            // Prepare the notification message
            $message = "Customer has booked a package. Booking ID: $booking_id.";

            // Send notification to all organizers and admins
            $admin_and_organizer_query = "SELECT user_id FROM tbl_user WHERE user_role IN ('organizer', 'admin')";
            $result = $conn->query($admin_and_organizer_query);
            
            while ($row = $result->fetch_assoc()) {
                $recipient_id = $row['user_id'];

                // Insert the notification
                $notification_query = "INSERT INTO tbl_notifications (user_id, booking_id, message) VALUES (?, ?, ?)";
                $notif_stmt = $conn->prepare($notification_query);
                $notif_stmt->bind_param("iis", $recipient_id, $booking_id, $message);
                $notif_stmt->execute();
            }

            echo "<script>alert('Package booked successfully and notification sent to admin and organizer.');</script>";
            // Redirect to customer dashboard or bookings page
            header("Location: customer_dashboard.php");
            exit;
        } else {
            echo "<script>alert('Booking failed. Please try again.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Packages</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #4CAF50;
            color: white;
            padding: 10px 0;
            text-align: center;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
        }

        .package-card {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin: 10px;
            padding: 15px;
            width: 300px;
            text-align: center;
            transition: transform 0.3s;
        }

        .package-card:hover {
            transform: scale(1.05);
        }

        .package-card img {
            max-width: 100%;
            border-radius: 5px;
            height: 300px;
            width:300px;
        }

        .package-details h3 {
            margin: 10px 0;
            font-size: 1.5em;
            color: #333;
        }

        .package-details p {
            margin: 5px 0;
            color: #555;
        }

        .book-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .book-button:hover {
            background-color: #45a049;
        }

        /* Additional styles for booking date and day input */
        .date-picker {
            margin: 10px 0;
        }
    </style>
</head>
<body>
<header>
    <h1>Available Packages</h1>
</header>

<main>
    <div class="container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="package-card">
                    <img src="../uploads/<?php echo htmlspecialchars($row['pack_img']); ?>" alt="Package Image">
                    <div class="package-details">
                        <h3><?php echo htmlspecialchars($row['l_name']); ?></h3>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($row['l_details']); ?></p>
                        <p><strong>Route:</strong> <?php echo htmlspecialchars($row['r_start']) . " - " . htmlspecialchars($row['r_end']); ?></p>
                        <p><strong>Vehicle:</strong> <?php echo htmlspecialchars($row['v_name']); ?></p>
                        <p><strong>Seats Available:</strong> <?php echo htmlspecialchars($row['seats']); ?></p>
                        <p><strong>Rate:</strong> $<?php echo htmlspecialchars($row['rate']); ?></p>
                        <p><strong>Number of Days:</strong> <?php echo htmlspecialchars($row['number_of_days']); ?></p>

                        <!-- Booking form -->
                        <form method="POST" action="">
                            <input type="hidden" name="package_id" value="<?php echo htmlspecialchars($row['as_id']); ?>">

                            <!-- Booking date input -->
                            <div class="date-picker">
                                <label for="booking_date">Select Booking Date:</label>
                                <input type="date" name="booking_date" required>
                            </div>

                            <div class="date-picker">
                                <label for="booking_day">Select Booking Day:</label>
                                <select name="booking_day" required>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>
                                    <option value="Sunday">Sunday</option>
   
                                </select>
                            </div>

                            <button type="submit" class="book-button">Book Now</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No packages available at the moment.</p>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
