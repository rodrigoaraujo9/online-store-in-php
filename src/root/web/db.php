<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];
$descendedPath = dirname($rootPath);
$dbFile = $descendedPath . '/database.db'; // Make sure the path is correct relative to the document root.

try {
    $conn = new PDO("sqlite:$dbFile");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
