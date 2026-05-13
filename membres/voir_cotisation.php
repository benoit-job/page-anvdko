<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
// Fonctions nécessaires
include("../include/php/fonctions.php"); 
?>

<?php
// Vérification si l'id_membre est passé en paramètre GET et mise à jour de la session
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

    <title>Cotisation</title>

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
            <a href='accueil.php' class="btn btn-xs btn-secondary rounded-circle me-2">
                <i class="uil uil-arrow-left"></i>
            </a>
            <h3 class="mb-0">Retour</h3>
        </div>
        <div class="card card-fluid mb-5">
    <div class="card-header px-3 py-2 border-bottom">
        <ul class="nav nav-tabs card-header-tabs m-0 p-0 justify-content-center w-100 fw-bold" id="loyerTabs" role="tablist">
            <li class="nav-item flex-fill text-center" role="presentation">
                <button class="nav-link active w-100" id="mois-actuel-tab" data-bs-toggle="tab" data-bs-target="#mois-actuel" type="button" role="tab">Mois Actuel</button>
            </li>
            <li class="nav-item flex-fill text-center" role="presentation">
                <button class="nav-link w-100" id="loyer-non-paye-tab" data-bs-toggle="tab" data-bs-target="#loyer-non-paye" type="button" role="tab">Non Payé</button>
            </li>
            <li class="nav-item flex-fill text-center" role="presentation">
                <button class="nav-link w-100" id="loyer-paye-avance-tab" data-bs-toggle="tab" data-bs-target="#loyer-paye-avance" type="button" role="tab">À venir</button>
            </li>
            <li class="nav-item flex-fill text-center" role="presentation">
                <a href="recap_pay_mensuels.php?id_membre=<?= htmlspecialchars(crypt_decrypt_chaine($_SESSION["membre_id"], 'C')) ?>" class="w-100 btn btn-primary rounded" target="_blank" rel="noopener noreferrer">Récapitulatis des paiements</a>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content" id="loyerTabsContent">

            <!-- Onglet Mois Actuel -->
            <div class="tab-pane fade show active" id="mois-actuel" role="tabpanel">
                <div class="row">
                    <?php
                    $mois_actuel = date('Y-m');
                    $query = "SELECT * FROM paiements WHERE id_membre = " . $_SESSION["membre_id"] . " AND mois_payer = '$mois_actuel'";
                    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");

                    if ($paiement = mysqli_fetch_assoc($resultat)) {
                        $a_payer = $paiement["a_payer"];
                        $paye = $paiement["paye"];
                        $reste = $a_payer - $paye;
                        $statut = $paiement["statut"];
                    } else {
                        // Valeurs par défaut si aucun paiement trouvé
                        $a_payer = 0;
                        $paye = 0;
                        $reste = 0;
                        $statut = 'Non payé';
                        $paiement = ["mois_payer" => $mois_actuel];
                    }

                    // Badge de statut
                    $statuts = match($statut) {
                        'Non payé' => "<span class='badge text-bg-danger'>Non payé</span>",
                        'Moitié payé' => "<span class='badge text-bg-warning'>Moitié payé</span>",
                        'Payé' => "<span class='badge text-bg-success'>Payé</span>",
                        default => "<span class='badge text-bg-secondary'>Inconnu</span>"
                    };

                    $card_class = match($statut) {
                        'Payé' => 'bg-success-subtle border-success',
                        'Moitié payé' => 'bg-warning-subtle border-warning',
                        default => 'bg-danger-subtle border-danger'
                    };
                    
                    ?>
                    <div class="col-md-4">
                        <a href="recap_pay_mensuels.php?id_membre=<?= htmlspecialchars(crypt_decrypt_chaine($_SESSION["membre_id"], 'C')) ?>" class="d-block text-decoration-none text-reset p-3 m-2 border-start border-5 rounded <?= $card_class ?>">
                            <h4 class="mb-3">
                                <?= safe_safe_ucfirst(utf8_encode(strftime('%B %Y', strtotime($paiement["mois_payer"])))) ?>
                            </h4>
                            <div class="my-1"><strong>À Payer</strong> <span class="float-end"><?= $a_payer ?> FCFA</span></div>
                            <div class="my-1"><strong>Payé</strong> <span class="float-end"><?= $paye ?> FCFA</span></div>
                            <div class="my-1"><strong>Reste</strong> <span class="float-end"><?= $reste ?> FCFA</span></div>
                            <div class="my-1"><strong>Statut</strong> <span class="float-end"><?= $statuts ?></span></div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Onglet Non Payé -->
            <div class="tab-pane fade" id="loyer-non-paye" role="tabpanel">
                <div class="row">
                    <?php
                    $membre_id = $_SESSION["membre_id"];

                    // 1. Récupère la date d'inscription du membre
                    $inscription_sql = "SELECT date_heure FROM membres WHERE id = $membre_id";
                    $inscription_result = mysqli_query($bdd, $inscription_sql);
                    $inscription_data = mysqli_fetch_assoc($inscription_result);
                    $date_inscription = new DateTime($inscription_data["date_heure"]);

                    // 2. Mois actuel (premier jour du mois)
                    $mois_actuel = new DateTime(date('Y-m-01'));

                    // 3. Récupérer tous les paiements passés du membre
                    $paiements_sql = "SELECT * FROM paiements WHERE id_membre = $membre_id AND mois_payer < '" . $mois_actuel->format('Y-m') . "'";
                    $paiements_result = mysqli_query($bdd, $paiements_sql);

                    $paiements = [];
                    while ($paiement = mysqli_fetch_assoc($paiements_result)) {
                        $paiements[$paiement["mois_payer"]] = $paiement;
                    }

                    // 4. Boucle de la date d'inscription jusqu'au mois actuel
                    $mois = clone $date_inscription;
                    $mois->modify('first day of this month');
                    $mois_non_payes = [];

                    while ($mois < $mois_actuel) {
                        $cle_mois = $mois->format('Y-m');

                        if (!isset($paiements[$cle_mois])) {
                            // Aucun paiement enregistré
                            $mois_non_payes[] = [
                                "mois" => $cle_mois,
                                "a_payer" => 0,
                                "paye" => 0,
                                "reste" => 0,
                                "statut" => "Non payé"
                            ];
                        } else {
                            // Paiement partiel ou non payé
                            $paiement = $paiements[$cle_mois];
                            if ($paiement["statut"] != "Payé") {
                                $mois_non_payes[] = $paiement;
                            }
                        }

                        $mois->modify('+1 month');
                    }

                    // 5. Affichage
                    if (count($mois_non_payes) > 0) {
                        foreach ($mois_non_payes as $p) {
                            $mois_affiche = safe_safe_ucfirst(utf8_encode(strftime('%B %Y', strtotime($p["mois"] ?? $p["mois_payer"]))));

                            $a_payer = $p["a_payer"];
                            $paye = $p["paye"];
                            $reste = $a_payer - $paye;

                            $statut = $p["statut"];
                            $statuts = match($statut) {
                                'Moitié payé' => "<span class='badge text-bg-warning'>Moitié payé</span>",
                                default => "<span class='badge text-bg-danger'>Non payé</span>"
                            };
                    ?>
                            <div class="col-md-4">
                                <a href="recap_pay_mensuels.php?id_membre=<?= htmlspecialchars(crypt_decrypt_chaine($_SESSION["membre_id"], 'C')) ?>" class="d-block text-decoration-none text-reset p-3 m-2 border-start border-5 rounded bg-danger-subtle border-danger">
                                    <h4 class="mb-3"><?= $mois_affiche ?></h4>
                                    <div class="my-1"><strong>À Payer</strong> <span class="float-end"><?= number_format($a_payer, 0, ',', ' ') ?> FCFA</span></div>
                                    <div class="my-1"><strong>Payé</strong> <span class="float-end"><?= number_format($paye, 0, ',', ' ') ?> FCFA</span></div>
                                    <div class="my-1"><strong>Reste</strong> <span class="float-end"><?= number_format($reste, 0, ',', ' ') ?> FCFA</span></div>
                                    <div class="my-1"><strong>Statut</strong> <span class="float-end"><?= $statuts ?></span></div>
                                </a>
                            </div>
                    <?php
                        }
                    } else {
                        echo "<p class='text-center'>Aucun mois manquant. Tous les paiements sont à jour.</p>";
                    }
                    ?>
                </div>
            </div>


            <!-- Onglet À Venir -->
            <div class="tab-pane fade" id="loyer-paye-avance" role="tabpanel">
                <div class="row">
                    <?php
                        $mois_actuel = date('Y-m');
                        $annee_actuelle = date('Y');
                        $membre_id = $_SESSION["membre_id"];

                        // Étape 1 : On récupère tous les paiements de l'année en cours
                        $query = "SELECT * FROM paiements 
                                WHERE id_membre = $membre_id 
                                AND LEFT(mois_payer, 4) = '$annee_actuelle'";
                        $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");

                        $paiements_map = [];
                        while ($paiement = mysqli_fetch_assoc($resultat)) {
                            $paiements_map[$paiement['mois_payer']] = $paiement;
                        }

                        // Étape 2 : Boucler de MAI à DÉCEMBRE
                        $mois_debut = new DateTime("$annee_actuelle-05-01");
                        $mois_fin = new DateTime("$annee_actuelle-12-01");

                        while ($mois_debut <= $mois_fin) {
                            $mois_format_sql = $mois_debut->format('Y-m');
                            $mois_formate = safe_safe_ucfirst(utf8_encode(strftime('%B %Y', $mois_debut->getTimestamp())));

                            if (isset($paiements_map[$mois_format_sql])) {
                                $paiement = $paiements_map[$mois_format_sql];
                                $a_payer = $paiement["a_payer"];
                                $paye = $paiement["paye"];
                                $reste = $a_payer - $paye;

                                switch ($paiement["statut"]) {
                                    case 'Payé':
                                        $bg = 'bg-success-subtle border-success';
                                        $statut_badge = "<span class='badge text-bg-success'>Payé</span>";
                                        break;
                                    case 'Moitié payé':
                                        $bg = 'bg-warning-subtle border-warning';
                                        $statut_badge = "<span class='badge text-bg-warning'>Moitié payé</span>";
                                        break;
                                    case 'Non payé':
                                    default:
                                        $bg = 'bg-primary-subtle border-primary';
                                        $statut_badge = "<span class='badge text-bg-primary'>Non payé</span>";
                                        break;
                                }
                            } else {
                                $a_payer = $paye = $reste = 0;
                                $bg = 'bg-primary-subtle border-primary';
                                $statut_badge = "<span class='badge text-bg-primary'>Non payé</span>";
                            }
                        ?>
                            <div class="col-md-4">
                                <a href="recap_pay_mensuels.php?id_membre=<?= htmlspecialchars(crypt_decrypt_chaine($_SESSION["membre_id"], 'C')) ?>" class="d-block text-decoration-none text-reset p-3 m-2 border-start border-5 rounded <?= $bg ?>">
                                    <h4 class="mb-3"><?= $mois_formate ?></h4>
                                    <div class="my-1"><strong>À Payer</strong> <span class="float-end"><?= number_format($a_payer, 0, ',', ' ') ?> FCFA</span></div>
                                    <div class="my-1"><strong>Payé</strong> <span class="float-end"><?= number_format($paye, 0, ',', ' ') ?> FCFA</span></div>
                                    <div class="my-1"><strong>Reste</strong> <span class="float-end"><?= number_format($reste, 0, ',', ' ') ?> FCFA</span></div>
                                    <div class="my-1"><strong>Statut paiement</strong> <span class="float-end"><?= $statut_badge ?></span></div>
                                </a>
                            </div>
                        <?php
                            $mois_debut->modify('+1 month');
                        }
                    ?>
                </div>
            </div>

        </div>
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
