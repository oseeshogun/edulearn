<?php
session_start();
require_once 'config.php';

// Vérifier si les données de cours existent, sinon les créer
$stmt = $pdo->query("SELECT COUNT(*) FROM cours");
if ($stmt->fetchColumn() == 0) {
    // Rediriger vers mock_data.php pour créer des données fictives
    header("Location: mock_data.php?redirect=cours.php");
    exit();
}

// Traitement de la recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$query = "SELECT c.*, u.nom as formateur_nom 
          FROM cours c 
          LEFT JOIN utilisateurs u ON c.formateur_id = u.id";

// Ajouter la condition de recherche si un terme est fourni
if (!empty($search)) {
    $query .= " WHERE c.titre LIKE :search OR c.description LIKE :search";
}

// Ajouter l'ordre de tri
$query .= " ORDER BY c.date_creation DESC";

$stmt = $pdo->prepare($query);

// Lier le paramètre de recherche si nécessaire
if (!empty($search)) {
    $searchTerm = "%$search%";
    $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
}

$stmt->execute();
$cours = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cours - EduLearn</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .courses-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        
        .search-container {
            margin: 30px 0;
            display: flex;
            justify-content: center;
        }
        
        .search-form {
            width: 100%;
            max-width: 600px;
            display: flex;
        }
        
        .search-input {
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-right: none;
            border-radius: 4px 0 0 4px;
            font-size: 16px;
        }
        
        .search-button {
            padding: 12px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }
        
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .course-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .course-header {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }
        
        .course-title {
            margin: 0;
            font-size: 1.3rem;
            color: #007bff;
        }
        
        .course-content {
            padding: 20px;
        }
        
        .course-description {
            color: #555;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .course-meta {
            display: flex;
            justify-content: space-between;
            color: #777;
            font-size: 0.9rem;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        
        .course-formateur {
            display: flex;
            align-items: center;
        }
        
        .formateur-icon {
            margin-right: 5px;
            color: #007bff;
        }
        
        .course-date {
            display: flex;
            align-items: center;
        }
        
        .date-icon {
            margin-right: 5px;
            color: #28a745;
        }
        
        .no-results {
            text-align: center;
            padding: 50px 0;
            color: #777;
        }
        
        .view-button {
            display: inline-block;
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        
        .view-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <!-- Affichage des messages flash -->
    <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['flash_message']['type']; ?>">
        <?php 
            echo $_SESSION['flash_message']['message']; 
            unset($_SESSION['flash_message']); // Supprime le message après l'avoir affiché
        ?>
    </div>
    <?php endif; ?>
    
    <div class="courses-container">
        <h1>Catalogue de cours</h1>
        
        <div class="search-container">
            <form class="search-form" action="cours.php" method="GET">
                <input type="text" name="search" class="search-input" placeholder="Rechercher un cours..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        
        <?php if (count($cours) > 0): ?>
            <div class="courses-grid">
                <?php foreach ($cours as $c): ?>
                    <div class="course-card">
                        <?php if (!empty($c['image'])): ?>
                        <div class="course-image-container" style="height: 180px; overflow: hidden;">
                            <img src="<?php echo htmlspecialchars($c['image']); ?>" alt="<?php echo htmlspecialchars($c['titre']); ?>" 
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <?php endif; ?>
                        <div class="course-header">
                            <h2 class="course-title"><?php echo htmlspecialchars($c['titre']); ?></h2>
                        </div>
                        <div class="course-content">
                            <p class="course-description">
                                <?php 
                                    // Limiter la description à 150 caractères
                                    echo strlen($c['description']) > 150 ? 
                                        htmlspecialchars(substr($c['description'], 0, 150)) . '...' : 
                                        htmlspecialchars($c['description']); 
                                ?>
                            </p>
                            <a href="cours_detail.php?id=<?php echo $c['id']; ?>" class="view-button">Voir le cours</a>
                            
                            <div class="course-meta">
                                <div class="course-formateur">
                                    <i class="fas fa-user-tie formateur-icon"></i>
                                    <?php echo htmlspecialchars($c['formateur_nom'] ?? 'Formateur inconnu'); ?>
                                </div>
                                <div class="course-date">
                                    <i class="far fa-calendar-alt date-icon"></i>
                                    <?php echo date('d/m/Y', strtotime($c['date_creation'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <i class="fas fa-search fa-3x" style="color: #ddd; margin-bottom: 15px;"></i>
                <h2>Aucun cours trouvé</h2>
                <p>Essayez avec d'autres termes de recherche ou consultez tous les cours disponibles.</p>
                <?php if (!empty($search)): ?>
                    <a href="cours.php" class="view-button">Voir tous les cours</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
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
