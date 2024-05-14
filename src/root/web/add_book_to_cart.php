<?php
include 'db.php'; // Include your database connection

session_start();

if (!isset($_SESSION['user_id'])) {
    // Handle the case where the user is not logged in
    die('User not logged in');
}

if (!isset($_GET['book_id'])) {
    // Handle the case where book_id is not provided
    die('Book ID not provided');
}

$user_id = $_SESSION['user_id'];
$book_id = $_GET['book_id'];

// Check if the book exists
$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    // Handle the case where the book does not exist
    die('Book not found');
}

// Add the book to the cart
$stmt = $conn->prepare("INSERT INTO shopping_cart (user_id, book_id, quantity) VALUES (?, ?, 1)");
$result = $stmt->execute([$user_id, $book_id]);

if ($result) {
    // Return a success message if the book was successfully added to the cart
    echo 'Book added to cart';
} else {
    // Handle errors if necessary
    echo 'Error adding book to cart';
}
?>
