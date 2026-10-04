<?php
// recherche_visiteur.php
// Permet de rechercher des visiteurs parrainant des animaux.
session_start();
require_once 'db.php';

if (!isset($_SESSION['id_user']) || !in_array($_SESSION['type_personnel'], ['Responsable boutique', 'Vendeur'])) {
    header('Location: index.php');
    exit();
}

$search   = isset($_GET['query']) ? trim($_GET['query']) : '';
$resultats = [];

if ($search !== '') {
    $sql = "SELECT v.nom_visiteur, v.prenom_visiteur, v.email_visiteur,
                   a.nom_animal, c.niveau_contribution
            FROM Visiteur v
            JOIN Parraine p ON v.id_visiteur = p.id_visiteur
            JOIN Animal a ON p.id_animal = a.id_animal
            JOIN Contribution c ON p.id_contribution = c.id_contribution
            WHERE v.nom_visiteur LIKE :query
               OR v.prenom_visiteur LIKE :query2
               OR a.nom_animal LIKE :query3";

    $stmt = oci_parse($conn, $sql);
    $term = "%$search%";
    oci_bind_by_name($stmt, ':query',  $term);
    oci_bind_by_name($stmt, ':query2', $term);
    oci_bind_by_name($stmt, ':query3', $term);
    oci_execute($stmt);
    while ($row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS)) {
        $resultats[] = array_change_key_case($row, CASE_LOWER);
    }
} else {
    $sql_all = "SELECT v.nom_visiteur, v.prenom_visiteur, v.email_visiteur,
                       a.nom_animal, c.niveau_contribution
                FROM Visiteur v
                JOIN Parraine p ON v.id_visiteur = p.id_visiteur
                JOIN Animal a ON p.id_animal = a.id_animal
                JOIN Contribution c ON p.id_contribution = c.id_contribution
                FETCH FIRST 10 ROWS ONLY";
    $st_all = oci_parse($conn, $sql_all);
    oci_execute($st_all);
    while ($row = oci_fetch_array($st_all, OCI_ASSOC + OCI_RETURN_NULLS)) {
        $resultats[] = array_change_key_case($row, CASE_LOWER);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche Parrains - Zooland</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .badge { padding: 5px 10px; border-radius: 20px; font-weight: bold; font-size: 0.8em; text-transform: uppercase; }
        .or { background: #FFD700; color: #554400; border: 1px solid #d4af37; }
        .argent { background: #C0C0C0; color: #333; border: 1px solid #a0a0a0; }
        .bronze { background: #CD7F32; color: #fff; }
        
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 15px; }
        th { background: var(--primary-color); color: white; padding: 12px; }
        td { padding: 12px; border-bottom: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>💎 Fidélisation : Parrains</h1>
        <div class="nav-links">
            <a href="gestion_boutique.php" class="btn">← Boutiques</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <form method="GET" style="display: flex; gap: 10px;">
                <input type="text" name="query" placeholder="Rechercher un visiteur ou un animal..." 
                       value="<?php echo htmlspecialchars($search); ?>" style="margin:0;">
                <button type="submit" class="btn">Rechercher</button>
            </form>
        </div>

        <div class="card">
            <h3><?php echo ($search !== '') ? "Résultats pour : " . htmlspecialchars($search) : "Derniers parrainages"; ?></h3>
            <table>
                <thead>
                    <tr>
                        <th>Visiteur</th>
                        <th>Animal Parrainé</th>
                        <th style="text-align: center;">Niveau de Contribution</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($resultats) > 0): ?>
                        <?php foreach ($resultats as $row): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['prenom_visiteur'] . " " . $row['nom_visiteur']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($row['email_visiteur']); ?></small>
                                </td>
                                <td>🐾 <?php echo htmlspecialchars($row['nom_animal']); ?></td>
                                <td style="text-align: center;">
                                    <span class="badge <?php echo strtolower($row['niveau_contribution']); ?>">
                                        <?php echo htmlspecialchars($row['niveau_contribution']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" style="text-align:center;">Aucun parrain trouvé.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
