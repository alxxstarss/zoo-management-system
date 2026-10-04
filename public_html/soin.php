 <?php

    // soin.php
    // Présente les types de soins disponibles pour l'animal sélectionné.
    session_start();
    require_once 'db.php';
    if(!isset($_SESSION['id_user'])||!isset($_GET['id_animal'])){
        header('location: soigneur.php');
        exit();
    }
    $id_animal=$_GET['id_animal'];
    $id_user = $_SESSION['id_user'];

    // Charge les types de soins déjà définis pour cet animal.
    $rqt_soin="SELECT type_soins FROM Soigne Se WHERE Se.id_animal =:id_animal ";
    $stdi_soin=oci_parse($conn,$rqt_soin);
    oci_bind_by_name($stdi_soin,":id_animal",$id_animal);
    oci_execute($stdi_soin);
    ?> 
    
<html>
    <head>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
       <div class="navbar">
        <h1>🏥 Soins Médicaux</h1>
        <div class="nav-links">
            <a href="soigneur.php" class="btn">⬅ Retour à la tournée</a>
        </div>
        </div>
        <div class="container">
        <div class="card">
        <h2>Appliquer des soins : Animal #<?php echo $id_animal; ?></h2>
        <p>Sélectionnez le type de soin et la dose à administrer.</p>
        <br>
        <form method="post" action="">
        <div class="form-group">
            <label for="soins">Type de soins disponibles :</label>
            <select name="soins" id="soins">
            <?php
                while( $line=oci_fetch_array($stdi_soin, OCI_ASSOC)) {
                    $type_s = $line['TYPE_SOINS'];
                    echo "<option value='".$type_s."'>".$type_s."</option>";
                }
            ?>
            </select>
        </div>

        <div class="form-group">
            <label for="dose">Dose de soins :</label>
            <input type="number" id="dose" placeholder="Ex: 5" name="dose" required>
        </div>

        <input type="submit" value="💉 Appliquer le soin" name="appliquer" class="btn" style="width: 100%;">
    </form>

    <?php if(!empty($message)) echo "<br>".$message; ?>
    </div>
</div>
</body>
</html>
