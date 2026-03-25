<?php
session_start();

// Include the configuration and functions files
include 'includes/config.php';
include 'includes/functions.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Create a database connection
    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname); // Use constants from config.php

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and bind the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT user_id, user_password, user_role FROM tbl_user WHERE user_name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password, $user_role);
        $stmt->fetch();

        // Verify the password
        if ($password === $hashed_password) { // Using plain password comparison, adjust if needed
            // Set session variables
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_role'] = $user_role;

            // Redirect to dashboard based on user role
            if ($user_role === 'admin') {
                header("Location: admin/admin_dashboard.php");
            } elseif ($user_role === 'organizer') {
                header("Location: organizer/organizer_dashboard.php");
            } elseif ($user_role === 'customer') {
                header("Location: customer/customer_dashboard.php");
            }
            exit();
        } else {
            $error_message = "Invalid password!";
        }
    } else {
        $error_message = "No user found with that username!";
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
    <title>Login - Trip and Travel Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"> <!-- Updated Font Awesome -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('uploads/waterfall.jpg');
            background-size: cover;
            color: #333;
        }
        header {
            background-color: rgba(51, 51, 51, 0.8);
            color: #fff;
            text-align: center;
            padding: 1rem;
        }
        main {
            padding: 50px;
            text-align: center;
            margin: auto;
            max-width: 400px;
        }
        form {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 50px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        input[type="text"], input[type="password"] {
            width: calc(100% - 40px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: inline-block;
        }
        .password-container {
            position: relative;
        }
        .password-container span {
            position: absolute;
            right: 10%;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
        input[type="submit"] {
            background-color: #333;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #555;
        }
        footer {
            background-color: rgba(51, 51, 51, 0.8);
            color: #fff;
            text-align: center;
            padding: 1rem;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>

<header>
    <h1>Login to Trip and Travel Management</h1>
</header>

<main>
    <form action="login.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <div class="password-container">
            <input type="password" name="password" id="password" placeholder="Password" required>
            <span id="togglePassword">
                <i class="fas fa-eye" id="eye-icon"></i> <!-- Updated eye icon -->
            </span>
        </div>
        <br><br>
        <input type="submit" value="Login">
    </form>
    <p style="color: #fff;">Don't have an account? <a href="register.php" style="color: #fff;">Register here</a></p>
    <?php if (isset($error_message)) echo "<p class='error'>$error_message</p>"; ?>
</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Trip and Travel Management. All rights reserved.</p>
</footer>

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eye-icon');

    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        eyeIcon.classList.toggle('fa-eye');
        eyeIcon.classList.toggle('fa-eye-slash');
    });
</script>

</body>
</html>
