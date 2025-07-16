<?php
session_start();

// Vérifier si l'utilisateur est connecté et est un formateur
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'formateur') {
    // Rediriger vers la page d'accueil si ce n'est pas un formateur
    header('Location: ../index.php');
    exit();
}

// Récupérer les informations de l'utilisateur
$user = $_SESSION['utilisateur'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Formateur - EduLearn</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
    </style>
</head>
<body>
    <header class="site-header">
        <div class="logo">EduLearn</div>
        <nav class="main-nav">
            <a href="../index.php">Accueil</a>
            <a href="#" class="active">Tableau de bord</a>
            <a href="#">Mes cours</a>
            <a href="#">Calendrier</a>
            <a href="#">Messages</a>
        </nav>
        <div class="auth-buttons">
            <a href="../index.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </div>
    </header>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="welcome-message">
                <h1>Bienvenue, <?php echo htmlspecialchars($user['nom']); ?> !</h1>
                <p>Voici votre tableau de bord formateur</p>
            </div>
        </div>

        <div class="dashboard-stats">
            <div class="stat-card">
                <i class="fas fa-book"></i>
                <h3>5</h3>
                <p>Cours actifs</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3>42</h3>
                <p>Apprenants</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-tasks"></i>
                <h3>12</h3>
                <p>Devoirs à corriger</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-comments"></i>
                <h3>8</h3>
                <p>Messages non lus</p>
            </div>
        </div>

        <div class="dashboard-actions">
            <div class="action-card">
                <h3><i class="fas fa-plus-circle"></i> Créer un nouveau cours</h3>
                <p>Créez et publiez un nouveau cours pour vos apprenants.</p>
                <button class="btn-primary">Créer un cours</button>
            </div>
            <div class="action-card">
                <h3><i class="fas fa-clipboard-check"></i> Évaluer les devoirs</h3>
                <p>Consultez et notez les devoirs soumis par vos apprenants.</p>
                <button class="btn-primary">Voir les devoirs</button>
            </div>
            <div class="action-card">
                <h3><i class="fas fa-chart-line"></i> Analyser les performances</h3>
                <p>Consultez les statistiques détaillées de vos cours et apprenants.</p>
                <button class="btn-primary">Voir les statistiques</button>
            </div>
        </div>

        <div class="recent-activity">
            <h2>Activité récente</h2>
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="activity-details">
                    <h4>Nouvel apprenant inscrit</h4>
                    <p>Sophie Martin s'est inscrite à votre cours "Introduction au développement web"</p>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="activity-details">
                    <h4>Devoir soumis</h4>
                    <p>Thomas Dubois a soumis le devoir "Création d'une page web responsive"</p>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-comment"></i>
                </div>
                <div class="activity-details">
                    <h4>Nouveau commentaire</h4>
                    <p>Julie Lefèvre a posé une question dans le forum du cours "Bases de données SQL"</p>
                </div>
            </div>
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
</body>
</html>
