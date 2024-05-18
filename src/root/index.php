<?php
// Include your database connection or any necessary PHP files here
include './web/db.php';

session_start();

// Check if the user is logged in, if not, redirect them to the login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Check if a book is being added to the wishlist
if (isset($_GET['add_to_wishlist']) && isset($_GET['book_id'])) {
    $user_id = $_SESSION['user_id'];
    $book_id = $_GET['book_id'];
    
    // Check if the book is not already in the wishlist
    $stmt = $conn->prepare("SELECT * FROM wishlists WHERE user_id = :user_id AND book_id = :book_id");
    $stmt->execute(['user_id' => $user_id, 'book_id' => $book_id]);
    $existingItem = $stmt->fetch();
    
    if (!$existingItem) {
        // Insert the book into the wishlist
        $stmt = $conn->prepare("INSERT INTO wishlists (user_id, book_id) VALUES (:user_id, :book_id)");
        $result = $stmt->execute(['user_id' => $user_id, 'book_id' => $book_id]);
        if ($result) {
            // Redirect back to index.php after adding the book to the wishlist
            header("Location: index.php");
            exit;
        } else {
            echo "Error adding book to wishlist: " . $conn->errorInfo()[2];
        }
    }
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
            // Redirect back to index.php after adding the book to the cart
            header("Location: index.php");
            exit;
        } else {
            echo "Error adding book to cart: " . $stmt->errorInfo()[2];
        }
    } else {
        echo "Book already exists in the cart.";
    }
}


// Fetch genres from the database
$sql = "SELECT genre_id, name FROM genres LIMIT 2";
$stmt = $conn->query($sql);
$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FableFoundry</title>
    <link rel="stylesheet" href="/web/style2.css">
</head>
<body>
<header>
    <h2 class="logo-title">FableFoundry</h2>
    <nav class="nav-left">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="./web/lookups.php">Shop All</a></li>
            <?php
            // Display genre filters
            foreach ($genres as $genre) {
                echo '<li><a href="/web/lookups.php?genre=' . $genre['genre_id'] . '">' . htmlspecialchars($genre['name']) . '</a></li>';
            }
            ?>
        </ul>
    </nav>
    <nav class="nav-right">
        <ul>
            <li><a href="/web/selling.php">Selling</a></li>
            <li><a href="/web/wishlist.php">Wishlist</a></li>
            <li><a href="/web/profile.php">Profile</a></li>
            <li><a href="/web/cart.php">Cart</a></li>
        </ul>
    </nav>
</header>

<div class="sell-cta">
    <h1>Have any books sitting on your shelf for too long?</h1>
    <h2>Turn them into cash today!</h2>
    <a href="/web/sell.php" class="sell-button"><h2>Start Selling!</h2></a>
</div>

<div class="main-feature">
    <h1>Shop Our Picks For the Week</h1>
    <div class="books-grid">
        <?php
        // Fetch and display books
        try {
            $sql = "SELECT book_id, title, author, listed_price, image_url FROM books LIMIT 8";
            $stmt = $conn->query($sql);
        
            while ($book = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="book-item">';
                // Add hyperlink around the book item to redirect to book_details.php
                echo '<a href="/web/book_details.php?book_id=' . $book['book_id'] . '">';
                echo '<img src="./images/' . htmlspecialchars($book['image_url']) . '" alt="' . htmlspecialchars($book['title']) . '">';
                echo '<h3 class="book-item-title">' . htmlspecialchars($book['title']) . '</h3>';
                echo '<p class="book-item-author">by ' . htmlspecialchars($book['author']) . '</p>';
                echo '<p class="book-item-price">€' . number_format($book['listed_price'], 2) . '</p>';
                echo '</a>'; // Close the hyperlink
                // Add a link to add the book to wishlist
                echo '<a href="index.php?add_to_wishlist=true&book_id=' . $book['book_id'] . '">Add to Wishlist</a>';
                // Add a link to add the book to cart
                echo '<a href="index.php?add_to_cart=true&book_id=' . $book['book_id'] . '">Add to Cart</a>';
                echo '</div>';
            }
        } catch (PDOException $e) {
            echo 'Database error: ' . $e->getMessage();
        }
        ?>
    </div>
</div>

<footer>
    <p>© 2024 FableFoundry. All rights reserved.</p>
</footer>
</body>
</html>
