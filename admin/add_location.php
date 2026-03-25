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

// Handle form submission
$message = ""; // Initialize message variable
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'add') {
    $l_name = sanitizeInput($_POST['l_name']);
    $l_details = sanitizeInput($_POST['l_details']);
    $c_id = sanitizeInput($_POST['c_id']); // Get the selected country ID

    // Insert location into the database
    $stmt = $conn->prepare("INSERT INTO tbl_location (c_id, l_name, l_details) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $c_id, $l_name, $l_details);

    if ($stmt->execute()) {
        $message = "Location added successfully!";
    } else {
        $message = "Error adding location: " . $stmt->error;
    }
    $stmt->close();
}

// Handle location deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['l_id'])) {
    $l_id = sanitizeInput($_GET['l_id']);
    
    // Delete location from the database
    $stmt = $conn->prepare("DELETE FROM tbl_location WHERE l_id = ?");
    $stmt->bind_param("i", $l_id);
    
    if ($stmt->execute()) {
        $message = "Location deleted successfully!";
    } else {
        $message = "Error deleting location: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch countries for the select dropdown
$countries = $conn->query("SELECT c_id, c_name FROM tbl_country");

// Fetch existing locations for the data table
$locations = $conn->query("SELECT l.l_id, l.l_name, l.l_details, c.c_name FROM tbl_location l JOIN tbl_country c ON l.c_id = c.c_id");

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Location - Trip and Travel Management</title>
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
            align-items: center; /* Center content horizontally */
            padding: 20px; /* Add some padding */
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 1rem;
            text-align: center;
            width: 100%;
        }
        h1 {
            margin: 20px 0;
            color: #fff;
            text-align: center; /* Center the header text */
        }
        form {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px; /* Set a max width for the form */
        }
        label {
            margin-top: 10px;
            font-weight: bold;
            display: block;
        }
        input[type="text"],
        select,
        textarea {
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
            width: 100%; /* Full width button */
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
    <h1>Add Location</h1>
</header>

<?php if (isset($message)) : ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST" action="add_location.php">
    <label for="country">Select Country:</label>
    <select name="c_id" id="country" required>
        <option value="">--Select Country--</option>
        <?php
        if ($countries->num_rows > 0) {
            while ($row = $countries->fetch_assoc()) {
                echo "<option value='" . $row['c_id'] . "'>" . $row['c_name'] . "</option>";
            }
        }
        ?>
    </select>

    <label for="l_name">Location Name:</label>
    <input type="text" name="l_name" id="l_name" required>

    <label for="l_details">Location Details:</label>
    <textarea name="l_details" id="l_details" required></textarea>

    <input type="hidden" name="action" value="add"> <!-- Hidden input to differentiate actions -->
    <button type="submit">Add Location</button>
</form>

<!-- Display Existing Locations -->
<h2>Existing Locations</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Location Name</th>
            <th>Details</th>
            <th>Country</th>
            <th>Action</th> <!-- Added Action column -->
        </tr>
    </thead>
    <tbody>
        <?php
        // Display locations
        if ($locations && $locations->num_rows > 0) {
            while ($row = $locations->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['l_id']) . "</td>
                        <td>" . htmlspecialchars($row['l_name']) . "</td>
                        <td>" . htmlspecialchars($row['l_details']) . "</td>
                        <td>" . htmlspecialchars($row['c_name']) . "</td>
                        <td>
                            <a href='add_location.php?action=delete&l_id=" . htmlspecialchars($row['l_id']) . "' onclick=\"return confirm('Are you sure you want to delete this location?');\">
                                <button class='delete-button'>Delete</button>
                            </a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No locations found.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>
