<?php
// gestion_animaux.php
// Permet aux comptables de consulter et supprimer des animaux.
session_start();
require_once 'db.php';

if (!isset($_SESSION['id_user']) || $_SESSION['type_personnel'] !== 'Comptable') {
    header('Location: index.php');
    exit();
}


if (isset($_GET['delete'])) {
    $id_del = $_GET['delete'];
    
    // Nettoyage des tables enfants avant suppression de l'animal
    $queries = [
        "DELETE FROM Parraine WHERE id_animal = :id",
        "DELETE FROM Soigne WHERE id_animal = :id",
        "DELETE FROM Nourrir WHERE id_animal = :id"
    ];
    
    foreach ($queries as $q) {
        $st = oci_parse($conn, $q);
        oci_bind_by_name($st, ':id', $id_del);
        oci_execute($st, OCI_NO_AUTO_COMMIT);
    }

    // Suppression finale de l'animal
    $stmt = oci_parse($conn, "DELETE FROM Animal WHERE id_animal = :id");
    oci_bind_by_name($stmt, ':id', $id_del);
    oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
    
    header('Location: gestion_animaux.php');
    exit();
}

$sql = "SELECT a.*, e.nom_usuel_espece, enc.id_enclos
        FROM Animal a
        JOIN Espece_Animale e ON a.id_espece = e.id_espece
        JOIN Enclos enc ON a.id_enclos = enc.id_enclos
        ORDER BY a.id_animal DESC";
$animaux = [];
$stmt_list = oci_parse($conn, $sql);
oci_execute($stmt_list);

while ($row = oci_fetch_array($stmt_list, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $animaux[] = array_change_key_case($row, CASE_LOWER);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - Gestion des Animaux</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Ajustements spécifiques pour le tableau des animaux */
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
        th { background-color: var(--secondary-color); color: white; padding: 15px; text-align: left; }
        td { padding: 12px 15px; border-bottom: 1px solid #ddd; }
        tr:hover { background-color: #f1f8f1; }
        .badge-menace { 
            padding: 4px 8px; 
            border-radius: 4px; 
            font-size: 0.85em; 
            font-weight: bold; 
        }
        .menace-oui { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .menace-non { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>🐾 Gestion des Animaux</h1>
        <div class="nav-links">
            <a href="ajouter_animal.php" class="btn" style="background-color: var(--primary-color);">➕ Ajouter un Animal</a>
            <a href="page_acceuil.php" class="btn" style="background-color: transparent; border: 1px solid white;">Menu</a>
        </div>
    </div>

    <div class="container">
        
        <div class="card">
            <h3>Liste du cheptel actuel</h3>
            <p>Voici la liste des animaux enregistrés dans la base de données du zoo.</p>

            <div class="card" style="border-left: 5px solid #f1c40f; background: #fffdf0;">
                <p><strong>Note sur la cohabitation :</strong> Avant de déplacer un animal dans un nouvel enclos, vérifiez les <a href="maintenance_especes.php" style="color: var(--secondary-color); font-weight: bold;">règles d'entente entre espèces</a> pour éviter les conflits territoriaux.</p>
            </div>
            
            <table style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Espèce</th>
                        <th>Enclos</th>
                        <th>Menacé ?</th>
                        <th>Espèce & Compatibilité</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($animaux) > 0): ?>
                        <?php foreach ($animaux as $a): ?>
                        <tr>
                            <td>#<?php echo $a['id_animal']; ?></td>
                            <td><strong><?php echo htmlspecialchars($a['nom_animal']); ?></strong></td>
                            <td><?php echo htmlspecialchars($a['nom_usuel_espece']); ?></td>
                            <td><span style="color: #666;">Enclos n°<?php echo $a['id_enclos']; ?></span></td>
                            <td>
                                <span class="badge-menace <?php echo (strtolower($a['menace']) == 'oui') ? 'menace-oui' : 'menace-non'; ?>">
                                    <?php echo strtoupper($a['menace']); ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="?delete=<?php echo $a['id_animal']; ?>" 
                                   class="btn" 
                                   style="background-color: #e74c3c; padding: 5px 10px; font-size: 0.9em;"
                                   onclick="return confirm('Voulez-vous vraiment supprimer cet animal et ses données liées ?')">
                                   Supprimer
                                </a>
                            </td>
                            <td>
                                <strong><?php echo $a['nom_usuel_espece']; ?></strong><br>
                                <a href="maintenance_especes.php" style="font-size: 0.8em; color: var(--primary-color); text-decoration: none;">
                                    🔍 Voir les ententes
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center;">Aucun animal trouvé.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>