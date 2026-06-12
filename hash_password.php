<?php
// Script à exécuter une seule fois pour convertir les mots de passe
require 'config/database.php'; // Votre fichier de connexion à la base

$users = $pdo->query("SELECT id_utilisateur, password FROM utilisateur")->fetchAll();

foreach ($users as $user) {
    // Vérifie si le mot de passe n'est pas déjà un hash (simple vérification)
    if (strlen($user['password']) < 60) { 
        $hashed = password_hash($user['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE utilisateur SET password = ? WHERE id_utilisateur = ?");
        $stmt->execute([$hashed, $user['id_utilisateur']]);
        echo "Utilisateur ID {$user['id_utilisateur']} mis à jour.<br>";
    }
}