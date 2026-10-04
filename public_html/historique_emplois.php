<?php
// historique_emplois.php
// Page réservée aux comptables pour rechercher
// l'historique des emplois et des contrats du personnel.
session_start();
require_once 'db.php';

if (!isset($_SESSION['id_user']) || $_SESSION['type_personnel'] !== 'Comptable') {
    header('Location: index.php');
    exit();
}

$search    = isset($_GET['search']) ? trim($_GET['search']) : '';
$historique = [];

if (!empty($search)) {
    $sql = "SELECT p.nom_personnel, p.prenom_personnel, p.type_personnel, e.*
            FROM Personnel p
            JOIN Emplois e ON p.id_personnel = e.id_personnel
            WHERE p.id_personnel = :id_search
               OR p.nom_personnel LIKE :term1
               OR p.prenom_personnel LIKE :term2
            ORDER BY e.date_debut DESC";

    $stmt = oci_parse($conn, $sql);
    $term = "%$search%";
    oci_bind_by_name($stmt, ':id_search', $search);
    oci_bind_by_name($stmt, ':term1',     $term);
    oci_bind_by_name($stmt, ':term2',     $term);
    oci_execute($stmt);

    while ($row = oci_fetch_assoc($stmt)) {
        $historique[] = array_change_key_case($row, CASE_LOWER);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ZOOLAND - Historique RH</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        /* style pour la page */
        .date-badge { 
            background: #ecf0f1; 
            padding: 5px 10px; 
            border-radius: 4px; 
            border-left: 3px solid var(--primary-color);
            font-size: 0.9em;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: var(--secondary-color); color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #ddd; background: white; }
        tr:hover td { background: #f9f9f9; }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>Évolution de Carrière</h1>
        <div class="nav-links">
            <a href="gestion_personnel.php" class="btn">Retour Personnel</a>
            <a href="page_acceuil.php" class="btn" style="background: transparent; border: 1px solid white;">Menu</a>
        </div>
    </div>

    <div class="container">
        
        <div class="card">
            <h3>🔍 Rechercher un historique</h3>
            <form method="GET" style="display: flex; gap: 10px; align-items: center;">
                <input type="text" name="search" placeholder="Entrez un ID, Nom ou Prénom..." 
                       value="<?php echo htmlspecialchars($search); ?>" style="margin: 0;">
                <button type="submit" class="btn">Rechercher</button>
            </form>
        </div>

        <?php if (!empty($search)): ?>
            <div class="card">
                <h2>Résultats pour : <?php echo htmlspecialchars($search); ?></h2>
                
                <?php if (count($historique) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Employé</th>
                                <th>Période de contrat</th>
                                <th>Type de Contrat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historique as $h): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($h['prenom_personnel'] . " " . $h['nom_personnel']); ?></strong><br>
                                    <small>ID Emploi: #<?php echo $h['id_emplois']; ?></small>
                                </td>
                                <td>
                                    <div class="date-badge">
                                        📅 Du <?php echo $h['date_debut']; ?> au <?php echo $h['date_fin'] ?? 'Présent'; ?>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: bold; color: var(--secondary-color);">
                                        <?php echo htmlspecialchars($h['contrat']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="msg-error">Aucun historique trouvé pour ce critère.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>
