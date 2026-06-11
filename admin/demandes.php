<?php
// admin/demandes.php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

require '../config/database.php';

$message = null;
$erreur = '';

// Action de validation de l'emprunt
if (isset($_GET['action']) && $_GET['action'] == 'confirmer' && isset($_GET['id_livre'])) {
    $emprunt_id = intval($_GET['id_livre']);

    try {
        // Commencer une transaction pour sécuriser la double opération (Update Emprunt + Update Stock)
        $pdo->beginTransaction();

        // Récupérer l'ID du livre associé à cet emprunt
        $stmtEmp = $pdo->prepare("SELECT id_livre FROM emprunter WHERE id_utilisateur = ? AND statut = 'en_attente'");
        $stmtEmp->execute([$id_emprunt]);
        $emprunt = $stmtEmp->fetch();

        if ($emprunt) {
            $id_livre = $emprunt['id_livre'];

            if ($action === 'valider' && $empruntEnCours['statut'] === 'en_attente') {
            // Calcul strict du délai : Date du jour + 10 jours
            $date_retour = date('Y-m-d', strtotime('+10 days'));

            // 1. Mettre à jour la table emprunts
            $stmtUpdateEmp = $pdo->prepare("UPDATE emprunter SET statut = 'confirme', date_retour= ? WHERE id_livre = ?");
            $stmtUpdateEmp->execute([$date_retour, $id_emprunt]);

            // 2. Diminuer le stock disponible du livre concerné de 1
            $stmtUpdateStock = $pdo->prepare("UPDATE livre SET stock_dispo = stock_dispo - 1 WHERE id_livre = ?");
            $stmtUpdateStock->execute([$id_livre]);

            $pdo->commit();
            $message = "L'emprunt a été validé avec succès. Le retour est attendu pour le " . date('d/m/Y', strtotime($date_retour)) . ".";
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
$query = "SELECT e.id_emprunt, e.date_demande, u.username, u.email, l.titre 
          FROM emprunter e 
          JOIN utilisateur u ON e.id_utilisateur = u.id_utilisateur
          JOIN livre l ON e.id_livre = l.id_livre
          WHERE e.statut = 'en_attente' 
          ORDER BY e.date_demande DESC";
$emprunt = $pdo->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation Emprunts - GESTBIBLIO</title>
    <link rel = "stylesheet" href ="../styles/styles.css">    
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
                  <?php if (count($emprunts) > 0): ?>
                    <?php foreach ($emprunts as $emp): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($emp['date_demande'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($emp['username']); ?></strong></td>
                            <td><?php echo htmlspecialchars($emp['titre']); ?></td>
                            <td>
                                <?php if ($emp['statut'] === 'en_attente'): ?>
                                    <span class="badge bg-warning">En attente</span>
                                <?php elseif ($emp['statut'] === 'confirme'): ?>
                                    <span class="badge bg-success">Confirmé</span>
                                <?php elseif ($emp['statut'] === 'rendu'): ?>
                                    <span class="badge bg-info">Rendu</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo ($emp['date_retour']) ? date('d/m/Y', strtotime($emp['date_retour'])) : '--/--/----'; ?>
                            </td>
                            <td>
                                <?php if ($emp['statut'] === 'en_attente'): ?>
                                    <!-- Liens d'action pour valider OU refuser la demande -->
                                    <a href="demandes.php?action=valider&id=<?php echo $emp['id_emprunt']; ?>" class="btn-adm btn-adm-confirm">Confirmer</a>
                                    <a href="demandes.php?action=refuser&id=<?php echo $emp['id_emprunt']; ?>" class="btn-adm btn-adm-reject" onclick="return confirm('Refuser définitivement cette demande ?');">Refuser</a>
                                
                                <?php elseif ($emp['statut'] === 'confirme'): ?>
                                    <!-- Action pour enregistrer le retour physique à la bibliothèque -->
                                    <a href="demandes.php?action=rendre&id=<?php echo $emp['id_emprunt']; ?>" class="btn-adm btn-adm-return">Marquer comme rendu</a>
                                
                                <?php else: ?>
                                    <span style="color: #bdc3c7; font-size: 12px; font-style: italic;">Aucune action requise</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #7f8c8d; padding: 20px;">Aucun enregistrement d'emprunt dans le système.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
