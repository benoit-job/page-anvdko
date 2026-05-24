<?php
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 

$erreur = "";
$afficher_formulaire_mdp = false;
$contact = "";

// Étape 1 : vérification du contact
if (isset($_POST['verifier_contact'])) {
    $contact = htmlspecialchars(trim($_POST['contact']));

    $query = "SELECT * FROM membres WHERE num_telephone = '$contact' LIMIT 1";
    $result = mysqli_query($bdd, $query);

    if (mysqli_num_rows($result) > 0) {
        $afficher_formulaire_mdp = true;
    } else {
        $erreur = "Aucun utilisateur trouvé avec ce contact.";
    }
}

// Étape 2 : mise à jour du mot de passe
if (isset($_POST['changer_mdp'])) {
    $contact = htmlspecialchars(trim($_POST['contact']));
    $nouveau_mdp = htmlspecialchars(trim($_POST['mot_de_passe']));

    $update = "UPDATE membres SET password = '$nouveau_mdp' WHERE num_telephone = '$contact'";
    if (mysqli_query($bdd, $update)) {
        echo "<script>alert('Mot de passe mis à jour avec succès. Vous pouvez maintenant vous connecter.'); window.location.href = 'index.php';</script>";
    } else {
        $erreur = "Erreur lors de la mise à jour.";
    }
}
?>


<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="en-US" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>Forgot Password</title>


    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <?php $base = (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'],'localhost')!==false || strpos($_SERVER['HTTP_HOST'],'127.0.0.1')!==false)) ? '/anvdko' : ''; ?>
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $base;?>/assets/img/LOGO.jpg">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $base;?>/assets/img/LOGO.jpg">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $base;?>/assets/img/LOGO.jpg">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $base;?>/assets/img/LOGO.jpg">
    <link rel="manifest" href="<?php echo $base;?>/assets/img/favicons/manifest.json">
    <meta name="msapplication-TileImage" content="<?php echo $base;?>/assets/img/LOGO.jpg">
    <meta name="theme-color" content="#ffffff">
    <script src="../anvdko/assets/vendors/simplebar/simplebar.min.js"></script>
    <script src="../anvdko/assets/js/config.js"></script>

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link href="../anvdko/assets/vendors/simplebar/simplebar.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link href="../anvdko/assets/css/theme-rtl.min.css" type="text/css" rel="stylesheet" id="style-rtl">
    <link href="../anvdko/assets/css/theme.min.css" type="text/css" rel="stylesheet" id="style-default">
    <link href="../anvdko/assets/css/user-rtl.min.css" type="text/css" rel="stylesheet" id="user-style-rtl">
    <link href="../anvdko/assets/css/user.min.css" type="text/css" rel="stylesheet" id="user-style-default">
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
     <style type="text/css">
      body {
  margin: 0;
  padding: 0;
  background-image: url('../assets/img/LOGO.jpg');
  background-size: cover;
  background-position: center;
  font-family: Arial, sans-serif;
  color: #333;
}

.container > .row {
  background: rgba(255, 255, 255, 0.85); /* fond blanc semi-transparent */
  padding: 30px 20px;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

/* Améliorer la lisibilité des labels */
.form-label {
  font-weight: 600;
  color: #1a1a1a;
}

/* Champ avec icône */
.form-icon-container {
  position: relative;
}

a.text-decoration-none:hover {
  color: #1e7e34;
  text-decoration: underline;
}

    </style>
  </head>


  <body>

    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
      <div class="container">
        <div class="row flex-center min-vh-100 py-5">
          <div class="col-sm-10 col-md-8 col-lg-5 col-xxl-4"><a class="d-flex flex-center text-decoration-none mb-4" href="password_forget.php">
              <div class="d-flex align-items-center fw-bolder fs-3 d-inline-block"><img src="../assets/img/LOGO.jpg" alt="KoueStore" width="75" class="rounded" />
              </div>
            </a>
            <div class="px-xxl-5">
              <div class="text-center mb-6">
                <h4 class="text-body-highlight">Mot de passe oublié ?</h4>
                <p class="text-body-tertiary mb-5">Entrez votre numéro de contact <br class="d-sm-none" />pour réinitialiser </p>

                <form method="post" action="password_forget.php" style="max-width: 400px; margin: 0 auto;">
                
                <!-- Champ de contact -->
                <div class="form-icon-container mb-3">
                  <input class="form-control form-icon-input" type="tel" name="contact" placeholder="Numéro de contact" value="<?= $contact ?>" required/><span class="fab fa-whatsapp text-success fs-8 form-icon"></span>
                </div>

                <!-- Si le contact est reconnu, afficher le champ du nouveau mot de passe -->
                <?php if ($afficher_formulaire_mdp): ?>
                    <div class="position-relative mb-3">
                        <span class="fas fa-key  text-success fs-8 position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%);"></span>
                        <input class="form-control ps-5 pe-5 password" id="password" type="password" name="mot_de_passe" placeholder="Nouveau mot de passe" required/>
                        <span class="fas fa-eye-slash  text-success fs-8 position-absolute" id="toggle-password"
                        onclick="togglePassword()" style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></span>
                    </div>
                    <button type="submit" name="changer_mdp" class="btn btn-success w-100">Modifier le mot de passe</button>
                <?php else: ?>
                    <button type="submit" name="verifier_contact" class="btn btn-primary w-100">Vérifier le contact <span class="fas fa-chevron-right ms-2"></span></button>
                <?php endif; ?>

                <!-- Message d'erreur -->
                <?php if (!empty($erreur)): ?>
                    <div class="alert alert-danger mt-3"><?= $erreur ?></div>
                <?php endif; ?>

                </form>


                <a class="fs-9 fw-bold" href="https://wa.me/2250171166820?text=Bonjour%20%C3%A0%20toute%20l%E2%80%99%C3%A9quipe%20de%20KoueConsolidated%2C%0AJe%20rencontre%20actuellement%20un%20probl%C3%A8me%20de%20connexion%20et%20j%E2%80%99aurais%20besoin%20d%E2%80%99assistance%20pour%20r%C3%A9initialiser%20mon%20mot%20de%20passe.%0AMerci%20par%20avance%20pour%20votre%20aide." target="_blank">
                    Toujours besoin d’aide ?
                </a>

              </div>

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
    <!--    End of Main Content-->
    <!-- ===============================================-->

    <script src="../anvdko/assets/vendors/popper/popper.min.js"></script>
    <script src="../anvdko/assets/vendors/bootstrap/bootstrap.min.js"></script>
    <script src="../anvdko/assets/vendors/anchorjs/anchor.min.js"></script>
    <script src="../anvdko/assets/vendors/is/is.min.js"></script>
    <script src="../anvdko/assets/vendors/fontawesome/all.min.js"></script>
    <script src="../anvdko/assets/vendors/lodash/lodash.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
    <script src="../anvdko/assets/vendors/list.js/list.min.js"></script>
    <script src="../anvdko/assets/vendors/feather-icons/feather.min.js"></script>
    <script src="../anvdko/assets/vendors/dayjs/dayjs.min.js"></script>
    <script src="../anvdko/assets/js/phoenix.js"></script>

  </body>

</html>