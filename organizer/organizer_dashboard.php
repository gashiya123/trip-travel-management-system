<?php
// Include necessary files
include '../includes/config.php'; // Database configuration
include '../includes/functions.php'; // Common functions

session_start();

// Check if user is logged in and is an organizer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'organizer') {
    header("Location: ../login.php"); // Redirect to login if not logged in as organizer
    exit;
}

// Fetch data for the dashboard
$totalUsers = getTotalUsers($conn); // Get total number of users
$totalVehicles = getTotalVehicles($conn); // Get total number of vehicles
$totalRoutes = getTotalRoutes($conn); // Get total number of routes
$totalBookings = getTotalBookings($conn); // Get total number of bookings
$assignments = getDetailedAssignments($conn); // Fetch detailed assignments

// Fetch unread notification count
$organizer_id = $_SESSION['user_id'];
$query = "SELECT COUNT(*) as unread_count FROM tbl_notifications WHERE user_id = ? AND status = 'unread'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $organizer_id);
$stmt->execute();
$result = $stmt->get_result();
$notificationData = $result->fetch_assoc();
$unreadNotifications = $notificationData['unread_count'];

// Optional: Message display
$message = isset($_SESSION['message']) ? $_SESSION['message'] : null; // Fetch any session message
unset($_SESSION['message']); // Clear session message
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Include Font Awesome -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
           background-image: url('../uploads/polar1.jpg');
            color: #333;
            display: flex; /* Use Flexbox for layout */
            flex-direction: column; /* Arrange children vertically */
            min-height: 100vh; /* Ensure the body takes the full height of the viewport */
        }

        header {
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 10px 0;
            text-align: center;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
        }

        nav ul li {
            display: inline;
            margin-right: 15px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        nav ul li a:hover {
            text-decoration: underline;
        }

        main {
            padding: 20px;
            max-width: 800px;
            margin: auto;
            flex: 1; /* Allow the main section to grow and fill available space */
        }

        section {
            background: white;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h3 {
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        img {
            max-width: 50px;
            height: auto;
        }

        footer {
            text-align: center;
            padding: 10px 0;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            width: 100%;
        }

        /* Container for the notification link */
        .nav-item {
            position: relative; /* Set relative position to parent for absolute positioning of the badge */
            display: inline-block;
            padding-right: 20px; /* Add some padding to prevent overlap with the notification badge */
        }

        /* Notification badge */
        .notification-badge {
            background-color: #ff0000; /* Red color for unread notifications */
            color: white;
            padding: 3px 6px;
            border-radius: 50%; /* Makes the badge round */
            font-size: 10px;
            min-width: 7px; /* Small circle */
            height: 18px;
            line-height: 18px; /* Vertically center the text */
            text-align: center; /* Horizontally center the text */
            position: absolute;
            top: -15px; /* Position the badge just above the link */
            right: 0; /* Align the badge to the right edge of the notification link */
            display: inline-block;
        }

        /* Hide the badge if there are no unread notifications */
        .notification-badge:empty {
            display: none;
        }

    </style>
</head>
<body>

<header>
    <h1>Welcome to the Organizer Dashboard</h1>
    <nav>
        <ul>
            <li><a href="add_package.php"><i class="fas fa-plus"></i> Add Package</a></li>
            <li><a href="package_list.php"><i class="fas fa-list"></i> View Packages</a></li>
            <li><a href="view_registered_users.php"><i class="fas fa-users"></i> View Registered Users</a></li>
            <li><a href="view_bookings.php"><i class="fas fa-calendar-check"></i> View Bookings</a></li>
            <li><a href="view_ratings.php"><i class="fas fa-star"></i> View Ratings</a></li>
            <li class="nav-item">
                <a href="view_notifications.php"><i class="fas fa-bell"></i> Notifications</a>
                <span class="notification-badge" <?php if ($unreadNotifications == 0) echo 'style="display:none;"'; ?>><?php echo htmlspecialchars($unreadNotifications); ?></span>
            </li> <!-- Notifications Link with Count -->
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
</header>

<main>
    <?php displayMessage($message); // Display any session messages ?>

    <section>
        <h3>Dashboard Overview</h3>
        <p>Total Users: <?php echo htmlspecialchars($totalUsers); ?></p>
        <p>Total Vehicles: <?php echo htmlspecialchars($totalVehicles); ?></p>
        <p>Total Routes: <?php echo htmlspecialchars($totalRoutes); ?></p>
        <p>Total Bookings: <?php echo htmlspecialchars($totalBookings); ?></p>
    </section>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Trip Advisor and Travel Management System. All rights reserved.</p>
</footer>

</body>
</html>
