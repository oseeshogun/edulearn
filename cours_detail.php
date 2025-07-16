<?php
session_start();
require_once 'config.php';

// Vérifier si l'ID du cours est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Rediriger vers la liste des cours si aucun ID valide n'est fourni
    header('Location: cours.php');
    exit();
}

$id = intval($_GET['id']);

// Récupérer les détails du cours
$stmt = $pdo->prepare("SELECT c.*, u.nom as formateur_nom 
                      FROM cours c 
                      LEFT JOIN utilisateurs u ON c.formateur_id = u.id 
                      WHERE c.id = ?");
$stmt->execute([$id]);
$cours = $stmt->fetch(PDO::FETCH_ASSOC);

// Si le cours n'existe pas, rediriger vers la liste des cours
if (!$cours) {
    $_SESSION['flash_message'] = [
        'message' => 'Ce cours n\'existe pas.',
        'type' => 'danger'
    ];
    header('Location: cours.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($cours['titre']); ?> - EduLearn</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .course-detail-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .course-header {
            display: flex;
            flex-direction: column;
            margin-bottom: 30px;
        }
        
        .course-title {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 10px;
        }
        
        .course-meta {
            display: flex;
            gap: 20px;
            color: #666;
            margin-bottom: 20px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .course-content {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        .course-image {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .course-description {
            line-height: 1.8;
            color: #444;
            font-size: 1.1rem;
        }
        
        .reservation-section {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin-top: 30px;
            text-align: center;
        }
        
        .reservation-title {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #333;
        }
        
        .reservation-button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .reservation-button:hover {
            background-color: #0056b3;
        }
        
        @media (max-width: 768px) {
            .course-header {
                flex-direction: column;
            }
            
            .course-title {
                font-size: 2rem;
            }
            
            .course-meta {
                flex-direction: column;
                gap: 10px;
            }
        }
        
        /* Styles pour les modals */
        .modal {
            display: flex;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }
        
        .modal.hidden {
            display: none;
        }
        
        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            position: relative;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        
        .close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .modal h2 {
            margin-top: 0;
            color: #333;
            margin-bottom: 20px;
        }
        
        .modal p {
            margin-bottom: 20px;
            line-height: 1.5;
        }
        
        .modal form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .modal button {
            margin-top: 10px;
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
    
    <div class="course-detail-container">
        <div class="course-header">
            <h1 class="course-title"><?php echo htmlspecialchars($cours['titre']); ?></h1>
            
            <div class="course-meta">
                <div class="meta-item">
                    <i class="fas fa-user-tie"></i>
                    <span>Formateur: <?php echo htmlspecialchars($cours['formateur_nom'] ?? 'Non spécifié'); ?></span>
                </div>
                <div class="meta-item">
                    <i class="far fa-calendar-alt"></i>
                    <span>Date: <?php echo date('d/m/Y', strtotime($cours['date_creation'])); ?></span>
                </div>
            </div>
        </div>
        
        <div class="course-content">
            <?php if (!empty($cours['image'])): ?>
            <img src="<?php echo htmlspecialchars($cours['image']); ?>" alt="<?php echo htmlspecialchars($cours['titre']); ?>" class="course-image">
            <?php endif; ?>
            
            <div class="course-description">
                <p><?php echo nl2br(htmlspecialchars($cours['description'])); ?></p>
            </div>
            
            <div class="reservation-section">
                <h2 class="reservation-title">Intéressé(e) par ce cours?</h2>
                <button class="reservation-button" onclick="openReservationModal()">
                    <i class="fas fa-bookmark"></i> Réserver ma place
                </button>
            </div>
            
            <!-- Modal de réservation -->
            <div id="reservation-modal" class="modal hidden">
                <div class="modal-content">
                    <span class="close" onclick="closeReservationModal()">&times;</span>
                    <h2>Réserver votre place</h2>
                    <p>Vous êtes sur le point de réserver une place pour le cours <strong><?php echo htmlspecialchars($cours['titre']); ?></strong>.</p>
                    
                    <?php if(isset($_SESSION['utilisateur'])): ?>
                        <!-- Formulaire de réservation -->
                        <form action="reserver_cours.php" method="post">
                            <input type="hidden" name="cours_id" value="<?php echo $id; ?>">
                            <p>Confirmez-vous votre réservation pour ce cours?</p>
                            <button type="submit" class="btn-primary">Confirmer ma réservation</button>
                        </form>
                    <?php else: ?>
                        <p>Vous devez être connecté pour réserver un cours.</p>
                        <button onclick="openModal('modal-login')" class="btn-primary">Se connecter</button>
                        <p>Vous n'avez pas de compte?</p>
                        <button onclick="openModal('modal-register')" class="btn-secondary">S'inscrire</button>
                    <?php endif; ?>
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
    <script>
        function openReservationModal() {
            document.getElementById('reservation-modal').classList.remove('hidden');
        }
        
        function closeReservationModal() {
            document.getElementById('reservation-modal').classList.add('hidden');
        }
        
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            // Si on ouvre un autre modal, on ferme celui de réservation
            closeReservationModal();
        }
        
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }
        
        // Fermer le modal si l'utilisateur clique en dehors
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
