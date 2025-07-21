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
    // Récupération et nettoyage des données du formulaire
    $nom = trim($_POST['nom']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $mot_de_passe = $_POST['mot_de_passe'];
    $role = $_POST['role'];

    // Validation des données
    if (empty($nom) || empty($email) || empty($mot_de_passe) || empty($role)) {
        redirect_with_message('index.php', 'Veuillez remplir tous les champs.', 'danger');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirect_with_message('index.php', 'L\'adresse email n\'est pas valide.', 'danger');
    } else {
        // Vérifier si l'email existe déjà
        $check_email = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = ?");
        $check_email->execute([$email]);
        $email_exists = $check_email->fetchColumn() > 0;

        if ($email_exists) {
            redirect_with_message('index.php', 'Cette adresse email est déjà utilisée. Veuillez vous connecter ou utiliser une autre adresse.', 'warning');
        } else {
            // Hashage du mot de passe
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            // Insertion de l'utilisateur
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_pass, role) VALUES (?, ?, ?, ?)");

            try {
                $stmt->execute([$nom, $email, $hash, $role]);
                
                // Création automatique de la session pour l'utilisateur nouvellement inscrit
                $user_id = $pdo->lastInsertId();
                $_SESSION['utilisateur'] = [
                    'id' => $user_id,
                    'nom' => $nom,
                    'email' => $email,
                    'role' => $role
                ];
                
                // Redirection selon le rôle
                if ($role == 'formateur') {
                    redirect_with_message('dashboard.php', 'Inscription réussie ! Bienvenue ' . htmlspecialchars($nom) . ' !', 'success');
                } else {
                    redirect_with_message('cours.php', 'Inscription réussie ! Bienvenue ' . htmlspecialchars($nom) . ' !', 'success');
                }
            } catch (PDOException $e) {
                redirect_with_message('index.php', 'Erreur lors de l\'inscription : ' . $e->getMessage(), 'danger');
            }
        }
    }
}
?>