<?php
// includes/sidebar.php
$role = $_SESSION['user']['role'];
?>

<div class="sidebar">
    <div class="sidebar-title">Navigation</div>

    <!-- LIENS RÉSERVÉS UNIQUEMENT À L'ADMINISTRATEUR -->
    <?php if ($role === 'admin'): ?>       
        <a href="../admin/dashboard.php">Tableau de bord</a>
        <a href="../admin/ajouter.php">Ajouter un Livre</a>
        <a href="../admin/catalogue.php">Catalogue</a>
        <a href="../admin/demandes.php">Demandes</a>

    <!-- LIENS RÉSERVÉS UNIQUEMENT AUX ÉTUDIANTS -->
    <?php else: ?>
        <a href="../user/dashboard.php">Tableau de bord</a>
        <a href="../user/catalogue.php">Catalogue</a>
    <?php endif; ?>

    <div class="menu-divider">Mon Compte</div>
    <a href="../profil.php">Modifier mon Profil</a>
    <a href="../index.php" style="color: #e74c3c;">Déconnexion</a>
</div>
