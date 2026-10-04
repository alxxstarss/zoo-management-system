<?php
// Page d'accueil après authentification. Elle lit les informations
// du personnel en fonction de l'utilisateur connecté.
session_start();
require_once 'db.php';

// Si l'utilisateur n'est pas connecté, redirection vers la page de login.
if (!isset($_SESSION['id_user'])) {
    header('Location: index.php');
    exit();
}

$sql_personnel="SELECT id_personnel,nom_personnel,prenom_personnel,type_personnel,salaire from Personnel where id_personnel=:id_user";
$stdi=oci_parse($conn,$sql_personnel);
oci_bind_by_name($stdi,":id_user",$_SESSION['id_user']);
oci_execute($stdi);
$result=oci_fetch_array($stdi,OCI_ASSOC);

$nom = $result['NOM_PERSONNEL'];
$prenom = $result['PRENOM_PERSONNEL'];
$role = $result['TYPE_PERSONNEL'];
$_SESSION['type_personnel']=$role;
$_SESSION['prenom_personnel']=$prenom;
$_SESSION['nom_personnel']=$nom;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Menu Principal - Zoo d'Amiens</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="page_acceuil.css">
</head>
<body>

<div class="navbar">
    <h1>Dashboard 🦭</h1>
    <div class="nav-links">
        <a href="logout.php" class="btn btn-danger">Déconnexion</a>
    </div>
</div>

<div class="container" style="text-align: center; margin-top: 40px;">
    
    <span class="role-badge">✨ <?php echo htmlspecialchars($role); ?></span>
    <h1>Tableau de Bord Personnel</h1>
    <p>Bonjour <strong><?php echo htmlspecialchars($prenom . " " . $nom); ?></strong>, que souhaitez-vous faire aujourd'hui ?</p>

    <div class="menu-grid">
        
        <?php if ($role == 'Responsable boutique' || $role == 'Vendeur'): ?>
            <a href="gestion_boutique.php" class="card card-link">
                <h3>🛒 Boutique</h3>
                <p>Consulter les ventes du jour et gérer la fidélité des parrains.</p>
            </a>
            <a href="liste_visiteurs.php" class="card card-link admin-card">
    		    <h3>👥 Annuaire Visiteurs</h3>
    		    <p>Consulter les visiteurs. (Ajout réservé aux Comptables)</p>
	        </a>
        <?php endif; ?>

        <?php if ($role == 'Soigneur'): ?>
            <a href="soigneur.php" class="card card-link">
                <h3>🥩 Soins & Nourriture</h3>
                <p>Accéder à votre tournée quotidienne et soigner les animaux.</p>
            </a>
        <?php endif; ?>

        <?php if ($role == 'Entretien'): ?>
            <a href="gestion_enclos.php" class="card card-link">
                <h3>🛠️ Maintenance</h3>
                <p>Vérifier l'état technique des enclos et zones du parc.</p>
            </a>
        <?php endif; ?>

        <?php if ($role == 'Comptable'): ?>
            <a href="gestion_personnel.php" class="card card-link admin-card">
                <h3>👥 Ressources Humaines</h3>
                <p>Gestion complète des employés (Ajout, modification, salaires).</p>
            </a>
            
            <a href="historique_emplois.php" class="card card-link admin-card">
                <h3>📈 Rapports & Historique</h3>
                <p>Analyse de l'évolution des contrats et des masses salariales.</p>
            </a>
            <a href="gestion_animaux.php" class="card card-link admin-card">
                <h3>🦒 Gestion Animaux (Admin)</h3>
                <p>Inventaire complet des animaux, espèces et gestion des cohabitations.</p>
            </a>
            <a href="liste_visiteurs.php" class="card card-link admin-card">
    		    <h3>👥 Annuaire Visiteurs</h3>
    		    <p>Consulter les visiteurs. (Ajout réservé aux Comptables)</p>
	        </a>

        <?php endif; ?>

        <a href="profil.php" class="card card-link" style="border-top-color: #95a5a6;">
            <h3>👤 Mon Profil</h3>
            <p>Modifier vos informations personnelles et votre mot de passe.</p>
        </a>

    </div>
</div>

</body>
</html>
