<nav class="navbar navbar-vertical navbar-expand-lg"> 
  <script>
    var navbarStyle = window.config.config.phoenixNavbarStyle;
    if (navbarStyle && navbarStyle !== 'transparent') {
      document.querySelector('body').classList.add(`navbar-${navbarStyle}`);
    }
  </script>

  <style>
    /* Styles pour les couleurs des icônes */
    .nav-link-icon span[data-feather="home"] { color: #4e73df; }
    .nav-link-icon span[data-feather="tag"] { color: #1cc88a; }
    .nav-link-icon span[data-feather="file-text"] { color: #36b9cc; }
    .nav-link-icon span[data-feather="calendar"] { color: #f6c23e; }
    .nav-link-icon span[data-feather="tv"] { color: #e74a3b; }
    .nav-link-icon span[data-feather="settings"] { color: #858796; }
    .nav-link-icon span[data-feather="users"] { color: #5a5c69; }
    .nav-link-icon span[data-feather="gift"] { color: #e83e8c; }
    .nav-link-icon span[data-feather="briefcase"] { color: #6f42c1; }
    
    /* Animation au survol */
    .nav-item-wrapper a.nav-link:hover .nav-link-icon span {
      animation: bounce 0.5s ease;
      display: inline-block;
    }
    
    /* Animation de rebond */
    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-5px); }
    }
    
    /* Transition douce pour la couleur */
    .nav-link-icon span {
      transition: color 0.3s ease, transform 0.3s ease;
    }
    
    .nav-item-wrapper a.nav-link:hover .nav-link-icon span {
      transform: scale(1.1);
    }
    
    /* Animation pour les éléments déroulants */
    .parent-wrapper .parent {
      transition: max-height 0.3s ease;
    }
  </style>

  <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
    <!-- scrollbar removed-->
    <div class="navbar-vertical-content">
      <ul class="navbar-nav flex-column" id="navbarVerticalNav">
      <?php
      $secretaire = isset($_SESSION['utilisateur']['secretaire']) ? $_SESSION['utilisateur']['secretaire'] : '';
      ?>

<!-- Gestions -->
<?php if ($secretaire === "Oui"): ?>
<!-- Catégories Actualités -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="adherents.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="users"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Gestion membres</span></span>
      </div>
    </a>
  </div>
</li>

<!-- Catégories Actualités -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="gestions_dons.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="gift"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Gestion dons</span></span>
      </div>
    </a>
  </div>
</li>

<?php elseif ($secretaire === "Non"): ?>

<!-- Accueil -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="accueil.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="home"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Accueil</span></span>
      </div>
    </a>
  </div>
</li>

<!-- Catégories Actualités -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="actualites_cats.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="tag"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Catégories</span></span>
      </div>
    </a>
  </div>
</li>

<!-- Actualités -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="actualites.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="file-text"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Actualités</span></span>
      </div>
    </a>
  </div>
</li>

<!-- Agenda -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="agenda.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="calendar"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Agenda</span></span>
      </div>
    </a>
  </div>
</li>
<!-- Agenda -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="evenements.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="calendar"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Evenements</span></span>
      </div>
    </a>
  </div>
</li>

<!-- ADCI TV -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="adci_tv.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="tv"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">ADCI TV</span></span>
      </div>
    </a>
  </div>
</li>

<!-- Configuration -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="configuration.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="settings"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Configuration</span></span>
      </div>
    </a>
  </div>
</li>

<!-- utilisateur -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="utilisateurs.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="users"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Utilisateurs</span></span>
      </div>
    </a>
  </div>
</li>
<?php else: ?>

<!-- Accueil -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="accueil.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="home"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Accueil</span></span>
      </div>
    </a>
  </div>
</li>

<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link dropdown-indicator label-1" href="#categorie" data-bs-toggle="collapse" aria-expanded="false" aria-controls="categorie">
      <div class="d-flex align-items-center">
        <div class="dropdown-indicator-icon"><span class="fas fa-caret-right"></span></div>
        <span class="nav-link-icon"><span data-feather="briefcase"></span></span>
        <span class="nav-link-text">Catégories</span>
      </div>
    </a>
    <div class="parent-wrapper label-1">
      <ul class="nav collapse parent" id="categorie">
        <li class="nav-item">
          <a class="nav-link" href="actualites_cats.php">
            <div class="d-flex align-items-center"><span class="nav-link-text">Catégorie actualités</span></div>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="event_cats.php">
            <div class="d-flex align-items-center"><span class="nav-link-text">Catégorie évenement</span></div>
          </a>
        </li>
      </ul>
    </div>
  </div>
</li>
<!-- Catégories Actualités -->
<!-- <li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="actualites_cats.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="tag"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Catégories</span></span>
      </div>
    </a>
  </div>
</li> -->

<!-- Actualités -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="actualites.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="file-text"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Actualités</span></span>
      </div>
    </a>
  </div>
</li>

<!-- Gestions -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link dropdown-indicator label-1" href="#agentReception" data-bs-toggle="collapse" aria-expanded="false" aria-controls="agentReception">
      <div class="d-flex align-items-center">
        <div class="dropdown-indicator-icon"><span class="fas fa-caret-right"></span></div>
        <span class="nav-link-icon"><span data-feather="briefcase"></span></span>
        <span class="nav-link-text">Comptabilité</span>
      </div>
    </a>
    <div class="parent-wrapper label-1">
      <ul class="nav collapse parent" id="agentReception">
        <li class="nav-item">
          <a class="nav-link" href="pay_mensuels.php">
            <div class="d-flex align-items-center"><span class="nav-link-text">Paiements mensuels</span></div>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="pay_mensuels_recap.php">
            <div class="d-flex align-items-center"><span class="nav-link-text">Récapitulatif mensuels</span></div>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="exceptionnels_pay.php">
            <div class="d-flex align-items-center"><span class="nav-link-text">Paiements exeptionnels</span></div>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="exceptionnels_pay_recap.php">
            <div class="d-flex align-items-center"><span class="nav-link-text">Récapitulatif exeptionnels</span></div>
          </a>
        </li>
      </ul>
    </div>
  </div>
</li>

<!-- Dépenses Anvdko -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="depenses_anvdko.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="credit-card"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Dépenses Anvdko</span></span>
      </div>
    </a>
  </div>
</li>
<!-- Agenda -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="agenda.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="calendar"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Agenda</span></span>
      </div>
    </a>
  </div>
</li>

<!-- Agenda -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="evenements.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="calendar"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Evenements</span></span>
      </div>
    </a>
  </div>
</li>

<!-- ADCI TV -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="adci_tv.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="tv"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">ANVDKO TV</span></span>
      </div>
    </a>
  </div>
</li>

<!-- Configuration -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="adherents.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="users"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Membres</span></span>
      </div>
    </a>
  </div>
</li>

<!-- Configuration -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="cartesAdherents.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="users"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Carte Membres</span></span>
      </div>
    </a>
  </div>
</li>

<!-- Configuration -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="configuration.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="settings"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Configuration</span></span>
      </div>
    </a>
  </div>
</li>

<!-- utilisateur -->
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="utilisateurs.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="users"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Utilisateurs</span></span>
      </div>
    </a>
  </div>
</li>
<?php endif; ?>

      </ul>
    </div>

    <div class="navbar-vertical-footer">
      <button class="btn navbar-vertical-toggle border-0 fw-semibold w-100 white-space-nowrap d-flex align-items-center">
        <span class="uil uil-left-arrow-to-left fs-8"></span>
        <span class="uil uil-arrow-from-right fs-8"></span>
        <span class="navbar-vertical-footer-text ms-2">Réduire</span>
      </button>
    </div>
  </div>
</nav>