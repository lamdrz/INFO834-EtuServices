<?php
session_start();
require_once '../db.php';

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

function get10LatestConnections($redis) {
    $latestKey = "connections:global:latest";
    return $redis->zRevRange($latestKey, 0, 9);
}

function get3TopUsers($redis) {
    $globalKey = "connections:global:total";
    return $redis->zRevRange($globalKey, 0, 2, ['WITHSCORES' => true]);
}

?>


<!DOCTYPE html>
<html>
<head>
    <title>Accueil</title>
</head>
<body>
    <h1>Bienvenue, <?= htmlspecialchars($_SESSION['prenom']) . ' ' . htmlspecialchars($_SESSION['nom']); ?> !</h1>
    <a href="logout.php">Se déconnecter</a>

    <h2>10 dernières connexions</h2>
    <ul>
        <?php foreach (get10LatestConnections($redis) as $userId => $timestamp): ?>
            <li>User <?= $userId ?> - <?= date("Y-m-d H:i:s", $timestamp) ?></li>
        <?php endforeach; ?>
    </ul>

    <h2>Top 3 des utilisateurs les plus connectés</h2>
    <ol>
        <?php foreach (get3TopUsers($redis) as $userId => $count): ?>
            <li>User ID: <?= $userId ?> - Connexions: <?= $count ?></li>
        <?php endforeach; ?>
    </ol>
</body>
</html>
