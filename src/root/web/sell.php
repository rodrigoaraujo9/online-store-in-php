<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $title = $_POST["title"];
    $author = $_POST["author"];
    $isbn = $_POST["isbn"];
    $condition = $_POST["condition"];
    $price = $_POST["price"];
    $description = $_POST["description"];
    
    // Process the image upload (save to server, store file path in database, etc.)
    // For demonstration purposes, let's assume the image is stored in a directory named 'uploads'
    $image_path = 'uploads/' . $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    
    // Now you can save the form data and image path to your database or perform other actions
    
    // For demonstration purposes, let's just display the submitted data
    echo "<h2>Book Details</h2>";
    echo "<p>Title: $title</p>";
    echo "<p>Author: $author</p>";
    echo "<p>ISBN: $isbn</p>";
    echo "<p>Condition: $condition</p>";
    echo "<p>Price: $price</p>";
    echo "<p>Description: $description</p>";
    echo "<p>Image Path: $image_path</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell Your Books</title>
    <!-- Include your CSS file here -->
    <link rel="stylesheet" href="style2.css">
    <style>
        /* Styles for star rating */
        .stars input[type="radio"] {
            display: none;
        }
        .stars label {
            font-size: 30px;
            color: #ccc;
            cursor: pointer;
            transition: color 0.3s;
        }
        .stars input[type="radio"]:checked ~ label,
        .stars input[type="radio"]:checked ~ label:hover,
        .stars input[type="radio"]:checked ~ label ~ input[type="radio"] + label {
            color: gold;
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
                include 'db.php';

                // Fetch genres from the database
                $sql = "SELECT genre_id, name FROM genres LIMIT 2";
                $stmt = $conn->query($sql);
                $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Display genre filters
                foreach ($genres as $genre) {
                    echo '<li><a href="lookups.php?genre=' . $genre['genre_id'] . '">' . htmlspecialchars($genre['name']) . '</a></li>';
                }
                ?>
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

    <main>
        <section class="sell-container">
            <h2>Enter Book Details</h2>
            <form action="submit_book.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="author">Author:</label>
                    <input type="text" id="author" name="author" required>
                </div>
                <div class="form-group">
                    <label for="isbn">ISBN:</label>
                    <input type="text" id="isbn" name="isbn" required>
                </div>
                <div class="form-group">
                    <label for="genre">Genre:</label>
                    <select id="genre" name="genre_id" required>
                        <option value="">Select Genre</option>
                        <!-- Populate options dynamically from database -->
                    </select>
                </div>
                <div class="form-group-condition">
                <label for="condition">Condition:</label>
                    <div class="stars">
                    <input type="radio" id="star1" name="condition" value="1">
                        <label for="star1">☆</label>
                        <input type="radio" id="star2" name="condition" value="2">
                        <label for="star2">☆</label>
                        <input type="radio" id="star3" name="condition" value="3">
                        <label for="star3">☆</label>
                        <input type="radio" id="star4" name="condition" value="4">
                        <label for="star4">☆</label>
                        <input type="radio" id="star5" name="condition" value="5">
                        <label for="star5">☆</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="price">Listed Price:</label>
                    <input type="number" id="price" name="listed_price" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label for="image">Upload Image:</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label for="age_group">Age Group:</label>
                    <select id="age_group" name="age_group" required>
                        <option value="">Select Age Group</option>
                        <option value="Children">Children</option>
                        <option value="Teens">Teens</option>
                        <option value="Adults">Adults</option>
                    </select>
                </div>
                <button class="sell-page-button" type="submit">Sell</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Your Bookstore. All rights reserved.</p>
    </footer>

    <script>
        const stars = document.querySelectorAll('.stars input[type="radio"]');

        stars.forEach((star, index) => {
            star.addEventListener('change', function() {
                // Uncheck all stars
                stars.forEach(s => s.checked = false);

                // Check selected star and stars to its left
                for (let i = 0; i <= index; i++) {
                    stars[i].checked = true;
                }
            });
        });
    </script>
</body>
</html>
