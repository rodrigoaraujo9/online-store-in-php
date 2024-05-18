<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if (isset($_SESSION['flash_message'])) {
    $flash = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']); // Clear the flash message so it doesn't show again on refresh
}

function echoSessionVar($varName) {
    if (isset($_SESSION[$varName])) {
        echo htmlspecialchars($_SESSION[$varName], ENT_QUOTES, 'UTF-8');
    } else {
        echo "Not available";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page - FableFoundry</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <?php if (isset($flash)): ?>
        <div class="alert <?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>
    <header>
        <h2 class="logo-title">FableFoundry</h2>
        <nav class="nav-left">
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="lookups.php">Shop All</a></li>
            </ul>
        </nav>
        <nav class="nav-right">
            <ul>
                <li><a href="#">Selling</a></li>
                <li><a href="#">Wishlist</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="cart.php">Cart</a></li>
            </ul>
        </nav>
    </header>

    <main class="profile-container">
        <div class="profile-card">
            <section class="profile-info">
                <form id="profilePhotoForm" action="upload_profile_photo.php" method="post" enctype="multipart/form-data">
                    <div class="profile-photo-wrapper" onclick="document.getElementById('profilePhotoUpload').click();">
                        <img src="<?php echo !empty($_SESSION['profile_picture_url']) ? htmlspecialchars($_SESSION['profile_picture_url'], ENT_QUOTES, 'UTF-8') . '?t=' . time() : '../images/default_profile.png'; ?>" alt="Profile Photo" class="profile-photo" id="profilePhoto">
                        <div class="edit-overlay">Edit</div>
                        <input type="file" id="profilePhotoUpload" name="profile_photo" accept="image/*" onchange="previewAndUploadPhoto();">
                    </div>
                </form>
                <h1><?php echoSessionVar('name'); ?></h1>
                <h3><?php echoSessionVar('bio'); ?></h3>
                <p><?php echoSessionVar('email'); ?></p>
                
                <div class="profile-action-buttons">
                    <button class="action-button" onclick="openModal('editProfileModal')">Edit Profile</button>
                    <form action="logout.php" method="post">
                        <button type="submit" class="action-button">Logout</button>
                    </form>
                </div>
            </section>
        </div>
    </main>

    <div id="editProfileModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editProfileModal')">&times;</span>
            <h2 class="modal-title">Edit Profile</h2>
            <form action="update_profile.php" method="post" enctype="multipart/form-data">
                <label for="editName">Name:</label>
                <input type="text" id="editName" name="name" value="<?php echoSessionVar('name'); ?>"><br>

                <label for="editEmail">Email:</label>
                <input type="email" id="editEmail" name="email" value="<?php echoSessionVar('email'); ?>"><br>

                <label for="editBio">Bio:</label>
                <textarea id="editBio" name="bio"><?php echoSessionVar('bio'); ?></textarea><br>

                <button type="submit" class="action-button">Save Changes</button>
            </form>
        </div>
    </div>

    <footer>
        <p>© 2024 FableFoundry. All rights reserved.</p>
    </footer>

    <script>
function previewAndUploadPhoto() {
    const fileInput = document.getElementById('profilePhotoUpload');
    const profilePhoto = document.getElementById('profilePhoto');

    if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            profilePhoto.src = e.target.result;

            // Submit the form automatically after preview
            document.getElementById('profilePhotoForm').submit();
        };
        reader.readAsDataURL(fileInput.files[0]);
    }
}

function openModal(modalId) {
    var modal = document.getElementById(modalId);
    modal.style.display = "block";
}

function closeModal(modalId) {
    var modal = document.getElementById(modalId);
    modal.style.display = "none";
}

window.onclick = function(event) {
    var modals = document.querySelectorAll('.modal');
    modals.forEach(function(modal) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });
}
</script>

</body>
</html>
