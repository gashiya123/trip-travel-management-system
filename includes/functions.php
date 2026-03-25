<?php
// Include the database connection
include __DIR__ . '/config.php'; // instead of include 'includes/config.php';

// Function to sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to display messages
function displayMessage($message) {
    if (isset($message)) {
        echo "<p style='color: red; text-align: center;'>".$message."</p>";
    }
}

// Function to get total users excluding admins and organizers
function getTotalUsers($conn) {
    $result = $conn->query("SELECT COUNT(*) as count FROM tbl_user WHERE user_role = 'customer'");
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Function to get total vehicles
function getTotalVehicles($conn) {
    $result = $conn->query("SELECT COUNT(*) as count FROM tbl_vehicle");
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Function to get total routes
function getTotalRoutes($conn) {
    $result = $conn->query("SELECT COUNT(*) as count FROM tbl_routes");
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Function to get total bookings
function getTotalBookings($conn) {
    $result = $conn->query("SELECT COUNT(*) as count FROM tbl_booking");
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Organizer dashboard
// Function to get detailed assignments with related vehicle, route, and location info
function getDetailedAssignments($conn) {
    $stmt = $conn->prepare("
        SELECT 
            a.as_id, 
            a.seats, 
            a.rate, 
            a.number_of_days, 
            v.v_name, 
            r.r_start, 
            r.r_end, 
            l.l_name, 
            l.l_details, 
            a.pack_img 
        FROM 
            tbl_assign a 
        JOIN 
            tbl_vehicle v ON a.v_id = v.v_id 
        JOIN 
            tbl_routes r ON a.r_id = r.r_id 
        JOIN 
            tbl_location l ON a.l_id = l.l_id
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $assignments = [];
    while ($row = $result->fetch_assoc()) {
        $assignments[] = $row;
    }
    return $assignments;
}

// Function to get all vehicles
function getAllVehicles($conn) {
    $result = $conn->query("SELECT * FROM tbl_vehicle");
    $vehicles = [];
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
    return $vehicles;
}

// Function to get all routes
function getAllRoutes($conn) {
    $result = $conn->query("SELECT * FROM tbl_routes");
    $routes = [];
    while ($row = $result->fetch_assoc()) {
        $routes[] = $row;
    }
    return $routes;
}

// Function to get all locations
function getAllLocations($conn) {
    $result = $conn->query("SELECT * FROM tbl_location");
    $locations = [];
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row;
    }
    return $locations;
}

// Function to get all countries
function getAllCountries($conn) {
    $result = $conn->query("SELECT * FROM tbl_country");
    $countries = [];
    while ($row = $result->fetch_assoc()) {
        $countries[] = $row;
    }
    return $countries;
}

/* Function to get total bookings
function getTotalBookings($conn) {
    $result = $conn->query("SELECT COUNT(*) as count FROM tbl_booking");
    $row = $result->fetch_assoc();
    return $row['count'];
}*/

// Function to get all available packages
function getAllPackages($conn) {
    $query = "
        SELECT a.as_id, a.pack_img, a.rate, l.l_name, v.v_name, r.r_start, r.r_end, a.seats, a.number_of_days 
        FROM tbl_assign a
        JOIN tbl_location l ON a.l_id = l.l_id
        JOIN tbl_vehicle v ON a.v_id = v.v_id
        JOIN tbl_routes r ON a.r_id = r.r_id
    ";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to check if email exists
function emailExists($conn, $email) {
    $stmt = $conn->prepare("SELECT * FROM tbl_user WHERE user_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0; // Returns true if email exists, false otherwise
}

// Function to upload files
function uploadFile($file, $targetDir) {
    // Create the uploads directory if it doesn't exist
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Set the target file path
    $targetFile = $targetDir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if the file is an actual image or a fake image
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return false; // File is not an image
    }

    // Check file size (limit set to 5MB)
    if ($file["size"] > 5000000) {
        return false; // File is too large
    }

    // Allow only certain file formats (jpg, jpeg, png, gif)
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        return false; // Invalid file format
    }

    // Try to upload the file
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return $targetFile; // Return the path to the uploaded file
    } else {
        return false; // Error occurred during upload
    }
}

// Add Package Function (insert into tbl_assign) with Image Upload
function addPackage($conn, $vehicle_id, $route_id, $location_id, $seats, $rate, $number_of_days, $imageFile) {
    // Check if the file is uploaded
    if (empty($imageFile['name'])) {
        return "No file uploaded.";
    }

    // Handle image upload
    $targetDir = "../uploads/"; // Directory to save the image
    $uploadResult = uploadFile($imageFile, $targetDir);

    if ($uploadResult === false) {
        return "Error uploading image.";
    }

    // Insert package details into the database
    $stmt = $conn->prepare("
        INSERT INTO tbl_assign (v_id, r_id, l_id, seats, rate, number_of_days, pack_img) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiiisss", $vehicle_id, $route_id, $location_id, $seats, $rate, $number_of_days, $uploadResult);
    
    if ($stmt->execute()) {
        return "Package added successfully!";
    } else {
        return "Error adding package: " . $stmt->error;
    }
}

// Customer dashboard functions

// Function to get customer details
function getCustomerDetails($conn, $user_id) {
    $stmt = $conn->prepare("SELECT user_name, user_email, user_phone FROM tbl_user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to get customer bookings
function getCustomerBookings($conn, $user_id) {
    $stmt = $conn->prepare("
        SELECT b.as_id, v.v_name, b.date, b.seats 
        FROM tbl_booking b 
        JOIN tbl_vehicle v ON b.v_id = v.v_id 
        WHERE b.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    return $bookings;
}

// Function to cancel a booking
function cancelBooking($conn, $booking_id) {
    $stmt = $conn->prepare("DELETE FROM tbl_booking WHERE as_id = ?");
    $stmt->bind_param("i", $booking_id);
    return $stmt->execute();
}


?>
