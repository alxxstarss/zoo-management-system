<?php
// ajouter_animal.php
// Page pour ajouter un nouvel animal au parc, réservée aux comptables.
session_start();
require_once 'db.php';

if (!isset($_SESSION['id_user']) || $_SESSION['type_personnel'] !== 'Comptable') {
    header('Location: index.php');
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "INSERT INTO Animal (id_animal, nom_animal, menace, date_animal, poids_animal, regime_animal, id_espece, idpere, idmere, id_enclos)
            VALUES (:id, :nom, :menace, TO_DATE(:date_a,'YYYY-MM-DD'), :poids, :regime, :esp, :pere, :mere, :enc)";
    $stmt = oci_parse($conn, $sql);
    $params = [
        'id'     => $_POST['id_animal'],
        'nom'    => $_POST['nom_animal'],
        'menace' => $_POST['menace'],
        'date_a' => $_POST['date_animal'],
        'poids'  => $_POST['poids_animal'],
        'regime' => $_POST['regime_animal'],
        'esp'    => $_POST['id_espece'],
        'pere'   => !empty($_POST['idpere']) ? $_POST['idpere'] : null,
        'mere'   => !empty($_POST['idmere']) ? $_POST['idmere'] : null,
        'enc'    => $_POST['id_enclos'],
    ];

    oci_bind_by_name($stmt, ':id',     $params['id']);
    oci_bind_by_name($stmt, ':nom',    $params['nom']);
    oci_bind_by_name($stmt, ':menace', $params['menace']);
    oci_bind_by_name($stmt, ':date_a', $params['date_a']);
    oci_bind_by_name($stmt, ':poids',  $params['poids']);
    oci_bind_by_name($stmt, ':regime', $params['regime']);
    oci_bind_by_name($stmt, ':esp',    $params['esp']);
    oci_bind_by_name($stmt, ':pere',   $params['pere']);
    oci_bind_by_name($stmt, ':mere',   $params['mere']);
    oci_bind_by_name($stmt, ':enc',    $params['enc']);

    if (oci_execute($stmt)) {
        $message = "<p style='color:green; font-weight:bold;'>L'animal a ete enregistre avec succes !</p>";
    } else {
        $e = oci_error($stmt);
        $message = "<p style='color:red; font-weight:bold;'>Erreur : " . htmlspecialchars($e['message']) . "</p>";
    }
}

// Recuperation des données pour la liste 

// Especes
$especes = [];
$st_esp = oci_parse($conn, "SELECT id_espece, nom_usuel_espece FROM Espece_Animale ORDER BY nom_usuel_espece");
oci_execute($st_esp);
while ($row = oci_fetch_array($st_esp, OCI_ASSOC)) {
    $especes[] = array_change_key_case($row, CASE_LOWER);
}

// Enclos
$enclos = [];
$st_enc = oci_parse($conn, "SELECT id_enclos, particularite FROM Enclos ORDER BY id_enclos");
oci_execute($st_enc);
while ($row = oci_fetch_array($st_enc, OCI_ASSOC)) {
    $enclos[] = array_change_key_case($row, CASE_LOWER);
}

// Parents 
$tous_animaux = [];
$st_ani = oci_parse($conn, "SELECT id_animal, nom_animal FROM Animal ORDER BY nom_animal");
oci_execute($st_ani);
while ($row = oci_fetch_array($st_ani, OCI_ASSOC)) {
    $tous_animaux[] = array_change_key_case($row, CASE_LOWER);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ZOOLAND - Ajouter un Animal</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styles pour le formulaire d'ajout */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .section-title {
            grid-column: span 2;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 5px;
            margin-top: 20px;
            color: var(--secondary-color);
        }
        .full-width {
            grid-column: span 2;
        }
        .genealogie-box {
            background: #f1f8f1;
            padding: 20px;
            border-radius: 8px;
            border-left: 5px solid var(--primary-color);
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>🐘 Nouvel Enregistrement Animal</h1>
        <div class="nav-links">
            <a href="gestion_animaux.php" class="btn" style="background:transparent; border:1px solid white;">← Retour à la liste</a>
        </div>
    </div>

    <div class="container">
        
        <?php if ($message) echo $message; ?>

        <div class="card">
            <form method="POST">
                
                <div class="form-grid">
                    <h3 class="section-title">Informations Générales</h3>
                    
                    <div class="form-group">
                        <label>Identifiant Unique (ID) :</label>
                        <input type="number" name="id_animal" required placeholder="Ex: 2007">
                    </div>

                    <div class="form-group">
                        <label>Nom de l'animal :</label>
                        <input type="text" name="nom_animal" required placeholder="Ex: Simba">
                    </div>

                    <div class="form-group">
                        <label>Espèce :</label>
                        <select name="id_espece" required>
                            <option value="" disabled selected>-- Choisir une espèce --</option>
                            <?php foreach($especes as $e): ?>
                                <option value="<?php echo $e['id_espece']; ?>"><?php echo htmlspecialchars($e['nom_usuel_espece']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Menace (Statut de conservation) :</label>
                        <select name="menace">
                            <option value="Non">Non (Espèce non menacée)</option>
                            <option value="Oui">Oui (Espèce en danger)</option>
                        </select>
                    </div>

                    <h3 class="section-title">Détails Biométriques & Habitat</h3>

                    <div class="form-group">
                        <label>Date de naissance / Arrivée :</label>
                        <input type="date" name="date_animal" required>
                    </div>

                    <div class="form-group">
                        <label>Poids actuel (kg) :</label>
                        <input type="number" step="0.01" name="poids_animal" required placeholder="Ex: 150.5">
                    </div>

                    <div class="form-group">
                        <label>Régime alimentaire :</label>
                        <input type="text" name="regime_animal" placeholder="Ex: Carnivore strict" required>
                    </div>

                    <div class="form-group">
                        <label>Enclos d'affectation :</label>
                        <select name="id_enclos" required>
                            <option value="" disabled selected>-- Sélectionner l'enclos --</option>
                            <?php foreach($enclos as $enc): ?>
                                <option value="<?php echo $enc['id_enclos']; ?>">🏠 Enclos n°<?php echo $enc['id_enclos']; ?> (<?php echo htmlspecialchars($enc['particularite']); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="full-width genealogie-box">
                        <h3 style="margin-top:0;">🌳 Généalogie (Optionnel)</h3>
                        <p style="font-size: 0.9em; color: #666;">Sélectionnez les parents si ces derniers sont déjà enregistrés dans la base.</p>
                        
                        <div style="display: flex; gap: 20px;">
                            <div style="flex: 1;">
                                <label>Père :</label>
                                <select name="idpere">
                                    <option value="">-- Aucun parent mâle --</option>
                                    <?php foreach($tous_animaux as $a): ?>
                                        <option value="<?php echo $a['id_animal']; ?>"><?php echo htmlspecialchars($a['nom_animal']); ?> (#<?php echo $a['id_animal']; ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div style="flex: 1;">
                                <label>Mère :</label>
                                <select name="idmere">
                                    <option value="">-- Aucun parent femelle --</option>
                                    <?php foreach($tous_animaux as $a): ?>
                                        <option value="<?php echo $a['id_animal']; ?>"><?php echo htmlspecialchars($a['nom_animal']); ?> (#<?php echo $a['id_animal']; ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 30px; text-align: right;">
                    <button type="reset" class="btn" style="background: #95a5a6; margin-right: 10px;">Réinitialiser</button>
                    <button type="submit" class="btn">💾 Enregistrer l'Animal</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
