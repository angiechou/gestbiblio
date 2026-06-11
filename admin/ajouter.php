<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

require '../config/database.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajouter_livre'])) {
    $titre = trim($_POST['titre']);
    $auteur = trim($_POST['auteur']);
    $stock_total = intval($_POST['stock_total']); // Force la valeur à être un nombre entier

    $stock_dispo = $stock_total;
    
    if (!empty($titre) && !empty($auteur)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO livre (titre, auteur, stock_total, stock_dispo) VALUES (?, ?, 1, 1)");
            if ($stmt->execute([$titre, $auteur])) {
                $success = "Le livre « $titre » a bien été ajouté au catalogue avec un stock initial de 1.";
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de l'ajout du livre : " . $e->getMessage();
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Livre - GESTBIBLIO</title>
    <link rel = "stylesheet " href ="../styles/styles.css">    
</head>
<body>

    <?php include '../includes/sidebar.php'; ?>
    <?php include '../includes/header.php'; ?>

    <div class="main-content">
        <h2>Ajout d'un nouveau livre</h2>
        <p>Veuillez renseigner les informations demandées pour insérer l'ouvrage en rayon.</p>

        <div class="form-container">
            <?php if ($success): ?> <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div> <?php endif; ?>
            <?php if ($error): ?> <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div> <?php endif; ?>

            <form action="ajouter.php" method="POST">
                <div class="form-group">
                    <label>Nom du livre (Titre)</label>
                    <input type="text" name="titre" placeholder="Ex: Informatique à tout prix" required>
                </div>
                <div class="form-group">
                    <label>Auteur/trice(s) du livre</label>
                    <input type="text" name="auteur" placeholder="Ex: Angelo ADANHOUNME" required>
                </div>

                <div class="form-group">
                    <label for="stock_total">Stock initial (Nombre d'exemplaires) :</label>
                    <!-- type="number" min="1" : Empêche l'admin de mettre 0 ou un stock négatif à la création -->
                    <input type="number" id="stock_total" name="stock_total" min="1" required>
                </div>
                
                <button type="submit" name="ajouter_livre" class="btn-submit">Ajouter l'ouvrage</button>
            </form>
        </div>
    </div>

</body>
</html>
