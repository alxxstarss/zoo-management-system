<?php
// logout.php
// Déconnecte l'utilisateur en terminant la session
// puis renvoie vers la page de connexion.
session_start();
session_destroy();
header('Location: index.php');
exit();
?>
