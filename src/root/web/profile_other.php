<?php
session_start();

include 'db.php'; // Include your database connection

// Get the user_id of the profile to be viewed from the URL parameter
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if (!$user_id) {
    // Redirect or display an error message if user_id is not provided
    header("Location: index.php");
    exit;
}

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the user exists
if (!$user) {
    // Redirect or display an error message if the user does not exist
    header("Location: index.php");
    exit;
}

// Fetch books listed by the user
$bookStmt = $conn->prepare("SELECT * FROM books WHERE seller_id = ?");
$bookStmt->execute([$user_id]);
$books = $bookStmt->fetchAll(PDO::FETCH_ASSOC);

function echoUserVar($user, $varName) {
    if (isset($user[$varName])) {
        echo htmlspecialchars($user[$varName], ENT_QUOTES, 'UTF-8');
    } else {
        echo "Not available";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page - FableFoundry</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <header>
        <h2 class="logo-title">FableFoundry</h2>
        <nav class="nav-left">
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="lookups.php">Shop All</a></li>
            </ul>
        </nav>
        <nav class="nav-right">
            <ul>
                <li><a href="sell.php">Selling</a></li>
                <li><a href="wishlist.php">Wishlist</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="cart.php">Cart</a></li>
            </ul>
        </nav>
    </header>

    <main class="profile-container">
        <div class="profile-card">
            <section class="profile-info">
                <img src="<?php echo !empty($user['profile_picture_url']) ? htmlspecialchars($user['profile_picture_url'], ENT_QUOTES, 'UTF-8') : '../images/default_profile.png'; ?>" alt="Profile Photo" class="profile-photo">
                <h1><?php echoUserVar($user, 'name'); ?></h1>
                <h3><?php echoUserVar($user, 'bio'); ?></h3>
                <p><?php echoUserVar($user, 'email'); ?></p>
            </section>
        </div>

        <div class="books-list">
            <h2>Books Listed by <?php echoUserVar($user, 'name'); ?></h2>
            <?php if ($books): ?>
                <div class="books-container">
                    <?php foreach ($books as $book): ?>
                        <div class="book-item">
                            <img src="../images/<?php echo htmlspecialchars($book['image_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                            <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p><?php echo htmlspecialchars($book['author']); ?></p>
                            <p>Price: $<?php echo number_format($book['listed_price'], 2); ?></p>
                            <a href="book_details.php?book_id=<?php echo $book['book_id']; ?>" class="view-book-details">View Details</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No books listed.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>© 2024 FableFoundry. All rights reserved.</p>
    </footer>
</body>
</html>
