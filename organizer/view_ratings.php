<?php
session_start();
include '../includes/config.php'; // Database connection
include '../includes/functions.php'; // Custom functions

// Check if user is logged in as an organizer
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // Redirect to login if not logged in
    exit;
}

// Fetch ratings, associated package details, and location from the database
$query = "
    SELECT r.rate_id, r.rating, u.user_name, a.pack_img, l.l_name AS location_name
    FROM tbl_rating r 
    JOIN tbl_user u ON r.user_id = u.user_id 
    JOIN tbl_booking b ON r.booking_id = b.booking_id 
    JOIN tbl_assign a ON b.as_id = a.as_id
    JOIN tbl_location l ON a.l_id = l.l_id"; // Linking tbl_rating to tbl_location

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Ratings</title>
    <!-- Include Font Awesome for star icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css"> <!-- Link to your CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        /* Card layout */
        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }

        .card {
            width: 300px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            text-align: center;
            padding: 15px;
        }

        .card img {
            max-width: 100%;
            height: auto;
            border-bottom: 1px solid #ddd;
        }

        .card h3 {
            margin: 10px 0;
            font-size: 1.2em;
            color: #333;
        }

        .card p {
            color: #555;
            margin: 5px 0;
        }

        /* Style for star rating */
        .star-rating {
            color: #FFD700; /* Gold color */
            font-size: 1.2em;
        }

        .btn-back {
            margin-top: 20px;
            text-align: center;
        }

        .btn-back a {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: rgba(0, 128, 0, 0.8);
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-back a:hover {
            background-color: darkgreen;
            transform: scale(1.05);
        }

        .btn-back a:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Customer Ratings and Reviews</h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="card-container">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <img src="../uploads/<?php echo htmlspecialchars($row['pack_img']); ?>" alt="Package Image">
                   

                    <h3><?php echo htmlspecialchars($row['location_name']); ?></h3>
                    <p>customer name: <?php echo htmlspecialchars($row['user_name']); ?></p>


             
                    <div class="star-rating">
                        <?php
                        // Display stars based on rating
                        $rating = intval($row['rating']);
                        for ($i = 0; $i < 5; $i++) {
                            echo $i < $rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                        }
                        ?>
                    </div>
                   
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No ratings found.</p>
    <?php endif; ?>

    <div class="btn-back">
        <a href="organizer_dashboard.php">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
