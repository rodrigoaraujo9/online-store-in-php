<?php
session_start();
include 'db.php'; // Ensure your database connection details are correct in this file

if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Not authenticated'];
    header("Location: login.php"); // Redirect to login page or another appropriate page
    exit;
}

$userId = $_SESSION['user_id'];

try {
    // Check if file was uploaded
    if (empty($_FILES['profile_photo']['name'])) {
        throw new Exception("No file uploaded.");
    }

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

    // Resize the image before saving it
    if (!resizeImage($_FILES['profile_photo']['tmp_name'], $target_file, $imageFileType, 500, 500)) {
        throw new Exception("Failed to resize image.");
    }

    // Update the database with the new profile picture URL
    $profileUrl = $target_file;
    $stmt = $conn->prepare("UPDATE users SET profile_picture_url = :profileUrl WHERE user_id = :userId");
    $stmt->bindParam(':profileUrl', $profileUrl);
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();

    // Update the session variable for the profile picture URL
    $_SESSION['profile_picture_url'] = $profileUrl;

    $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Profile photo updated successfully'];
} catch (PDOException $e) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Error updating profile photo: ' . $e->getMessage()];
} catch (Exception $e) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => $e->getMessage()];
}

header("Location: profile.php"); // Redirect back to the profile page
exit;

// Function to resize the image
function resizeImage($sourcePath, $destPath, $imageFileType, $maxWidth, $maxHeight) {
    list($origWidth, $origHeight) = getimagesize($sourcePath);

    $width = $origWidth;
    $height = $origHeight;

    if ($width > $maxWidth || $height > $maxHeight) {
        $aspectRatio = $origWidth / $origHeight;

        if ($width / $height > $aspectRatio) {
            $width = $maxWidth;
            $height = $maxWidth / $aspectRatio;
        } else {
            $height = $maxHeight;
            $width = $maxHeight * $aspectRatio;
        }
    }

    $image_p = imagecreatetruecolor($width, $height);

    switch ($imageFileType) {
        case 'jpg':
        case 'jpeg':
            $image = imagecreatefromjpeg($sourcePath);
            break;
        case 'png':
            $image = imagecreatefrompng($sourcePath);
            break;
        case 'gif':
            $image = imagecreatefromgif($sourcePath);
            break;
        default:
            return false;
    }

    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);

    switch ($imageFileType) {
        case 'jpg':
        case 'jpeg':
            imagejpeg($image_p, $destPath, 90);
            break;
        case 'png':
            imagepng($image_p, $destPath);
            break;
        case 'gif':
            imagegif($image_p, $destPath);
            break;
    }

    imagedestroy($image_p);
    imagedestroy($image);

    return true;
}
?>
