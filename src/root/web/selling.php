<?php
include 'db.php';

session_start();

// Check if the user is logged in, if not, redirect them to the login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Fetch books sold by the current user
$user_id = $_SESSION['user_id'];
$sql = "SELECT b.book_id, b.title, b.author, b.listed_price, b.image_url, t.transaction_id, t.sale_price, t.transaction_date, t.status
        FROM books b
        LEFT JOIN transactions t ON b.book_id = t.book_id
        WHERE b.seller_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$sellingItems = $stmt->fetchAll();

// Check if a book is being removed from the selling list
if (isset($_GET['remove_from_selling']) && isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];
    
    // Delete the book from the books table
    $stmt = $conn->prepare("DELETE FROM books WHERE book_id = :book_id AND seller_id = :user_id");
    $stmt->execute(['book_id' => $book_id, 'user_id' => $user_id]);
    // Redirect back to the selling page after removing the book
    header("Location: selling.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FableFoundry - Selling</title>
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
            <li><a href="selling.php">Selling</a></li>
            <li><a href="wishlist.php">Wishlist</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="cart.php">Cart</a></li>
        </ul>
    </nav>
</header>

<main>
    <h1>Books You're Selling</h1>
    <?php if (empty($sellingItems)) : ?>
        <p>You are not selling any books.</p>
    <?php else : ?>
        <div class="books-container-selling">
            <?php foreach ($sellingItems as $item) : ?>
                <div class="book-item-selling">
                    <div class="book-card-selling">
                        <img src="../images/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="book-details-selling">
                            <h3 class="book-item-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p class="book-item-author">by <?php echo htmlspecialchars($item['author']); ?></p>
                            <p class="book-item-price">€<?php echo number_format($item['listed_price'], 2); ?></p>
                            <?php if ($item['transaction_id']) : ?>
                                <h4>Transaction Details</h4>
                                <p>Transaction ID: <?php echo htmlspecialchars($item['transaction_id']); ?></p>
                                <p>Sale Price: €<?php echo number_format($item['sale_price'], 2); ?></p>
                                <p>Transaction Date: <?php echo htmlspecialchars($item['transaction_date']); ?></p>
                                <p>Status: <?php echo htmlspecialchars($item['status']); ?></p>
                            <?php endif; ?>
                            <a href="selling.php?remove_from_selling=true&book_id=<?php echo $item['book_id']; ?>" class="remove-from-selling">Remove from Selling</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> FableFoundry. All rights reserved.</p>
</footer>
</body>
</html>
