<?php
$dbFile = '../database.db'; // Path to your SQLite database file
$dsn = 'mysql:host=localhost;dbname=fablefoundry';
$username = 'root';
$password = 'root';

try {
    // Connect to SQLite database
    $conn = new PDO("sqlite:$dbFile");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Example query to fetch data
    $stmt = $conn->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output the results
    foreach ($users as $user) {
        echo "ID: {$user['user_id']}, Name: {$user['name']}, Username: {$user['username']}, Email: {$user['email']}<br>";
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>
