<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Include the configuration and functions files for database connection
include '../includes/config.php';
include '../includes/functions.php';

// Create a database connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch bookings with user details, vehicle names, route names, location names, and package image (pack_img)
$bookings = $conn->query("
    SELECT 
        b.booking_id, 
        u.user_name, 
        u.user_email, 
        b.as_id, 
        b.status, 
        a.seats, 
        a.rate, 
        v.v_name AS vehicle_name, 
        r.r_start AS route_start, 
        r.r_end AS route_end, 
        l.l_name AS location_name, 
        a.pack_img
    FROM tbl_booking AS b
    JOIN tbl_user AS u ON b.user_id = u.user_id
    JOIN tbl_assign AS a ON b.as_id = a.as_id
    JOIN tbl_vehicle AS v ON a.v_id = v.v_id
    JOIN tbl_routes AS r ON a.r_id = r.r_id
    JOIN tbl_location AS l ON a.l_id = l.l_id
");

if (!$bookings) {
    die("Error fetching bookings: " . $conn->error);
}

// Close the database connection after fetching data
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings - Trip and Travel Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        h1 {
            text-align: center;
            margin: 20px 0;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 20px;
        }
        .card {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 10px;
            width: 300px;
            margin: 15px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card-body {
            padding: 10px;
        }
        .card-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .card-text {
            font-size: 14px;
            color: #555;
        }
        .status {
            font-weight: bold;
            color: #007bff;
        }
        .delete-btn {
            background-color: red;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            display: block;
            margin-top: 10px;
            text-align: center;
        }
        .package-img {
            width: 100%;
            height: auto;
            border-radius: 5px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<h1>View Bookings</h1>

<div class="container">
    <?php if ($bookings->num_rows > 0): ?>
        <?php while ($row = $bookings->fetch_assoc()): ?>
            <div class="card">
                <!-- Display package image using pack_img -->
                <img src="../uploads/<?php echo $row['pack_img']; ?>" alt="Package Image" class="package-img">

                <div class="card-body">
                    <h2 class="card-title">Booking ID: <?php echo $row['booking_id']; ?></h2>
                    <p class="card-text"><strong>User Name:</strong> <?php echo $row['user_name']; ?></p>
                    <p class="card-text"><strong>User Email:</strong> <?php echo $row['user_email']; ?></p>
                    <p class="card-text"><strong>Assign ID:</strong> <?php echo $row['as_id']; ?></p>
                    <p class="card-text"><strong>Status:</strong> <span class="status"><?php echo $row['status']; ?></span></p>
                    <p class="card-text"><strong>Vehicle:</strong> <?php echo $row['vehicle_name']; ?></p>
                    <p class="card-text"><strong>Route:</strong> <?php echo $row['route_start']; ?> to <?php echo $row['route_end']; ?></p>
                    <p class="card-text"><strong>Location:</strong> <?php echo $row['location_name']; ?></p>
                    <p class="card-text"><strong>Seats:</strong> <?php echo $row['seats']; ?></p>
                    <p class="card-text"><strong>Rate:</strong> <?php echo $row['rate']; ?></p>
                    
                    <!-- Add a form for deleting the booking -->
                    <form method="POST" action="delete_booking.php">
                        <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                        <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this booking?');">Delete</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No bookings found.</p>
    <?php endif; ?>
</div>

</body>
</html>
