<?php
session_start(); // Start the session

include 'db.php'; // Include your database connection

// Retrieve book_id from the URL parameter
$book_id = isset($_GET['book_id']) ? $_GET['book_id'] : null;

if (!$book_id) {
    // Redirect or display an error message if book_id is not provided
    header("Location: index.php");
    exit;
}

// Fetch book details
$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the book exists
if (!$book) {
    // Redirect or display an error message if the book does not exist
    header("Location: index.php");
    exit;
}

// Fetch seller details (only name and profile picture)
$seller_id = $book['seller_id'];
$sellerStmt = $conn->prepare("SELECT name, profile_picture_url FROM users WHERE user_id = ?");
$sellerStmt->execute([$seller_id]);
$seller = $sellerStmt->fetch(PDO::FETCH_ASSOC);

// Check if a book is being added to the cart
if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        // Redirect to login page if user is not logged in
        header("Location: login.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $book_id = $_POST['book_id'];

    // Check if the book is not already in the cart
    $stmt = $conn->prepare("SELECT * FROM shopping_cart WHERE user_id = :user_id AND book_id = :book_id");
    $stmt->execute(['user_id' => $user_id, 'book_id' => $book_id]);
    $existingItem = $stmt->fetch();

    if (!$existingItem) {
        // Insert the book into the cart with a quantity of 1
        $stmt = $conn->prepare("INSERT INTO shopping_cart (user_id, book_id, quantity) VALUES (:user_id, :book_id, 1)");
        $result = $stmt->execute(['user_id' => $user_id, 'book_id' => $book_id]);

        if ($result) {
            // Set session variable to show the pop-up message on cart.php
            $_SESSION['added_to_cart'] = true;
            // Redirect back to book_details.php after adding the book to the cart
            header("Location: book_details.php?book_id=" . $book_id);
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
    <title>Book Details - FableFoundry</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <header>
        <h2 class="logo-title"><a href="../index.php">FableFoundry</a></h2>
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
        <div class="book-detail-container">
        <div class="book-detail">
            <div class="book-image">
                <img src="../images/<?= htmlspecialchars($book['image_url']); ?>" alt="<?= htmlspecialchars($book['title']); ?>">
            </div>
            <div class="book-metadata">
                <h1 class="book-title">Title: <?= htmlspecialchars($book['title']); ?></h1>
                <p class="book-author">Author: <?= htmlspecialchars($book['author']); ?></p>
                <p class="book-isbn">ISBN: <?= htmlspecialchars($book['isbn']); ?></p>
                <p class="book-genre">Genre ID: <?= $book['genre_id']; ?> (Classics)</p>
                <p class="book-seller">Seller: <?= htmlspecialchars($seller['name']); ?></p>
                <p class="book-condition">Condition: <?= htmlspecialchars($book['condition']); ?></p>
                <p class="book-price">Listed Price: $<?= number_format($book['listed_price'], 2); ?></p>
                <p class="book-description">Description: <?= htmlspecialchars($book['description']); ?></p>
                <p class="book-listing-date">Listing Date: <?= htmlspecialchars($book['listing_date']); ?></p>
                <!-- Add form to submit book to cart -->
                <form method="post">
                    <input type="hidden" name="book_id" value="<?= $book_id ?>">
                    <button type="submit" name="add_to_cart" class="add-to-cart-button">Add to Cart</button>
                </form>
            </div>
        </div>
            </div>
    </main>

    <div class="seller-profile">
        <div class="seller-info">
            <img src="../images/<?= htmlspecialchars($seller['profile_picture_url']); ?>" alt="Seller Photo" class="seller-photo">
            <a href="profile_other.php?user_id=<?= htmlspecialchars($seller_id); ?>">
                        <?= htmlspecialchars($seller['name']); ?>
                    </a>
        </div>
    </div>

    <footer>
        <p>© 2024 FableFoundry. All rights reserved.</p>
    </footer>
</body>
</html>
