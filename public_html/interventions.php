<?php
// interventions.php
// Enregistre les demandes de réparation et liste
// les interventions planifiées.
session_start();
require_once 'db.php';

if (!isset($_SESSION['id_user'])) { header('Location: index.php'); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['creer_reparation'])) {
    $id_enclos = $_POST['id_enclos'];
    $nature    = $_POST['nature'];
    $type      = $_POST['type_rep'];
    $id_rep    = rand(1000, 9999);

    $stmt1 = oci_parse($conn, "INSERT INTO Reparation (num_reparation, nature_reparation, type_reparation) VALUES (:id_rep, :nature, :type)");
    oci_bind_by_name($stmt1, ':id_rep', $id_rep);
    oci_bind_by_name($stmt1, ':nature', $nature);
    oci_bind_by_name($stmt1, ':type',   $type);
    oci_execute($stmt1, OCI_NO_AUTO_COMMIT);

    $stmt2 = oci_parse($conn, "INSERT INTO Reparer (id_enclos, num_reparation) VALUES (:id_enclos, :id_rep)");
    oci_bind_by_name($stmt2, ':id_enclos', $id_enclos);
    oci_bind_by_name($stmt2, ':id_rep',    $id_rep);
    $ok = oci_execute($stmt2, OCI_NO_AUTO_COMMIT);

    if ($ok) {
        oci_commit($conn);
        echo "<p style='color:green'>Demande enregistree !</p>";
    } else {
        oci_rollback($conn);
        $e = oci_error($stmt2);
        echo "Erreur : " . htmlspecialchars($e['message']);
    }
}

$sql = "SELECT r.*, e.id_enclos FROM Reparation r
        JOIN Reparer rep ON r.num_reparation = rep.num_reparation
        JOIN Enclos e ON rep.id_enclos = e.id_enclos";
$repairs = [];
$stmt_list = oci_parse($conn, $sql);
oci_execute($stmt_list);

while ($row = oci_fetch_array($stmt_list, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $repairs[] = array_change_key_case($row, CASE_LOWER);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ZOOLAND - Suivi Interventions</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-group { display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; }
        .form-group div { flex: 1; min-width: 200px; }
        
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; }
        th { background: var(--secondary-color); color: white; padding: 12px; }
        td { padding: 12px; border-bottom: 1px solid #ddd; }

        .badge-type { padding: 4px 10px; border-radius: 20px; font-size: 0.8em; font-weight: bold; }
        .type-technique { background: #3498db; color: white; }
        .type-electrique { background: #f1c40f; color: #000; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>🛠️ Suivi des Réparations</h1>
        <div class="nav-links">
            <a href="gestion_enclos.php" class="btn">← Retour Enclos</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <h3>📝 Nouvelle Demande d'Intervention</h3>
            <form method="POST">
                <div class="form-group">
                    <div>
                        <label>ID Enclos :</label>
                        <input type="number" name="id_enclos" value="<?php echo htmlspecialchars($_GET['id_enclos'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label>Nature du problème :</label>
                        <input type="text" name="nature" placeholder="Ex: Grillage sectionné" required>
                    </div>
                    <div>
                        <label>Type de panne :</label>
                        <select name="type_rep">
                            <option value="Technique">Technique</option>
                            <option value="Electrique">Electrique</option>
                        </select>
                    </div>
                    <button type="submit" name="creer_reparation" class="btn" style="height: 42px;">🚀 Envoyer la demande</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h3>📋 Historique des réparations</h3>
            <table>
                <thead>
                    <tr>
                        <th>N° Ticket</th>
                        <th>Enclos</th>
                        <th>Nature de l'intervention</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($repairs as $r): ?>
                    <tr>
                        <td><code>#<?php echo $r['num_reparation']; ?></code></td>
                        <td><strong>Enclos #<?php echo $r['id_enclos']; ?></strong></td>
                        <td><?php echo htmlspecialchars($r['nature_reparation']); ?></td>
                        <td>
                            <span class="badge-type <?php echo (strtolower($r['type_reparation']) == 'technique') ? 'type-technique' : 'type-electrique'; ?>">
                                <?php echo strtoupper($r['type_reparation']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($repairs)): ?>
                        <tr><td colspan="4" style="text-align:center;">Aucune réparation en cours.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
