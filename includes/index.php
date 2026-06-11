<?php
// index.php
session_start();
require 'config/database.php';

$erreur = '';
$mode = isset($_GET['action']) && $_GET['action'] == 'inscription' ? 'inscription' : 'connexion';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // --- Traitement de l'INSCRIPTION ---
    if (isset($_POST['register'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        
        // Hachage du mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); //
        
        try {
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (username, email, password) VALUES (?, ?, ?)"); //[cite: 1]
            if ($stmt->execute([$username, $email, $hashed_password])) {
                // Récupération de l'ID généré pour la connexion automatique
                $user_id = $pdo->lastInsertId();
                
                // Connexion automatique après inscription
                $_SESSION['user'] = [
                    'id' => $user_id,
                    'username' => $username,
                    'email' => $email,
                    'role' => 'user', // Rôle par défaut[cite: 3]
                    'avatar' => 'default.png'
                ]; //[cite: 1]
                
                header('Location: user/dashboard.php');
                exit;
            }
        } catch (PDOException $e) {
            $erreur = "Erreur : Cet identifiant ou cet e-mail est déjà utilisé.";
        }
    }

    // --- Traitement de la CONNEXION ---
    if (isset($_POST['login'])) {
        $identifiant = trim($_POST['identifiant']); // Peut être l'email ou le username[cite: 2]
        $password = trim($_POST['password']);
        
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE username = ? OR email = ?"); //[cite: 3]
        $stmt->execute([$identifiant, $identifiant]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) { //[cite: 3]
            // Stockage des informations en session[cite: 1]
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
                'avatar' => $user['avatar']
            ];
            
            // Redirection dynamique selon le rôle
            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: user/dashboard.php');
            }
            exit;
        } else {
            $erreur = "Identifiants incorrects"; // Message d'erreur défini dans la feuille de route[cite: 3]
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GESTBIBLIO - Authentification</title>
    <link rel = "stylesheet " href ="../styles/styles.css">        
</head>
<body>

    <div class="auth-container">
        <!-- Logo Textuel GESTBIBLIO -->
        <div class="logo-text">GESTBIBLIO</div>
        <div class="logo-sub">GESTIONNAIRE DE BIBLIOTHÈQUE</div>

        <?php if (!empty($erreur)): ?>
            <div class="erreur-msg"><?php echo htmlspecialchars($erreur); ?></div>
        <?php endif; ?>

        <?php if ($mode === 'connexion'): ?>
            <!-- FORMULAIRE DE CONNEXION -->
            <h3>Se connecter pour continuer</h3>
            <form action="index.php" method="POST">
                <div class="form-group">
                    <label>E-MAIL OU USERNAME</label>
                    <input type="text" name="identifiant" required>
                </div>
                <div class="form-group">
                    <label>MOT DE PASSE</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" name="login">Se connecter</button>
            </form>
            <a href="index.php?action=inscription" class="toggle-link">Créer un nouveau compte</a> <!-- Lien discret de bascule[cite: 3] -->

        <?php else: ?>
            <!-- FORMULAIRE D'INSCRIPTION -->
            <h3>Créer un nouveau compte</h3>
            <form action="index.php?action=inscription" method="POST">
                <div class="form-group">
                    <label>USERNAME</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>E-MAIL</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>MOT DE PASSE</label>
                    <input type="password" name="password" required>
                </div>
                <!-- La date de naissance figure sur la maquette mais n'est pas dans le script SQL de la base[cite: 2, 3]. Elle est omise pour respecter le backend. -->
                <button type="submit" name="register">S'inscrire</button>
            </form>
            <a href="index.php" class="toggle-link">Vous êtes déjà inscrit ? Se connecter</a> <!-- Lien discret de bascule[cite: 3] -->
        <?php endif; ?>
    </div>

</body>
</html>