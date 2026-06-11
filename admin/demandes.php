<?php
// admin/demandes.php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

require '../config/database.php';

$message = '';
$erreur = '';

// Action de validation de l'emprunt
if (isset($_GET['action']) && $_GET['action'] == 'confirmer' && isset($_GET['id'])) {
    $emprunt_id = intval($_GET['id']);

    try {
        // Commencer une transaction pour sécuriser la double opération (Update Emprunt + Update Stock)
        $pdo->beginTransaction();

        // Récupérer l'ID du livre associé à cet emprunt
        $stmtEmp = $pdo->prepare("SELECT livre_id FROM emprunts WHERE id = ? AND statut = 'en_attente'");
        $stmtEmp->execute([$emprunt_id]);
        $emprunt = $stmtEmp->fetch();

        if ($emprunt) {
            $livre_id = $emprunt['livre_id'];

            // Calcul strict du délai : Date du jour + 10 jours[cite: 1]
            $date_retour_prevue = date('Y-m-d', strtotime('+10 days'));[cite: 1]

            // 1. Mettre à jour la table emprunts
            $stmtUpdateEmp = $pdo->prepare("UPDATE emprunts SET statut = 'confirme', date_retour_prevue = ? WHERE id = ?");
            $stmtUpdateEmp->execute([$date_retour_prevue, $emprunt_id]);

            // 2. Diminuer le stock disponible du livre concerné de 1[cite: 1]
            $stmtUpdateStock = $pdo->prepare("UPDATE livres SET stock_dispo = stock_dispo - 1 WHERE id = ?");
            $stmtUpdateStock->execute([$livre_id]);

            $pdo->commit();
            $message = "L'emprunt a été validé avec succès. Le retour est attendu pour le " . date('d/m/Y', strtotime($date_retour_prevue)) . ".";
        } else {
            $pdo->rollBack();
            $erreur = "Cette demande d'emprunt a déjà été traitée ou n'existe pas.";
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        $erreur = "Une erreur est survenue lors de la validation : " . $e->getMessage();
    }
}

// Récupération des demandes d'emprunt en attente
$query = "SELECT e.id, e.date_demande, u.username, u.email, l.titre 
          FROM emprunts e 
          JOIN utilisateurs u ON e.utilisateur_id = u.id 
          JOIN livres l ON e.livre_id = l.id 
          WHERE e.statut = 'en_attente' 
          ORDER BY e.date_demande ASC";[cite: 3]
$demandes = $pdo->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation Emprunts - GESTBIBLIO</title>
    <link rel = "stylesheet " href ="../styles/styles.css">    
</head>
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