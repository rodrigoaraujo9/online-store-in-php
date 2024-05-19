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

    // Default profile picture URL
    $profilePictureUrl = '../images/profile_picture.png'; // Ensure this path points to your default image

    try {
        // Check if username or email already exists
        $checkSql = "SELECT username, email FROM users WHERE username = :username OR email = :email";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bindParam(':username', $username);
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();

        // Debug: Print the number of rows returned
        $rowCount = $checkStmt->rowCount();
        echo "Row count: " . $rowCount . "<br>";

        if ($rowCount > 0) {
            $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
            // Debug: Print the existing user details
            echo "Existing User: ";
            print_r($existing);
            echo "<br>";

            if ($existing['username'] === $username) {
                $error = "Username already in use. Please choose another.";
            }
            if ($existing['email'] === $email) {
                $error = "Email address already in use. Please use a different email address.";
            }
        } else {
            // Proceed with registration if no conflicts
            $sql = "INSERT INTO users (name, username, password, email, role, registered_date, profile_picture_url) 
                    VALUES (:name, :username, :password, :email, 'User', DATE('now'), :profile_picture_url)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':profile_picture_url', $profilePictureUrl);

            try {
                $stmt->execute();
                $_SESSION['user_id'] = $conn->lastInsertId();
                $_SESSION['username'] = $username;
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = 'User'; // Set the role to User
                $_SESSION['profile_picture_url'] = $profilePictureUrl;

                header("Location: profile.php");
                exit;
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $error = "Email or username already in use.";
                } else {
                    $error = "Registration failed. Please try again later.";
                }
            }
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
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
        <h2 class="logo-title"><a href="../index.php">FableFoundry</a></h2>
    </header>

    <main class="login-container">
        <form class="login-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h1 class="form-title">Create an account</h1>
            <?php if (!empty($error)): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
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
