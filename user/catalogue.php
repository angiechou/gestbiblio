<?php
// user/catalogue.php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: ../index.php");
    exit;
}

require '../config/database.php';

// Récupérer la liste complète des livres[cite: 1]
$query = "SELECT * FROM livres ORDER BY titre ASC";
$livres = $pdo->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catalogue - GESTBIBLIO</title>
    <link rel ="stylesheet" href="../styles/styles.css">
</head>
<body>



























    
