<?php
session_start();

// Check if user is not logged in, redirect to register page
if (!isset($_SESSION['username'])) {
    header("Location: register.php");
    exit;
}

// Define a function to safely echo session variables
function echoSessionVar($varName) {
    if (isset($_SESSION[$varName])) {
        echo htmlspecialchars($_SESSION[$varName], ENT_QUOTES, 'UTF-8');
    } else {
        echo "Not available";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page - FableFoundry</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <header>
    <h2 class="logo-title">FableFoundry</h2>
        <nav class="nav-left">
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="lookups.php">Shop All</a></li>
                <?php
                // Display generic navigation if user is not logged in
                if (!isset($_SESSION['username'])) {
                    echo '<li><a href="login.php">Login</a></li>';
                    echo '<li><a href="register.php">Register</a></li>';
                }
                ?>
            </ul>
        </nav>
        <nav class="nav-right">
            <ul>
                <li><a href="#">Selling</a></li>
                <li><a href="#">Wishlist</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="cart.html">Cart</a></li>
            </ul>
        </nav>
    </header>

    <main class="profile-container">
        <section class="profile-info">
            <img src="../images/profile_picture.png" alt="Profile Photo" class="profile-photo">
            <h1><?php echoSessionVar('name'); ?></h1>
            <p>Email: <?php echoSessionVar('email'); ?></p>
            <button class="action-button">Edit Profile</button>
            <!-- Logout button moved here -->
            <form action="logout.php" method="post">
                <button type="submit" class="action-button">Logout</button>
            </form>
        </section>
        <section class="actions">
            <button class="action-button">Orders</button>
            <button class="action-button">Listings</button>
        </section>
    </main>

    <footer>
        <p>© 2024 FableFoundry. All rights reserved.</p>
    </footer>
</body>
</html>
