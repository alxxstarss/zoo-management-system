<?php
    // soigneur.php
    // Affiche la tournée du soigneur connecté avec les animaux à suivre.
    session_start(); 
    require_once 'db.php';
    if(!isset($_SESSION['id_user'])){
        header('Location: index.php');
        exit();
    }

    $rqt_user="SELECT nom_personnel,prenom_personnel,type_personnel from Personnel where id_personnel= :id_user";
    $rqt_tournee="SELECT A.id_Animal,A.nom_animal, A.regime_animal, E.id_enclos, Z.nom_zone FROM Personnel P ,Zone Z,Entretien Ent ,Enclos E ,Animal A WHERE P.id_personnel = Ent.id_personnel AND Ent.id_zone = Z.id_zone AND Z.id_zone = E.id_zone AND E.id_enclos = A.id_enclos AND P.id_personnel = :id_user";
    $stdi_user=oci_parse($conn,$rqt_user);
    oci_bind_by_name($stdi_user,":id_user",$_SESSION['id_user']);
    oci_execute($stdi_user);

    $stdi_tournee=oci_parse($conn,$rqt_tournee);
    oci_bind_by_name($stdi_tournee,":id_user",$_SESSION['id_user']);
    oci_execute($stdi_tournee);

    $result_user=oci_fetch_array($stdi_user,OCI_ASSOC);
?>
<html>
    <head>
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="soigneur.css">
    </head>
    <body>
            <div class="navbar">
                <h1>Mon Espace Personnel</h1>
                <div class="nav-links">
                    <a href="page_acceuil.php" class="btn">Retourner à la page d'acceuil </a>
                    <a href="logout.php" class="btn" style="background-color: #e74c3c;">Se déconnecter</a>
                </div>
            </div>
            <div class="container">
            <div class="card">
                <h2><?php echo htmlspecialchars($result_user['PRENOM_PERSONNEL']." ".$result_user['NOM_PERSONNEL']); ?></h2>
                <p>Poste : <strong><?php echo $result_user['TYPE_PERSONNEL']; ?></strong></p>
            </div>
            <div class="card">
                <h3>Ma tournée du jour</h3>
                <table>
                    <tr><td>Nom Animal</td><td>Regime Alimentaire</td><td>num Enclos</td><td>Nom Zone</td><td>action</td></tr>
                    <?php while ($result_tournee = oci_fetch_array($stdi_tournee, OCI_ASSOC)):?>
                        <tr>
                            <td><?php echo $result_tournee['NOM_ANIMAL'] ;?></td>
                            <td><?php echo $result_tournee['REGIME_ANIMAL'] ?></td>
                            <td><?php echo $result_tournee['ID_ENCLOS'];?></td>
                            <td><?php echo $result_tournee['NOM_ZONE'];?> </td>
                           <td>
                                <a href="nourrir.php?id_animal=<?php echo $result_tournee['ID_ANIMAL']; ?>" class="btn btn-nourrir">🍎 Nourrir</a>
                                <a href="soin.php?id_animal=<?php echo $result_tournee['ID_ANIMAL']; ?>" class="btn btn-soin">🏥 Soigner</a>
                            </td>
                            </tr> 
                            <?php endwhile; ?>             
                </table>
            </div>
            </div>
            </div>
    </body>
</html>
