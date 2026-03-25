\<?php
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
    $cat_name = sanitizeInput($_POST['cat_name']);
    $cat_description = sanitizeInput($_POST['cat_description']);

    // Insert category into the database
    $stmt = $conn->prepare("INSERT INTO tbl_category (cat_name, cat_description) VALUES (?, ?)");
    $stmt->bind_param("ss", $cat_name, $cat_description);

    if ($stmt->execute()) {
        $message = "Category added successfully!";
    } else {
        $message = "Error adding category: " . $stmt->error;
    }
    $stmt->close();
}

// Handle category deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['cat_id'])) {
    $cat_id = sanitizeInput($_GET['cat_id']);
    
    // Delete category from the database
    $stmt = $conn->prepare("DELETE FROM tbl_category WHERE cat_id = ?");
    $stmt->bind_param("i", $cat_id);
    
    if ($stmt->execute()) {
        $message = "Category deleted successfully!";
    } else {
        $message = "Error deleting category: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch existing categories for the data table
$categories = $conn->query("SELECT cat_id, cat_name, cat_description FROM tbl_category");

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category - Trip and Travel Management</title>
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
    <h1>Add Category</h1>
</header>

<?php if (isset($message)) : ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST" action="add_category.php">
    <label for="cat_name">Category Name:</label>
    <input type="text" name="cat_name" id="cat_name" required>

    <label for="cat_description">Category Description:</label>
    <textarea name="cat_description" id="cat_description" required></textarea>

    <input type="hidden" name="action" value="add"> <!-- Hidden input to differentiate actions -->
    <button type="submit">Add Category</button>
</form>

<!-- Display Existing Categories -->
<h2>Existing Categories</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Category Name</th>
            <th>Description</th>
            <th>Action</th> <!-- Added Action column -->
        </tr>
    </thead>
    <tbody>
        <?php
        // Display categories
        if ($categories && $categories->num_rows > 0) {
            while ($row = $categories->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['cat_id']) . "</td>
                        <td>" . htmlspecialchars($row['cat_name']) . "</td>
                        <td>" . htmlspecialchars($row['cat_description']) . "</td>
                        <td>
                            <a href='add_category.php?action=delete&cat_id=" . htmlspecialchars($row['cat_id']) . "' onclick=\"return confirm('Are you sure you want to delete this category?');\">
                                <button class='delete-button'>Delete</button>
                            </a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No categories found.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>
