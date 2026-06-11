<?php 

session_start();

if (!issert($_SESSION['user']) || $_SESSION['user']['code'] !== 'admin'){
    header("Location: ../index.php");
    exit;
}

require '../config/database';

try{
    // 1. Nombre total de livres
     $stmtTotal = $pdo->query("SELECT COUNT(*) FROM livres");
    $totalLivres = $stmtTotal->fetchColumn();

    // 2. NOmbre d'Emprunt en attente
    $stmAttente = $pdo->query("SELECT COUNT(*) FROM livres");
    $totallivres = $stmTotal->fetchColum();

    // 3.  Nombre de livres actuellement dehors (emprunts confirmés)
    $stmDehors = $stmAttente->fetchColum();
    
} catch (PDOException $e) {
    die("Erreur de statistiques : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head> 
    <meta charset="UTF-8">
    <link rel ="stylesheed" href="../styles/styles.css">
</head>
<body>
        <div class="main-content">
        <h2>Tableau de bord du gestionnaire</h2>
        <p>Bienvenue dans votre espace d'administration. Voici un aperçu de l'état actuel de la bibliothèque.</p>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo sprintf("%02d", $totalLivres); ?></div>
                <div class="stat-label">Livres enregistrés</div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-number"><?php echo sprintf("%02d", $totalAttente); ?></div>
                <div class="stat-label">Demandes en attente</div>
            </div>
            
            <div class="stat-card danger">
                <div class="stat-number"><?php echo sprintf("%02d", $totalDehors); ?></div>
                <div class="stat-label">Livres empruntés</div>
            </div>
        </div>
    </div>

</body>
</html>



