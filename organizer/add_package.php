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

// Initialize variables for form values and errors
$packageName = $vehicleId = $routeId = $locationId = $seats = $rate = $numberOfDays = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $packageName = $_POST['package_name'];
    $vehicleId = $_POST['vehicle_id'];
    $routeId = $_POST['route_id'];
    $locationId = $_POST['location_id'];
    $seats = $_POST['seats'];
    $rate = $_POST['rate'];
    $numberOfDays = $_POST['number_of_days']; // Added this line
    $packageImage = $_POST['pack_img']; // Get the image from the dropdown

    // Validate form data
    if (empty($packageName)) {
        $errors[] = 'Package name is required.';
    }
    if (empty($vehicleId)) {
        $errors[] = 'Vehicle must be selected.';
    }
    if (empty($routeId)) {
        $errors[] = 'Route must be selected.';
    }
    if (empty($locationId)) {
        $errors[] = 'Location must be selected.';
    }
    if (empty($seats) || !is_numeric($seats)) {
        $errors[] = 'Valid seat number is required.';
    }
    if (empty($rate) || !is_numeric($rate)) {
        $errors[] = 'Valid rate is required.';
    }
    if (empty($numberOfDays) || !is_numeric($numberOfDays) || $numberOfDays <= 0) {
        $errors[] = 'Valid number of days is required.';
    }
    if (empty($packageImage)) {
        $errors[] = 'Package image is required.';
    }

    // Proceed if there are no errors
    if (empty($errors)) {
        // Insert package into the database
        $query = "INSERT INTO tbl_assign (v_id, r_id, l_id, seats, rate, number_of_days, pack_img) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iiidiss', $vehicleId, $routeId, $locationId, $seats, $rate, $numberOfDays, $packageImage);

        if ($stmt->execute()) {
            // Set a session message for success
            $_SESSION['message'] = 'Package added successfully!';
            
            // Redirect to package list page
            header("Location: package_list.php");
            exit;
        } else {
            $errors[] = 'Error adding package. Please try again.';
        }
    }
}

// Fetch vehicles, routes, and locations for the form dropdowns
$vehicles = getAllVehicles($conn);
$routes = getAllRoutes($conn);
$locations = getAllLocations($conn);

// Fetch images for the package
$images_dir = '../uploads/'; // Path to image folder
$images = array_diff(scandir($images_dir), array('.', '..')); // Filter out current and parent directory

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Package</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        header {
            background: #87CEFA;
            color: white;
            padding: 10px 0;
            text-align: center;
        }

        main {
            padding: 20px;
            max-width: 600px;
            margin: auto;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-top: 0;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="text"], input[type="number"], select {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #87CEFA;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<header>
    <h1>Add New Package</h1>
</header>

<main>
    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="add_package.php" method="POST" enctype="multipart/form-data">
        <label for="package_name">Package Name:</label>
        <input type="text" id="package_name" name="package_name" value="<?php echo htmlspecialchars($packageName); ?>" required>

        <label for="vehicle_id">Select Vehicle:</label>
        <select id="vehicle_id" name="vehicle_id" required>
            <option value="">Choose Vehicle</option>
            <?php foreach ($vehicles as $vehicle): ?>
                <option value="<?php echo $vehicle['v_id']; ?>" <?php if ($vehicleId == $vehicle['v_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($vehicle['v_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="route_id">Select Route:</label>
        <select id="route_id" name="route_id" required>
            <option value="">Choose Route</option>
            <?php foreach ($routes as $route): ?>
                <option value="<?php echo $route['r_id']; ?>" <?php if ($routeId == $route['r_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($route['r_start']) . ' - ' . htmlspecialchars($route['r_end']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="location_id">Select Location:</label>
        <select id="location_id" name="location_id" required>
            <option value="">Choose Location</option>
            <?php foreach ($locations as $location): ?>
                <option value="<?php echo $location['l_id']; ?>" <?php if ($locationId == $location['l_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($location['l_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="seats">Number of Seats:</label>
        <input type="number" id="seats" name="seats" value="<?php echo htmlspecialchars($seats); ?>" required>

        <label for="rate">Rate:</label>
        <input type="number" id="rate" name="rate" value="<?php echo htmlspecialchars($rate); ?>" required>

        <label for="number_of_days">Number of Days:</label>
        <input type="number" id="number_of_days" name="number_of_days" value="<?php echo htmlspecialchars($numberOfDays); ?>" required>

        <label for="pack_img">Select Package Image:</label>
        <select id="pack_img" name="pack_img" required>
            <option value="">Choose Image</option>
            <?php foreach ($images as $image): ?>
                <option value="<?php echo htmlspecialchars($image); ?>">
                    <?php echo htmlspecialchars($image); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="submit" value="Add Package">
    </form>
</main>
</body>
</html>
