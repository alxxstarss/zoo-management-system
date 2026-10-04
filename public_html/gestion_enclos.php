<?php
// gestion_enclos.php
// Page de gestion de l'état des enclos réservée au personnel d'entretien.
session_start();
require_once 'db.php';

if (!isset($_SESSION['id_user']) || $_SESSION['type_personnel'] !== 'Entretien') {
    header('Location: index.php');
    exit();
}

$sql = "SELECT e.*, z.nom_zone
        FROM Enclos e
        JOIN Zone z ON e.id_zone = z.id_zone";
$enclos = [];
$stmt = oci_parse($conn, $sql);
oci_execute($stmt);

while ($row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $enclos[] = array_change_key_case($row, CASE_LOWER);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ZOOLAND - Gestion Enclos</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
        th { background: var(--secondary-color); color: white; padding: 15px; text-align: left; }
        td { padding: 12px 15px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f4f7f6; }
        .zone-tag { background: #eee; padding: 3px 8px; border-radius: 4px; font-size: 0.9em; font-weight: bold; color: #555; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>🏗️ État des Enclos</h1>
        <div class="nav-links">
            <a href="interventions.php" class="btn">🛠️ Voir les Interventions</a>
            <a href="page_acceuil.php" class="btn" style="background:transparent; border:1px solid white;">Menu</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <h3>Liste des structures</h3>
            <p>Consultez les détails des enclos et signalez toute anomalie structurelle.</p>
            
            <table style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Zone du Zoo</th>
                        <th>Particularité</th>
                        <th>Superficie</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($enclos as $e): ?>
                    <tr>
                        <td>#<?php echo $e['id_enclos']; ?></td>
                        <td><span class="zone-tag"><?php echo htmlspecialchars($e['nom_zone']); ?></span></td>
                        <td><?php echo htmlspecialchars($e['particularite']); ?></td>
                        <td><strong><?php echo $e['superficie_enclos']; ?> m²</strong></td>
                        <td style="text-align: center;">
                            <a href="interventions.php?id_enclos=<?php echo $e['id_enclos']; ?>" 
                               class="btn" style="padding: 5px 12px; font-size: 0.85em; background: #e67e22;">
                                ⚠️ Signaler Réparation
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
