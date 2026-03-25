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

// Fetch all organizers (users with the role of organizer)
$organizers = $conn->query("SELECT user_id, user_name, user_email, user_phone FROM tbl_user WHERE user_role = 'organizer'");

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Organizers - Trip and Travel Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 1rem;
            text-align: center;
            width: 100%;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }
        .card {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            width: 300px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .card h3 {
            margin: 0 0 10px;
            font-size: 1.5em;
        }
        .card p {
            margin: 5px 0;
            font-size: 1em;
        }
        .logout {
            text-align: right;
            margin-top: 20px;
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
    </style>
</head>
<body>

<header>
    <h1>View Organizers</h1>
</header>

<div class="card-container">
    <?php if ($organizers->num_rows > 0): ?>
        <?php while ($row = $organizers->fetch_assoc()): ?>
            <div class="card">
                <h3><?php echo htmlspecialchars($row['user_name']); ?></h3>
                <p><strong>User ID:</strong> <?php echo htmlspecialchars($row['user_id']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($row['user_email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['user_phone']); ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="card" style="text-align: center;">
            <p>No organizers found.</p>
        </div>
    <?php endif; ?>
</div>

<div class="logout">
    <a href="../logout.php">Logout</a>
</div>

</body>
</html>
