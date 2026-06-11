<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: ../index.php");
    exit;
}

require '../config/database.php';

// Récupérer la liste complète des livres[cite: 1]
$query = "SELECT * FROM livres ORDER BY titre ASC";
$livres = $pdo->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catalogue - GESTBIBLIO</title>
    <link rel ="stylesheet" href="../styles/styles.css">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <?php include '../includes/header.php'; ?>

    <div class="main-content">
        <h2>Catalogue des livres</h2>
        <p>Consultez la liste des ouvrages et soumettez vos requêtes d'emprunt.</p>

        <div class="grid-cards">
            <?php if (count($livres) > 0): ?>
                <?php foreach ($livres as $l): ?>
                    <div class="book-card">
                        <div class="book-icon">📖</div>
                        <div class="book-info">
                            <div class="book-title"><?php echo htmlspecialchars($l['titre']); ?></div>
                            <div class="book-author"><?php echo htmlspecialchars($l['auteur']); ?></div>
                        </div>
                        
                        <?php if ($l['stock_dispo'] > 0): ?>
                            <a href="emprunter.php?id=<?php echo $l['id_livre']; ?>" class="btn-action btn-available">Demander l'emprunt</a>
                        <?php else: ?>
                            <button class="btn-action btn-unavailable" disabled>Indisponible</button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #7f8c8d; grid-column: 1 / -1;">La bibliothèque ne possède aucun livre pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
