<?php
session_start();

include 'db.php'; // Include your database connection

// Ensure only admin users can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

// Fetch all users from the database
$stmt = $conn->prepare("SELECT user_id, username, role FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle promoting/demoting users
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['user_id'];
    $newRole = $_POST['new_role'];

    $updateStmt = $conn->prepare("UPDATE users SET role = ? WHERE user_id = ?");
    $updateStmt->execute([$newRole, $userId]);

    // Set a flash message
    $_SESSION['flash_message'] = [
        'type' => 'success',
        'message' => 'User role updated successfully.'
    ];

    header("Location: make_admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Admin - FableFoundry</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <header>
        <h2 class="logo-title"><a href="../index.php">FableFoundry</a></h2>
        <nav class="nav-left">
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="lookups.php">Shop All</a></li>
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

    <main class="admin-container">
        <div class="admin-card">
            <h2>Manage User Roles</h2>
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert <?php echo htmlspecialchars($_SESSION['flash_message']['type']); ?>">
                    <?php echo htmlspecialchars($_SESSION['flash_message']['message']); ?>
                </div>
                <?php unset($_SESSION['flash_message']); ?>
            <?php endif; ?>

            <table class="user-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td>
                                <form method="post" action="make_admin.php">
                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                    <?php if ($user['role'] === 'Admin'): ?>
                                        <input type="hidden" name="new_role" value="User">
                                        <button type="submit" class="role-button demote-button">Demote to User</button>
                                    <?php else: ?>
                                        <input type="hidden" name="new_role" value="Admin">
                                        <button type="submit" class="role-button promote-button">Promote to Admin</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <p>© 2024 FableFoundry. All rights reserved.</p>
    </footer>
</body>
</html>
