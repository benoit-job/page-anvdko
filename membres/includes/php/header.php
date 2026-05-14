
  	  
<!-- ANVDKO Page Loader -->
<div class="anvdko-page-loader" id="anvdkoLoader">
  <img src="../../assets/img/LOGO.jpg" alt="ANVDKO" class="loader-logo">
  <div class="loader-bar"><div class="loader-bar-inner"></div></div>
  <div class="loader-text">Chargement…</div>
</div>
<script>window.addEventListener('load',function(){var l=document.getElementById('anvdkoLoader');if(l)l.classList.add('loaded');});</script>

<nav class='mb-1' style='height: 60px; padding: 5px; background-color: white; display: flex; justify-content: space-between; align-items: center; position: fixed; top: 0; width: 100%; z-index: 1000;'>

<!-- Premier bloc -->
<a href="accueil.php" style="display: flex; align-items: center; text-decoration: none;">
    <img src="../../assets/img/LOGO.jpg" height="40" class="rounded" style="margin-right: 3px;">
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