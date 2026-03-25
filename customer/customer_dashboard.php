<?php
session_start();

// Check if the user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

// Include the configuration and functions files
include '../includes/config.php';
include '../includes/functions.php';

// Create a database connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch customer details
$user_id = $_SESSION['user_id'];
$user_info = getCustomerDetails($conn, $user_id);

// Fetch notifications for the customer
$notifications_query = $conn->prepare("
    SELECT notification_id, message, status 
    FROM tbl_notifications 
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$notifications_query->bind_param("i", $user_id);
$notifications_query->execute();
$notifications_result = $notifications_query->get_result();

// Count unread notifications
$unread_notifications_count = 0;
$notifications = [];
while ($notification = $notifications_result->fetch_assoc()) {
    $notifications[] = $notification; // Store notifications for display later
    if ($notification['status'] === 'unread') {
        $unread_notifications_count++;
    }
}

// Free the result set
$notifications_query->close();

// Fetch customer bookings, including the package price
$bookings_query = $conn->prepare("
    SELECT b.as_id, v.v_name, a.number_of_days, a.seats, l.l_name, a.rate 
    FROM tbl_booking b 
    JOIN tbl_assign a ON b.as_id = a.as_id 
    JOIN tbl_vehicle v ON a.v_id = v.v_id 
    JOIN tbl_location l ON a.l_id = l.l_id 
    WHERE b.user_id = ?
");
$bookings_query->bind_param("i", $user_id);
$bookings_query->execute();
$bookings_result = $bookings_query->get_result();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Trip and Travel Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        header {
            background-color: rgba(0,0,0,0.8);
            color: #fff;
            padding: 1rem;
            text-align: center;
            width: 100%;
        }
        h1 {
            color: #fff;
            margin: 0;
        }
        .container {
            max-width: 800px;
            width: 100%;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            margin-top: 20px;
        }
        .menu {
            display: flex;
            justify-content: space-around;
            background-color: rgba(0,0,0,0.8);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .menu a {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
            position: relative;
        }
        .menu a:hover {
            background-color: rgba(0,0,0,0.4);
        }
        .notification-badge {
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 3px 7px;
            position: absolute;
            top: -10px;
            right: -10px;
            font-weight: bold;
            font-size: 12px;
        }
        .profile, .bookings {
            margin-bottom: 20px;
        }
        .bookings {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .booking-card {
            background: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            flex: 1 1 30%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s;
        }
        .booking-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .booking-card h3 {
            margin-top: 0;
        }
    </style>
</head>
<body>

<header>
    <h1>Welcome, <?php echo htmlspecialchars($user_info['user_name']); ?></h1>
</header>

<div class="container">
    
    <div class="menu">
        <a href="book_package.php">Book Package</a>
        <a href="view_booked_packages.php">View Booked Packages</a>
        <a href="profile.php">View Profile</a>
        <a href="rate_package.php">Rate Package</a>
        <a href="notifications.php">Notifications 
            <?php if ($unread_notifications_count > 0): ?>
                <span class="notification-badge"><?php echo $unread_notifications_count; ?></span>
            <?php endif; ?>
        </a>
    </div>

    <div class="profile">
        <h2>Your Profile</h2>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user_info['user_email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user_info['user_phone']); ?></p>
    </div>

    <div class="bookings">
        <h2>Your Bookings</h2>
        <?php if ($bookings_result->num_rows > 0): ?>
            <?php while ($row = $bookings_result->fetch_assoc()): ?>
                <div class="booking-card">
                    <h3>Assignment ID: <?php echo htmlspecialchars($row['as_id']); ?></h3>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($row['l_name']); ?></p>
                    <p><strong>Vehicle:</strong> <?php echo htmlspecialchars($row['v_name']); ?></p>
                    <p><strong>Number of Days:</strong> <?php echo htmlspecialchars($row['number_of_days']); ?></p>
                    <p><strong>Seats:</strong> <?php echo htmlspecialchars($row['seats']); ?></p>
                    <p><strong>Price:</strong> $<?php echo htmlspecialchars(number_format($row['rate'], 2)); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No bookings found.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
