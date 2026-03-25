<?php
// Start the session
session_start();

// Include the configuration and functions files
include 'includes/config.php';
include 'includes/functions.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $username = $_POST['username'];
    $address = $_POST['address'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Get the role
    $status = 'active'; // Default status

    // Debugging statements to check captured values
    echo "Username: $username<br>";
    echo "Address: $address<br>";
    echo "Age: $age<br>";
    echo "Gender: $gender<br>";
    echo "Phone: $phone<br>";
    echo "Email: $email<br>";
    echo "Role: $role<br>";

    // Check if the email already exists
    if (emailExists($conn, $email)) {
        $_SESSION['message'] = "This email is already registered. Please use a different email.";
        header("Location: register.php"); // Redirect back to the registration page
        exit; // Stop further execution
    }

    // Only check for existing users if the role is admin or organizer
    if ($role === 'admin' || $role === 'organizer') {
        // Check if the role already has a registered user
        $roleCheckStmt = $conn->prepare("SELECT COUNT(*) FROM tbl_user WHERE user_role = ?");
        $roleCheckStmt->bind_param("s", $role);
        $roleCheckStmt->execute();
        $roleCheckStmt->bind_result($roleCount);
        $roleCheckStmt->fetch();
        $roleCheckStmt->close();

        // If a user with the same role already exists
        if ($roleCount > 0) {
            $_SESSION['message'] = "Only one user is permitted to register as $role. Please contact the administrator.";
            header("Location: register.php"); // Redirect back to the registration page
            exit; // Stop further execution
        }
    }

    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("INSERT INTO tbl_user (user_name, user_address, user_age, user_gender, user_phone, user_email, user_status, user_password, user_role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssississs", $username, $address, $age, $gender, $phone, $email, $status, $password, $role); // No password hash since you've requested not to hash passwords

    // Execute the statement and check for success
    if ($stmt->execute()) {
        $_SESSION['message'] = "Registration successful!";
        header("Location: login.php"); // Redirect to login page
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Trip and Travel Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('uploads/flower.avif');
            background-size: cover;
            background-position: center;
            color: white;
        }
        .register-container {
            background-color: rgba(105, 105, 105, 0.5);
            border-radius: 10px;
            padding: 40px;
            width: 600px;
            margin: 100px auto;
        }
        h1 {
            text-align: center;
        }
        input, select {
            width:80%;
            padding: 10px;
            margin: 20px 0;
        }
        button {
            width: 20%;
            padding: 10px;
            color:white;
            background-color: black;
        }
    </style>
</head>
<body>

<div class="register-container">
    <h1 style="color:black">Register</h1>
    <?php
    // Display session message if it exists
    if (isset($_SESSION['message'])) {
        echo "<p style='color: red; text-align: center;'>".$_SESSION['message']."</p>";
        unset($_SESSION['message']);
    }
    ?>
    <form method="POST" action="register.php">
        <input type="text" name="username" placeholder="Username" required>
        <input type="text" name="address" placeholder="Address" required>
        <input type="number" name="age" placeholder="Age" required>
        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="">Select Role</option>
            <option value="admin">Admin</option>
            <option value="organizer">Organizer</option>
            <option value="customer">Customer</option>
        </select>
        <button type="submit">Register</button>
    </form>
</div>

<script src="register.js"></script>
<script src="common.js"></script>
</body>
</html>
