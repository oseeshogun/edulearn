<?php
session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    $_SESSION['flash_message'] = [
        'message' => 'Vous devez être connecté pour annuler une réservation.',
        'type' => 'danger'
    ];
    header('Location: index.php');
    exit();
}

// Vérifier si l'ID de la réservation est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash_message'] = [
        'message' => 'Réservation invalide.',
        'type' => 'danger'
    ];
    header('Location: mes_reservations.php');
    exit();
}

$reservation_id = intval($_GET['id']);
$utilisateur_id = $_SESSION['utilisateur']['id'];

// Vérifier que la réservation appartient bien à l'utilisateur
$stmt = $pdo->prepare("SELECT id FROM reservations WHERE id = ? AND utilisateur_id = ?");
$stmt->execute([$reservation_id, $utilisateur_id]);
$reservation = $stmt->fetch();

if (!$reservation) {
    $_SESSION['flash_message'] = [
        'message' => 'Vous n\'êtes pas autorisé à annuler cette réservation.',
        'type' => 'danger'
    ];
    header('Location: mes_reservations.php');
    exit();
}

// Supprimer la réservation
try {
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->execute([$reservation_id]);
    
    $_SESSION['flash_message'] = [
        'message' => 'Votre réservation a été annulée avec succès.',
        'type' => 'success'
    ];
} catch (PDOException $e) {
    $_SESSION['flash_message'] = [
        'message' => 'Une erreur est survenue lors de l\'annulation: ' . $e->getMessage(),
        'type' => 'danger'
    ];
}

header('Location: mes_reservations.php');
exit();
?>
