<?php
session_start(); // Démarre la session pour gérer l'utilisateur connecté
require_once 'config.php'; // Inclure la configuration de la base de données
// La connexion est déjà établie dans config.php avec la variable $pdo
// Récupérer les 3 derniers cours de la base de données
$stmt = $pdo->query('SELECT * FROM cours ORDER BY id DESC LIMIT 3');
$cours = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EduLearn</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <!-- Séquence de chargement -->
  <div id="loader-wrapper">
    <div class="spinner"></div>
    <p class="loader-text">Chargement EduLearn...</p>
  </div>

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

  <!-- Hero Section (version B - split image/texte) -->
  <section class="hero hero-split">
    <div class="hero-text">
      <h1>Apprenez. Progressez. Réussissez.</h1>
      <p>Rejoignez EduLearn et accédez à des centaines de cours pour développer vos compétences et réussir vos objectifs.</p>
      <button class="btn-primary">Explorer les cours</button>
    </div>
    <div class="hero-image">
      <img src="images/quiz-image.png" alt="quiz-image" id="image1">

    </div>
  </section>

  <!-- Fonctionnalités clés -->
  <section class="features">
    <div class="container">
      <h2 class="section-title">Pourquoi choisir EduLearn ?</h2>
      <div class="features-grid">
        <div class="feature-card">
          <i class="fas fa-laptop-code feature-icon"></i>
          <h3>Cours en ligne variés</h3>
          <p>Explorez des dizaines de thématiques : développement, design, langues et bien plus.</p>
        </div>
        <div class="feature-card">
          <i class="fas fa-user-check feature-icon"></i>
          <h3>Suivi personnalisé</h3>
          <p>Progressez à votre rythme avec un tableau de bord intuitif et vos statistiques.</p>
        </div>
        <div class="feature-card">
          <i class="fas fa-chalkboard-teacher feature-icon"></i>
          <h3>Formateurs experts</h3>
          <p>Apprenez auprès de professionnels qualifiés et passionnés.</p>
        </div>
        <div class="feature-card">
          <i class="fas fa-certificate feature-icon"></i>
          <h3>Certificats de réussite</h3>
          <p>Validez vos compétences et valorisez-les sur votre CV.</p>
        </div>
        <div class="feature-card">
          <i class="fas fa-video feature-icon"></i>
          <h3>Cours en direct et enregistrés</h3>
          <p>Accédez à des vidéos en direct et à la demande selon votre disponibilité.</p>
        </div>
        <div class="feature-card">
          <i class="fas fa-folder-open feature-icon"></i>
          <h3>Ressources variées</h3>
          <p>PDF, PowerPoint, quiz interactifs, tous les supports sont disponibles.</p>
        </div>
        <div class="feature-card">
          <i class="fas fa-comments feature-icon"></i>
          <h3>Messagerie intégrée</h3>
          <p>Communiquez facilement avec vos formateurs et vos camarades.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Cours populaires -->
  <section class="popular-courses">
    <h2>Nos cours les plus populaires</h2>
    <div class="courses-grid">
      <?php if (!empty($cours)): ?>
        <?php foreach ($cours as $course): ?>
          <div class="course-card">
            <img src="<?php echo htmlspecialchars($course['image']); ?>" alt="<?php echo htmlspecialchars($course['titre']); ?>">
            <div class="course-info">
              <h3><?php echo htmlspecialchars($course['titre']); ?></h3>
              <p><?php echo htmlspecialchars($course['description']); ?></p>
              <a href="cours_detail.php?id=<?php echo $course['id']; ?>" class="btn-secondary">Voir le cours</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Aucun cours disponible pour le moment.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Témoignages -->
  <section class="testimonials">
    <h2>Ce que disent nos apprenants</h2>
    <div class="testimonial-grid">
      <div class="testimonial">
        <p>"EduLearn m'a permis de décrocher mon premier job dans la tech. Merci !"</p>
        <div class="author">
          <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Utilisateur 1">
          <span>Marc D., Développeur junior</span>
        </div>
      </div>

      <div class="testimonial">
        <p>"J'ai adoré la clarté des cours et le suivi personnalisé."</p>
        <div class="author">
          <img src="https://randomuser.me/api/portraits/women/45.jpg" alt="Utilisateur 2">
          <span>Sophie L., UX Designer</span>
        </div>
      </div>

      <div class="testimonial">
        <p>"La plateforme est intuitive et les certificats valorisent vraiment mon CV."</p>
        <div class="author">
          <img src="https://randomuser.me/api/portraits/men/67.jpg" alt="Utilisateur 3">
          <span>Ali B., Étudiant en reconversion</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Call To Action final -->
  <section class="cta-final">
    <h2>Prêt(e) à commencer votre apprentissage ?</h2>
    <p>Rejoignez des milliers d'apprenants et développez vos compétences dès aujourd'hui.</p>
    <a href="#" class="cta-button" onclick="openModal('modal-register')">Commencez maintenant</a>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-content">
      <div class="footer-links">
        <a href="apropos.php ">À propos</a>
        <a href="#" id="opencontactModal">Contact</a>

        <a href="#!">Mentions légales</a>
        <a href="#!">FAQ</a>
      </div>
      <div class="text-light">Suivez-nous :
        <a href="#!" aria-label="Facebook">Facebook</a>
        <a href="#!" aria-label="Twitter">Twitter</a>
        <a href="#!" aria-label="LinkedIn">LinkedIn</a>
      </div>
      <p class="copyright">&copy; 2025 EduLearn. Tous droits réservés.</p>
    </div>
  </footer>

  <!-- Les modales de connexion et d'inscription ont été déplacées vers header.php -->

  <script>
    const menuToggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('.main-nav');
    const buttons = document.querySelector('.auth-buttons');
    menuToggle.addEventListener('click', () => {
      nav.classList.toggle('show');
      buttons.classList.toggle('show');
    });
  </script>

  <script>
    const loader = document.getElementById("loader-wrapper");
    const startTime = Date.now();

    // Détermine le temps minimum selon la qualité du réseau
    let minDisplayTime = 2000;
    const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;

    if (connection) {
      const type = connection.effectiveType;
      if (type === "slow-2g" || type === "2g") {
        minDisplayTime = 4000;
      } else if (type === "3g") {
        minDisplayTime = 3000;
      }
    }
    window.addEventListener("load", function() {
      const elapsed = Date.now() - startTime;
      const remainingTime = Math.max(0, minDisplayTime - elapsed);

      setTimeout(() => {
        loader.style.transition = "opacity 0.5s ease";
        loader.style.opacity = "0";
        setTimeout(() => {
          loader.style.display = "none";
        }, 500);
      }, remainingTime);
    });
  </script>

  <script>
    document.getElementById('opencontactModal').addEventListener('click', function(event) {
      event.preventDefault(); // Empêche le comportement par défaut du lien


      // Crée un iframe pour charger la modal depuis la page "À propos"
      var iframe = document.createElement('iframe');
      iframe.src = 'C:\Users\MOMBO TSASA JOSUE\Pictures\projet_Edulearn\page apropos\apropos.html'; // Charge la page À propos (ou la modal)
      iframe.style.width = '100%';
      iframe.style.height = '100%';
      iframe.style.border = 'none';
      iframe.style.position = 'fixed';
      iframe.style.top = '0';
      iframe.style.left = '0';
      iframe.style.zIndex = '9999';
      document.body.appendChild(iframe);

      // Accède à la modal dans l'iframe et l'ouvre
      iframe.onload = function() {
        var iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
        var modal = iframeDocument.getElementById('contactModal');
        modal.style.display = 'block';
      };
    });
  </script>


</body>

</html