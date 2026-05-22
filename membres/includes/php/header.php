
  	  
<!-- ANVDKO Page Loader -->
<div class="anvdko-page-loader" id="anvdkoLoader">
  <div class="anvdko-loader-ring">
    <svg viewBox="0 0 180 180" class="anvdko-loader-svg" aria-hidden="true">
      <defs>
        <linearGradient id="anvdkoLoaderGradient" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#7b1fa2" />
          <stop offset="50%" stop-color="#9c4dcc" />
          <stop offset="100%" stop-color="#4a148c" />
        </linearGradient>
      </defs>
      <circle class="anvdko-ring-bg" cx="90" cy="90" r="72" fill="none" stroke="#e8e0f7" stroke-width="8" />
      <circle class="anvdko-ring-inner-bg" cx="90" cy="90" r="62" fill="none" stroke="#f4eff9" stroke-width="4" />
      <circle id="anvdko-ring-progress" class="anvdko-ring-progress" cx="90" cy="90" r="72" fill="none" stroke="url(#anvdkoLoaderGradient)" stroke-width="8" stroke-linecap="round" transform="rotate(-90 90 90)" stroke-dasharray="452.39" stroke-dashoffset="452.39" />
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
    width: 180px;
    height: 180px;
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
    gap: 8px;
  }
  .anvdko-loader-logo {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid rgba(255,255,255,0.92);
    box-shadow: 0 12px 32px rgba(74,20,140,0.18);
    animation: anvdko-pulse 2s ease-in-out infinite;
  }
  .anvdko-loader-percent {
    font-size: 18px;
    font-weight: 800;
    color: #4a148c;
    letter-spacing: 0.5px;
  }
  .anvdko-loader-text {
    font-size: 14px;
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
    50% { transform: scale(1.04); }
  }
  @media (max-width: 768px) {
    .anvdko-loader-ring { width: 150px; height: 150px; }
    .anvdko-loader-logo { width: 72px; height: 72px; }
    .anvdko-loader-percent { font-size: 16px; }
    .anvdko-loader-text { font-size: 13px; }
  }
</style>
<script>
(function(){
  var loader = document.getElementById('anvdkoLoader');
  var pctEl = document.getElementById('loaderPercent');
  var progress = document.getElementById('anvdko-ring-progress');
  if(!loader || !pctEl || !progress) return;

  var circumference = 2 * Math.PI * 72;
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
  }, 45);

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

<nav class='mb-1' style='height: 60px; padding: 5px; background-color: white; display: flex; justify-content: space-between; align-items: center; position: fixed; top: 0; width: 100%; z-index: 1000;'>

<!-- Premier bloc -->
<a href="accueil.php" style="display: flex; align-items: center; text-decoration: none;">
    <img src="../assets/img/LOGO.jpg" height="40" class="rounded" style="margin-right: 3px;">
    <div style="display: flex; flex-direction: column; justify-content: space-between;">
        <b style="color: black;">MEMBRE ANVDKO</b>
        <span class="badge" style="font-size: 10px; padding: 2px; background: linear-gradient(45deg, #ff8c00, #6a5acd, #00ced1, #ff1493);">
            <?= date("Y") . "/" . (date("Y") + 1) ?>
        </span>

    </div>
</a>


<div class="dropdown" style='cursor: pointer;'>
    <div class="dropdown-toggle no-caret" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; flex-direction: column; justify-content: space-between; margin-right: 5px;">
            <div style="color: black; font-weight: bold; position: relative; bottom: -3px;">
                <span class="d-block d-sm-none"><?php echo safe_safe_ucfirst($_SESSION["membre"]["nom"]);?></span>
                <span class="d-none d-sm-block"><?php echo safe_safe_ucfirst($_SESSION["membre"]["nom"]);?></span>
            </div>
            <div class="text-end" style='position: relative; top: -3px;'>
                <span class="badge text-bg-primary" style="font-size: 10px; padding: 2px; box-shadow: 0 12px 25px rgba(0, 0, 0, 0.4); transform: translateY(-5px); transition: all 0.3s ease;"><?php echo safe_safe_ucfirst($_SESSION["membre"]["num_adhesion"]);?></span>
            </div>
        </div>
        <img src="<?php echo getUrlFichier($_SESSION["membre"]["logo"]);?>" class="rounded-circle" width="40" height="40">
    </div>

    <div class="p-0 dropdown-menu" aria-labelledby="dropdownMenuButton1">
        <a href="deconnexion.php" class="dropdown-item"><i class='fas fa-sign-out-alt'></i> Déconnexion</a>
    </div>
</div>

</nav> 