<!-- ANVDKO Page Loader -->
<div class="anvdko-page-loader" id="anvdkoLoader">
  <img src="../assets/img/LOGO.jpg" alt="ANVDKO" class="loader-logo">
  <div class="loader-bar"><div class="loader-bar-inner"></div></div>
  <div class="loader-text">Chargement…</div>
</div>
<script>window.addEventListener('load',function(){var l=document.getElementById('anvdkoLoader');if(l)l.classList.add('loaded');});</script>

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