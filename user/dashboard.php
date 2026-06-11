<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: ../index.php");
    exit;
}

require '../config/database.php';

$user_id = $_SESSION['user']['id_utilisateur'];
$today = date('Y-m-d');

// Récupération des emprunts de l'utilisateur connecté avec les infos du livre
$query = "SELECT e.*, l.titre, l.auteur 
          FROM emprunts e 
          JOIN livres l ON e.id_livre = l.id_livre 
          WHERE e.id_utilisateur = ? 
          ORDER BY e.date_demande DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$mes_emprunts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTE-8">
    <title>Mon Espace - GESTBIBLIO</title>
    <link rel ="stylesheet" href="../styles/styles.css">
</head>
    
<body>
    <body>

    <?php include '../includes/sidebar.php'; ?>
    <?php include '../includes/header.php'; ?>

    <div class="main-content">
        <h2>Validation des demandes d'emprunts</h2>
        <p>Veuillez confirmer ou rejeter les flux de demandes initiés par les étudiants de l'établissement.</p>

        <?php if ($message): ?> <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div> <?php endif; ?>
        <?php if ($erreur): ?> <div class="alert alert-danger"><?php echo htmlspecialchars($erreur); ?></div> <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Date demande</th>
                    <th>Nom de l'étudiant</th>
                    <th>Ouvrage sollicité</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($demandes) > 0): ?>
                    <?php foreach ($demandes as $d): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($d['date_demande'])); ?></td>[cite: 2, 3]
                            <td><strong><?php echo htmlspecialchars($d['username']); ?></strong> <br><small style="color: #7f8c8d;"><?php echo htmlspecialchars($d['email']); ?></small></td>
                            <td>« <?php echo htmlspecialchars($d['titre']); ?> »</td>[cite: 3]
                            <td>
                                <a href="demandes.php?action=confirmer&id=<?php echo $d['id']; ?>" class="btn-validate">Confirmer l'emprunt</a>[cite: 3]
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #7f8c8d; padding: 30px;">Aucune requête d'emprunt en attente de confirmation pour le moment.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>

