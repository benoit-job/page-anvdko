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


  </style>
</head>

<body>
    <?php include('includes/php/header.php'); ?>  
    <?php include('includes/php/body.php');?>
    <?php include('includes/php/footer.php');?>
    
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