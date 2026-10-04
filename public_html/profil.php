<?php
    // profil.php
    // Affiche les informations du personnel connecté
    // et son contrat actuel.
    session_start(); 
    if(!isset($_SESSION['id_user'])){
        header('Location: index.php');
        exit();
    }
    require_once 'db.php';

    // Récupère les informations personnelles de l'utilisateur.
    $sql_personnel="SELECT id_personnel,nom_personnel,prenom_personnel,type_personnel,salaire from Personnel where id_personnel=:id_user";
    $stdi=oci_parse($conn,$sql_personnel);
    oci_bind_by_name($stdi,":id_user",$_SESSION['id_user']);
    oci_execute($stdi);
    $result=oci_fetch_array($stdi,OCI_ASSOC);

    // Charge le contrat en cours pour le profil.
    $sql_emplois="SELECT TO_CHAR(date_debut, 'DD/MM/YYYY') as DEBUT, TO_CHAR(date_fin, 'DD/MM/YYYY') as FIN,contrat FROM Emplois WHERE id_personnel=:id_user";
    $stdi_emp=oci_parse($conn,$sql_emplois);
    oci_bind_by_name($stdi_emp,":id_user",$_SESSION['id_user']);
    oci_execute($stdi_emp);
    $result_emp=oci_fetch_array($stdi_emp,OCI_ASSOC);
    ?> 
<html>
    <head>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="card">
        <div class="navbar">
            <h1>Page Profil </h1> 
            <div class="nav-links">       
            <a href='page_acceuil.php' class="btn">Revenir en arriere</a>
            <a href='logout.php' class="btn">se deconnecter</a>
            </div>  
        </div>
        <div class="card">
            <h2>Bonjour <?php echo $result['NOM_PERSONNEL']." ".$result['PRENOM_PERSONNEL'] ;?> </h2>
           <h3>Contrat</h3>  
           <?php 
                if($result_emp){
                    echo "<p>vous actuellement en ".$result_emp['CONTRAT'];
                    echo "<br><p>La date de debut :".$result_emp['DEBUT'];
                    if(!empty($result_emp['DATE_FIN'])) echo "<br><p>La date de fin :".$result_emp['FIN'];
                }
                ?>
            <br>    
            <h3>Mon Salaire Actuel</h3>
            <?php
                echo $result['SALAIRE']; 
            ?>
            <br>
            <a href="modif_profil.php" class="btn">modifier mes informations</a>
        </div>
    </div>
    </body>
</html>
