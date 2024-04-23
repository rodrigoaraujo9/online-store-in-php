<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $sql = "SELECT username, name, email, password FROM users WHERE username = :username";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {

            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $user['email'];
                $_SESSION['name'] = $user['name'];
                header("Location: profile.php");
                exit;
            } else {
                // Incorrect password
                echo "<script>alert('Invalid username or password.');</script>";
            }
        } else {
            // User does not exist
            echo "<script>alert('Invalid username or password.');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
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
        <h2 class="logo-title">FableFoundry</h2>
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