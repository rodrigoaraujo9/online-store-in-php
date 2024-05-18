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
$sql = "SELECT w.wishlist_id, b.book_id, b.title, b.author, b.listed_price, b.image_url FROM wishlists w JOIN books b ON w.book_id = b.book_id WHERE w.user_id = :user_id";
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

// Check if a book is being added to the cart
if (isset($_GET['add_to_cart']) && isset($_GET['book_id'])) {
    $user_id = $_SESSION['user_id'];
    $book_id = $_GET['book_id'];

    // Check if the book is not already in the cart
    $stmt = $conn->prepare("SELECT * FROM shopping_cart WHERE user_id = :user_id AND book_id = :book_id");
    $stmt->execute(['user_id' => $user_id, 'book_id' => $book_id]);
    $existingItem = $stmt->fetch();

    if (!$existingItem) {
        // Insert the book into the cart with a quantity of 1
        $stmt = $conn->prepare("INSERT INTO shopping_cart (user_id, book_id, quantity) VALUES (:user_id, :book_id, 1)");
        $result = $stmt->execute(['user_id' => $user_id, 'book_id' => $book_id]);

        if ($result) {
            // Delete the book from the wishlist after adding to the cart
            $stmt = $conn->prepare("DELETE FROM wishlists WHERE user_id = :user_id AND book_id = :book_id");
            $stmt->execute(['user_id' => $user_id, 'book_id' => $book_id]);

            // Redirect back to wishlist.php after adding the book to the cart
            header("Location: wishlist.php");
            exit;
        } else {
            echo "Error adding book to cart: " . $stmt->errorInfo()[2];
        }
    } else {
        echo "Book already exists in the cart.";
    }
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
            <li><a href="cart.php">Cart</a></li>
        </ul>
    </nav>
</header>

<main>
    <h1>Wishlist</h1>
    <?php if (empty($wishlistItems)) : ?>
        <p>No items in your wishlist.</p>
    <?php else : ?>
        <div class="books-container-wishlist">
            <?php foreach ($wishlistItems as $item) : ?>
                <div class="book-item-wishlist">
                    <div class="book-card-wishlist">
                    <img src="../images/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    <div class="book-details-wishlist">
                    <h3 class="book-item-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p class="book-item-author">by <?php echo htmlspecialchars($item['author']); ?></p>
                    <p class="book-item-price">€<?php echo number_format($item['listed_price'], 2); ?></p>
                    <!-- Add a link to remove the book from wishlist -->
                    <a href="wishlist.php?remove_from_wishlist=true&wishlist_id=<?php echo $item['wishlist_id']; ?>" class="remove-from-wishlist">Remove from Wishlist</a>
                    <!-- Add a link to add the book to cart -->
                    <a href="wishlist.php?add_to_cart=true&book_id=<?php echo $item['book_id']; ?>" class="add-to-cart">Add to Cart</a>
                </div>
            </div>
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
