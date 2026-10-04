<?php 
    // Démarrage de la session pour gérer l'authentification utilisateur.
    session_start();
?>
<!DOCTYPE html>
<html>
    <head> <title>Connexion</title>
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="login.css">
    </head>
    <body>
        <div class="login-wrapper"> <div class="login-card"> <div class="logo">🦭</div>
                <h1>Dashboard</h1>
                <p class="subtitle">Portail de Connexion</p>

                <form method="post" action="">
                    <div class="input-group">
                        <input name="id_personnel" type="text" placeholder="Identifiant" required>
                    </div>
                    <div class="input-group">
                        <input name="pass" type="password" placeholder="Mot de passe" required>
                    </div>
                    <input name="valider" type="submit" value="Se connecter" class="btn-login">
                </form>

                <?php
                require_once 'db.php';
                if (isset($_POST['id_personnel']) && isset($_POST['pass'])){
                    $id_personnel = $_POST['id_personnel'];
                    $pass = $_POST['pass'];
                    $sql = "SELECT mot_de_passe from Personnel where id_personnel= :id_personnel";
                    $ordre = oci_parse($conn, $sql);
                    oci_bind_by_name($ordre, ":id_personnel", $id_personnel);
                    oci_execute($ordre);
                    $result = oci_fetch_array($ordre, OCI_ASSOC);

                    if($result){
                        if(password_verify($pass, trim($result['MOT_DE_PASSE']))){
                            $_SESSION['id_user'] = $id_personnel;
                            header('Location: page_acceuil.php');
                            exit();
                        } else {
                            echo "<p class='error-msg'>❌ Mot de passe invalide</p>";
                        }
                    } else {
                        echo "<p class='error-msg'>⚠️ Utilisateur introuvable</p>";
                    }
                }
                ?>
            </div>
        </div>
    </body>
</html>
