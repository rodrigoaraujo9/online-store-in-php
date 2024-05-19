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
    <style>
    .books-container {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        justify-content: center; /* Center items horizontally */
        padding: 1rem;
    }

    .book-item {
        width: 300px; /* Fixed width for better layout */
        border: 1px solid var(--darker-forest-green);
        border-radius: 0.5rem;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        background-color: var(--almost-white);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add shadow for depth */
        transition: transform 0.3s ease;
    }

    .book-item:hover {
        transform: scale(1.05); /* Slightly enlarge on hover */
    }

    .book-card {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .book-card img {
        width: 100%;
        height: 200px; /* Fixed height for images */
        object-fit: cover;
    }

    .book-details-wishlist {
        padding: 1rem;
        text-align: center; /* Center text within each book item */
    }

    .book-item-title {
        font-family: 'CustomFontSemi';
        color: var(--text-dark);
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }

    .book-item-author, .book-item-price {
        font-family: 'CustomFont';
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }

    .remove-from-wishlist, .add-to-cart {
        display: inline-block;
        margin-top: 0.5rem;
        color: var(--almost-white);
        text-decoration: none;
        font-family: 'CustomFontSemi';
        background-color: var(--text-dark);
        border: 1px solid var(--text-dark);
        border-radius: 0.25rem;
        padding: 0.5rem 1rem;
        transition: background-color 0.3s, color 0.3s;
    }

    .remove-from-wishlist:hover, .add-to-cart:hover {
        
    }
</style>
</head>
<body>
<header>
    <h2 class="logo-title"><a href="../index.php">FableFoundry </a></h2>
    <nav class="nav-left">
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="lookups.php">Shop All</a></li>
                <?php
                include 'db.php';

                // Fetch genres from the database
                $sql = "SELECT genre_id, name FROM genres LIMIT 2";
                $stmt = $conn->query($sql);
                $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Display genre filters
                foreach ($genres as $genre) {
                    echo '<li><a href="lookups.php?genre=' . $genre['genre_id'] . '">' . $genre['name'] . '</a></li>';
                }
                ?>
            </ul>
        </nav>
        <nav class="nav-right">
            <ul>
                <li><a href="selling.php">Selling</a></li>
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
        <div class="books-container">
            <?php foreach ($wishlistItems as $item) : ?>
                <div class="book-item">
                    <div class="book-card">
                    <img src="../images/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    <div class="book-details-wishlist">
                    <h3 class="book-item-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p class="book-item-author">by <?php echo htmlspecialchars($item['author']); ?></p>
                    <p class="book-item-price">€<?php echo number_format($item['listed_price'], 2); ?></p>
                    <!-- Add a link to remove the book from wishlist -->
                    <a href="wishlist.php?remove_from_wishlist=true&wishlist_id=<?php echo $item['wishlist_id']; ?>" class="remove-from-wishlist">Remove</a>
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
