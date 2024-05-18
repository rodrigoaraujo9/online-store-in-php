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
    // Start transaction
    $conn->beginTransaction();

    // Update user info in the database
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

        // Ensure the target directory exists
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                throw new Exception("Failed to create directory: $target_dir");
            }
        }

        $originalFileName = basename($_FILES['profile_photo']['name']);
        $imageFileType = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));

        // Validate file type
        $allowedFileTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowedFileTypes)) {
            throw new Exception("Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.");
        }

        // Validate file size (e.g., max 5MB)
        if ($_FILES['profile_photo']['size'] > 5000000) {
            throw new Exception("File is too large. Maximum file size is 5MB.");
        }

        // Generate a unique file name
        $sanitizedFileName = preg_replace("/[^a-zA-Z0-9.]/", "_", $originalFileName);
        $uniqueFileName = uniqid() . "_" . $sanitizedFileName;
        $target_file = $target_dir . $uniqueFileName;

        // Attempt to move the uploaded file to the target directory
        if (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file)) {
            throw new Exception("Failed to move uploaded file. Check permissions and file size limits.");
        }

        // Update the database with the new profile picture URL
        $profileUrl = $target_file;
        $stmt = $conn->prepare("UPDATE users SET profile_picture_url = :profileUrl WHERE user_id = :userId");
        $stmt->bindParam(':profileUrl', $profileUrl);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();

        // Update the session variable for the profile picture URL
        $_SESSION['profile_picture_url'] = $profileUrl;
    }

    // Commit transaction
    $conn->commit();

    $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Profile updated successfully'];
} catch (PDOException $e) {
    // Rollback transaction on error
    $conn->rollBack();
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Error updating profile: ' . $e->getMessage()];
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollBack();
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => $e->getMessage()];
}

header("Location: profile.php"); // Redirect back to the profile page
exit;
?>
