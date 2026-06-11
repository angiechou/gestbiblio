<?php
// includes/sidebar.php
$role = $_SESSION['user']['role'];
?>

<style>
    .sidebar {
    width: 260px;
    background-color: #2c3e50; /* Bleu nuit profond et professionnel */
    color: #ecf0f1;
    height: 100vh; /* Prend toute la hauteur de la fenêtre */
    position: fixed; /* Reste figée lors du défilement de la page */
    top: 0;
    left: 0;
    padding-top: 30px;
    box-shadow: 3px 0 10px rgba(0, 0, 0, 0.15); /* Ombre douce pour détacher du fond */
    display: flex;
    flex-direction: column;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    z-index: 1000; /* S'assure que la sidebar reste au-dessus des autres éléments */
}

    /* Titre principal tout en haut */
    .sidebar-title {
        font-size: 22px;
        font-weight: 800;
        text-align: center;
        margin-bottom: 30px;
        color: #f39c12; /* Touche d'accentuation dynamique */
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    /* Les petits titres qui séparent les sections (ex: Zone Admin) */
    .menu-divider {
        font-size: 12px;
        color: #95a5a6;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin: 25px 20px 10px 20px;
        border-bottom: 1px solid #34495e;
        padding-bottom: 8px;
        font-weight: 600;
    }

    /* Style de base des liens de navigation */
    .sidebar a {
        padding: 15px 25px;
        text-decoration: none;
        font-size: 15px;
        color: #bdc3c7;
        display: block;
        transition: all 0.3s ease; /* Animation fluide */
        border-left: 4px solid transparent; /* Bordure invisible préparée pour le survol */
    }

    /* Effet de survol (Hover) sur les liens */
    .sidebar a:hover {
        background-color: #34495e;
        color: #ffffff;
        border-left: 4px solid #3498db; /* Apparition d'une barre bleue à gauche */
        padding-left: 32px; /* Léger décalage du texte vers la droite pour le dynamisme */
    }

    /* Cas particulier : Le bouton de déconnexion */
    .sidebar a[href*="logout.php"] {
        margin-top: auto; /* Pousse le bouton de déconnexion vers le bas si on utilise flexbox */
        margin-bottom: 20px;
    }

    .sidebar a[href*="logout.php"]:hover {
        border-left: 4px solid #e74c3c; /* Barre rouge d'alerte */
        background-color: rgba(231, 76, 60, 0.1);
        color: #e74c3c;
    }

    .main-content, .header {
        margin-left: 260px; /* Doit être égal à la largeur de la sidebar */
        padding: 20px 40px;
    }

</style>

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
