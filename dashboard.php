<?php
session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est connecté et est un formateur
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'formateur') {
    // Rediriger vers la page d'accueil si ce n'est pas un formateur
    header('Location: index.php');
    exit();
}

// Récupérer les informations de l'utilisateur
$user = $_SESSION['utilisateur'];
$utilisateur_id = $user['id'];

// Récupérer le nombre de cours actifs
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cours WHERE formateur_id = ?"); 
$stmt->execute([$utilisateur_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row && isset($row['count'])) {
    $nb_cours = (int)$row['count'];
} else {
    $nb_cours = 0;
}

// Récupérer le nombre d'apprenants qui ont réservé mes cours
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT utilisateur_id) as count FROM reservations r 
JOIN cours c ON r.cours_id = c.id 
WHERE c.formateur_id = ?");
$stmt->execute([$utilisateur_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row && isset($row['count'])) {
    $nb_apprenants = (int)$row['count'];
} else {
    $nb_apprenants = 0;
}

// Traitement du formulaire de création de cours
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['creer_cours'])) {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $image = trim($_POST['image']); // URL de l'image
    $formateur_id = $user['id'];
    
    // Validation des champs
    if (empty($titre) || empty($description) || empty($image)) {
        $message = '<div class="alert alert-danger">Tous les champs sont obligatoires.</div>';
    } else {
        try {
            // Insertion dans la base de données
            $stmt = $pdo->prepare('INSERT INTO cours (titre, description, formateur_id, image) VALUES (?, ?, ?, ?)');
            $stmt->execute([$titre, $description, $formateur_id, $image]);
            
            $message = '<div class="alert alert-success">Cours créé avec succès!</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Erreur lors de la création du cours: ' . $e->getMessage() . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Formateur - EduLearn</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Bootstrap CSS pour le modal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background-color: #f1f5f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .welcome-message {
            font-size: 1.5rem;
            color: #007bff;
        }
        
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card i {
            font-size: 2rem;
            color: #007bff;
            margin-bottom: 10px;
        }
        
        .stat-card h3 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        
        .stat-card p {
            color: #666;
        }
        
        .dashboard-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .action-card {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .action-card h3 {
            color: #007bff;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .action-card h3 i {
            margin-right: 10px;
        }
        
        .action-card .btn-primary {
            margin-top: 15px;
        }
        
        .recent-activity {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .activity-item {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            background-color: #e6f2ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .activity-icon i {
            color: #007bff;
        }
        
        .activity-details h4 {
            margin: 0;
            font-size: 1rem;
        }
        
        .activity-details p {
            margin: 5px 0 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .logout-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            align-items: center;
        }
        
        .logout-btn i {
            margin-right: 5px;
        }
        
        /* Styles pour le modal */
        .modal-backdrop {
            background-color: rgba(0,0,0,0.5);
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="welcome-message">
                <h1>Bienvenue, <?php echo htmlspecialchars($user['nom']); ?> !</h1>
                <p>Voici un aperçu de votre activité</p>
            </div>
        </div>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <i class="fas fa-book"></i>
                <h3><?php echo $nb_cours; ?></h3>
                <p>Cours actifs</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3><?php echo $nb_apprenants; ?></h3>
                <p>Apprenants</p>
            </div>
            <!-- <div class="stat-card">
                <i class="fas fa-tasks"></i>
                <h3>12</h3>
                <p>Devoirs à corriger</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-comments"></i>
                <h3>8</h3>
                <p>Messages non lus</p>
            </div> -->
        </div>

        <div class="dashboard-actions">
            <div class="action-card">
                <h3><i class="fas fa-plus-circle"></i> Créer un nouveau cours</h3>
                <p>Créez et publiez un nouveau cours pour vos apprenants.</p>
                <button class="btn-primary" data-bs-toggle="modal" data-bs-target="#creerCoursModal">Créer un cours</button>
            </div>
            <!-- <div class="action-card">
                <h3><i class="fas fa-clipboard-check"></i> Évaluer les devoirs</h3>
                <p>Consultez et notez les devoirs soumis par vos apprenants.</p>
                <button class="btn-primary">Voir les devoirs</button>
            </div>
            <div class="action-card">
                <h3><i class="fas fa-chart-line"></i> Analyser les performances</h3>
                <p>Consultez les statistiques détaillées de vos cours et apprenants.</p>
                <button class="btn-primary">Voir les statistiques</button>
            </div> -->
        </div>


    </div>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-links">
                <a href="#">À propos</a>
                <a href="#">Contact</a>
                <a href="#">Mentions légales</a>
            </div>
            <p>&copy; 2025 EduLearn - Tous droits réservés</p>
        </div>
    </footer>
    
    <!-- Modal pour créer un cours -->
    <div class="modal fade" id="creerCoursModal" tabindex="-1" aria-labelledby="creerCoursModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="creerCoursModalLabel">Créer un nouveau cours</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <?php if (!empty($message)) echo $message; ?>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre du cours</label>
                            <input type="text" class="form-control" id="titre" name="titre" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">URL de l'image du cours</label>
                            <input type="url" class="form-control" id="image" name="image" placeholder="https://exemple.com/image.jpg" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" name="creer_cours" class="btn btn-primary">Créer le cours</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Afficher le modal si le formulaire a été soumis avec des erreurs
        <?php if (!empty($message) && strpos($message, 'alert-danger') !== false): ?>
        document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('creerCoursModal'));
            myModal.show();
        });
        <?php endif; ?>
    </script>
</body>
</html>
