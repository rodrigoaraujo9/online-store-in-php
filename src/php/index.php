<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FableFoundry</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <header>
        <h2 class="logo-title">FableFoundry</h2>
        <nav class="nav-left">
            <ul>
                <li><a href="lookups.php">Shop All</a></li>
                <li><a href="lookups.php">Portuguese Literature</a></li>
                <li><a href="lookups.php">Premium</a></li>
                <li><a href="lookups.php">Classics</a></li>
            </ul>
        </nav>
        <nav class="nav-right">
            <ul>
                <li><a href="selling.php">Selling</a></li>
                <li><a href="wishlist.php">Wishlist</a></li>
                <li><a href="login.php">Profile</a></li>
                <li><a href="cart.php">Cart</a></li>
            </ul>
        </nav>
    </header>

    <div class="sell-cta">
        <h1>Have any books sitting on your shelf for too long?</h1>
        <h2>Turn them into cash today!</h2>
        <a href="sell.php" class="sell-button"><h2>Start Selling!</h2></a>
    </div>

    <div class="main-feature">
        <h1>Shop Our Picks For the Week</h1>
        <div class="books-grid">
            <?php
            include 'db.php';
            try {
                $stmt = $conn->query("SELECT title, author, listed_price, image_url FROM books");
                while ($book = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div class="book-item">';
                    echo '<img src="../../images/' . htmlspecialchars($book['image_url']) . '" alt="' . htmlspecialchars($book['title']) . '">';
                    echo '<h3 class="book-item-title">' . htmlspecialchars($book['title']) . '</h3>';
                    echo '<p class="book-item-author">by ' . htmlspecialchars($book['author']) . '</p>';
                    echo '<p class="book-item-price">€' . number_format($book['listed_price'], 2) . '</p>';
                    echo '</div>';
                }
            } catch (PDOException $e) {
                echo 'Database error: ' . $e->getMessage();
            }
            ?>
        </div>
        <!-- Additional content can be added here -->
    </div>

    <footer>
        <p>© 2024 FableFoundry. All rights reserved.</p>
    </footer>
</body>
</html>
