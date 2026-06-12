<?php
// Désactive l'affichage des erreurs à l'écran pour les utilisateurs
ini_set('display_errors', 0);
// Journalise les erreurs dans un fichier de log
ini_set('log_errors', 1);
// Assure que les erreurs ne sont pas affichées publiquement
error_reporting(E_ALL);
// Débogage : Affiche les variables avant la connexion
$host = '127.0.0.1'; // Utiliser l'IP au lieu de 'localhost' pour forcer le protocole TCP
$db   = 'bibliotheque';
$user = 'root';
$pass = '';

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
?>