<?php
session_start();
include '../includes/config.php';

// Check if the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Fetch all FAQs
$query = "SELECT * FROM tbl_faq"; 
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage FAQs - Admin</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            font-size: 2em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: rgba(0,0,0,0.8);
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        a {
            text-decoration: none;
            color: #28a745;
            padding: 10px 15px;
            border: 1px solid black;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }
        a:hover {
            background-color: green;
            color: white;
        }
        .no-faqs {
            text-align: center;
            padding: 20px;
            font-style: italic;
        }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: white;
            background-color: #28a745;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .back-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <h1>Manage FAQs</h1>
    <table>
        <thead>
            <tr>
                <th>Question</th>
                <th>Answer</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['question']); ?></td>
                        <td>
                            <?php if (!empty($row['answer'])): ?>
                                <?php echo htmlspecialchars($row['answer']); ?>
                            <?php else: ?>
                                <em>No answer yet.</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="process_answer.php?faq_id=<?php echo $row['faq_id']; ?>">Answer</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="no-faqs">No FAQs submitted yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="admin_dashboard.php" class="back-button">Back to Dashboard</a>
</body>
</html>
