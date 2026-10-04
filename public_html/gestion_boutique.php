<?php
// gestion_boutique.php
// Affiche les boutiques assignées au vendeur ou responsable boutique.
session_start();
require_once 'db.php';

if (!isset($_SESSION['id_user']) || !in_array($_SESSION['type_personnel'], ['Responsable boutique', 'Vendeur'])) {
    header('Location: index.php');
    exit();
}

$id_user = $_SESSION['id_user'];

$sql = "SELECT b.* FROM Boutique b
        WHERE b.id_responsable = :id_res
        OR b.id_boutique IN (SELECT id_boutique FROM Vend WHERE id_vendeur = :id_vend)";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':id_res',  $id_user);
oci_bind_by_name($stmt, ':id_vend', $id_user);
oci_execute($stmt);

$boutiques = [];
while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
    $boutiques[] = array_change_key_case($row, CASE_LOWER);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ZOOLAND - Mes Boutiques</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <h1>🏬 Tableau de Bord Boutique</h1>
        <div class="nav-links">
            <a href="page_acceuil.php" class="btn" style="background:transparent; border:1px solid white;">Menu</a>
            <a href="logout.php" class="btn" style="background:#e74c3c;">Déconnexion</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <p>Bienvenue, <strong><?php echo htmlspecialchars($_SESSION['prenom_personnel'] . " " . $_SESSION['nom_personnel']); ?></strong></p>
            <nav style="margin-top:10px;">
                <a href="recherche_visiteur.php" class="btn">🔍 Rechercher des Parrains</a>
            </nav>
        </div>

        <h2>Vos Boutiques assignées :</h2>
        
        <?php if (count($boutiques) > 0): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <?php foreach ($boutiques as $btq): ?>
                    <div class="card">
                        <h3 style="color:var(--secondary-color);"><?php echo htmlspecialchars($btq['nom_boutique']); ?></h3>
                        <p><strong>Type :</strong> <span class="badge" style="background:#eee; color:#333;"><?php echo htmlspecialchars($btq['type_boutique']); ?></span></p>
                        <br>
                        <a href="chiffre_affaires.php?id_boutique=<?php echo $btq['id_boutique']; ?>" class="btn">
                            📊 Consulter le CA
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card"><p>Vous n'avez aucune boutique assignée.</p></div>
        <?php endif; ?>
    </div>
</body>
</html>
