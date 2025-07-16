<?php
session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    $_SESSION['flash_message'] = [
        'message' => 'Vous devez être connecté pour réserver un cours.',
        'type' => 'danger'
    ];
    header('Location: index.php');
    exit();
}

// Vérifier si l'ID du cours est fourni
if (!isset($_POST['cours_id']) || !is_numeric($_POST['cours_id'])) {
    $_SESSION['flash_message'] = [
        'message' => 'Cours invalide.',
        'type' => 'danger'
    ];
    header('Location: cours.php');
    exit();
}

$utilisateur_id = $_SESSION['utilisateur']['id'];
$cours_id = intval($_POST['cours_id']);

// Vérifier si le cours existe
$stmt = $pdo->prepare("SELECT id FROM cours WHERE id = ?");
$stmt->execute([$cours_id]);
$cours = $stmt->fetch();

if (!$cours) {
    $_SESSION['flash_message'] = [
        'message' => 'Ce cours n\'existe pas.',
        'type' => 'danger'
    ];
    header('Location: cours.php');
    exit();
}

// Vérifier si l'utilisateur a déjà réservé ce cours
$stmt = $pdo->prepare("SELECT id FROM reservations WHERE utilisateur_id = ? AND cours_id = ?");
$stmt->execute([$utilisateur_id, $cours_id]);
$reservation_existante = $stmt->fetch();

if ($reservation_existante) {
    $_SESSION['flash_message'] = [
        'message' => 'Vous avez déjà réservé ce cours.',
        'type' => 'info'
    ];
    header('Location: cours_detail.php?id=' . $cours_id);
    exit();
}

// Créer la réservation
try {
    $stmt = $pdo->prepare("INSERT INTO reservations (utilisateur_id, cours_id) VALUES (?, ?)");
    $stmt->execute([$utilisateur_id, $cours_id]);
    
    $_SESSION['flash_message'] = [
        'message' => 'Votre réservation a été enregistrée avec succès!',
        'type' => 'success'
    ];
    header('Location: cours_detail.php?id=' . $cours_id);
    exit();
} catch (PDOException $e) {
    $_SESSION['flash_message'] = [
        'message' => 'Une erreur est survenue lors de la réservation: ' . $e->getMessage(),
        'type' => 'danger'
    ];
    header('Location: cours_detail.php?id=' . $cours_id);
    exit();
}
?>
