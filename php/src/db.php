<?php
try {
    $pdo = new PDO('mysql:host=mysql;dbname=etuServices', 'root', 'rootpassword');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $redis = new Redis();
    $redis->connect('redis', 6379);
} catch (PDOException $e) {
    die("Erreur de connexion BDD : " . $e->getMessage());
} catch (RedisException $e) {
    die("Erreur de connexion Redis : " . $e->getMessage());
}
?>
