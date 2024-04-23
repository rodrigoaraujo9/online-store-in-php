<?php
session_start();
include 'db.php';

$error = ''; // Initialize an empty error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if username or email already exists
    $checkSql = "SELECT username, email FROM users WHERE username = :username OR email = :email";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bindParam(':username', $username);
    $checkStmt->bindParam(':email', $email);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
        if ($existing['username'] === $username) {
            $error = "Username already in use. Please choose another.";
        }
        if ($existing['email'] === $email) {
            $error = "Email address already in use. Please use a different email address.";
        }
    } else {
        // Proceed with registration if no conflicts
        $sql = "INSERT INTO users (name, username, password, email, role, registered_date) 
                VALUES (:name, :username, :password, :email, 'Buyer', DATE('now'))";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':email', $email);

        try {
            $stmt->execute();
            $_SESSION['username'] = $username;
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            header("Location: profile.php");
            exit;
        } catch (PDOException $e) {
            // Check if the error is a unique constraint violation
            if ($e->getCode() == 23000) {
                // Additional check to handle or log the error more specifically if needed
                $error = "Email or username already in use.";
            } else {
                $error = "Registration failed. Please try again later.";
            }
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
            <?php if (!empty($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
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
            <button type="button" class="text-button" onclick="window.location.href='login.php';">Login to existing account</button>
        </form>
    </main>

    <footer>
        <p>© 2024 FableFoundry. All rights reserved.</p>
    </footer>
</body>
</html>