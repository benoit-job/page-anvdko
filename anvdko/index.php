

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="en-US" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>Administration</title>


    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <?php $base = (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'],'localhost')!==false || strpos($_SERVER['HTTP_HOST'],'127.0.0.1')!==false)) ? '/anvdko' : ''; ?>
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $base;?>/fichiers/logos/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $base;?>/fichiers/logos/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $base;?>/fichiers/logos/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $base;?>/fichiers/logos/favicon.ico">
    <link rel="manifest" href="<?php echo $base;?>/assets/img/favicons/manifest.json">
    <meta name="msapplication-TileImage" content="<?php echo $base;?>/fichiers/logos/favicon.ico">
    <meta name="theme-color" content="#ffffff">
    <script src="vendors/simplebar/simplebar.min.js"></script>
    <script src="assets/js/config.js"></script>


    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link href="vendors/simplebar/simplebar.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link href="assets/css/theme-rtl.min.css" type="text/css" rel="stylesheet" id="style-rtl">
    <link href="assets/css/theme.min.css" type="text/css" rel="stylesheet" id="style-default">
    <link href="assets/css/user-rtl.min.css" type="text/css" rel="stylesheet" id="user-style-rtl">
    <link href="assets/css/user.min.css" type="text/css" rel="stylesheet" id="user-style-default">
    <script>
      var phoenixIsRTL = window.config.config.phoenixIsRTL;
      if (phoenixIsRTL) {
        var linkDefault = document.getElementById('style-default');
        var userLinkDefault = document.getElementById('user-style-default');
        linkDefault.setAttribute('disabled', true);
        userLinkDefault.setAttribute('disabled', true);
        document.querySelector('html').setAttribute('dir', 'rtl');
      } else {
        var linkRTL = document.getElementById('style-rtl');
        var userLinkRTL = document.getElementById('user-style-rtl');
        linkRTL.setAttribute('disabled', true);
        userLinkRTL.setAttribute('disabled', true);
      }
    </script>
  </head>

  <body class="login-watermark">

    <style>
      body.login-watermark { position: relative; min-height: 100vh; }
      body.login-watermark::before {
        content: '';
        position: fixed;
        inset: 0;
        background: url('/anvdko/assets/img/LOGO.jpg') center 42% no-repeat;
        background-size: min(480px, 85vw) auto;
        opacity: 0.07;
        pointer-events: none;
        z-index: 0;
      }
      body.login-watermark #top { position: relative; z-index: 1; }
    </style>

    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
      <div class="container">
        <div class="row flex-center min-vh-100 py-5">
            <div class="col-sm-10 col-md-8 col-lg-5 col-xl-5 col-xxl-3">
                <div class="text-center mb-5">
                    <div class="avatar avatar-4xl mb-4">
                    <img class="rounded-circle" src="../assets/img/LOGO.jpg" alt="ANVDKO" style="object-fit:cover;" />
                    </div>
                    <h2 class="text-body-highlight"> <span class="fw-normal"></span>Compte administrateur</h2>
                    <p class="text-body-tertiary">Identifiant (pseudo, téléphone ou e-mail) et mot de passe</p>
                </div>
                <div class="position-relative">
                    <hr class="bg-body-secondary mt-5 mb-4" />
                    <div class="divider-content-center">Connexion au compte</div>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label" for="login_identifier">Utilisateur ou téléphone ou e-mail</label>
                    <div class="form-icon-container">
                    <input class="form-control form-icon-input" id="login_identifier" type="text" name="login_identifier" placeholder="Pseudo, n° téléphone ou e-mail" autocomplete="username" required/>
                    <span class="fas fa-user text-body fs-9 form-icon"></span>
                    </div>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label" for="password">Mot de passe</label>
                    <div class="position-relative">
                        <span class="fas fa-key text-body fs-9 position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%);"></span>
                        <input class="form-control ps-5 pe-5 password" id="password" type="password" name="password" placeholder="Mot de passe" autocomplete="current-password" required/>
                        <span class="fas fa-eye-slash text-body fs-9 position-absolute" id="toggle-password"
                        onclick="togglePassword()" style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></span>
                    </div>
                </div>
                <div class="mb-2">
                <small id="message"></small>
                <button class="btn btn-primary w-100"  type="button" name="connexion" id="connexions">Connectez-vous</button>
                </div>
            </div>

        </div>
      </div>
      <script>
        var navbarTopStyle = window.config.config.phoenixNavbarTopStyle;
        var navbarTop = document.querySelector('.navbar-top');
        if (navbarTopStyle === 'darker') {
          navbarTop.setAttribute('data-navbar-appearance', 'darker');
        }

        var navbarVerticalStyle = window.config.config.phoenixNavbarVerticalStyle;
        var navbarVertical = document.querySelector('.navbar-vertical');
        if (navbarVertical && navbarVerticalStyle === 'darker') {
          navbarVertical.setAttribute('data-navbar-appearance', 'darker');
        }
      </script>

<script>
  // Fonction pour afficher/masquer le mot de passe
  function togglePassword() {
    var passwordField = document.getElementById("password");
    var toggleIcon = document.getElementById("toggle-password");
    
    // Vérifier si le mot de passe est visible
    if (passwordField.type === "password") {
      passwordField.type = "text";  // Afficher le mot de passe
      toggleIcon.classList.remove("fa-eye-slash");
      toggleIcon.classList.add("fa-eye");
    } else {
      passwordField.type = "password";  // Cacher le mot de passe
      toggleIcon.classList.remove("fa-eye");
      toggleIcon.classList.add("fa-eye-slash");
    }
  }
</script>

    </main>


    <!-- ===============================================-->
    <!--    JavaScripts-->
    <!-- ===============================================-->
     <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="vendors/popper/popper.min.js"></script>
    <script src="vendors/bootstrap/bootstrap.min.js"></script>
    <script src="vendors/anchorjs/anchor.min.js"></script>
    <script src="vendors/is/is.min.js"></script>
    <script src="vendors/fontawesome/all.min.js"></script>
    <script src="vendors/lodash/lodash.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
    <script src="vendors/list.js/list.min.js"></script>
    <script src="vendors/feather-icons/feather.min.js"></script>
    <script src="vendors/dayjs/dayjs.min.js"></script>
    <script src="assets/js/phoenix.js"></script>

  </body>

</html>

<script>
   
    $("#connexions").click(function(){

        var loginId = $('#login_identifier').val();
        var password = $('.password').val();

        $("#connexions").html("<div class='spinner-border spinner-border-sm' role='status'></div> Connexion en cours....");


          $.ajax({
                url: "ajax.php",
                method: "POST",
                data: {
                    'login_identifier': loginId,
                    'password': password,
                    'connexion': ''
                },
                dataType: "html",
                success: function (data) {
                    setTimeout(function () {
                      
                        $("#connexions").html("Connectez-vous");
                        if(data.trim() == "succes")
                        {
                          window.location.href = "accueil.php";
                        }
                        else
                        {
                          $("#message").css("color", "red").html(data);
                          
                        }
                    }, 2000);
              }
    });  
    });
</script>