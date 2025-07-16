<?php
session_start();

// Détruire toutes les variables de session
$_SESSION = array();

// Détruire la session
session_destroy();

// Rediriger vers la page d'accueil avec un message
session_start(); // Redémarrer la session pour pouvoir définir un message flash
$_SESSION['flash_message'] = [
    'message' => 'Vous avez été déconnecté avec succès.',
    'type' => 'info'
];

// Rediriger vers la page d'accueil
header('Location: index.php');
exit();
?>
