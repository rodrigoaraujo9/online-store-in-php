<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, username, password, email, role, registered_date) 
            VALUES (:name, :username, :password, :email, 'Buyer', DATE('now'))";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':email', $email);

    try {
        if ($stmt->execute()) {
            // Assuming registration is successful, set session variables and redirect
            $_SESSION['username'] = $username;
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email; // Add email to session
            header("Location: profile.php");
            exit;
        } else {
            echo "Registration failed. Please try again.";
        }
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            // Unique constraint violation, email already exists
            echo "Email address is already in use. Please try again with a different email address.";
        } else {
            echo "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration - FableFoundry</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <header>
        <h2 class="logo-title">FableFoundry</h2>
    </header>

    <main class="login-container">
        
        <form class="login-form" method="POST">
            <h1 class="form-title">Create an account</h1>
            <div class="form-field">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-field">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-field">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-button"><h2>Register</h2></button>
        </form>
    </main>

    <footer>
        <p>© 2024 FableFoundry. All rights reserved.</p>
    </footer>
</body>
</html>
