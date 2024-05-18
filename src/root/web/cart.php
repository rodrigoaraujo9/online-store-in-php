<?php
// Include your database connection or any necessary PHP files here
include 'db.php';

session_start();

// Check if the user is logged in, if not, redirect them to the login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Check if a book is being removed from the cart
if (isset($_GET['remove_from_cart']) && isset($_GET['book_id'])) {
    $user_id = $_SESSION['user_id'];
    $book_id = $_GET['book_id'];
    
    // Remove the book from the cart
    $stmt = $conn->prepare("DELETE FROM shopping_cart WHERE user_id = :user_id AND book_id = :book_id");
    $result = $stmt->execute(['user_id' => $user_id, 'book_id' => $book_id]);
    if ($result) {
        // Redirect back to cart.php after removing the book from the cart
        header("Location: cart.php");
        exit;
    } else {
        echo "Error removing book from cart: " . $conn->errorInfo()[2];
    }
}

// Fetch cart items from the database for the current user
$user_id = $_SESSION['user_id'];
$sql = "SELECT b.title, b.author, b.listed_price, b.image_url, c.book_id 
        FROM shopping_cart c 
        JOIN books b ON c.book_id = b.book_id 
        WHERE c.user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$cartItems = $stmt->fetchAll();

// Calculate total cost of books in the cart
$totalCost = 0;
foreach ($cartItems as $item) {
    $totalCost += $item['listed_price'];
}

// Add shipping costs (example: €5 flat rate)
$shippingCost = 5; // You can adjust this based on your shipping criteria

// Calculate total cost including shipping
$totalCostWithShipping = $totalCost + $shippingCost;
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

<br>
<main class="cart-container">
    <div class="cart-items">
        <h2>Shopping Cart</h2>
        <?php if (empty($cartItems)) : ?>
            <p>Your cart is empty.</p>
        <?php else : ?>
            <div class="books-container-cart">
                <?php foreach ($cartItems as $item) : ?>
                    <div class="book-item-cart">
                        <div class="book-card-cart">
                            <img class="book-item-image-cart" src="../images/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                            <div class="book-details-cart">
                                <h3 class="book-item-title-cart"><?php echo htmlspecialchars($item['title']); ?></h3>
                                <p class="book-item-author-cart">by <?php echo htmlspecialchars($item['author']); ?></p>
                                <p class="book-item-price-cart">€<?php echo number_format($item['listed_price'], 2); ?></p>
                                <!-- Add a link to remove the item from the cart -->
                                <a href="cart.php?remove_from_cart=true&book_id=<?php echo $item['book_id']; ?>" class="remove-from-cart">Remove from Cart</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <br>
    <div class="checkout-form">
        <h2>Checkout</h2>
        <p>Total cost of books: €<?php echo number_format($totalCost, 2); ?></p>
        <p>Shipping cost: €<?php echo number_format($shippingCost, 2); ?></p>
        <p>Total cost including shipping: €<?php echo number_format($totalCostWithShipping, 2); ?></p>
        <form action="#" method="post">
            <input type="text" name="address" placeholder="Address" required>
            <input type="text" name="postal_code" placeholder="Postal Code" required>
            <br>
            <p> Payment options: </p>
            <label class="payment-option">
                Credit Card
                <input type="radio" name="payment_type" value="Credit Card" required>
                <span class="checkmark"></span>
            </label>
            <label class="payment-option">
                PayPal
                <input type="radio" name="payment_type" value="PayPal" required>
                <span class="checkmark"></span>
            </label>
            <label class="payment-option">
                Bank Transfer
                <input type="radio" name="payment_type" value="Bank Transfer" required>
                <span class="checkmark"></span>
            </label>
            <br>
            <button type="submit" class="checkout-button">Checkout</button>
        </form>
    </div>
</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> FableFoundry. All rights reserved.</p>
</footer>
</body>
</html>
