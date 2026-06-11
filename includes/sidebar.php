<?php
// includes/sidebar.php
$role = $_SESSION['user']['role'];
?>

<div class="sidebar">
    <div class="sidebar-logo">
        GESTBIBLIO <br> <small style="font-size: 12px; font-weight: normal;">GESTIONNAIRE</small>
    </div>
    
    <ul class="nav-links">
        <?php if ($role === 'admin'): ?>
            <!-- Menu Administrateur[cite: 3] -->
            <li><a href="<?php echo $base_path; ?>admin/dashboard.php">Dashboard (Stats)</a></li>
            <li><a href="<?php echo $base_path; ?>admin/ajouter.php">Ajouter un livre</a></li>
            <li><a href="<?php echo $base_path; ?>admin/catalogue.php">Liste & Retrait</a></li>
            <li><a href="<?php echo $base_path; ?>admin/demandes.php">Valider Emprunts</a></li>
        <?php else: ?>
            <!-- Menu Étudiant (User)[cite: 3] -->
            <li><a href="<?php echo $base_path; ?>user/dashboard.php">Dashboard (Mes Emprunts)</a></li>
            <li><a href="<?php echo $base_path; ?>user/catalogue.php">Catalogue des Livres</a></li>
        <?php endif; ?>
    </ul>
</div>