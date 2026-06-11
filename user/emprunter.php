<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: ../index.php");
    exit;
}

require '../config/database.php';

// Vérifier si un ID de livre a été transmis en paramètre URL
if (isset($_GET['id_livre'])) {
    $livre_id = intval($_GET['id_livre']);
    $user_id = $_SESSION['user']['id_livre'];

    try {
        // 1. Vérifier si le livre existe et s'il est réellement disponible (sécurité supplémentaire)
        $stmtCheck = $pdo->prepare("SELECT stock_dispo FROM livre WHERE id_livre = ?");
        $stmtCheck->execute([$livre_id]);
        $livre = $stmtCheck->fetch();

        if ($livre && $livre['stock_dispo'] > 0) {
            
          
            $stmtInsert = $pdo->prepare("INSERT INTO emprunter (id_utilisateur, id_livre) VALUES (?, ?)");
            $stmtInsert->execute([$user_id, $livre_id]);
            
          
            header("Location: dashboard.php?msg=success");
            exit;
            
        } else {
           
            header("Location: catalogue.php?error=unavailable");
            exit;
        }

    } catch (PDOException $e) {
        die("Erreur de traitement de la demande : " . $e->getMessage());
    }
} else {
  
    header("Location: catalogue.php");
    exit;
}
?>
