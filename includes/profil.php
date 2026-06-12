<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user']['id_utilisateur'];
$message = '';
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // On vérifie si la clé existe avant de l'assigner
    $username = isset($_POST['username']) ? $_POST['username'] : $_SESSION['user']['username'];
    $email = isset($_POST['email']) ? $_POST['email'] : $_SESSION['user']['email'];
}

// Traitement des formulaires de mise à jour
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Modification des données textuelles (Username & Email)
    if (isset($_POST['update_info'])) {
        $new_username = trim($_POST['username']);
        $new_email = trim($_POST['email']);
        
        $stmt = $pdo->prepare("UPDATE utilisateur SET username = ?, email = ? WHERE id_utilisateur = ?");
        if ($stmt->execute([$new_username, $new_email, $user_id])) {
            $_SESSION['user']['username'] = $new_username;
            $_SESSION['user']['email'] = $new_email;
            $message = "Informations mises à jour avec succès.";
        } else {
            $erreur = "Erreur lors de la mise à jour des informations.";
        }
    }

    // 2. Changement de mot de passe
    if (isset($_POST['update_password'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($new_password === $confirm_password) {
            $hashed_pwd = password_hash($new_password, PASSWORD_DEFAULT); // Hachage de la nouvelle valeur[cite: 3]
            $stmt = $pdo->prepare("UPDATE utilisateur SET password = ? WHERE id_utilisateur = ?");
            if ($stmt->execute([$hashed_pwd, $user_id])) {
                $message = "Mot de passe modifié avec succès.";
            }
        } else {
            $erreur = "Les mots de passe ne correspondent pas.";
        }
    }

    // 3. Changement de la photo de profil
    if (isset($_POST['update_avatar']) && isset($_FILES['photo'])) {
        $file = $_FILES['photo'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'png']; // Filtrage impératif[cite: 1]
        
        if (in_array($ext, $allowed_ext)) {
            // Renommage propre du fichier pour éviter l'exécution de scripts[cite: 1, 3]
            $new_filename = 'avatar_user' . $user_id . '_' . time() . '.' . $ext;
            $destination = 'assets/uploads/' . $new_filename;
            
            // S'assurer que le dossier existe
            if (!is_dir('assets/uploads/')) {
                mkdir('assets/uploads/', 0777, true);
            }
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $stmt = $pdo->prepare("UPDATE utilisateur SET photo = ? WHERE id_utilisateur = ?");
                if ($stmt->execute([$new_filename, $user_id])) {
                    $_SESSION['user']['photo'] = $new_filename; // S'actualise instantanément[cite: 3]
                    $message = "Photo de profil mise à jour.";
                }
            } else {
                $erreur = "Erreur lors du téléversement du fichier.";
            }
        } else {
            $erreur = "Seuls les formats JPG et PNG sont autorisés.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil - GESTBIBLIO</title>
    <link rel="stylesheet" href="styles/styles.css">
    
</head>

    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>

    <div class="main-content">
        <h2>Gestion du Profil</h2>
        
        <?php if ($message): ?> <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div> <?php endif; ?>
        <?php if ($erreur): ?> <div class="alert alert-danger"><?php echo htmlspecialchars($erreur); ?></div> <?php endif; ?>

        <div class="grid-container">
            <!-- Formulaire Avatar -->
            <div class="card" style="text-align: center;">
                <img src="assets/uploads/<?php echo htmlspecialchars($_SESSION['user']['photo']); ?>" alt="Avatar" style="width:120px; height:120px; border-radius:50%; object-fit:cover; margin-bottom:15px; border:3px solid #2c3e50;">
                <form action="profil.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="file" name="photo" required accept=".jpg, .png">
                    </div>
                    <button type="submit" name="update_avatar" class="btn">Changer la photo</button>
                </form>
            </div>

            <!-- Formulaire Infos -->
            <div class="card">
                <h3>Informations Personnelles</h3>
                <form action="profil.php" method="POST">
                    <div class="form-group">
                        <label>Nom d'utilisateur</label>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($_SESSION['user']['username']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>E-mail</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user']['email']); ?>" required>
                    </div>
                    <button type="submit" name="update_info" class="btn">Mettre à jour</button>
                </form>
            </div>
            
            <!-- Formulaire Mot de passe -->
            <div class="card">
                <h3>Sécurité</h3>
                <form action="profil.php" method="POST">
                    <div class="form-group">
                        <label>Nouveau mot de passe</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirmer le mot de passe</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="update_password" class="btn">Modifier le mot de passe</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
