<?php
    // modif_profil.php
    // Permet au personnel connecté de modifier son mot de passe.
    session_start(); 
    if(!isset($_SESSION['id_user'])){
        header('Location: index.php');
        exit();
    }
    require_once 'db.php';

    $id_user = $_SESSION['id_user'];
    $message = "";

    if(isset($_POST['valider'])){
        $new_pass = $_POST['pass'];
        $conf_pass = $_POST['conf_pass'];

        // Vérification de la saisie et de la confirmation du mot de passe.
        if(!empty($new_pass)){
            // On vérifie si les deux mots de passe correspondent
            if($new_pass === $conf_pass){
                $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
                
                $sql_pass = "UPDATE Personnel SET mot_de_passe = :pass WHERE id_personnel = :id_user";
                $stdi_pass = oci_parse($conn, $sql_pass);
                oci_bind_by_name($stdi_pass, ":pass", $hashed_pass);
                oci_bind_by_name($stdi_pass, ":id_user", $id_user);
                
                if(oci_execute($stdi_pass, OCI_COMMIT_ON_SUCCESS)){
                    $message = "<p class='msg-success'>✅ Mot de passe mis à jour avec succès !</p>";
                } else {
                    $message = "<p class='msg-error'>❌ Erreur lors de la mise à jour.</p>";
                }
            } else {
                $message = "<p class='msg-error'>⚠️ Les mots de passe ne correspondent pas.</p>";
            }
        } else {
            $message = "<p class='msg-error'>⚠️ Veuillez saisir un nouveau mot de passe.</p>";
        }
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ZOOLAND - Sécurité</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <h1>🔒 Sécurité du compte</h1>
        <div class="nav-links">
            <a href="page_acceuil.php" class="btn" style="background:transparent; border:1px solid white;">Retour</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <h3>Changer mon mot de passe</h3>
            <p style="color: #666; margin-bottom: 20px;">Choisissez un mot de passe robuste pour protéger votre accès au cheptel.</p>
            
            <?php echo $message; ?>

            <form method="post" style="margin-top: 15px;">
                <label>Nouveau mot de passe :</label>
                <input type="password" name="pass" placeholder="Entrez le nouveau mot de passe" required>

                <label>Confirmer le mot de passe :</label>
                <input type="password" name="conf_pass" placeholder="Répétez le mot de passe" required>

                <div style="margin-top: 20px;">
                    <input type="submit" name="valider" value="Mettre à jour le mot de passe" class="btn" style="width: 100%;">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
