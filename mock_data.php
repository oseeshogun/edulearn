<?php
require_once 'config.php';

// Vérifier si la table cours existe
$tableExists = false;
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'cours'");
    $tableExists = $stmt->rowCount() > 0;
} catch (PDOException $e) {
    die("Erreur lors de la vérification de la table: " . $e->getMessage());
}

// Si la table n'existe pas, la créer
if (!$tableExists) {
    try {
        $pdo->exec("CREATE TABLE cours (
            id INT PRIMARY KEY AUTO_INCREMENT,
            titre VARCHAR(255) NOT NULL,
            description TEXT,
            image VARCHAR(255),
            date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
            formateur_id INT,
            FOREIGN KEY (formateur_id) REFERENCES utilisateurs(id)
        )");
        echo "Table 'cours' créée avec succès.<br>";
    } catch (PDOException $e) {
        die("Erreur lors de la création de la table: " . $e->getMessage());
    }
}

// Vérifier si la table cours est vide
$isEmpty = true;
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM cours");
    $count = $stmt->fetchColumn();
    $isEmpty = ($count == 0);
} catch (PDOException $e) {
    die("Erreur lors de la vérification des données: " . $e->getMessage());
}

// Si la table est vide, ajouter des données fictives
if ($isEmpty) {
    // Récupérer les IDs des formateurs
    $formateurIds = [];
    try {
        $stmt = $pdo->query("SELECT id FROM utilisateurs WHERE role = 'formateur'");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $formateurIds[] = $row['id'];
        }
    } catch (PDOException $e) {
        // Si aucun formateur n'existe, créer un formateur par défaut
        try {
            $hash = password_hash('password123', PASSWORD_DEFAULT);
            $pdo->exec("INSERT INTO utilisateurs (nom, email, mot_de_pass, role) 
                       VALUES ('Formateur Défaut', 'formateur@edulearn.com', '$hash', 'formateur')");
            $formateurIds[] = $pdo->lastInsertId();
            echo "Formateur par défaut créé.<br>";
        } catch (PDOException $e2) {
            die("Erreur lors de la création du formateur par défaut: " . $e2->getMessage());
        }
    }

    // Si aucun formateur n'a été trouvé ou créé, arrêter
    if (empty($formateurIds)) {
        die("Impossible de créer des cours sans formateurs.");
    }

    // Données fictives pour les cours
    $mockCourses = [
        [
            'titre' => 'Développement Web',
            'description' => 'Apprenez HTML, CSS, JavaScript et créez vos propres sites !',
            'image' => 'https://images.pexels.com/photos/270348/pexels-photo-270348.jpeg?auto=compress&cs=tinysrgb&w=600'
        ],
        [
            'titre' => 'Santé',
            'description' => 'Renforcez vos connaissances en santé, apprenez à prévenir les maladies et devenez un acteur engagé du bien-être autour de vous.',
            'image' => 'https://images.pexels.com/photos/3938022/pexels-photo-3938022.jpeg'
        ],
        [
            'titre' => 'Agriculture',
            'description' => 'Renforcez vos connaissances en agriculture, apprenez les dernières techniques et devenez un acteur engagé de la transition écologique.',
            'image' => 'https://images.pexels.com/photos/4505161/pexels-photo-4505161.jpeg'
        ],
        [
            'titre' => 'Introduction au Développement Web',
            'description' => 'Apprenez les bases du développement web avec HTML, CSS et JavaScript. Ce cours est parfait pour les débutants qui souhaitent se lancer dans le développement web.',
            'image' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80'
        ],
        [
            'titre' => 'PHP pour les Débutants',
            'description' => 'Découvrez le langage PHP et comment créer des sites web dynamiques. Vous apprendrez à manipuler des formulaires, interagir avec une base de données et créer des sessions utilisateur.',
            'image' => 'https://images.unsplash.com/photo-1599507593499-a3f7d7d97667?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80'
        ],
        [
            'titre' => 'Bases de Données MySQL',
            'description' => 'Maîtrisez les concepts fondamentaux des bases de données relationnelles avec MySQL. Apprenez à concevoir des schémas de base de données efficaces et à écrire des requêtes SQL complexes.',
            'image' => 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80'
        ],
        [
            'titre' => 'Développement Mobile avec React Native',
            'description' => 'Créez des applications mobiles natives pour iOS et Android avec un seul code source en utilisant React Native. Ce cours couvre les fondamentaux jusqu\'aux techniques avancées.',
            'image' => 'https://images.unsplash.com/photo-1551650975-87deedd944c3?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80'
        ],
        [
            'titre' => 'Intelligence Artificielle: Concepts et Applications',
            'description' => 'Explorez le monde fascinant de l\'intelligence artificielle. Ce cours couvre les algorithmes d\'apprentissage automatique, les réseaux de neurones et les applications pratiques de l\'IA.',
            'image' => 'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80'
        ],
        [
            'titre' => 'Cybersécurité pour Développeurs',
            'description' => 'Apprenez à sécuriser vos applications web contre les vulnérabilités courantes. Ce cours couvre les injections SQL, XSS, CSRF et les bonnes pratiques de sécurité.',
            'image' => 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80'
        ],
        [
            'titre' => 'DevOps et Intégration Continue',
            'description' => 'Maîtrisez les outils et pratiques DevOps pour améliorer la collaboration et l\'efficacité dans le développement logiciel. Apprenez à utiliser Docker, Jenkins et Git.',
            'image' => 'https://images.unsplash.com/photo-1607799279861-4dd421887fb3?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80'
        ],
        [
            'titre' => 'Data Science avec Python',
            'description' => 'Plongez dans l\'analyse de données avec Python. Ce cours couvre les bibliothèques NumPy, Pandas, Matplotlib et les techniques d\'analyse statistique.',
            'image' => 'https://images.unsplash.com/photo-1527474305487-b87b222841cc?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80'
        ]
    ];

    // Insérer les cours dans la base de données
    try {
        $stmt = $pdo->prepare("INSERT INTO cours (titre, description, image, formateur_id) VALUES (?, ?, ?, ?)");
        
        foreach ($mockCourses as $course) {
            // Sélectionner aléatoirement un formateur
            $formateurId = $formateurIds[array_rand($formateurIds)];
            
            $stmt->execute([
                $course['titre'],
                $course['description'],
                $course['image'],
                $formateurId
            ]);
        }
        
        echo "Données fictives ajoutées avec succès à la table 'cours'.<br>";
    } catch (PDOException $e) {
        die("Erreur lors de l'ajout des données fictives: " . $e->getMessage());
    }
} else {
    echo "La table 'cours' contient déjà des données.<br>";
}

// Vérifier si une redirection est demandée
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'cours.php';

// Ajouter un message flash pour informer l'utilisateur
session_start();
$_SESSION['flash_message'] = [
    'message' => 'Des données de cours fictives ont été créées avec succès.',
    'type' => 'success'
];

// Rediriger vers la page spécifiée
header("Location: $redirect");
exit();
?>
