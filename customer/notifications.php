<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: login.php");
    exit();
}
include '../includes/config.php';
include '../includes/functions.php';

// Establish database connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch notifications for the user
$notifications_query = $conn->prepare("
    SELECT notification_id, message, status, created_at 
    FROM tbl_notifications 
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$notifications_query->bind_param("i", $user_id);
$notifications_query->execute();
$notifications_result = $notifications_query->get_result();

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Notifications</title>
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

        h1 {
            color: #00BFFF;
            margin-bottom: 20px;
        }

        ul {
            list-style-type: none;
            padding: 0;
            width: 100%;
            max-width: 600px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        li {
            border-bottom: 1px solid #e0e0e0;
            padding: 15px;
            display: flex;
            flex-direction: column;
        }

        li:last-child {
            border-bottom: none;
        }

        p {
            margin: 0;
            color: #333;
        }

        small {
            color: #888;
            font-size: 0.9em;
        }

        .status {
            margin-top: 5px;
            font-weight: bold;
        }

        .status.unread {
            color: red;
        }

        .status.read {
            color: green;
        }

        .mark-read {
            margin-top: 10px;
            padding: 5px 10px;
            background-color: #00BFFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .mark-read:hover {
            background-color: #008bbf;
        }

        .success-message {
            color: green;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Your Notifications</h1>
    <?php if (isset($_GET['success'])): ?>
        <p class="success-message">Notification marked as read successfully!</p>
    <?php endif; ?>
    <?php if ($notifications_result->num_rows > 0): ?>
        <ul>
            <?php while ($notification = $notifications_result->fetch_assoc()): ?>
                <li>
                    <p><?php echo htmlspecialchars($notification['message']); ?></p>
                    <small>Created At: <?php echo htmlspecialchars($notification['created_at']); ?></small>
                    <small class="status <?php echo htmlspecialchars($notification['status']); ?>">
                        Status: <?php echo htmlspecialchars($notification['status']); ?>
                    </small>
                    <?php if ($notification['status'] === 'unread'): ?>
                        <a href="mark_as_read.php?id=<?php echo htmlspecialchars($notification['notification_id']); ?>" class="mark-read">Mark as Read</a>
                    <?php endif; ?>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No notifications found.</p>
    <?php endif; ?>
</body>
</html>
