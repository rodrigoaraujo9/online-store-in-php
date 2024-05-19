<?php
include 'db.php';

session_start();

// Check if the user is logged in, if not, redirect them to the login page
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: /web/login.php");
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
            // Redirect back to lookups.php after adding the book to the wishlist
            header("Location: lookups.php");
            exit;
        } else {
            echo "Error adding book to wishlist: " . $stmt->errorInfo()[2];
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
            // Redirect back to lookups.php after adding the book to the cart
            header("Location: lookups.php");
            exit;
        } else {
            echo "Error adding book to cart: " . $stmt->errorInfo()[2];
        }
    } else {
        echo "Book already exists in the cart.";
    }
}

// Fetch genres from the database
$sql = "SELECT genre_id, name FROM genres";
$stmt = $conn->query($sql);
$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch books based on filters if set
$filter_genre = isset($_GET['genre']) ? $_GET['genre'] : null;
$filter_age = isset($_GET['age']) ? $_GET['age'] : null;
$filter_condition = isset($_GET['condition']) ? $_GET['condition'] : null;
$sort_order = isset($_GET['sort']) ? $_GET['sort'] : 'title';

$filter_query = "WHERE 1=1";
$filter_params = [];

if ($filter_genre && $filter_genre !== 'none') {
    $filter_query .= " AND genre_id = :genre_id";
    $filter_params[':genre_id'] = $filter_genre;
}

if ($filter_age && $filter_age !== 'none') {
    $filter_query .= " AND age_group = :age_group";
    $filter_params[':age_group'] = $filter_age;
}

if ($filter_condition && $filter_condition !== 'none') {
    $filter_query .= " AND condition = :condition";
    $filter_params[':condition'] = $filter_condition;
}

$sql_books = "SELECT book_id, title, author, listed_price, image_url FROM books $filter_query ORDER BY $sort_order";
$stmt_books = $conn->prepare($sql_books);
$stmt_books->execute($filter_params);
$books = $stmt_books->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show All Books - FableFoundry</title>
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="lookups.css"> <!-- New CSS file for specific styles -->
    <style>
       
        .book-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.actions a {
    font-family: 'CustomFontSemi', Arial, sans-serif;
    padding: 0.5rem 1rem;
    background-color: var(--darker-forest-green);
    color: #fff;
    border-radius: 0.25rem;
    text-decoration: none;
    transition: background-color 0.3s ease;
    text-align: center; /* Ensure the text is centered */
}

.actions a:hover {
    background-color: var(--text-dark);
}
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
                // Display genre filters
                foreach ($genres as $genre) {
                    echo '<li><a href="lookups.php?genre=' . $genre['genre_id'] . '">' . htmlspecialchars($genre['name']) . '</a></li>';
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
    <main class="lookups-main">
        <aside class="lookups-sidebar">
            <form method="GET">
                <section class="lookups-filter-sort-bar">
                    <div class="lookups-book-filter">
                        <label for="genre-select">Genre:</label>
                        <select id="genre-select" name="genre">
                            <option value="none">None</option>
                            <?php
                            // Display genre options
                            foreach ($genres as $genre) {
                                echo '<option value="' . $genre['genre_id'] . '"' . ($filter_genre == $genre['genre_id'] ? ' selected' : '') . '>' . htmlspecialchars($genre['name']) . '</option>';
                            }
                            ?>
                        </select>

                        <label for="age-select">Age Group:</label>
                        <select id="age-select" name="age">
                            <option value="none">None</option>
                            <option value="Adults"<?php echo $filter_age == 'Adults' ? ' selected' : ''; ?>>Adults</option>
                            <option value="Teens"<?php echo $filter_age == 'Teens' ? ' selected' : ''; ?>>Teens</option>
                            <option value="Children"<?php echo $filter_age == 'Children' ? ' selected' : ''; ?>>Children</option>
                        </select>

                        <label for="condition-select">Condition:</label>
                        <select id="condition-select" name="condition">
                            <option value="none">None</option>
                            <option value="New"<?php echo $filter_condition == 'New' ? ' selected' : ''; ?>>New</option>
                            <option value="Used"<?php echo $filter_condition == 'Used' ? ' selected' : ''; ?>>Used</option>
                        </select>
                    </div>

                    <div class="lookups-book-sort">
                        <label for="sort-select">Sort by:</label>
                        <select id="sort-select" name="sort">
                            <option value="title"<?php echo $sort_order == 'title' ? ' selected' : ''; ?>>Title</option>
                            <option value="author"<?php echo $sort_order == 'author' ? ' selected' : ''; ?>>Author</option>
                            <option value="listed_price"<?php echo $sort_order == 'listed_price' ? ' selected' : ''; ?>>Price</option>
                        </select>
                    </div>

                    <button class="lookups-apply" type="submit">Apply Filters</button>
                </section>
            </form>
        </aside>

        <div class="lookups-books-container">
            <section class="lookups-books-grid">
                <?php
                // Display filtered books
                foreach ($books as $book): ?>
                    <div class="book-item">
                        <a href="book_details.php?book_id=<?php echo $book['book_id']; ?>">
                            <img src="../images/<?php echo htmlspecialchars($book['image_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                            <h3 class="book-item-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p class="book-item-author">by <?php echo htmlspecialchars($book['author']); ?></p>
                            <p class="book-item-price">€<?php echo number_format($book['listed_price'], 2); ?></p>
                        </a>
                        <div class="actions">
                            <a href="lookups.php?add_to_wishlist=true&book_id=<?php echo $book['book_id']; ?>">Wishlist</a>
                            <a href="lookups.php?add_to_cart=true&book_id=<?php echo $book['book_id']; ?>">Cart</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>
        </div>
    </main>

    <footer>
        <p>© 2024 FableFoundry. All rights reserved.</p>
    </footer>
</body>
</html>
