
<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
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

// Fetch necessary data for dashboard stats
$totalUsers = getTotalUsers($conn);
$totalVehicles = getTotalVehicles($conn);
$totalRoutes = getTotalRoutes($conn);
$totalBookings = getTotalBookings($conn);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Trip and Travel Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('../uploads/polar1.jpg'); /* Replace with your image URL */
            background-size: cover;
            background-position: center;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Ensure the body takes at least full viewport height */
        }
        header {
            background-color: rgba(51, 51, 51, 0.8); /* Semi-transparent header for better visibility */
            color: #fff;
            padding: 1rem;
            text-align: center;
        }
        .container {
            display: flex;
            flex: 1; /* Allow the container to grow and take available space */
        }
        .sidebar {
            background-color: rgba(68, 68, 68, 0.9); /* Semi-transparent sidebar for better visibility */
            color: #fff;
            height: 100vh;
            padding: 1rem;
            position: fixed;
            left: -250px; /* Hidden by default */
            transition: left 0.3s ease; /* Smooth transition */
        }
        .sidebar.active {
            left: 0; /* Show sidebar when active */
        }
        .sidebar h2 {
            text-align: center;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 8px; /* Reduced padding */
            margin: 5px 0;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background-color: #555;
        }
        .main {
            margin-left: 0; /* Adjusted for hidden sidebar */
            padding: 20px;
            flex: 1;
            transition: margin-left 0.3s ease; /* Smooth transition */
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .dashboard-header h1 {
            margin: 0;
            font-size: 1.5rem; /* Increased font size for better visibility */
            color:white;
        }
        .menu-toggle {
            cursor: pointer;
            font-size: 24px;
            color: #fff;
            background-color: #333;
            border: none;
            padding: 10px;
            border-radius: 5px;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent cards */
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
            margin: 10px;
            flex: 1;
            text-align: center;
        }
        .card h3 {
            margin: 0;
        }
        .card i {
            font-size: 30px;
            margin-bottom: 10px;
            color: #333;
        }
        .logout {
            text-align: right;
        }
        .logout a {
            color: #fff;
            background-color: #c00;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
        }
        .logout a:hover {
            background-color: #e00;
        }
        footer {
            background-color: rgba(51, 51, 51, 0.8); /* Semi-transparent footer */
            color: #fff;
            text-align: center;
            padding: 1rem;
            margin-top: auto; /* Push footer to the bottom */
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main {
                margin-left: 0;
            }
        }
    </style>

</head>
<body>

<header>
    <h1>Admin Dashboard</h1>
</header>

<div class="container">
    <div class="sidebar" id="sidebar">
        <h2>Admin Menu</h2>
        <a href="add_country.php">Add Country</a>
        <a href="add_location.php">Add Location</a>
        <a href="add_category.php">Add Category</a>
        <a href="add_vehicle.php">Add Vehicle</a>
        <a href="add_route.php">Add Route</a>
        <a href="view_organizers.php">View Organizers</a>
        <a href="view_bookings.php">View Bookings</a>
        <a href="view_packages.php">View Packages</a>
        
        <a href="admin_faq.php">Manage FAQs</a>
        <div class="logout">
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="main" id="main-content">
        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>

        <div class="dashboard-header">
            <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
        </div>

        <div class="dashboard-cards" style="display: flex; flex-wrap: wrap;">
            <div class="card">
                <i class="fas fa-user"></i>
                <h3>Total Users</h3>
                <p><?php echo $totalUsers; ?></p> <!-- Display dynamic total users -->
            </div>
            <div class="card">
                <i class="fas fa-bus"></i>
                <h3>Total Vehicles</h3>
                <p><?php echo $totalVehicles; ?></p> <!-- Display dynamic total vehicles -->
            </div>
            <div class="card">
                <i class="fas fa-route"></i>
                <h3>Total Routes</h3>
                <p><?php echo $totalRoutes; ?></p> <!-- Display dynamic total routes -->
            </div>
            <div class="card">
                <i class="fas fa-shopping-cart"></i>
                <h3>Total Bookings</h3>
                <p><?php echo $totalBookings; ?></p> <!-- Display dynamic total bookings -->
            </div>
        </div>
    </div>
</div>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Trip and Travel Management. All rights reserved.</p>
</footer>

<script src="../common.js"></script>
<script>
    document.getElementById('menuToggle').onclick = function() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        sidebar.classList.toggle('active');
        mainContent.style.marginLeft = sidebar.classList.contains('active') ? '250px' : '0';
    };
</script>
</body>
</html>
