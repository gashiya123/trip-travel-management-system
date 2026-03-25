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

// Initialize variables for error handling and success message
$countryName = "";
$countryDescription = "";
$countryStatus = "active"; // Default status
$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $countryName = sanitizeInput($_POST['country_name']);
    $countryDescription = sanitizeInput($_POST['country_description']);
    $countryStatus = sanitizeInput($_POST['country_status']);

    // Validate the input
    if (empty($countryName)) {
        $message = "Country name is required.";
    } else {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO tbl_country (c_name, c_description, c_status) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $countryName, $countryDescription, $countryStatus);

        // Execute the statement
        if ($stmt->execute()) {
            $message = "New country added successfully.";
            // Clear the input fields after successful submission
            $countryName = "";
            $countryDescription = "";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Fetch all countries from the database
$countries = $conn->query("SELECT * FROM tbl_country");

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Country - Trip and Travel Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 1rem;
            text-align: center;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .message {
            color: red;
            text-align: center;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<header>
    <h1>Add Country</h1>
</header>

<div class="container">
    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    
    <form method="POST" action="">
        <label for="country_name">Country Name:</label>
        <input type="text" id="country_name" name="country_name" value="<?php echo $countryName; ?>" required>

        <label for="country_description">Description:</label>
        <textarea id="country_description" name="country_description" rows="4"><?php echo $countryDescription; ?></textarea>

        <label for="country_status">Status:</label>
        <select id="country_status" name="country_status">
            <option value="active" <?php echo ($countryStatus == "active") ? 'selected' : ''; ?>>Active</option>
            <option value="inactive" <?php echo ($countryStatus == "inactive") ? 'selected' : ''; ?>>Inactive</option>
        </select>

        <input type="submit" value="Add Country">
    </form>

    <!-- Display the countries table -->
    <h2>Existing Countries</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Country Name</th>
                <th>Description</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($countries && $countries->num_rows > 0): ?>
                <?php while ($row = $countries->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['c_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['c_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['c_description']); ?></td>
                        <td><?php echo htmlspecialchars($row['c_status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No countries found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
