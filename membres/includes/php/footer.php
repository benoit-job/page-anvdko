<!-- Ajoutez cette balise pour créer un espace réservé au bas de votre contenu principal -->
<div class="footer-spacer" style="height: 20px;"></div>

<!-- Menu fixe bas -->
<nav class="menu_footer fixed-bottom bg-white border-top shadow-sm rounded-top-5">
  <div class="container d-flex justify-content-between py-1 px-2">

    <a href="accueil.php" class="text-center text-decoration-none text-dark flex-fill mx-1 menu-item">
      <div><i class="bi bi-house-door-fill fs-5 icon-accueil" style="color: #FF6B6B;"></i></div>
      <small style="font-size: 0.65rem;">Accueil</small>
    </a>

    <a href="evenements.php" class="text-center text-decoration-none text-dark flex-fill mx-1 menu-item">
      <div><i class="bi bi-calendar-event-fill fs-5 icon-evenements" style="color: #4ECDC4;"></i></div>
      <small style="font-size: 0.65rem;">Événements</small>
    </a>

    <a href="membres.php" class="text-center text-decoration-none text-dark flex-fill mx-1 menu-item">
      <div><i class="bi bi-people-fill fs-5 icon-membres" style="color: #45B7D1;"></i></div>
      <small style="font-size: 0.65rem;">Membres</small>
    </a>

    <a href="documents.php" class="text-center text-decoration-none text-dark flex-fill mx-1 menu-item">
      <div><i class="bi bi-folder-fill fs-5 icon-documents" style="color: #FFA07A;"></i></div>
      <small style="font-size: 0.65rem;">Documents</small>
    </a>

    <a href="profil.php?id=<?php echo htmlspecialchars(trim(crypt_decrypt_chaine($_SESSION['membre']['id'], 'C'))); ?>" class="text-center text-decoration-none text-dark flex-fill mx-1 menu-item">
      <div><i class="bi bi-person-circle fs-5 icon-profil" style="color: #9B59B6;"></i></div>
      <small style="font-size: 0.65rem;">Profil</small>
    </a>

  </div>
</nav>

<!-- CSS -->
<style>
  /* Ajoutez un padding-bottom au body pour éviter que le contenu ne soit caché */
  body {
    padding-bottom: 70px;
  }
  
  .menu_footer a {
    flex-grow: 1;
    transition: all 0.3s ease;
  }
  
  .menu-item:hover {
    transform: translateY(-5px);
  }
  
  .menu-item:hover .icon-accueil {
    color: #FF0000 !important;
    animation: bounce 0.5s;
  }
  
  .menu-item:hover .icon-evenements {
    color: #00AA99 !important;
    animation: pulse 0.5s;
  }
  
  .menu-item:hover .icon-membres {
    color: #0077CC !important;
    animation: swing 0.5s;
  }
  
  .menu-item:hover .icon-documents {
    color: #FF5500 !important;
    animation: tada 0.5s;
  }
  
  .menu-item:hover .icon-profil {
    color: #660099 !important;
    animation: rubberBand 0.5s;
  }
  
  @keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
  }
  
  @keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
  }
  
  @keyframes swing {
    20% { transform: rotate(15deg); }
    40% { transform: rotate(-10deg); }
    60% { transform: rotate(5deg); }
    80% { transform: rotate(-5deg); }
    100% { transform: rotate(0deg); }
  }
  
  @keyframes tada {
    0% { transform: scale(1); }
    10%, 20% { transform: scale(0.9) rotate(-3deg); }
    30%, 50%, 70%, 90% { transform: scale(1.1) rotate(3deg); }
    40%, 60%, 80% { transform: scale(1.1) rotate(-3deg); }
    100% { transform: scale(1) rotate(0); }
  }
  
  @keyframes rubberBand {
    0% { transform: scale(1); }
    30% { transform: scaleX(1.25) scaleY(0.75); }
    40% { transform: scaleX(0.75) scaleY(1.25); }
    60% { transform: scaleX(1.15) scaleY(0.85); }
    100% { transform: scale(1); }
  }
</style>

<!-- N'oublie pas d'inclure Bootstrap Icons pour les icônes -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />