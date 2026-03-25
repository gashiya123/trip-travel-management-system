<?php
session_start();

// Include configuration and functions files
include '../includes/config.php';
include '../includes/functions.php';

// Check if the organizer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'organizer') {
    header("Location: ../login.php");
    exit();
}

// Get the logged-in organizer's ID
$organizer_id = $_SESSION['user_id'];

// Create a connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch unread notifications for the organizer
$query = "SELECT notification_id, message, status, created_at 
          FROM tbl_notifications 
          WHERE user_id = ? 
          ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $organizer_id);
$stmt->execute();
$result = $stmt->get_result();

// Mark notification as read when the organizer clicks on it
if (isset($_POST['mark_read'])) {
    $notification_id = $_POST['notification_id'];

    // Update the status of the notification to 'read'
    $update_query = "UPDATE tbl_notifications SET status = 'read' WHERE notification_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("i", $notification_id);
    $update_stmt->execute();

    // Refresh the page to show updated notifications
    header("Location: view_notifications.php");
    exit();
}

// Count unread notifications
$count_query = "SELECT COUNT(*) as unread_count FROM tbl_notifications WHERE user_id = ? AND status = 'unread'";
$count_stmt = $conn->prepare($count_query);
$count_stmt->bind_param("i", $organizer_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$unread_count = $count_result->fetch_assoc()['unread_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        main {
            padding: 20px;
            max-width: 800px;
            margin: 50px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #333;
            color: white;
        }
        button {
            padding: 10px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button[style*="background-color: red;"] {
            background-color: red;
        }
        button[style*="background-color: green;"] {
            background-color: green;
            cursor: default;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<main>
    <h1>Notifications</h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Message</th>
                    <th>Received At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['message']); ?></td>
                        <td><?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?></td>
                        <td>
                            <form action="view_notifications.php" method="POST">
                                <input type="hidden" name="notification_id" value="<?php echo $row['notification_id']; ?>">
                                <?php if ($row['status'] === 'unread'): ?>
                                    <button type="submit" name="mark_read" style="background-color: red;">Mark as Read</button>
                                <?php else: ?>
                                    <button type="submit" style="background-color: green;" disabled>Read</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No new notifications.</p>
    <?php endif; ?>

</main>

</body>
</html>

<?php
// Close the statement and connection
$stmt->close();
$count_stmt->close();
$conn->close();
?>
