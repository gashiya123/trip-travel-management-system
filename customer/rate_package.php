<?php
session_start();

// Check if the user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
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

// Initialize variables for rating
$rating = null;
$message = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = $_POST['booking_id']; // Booking ID related to the rating
    $rating = $_POST['rating']; // Rating from the form
    $user_id = $_SESSION['user_id']; // Fetch the user ID from session

    // Verify if the user has already booked the package
    $check_booking_query = $conn->prepare("
        SELECT COUNT(*) AS booking_count 
        FROM tbl_booking 
        WHERE user_id = ? AND booking_id = ?
    ");
    $check_booking_query->bind_param("ii", $user_id, $booking_id);
    $check_booking_query->execute();
    $result = $check_booking_query->get_result();
    $row = $result->fetch_assoc();
    $check_booking_query->close(); // Close the check_booking_query statement

    if ($row['booking_count'] > 0) {
        // Check if the user has already rated the package
        $check_rating_query = $conn->prepare("
            SELECT COUNT(*) AS rating_count 
            FROM tbl_rating 
            WHERE booking_id = ? AND user_id = ?
        ");
        $check_rating_query->bind_param("ii", $booking_id, $user_id);
        $check_rating_query->execute();
        $rating_result = $check_rating_query->get_result();
        $rating_row = $rating_result->fetch_assoc();
        $check_rating_query->close(); // Close the check_rating_query statement

        if ($rating_row['rating_count'] == 0) {
            // If the user has not rated yet, insert the rating
            $insert_rating_query = $conn->prepare("
                INSERT INTO tbl_rating (booking_id, rating, user_id) 
                VALUES (?, ?, ?)
            ");

            // Check if the query was prepared successfully
            if ($insert_rating_query) {
                $insert_rating_query->bind_param("iii", $booking_id, $rating, $user_id);

                if ($insert_rating_query->execute()) {
                    $message = "Thank you for your rating!";
                } else {
                    $message = "Error submitting your rating. Please try again.";
                }

                // Close the prepared statement after execution
                $insert_rating_query->close();
            } else {
                $message = "Error preparing the query. Please try again.";
            }
        } else {
            $message = "You have already rated this package.";
        }
    } else {
        $message = "You must book this package before rating it.";
    }
}

// Fetch available bookings for the user to rate
$bookings_query = $conn->prepare("
    SELECT b.booking_id, l.l_name, v.v_name 
    FROM tbl_booking b 
    JOIN tbl_assign a ON b.as_id = a.as_id 
    JOIN tbl_vehicle v ON a.v_id = v.v_id 
    JOIN tbl_location l ON a.l_id = l.l_id 
    WHERE b.user_id = ?
");
$bookings_query->bind_param("i", $_SESSION['user_id']);
$bookings_query->execute();
$bookings_result = $bookings_query->get_result();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Package</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            max-width: 500px;
            width: 100%;
        }
        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .rating {
            display: flex;
            margin-bottom: 15px;
        }
        .rating input[type="radio"] {
            display: none; /* Hide the radio button */
        }
        .rating label {
            font-size: 30px;
            color: #ccc;
            cursor: pointer;
            transition: color 0.2s;
        }
        .rating label:hover,
        .rating input[type="radio"]:checked ~ label {
            color: #f39c12; /* Gold color for selected */
        }
        .rating input[type="radio"]:checked + label {
            color: #f39c12; /* Gold color for the checked star */
        }
        input[type="submit"] {
            background-color: #333;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #555;
        }
        .message {
            margin-bottom: 15px;
            font-weight: bold;
            color: #d9534f; /* Bootstrap danger color */
        }
        .selected-rating {
            font-size: 20px;
            margin-top: 10px;
            color: #333;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Rate Your Package</h1>

    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="booking_id">Select Booking:</label>
        <select name="booking_id" required>
            <option value="">Select a booking</option>
            <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($booking['booking_id']); ?>">
                    <?php echo htmlspecialchars($booking['l_name']) . " - " . htmlspecialchars($booking['v_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        
        <label for="rating">Rating:</label>
        <div class="rating" id="rating-stars">
            <input type="radio" id="star1" name="rating" value="1" onclick="updateRating(1)">
            <label for="star1" class="fas fa-star"></label>
            <input type="radio" id="star2" name="rating" value="2" onclick="updateRating(2)">
            <label for="star2" class="fas fa-star"></label>
            <input type="radio" id="star3" name="rating" value="3" onclick="updateRating(3)">
            <label for="star3" class="fas fa-star"></label>
            <input type="radio" id="star4" name="rating" value="4" onclick="updateRating(4)">
            <label for="star4" class="fas fa-star"></label>
            <input type="radio" id="star5" name="rating" value="5" onclick="updateRating(5)">
            <label for="star5" class="fas fa-star"></label>
        </div>

        <div class="selected-rating" id="display-rating">Selected Rating: None</div>

        <input type="submit" value="Submit Rating">
    </form>
</div>

<script>
    function updateRating(rating) {
        document.getElementById('display-rating').innerText = 'Selected Rating: ' + rating;
        // Update the stars based on the selected rating
        const stars = document.querySelectorAll('.rating input[type="radio"]');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.nextElementSibling.style.color = '#f39c12'; // Gold color for selected stars
            } else {
                star.nextElementSibling.style.color = '#ccc'; // Default color for unselected stars
            }
        });
    }
</script>

</body>
</html>
