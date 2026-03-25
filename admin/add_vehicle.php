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

// Handle form submission for adding a vehicle
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_vehicle'])) {
    $v_name = sanitizeInput($_POST['v_name']);
    $v_description = sanitizeInput($_POST['v_description']);
    $cat_id = sanitizeInput($_POST['cat_id']); // Get the selected category ID

    // Insert vehicle into the database
    $stmt = $conn->prepare("INSERT INTO tbl_vehicle (v_name, v_description, cat_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $v_name, $v_description, $cat_id);

    if ($stmt->execute()) {
        $message = "Vehicle added successfully!";
    } else {
        $message = "Error adding vehicle: " . $stmt->error;
    }
    $stmt->close();
}

// Handle deletion of a vehicle
if (isset($_GET['delete_id'])) {
    $delete_id = sanitizeInput($_GET['delete_id']);

    // Delete vehicle from the database
    $stmt = $conn->prepare("DELETE FROM tbl_vehicle WHERE v_id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $message = "Vehicle deleted successfully!";
    } else {
        $message = "Error deleting vehicle: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch categories for the select dropdown
$categories = $conn->query("SELECT cat_id, cat_name FROM tbl_category");

// Fetch vehicles for display in the DataTable
$vehicles = $conn->query("SELECT v_id, v_name, v_description, cat_id FROM tbl_vehicle");

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vehicle - Trip and Travel Management</title>
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
            margin-bottom: 20px;
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
            background-color: #f2f2f2;
        }
        .delete-button {
            background-color: #dc3545; /* Red */
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-button:hover {
            background-color: #c82333; /* Darker red on hover */
        }
    </style>
</head>
<body>

<header>
    <h1>Add Vehicle</h1>
</header>

<?php if (isset($message)) : ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST" action="add_vehicle.php">
    <label for="category">Select Category:</label>
    <select name="cat_id" id="category" required>
        <option value="">--Select Category--</option>
        <?php
        if ($categories->num_rows > 0) {
            while ($row = $categories->fetch_assoc()) {
                echo "<option value='" . $row['cat_id'] . "'>" . $row['cat_name'] . "</option>";
            }
        }
        ?>
    </select>

    <label for="v_name">Vehicle Name:</label>
    <input type="text" name="v_name" id="v_name" required>

    <label for="v_description">Vehicle Description:</label>
    <textarea name="v_description" id="v_description" required></textarea>

    <button type="submit" name="add_vehicle">Add Vehicle</button>
</form>

<!-- DataTable for displaying vehicles -->
<table>
    <thead>
        <tr>
            <th>Vehicle Name</th>
            <th>Description</th>
            <th>Category ID</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($vehicles->num_rows > 0) {
            while ($row = $vehicles->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['v_name'] . "</td>";
                echo "<td>" . $row['v_description'] . "</td>";
                echo "<td>" . $row['cat_id'] . "</td>";
                echo "<td><a href='add_vehicle.php?delete_id=" . $row['v_id'] . "' class='delete-button' onclick=\"return confirm('Are you sure you want to delete this vehicle?');\">Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No vehicles found.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>
