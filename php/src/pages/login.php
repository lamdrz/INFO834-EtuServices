<?php
session_start();
require_once '../db.php';

$window = 600; // = 10min
$limit = 10;

$message = '';

function checkRateLimit($redis, $userId) {
    global $window, $limit;
    
    $redisKey = "connections:user_" . $userId;
    $now = time();

    // Si 1ere connection, la clé n'existe pas encore, on autorise
    if (!$redis->exists($redisKey)) {
        return true;
    }

    // On supprime les connexions hors de la fenêtre
    $redis->zRemRangeByScore($redisKey, 0, $now - $window);

    // Puis on compte le nombre de connexions restantes dans la fenêtre
    return $redis->zCard($redisKey) < $limit;
}


function incrementGlobal($redis, $userId) {
    $totalKey = "connections:global:total";
    $redis->zIncrBy($totalKey, 1, $userId);

    $latestKey = "connections:global:latest";
    $redis->zAdd($latestKey, time() . microtime(true), $userId);
    $redis->zRemRangeByRank($latestKey, 0, -11); // Garde que les 10 dernières connexions
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            if (!checkRateLimit($redis, $user['id'])) {
                $message = "Limite atteinte.";
            } else {
                $redisKey = "connections:user_" . $user['id'];
                $now = time();
                $redis->zAdd($redisKey, $now, $now . microtime(true));
                
                // Expire en entier après la fenètre
                $redis->expire($redisKey, $window + 1);

                incrementGlobal($redis, $user['id']);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['prenom'] = $user['prenom'];
                $_SESSION['nom'] = $user['nom'];
                header("Location: home.php"); // Redirection après succès
                exit;
            }
        } else {
            $message = "Email ou mot de passe incorrect.";
        }


    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
</head>
<body>
    <h1>Connexion</h1>
    <?php if ($message): ?>
        <p style="color: red;"><?= $message ?></p>
    <?php endif; ?>

    <form method="post">
        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Mot de passe:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Se connecter</button>
    </form>
    <p><a href="register.php">S'inscrire</a></p>
</body>
</html>
