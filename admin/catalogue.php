<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

require '../config/database.php';

$message = '';
$erreur = '';

// Traitement de l'action de suppression (Retrait)
if (isset($_GET['action']) && $_GET['action'] == 'retirer' && isset($_GET['id_livre'])) {
    $livre_id = intval($_GET['id_livre']);

    try {
        // Récupérer les informations de stock du livre pour le contrôle de sécurité
        $stmtCheck = $pdo->prepare("SELECT titre, stock_total, stock_dispo FROM livre WHERE id_livre = ?");
        $stmtCheck->execute([$livre_id]);
        $livre = $stmtCheck->fetch();

        if ($livre) {
            // VERROUILLAGE LOGIQUE : Bloquer si le stock disponible est inférieur au stock total initial[cite: 1, 3]
            if ($livre['stock_dispo'] < $livre['stock_total']) {
                $erreur = "Action interdite : Le livre « {$livre['titre']} » est en cours d'emprunt et ne peut pas être retiré.";[cite: 1, 3]
            } else {
                // Suppression autorisée
                $stmtDelete = $pdo->prepare("DELETE FROM livre WHERE id_livre = ?");
                $stmtDelete->execute([$livre_id]);
                $message = "Le livre « {$livre['titre']} » a été retiré avec succès du catalogue.";
            }
        }
    } catch (PDOException $e) {
        $erreur = "Erreur lors du retrait de l'ouvrage : " . $e->getMessage();
    }
}

// Récupération de l'ensemble du catalogue
$livres = $pdo->query("SELECT * FROM livre ORDER BY id_livre DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Catalogue - GESTBIBLIO</title>
    <link rel ="stylesheed" href="../styles/styles.css">
</head>
<body>

    <?php include '../includes/sidebar.php'; ?>
    <?php include '../includes/header.php'; ?>

    <div class="main-content">
        <h2>Gestion du catalogue</h2>
        <p>Liste complète des ouvrages physiques de l'établissement.</p>

        <?php if ($message): ?> <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div> <?php endif; ?>
        <?php if ($erreur): ?> <div class="alert alert-danger"><?php echo htmlspecialchars($erreur); ?></div> <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre de l'ouvrage</th>
                    <th>Auteur(s)</th>
                    <th>Stock total</th>
                    <th>Stock Disponible</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($livres) > 0): ?>
                    <?php foreach ($livres as $l): ?>
                        <tr>
                            <td>#<?php echo $l['id_livre']; ?></td>
                            <td><strong><?php echo htmlspecialchars($l['titre']); ?></strong></td>
                            <td><?php echo htmlspecialchars($l['auteur']); ?></td>
                            <td><?php echo $l['stock_total']; ?></td>
                            <td>
                                <?php if ($l['stock_dispo'] > 0): ?>
                                    <span class="badge badge-success">En Rayon (<?php echo $l['stock_dispo']; ?>)</span>[cite: 1]
                                <?php else: ?>
                                    <span class="badge badge-danger">Indisponible (0)</span>[cite: 1]
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <!-- Bouton pour modifier le stock -->
                                <a href="modifier.php?id=<?php echo $l['id_livre']; ?>" class="btn-edit">Modifier</a>
                                <!-- Bouton rouge de retrait avec confirmation JS réglementaire[cite: 3] -->
                                <a href="catalogue.php?action=retirer&id=<?php echo $l['id_livre']; ?>" class="btn-delete" onclick="return confirm('Êtes-vous certain de vouloir supprimer définitivement ce livre ?');">Retirer</a>[cite: 3]
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #7f8c8d;">Aucun livre enregistré dans le catalogue.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>   

