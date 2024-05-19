<?php
include 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Check if transaction details are available
if (!isset($_SESSION['transaction'])) {
    header("Location: cart.php");
    exit;
}

$transaction = $_SESSION['transaction'];

// Process each book in the transaction
foreach ($transaction['books'] as $book) {
    // Remove the book from the database
    $stmt = $conn->prepare("DELETE FROM books WHERE book_id = :book_id");
    $stmt->execute(['book_id' => $book['book_id']]);
    
    // Record the transaction
    $stmt = $conn->prepare("INSERT INTO transactions (buyer_id, seller_id, book_id, sale_price, transaction_date, status) VALUES (:buyer_id, :seller_id, :book_id, :sale_price, :transaction_date, 'Completed')");
    $stmt->execute([
        'buyer_id' => $transaction['user_id'],
        'seller_id' => $book['seller_id'],
        'book_id' => $book['book_id'],
        'sale_price' => $book['listed_price'],
        'transaction_date' => date('Y-m-d H:i:s')
    ]);
}

// Clear the transaction session
unset($_SESSION['transaction']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Completed - FableFoundry</title>
    <link rel="stylesheet" href="style2.css">
    <style>
        .checkout-completed-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(45deg, var(--pastel-green), var(--button-hover-purple));
            padding: 2rem;
        }

        .checkout-details {
            background-color: var(--almost-white);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        .checkout-details h2 {
            font-family: 'CustomFontBold', Arial, sans-serif;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
        }

        .checkout-details p {
            font-family: 'CustomFont', Arial, sans-serif;
            color: var(--text-dark);
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .checkout-details .transaction-info {
            margin: 1.5rem 0;
        }

        .checkout-details .transaction-info h3 {
            font-family: 'CustomFontSemi', Arial, sans-serif;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .checkout-details .transaction-info p {
            font-family: 'CustomFontLight', Arial, sans-serif;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .checkout-details .back-home {
            font-family: 'CustomFontSemi', Arial, sans-serif;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--pastel-green) 0%, var(--button-hover-purple) 100%);
            color: var(--almost-white);
            border-radius: 3.125rem;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.3s;
            border: none;
            display: inline-block;
            margin-top: 2rem;
        }

        .checkout-details .back-home:hover {
            background: linear-gradient(135deg, var(--button-hover-purple) 0%, var(--pastel-green) 100%);
            transform: scale(1.05);
            box-shadow: 0 0.35rem 0.75rem rgba(0, 0, 0, 0.15);
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
                <!-- Display dynamic genre links here -->
                <?php
                // Fetch genres from the database
                $sql = "SELECT genre_id, name FROM genres LIMIT 2";
                $stmt = $conn->query($sql);
                $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        <div class="checkout-completed-container">
            <div class="checkout-details">
                <h2>Checkout Completed</h2>
                <p>Thank you for your purchase! Your transaction has been completed successfully.</p>
                <div class="transaction-info">
                    <h3>Transaction Details</h3>
                    <p><strong>Transaction Date:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                    <p><strong>Total Price:</strong> €<?php echo number_format($transaction['total_price'], 2); ?></p>
                    <p><strong>Purchased Books:</strong></p>
                    <ul>
                        <?php foreach ($transaction['books'] as $book): ?>
                            <li><?php echo htmlspecialchars($book['title']); ?> by <?php echo htmlspecialchars($book['author']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <a href="../index.php" class="back-home">Back to Home</a>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 FableFoundry. All rights reserved.</p>
    </footer>
</body>
</html>
