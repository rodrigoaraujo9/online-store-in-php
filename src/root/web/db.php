<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];
$descendedPath = dirname($rootPath);
$dbFile = $descendedPath . '/database.db';
$dsn = 'mysql:host=localhost;dbname=fablefoundry';
$username = 'root';
$password = 'root';

try {

    $conn = new PDO("sqlite:$dbFile");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $stmt = $conn->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);


} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>
