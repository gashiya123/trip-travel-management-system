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

// Handle form submission for adding a new route
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_route'])) {
    $r_start = sanitizeInput($_POST['r_start']);
    $r_end = sanitizeInput($_POST['r_end']);

    // Check if both routes are provided
    if (!empty($r_start) && !empty($r_end)) {
        // Insert route into the database
        $stmt = $conn->prepare("INSERT INTO tbl_routes (r_start, r_end) VALUES (?, ?)");
        $stmt->bind_param("ss", $r_start, $r_end);

        if ($stmt->execute()) {
            $message = "Route added successfully!";
        } else {
            $message = "Error adding route: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Please provide both starting and ending locations.";
    }
}

// // Handle route deletion
// if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_route'])) {
//     $delete_id = intval($_POST['delete_id']);

//     // Delete route from the database
//     $stmt = $conn->prepare("DELETE FROM tbl_routes WHERE r_id = ?");
//     $stmt->bind_param("i", $delete_id);

//     if ($stmt->execute()) {
//         $message = "Route deleted successfully!";
//     } else {
//         $message = "Error deleting route: " . $stmt->error;
//     }
//     $stmt->close();
// }
// Handle location deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['r_id'])) {
    $r_id = sanitizeInput($_GET['r_id']);
    
    // Delete location from the database
    $stmt = $conn->prepare("DELETE FROM tbl_routes WHERE r_id = ?");
    $stmt->bind_param("i", $r_id);
    
    if ($stmt->execute()) {
        $message = "route deleted successfully!";
    } else {
        $message = "Error deleting location: " . $stmt->error;
    }
    $stmt->close();
}


// Fetch all routes from the database
$routes = $conn->query("SELECT * FROM tbl_routes");

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Route - Trip and Travel Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-size: cover;
            background-position: center;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 1rem;
            text-align: center;
            width: 100%;
        }
        h1 {
            margin-bottom: 20px;
            color: #fff;
            text-align: center;
        }
        form {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        label {
            margin-top: 10px;
            font-weight: bold;
            display: block;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #28a745; /* Green */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            width: 80%;
        }
        button:hover {
            background-color: #218838; /* Darker green on hover */
        }
        .message {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            margin-top: 20px;
            border-collapse: collapse;
            width: 100%;
            max-width: 600px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .delete-button {
            background-color: #dc3545; /* Red */
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        .delete-button:hover {
            background-color: #c82333; /* Darker red on hover */
        }
    </style>
</head>
<body>

<header>
    <h1>Add Route</h1>
</header>

<?php if (isset($message)) : ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST" action="add_route.php">
    <label for="r_start">Route Start:</label>
    <input type="text" name="r_start" id="r_start" required placeholder="Enter Starting Location">

    <label for="r_end">Route End:</label>
    <input type="text" name="r_end" id="r_end" required placeholder="Enter Ending Location">

    <button type="submit" name="add_route">Add Route</button>
</form>

<!-- Display the routes table -->
<h2>Existing Routes</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Starting Location</th>
            <th>Ending Location</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($routes && $routes->num_rows > 0) : ?>
            <?php while ($row = $routes->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $row['r_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['r_start']); ?></td>
                    <td><?php echo htmlspecialchars($row['r_end']); ?></td>
                   
                        <!-- <form method="POST" action="add_route.php" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $row['r_id']; ?>">
                            <button type="submit" class="delete-button" name="delete_route" onclick="return confirm('Are you sure you want to delete this route?');">Delete</button>
                        </form> -->
                      <?php  echo"  <td><a href='add_route.php?action=delete&r_id=" . htmlspecialchars($row['r_id']) . "' onclick=\"return confirm('Are you sure you want to delete this route?');\">
                                <button class='delete-button'>Delete</button>
                            </a>
                    </td>"?>
                </tr>
            <?php endwhile; ?>
        <?php else : ?>
            <tr>
                <td colspan="4">No routes found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
