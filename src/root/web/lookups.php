<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show All Books - FableFoundry</title>
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="lookups.css"> <!-- New CSS file for specific styles -->
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
    <main class="lookups-main">
        <aside class="lookups-sidebar">
            <form method="GET">
                <section class="lookups-filter-sort-bar">
                    <div class="lookups-book-filter">
                        <label for="genre-select">Genre:</label>
                        <select id="genre-select" name="genre">
                            <option value="none">None</option>
                            <?php
                            // Fetch genres from the database
                            $sql = "SELECT genre_id, name FROM genres";
                            $stmt = $conn->query($sql);
                            $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Display genre options
                            foreach ($genres as $genre) {
                                echo '<option value="' . $genre['genre_id'] . '">' . htmlspecialchars($genre['name']) . '</option>';
                            }
                            ?>
                        </select>

                        <label for="age-select">Age Group:</label>
                        <select id="age-select" name="age">
                            <option value="none">None</option>
                            <option value="Adults">Adults</option>
                            <option value="Teens">Teens</option>
                            <option value="Children">Children</option>
                        </select>

                        <label for="condition-select">Condition:</label>
                        <select id="condition-select" name="condition">
                            <option value="none">None</option>
                            <option value="New">New</option>
                            <option value="Used">Used</option>
                        </select>
                    </div>

                    <div class="lookups-book-sort">
                        <label for="sort-select">Sort by:</label>
                        <select id="sort-select" name="sort">
                            <option value="title">Title</option>
                            <option value="author">Author</option>
                            <option value="listed_price">Price</option>
                        </select>
                    </div>

                    <button class="lookups-apply" type="submit">Apply Filters</button>
                </section>
            </form>
        </aside>

        <div class="lookups-books-container">
            <section class="lookups-books-grid">
                <?php
                // Initialize filter variables
                $genreFilter = isset($_GET['genre']) && $_GET['genre'] !== 'none' ? $_GET['genre'] : null;
                $ageFilter = isset($_GET['age']) && $_GET['age'] !== 'none' ? $_GET['age'] : null;
                $conditionFilter = isset($_GET['condition']) && $_GET['condition'] !== 'none' ? $_GET['condition'] : null;
                $sortFilter = isset($_GET['sort']) ? $_GET['sort'] : 'title';

                // Build the SQL query with filters
                $sql = "SELECT book_id, title, author, listed_price, image_url FROM books WHERE 1=1";
                if ($genreFilter) {
                    $sql .= " AND genre_id = :genre";
                }
                if ($ageFilter && $ageFilter !== 'none') {
                    $sql .= " AND age_group = :age";
                }
                if ($conditionFilter && $conditionFilter !== 'none') {
                    $sql .= " AND condition = :condition";
                }
                $sql .= " ORDER BY $sortFilter";

                try {
                    $stmt = $conn->prepare($sql);
                    if ($genreFilter) {
                        $stmt->bindParam(':genre', $genreFilter);
                    }
                    if ($ageFilter && $ageFilter !== 'none') {
                        $stmt->bindParam(':age', $ageFilter);
                    }
                    if ($conditionFilter && $conditionFilter !== 'none') {
                        $stmt->bindParam(':condition', $conditionFilter);
                    }
                    $stmt->execute();

                    // Display filtered books
                    while ($book = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<div class="lookups-book-item">';
                        // Add hyperlink around the book item to redirect to book_details.php
                        echo '<a href="book_details.php?book_id=' . $book['book_id'] . '">';
                        echo '<img src="../images/' . htmlspecialchars($book['image_url']) . '" alt="' . htmlspecialchars($book['title']) . '">';
                        echo '<h3 class="lookups-book-item-title">' . htmlspecialchars($book['title']) . '</h3>';
                        echo '<p class="lookups-book-item-author">by ' . htmlspecialchars($book['author']) . '</p>';
                        echo '<p class="lookups-book-item-price">€' . number_format($book['listed_price'], 2) . '</p>';
                        echo '</a>'; // Close the hyperlink
                        echo '</div>';
                    }
                } catch (PDOException $e) {
                    echo 'Database error: ' . $e->getMessage();
                }
                ?>
            </section>
        </div>
    </main>

    <footer>
        <p>© 2024 FableFoundry. All rights reserved.</p>
    </footer>
</body>
</html>
