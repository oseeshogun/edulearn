<?php
session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    $_SESSION['flash_message'] = [
        'message' => 'Vous devez être connecté pour voir vos réservations.',
        'type' => 'danger'
    ];
    header('Location: index.php');
    exit();
}

$utilisateur_id = $_SESSION['utilisateur']['id'];

// Récupérer les réservations de l'utilisateur avec les détails des cours
$stmt = $pdo->prepare("
    SELECT r.id as reservation_id, r.date_reservation, 
           c.id as cours_id, c.titre, c.description, c.image, c.date_creation,
           u.nom as formateur_nom
    FROM reservations r
    JOIN cours c ON r.cours_id = c.id
    LEFT JOIN utilisateurs u ON c.formateur_id = u.id
    WHERE r.utilisateur_id = ?
    ORDER BY r.date_reservation DESC
");
$stmt->execute([$utilisateur_id]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réservations - EduLearn</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .reservations-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .reservations-title {
            font-size: 2rem;
            margin-bottom: 30px;
            color: #333;
            text-align: center;
        }
        
        .reservation-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .reservation-card {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .reservation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        
        .reservation-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        
        .reservation-info {
            padding: 20px;
        }
        
        .reservation-info h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.4rem;
            color: #333;
        }
        
        .reservation-info p {
            margin: 10px 0;
            color: #666;
            line-height: 1.5;
        }
        
        .reservation-meta {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            color: #777;
            font-size: 0.9rem;
        }
        
        .reservation-date {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .reservation-formateur {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-secondary {
            display: inline-block;
            background-color: #007BFF;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 15px;
            transition: background-color 0.3s;
        }
        
        .btn-secondary:hover {
            background-color: #0056b3;
        }
        
        .btn-cancel {
            display: inline-block;
            background-color: #dc3545;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 15px;
            transition: background-color 0.3s;
        }
        
        .btn-cancel:hover {
            background-color: #c82333;
        }
        
        .no-reservations {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 1.2rem;
        }
        
        .buttons-container {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        
        @media (max-width: 768px) {
            .reservation-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="reservations-container">
        <h1 class="reservations-title">Mes Réservations</h1>
        
        <!-- Affichage des messages flash -->
        <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['flash_message']['type']; ?>">
            <?php 
                echo $_SESSION['flash_message']['message']; 
                unset($_SESSION['flash_message']); // Supprime le message après l'avoir affiché
            ?>
        </div>
        <?php endif; ?>
        
        <?php if (count($reservations) > 0): ?>
            <div class="reservation-list">
                <?php foreach ($reservations as $reservation): ?>
                    <div class="reservation-card">
                        <img src="<?php echo htmlspecialchars($reservation['image']); ?>" alt="<?php echo htmlspecialchars($reservation['titre']); ?>">
                        <div class="reservation-info">
                            <h3><?php echo htmlspecialchars($reservation['titre']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($reservation['description'], 0, 100)) . '...'; ?></p>
                            
                            <div class="reservation-meta">
                                <div class="reservation-date">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo date('d/m/Y', strtotime($reservation['date_reservation'])); ?>
                                </div>
                                <div class="reservation-formateur">
                                    <i class="fas fa-user"></i>
                                    <?php echo htmlspecialchars($reservation['formateur_nom']); ?>
                                </div>
                            </div>
                            
                            <div class="buttons-container">
                                <a href="cours_detail.php?id=<?php echo $reservation['cours_id']; ?>" class="btn-secondary">Voir le cours</a>
                                <a href="annuler_reservation.php?id=<?php echo $reservation['reservation_id']; ?>" class="btn-cancel" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation?');">Annuler</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-reservations">
                <p>Vous n'avez pas encore de réservations.</p>
                <a href="cours.php" class="btn-secondary">Découvrir nos cours</a>
            </div>
        <?php endif; ?>
    </div>
    
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-links">
                <a href="page apropos/apropos.php">À propos</a>
                <a href="#" id="opencontactModal">Contact</a>
                <a href="#">Mentions légales</a>
                <a href="#">FAQ</a>
            </div>
            <p>&copy; 2025 EduLearn - Tous droits réservés</p>
        </div>
    </footer>
</body>
</html>
