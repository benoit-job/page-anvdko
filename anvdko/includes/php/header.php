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
      <img src="../assets/img/LOGO.jpg" alt="ANVDKO" class="anvdko-loader-logo">
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

<nav class="navbar navbar-top fixed-top navbar-expand" id="navbarDefault">
  <div class="collapse navbar-collapse justify-content-between">
    <div class="navbar-logo">

      <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
      <a class="navbar-brand me-1 me-sm-3" href="accueil.php">
        <div class="d-flex align-items-center">
          <div class="d-flex align-items-center">
            <img src="<?php echo getUrlFichier($_SESSION["configuration"]["logo"]);?>" alt="logo" class="rounded" width="45" height="35"/>
            <p class="logo-text ms-2 d-none d-sm-block"><?php echo safe_safe_ucfirst($_SESSION["configuration"]["nom"]);?></p>
          </div>
        </div>
      </a>
    </div>
    
    <ul class="navbar-nav navbar-nav-icons flex-row">
      <li class="nav-item">
        <div class="theme-control-toggle fa-icon-wait px-2">
          <input class="form-check-input ms-0 theme-control-toggle-input" type="checkbox" data-theme-control="phoenixTheme" value="dark" id="themeControlToggle" />
          <label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon" data-feather="moon"></span></label>
          <label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon" data-feather="sun"></span></label>
        </div>
      </li>
      <li class="nav-item dropdown"><a class="nav-link lh-1 pe-0" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
          <div class="avatar avatar-l ">
            <img class="rounded-circle " src="<?php echo getUrlUtilisateur($_SESSION['utilisateur']["logo"]);?>" alt="" />

          </div>
        </a>
        <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border" aria-labelledby="navbarDropdownUser">
          <div class="card position-relative border-0">
            <div class="card-body p-0">
              <div class="text-center pt-4 pb-3">
                <div class="avatar avatar-xl ">
                  <img class="rounded-circle " src="<?php echo getUrlUtilisateur($_SESSION['utilisateur']["logo"]);?>" alt="" />

                </div>
                <h6 class="mt-2 text-body-emphasis"><?php echo safe_safe_ucfirst($_SESSION["utilisateur"]["pseudo"]);?></h6>
              </div>
            </div>

            <div class="card-footer p-0 border-top border-translucent">
              <div class="p-2"> 
                  <a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="mon_compte.php"> 
                      <span class="me-2" data-feather="user-plus"> </span> Mon compte
                  </a>
              </div>
            </div>

            <div class="card-footer p-0 border-top border-translucent">
              <div class="p-2"> 
                  <a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="deconnexion.php"> 
                      <span class="me-2" data-feather="log-out"> </span> Déconnexion
                  </a>
              </div>
            </div>
          </div>
        </div>
      </li>
    </ul>
  </div>
</nav>