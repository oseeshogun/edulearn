<?php
$host = 'db'; // Nom du service MySQL dans docker-compose.yml
$dbname = 'edulearn';
$user = 'eduuser'; // Utilisateur défini dans docker-compose.yml
$pass = 'edupassword'; // Mot de passe défini dans docker-compose.yml

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>