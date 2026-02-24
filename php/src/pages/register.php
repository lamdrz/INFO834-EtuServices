<?php
session_start();
require_once '../db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($nom && $prenom && $email && $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $email, $hashedPassword]);
            $message = "Compte créé avec succès ! <a href='login.php'>Connectez-vous ici</a>";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "Cet email est déjà utilisé.";
            } else {
                $message = "Erreur lors de l'inscription : " . $e->getCode() . " - " . $e->getMessage();
            }
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
</head>
<body>
    <h1>Inscription</h1>
    <?php if ($message): ?>
        <p><?= $message ?></p>
    <?php endif; ?>
    
    <form method="post">
        <div>
            <label>Nom:</label>
            <input type="text" name="nom" required>
        </div>
        <div>
            <label>Prénom:</label>
            <input type="text" name="prenom" required>
        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Mot de passe:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">S'inscrire</button>
    </form>
    <p><a href="login.php">Se connecter</a></p>
</body>
</html>
