 <!-- ======= Header ======= -->
 <header id="header" class="fixed-top">
    <div class="container d-flex align-items-center">

      <!--<h1 class="logo me-auto"><a href="index.html"><span>AD</span>EN</a></h1>-->
      <!-- Uncomment below if you prefer to use an image logo -->
       <a href="index.php" class="logo me-auto ">
        <img src="assets/img/LOGO.jpg" alt="" class="img-fluid">
      </a>
      <h1 class="logo me-auto chinois-style"><a href="index.php"><span>ANV</span>DKO</a></h1>

      <nav id="navbar" class="navbar order-last order-lg-0">
        <ul class="menu">
          <li><a href="#hero" class="active">Accueil</a></li>
          <li><a href="#about">À propos</a></li>
          <li><a href="projets.php">Projets</a></li>
          <li><a href="#Actualites">Actualités</a></li>
          <li><a href="#gallery">Galerie</a></li>
          <li><a href="adhesion.php">Adhésion</a></li>
          <li><a href="membres/index.php">Espace Membres</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>

        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->

      <div class="header-social-links d-flex">
        <!--<a href="#" class="twitter"><i class="bu bi-twitter"></i></a>-->
        <a href="https://www.facebook.com/OssoukroNouvelleVision" target="_blank">
          <i class="bi bi-facebook"></i>
        </a>
       <!-- <a href="#" class="instagram"><i class="bu bi-instagram"></i></a>-->
       <!-- <a href="#" class="linkedin"><i class="bu bi-linkedin"></i></i></a>-->
      </div>

    </div>
  </header><!-- End Header -->
  <script>
  document.addEventListener("DOMContentLoaded", function () {
    let links = document.querySelectorAll("#navbar ul li a");
    let currentURL = window.location.href;

    links.forEach(link => {
      // Vérifie si le lien correspond à l'URL actuelle (avec ou sans ancre #id)
      if (link.href === currentURL || currentURL.includes(link.getAttribute("href"))) {
        link.classList.add("active");
      } else {
        link.classList.remove("active");
      }
    });
  });
</script>
