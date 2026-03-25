<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip and Travel Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('uploads/wood1.jpg');
            background-size: cover;
            color: white;
        }
        header {
            background-color: rgba(51, 51, 51, 0.8);
            color: #fff;
            text-align: center;
            padding: 1rem;
        }
        nav {
            text-align: right;
            margin-top: 10px;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }
        nav a:hover {
            text-decoration: underline;
        }
        main {
            padding: 50px;
            text-align: center;
           
            border-radius: 10px;
            margin: 20px auto;
            max-width: 1000px;
        }
        .intro {
            margin: 20px 0;
            font-size: 1.1rem;
        }
        .featured-packages {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin: 20px 0;
            color:white;
        }
        .package {
            background-color: rgba(0, 0, 0, 0.5);
            border: 1px solid #ccc;
            border-radius: 5px;
            margin: 10px;
            padding: 15px;
            width: 300px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .package img {
            max-width: 100%;
            border-radius: 5px;
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
    </style>
</head>
<body>

<header>
    <h1>Welcome to Trip and Travel Management</h1>
    <nav>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
        <a href="about.php">About</a>
        <a href="faq.php">FAQ</a>
        <a href="contact.php">Contact</a>
    </nav>
</header>
<br><br>
<main>
    <div class="intro">
        <h2>Your Journey Starts Here!</h2>
        <p>At Trip and Travel Management, we offer a comprehensive platform to plan and book your trips with ease. Whether you're looking for relaxing beach vacations, thrilling mountain adventures, or cultural city tours, we have something for everyone.</p>
        <p>From tranquil beach getaways to exhilarating mountain treks, our diverse offerings are designed to inspire adventure and relaxation alike. Experience the world like never before with us!</p>
        <p>Join our community of travel enthusiasts and enjoy:</p>
        <ul>
            <li>Seamless online booking for unforgettable travel experiences</li>
            <li>Expert insights and recommendations for each destination</li>
            <li>Personalized service to make your journey hassle-free</li>
            <li>A vibrant community to share your stories and adventures</li>
        </ul>
    </div>
</main>
<br><br><br>
<hr><hr>
<br><br>
<h2>Featured Packages</h2>
<div class="featured-packages">
    <div class="package">
        <img src="https://img.freepik.com/premium-photo/beautiful-tropical-outdoor-nature-landscape-beach-sea-ocean-with-coconut-palm-tree_198067-188663.jpg" alt="Beach Vacation" />
        <h3>Beach Vacation</h3>
        <p>Enjoy the sun and sand at the best beaches.</p>
    </div>
    <div class="package">
        <img src="https://images.unsplash.com/photo-1480497490787-505ec076689f?fm=jpg&q=60&w=3000&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxleHBsb3JlLWZlZWR8MXx8fGVufDB8fHx8fA%3D%3D" alt="Mountain Adventure" />
        <h3>Mountain Adventure</h3>
        <p>Experience thrilling hikes and breathtaking views.</p>
    </div>
    <div class="package">
        <img src="https://c4.wallpaperflare.com/wallpaper/819/348/53/architecture-city-building-travel-wallpaper-preview.jpg" alt="City Tour" />
        <h3>City Tour</h3>
        <p>Explore the rich culture and history of the city.</p>
    </div>
</div>

<br><br><br>
<footer>
    <p>&copy; <?php echo date('Y'); ?> Trip and Travel Management. All rights reserved.</p>
</footer>

<script src="common.js"></script> <!-- Include common.js here -->
</body>
</html>
