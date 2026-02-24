<?php
require_once 'db.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(50) NOT NULL,
        prenom VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    )";

    $pdo->exec($sql);

} catch (PDOException $e) {
    die("Erreur de connexion ou de création : " . $e->getMessage());
}
?>
