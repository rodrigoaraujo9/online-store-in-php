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
$sql = "SELECT b.book_id, b.title, b.author, b.listed_price, b.image_url, 
               t.transaction_id, t.sale_price, t.transaction_date, t.status
        FROM books b
        LEFT JOIN transactions t ON b.book_id = t.book_id
        WHERE b.seller_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$sellingItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <style>
        .add-book-button {
            text-align: center;
            margin: 2rem 0;
        }

        .add-book-link {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--button-hover-purple) 0%, var(--pastel-green) 100%);
            color: var(--almost-white);
            border-radius: 1.25rem;
            text-decoration: none;
            font-family: 'CustomFontSemi';
            transition: transform 0.3s, background-color 0.3s;
        }

        :root {
            --almost-white: #FAFAF9;
            --darker-forest-green: #2A5230;
            --pastel-green: #77DD77;
            --text-dark: #2E2E2E;
            --button-hover-purple: #B19CD9;
            --moss-green: #8FA98F;
            --pastel-red: #FF6961;
        }

        .add-book-button {
            text-align: center;
            margin: 2rem 0;
        }

        .add-book-link {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--button-hover-purple) 0%, var(--pastel-green) 100%);
            color: var(--almost-white);
            border-radius: 1.25rem;
            text-decoration: none;
            font-family: 'CustomFontSemi';
            transition: transform 0.3s, background-color 0.3s;
        }

        .add-book-link:hover {
            transform: scale(1.05);
            color: var(--almost-white);
            background: linear-gradient(135deg, var(--pastel-green) 0%, var(--button-hover-purple) 100%);
        }

        .books-container-selling {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            justify-content: center; /* Center the book items */
        }

        .book-item {
            width: 300px; /* Fixed width for better layout */
            border: 1px solid var(--darker-forest-green);
            border-radius: 0.5rem;
            overflow: hidden;
            display: flex;
            flex-direction: column; /* Stack items vertically */
            background-color: var(--almost-white);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add shadow for depth */
            transition: transform 0.3s ease;
        }

        .book-item:hover {
            transform: scale(1.05); /* Slightly enlarge on hover */
        }

        .book-item img {
            width: 100%;
            height: 200px; /* Fixed height for images */
            object-fit: cover;
        }

        .book-details {
            padding: 1rem;
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

        .transaction-details {
            margin-top: 1rem;
            background-color: var(--pastel-green);
            padding: 0.5rem;
            border-radius: 0.25rem;
        }

        .remove-from-selling, .print-shipping {
            display: inline-block;
            margin-top: 1rem;
            color: var(--almost-white);
            text-decoration: none;
            font-family: 'CustomFontSemi';
            background-color: var(--text-dark);
            border: 1px solid var(--text-dark);
            border-radius: 0.25rem;
            padding: 0.5rem 1rem;
            transition: background-color 0.3s, color 0.3s;
        }

        .remove-from-selling:hover, .print-shipping:hover {
            color: var(--almost-white);
            
        }

        .book-actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .book-actions a {
            font-family: 'CustomFontSemi', Arial, sans-serif;
            padding: 0.5rem 1rem;
            background-color: var(--darker-forest-green);
            color: #fff;
            border-radius: 0.25rem;
            text-decoration: none;
            transition: background-color 0.3s ease;
            text-align: center; /* Ensure the text is centered */
        }

        .book-actions a:hover {
            background-color: var(--text-dark);
            transform: scale(1.1); /* Slightly enlarge on hover */
        }
    </style>
</head>
<body>
<header>
    <h2 class="logo-title"><a href="../index.php">FableFoundry</a></h2>
    <nav class="nav-left">
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="lookups.php">Shop All</a></li>
            <?php
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
<div class="profile-container">
    <div class="profile-card">
        <section class="profile-info">
            <h1>Books You're Selling</h1>
            <!-- Add Book Button -->
            <div class="add-book-button">
                <a href="sell.php" class="add-book-link">Add New Book</a>
            </div>
            <?php if (empty($sellingItems)) : ?>
                <p>You are not selling any books.</p>
            <?php else : ?>
                <div class="books-container-selling">
                <?php foreach ($sellingItems as $item) : ?>
    <div class="book-item">
        <a href="book_details.php?book_id=<?php echo $item['book_id']; ?>">
            <img src="../images/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
            <div class="book-details">
                <h3 class="book-item-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                <p class="book-item-author">by <?php echo htmlspecialchars($item['author']); ?></p>
                <p class="book-item-price">€<?php echo number_format($item['listed_price'], 2); ?></p>
                <?php if ($item['transaction_id']) : ?>
                    <div class="transaction-details">
                        <h4>Transaction Details</h4>
                        <p>Transaction ID: <?php echo htmlspecialchars($item['transaction_id']); ?></p>
                        <p>Sale Price: €<?php echo number_format($item['sale_price'], 2); ?></p>
                        <p>Transaction Date: <?php echo htmlspecialchars($item['transaction_date']); ?></p>
                        <p>Status: <?php echo htmlspecialchars($item['status']); ?></p>
                        <a href="print_shipping.php?transaction_id=<?php echo $item['transaction_id']; ?>" class="print-shipping">Print Shipping Form</a>
                    </div>
                <?php endif; ?>
            </div>
        </a>
        <div class="book-actions">
            <a href="selling.php?remove_from_selling=true&book_id=<?php echo $item['book_id']; ?>" class="remove-from-selling">Remove Listing</a>
        </div>
    </div>
<?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> FableFoundry. All rights reserved.</p>
</footer>
</body>
</html>
