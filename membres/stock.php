<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 
?>

<?php
if (isset($_GET["id_membre"])) {
    $_SESSION["membre_id"] = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_GET["id_membre"], 'D'))));
    reload_current_page();
            
    $query = "SELECT * FROM membres 
    WHERE id =".$_SESSION["membre_id"];
$resultat = mysqli_query($bdd, $query) or die("Requête non conforme784521");
$_SESSION['membre'] = mysqli_fetch_array($resultat);
}



$query = "SELECT *
           FROM paiements WHERE id_membre =".$_SESSION["membre_id"];
$resultat = mysqli_query($bdd, $query) or die("Requête non conforme");   
$_SESSION['paiment'] = mysqli_fetch_array($resultat);
?>

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Récapitulatif Cotisation</title>

    <!-- Inclus les fichiers CSS -->
    <?php include('includes/php/include-css.php'); ?>

    <!-- CDN Bootstrap 5.3 pour le style -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CDN FontAwesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body style='padding-top: 70px;'>

    <!-- Inclus le header -->
    <?php include('includes/php/header.php'); ?>

    <div class="container">

        <div class="d-flex align-items-center my-4">
            <a href='voir_cotisation.php' class="btn btn-xs btn-secondary rounded-circle me-2">
                <i class="uil uil-arrow-left"></i>
            </a>
            <h3 class="mb-0">Retour</h3>
        </div>
        <div class="card card-fluid mb-5">
            <div class="card-header px-3 py-2 border-bottom d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-2">
                    <h3 class="mb-0">Récapitulatif des cotisations mensuelles</h3>
                    <h5 class="text-body-tertiary fw-semibold mb-0">Année <?= htmlspecialchars($annee) ?></h5>
                </div>

                <form method="get" class="d-flex align-items-center gap-2">
                    <label for="annee" class="form-label mb-0 me-2">Année :</label>
                    <select class="form-select form-select-sm" id="annee" name="annee" onchange="this.form.submit()">
                        <?php 
                        // Générer les 10 dernières années
                        $current_year = date("Y");
                        for ($y = $current_year; $y >= $current_year - 10; $y--) {
                            echo '<option value="'.$y.'" '.($y == $annee ? 'selected' : '').'>'.$y.'</option>';
                        }
                        ?>
                    </select>
                </form>
            </div>

            <div class="card-body p-0">
            </div>

        </div>

    </div>

    <!-- Scripts nécessaires pour le bon fonctionnement de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


    <!-- Réinclus le fichier CSS si nécessaire -->
    <?php include('includes/php/include-css.php'); ?>
</body>

</html>
