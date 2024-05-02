<?php
include 'db.php';  // Ensure your database connection details are correct in this file

session_start(); // Start session at the beginning to ensure it's available

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // Include user_id in your SELECT query
        $sql = "SELECT user_id, username, name, email, password, role, bio, profile_picture_url FROM users WHERE username = :username";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            // Set all necessary session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['bio'] = $user['bio'];
            $_SESSION['profile_picture_url'] = $user['profile_picture_url'];

            header("Location: profile.php");
            exit;
        } else {
            // Handle errors for invalid username/password with a simple message
            $login_error = 'Invalid username or password.';
        }
    } catch (PDOException $e) {
        $login_error = 'Error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FableFoundry</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <header>
        <h2 class="logo-title"><a href="../index.php">FableFoundry</a></h2>
    </header>

    <main class="login-container">
        <form class="login-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h1 class="form-title">Login to Your Account</h1>
            <?php if (isset($login_error)): ?>
                <p class="error"><?php echo $login_error; ?></p> <!-- Display the login error if set -->
            <?php endif; ?>
            <div class="form-field">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-button"><h2>Enter</h2></button>
            <button type="button" class="text-button" onclick="window.location.href='register.php';">Create Account</button>
        </form>
    </main>

    <footer>
        <p>© 2024 FableFoundry. All rights reserved.</p>
    </footer>
</body>
</html>    