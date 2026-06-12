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
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action']; // On récupère l'action (valider ou refuser)
    $emprunt_id = intval($_GET['id']); // On récupère l'ID de l'emprunt

    try {
        $pdo->beginTransaction();
        
        // Récupérer l'ID du livre et le statut actuel pour vérification
        $stmtEmp = $pdo->prepare("SELECT id_livre, statut FROM emprunter WHERE id_emprunt = ?");
        $stmtEmp->execute([$emprunt_id]);
        $empruntEnCours = $stmtEmp->fetch();

        if ($empruntEnCours) {
            $id_livre = $empruntEnCours['id_livre'];

            if ($action === 'valider' && $empruntEnCours['statut'] === 'en_attente') {
                $date_retour = date('Y-m-d', strtotime('+10 days'));
                
                // Mettre à jour l'emprunt
                $stmtUpdateEmp = $pdo->prepare("UPDATE emprunter SET statut = 'confirme', date_retour = ? WHERE id_emprunt = ?");
                $stmtUpdateEmp->execute([$date_retour, $emprunt_id]);

                // Diminuer le stock
                $stmtUpdateStock = $pdo->prepare("UPDATE livre SET stock_dispo = stock_dispo - 1 WHERE id_livre = ?");
                $stmtUpdateStock->execute([$id_livre]);
                
                $message = "Emprunt validé avec succès.";
            } 
            elseif ($action === 'refuser') {
                // Supprimer ou marquer comme refusé
                $stmtDelete = $pdo->prepare("UPDATE emprunter SET statut = 'refuse' WHERE id_emprunt = ?");
                $stmtDelete->execute([$emprunt_id]);
                $message = "La demande a été refusée.";
            }
            $pdo->commit();
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        $erreur = "Erreur : " . $e->getMessage();
    }
}
        // Récupération des demandes d'emprunt
        $query = "SELECT e.id_emprunt, e.date_demande, e.statut, e.date_retour, u.username, u.email, l.titre 
                FROM emprunter e 
                JOIN utilisateur u ON e.id_utilisateur = u.id_utilisateur
                JOIN livre l ON e.id_livre = l.id_livre
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
                    <th>Statut</th>
                    <th>Date de retour</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                  <?php if (count($emprunt) > 0): ?>
                    <?php foreach ($emprunt as $emp): ?>
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
