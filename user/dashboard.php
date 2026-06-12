<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: ../index.php");
    exit;
}

require '../config/database.php';

$user_id = $_SESSION['user']['id_utilisateur'];
$today = date('Y-m-d');

// Récupération des emprunts de l'utilisateur connecté avec les infos du livre
$query = "SELECT e.*, l.titre, l.auteur 
          FROM emprunter e 
          JOIN livre l ON e.id_livre = l.id_livre 
          WHERE e.id_utilisateur = ? 
          ORDER BY e.date_demande DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$mes_emprunts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTE-8">
    <title>Mon Espace - GESTBIBLIO</title>
    <link rel ="stylesheet" href="../styles/styles.css">
</head>
    
<body>
    <body>

    <?php include '../includes/sidebar.php'; ?>
    <?php include '../includes/header.php'; ?>

        <div class="main-content">
        <h2>Tableau de bord de l'utilisateur</h2>
        <p>Suivez l'état de validation et la date limite de retour de vos emprunts.</p>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
            <div class="alert">Votre demande d'emprunt a été envoyée avec succès.</div>
        <?php endif; ?>

        <div class="grid-cards">
            <?php if (count($mes_emprunts) > 0): ?>
                <?php foreach ($mes_emprunts as $emp): ?>
                    <div class="book-card">
                        <div class="book-icon">📚</div>
                        <div class="book-info">
                            <div class="book-title"><?php echo htmlspecialchars($emp['titre']); ?></div>
                            <div class="book-author"><?php echo htmlspecialchars($emp['auteur']); ?></div>
                        </div>
                        
                        <?php if ($emp['statut'] === 'en_attente'): ?>
                            <div class="status-bar status-pending">En attente de confirmation</div>
                        
                        <?php elseif ($emp['statut'] === 'confirme'): ?>
                            <!-- Ciblage de la colonne 'date_retour' de votre dump SQL -->
                            <?php if ($today > $emp['date_retour']): ?>
                                <div class="status-bar status-expired">Délai atteint<br>Plus accessible</div>
                            <?php else: ?>
                                <div class="status-bar status-active">Emprunté<br>Fin : <?php echo date('d/m/Y', strtotime($emp['date_retour'])); ?></div>
                            <?php endif; ?>
                            
                        <?php elseif ($emp['statut'] === 'rendu'): ?>
                            <!-- Prise en compte du statut 'rendu' spécifié dans votre ENUM SQL -->
                            <div class="status-bar status-returned" style="background-color: #3498db; color: white; padding: 12px; font-weight: bold; font-size: 13px; text-transform: uppercase;">Livre Retourné</div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #7f8c8d; grid-column: 1 / -1;">Vous n'avez effectué aucune demande d'emprunt pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
