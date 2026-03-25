<?php
// Include database configuration
include __DIR__ . '/../includes/config.php'; // Adjust the path if necessary

// Start the session
session_start();

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'organizer') {
    header('Location: login.php'); // Redirect if not logged in
    exit();
}

// Create a new MySQLi connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch registered users from the database including their roles
$sql = "SELECT user_id, user_name, user_email, user_phone, user_gender, user_age, user_address, user_role FROM tbl_user WHERE user_status = 'active' ORDER BY user_name ASC";
$result = $conn->query($sql);

// HTML structure
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #87CEFA;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #87CEFA;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        a {
            display: block;
            margin: 20px auto;
            text-align: center;
            text-decoration: none;
            color: white;
            background-color: #87CEFA;
            padding: 10px;
            border-radius: 5px;
        }
        a:hover {
            background-color: #B0E0E6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registered Users</h1>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>Age</th>
                    <th>Address</th>
                    <th>Role</th> <!-- New Column for Role -->
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($user['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['user_email']); ?></td>
                            <td><?php echo htmlspecialchars($user['user_phone']); ?></td>
                            <td><?php echo htmlspecialchars($user['user_gender']); ?></td>
                            <td><?php echo htmlspecialchars($user['user_age']); ?></td>
                            <td><?php echo htmlspecialchars($user['user_address']); ?></td>
                            <td><?php echo htmlspecialchars($user['user_role']); ?></td> <!-- Display Role -->
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No registered users found.</td> <!-- Updated colspan -->
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="organizer_dashboard.php">Back to Dashboard</a>
    </div>

<?php
// Close the connection
$conn->close();
?>
</body>
</html>
