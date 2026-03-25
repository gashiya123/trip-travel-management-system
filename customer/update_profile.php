<?php
session_start();
include '../includes/config.php'; // Database connection
include '../includes/functions.php'; // Custom functions

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

// Get the logged-in user's ID
$userId = $_SESSION['user_id'];

// Fetch user profile data from the database
$query = "SELECT user_id, user_name, user_email, user_phone, user_address, user_age, user_gender FROM tbl_user WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc(); // Fetch user data
} else {
    echo "No user found.";
    exit;
}

// Process form submission
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_name = trim($_POST['user_name']);
    $user_email = trim($_POST['user_email']);
    $user_phone = trim($_POST['user_phone']);
    $user_address = trim($_POST['user_address']);
    $user_age = trim($_POST['user_age']);
    $user_gender = trim($_POST['user_gender']);

    // Validate form inputs
    if (empty($user_name)) {
        $errors[] = "Name is required.";
    }
    if (empty($user_email) || !filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if (empty($user_phone) || !preg_match('/^[0-9]{10}$/', $user_phone)) {
        $errors[] = "Valid 10-digit phone number is required.";
    }
    if (empty($user_address)) {
        $errors[] = "Address is required.";
    }
    if (empty($user_age) || !is_numeric($user_age)) {
        $errors[] = "Age must be a number.";
    }

    // If no errors, update the profile in the database
    if (empty($errors)) {
        $stmt = $conn->prepare("
            UPDATE tbl_user 
            SET user_name = ?, user_email = ?, user_phone = ?, user_address = ?, user_age = ?, user_gender = ? 
            WHERE user_id = ?
        ");
        $stmt->bind_param('ssssisi', $user_name, $user_email, $user_phone, $user_address, $user_age, $user_gender, $userId);

        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
        } else {
            $errors[] = "Error updating profile: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 5px;
        }

        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 5px;
        }

        .btn {
            display: block;
            width: 100%;
            background-color: green;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: darkgreen;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .success {
            color: green;
            margin-bottom: 15px;
        }

        .btn-back {
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        .btn-back a {
            text-decoration: none;
            color: green;
            font-size: 16px;
        }

        .btn-back a:hover {
            color: darkgreen;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Update Profile</h2>

    <!-- Success Message -->
    <?php if (!empty($success)): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Error Messages -->
    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Update Profile Form -->
    <form action="update_profile.php" method="POST">
        <div class="form-group">
            <label for="user_name">Name</label>
            <input type="text" id="user_name" name="user_name" value="<?php echo htmlspecialchars($user['user_name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="user_email">Email</label>
            <input type="email" id="user_email" name="user_email" value="<?php echo htmlspecialchars($user['user_email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="user_phone">Phone</label>
            <input type="text" id="user_phone" name="user_phone" value="<?php echo htmlspecialchars($user['user_phone']); ?>" required>
        </div>

        <div class="form-group">
            <label for="user_address">Address</label>
            <input type="text" id="user_address" name="user_address" value="<?php echo htmlspecialchars($user['user_address']); ?>" required>
        </div>

        <div class="form-group">
            <label for="user_age">Age</label>
            <input type="number" id="user_age" name="user_age" value="<?php echo htmlspecialchars($user['user_age']); ?>" required>
        </div>

        <div class="form-group">
            <label for="user_gender">Gender</label>
            <select id="user_gender" name="user_gender">
                <option value="Male" <?php if ($user['user_gender'] == 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if ($user['user_gender'] == 'Female') echo 'selected'; ?>>Female</option>
                <option value="Other" <?php if ($user['user_gender'] == 'Other') echo 'selected'; ?>>Other</option>
            </select>
        </div>

        <button type="submit" class="btn">Update Profile</button>
    </form>

    <div class="btn-back">
        <a href="customer_dashboard.php">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
