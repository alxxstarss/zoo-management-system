<?php 
    // nourrir.php
    // Présente la dose de nourriture déjà définie pour l'animal
    // et affiche le formulaire de nourrissage.
    session_start();
    require_once 'db.php';
    if(!isset($_SESSION['id_user'])||!isset($_GET['id_animal'])){
        header('location: soigneur.php');
        exit();
    }
    $id_animal=$_GET['id_animal'];
    $id_user = $_SESSION['id_user'];
    $rqt_nourriture="SELECT dose_nourriture FROM nourrir N ,soigneur S WHERE N.id_animal =:id_animal AND N.id_soigneur= S.id_soigneur AND S.id_personnel =:id_user";
    $stdi_nourriture=oci_parse($conn,$rqt_nourriture);
    oci_bind_by_name($stdi_nourriture,":id_animal",$id_animal);
    oci_bind_by_name($stdi_nourriture, ":id_user", $id_user);
    oci_execute($stdi_nourriture);

    $result_nourriture=oci_fetch_array($stdi_nourriture,OCI_ASSOC);
    ?>
<!DOCTYPE html>
<html>
<head>
    <title>ZOO - Nourrissage</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <h1>🍎 Espace Nourrissage</h1>
        <div class="nav-links">
            <a href="soigneur.php" class="btn">⬅ Retour à la tournée</a>
        </div>
    </div>

    <div class="container">
        <div class="card" style="text-align: center;">
            <div style="font-size: 4rem; margin-bottom: 10px;">🍲</div>
            <h2>Vous nourrissez l'animal #<?php echo htmlspecialchars($id_animal); ?></h2>
            
            <?php if($result_nourriture): ?>
                <p>Dose actuelle enregistrée : <strong><?php echo $result_nourriture['DOSE_NOURRITURE']; ?></strong></p>
            <?php endif; ?>
            
            <br>
            <form method="post" action="">
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <input type="submit" value="🍎 Donner une dose (+1)" name="nourrir" class="btn">
                    <a href="soigneur.php" class="btn" style="background-color: #95a5a6;">Annuler</a>
                </div>
            </form>

            <?php if(!empty($message)) echo "<br>".$message; ?>
        </div>
    </div>
</body>
</html>
