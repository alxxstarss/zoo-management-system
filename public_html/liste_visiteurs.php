<?php
// liste_visiteurs.php
// Affiche l'annuaire des visiteurs et autorise
// les comptables à ajouter de nouveaux visiteurs.
session_start();
require_once 'db.php';
ini_set('display_errors', 1);
// verifier si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header('Location: index.php');
    exit();
}

$role = $_SESSION['type_personnel'];

// récupération de la liste des visiteurs 
$sql = "SELECT id_visiteur, nom_visiteur, prenom_visiteur, email_visiteur, tel_visiteur
        FROM Visiteur ORDER BY nom_visiteur ASC";
$stmt = oci_parse($conn, $sql);
oci_execute($stmt);

// On utilise la fonction de traitement de casse définie dans votre db.php
$visiteurs = oci_fetch_all_lower($stmt);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Annuaire des Visiteurs</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container { max-width: 1000px; margin: 30px auto; font-family: sans-serif; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #1565c0; color: white; }
        .btn-add { background: #2e7d32; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; }
        .btn-back { color: #1565c0; text-decoration: none; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-actions">
        <h1>👥 Liste des Visiteurs</h1>
        <a href="page_acceuil.php" class="btn-back">🏠 Retour Menu</a>
    </div>

    <?php if ($role === 'Comptable'): ?>
        <div style="margin-bottom: 20px;">
            <a href="ajouter_visiteur.php" class="btn-add">➕ Enregistrer un nouveau visiteur</a>
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom Complet</th>
                <th>Email</th>
                <th>Téléphone</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($visiteurs as $v): ?>
                <tr>
                    <td>#<?php echo $v['id_visiteur']; ?></td>
                    <td><?php echo htmlspecialchars(($v['prenom_visiteur'] ?? '') . " " . ($v['nom_visiteur'] ?? '')); ?></td>
                    <td><?php echo htmlspecialchars($v['email_visiteur'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($v['tel_visiteur'] ?? 'N/A'); ?></td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($visiteurs)): ?>
                <tr><td colspan="4" style="text-align:center;">Aucun visiteur trouvé.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>

