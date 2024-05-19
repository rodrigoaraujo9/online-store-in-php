<?php
session_start();
include 'db.php'; // Ensure your database connection details are correct in this file

// Ensure only admin can access this page
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_genre'])) {
        // Retrieve form data for genre
        $name = $_POST["name"];
        $description = $_POST["description"];

        // Insert the new genre into the database
        $stmt = $conn->prepare("INSERT INTO genres (name, description) VALUES (:name, :description)");
        $stmt->execute([
            ':name' => $name,
            ':description' => $description
        ]);

        $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Genre added successfully.'];
    } elseif (isset($_POST['add_age_group'])) {
        // Retrieve form data for age group
        $age_group = $_POST["age_group"];

        // Insert the new age group into the database
        $stmt = $conn->prepare("INSERT INTO age_groups (age_group) VALUES (:age_group)");
        $stmt->execute([
            ':age_group' => $age_group
        ]);

        $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Age group added successfully.'];
    }
    header("Location: admin_op.php");
    exit;
}

function displayFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return '<div class="alert ' . htmlspecialchars($flash['type']) . '">' . htmlspecialchars($flash['message']) . '</div>';
    }
    return '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Operations - FableFoundry</title>
    <link rel="stylesheet" href="style2.css">
    <style>
        .form-spacing {
            margin-bottom: 3rem; /* Adjust this value as needed */
        }
    </style>
</head>
<body class="sellbody">
<header>
    <h2 class="logo-title"><a href="../index.php">FableFoundry</a></h2>
    <nav class="nav-left">
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="lookups.php">Shop All</a></li>
            <?php
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
    <section class="sell-container">
        <div class="selling-details">
            <h2>Add Genre</h2>
            <?php echo displayFlashMessage(); ?>

            <!-- Add Genre Form -->
            <form action="admin_op.php" method="post" class="form-spacing">
                <input type="hidden" name="add_genre" value="1">
                <div class="form-group">
                    <label for="name">Genre Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                </div>
                <button class="sell-page-button" type="submit">Add</button>
            </form>
            <h2>Add Age Group</h2>
            <!-- Add Age Group Form -->
            <form action="admin_op.php" method="post" class="form-spacing">
                <input type="hidden" name="add_age_group" value="1">
                <div class="form-group">
                    <label for="age_group">Age Group:</label>
                    <input type="text" id="age_group" name="age_group" required>
                </div>
                <button class="sell-page-button" type="submit">Add</button>
            </form>
        </div>
    </section>
</main>

<footer>
    <p>© 2024 FableFoundry. All rights reserved.</p>
</footer>

</body>
</html>
