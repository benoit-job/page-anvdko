<!-- ANVDKO Page Loader -->
<div class="anvdko-page-loader" id="anvdkoLoader">
  <div class="anvdko-loader-ring">
    <svg viewBox="0 0 200 200" class="anvdko-loader-svg" aria-hidden="true">
      <defs>
        <linearGradient id="anvdkoLoaderGradient" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#7b1fa2" />
          <stop offset="50%" stop-color="#9c4dcc" />
          <stop offset="100%" stop-color="#4a148c" />
        </linearGradient>
      </defs>
      <circle class="anvdko-ring-bg" cx="100" cy="100" r="80" fill="none" stroke="#e8e0f7" stroke-width="10" />
      <circle class="anvdko-ring-inner-bg" cx="100" cy="100" r="68" fill="none" stroke="#f4eff9" stroke-width="5" />
      <circle id="anvdko-ring-progress" class="anvdko-ring-progress" cx="100" cy="100" r="80" fill="none" stroke="url(#anvdkoLoaderGradient)" stroke-width="10" stroke-linecap="round" transform="rotate(-90 100 100)" stroke-dasharray="502.65" stroke-dashoffset="502.65" />
    </svg>
    <div class="anvdko-loader-center">
      <img src="assets/img/LOGO.jpg" alt="ANVDKO" class="anvdko-loader-logo">
      <div class="anvdko-loader-percent" id="loaderPercent">0%</div>
    </div>
  </div>
  <div class="anvdko-loader-text">Chargement en cours…</div>
</div>
<style>
  .anvdko-page-loader {
    position: fixed;
    inset: 0;
    background: rgba(255,255,255,0.96);
    z-index: 20000;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 18px;
    transition: opacity .35s ease, visibility .35s ease;
    opacity: 1;
    visibility: visible;
  }
  .anvdko-page-loader.loaded,
  .anvdko-page-loader.hide {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
  }
  .anvdko-loader-ring {
    position: relative;
    width: 220px;
    height: 220px;
  }
  .anvdko-loader-svg {
    width: 100%;
    height: 100%;
    animation: anvdko-spin 2.8s linear infinite;
  }
  .anvdko-ring-progress {
    transition: stroke-dashoffset 0.25s ease;
  }
  .anvdko-loader-center {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
  }
  .anvdko-loader-logo {
    width: 108px;
    height: 108px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid rgba(255,255,255,0.92);
    box-shadow: 0 14px 36px rgba(74,20,140,0.18);
    animation: anvdko-pulse 2s ease-in-out infinite;
  }
  .anvdko-loader-percent {
    font-size: 20px;
    font-weight: 800;
    color: #4a148c;
    letter-spacing: 0.5px;
  }
  .anvdko-loader-text {
    font-size: 15px;
    color: #4a148c;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
  }
  @keyframes anvdko-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  @keyframes anvdko-pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
  }
  @media (max-width: 768px) {
    .anvdko-loader-ring { width: 180px; height: 180px; }
    .anvdko-loader-logo { width: 88px; height: 88px; }
    .anvdko-loader-percent { font-size: 18px; }
    .anvdko-loader-text { font-size: 14px; }
  }
</style>
<script>
(function(){
  var loader = document.getElementById('anvdkoLoader');
  var pctEl = document.getElementById('loaderPercent');
  var progress = document.getElementById('anvdko-ring-progress');
  if(!loader || !pctEl || !progress) return;

  var circumference = 2 * Math.PI * 80;
  progress.style.strokeDasharray = circumference;
  progress.style.strokeDashoffset = circumference;

  var current = 0;
  function setProgress(value) {
    var clamped = Math.min(100, Math.max(0, value));
    var offset = circumference * (1 - clamped / 100);
    progress.style.strokeDashoffset = offset;
    pctEl.textContent = clamped + '%';
  }

  var interval = setInterval(function(){
    if (current < 96) {
      current += 1 + Math.floor(Math.random() * 2);
      setProgress(current);
    }
  }, 40);

  window.addEventListener('load', function(){
    clearInterval(interval);
    setProgress(100);
    setTimeout(function(){
      loader.classList.add('loaded');
      setTimeout(function(){ loader.style.display = 'none'; }, 450);
    }, 300);
  });
})();
</script>

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
