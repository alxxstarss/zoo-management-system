<?php
// ajouter_visiteur.php
// Autorisé uniquement aux comptables : enregistrement d'un nouveau visiteur.
session_start();
require_once 'db.php';

//Vérifier si l'utilisateur est un Comptable
if (!isset($_SESSION['id_user']) || $_SESSION['type_personnel'] !== 'Comptable') {
    header('Location: index.php');
    exit();
}

$message = "";

//  TRAITEMENT DU FORMULAIRE 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "INSERT INTO Visiteur (id_visiteur, nom_visiteur, prenom_visiteur, tel_visiteur, email_visiteur)
            VALUES (:id, :nom, :prenom, :tel, :email)";

    $stmt = oci_parse($conn, $sql);

    // Liaison des variables
    oci_bind_by_name($stmt, ':id', $_POST['id_visiteur']);
    oci_bind_by_name($stmt, ':nom', $_POST['nom_visiteur']);
    oci_bind_by_name($stmt, ':prenom', $_POST['prenom_visiteur']);
    oci_bind_by_name($stmt, ':tel', $_POST['tel_visiteur']);
    oci_bind_by_name($stmt, ':email', $_POST['email_visiteur']);

    if (oci_execute($stmt)) {
        $message = "<div class='alert success'>✅ Visiteur enregistré avec succès !</div>";
    } else {
        $e = oci_error($stmt);
        $message = "<div class='alert error'>❌ Erreur Oracle : " . htmlentities($e['message']) . "</div>";
    }
    oci_free_statement($stmt);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Visiteur</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; margin: 40px; }
        .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1 { color: #1565c0; margin-top: 0; }
        .nav-actions { margin-bottom: 25px; display: flex; gap: 15px; }
        .btn-back { text-decoration: none; color: #555; font-weight: bold; display: flex; align-items: center; gap: 5px; }
        .btn-back:hover { color: #1565c0; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .btn-submit { background: #2e7d32; color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; width: 100%; font-size: 16px; font-weight: bold; margin-top: 10px; }
        .btn-submit:hover { background: #1b5e20; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<div class="container">
    <div class="nav-actions">
        <a href="liste_visiteurs.php" class="btn-back">⬅️ Retour à la liste</a>
        <a href="page_acceuil.php" class="btn-back">🏠 Menu Principal</a>
    </div>

    <h1>🎟️ Nouveau Visiteur</h1>

    <?php echo $message; ?>

    <form method="POST">
        <div class="form-group">
            <label>ID Visiteur (Unique) :</label>
            <input type="number" name="id_visiteur" required placeholder="Ex: 5001">
        </div>
        <div class="form-group">
            <label>Nom :</label>
            <input type="text" name="nom_visiteur" required placeholder="Ex: DUPONT">
        </div>
        <div class="form-group">
            <label>Prénom :</label>
            <input type="text" name="prenom_visiteur" required placeholder="Ex: Marie">
        </div>
        <div class="form-group">
            <label>Téléphone :</label>
            <input type="text" name="tel_visiteur" maxlength="10" placeholder="0601020304">
        </div>
        <div class="form-group">
            <label>Email :</label>
            <input type="email" name="email_visiteur" required placeholder="marie.dupont@exemple.com">
        </div>
        <button type="submit" class="btn-submit">✅ Enregistrer le Visiteur</button>
    </form>
</div>

</body>
</html>
