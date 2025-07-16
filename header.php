<?php
// Vérifier si l'utilisateur est connecté
$is_logged_in = isset($_SESSION['utilisateur']);
$user = $is_logged_in ? $_SESSION['utilisateur'] : null;
?>

<header class="site-header">
  <div class="logo"> 
    <i class="fa-sharp fa-solid fa-graduation-cap fa-flip-horizontal" style="color: #007bff;"></i> 
    <span class="text">EduLearn</span>
  </div>
  
  <button class="menu-toggle" aria-label="Menu">&#9776;</button>
  
  <nav class="main-nav">
    <a href="index.php">Accueil</a>
    <a href="cours.php">Cours</a>
    
    <?php if ($is_logged_in): ?>
    <a href="mes_reservations.php">Mes Réservations</a>
    <?php endif; ?>
    
    <?php if ($is_logged_in && $user['role'] == 'formateur'): ?>
    <a href="formateur/dashboard.php">Tableau de bord</a>
    <?php endif; ?>
    
    <a href="page apropos/apropos.php">À propos</a>
    <a id="openModalBtn" onclick="openModal()">Contact</a>
  </nav>
  
  <div class="auth-buttons">
    <?php if ($is_logged_in): ?>
      <div class="user-menu">
        <span class="user-name">Bonjour, <?php echo htmlspecialchars($user['nom']); ?></span>
        <a href="logout.php" class="btn-logout">
          <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
      </div>
    <?php else: ?>
      <button class="btn-login" onclick="openModal('modal-login')">
        <i class="fas fa-sign-in-alt"></i> Connexion
      </button>
      <button class="btn-register" onclick="openModal('modal-register')">
        <i class="fas fa-user-plus"></i> Inscription
      </button>
    <?php endif; ?>
  </div>
</header>

<!-- MODALE CONNEXION -->
<div id="modal-login" class="modal hidden">
  <div class="modal-content">
    <span class="close" onclick="closeModal('modal-login')">&times;</span>
    <h2>Connexion</h2>
    <form action="connexion.php" method="post">
      <input type="email" name="email" placeholder="Adresse email" required>
      <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
      <button type="submit">Se connecter</button>
    </form>
  </div>
</div>

<!-- MODALE INSCRIPTION -->
<div id="modal-register" class="modal hidden">
  <div class="modal-content">
    <span class="close" onclick="closeModal('modal-register')">&times;</span>
    <h2>Inscription</h2>
    <form action="inscription.php" method="post">
      <input type="text" name="nom" placeholder="Nom complet" required>
      <input type="email" name="email" placeholder="Adresse email" required>
      <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
      <label for="role">Je suis :</label>
      <select id="role" name="role" required>
        <option value="" disabled selected>Choisissez votre rôle</option>
        <option value="apprenant">Apprenant</option>
        <option value="formateur">Formateur</option>
      </select>
      <button type="submit">Créer mon compte</button>
    </form>
  </div>
</div>

<script>
  function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
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