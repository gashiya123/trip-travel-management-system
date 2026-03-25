

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Trip Advisor and Travel Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Ensures the body takes the full height of the viewport */
        }

        header {
            background: #87CEFA; /* Light blue background */
            color: white;
            padding: 10px 0;
            text-align: center;
        }

        main {
            padding: 20px;
            max-width: 800px;
            margin: auto;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            flex: 1; /* This allows main to grow and take available space */
        }

        h1, h2 {
            color: #007BFF; /* Darker shade of blue for headers */
        }

        .contact-info {
            margin-top: 20px;
            padding: 10px;
            background-color: #e6f7ff; /* Lighter blue for contact info background */
            border-radius: 5px;
            border: 1px solid #b3d7ff; /* Border color matching the lighter background */
        }

        footer {
            text-align: center;
            padding: 10px 0;
            background: #87CEFA; /* Light blue footer */
            color: white;
            width: 100%;
        }
    </style>
</head>
<body>

<header>
    <h1>Contact Us</h1>
</header>

<main>
    <h2>We'd love to hear from you!</h2>
    <p>If you have any questions or feedback, feel free to reach out using the contact information below:</p>

    <div class="contact-info">
        <h3>Contact Information</h3>
        <p><strong>Phone:</strong> +123-456-7890</p>
        <p><strong>Email:</strong> support@tripadvisor.com</p>
        <p><strong>Address:</strong> 123 Travel St, Suite 456, City, State, ZIP</p>
    </div>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Trip Advisor and Travel Management System. All rights reserved.</p>
</footer>

</body>
</html>
