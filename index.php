<?php
require_once __DIR__ . '/include/php/connexion_bdd.php';
require_once __DIR__ . '/include/php/site_public.php';
$anvdko_site = anvdko_load_site_data($bdd, 1);
$anvdko_evenements_json = json_encode($anvdko_site['evenements_alerte'], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>ANVDKO |</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <?php $base = (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'],'localhost')!==false || strpos($_SERVER['HTTP_HOST'],'127.0.0.1')!==false)) ? '/anvdko' : ''; ?>
  <link href="<?php echo $base;?>/assets/img/LOGO.jpg" rel="icon">
  <link href="<?php echo $base;?>/assets/img/LOGO.jpg" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <!-- Glightbox CSS -->
<link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <style>
    .img-fixed {
      width: 100%;
      max-width: 1024px;
      height: 300px;
      object-fit: cover;
      object-position: center;
    }
    .img-square {
      width: 100%;
      aspect-ratio: 1 / 1;
      object-fit: cover;
      object-position: center;
    }
    /* Style pour le formulaire de contact */
    .php-email-form {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
        transition: all 0.3s ease;
    }

    .php-email-form:hover {
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15) !important;
    }

    .php-email-form .form-control {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .php-email-form .form-control:focus {
        border-color: #3a336e;
        box-shadow: 0 0 0 0.2rem rgba(58, 51, 110, 0.15);
        outline: none;
    }

    .php-email-form .form-control::placeholder {
        color: #999;
        font-style: italic;
    }

    .php-email-form textarea.form-control {
        resize: vertical;
        min-height: 150px;
    }

    .btn-send {
        background: linear-gradient(135deg, #3a336e 0%, #8d4eb5 100%);
        color: white;
        border: none;
        padding: 12px 40px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 30px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(58, 51, 110, 0.3);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-send:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(58, 51, 110, 0.4);
        background: linear-gradient(135deg, #2d2758 0%, #7a4199 100%);
    }

    .btn-send:active {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(58, 51, 110, 0.3);
    }

    .btn-send:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Messages d'alerte */
    .alert-message {
        animation: slideInDown 0.5s ease-out;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-weight: 500;
    }

    .alert-message i {
        font-size: 18px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* Animation pour les champs */
    .php-email-form .form-control {
        animation: fadeInUp 0.6s ease-out;
    }

    .php-email-form .col-md-6:nth-child(1) .form-control {
        animation-delay: 0.1s;
    }

    .php-email-form .col-md-6:nth-child(2) .form-control {
        animation-delay: 0.2s;
    }

    .php-email-form .col-md-12:nth-child(3) .form-control {
        animation-delay: 0.3s;
    }

    .php-email-form .col-md-12:nth-child(4) .form-control {
        animation-delay: 0.4s;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .php-email-form {
            padding: 20px !important;
        }
        
        .btn-send {
            width: 100%;
            padding: 15px;
        }
    }

    /* Boutons flottants Adhésion / Espace Membres */
    .anvdko-floating-actions {
      position: fixed;
      right: 20px;
      top: 50%;
      transform: translateY(-50%);
      z-index: 9998;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .anvdko-floating-btn {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 18px;
      border-radius: 50px;
      color: #fff;
      font-weight: 700;
      font-size: 14px;
      text-decoration: none;
      white-space: nowrap;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.18);
      transition: transform 0.25s ease, box-shadow 0.25s ease;
      animation: anvdko-float 3s ease-in-out infinite;
    }

    .anvdko-floating-btn i {
      font-size: 18px;
      line-height: 1;
    }

    .anvdko-floating-btn--adhesion {
      background: linear-gradient(135deg, #F26522 0%, #ff8c42 100%);
      animation-delay: 0s;
    }

    .anvdko-floating-btn--membres {
      background: linear-gradient(135deg, #3a336e 0%, #8d4eb5 100%);
      animation-delay: 0.4s;
    }

    .anvdko-floating-btn:hover {
      color: #fff;
      animation: none;
      transform: translateX(-4px) scale(1.04);
      box-shadow: 0 10px 28px rgba(0, 0, 0, 0.25);
    }

    @keyframes anvdko-float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-6px); }
    }

    @media (max-width: 768px) {
      .anvdko-floating-actions {
        right: 12px;
        gap: 10px;
      }

      .anvdko-floating-btn {
        padding: 10px 14px;
        font-size: 12px;
      }

      .anvdko-floating-btn span {
        display: none;
      }

      .anvdko-floating-btn i {
        font-size: 20px;
      }
    }

  </style>
</head>

<body>
    <?php include('includes/php/header.php'); ?>  
    <?php include('includes/php/body.php');?>
    <?php include('includes/php/footer.php');?>
    
<div class="anvdko-floating-actions" aria-label="Accès rapide">
  <a href="adhesion.php" class="anvdko-floating-btn anvdko-floating-btn--adhesion" title="Adhésion">
    <i class="bi bi-person-plus-fill"></i>
    <span>Adhésion</span>
  </a>
  <a href="membres/index.php" class="anvdko-floating-btn anvdko-floating-btn--membres" title="Espace Membres">
    <i class="bi bi-people-fill"></i>
    <span>Espace Membres</span>
  </a>
</div>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

<!-- Glightbox JS -->
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

<!-- Initialisation -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const lightbox = GLightbox({
      selector: '.glightbox'
    });
  });
</script>


  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    #anvdko-event-alert {
      position: fixed; top: 90px; left: 16px; z-index: 9999;
      background: linear-gradient(135deg, #dc3545, #c82333);
      color: #fff; border: none; border-radius: 50px;
      padding: 10px 18px; font-weight: 700; cursor: pointer;
      box-shadow: 0 4px 20px rgba(220,53,69,.45);
      animation: anvdko-pulse 2s infinite;
    }
    @keyframes anvdko-pulse { 0%,100%{ transform: scale(1); } 50%{ transform: scale(1.05); } }
  </style>
  <script>
    window.anvdkoEvenements = <?php echo $anvdko_evenements_json ?: '[]'; ?>;
    document.addEventListener('DOMContentLoaded', function() {
      const evts = window.anvdkoEvenements || [];
      if (!evts.length || typeof Swal === 'undefined') return;
      const btn = document.createElement('button');
      btn.id = 'anvdko-event-alert';
      btn.type = 'button';
      btn.innerHTML = '🚨 Événement' + (evts.length > 1 ? 's' : '');
      btn.title = 'Cliquez pour voir les événements';
      document.body.appendChild(btn);
      function fmtDate(d) {
        if (!d) return '—';
        try { return new Date(d.replace(' ', 'T')).toLocaleString('fr-FR'); } catch(e) { return d; }
      }
      function showEvent(ev) {
        const tel = ev.contact_telephone || '';
        let html = '<div class="text-start" style="font-size:15px">';
        if (ev.lieu) html += '<p><strong>Lieu :</strong> ' + ev.lieu + '</p>';
        if (ev.date_debut) html += '<p><strong>Début :</strong> ' + fmtDate(ev.date_debut) + '</p>';
        if (ev.date_fin) html += '<p><strong>Fin :</strong> ' + fmtDate(ev.date_fin) + '</p>';
        if (ev.description_plain) html += '<p>' + ev.description_plain.replace(/\n/g, '<br>') + '</p>';
        if (tel) html += '<p class="mt-3"><strong>📞 Contact :</strong> <a href="tel:' + tel.replace(/\s/g,'') + '">' + tel + '</a></p>';
        html += '</div>';
        Swal.fire({
          title: ev.titre || ev.n_event || 'Événement ANVDKO',
          html: html,
          width: '720px',
          confirmButtonText: 'Fermer',
          showCloseButton: true
        });
      }
      btn.addEventListener('click', function() {
        if (evts.length === 1) {
          showEvent(evts[0]);
          return;
        }
        const opts = {};
        evts.forEach((ev, i) => { opts[i] = (ev.titre || ev.n_event || 'Événement ' + (i+1)); });
        Swal.fire({
          title: '🚨 Événements à venir',
          input: 'select',
          inputOptions: opts,
          inputPlaceholder: 'Choisir un événement',
          showCancelButton: true,
          confirmButtonText: 'Voir les détails'
        }).then(r => {
          if (r.isConfirmed && evts[r.value]) showEvent(evts[r.value]);
        });
      });
    });
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
  const sections = document.querySelectorAll("section[id]");
  const navLinks = document.querySelectorAll("ul li a");

  window.addEventListener("scroll", () => {
    let scrollPosition = window.scrollY + 200; // +200 pour ajuster le point de déclenchement

    sections.forEach(section => {
      if (scrollPosition >= section.offsetTop && scrollPosition < section.offsetTop + section.offsetHeight) {
        navLinks.forEach(link => {
          link.classList.remove("active");
          if (link.getAttribute("href") === `#${section.id}` || link.getAttribute("href") === `index.php#${section.id}`) {
            link.classList.add("active");
          }
        });
      }
    });
  });
});

  </script>

</body>

</html>