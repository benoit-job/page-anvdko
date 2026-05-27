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
    #navbarVerticalNav .nav-link {
      border-radius: 10px;
      margin: 2px 10px;
      position: relative;
      transition: background-color 0.18s ease, color 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease;
    }

    #navbarVerticalNav .nav-link:hover {
      background: rgba(56, 116, 255, 0.08);
      transform: translateX(2px);
    }

    #navbarVerticalNav .nav-link.menu-link-active,
    #navbarVerticalNav .nav-link.active {
      background: linear-gradient(90deg, rgba(56, 116, 255, 0.16), rgba(32, 201, 151, 0.08));
      color: #1f3a8a !important;
      font-weight: 700;
      box-shadow: inset 3px 0 0 #3874ff;
    }

    #navbarVerticalNav .nav-link.menu-link-active::after,
    #navbarVerticalNav .nav-link.active::after {
      content: "";
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: #22c55e;
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.12);
    }

    #navbarVerticalNav .nav-link.menu-link-active .nav-link-icon span,
    #navbarVerticalNav .nav-link.active .nav-link-icon span {
      color: #2563eb !important;
      transform: scale(1.08);
    }

    #navbarVerticalNav .dropdown-indicator.menu-parent-active {
      background: rgba(15, 23, 42, 0.05);
      color: #111827 !important;
      font-weight: 700;
    }

    #navbarVerticalNav .dropdown-indicator.menu-parent-active .dropdown-indicator-icon {
      transform: rotate(90deg);
      transition: transform 0.18s ease;
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const currentPage = (window.location.pathname.split('/').pop() || 'accueil.php').toLowerCase();
      const pageAliases = {
        adherent_details: 'adherents.php',
        depense_detail: 'depenses_anvdko.php',
        recap_pay_mensuel: 'pay_mensuels_recap.php',
        recap_global_cotisations: 'recap_global_cotisations.php',
        recap_paiements_exceptionnels: 'recap_paiements_exceptionnels.php'
      };
      const normalizedCurrent = pageAliases[currentPage.replace('.php', '')] || currentPage;
      let activeLink = null;

      document.querySelectorAll('#navbarVerticalNav a.nav-link[href]').forEach(function (link) {
        const href = link.getAttribute('href');
        if (!href || href.charAt(0) === '#') return;

        const linkPage = href.split('?')[0].split('/').pop().toLowerCase();
        if (linkPage === normalizedCurrent) {
          activeLink = link;
          link.classList.add('active', 'menu-link-active');
          link.setAttribute('aria-current', 'page');
        }
      });

      if (!activeLink) return;

      const parentMenu = activeLink.closest('.parent');
      if (parentMenu) {
        parentMenu.classList.add('show');
        const parentToggle = document.querySelector('[data-bs-toggle="collapse"][href="#' + parentMenu.id + '"], [data-bs-toggle="collapse"][data-bs-target="#' + parentMenu.id + '"]');
        if (parentToggle) {
          parentToggle.classList.add('menu-parent-active');
          parentToggle.setAttribute('aria-expanded', 'true');
        }
      }

      const activeItem = activeLink.closest('.nav-item');
      if (activeItem) {
        activeItem.scrollIntoView({ block: 'nearest' });
      }
    });
  </script>

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

<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="gestion_projets.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="layers"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Gestion page Projets</span></span>
      </div>
    </a>
  </div>
</li>

<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="gestion_site.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="globe"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Gestion site public</span></span>
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

<!-- ANVDKO TV -->
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

<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="gestion_projets.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="layers"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Gestion page Projets</span></span>
      </div>
    </a>
  </div>
</li>

<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="gestion_site.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="globe"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Gestion site public</span></span>
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
          <a class="nav-link" href="adhesion.php">
            <div class="d-flex align-items-center"><span class="nav-link-text">Adhésions</span></div>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="adhesion_recap.php">
            <div class="d-flex align-items-center"><span class="nav-link-text">Récap. adhésions</span></div>
          </a>
        </li>
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
        <li class="nav-item">
          <a class="nav-link fw-bold text-primary" href="recap_global_cotisations.php">
            <div class="d-flex align-items-center"><span class="nav-link-text">Récap Global Cotisations</span></div>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link fw-bold text-warning" href="recap_paiements_exceptionnels.php">
            <div class="d-flex align-items-center"><span class="nav-link-text">Récap Global Exceptionnels</span></div>
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
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="gestion_projets.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="layers"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Gestion page Projets</span></span>
      </div>
    </a>
  </div>
</li>
<li class="nav-item">
  <div class="nav-item-wrapper">
    <a class="nav-link label-1" href="gestion_site.php">
      <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span data-feather="globe"></span></span>
        <span class="nav-link-text-wrapper"><span class="nav-link-text">Gestion site public</span></span>
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

<!-- ANVDKO TV -->
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
