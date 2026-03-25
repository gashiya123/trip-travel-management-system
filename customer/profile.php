<?php
session_start();
include '../includes/config.php'; // Database connection
include '../includes/functions.php'; // Any custom functions

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

// Get the logged-in user's ID
$userId = $_SESSION['user_id'];

// Fetch user profile data from the database
$query = "SELECT user_id, user_name, user_email, user_phone, user_address, user_age, user_gender FROM tbl_user WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc(); // Fetch user data
} else {
    echo "No user found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .profile {
            margin-top: 20px;
        }

        .profile p {
            font-size: 16px;
            color: #555;
            margin: 10px 0;
        }

        .profile p strong {
            color: #333;
        }

        .btn {
            display: inline-block;
            text-align: center;
            background-color: green;
            color: white;
            padding: 10px 20px;
            margin: 10px 5px 0 0;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: darkgreen;
        }

        .btn:active {
            background-color: #004085;
        }

        .btn + .btn {
            margin-left: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Profile Information</h2>
    <div class="profile">
        <p><strong>User ID:</strong> <?php echo htmlspecialchars($user['user_id']); ?></p>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['user_name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['user_email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['user_phone']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($user['user_address']); ?></p>
        <p><strong>Age:</strong> <?php echo htmlspecialchars($user['user_age']); ?></p>
        <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['user_gender']); ?></p>
    </div>
    <a href="update_profile.php" class="btn">Edit Profile</a>
    <a href="../logout.php" class="btn">Logout</a>
</div>

</body>
</html>
