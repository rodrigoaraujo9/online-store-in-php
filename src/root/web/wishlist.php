<?php
// Include your database connection or any necessary PHP files here
include 'db.php';

session_start();

// Check if the user is logged in, if not, redirect them to the login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Fetch wishlist items from the database for the current user
$user_id = $_SESSION['user_id'];
$sql = "SELECT w.wishlist_id, b.title, b.author, b.listed_price, b.image_url FROM wishlists w JOIN books b ON w.book_id = b.book_id WHERE w.user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$wishlistItems = $stmt->fetchAll();

// Check if a book is being removed from the wishlist
if (isset($_GET['remove_from_wishlist']) && isset($_GET['wishlist_id'])) {
    $wishlist_id = $_GET['wishlist_id'];
    
    // Delete the book from the wishlist
    $stmt = $conn->prepare("DELETE FROM wishlists WHERE wishlist_id = :wishlist_id");
    $stmt->execute(['wishlist_id' => $wishlist_id]);
    // Redirect back to the wishlist page after removing the book
    header("Location: wishlist.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FableFoundry - Wishlist</title>
    <link rel="stylesheet" href="/web/style2.css">
</head>
<body>
<header>
    <h2 class="logo-title"><a href="../index.php">FableFoundry </a></h2>
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
            <li><a href="cart.html">Cart</a></li>
        </ul>
    </nav>
</header>

<main>
    <h1>Wishlist</h1>
    <?php if (empty($wishlistItems)) : ?>
        <p>No items in your wishlist.</p>
    <?php else : ?>
        <div class="books-grid">
            <?php foreach ($wishlistItems as $item) : ?>
                <div class="book-item">
                    <img src="./images/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    <h3 class="book-item-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p class="book-item-author">by <?php echo htmlspecialchars($item['author']); ?></p>
                    <p class="book-item-price">€<?php echo number_format($item['listed_price'], 2); ?></p>
                    <!-- Add a link to remove the book from wishlist -->
                    <a href="wishlist.php?remove_from_wishlist=true&wishlist_id=<?php echo $item['wishlist_id']; ?>">Remove from Wishlist</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Your Bookstore. All rights reserved.</p>
</footer>
</body>
</html>
