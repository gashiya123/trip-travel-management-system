<?php
session_start();
include '../includes/config.php';

// Check if the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faq_id = $_POST['faq_id'];
    $answer = $_POST['answer'];

    // Update the answer in the database
    $query = "UPDATE tbl_faq SET answer = ? WHERE faq_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $answer, $faq_id);

    if ($stmt->execute()) {
        // Redirect back to admin_faq.php after successful answer submission
        header('Location: admin_faq.php?success=1');
        exit();
    } else {
        // Handle error
        echo "Error updating answer: " . $conn->error;
    }
} else {
    // Fetch the question to be answered
    $faq_id = $_GET['faq_id'];
    $query = "SELECT * FROM tbl_faq WHERE faq_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $faq_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $faq = $result->fetch_assoc();

    if (!$faq) {
        echo "FAQ not found.";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Answer FAQ - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            color: #333;
            border-bottom: 2px solid green;
            padding-bottom: 10px;
        }
        label {
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
        }
        textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            resize: none; /* Disable resizing */
        }
        input[type="submit"] {
            background-color: green;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: darkgreen; /* Darker blue */
        }
        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: green;
            font-weight: bold;
            border: 1px solid green;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }
        a:hover {
            background-color: green;
            color: white;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Answer FAQ</h1>
        <form method="POST" action="">
            <input type="hidden" name="faq_id" value="<?php echo $faq['faq_id']; ?>">
            <label for="question">Question:</label>
            <p><?php echo htmlspecialchars($faq['question']); ?></p>
            <label for="answer">Your Answer:</label>
            <textarea id="answer" name="answer" required></textarea>
            <input type="submit" value="Submit Answer">
        </form>
        <a href="admin_faq.php">Back to FAQs</a>
    </div>
</body>
</html>
