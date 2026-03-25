<?php
session_start();
include 'includes/config.php';

// Fetch all FAQs
$query = "SELECT * FROM tbl_faq";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQs - Trip Advisor and Travel Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 30px;
            font-size: 2.5em;
        }
        ul {
            list-style-type: none;
            padding: 0;
            max-width: 800px;
            margin: 0 auto;
        }
        li {
            margin: 15px 0;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        li:hover {
            transform: translateY(-2px);
        }
        strong {
            color: #555;
        }
        a {
            display: inline-block;
            margin: 20px auto;
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            text-align: center;
            padding: 10px 20px;
            background-color: #007bff;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        a:hover {
            background-color: #0056b3;
        }
        .faq-container {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <h1>Frequently Asked Questions</h1>
    <div class="faq-container">
        <a href="submit_faq.php">Submit a Question</a>
        <ul>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li>
                        <strong>Question:</strong> <?php echo htmlspecialchars($row['question']); ?>
                        <?php if (!empty($row['answer'])): ?>
                            <br><strong>Answer:</strong> <?php echo htmlspecialchars($row['answer']); ?>
                        <?php else: ?>
                            <br><strong>Answer:</strong> <em>No answer yet.</em>
                        <?php endif; ?>
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <li>No FAQs submitted yet.</li>
            <?php endif; ?>
            <!-- Additional Simple Questions -->
            <li>
                <strong>Question:</strong> Is there any option to cancel the booking?
                <br><strong>Answer:</strong> Yes, you can cancel your booking through your account settings in the 'view booked package' section.
            </li>
            <li>
                <strong>Question:</strong> How can i book a package?
                <br><strong>Answer:</strong> you can book your desired package from 'book package' section.
            </li>
            
            <li>
                <strong>Question:</strong> How can I contact customer support?
                <br><strong>Answer:</strong> You can reach customer support via the 'Contact Us' page.
            </li>
        </ul>
    </div>
</body>
</html>
