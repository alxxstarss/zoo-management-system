<?php
// db.php
require_once 'myparam.inc.example.php';

$conn = oci_connect(MYUSER,MYPASS,MYHOST, 'AL32UTF8');

if (!$conn) {
    $e = oci_error();
    die("Erreur de connexion : " . htmlentities($e['message']));
}


/*Récupère TOUTES les lignes d'une requête (pour les boucles foreach)
Gère la préparation, l'exécution et la mise en minuscules des clés.*/
function oci_fetch_all_assoc($conn, $sql) {
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);
    $results = [];
    while ($row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS)) {
        // Force les clés en minuscules pour correspondre à ton code PHP
        $results[] = array_change_key_case($row, CASE_LOWER);
    }
    oci_free_statement($stmt);
    return $results;
}

/*

Récupère UNE SEULE ligne (pour les recherches par ID ou login)
@param array $params Tableau associatif des paramètres*/
function oci_fetch_one_assoc($conn, $sql, $params = []) {
    $stmt = oci_parse($conn, $sql);

    // Liaison dynamique des paramètres :id, :nom, etc.
    foreach ($params as $key => $val) {
        oci_bind_by_name($stmt, ":" . $key, $params[$key]);
    }

    oci_execute($stmt);
    $row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS);
    oci_free_statement($stmt);

    // Retourne la ligne en minuscules ou false si rien n'est trouvé
    return $row ? array_change_key_case($row, CASE_LOWER) : false;
}
/**
 * Récupère tous les résultats d'un statement OCI8 
 * et convertit les clés (noms des colonnes) en minuscules.
 * * @param resource $stmt Le statement Oracle déjà exécuté
 * @return array Un tableau multidimensionnel
 */
function oci_fetch_all_lower($stmt) {
    $results = [];
    
    // OCI_ASSOC : Récupère sous forme de tableau associatif
    // OCI_RETURN_NULLS : Force la création de la clé même si la valeur est vide
    while ($row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS)) {
        // array_change_key_case transforme 'NOM_VISITEUR' en 'nom_visiteur'
        $results[] = array_change_key_case($row, CASE_LOWER);
    }
    
    return $results;
}
?>
