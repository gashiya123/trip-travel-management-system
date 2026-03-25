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

// Fetch assignments for packages along with the image and location name
$packages = $conn->query("
    SELECT a.as_id, v.v_name, r.r_start, r.r_end, a.seats, a.rate, a.number_of_days, a.pack_img, l.l_name
    FROM tbl_assign AS a
    JOIN tbl_vehicle AS v ON a.v_id = v.v_id
    JOIN tbl_routes AS r ON a.r_id = r.r_id
    JOIN tbl_location AS l ON a.l_id = l.l_id
");

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Packages - Trip and Travel Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 1rem;
            text-align: center;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
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
        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px 10px 0 0;
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
        .rate, .seats, .days {
            font-weight: bold;
        }
    </style>
</head>
<body>

<header>
    <h1>View Packages</h1>
</header>

<div class="container">
    <?php if ($packages->num_rows > 0): ?>
        <?php while ($row = $packages->fetch_assoc()): ?>
            <div class="card">
                <img src="../uploads/<?php echo $row['pack_img']; ?>" alt="Package Image">
                <div class="card-body">
                    <h2 class="card-title"><?php echo $row['l_name']; ?></h2> <!-- Updated to location name -->
                    <p class="card-text"><strong>Route:</strong> <?php echo $row['r_start'] . ' to ' . $row['r_end']; ?></p>
                    <p class="card-text"><strong>Seats:</strong> <?php echo $row['seats']; ?></p>
                    <p class="card-text"><strong>Rate:</strong> <?php echo $row['rate']; ?> per day</p>
                    <p class="card-text"><strong>Duration:</strong> <?php echo $row['number_of_days']; ?> days</p>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No packages found</p>
    <?php endif; ?>
</div>

</body>
</html>
