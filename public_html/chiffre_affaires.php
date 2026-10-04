<?php
// chiffre_affaires.php
// Gère l'enregistrement du chiffre d'affaires par boutique
// et affiche l'historique des montants saisis.
session_start();
require_once 'db.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: index.php');
    exit();
}

$id_btq = isset($_GET['id_boutique']) ? $_GET['id_boutique'] : null;
if (!$id_btq) { header('Location: gestion_boutique.php'); exit(); }

$id_user = $_SESSION['id_user'];
$msg = "";

//  Verification acces a la boutique
$sql_check = "SELECT nom_boutique FROM Boutique
              WHERE id_boutique = :id_b
              AND (id_responsable = :id_res
              OR id_boutique IN (SELECT id_boutique FROM Vend WHERE id_vendeur = :id_vend))";

$stmt = oci_parse($conn, $sql_check);
oci_bind_by_name($stmt, ':id_b',   $id_btq);
oci_bind_by_name($stmt, ':id_res', $id_user);
oci_bind_by_name($stmt, ':id_vend',$id_user);
oci_execute($stmt);
$boutique_info = oci_fetch_array($stmt, OCI_ASSOC); 

if (!$boutique_info) {
    die("Accès refusé : Vous n'êtes pas autorisé à voir cette boutique.");
}

// Logique d'ajout de chiffre d'affaire
if (isset($_POST['ajouter_ca'])) {
    $montant_brut = $_POST['montant'];
    $montant_propre = str_replace(',', '.', $montant_brut);
    $montant = (float)$montant_propre; // On force le type float
    $date_saisie = $_POST['date_j'];

    // On vérifie si une entrée existe déjà pour cette boutique à cette date
    // (car la clé primaire est id_responsable + id_boutique + date_j)
    $sql_ins = "INSERT INTO Chiffre_Af_j (id_responsable, id_boutique, date_j, montantca) 
                VALUES (:id_res, :id_b, TO_DATE(:dj, 'YYYY-MM-DD'), TO_NUMBER(:mnt, '99999999D99', 'NLS_NUMERIC_CHARACTERS = ''. '''))";
    
    $st_ins = oci_parse($conn, $sql_ins);
    oci_bind_by_name($st_ins, ':id_res', $id_user);
    oci_bind_by_name($st_ins, ':id_b',   $id_btq);
    oci_bind_by_name($st_ins, ':dj',     $date_saisie);
    oci_bind_by_name($st_ins, ':mnt',    $montant);

    if (oci_execute($st_ins)) {
        $msg = "<p style='color:green; font-weight:bold;'>✅ CA enregistré avec succès !</p>";
    } else {
        $e = oci_error($st_ins);
        $msg = "<p style='color:red; font-weight:bold;'>❌ Erreur : " . htmlspecialchars($e['message']) . "</p>";
    }
}

// 3. Historique CA
$historique = [];
$sql_hist = "SELECT * FROM Chiffre_Af_j WHERE id_boutique = :id_b ORDER BY date_j DESC";
$st_hist = oci_parse($conn, $sql_hist);
oci_bind_by_name($st_hist, ':id_b', $id_btq);
oci_execute($st_hist);
while ($row = oci_fetch_array($st_hist, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $historique[] = $row; 
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CA - <?php echo htmlspecialchars($boutique_info['NOM_BOUTIQUE']); ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; margin-top: 20px;}
        th { background: #2c3e50; color: white; padding: 15px; text-align: left; }
        td { padding: 12px 15px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f1f8f1; }
        .form-add { background: #ecf0f1; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #bdc3c7; }
        input[type="number"], input[type="date"] { padding: 10px; border-radius: 5px; border: 1px solid #ddd; margin-right: 10px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>📊 Chiffre d'Affaires</h1>
        <div class="nav-links">
            <a href="gestion_boutique.php" class="btn">← Retour</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <h2>Boutique : <?php echo htmlspecialchars($boutique_info['NOM_BOUTIQUE']); ?></h2>
            
            <?php echo $msg; ?>

            <div class="form-add">
                <h3>➕ Enregistrer un nouveau CA</h3>
                <form method="post">
                    <label>Date : </label>
                    <input type="date" name="date_j" value="<?php echo date('Y-m-d'); ?>" required>
                    
                    <label>Montant (€) : </label>
                    <input type="number" name="montant" step="0.01" placeholder="Ex: 1250.50" required>
                    
                    <input type="submit" name="ajouter_ca" value="Enregistrer" class="btn">
                </form>
            </div>

            <h3>Historique des ventes</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date de l'enregistrement</th>
                        <th style="text-align: right;">Montant du CA</th>
                    </tr>
                </thead>
                <tbody>
                   <?php foreach ($historique as $ligne): ?>
                    <tr>
                        <td>📅 <?php echo $ligne['DATE_J']; ?></td>
                        <td style="text-align: right; font-weight: bold; color: #27ae60;">
                            <?php 
                                $montant = (float)str_replace(',', '.', $ligne['MONTANTCA']); 
                                echo number_format($montant, 2, ',', ' '); 
                            ?> €
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($historique)): ?>
                        <tr><td colspan="2" style="text-align:center;">Aucune donnée enregistrée.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
