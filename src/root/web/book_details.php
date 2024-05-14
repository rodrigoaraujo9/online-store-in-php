<?php
include 'db.php'; // Include your database connection

// Retrieve book_id from the URL parameter
$book_id = isset($_GET['book_id']) ? $_GET['book_id'] : null;

if (!$book_id) {
    // Redirect or display an error message if book_id is not provided
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the book exists
if (!$book) {
    // Redirect or display an error message if the book does not exist
    header("Location: index.php");
    exit;
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
        <h2 class="logo-title"><a href="../index.php">FableFoundry </a></h2>
    </header>

    <main class="book-detail-container">
        <div class="book-detail">
            <div class="book-image">
                <img src="../images/<?= $book['image_url']; ?>" alt="<?= htmlspecialchars($book['title']); ?>">
            </div>
            <div class="book-metadata">
                <h1 class="book-title">Title: <?= htmlspecialchars($book['title']); ?></h1>
                <p class="book-author">Author: <?= htmlspecialchars($book['author']); ?></p>
                <p class="book-isbn">ISBN: <?= htmlspecialchars($book['isbn']); ?></p>
                <p class="book-genre">Genre ID: <?= $book['genre_id']; ?> (Classics)</p>
                <p class="book-seller">Seller ID: <?= $book['seller_id']; ?></p>
                <p class="book-condition">Condition: <?= htmlspecialchars($book['condition']); ?></p>
                <p class="book-price">Listed Price: $<?= number_format($book['listed_price'], 2); ?></p>
                <p class="book-description">Description: <?= htmlspecialchars($book['description']); ?></p>
                <p class="book-listing-date">Listing Date: <?= $book['listing_date']; ?></p>
                <button id="add-to-cart-button" class="add-to-cart-button" data-book-id="<?= $book_id ?>">Add to Cart</button>
            </div>
        </div>
    </main>
    <div class="seller-profile">
                <div class="seller-info">
                <img src="../images/profile_picture.png" alt="seller Photo" class="seller-photo">
                    <h3>Seller Name</h3>
                </div>
            </div>

    <footer>
        <p>© 2024 FableFoundry. All rights reserved.</p>
    </footer>
</body>
</html>

<script>
document.getElementById('add-to-cart-button').addEventListener('click', function() {
    var bookId = this.getAttribute('data-book-id');

    // Send an AJAX request to add_book_to_cart.php with the book_id parameter
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'add_book_to_cart.php?book_id=' + bookId, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Show a pop-up message confirming that the book has been added to the cart
            alert('Book added to cart!');
        } else {
            // Handle errors if necessary
            console.error('Error adding book to cart:', xhr.statusText);
        }
    };
    xhr.onerror = function() {
        // Handle errors if necessary
        console.error('Error adding book to cart:', xhr.statusText);
    };
    xhr.send();
});
</script>

