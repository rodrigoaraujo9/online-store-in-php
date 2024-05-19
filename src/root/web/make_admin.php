<?php
session_start();
include 'db.php'; // Ensure your database connection details are correct in this file

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $newRole = $_POST['new_role'];

    $stmt = $conn->prepare("UPDATE users SET role = :newRole WHERE user_id = :userId");
    $stmt->bindParam(':newRole', $newRole);
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();

    $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'User role updated successfully.'];
    header("Location: make_admin.php");
    exit;
}

$usersStmt = $conn->prepare("SELECT user_id, username, role FROM users");
$usersStmt->execute();
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Manage User Roles - FableFoundry</title>
    <link rel="stylesheet" href="style2.css">
    <style>
        .manage-users-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 5rem;
        }

        .user-table {
            width: 80%;
            border-collapse: collapse;
            margin: 1.5rem 0;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        }

        .user-table th, .user-table td {
            border: 1px solid var(--darker-forest-green);
            padding: 0.75rem 1rem;
            text-align: left;
        }

        .user-table th {
            background-color: var(--pastel-green);
            color: var(--text-dark);
            font-family: 'CustomFontSemi';
        }

        .user-table td {
            background-color: var(--almost-white);
            color: var(--text-dark);
            font-family: 'CustomFont';
        }

        .promote-button, .demote-button {
            font-family: 'CustomFontSemi';
            background: linear-gradient(135deg, var(--button-hover-purple) 0%, var(--pastel-green) 100%);
            color: var(--almost-white);
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 1.25rem;
            cursor: pointer;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .promote-button:hover, .demote-button:hover {
            transform: scale(1.1);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.2);
        }

        .alert {
            width: 80%;
            margin-bottom: 1rem;
            padding: 0.75rem 1rem;
            border-radius: 0.25rem;
            text-align: center;
            font-family: 'CustomFontSemi';
        }

        .alert.success {
            background-color: var(--pastel-green);
            color: var(--darker-forest-green);
        }

        .alert.error {
            background-color: var(--pastel-red);
            color: var(--text-dark);
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

    <main class="manage-users-container">
        <h1>Manage User Roles</h1>
        <?php echo displayFlashMessage(); ?>
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
                            <?php if ($user['role'] === 'User'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                                    <input type="hidden" name="new_role" value="Admin">
                                    <button type="submit" class="promote-button">Promote to Admin</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                                    <input type="hidden" name="new_role" value="User">
                                    <button type="submit" class="demote-button">Demote to User</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <footer>
        <p>© 2024 FableFoundry. All rights reserved.</p>
    </footer>
</body>
</html>
