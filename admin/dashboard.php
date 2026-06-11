<?php 

session_start();

if (!issert($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
    header("Location: ../index.php");
    exit;
}

require '../config/database.php';

try{
    // 1. Nombre total de livres en stock
     $stmtTotal = $pdo->query("SELECT SUM(stock_total) as total_livres FROM livre");
     $total_livres = $stmtLivres->fetch()['total_livres'] ?? 0;

    // 2. NOmbre d'etudiants inscrits
    $stmtUsers = $pdo->query("SELECT COUNT(id_utilisateur) as total_users FROM utilisateur WHERE role = 'user'");
    $total_users = $stmtUsers->fetch()['total_users'] ?? 0;

    // 3. Demandes en attente
    $stmtAttente = $pdo->query("SELECT COUNT(id_emprunt) as total_attente FROM emprunter WHERE statut = 'en_attente'");
    $total_attente = $stmtAttente->fetch()['total_attente'] ?? 0;
    
} catch (PDOException $e) {
    die("Erreur de statistiques : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head> 
    <meta charset="UTF-8">
    <link rel ="stylesheets" href="../styles/styles.css">
    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 20px; }
        .stat-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center; }
        .stat-card h3 { margin: 0; color: #7f8c8d; font-size: 16px; }
        .stat-card .number { font-size: 36px; font-weight: bold; color: #2c3e50; margin: 10px 0; }
    </style>
</head>
<body>
        <?php include '../includes/sidebar.php'; ?>
        <?php include '../includes/header.php'; ?>

        <div class="main-content">
        <h2>Tableau de bord : Statistiques Globales</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Ouvrages en bibliothèque</h3>
                <div class="number"><?php echo $total_livres; ?></div>
            </div>
            <div class="stat-card">
                <h3>Étudiants inscrits</h3>
                <div class="number"><?php echo $total_users; ?></div>
            </div>
            <div class="stat-card" style="border-bottom: 4px solid #f39c12;">
                <h3>Demandes en attente</h3>
                <div class="number"><?php echo $total_attente; ?></div>
                <?php if($total_attente > 0): ?>
                    <a href="demandes.php" style="color: #f39c12; text-decoration: none; font-size: 14px; font-weight: bold;">Traiter les demandes ➔</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
       

</body>
</html>



