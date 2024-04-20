<?php
include 'db.php'; // Include your database connection

$book_id = 1; // This should be dynamically set, perhaps passed via GET request
$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details - FableFoundry</title>
    <link rel="stylesheet" href="../style2.css">
</head>
<body>
    <header>
        <h2 class="logo-title">FableFoundry</h2>
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
            </div>
        </div>
    </main>

    <footer>
        <p>© 2024 FableFoundry. All rights reserved.</p>
    </footer>
</body>
</html>
