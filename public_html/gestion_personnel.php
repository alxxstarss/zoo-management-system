<?php
// gestion_personnel.php
// Page de gestion du personnel pour les comptables.
// Automatise l'archivage en Emplois et la suppression des liens.
session_start();
require_once 'db.php';

// Accès restreint au rôle 'Comptable'
if (!isset($_SESSION['id_user']) || $_SESSION['type_personnel'] !== 'Comptable') {
    header('Location: index.php');
    exit();
}

$msg = "";

if (isset($_GET['delete'])) {
    $id_p = $_GET['delete'];

    // 1. recuperation des infos pour l'archivage
    // Utilisation de la fonction oci_fetch_one_assoc définie dans db.php
    $emp = oci_fetch_one_assoc($conn, "SELECT id_personnel, nom_personnel, prenom_personnel, TO_CHAR(date_debut, 'YYYY-MM-DD') as date_debut, salaire, type_personnel FROM Personnel WHERE id_personnel = :id", ['id' => $id_p]);

    if ($emp) {
        try {
            // archivage dans emplois
            $sql_archive = "INSERT INTO Emplois (id_personnel, date_debut, date_fin, contrat)
                            VALUES (:id, TO_DATE(:debut, 'YYYY-MM-DD'), SYSDATE, 'FIN DE CONTRAT')";

            $stmt_a = oci_parse($conn, $sql_archive);
            $debut = $emp['date_debut'] ?? date('Y-m-d');

            oci_bind_by_name($stmt_a, ':id', $id_p);
            oci_bind_by_name($stmt_a, ':debut', $debut);

            // On exécute sans auto-commit pour gérer la transaction manuellement
            oci_execute($stmt_a, OCI_NO_AUTO_COMMIT);

            // nettoyage des références

            // Suppression dans les tables de liaison simples
            $stmts_clean = [
                "DELETE FROM Vend WHERE id_vendeur = :id",
                "DELETE FROM Entretien WHERE id_personnel = :id",
                "DELETE FROM Chiffre_Af_j WHERE id_responsable = :id",
                "DELETE FROM Intervient2 WHERE id_personnel_teq = :id"
            ];
            foreach ($stmts_clean as $sql_c) {
                $s = oci_parse($conn, $sql_c);
                oci_bind_by_name($s, ':id', $id_p);
                oci_execute($s, OCI_NO_AUTO_COMMIT);
            }

            // Désaffectation du responsable boutique
            $s_btq = oci_parse($conn, "UPDATE Boutique SET id_responsable = NULL WHERE id_responsable = :id");
            oci_bind_by_name($s_btq, ':id', $id_p);
            oci_execute($s_btq, OCI_NO_AUTO_COMMIT);

            // gestion des Soigneurs
            $soigneur = oci_fetch_one_assoc($conn, "SELECT id_soigneur FROM Soigneur WHERE id_personnel = :id", ['id' => $id_p]);

            if ($soigneur) {
                $id_s = $soigneur['id_soigneur'];
                $stmts_soigneur = [
                    "DELETE FROM Soigne WHERE id_soigneur = :ids",
                    "DELETE FROM Nourrir WHERE id_soigneur = :ids",
                    "UPDATE Soigneur SET id_manager = NULL WHERE id_manager = :ids",
                    "UPDATE Soigneur SET id_remplacent = NULL WHERE id_remplacent = :ids",
                    "DELETE FROM Soigneur WHERE id_soigneur = :ids"
                ];
                foreach ($stmts_soigneur as $sql_s) {
                    $ss = oci_parse($conn, $sql_s);
                    oci_bind_by_name($ss, ':ids', $id_s);
                    oci_execute($ss, OCI_NO_AUTO_COMMIT);
                }
            }

            // 5. suppression finale dans personnel
            $s_del = oci_parse($conn, "DELETE FROM Personnel WHERE id_personnel = :id");
            oci_bind_by_name($s_del, ':id', $id_p);
            oci_execute($s_del, OCI_NO_AUTO_COMMIT);

            // validation de la transaction
            oci_commit($conn);
            $msg = "L'employé #" . $id_p . " a été archivé et supprimé avec succès.";

        } catch (Exception $e) {
            oci_rollback($conn);
            $msg = "Erreur lors de la suppression : " . $e->getMessage();
        }
    }
}

// Récupération de la liste des employés actifs
$personnel = oci_fetch_all_assoc($conn, "SELECT id_personnel, nom_personnel, prenom_personnel, salaire, type_personnel FROM Personnel ORDER BY nom_personnel");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>RH - Gestion du Personnel</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; margin: 30px; }
        .container { max-width: 1000px; margin: auto; }
        .nav-bar { background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; display: flex; gap: 20px; align-items: center; }
        .nav-bar a { text-decoration: none; font-weight: bold; color: #1565c0; }
        .btn-add { background: #2e7d32; color: white !important; padding: 10px 15px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #1565c0; color: white; }
        .badge { padding: 4px 10px; border-radius: 15px; font-size: 0.85em; font-weight: bold; background: #e3f2fd; color: #1565c0; }
        .alert { padding: 15px; background: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .btn-delete { color: #d32f2f; font-weight: bold; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <h1>👥 Gestion du Personnel (RH)</h1>

    <div class="nav-bar">
        <a href="page_acceuil.php">🏠 Menu Principal</a>
        <a href="ajouter_personnel.php" class="btn-add">➕ Ajouter un Employé</a>
        <a href="historique_emplois.php">📈 Historique des Archives</a>
    </div>

    <?php if ($msg): ?>
        <div class="alert"><?php echo $msg; ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom Complet</th>
                <th>Poste</th>
                <th>Salaire</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($personnel as $p): ?>
                <tr>
                    <td>#<?php echo $p['id_personnel']; ?></td>
                    <td><?php echo htmlspecialchars(($p['prenom_personnel'] ?? '') . " " . ($p['nom_personnel'] ?? '')); ?></td>
                    <td><span class="badge"><?php echo htmlspecialchars($p['type_personnel'] ?? 'N/A'); ?></span></td>
                    <td><?php echo number_format($p['salaire'] ?? 0, 2, ',', ' '); ?> €</td>
                    <td>
                        <a href="?delete=<?php echo $p['id_personnel']; ?>"
                           class="btn-delete"
                           onclick="return confirm('Confirmer la suppression ? L\'employé sera déplacé vers l\'historique des emplois.')">
                           Supprimer & Archiver
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($personnel)): ?>
                <tr><td colspan="5" style="text-align:center;">Aucun employé actif.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
