<?php
// includes/header.php
// Sécurisation : Vérifier si la session existe, sinon rediriger vers la connexion
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}

// Définition du chemin de base dynamique pour éviter les erreurs 404 selon si on est dans admin/ ou à la racine
$base_path = (basename($_SERVER['PHP_SELF']) == 'profil.php') ? '' : '../';
$avatar_path = $base_path . 'assets/uploads/' . htmlspecialchars($_SESSION['user']['avatar']);
?>

<style>
    .top-header {
        position: fixed;
        top: 0;
        left: 260px; /* Laisse la place à la sidebar */
        right: 0;
        height: 70px;
        background-color: #fff;
        border-bottom: 2px solid #ddd;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 30px;
        z-index: 1000;
        color: #333;
    }
    .header-left h2 { margin: 0; font-size: 20px; }
    .header-right { position: relative; display: flex; align-items: center; cursor: pointer; }
    .header-right span { font-weight: bold; margin-right: 15px; }
    .profile-img { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #2c3e50; }
    
    /* Menu déroulant[cite: 3] */
    .dropdown-menu {
        display: none;
        position: absolute;
        top: 60px;
        right: 0;
        background-color: #fff;
        border: 1px solid #ccc;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-radius: 5px;
        min-width: 150px;
        overflow: hidden;
    }
    .dropdown-menu a { display: block; padding: 12px 20px; color: #333; text-decoration: none; border-bottom: 1px solid #eee; }
    .dropdown-menu a:hover { background-color: #f5f5f5; color: #e74c3c; }
</style>

<div class="top-header">
    <div class="header-left">
        <h2>GESTBIBLIO</h2>
    </div>
    <div class="header-right" onclick="toggleDropdown()">
        <span><?php echo htmlspecialchars($_SESSION['user']['username']); ?></span>
        <img src="<?php echo $avatar_path; ?>" alt="Avatar" class="profile-img">
        
        <div class="dropdown-menu" id="profileDropdown">
            <a href="<?php echo $base_path; ?>profil.php">Mon Profil</a> <!-- Lien vers le profil[cite: 3] -->
            <a href="<?php echo $base_path; ?>index.php?action=logout">Déconnexion</a> <!-- Destruction de session[cite: 3] -->
        </div>
    </div>
</div>

<script>
    function toggleDropdown() {
        var dropdown = document.getElementById("profileDropdown");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }
</script>