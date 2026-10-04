<?php
// maintenance_especes.php
// Permet aux comptables de définir quelles espèces
// peuvent cohabiter dans le parc.
session_start();
require_once 'db.php';

if (!isset($_SESSION['id_user']) || $_SESSION['type_personnel'] !== 'Comptable') {
    header('Location: index.php');
    exit();
}
// traitement du formulaire
if (isset($_POST['add_cohab'])) {
    $esp1 = $_POST['esp1'];
    $esp2 = $_POST['esp2'];
    
    if ($esp1 != $esp2) { // Éviter de lier une espèce avec elle-même
        $stmt = oci_parse($conn, "INSERT INTO Cohabite (id_espece1, id_espece2) VALUES (:esp1, :esp2)");
        oci_bind_by_name($stmt, ':esp1', $esp1);
        oci_bind_by_name($stmt, ':esp2', $esp2);
        oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
    }
}

//  recuperation des especes
$especes = [];
$st_e = oci_parse($conn, "SELECT id_espece, nom_usuel_espece FROM Espece_Animale ORDER BY nom_usuel_espece");
oci_execute($st_e);
while ($row = oci_fetch_array($st_e, OCI_ASSOC)) {
    $especes[] = array_change_key_case($row, CASE_LOWER);
}

// recuperation des cohabitations
$cohabitations = [];
$sql_cohab = "SELECT e1.nom_usuel_espece as n1, e2.nom_usuel_espece as n2
              FROM Cohabite c
              JOIN Espece_Animale e1 ON c.id_espece1 = e1.id_espece
              JOIN Espece_Animale e2 ON c.id_espece2 = e2.id_espece";
$st_c = oci_parse($conn, $sql_cohab);
oci_execute($st_c);
while ($row = oci_fetch_array($st_c, OCI_ASSOC)) {
    $cohabitations[] = array_change_key_case($row, CASE_LOWER);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ZOOLAND - Maintenance Espèces</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .cohab-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .cohab-item:last-child { border-bottom: none; }
        .vs-circle {
            background: var(--primary-color);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 15px;
            font-size: 0.8em;
            font-weight: bold;
        }
        .esp-name { flex: 1; font-weight: 500; color: var(--secondary-color); }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>🤝 Gestion des Cohabitations</h1>
        <div class="nav-links">
            <a href="gestion_animaux.php" class="btn" style="background:transparent; border:1px solid white;">← Retour Animaux</a>
            <a href="page_acceuil.php" class="btn">Menu</a>
        </div>
    </div>

    <div class="container">
        
        <div class="card">
            <h3>🔗 Créer une nouvelle entente</h3>
            <p>Définissez quelles espèces peuvent partager le même enclos sans danger.</p>
            
            <form method="POST" style="display: flex; gap: 20px; align-items: flex-end; margin-top:15px;">
                <div style="flex: 1;">
                    <label>Espèce A :</label>
                    <select name="esp1" required>
                        <?php foreach($especes as $e): ?>
                            <option value="<?php echo $e['id_espece']; ?>"><?php echo htmlspecialchars($e['nom_usuel_espece']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="font-weight: bold; padding-bottom: 20px;">&</div>

                <div style="flex: 1;">
                    <label>Espèce B :</label>
                    <select name="esp2" required>
                        <?php foreach($especes as $e): ?>
                            <option value="<?php echo $e['id_espece']; ?>"><?php echo htmlspecialchars($e['nom_usuel_espece']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" name="add_cohab" class="btn" style="height: 45px;">Lier les espèces</button>
            </form>
        </div>

        <div class="card">
            <h3>📜 Liste des cohabitations autorisées</h3>
            <div style="margin-top: 15px;">
                <?php if (empty($cohabitations)): ?>
                    <p style="color: #666; font-style: italic;">Aucune cohabitation enregistrée pour le moment.</p>
                <?php else: ?>
                    <?php foreach ($cohabitations as $c): ?>
                        <div class="cohab-item">
                            <div class="esp-name" style="text-align: right;"><?php echo htmlspecialchars($c['n1']); ?></div>
                            <div class="vs-circle">🤝</div>
                            <div class="esp-name"><?php echo htmlspecialchars($c['n2']); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

</body>
</html>
