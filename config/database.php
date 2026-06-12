<?php
// Débogage : Affiche les variables avant la connexion
$host = '127.0.0.1'; // Utiliser l'IP au lieu de 'localhost' pour forcer le protocole TCP
$port = '3307';
$db   = 'bibliotheque';
$user = 'root';
$pass = '';

echo "Tentative de connexion à $host sur le port $port...<br>";
$pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
?>