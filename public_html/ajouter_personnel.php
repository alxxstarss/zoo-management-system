<?php
// ajouter_personnel.php
// Autorisé uniquement aux comptables : création et suppression d'employés.
session_start();
require_once 'db.php';

if (!isset($_SESSION['id_user']) || $_SESSION['type_personnel'] !== 'Comptable') {
    header('Location: index.php');
    exit();
}

$msg = "";

//  Logique d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_personnel'])) {
    $id_p     = $_POST['id_p'];
    $nom      = $_POST['nom'];
    $prenom   = $_POST['prenom'];
    $date_d   = $_POST['date_debut'];
    $mdp      = password_hash($_POST['mdp'], PASSWORD_DEFAULT); // Sécurité
    $salaire  = $_POST['salaire'];
    $type     = $_POST['type_p'];

    $sql_add = "INSERT INTO Personnel (id_personnel, nom_personnel, prenom_personnel, date_debut, mot_de_passe, salaire, type_personnel) 
                VALUES (:id, :nom, :prenom, TO_DATE(:dd, 'YYYY-MM-DD'), :mdp, :sal, :typ)";
    
    $st_add = oci_parse($conn, $sql_add);
    oci_bind_by_name($st_add, ':id', $id_p);
    oci_bind_by_name($st_add, ':nom', $nom);
    oci_bind_by_name($st_add, ':prenom', $prenom);
    oci_bind_by_name($st_add, ':dd', $date_d);
    oci_bind_by_name($st_add, ':mdp', $mdp);
    oci_bind_by_name($st_add, ':sal', $salaire);
    oci_bind_by_name($st_add, ':typ', $type);

    if (oci_execute($st_add)) {
        $msg = "✅ Employé ajouté avec succès !";
    } else {
        $e = oci_error($st_add);
        $msg = "❌ Erreur ajout : " . $e['message'];
    }
}

// logique de suppresion
if (isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    
    // Nettoyage des tables liées 
    $clean = [
        "DELETE FROM Vend WHERE id_vendeur = :id",
        "DELETE FROM Entretien WHERE id_personnel = :id",
        "DELETE FROM Intervient2 WHERE id_personnel_teq = :id",
        "DELETE FROM Chiffre_Af_j WHERE id_responsable = :id",
        "UPDATE Boutique SET id_responsable = NULL WHERE id_responsable = :id"
    ];
    
    foreach ($clean as $q) {
        $s = oci_parse($conn, $q);
        oci_bind_by_name($s, ':id', $id_to_delete);
        oci_execute($s, OCI_NO_AUTO_COMMIT);
    }

    $stmt_del = oci_parse($conn, "DELETE FROM Personnel WHERE id_personnel = :id");
    oci_bind_by_name($stmt_del, ':id', $id_to_delete);
    
    if (oci_execute($stmt_del, OCI_COMMIT_ON_SUCCESS)) {
        $msg = "✅ Employé #$id_to_delete supprimé.";
    } else {
        oci_rollback($conn);
        $msg = "❌ Erreur suppression.";
    }
}

// recuperation de la liste
$personnel = [];
$st_list = oci_parse($conn, "SELECT * FROM Personnel ORDER BY nom_personnel ASC");
oci_execute($st_list);
while ($row = oci_fetch_array($st_list, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $personnel[] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion du Personnel</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background: #f4f4f4; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        input, select { padding: 8px; margin: 5px 0; width: 100%; box-sizing: border-box; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #28a745; color: white; }
        .btn { background: #28a745; color: white; padding: 10px; border: none; cursor: pointer; border-radius: 4px; }
    </style>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
    <h1>Gestion du Personnel (Comptabilité)</h1>
    <a href="gestion_personnel.php" class="btn">Retour</a>
    </div>
    <div class="card">
        <h3>➕ Ajouter un nouvel employé</h3>
        <?php if ($msg) echo "<p>$msg</p>"; ?>
        <form method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <input type="number" name="id_p" placeholder="ID Personnel" required>
                <input type="text" name="nom" placeholder="Nom" required>
                <input type="text" name="prenom" placeholder="Prénom" required>
                <input type="date" name="date_debut" required>
                <input type="password" name="mdp" placeholder="Mot de passe" required>
                <input type="number" step="0.01" name="salaire" placeholder="Salaire (ex: 2500.50)" required>
                <select name="type_p" required>
                    <option value="Vendeur">Vendeur</option>
                    <option value="Responsable boutique">Responsable boutique</option>
                    <option value="Entretien">Entretien</option>
                    <option value="Soigneur">Soigneur</option>
                    <option value="Comptable">Comptable</option>
                </select>
            </div>
            <br>
            <button type="submit" name="ajouter_personnel" class="btn">Enregistrer l'employé</button>
        </form>
    </div>

    <div class="card">
        <h3>👥 Liste des employés</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom Complet</th>
                    <th>Poste</th>
                    <th>Salaire</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($personnel as $p): ?>
                <tr>
                    <td>#<?php echo $p['ID_PERSONNEL']; ?></td>
                    <td><?php echo htmlspecialchars($p['PRENOM_PERSONNEL'] . ' ' . $p['NOM_PERSONNEL']); ?></td>
                    <td><?php echo htmlspecialchars($p['TYPE_PERSONNEL']); ?></td>
                    <td><?php echo number_format((float)str_replace(',', '.', $p['SALAIRE']), 2, ',', ' '); ?> €</td>
                    <td>
                        <a href="?delete=<?php echo $p['ID_PERSONNEL']; ?>" 
                           onclick="return confirm('Supprimer définitivement ?')" 
                           style="color:red; text-decoration:none;">❌ Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
