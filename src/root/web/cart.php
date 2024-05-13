<?php
// Include your database connection or any necessary PHP files here
include 'db.php';

session_start();

// Check if the user is logged in, if not, redirect them to the login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Fetch cart items from the database for the current user
$user_id = $_SESSION['user_id'];
$sql = "SELECT b.title, b.author, b.listed_price, b.image_url FROM shopping_cart c JOIN books b ON c.book_id = b.book_id WHERE c.user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$cartItems = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FableFoundry - Cart</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
<header>
    <h2 class="logo-title"><a href="../index.php">FableFoundry</a></h2>
    <nav class="nav-left">
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="lookups.php">Shop All</a></li>
        </ul>
    </nav>
    <nav class="nav-right">
        <ul>
            <li><a href="#">Selling</a></li>
            <li><a href="wishlist.php">Wishlist</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="cart.php">Cart</a></li>
        </ul>
    </nav>
</header>

<main>
    <h1>Shopping Cart</h1>
    <?php if (empty($cartItems)) : ?>
        <p>Your cart is empty.</p>
    <?php else : ?>
        <div class="books-grid">
            <?php foreach ($cartItems as $item) : ?>
                <div class="book-item">
                    <img src="./images/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    <h3 class="book-item-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p class="book-item-author">by <?php echo htmlspecialchars($item['author']); ?></p>
                    <p class="book-item-price">€<?php echo number_format($item['listed_price'], 2); ?></p>
                    <!-- Add more details or customize as needed -->
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <button class="checkout-button">Checkout</button>
</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> FableFoundry. All rights reserved.</p>
</footer>
</body>
</html>
