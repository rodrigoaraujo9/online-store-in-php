<?php
session_start();
include 'db.php'; // Ensure your database connection details are correct in this file

if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Not authenticated'];
    header("Location: login.php"); // Redirect to login page or another appropriate page
    exit;
}

$userId = $_SESSION['user_id'];
$name = $_POST['name'];
$email = $_POST['email'];
$bio = $_POST['bio'];

try {
    // Update SQL query
    $sql = "UPDATE users SET name = :name, email = :email, bio = :bio WHERE user_id = :userId";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':bio', $bio);
    $stmt->bindParam(':userId', $userId);
    
    $stmt->execute();

    // If the database is updated successfully, also update the session variables
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    $_SESSION['bio'] = $bio;

    // Handle profile photo upload
    if (!empty($_FILES['profile_photo']['name'])) {
        $target_dir = "../images/profiles/";
        $originalFileName = basename($_FILES['profile_photo']['name']);
        $imageFileType = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
        $sanitizedFileName = preg_replace("/[^a-zA-Z0-9.]/", "_", $originalFileName); // Sanitizing file name
        $target_file = $target_dir . $sanitizedFileName;
    
        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file)) {
            $stmt = $conn->prepare("UPDATE users SET profile_picture_url = :profileUrl WHERE user_id = :userId");
            $profileUrl = $target_file;
            $stmt->bindParam(':profileUrl', $profileUrl);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
    
            // Update the session variable for the profile picture URL
            $_SESSION['profile_picture_url'] = $profileUrl;
        }
    }
    

    $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Profile updated successfully'];
} catch (PDOException $e) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Error updating profile: ' . $e->getMessage()];
}

header("Location: profile.php"); // Redirect back to the profile page
exit;
?>
