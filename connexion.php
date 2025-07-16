<?php
require_once 'config.php';
session_start();

// Fonction pour rediriger avec un message
function redirect_with_message($url, $message, $type = 'success') {
    $_SESSION['flash_message'] = [
        'message' => $message,
        'type' => $type
    ];
    header("Location: $url");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération et nettoyage des données
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $mot_de_passe = $_POST['mot_de_passe'];

    // Validation des données
    if (empty($email) || empty($mot_de_passe)) {
        redirect_with_message('index.php', 'Veuillez remplir tous les champs.', 'danger');
    }

    // Vérification des identifiants
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($mot_de_passe, $user['mot_de_pass'])) {
        // Création de la session utilisateur
        $_SESSION['utilisateur'] = [
            'id' => $user['id'],
            'nom' => $user['nom'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        
        // Redirection selon le rôle
        if ($user['role'] == 'formateur') {
            redirect_with_message('formateur/dashboard.php', 'Bienvenue ' . htmlspecialchars($user['nom']) . ' !', 'success');
        } else {
            redirect_with_message('index.php', 'Bienvenue ' . htmlspecialchars($user['nom']) . ' !', 'success');
        }
    } else {
        redirect_with_message('index.php', 'Email ou mot de passe incorrect.', 'danger');
    }
}
?>





