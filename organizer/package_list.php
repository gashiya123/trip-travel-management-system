<?php
// Include necessary files
include '../includes/config.php'; // Database configuration
include '../includes/functions.php'; // Common functions

session_start();

// Check if user is logged in and is an organizer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'organizer') {
    header("Location: ../login.php"); // Redirect to login if not logged in as organizer
    exit;
}

// Fetch all packages with the package ID
$query = "SELECT a.as_id, a.pack_img, a.seats, a.rate, a.number_of_days, v.v_name, r.r_start, r.r_end, l.l_name 
          FROM tbl_assign a
          JOIN tbl_vehicle v ON a.v_id = v.v_id
          JOIN tbl_routes r ON a.r_id = r.r_id
          JOIN tbl_location l ON a.l_id = l.l_id";
$result = $conn->query($query);

// Handle package deletion
if (isset($_POST['delete_package'])) {
    $package_id = intval($_POST['package_id']); // Get package ID from the form
    $delete_query = "DELETE FROM tbl_assign WHERE as_id = ?";
    
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $package_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Package deleted successfully."; // Set success message
    } else {
        $_SESSION['message'] = "Error deleting package."; // Set error message
    }
    
    $stmt->close();
    header("Location: package_list.php"); // Redirect back to the package list
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        header {
            background-color: #87CEFA;
            color: white;
            padding: 10px 0;
            text-align: center;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .package-card {
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 10px;
            width: 300px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth transition for transform and shadow */
        }

        .package-card:hover {
            transform: translateY(-10px); /* Slight lift on hover */
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2); /* Add stronger shadow on hover */
        }

        .package-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }

        .package-details {
            margin-top: 10px;
        }

        .package-details h3 {
            margin: 0 0 10px;
            font-size: 1.5rem;
        }

        .package-details p {
            margin: 5px 0;
            color: #555;
        }

        .success-message {
            text-align: center;
            margin-bottom: 20px;
            color: green;
        }

        .delete-button {
            background-color: #e74c3c; /* Red color */
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .delete-button:hover {
            background-color: #c0392b; /* Darker red on hover */
        }
    </style>
</head>
<body>

<header>
    <h1>Package List</h1>
</header>

<main>
    <?php
    // Display success message if set in session
    if (isset($_SESSION['message'])) {
        echo "<p class='success-message'>" . $_SESSION['message'] . "</p>";
        unset($_SESSION['message']); // Remove the message after displaying it
    }
    ?>

    <div class="container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="package-card">
                    <img src="../uploads/<?php echo htmlspecialchars($row['pack_img']); ?>" alt="Package Image">
                    <div class="package-details">
                        <h3><?php echo htmlspecialchars($row['l_name']); ?></h3>
                        <p><strong>Route:</strong> <?php echo htmlspecialchars($row['r_start']) . " - " . htmlspecialchars($row['r_end']); ?></p>
                        <p><strong>Vehicle:</strong> <?php echo htmlspecialchars($row['v_name']); ?></p>
                        <p><strong>Seats:</strong> <?php echo htmlspecialchars($row['seats']); ?></p>
                        <p><strong>Rate:</strong> $<?php echo htmlspecialchars($row['rate']); ?></p>
                        <p><strong>Number of Days:</strong> <?php echo htmlspecialchars($row['number_of_days']); ?></p>

                        <!-- Delete button with confirmation -->
                        <form method="POST" action="" onsubmit="return confirmDelete();">
                            <input type="hidden" name="package_id" value="<?php echo htmlspecialchars($row['as_id']); ?>">
                            <button type="submit" name="delete_package" class="delete-button">Delete Package</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No packages available.</p>
        <?php endif; ?>
    </div>
</main>

<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this package? This action cannot be undone.");
    }
</script>

</body>
</html>
