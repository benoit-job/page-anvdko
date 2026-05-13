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
if (!isset($_SESSION["annee"])) {
    $_SESSION["annee"] = date("Y");
}

if (isset($_GET['annee'])) {
    $_SESSION["annee"] = intval($_GET['annee']);
    reload_current_page();
}

$annee = $_SESSION["annee"];
$paiements = [];
$totaux_mois = array_fill(1, 12, ['a_payer' => 0, 'paye' => 0, 'reste' => 0]);
$total_global = ['a_payer' => 0, 'paye' => 0, 'reste' => 0];

// Modifier la requête pour filtrer par membre si un ID est spécifié
$sql = "SELECT p.*, 
               UPPER(CONCAT(m.nom, ' ', m.prenom)) AS nom_complet,
               m.genre,
               MONTH(STR_TO_DATE(p.mois_payer, '%Y-%m')) AS mois
        FROM paiements p
        JOIN membres m ON p.id_membre = m.id
        WHERE YEAR(STR_TO_DATE(p.mois_payer, '%Y-%m')) = ?";
        
// Ajouter le filtre par membre si un ID est spécifié
if (isset($_SESSION["membre_id"]) && !empty($_SESSION["membre_id"])) {
    $sql .= " AND p.id_membre = ?";
    $sql .= " ORDER BY p.mois_payer";
    $stmt = mysqli_prepare($bdd, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $annee, $_SESSION["membre_id"]);
} else {
    $sql .= " ORDER BY m.nom, m.prenom, p.mois_payer";
    $stmt = mysqli_prepare($bdd, $sql);
    mysqli_stmt_bind_param($stmt, "i", $annee);
}

mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $paiements[$row['id_membre']][$row['mois']] = $row;
        
        $totaux_mois[$row['mois']]['a_payer'] += $row['a_payer'];
        $totaux_mois[$row['mois']]['paye'] += $row['paye'];
        $totaux_mois[$row['mois']]['reste'] += $row['reste'];
        
        $total_global['a_payer'] += $row['a_payer'];
        $total_global['paye'] += $row['paye'];
        $total_global['reste'] += $row['reste'];
    }
}

$noms_mois = ["", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", 
              "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
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
    <style>
        .progress-container {
            height: 30px;
            background-color: #e9ecef;
            border-radius: 4px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .progress-bar-custom {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            transition: width 0.6s ease;
        }

        /* Couleurs selon le statut */
        .progress-paid {
            background-color: #28a745; /* Vert pour payé */
        }

        .progress-partial {
            background-color: #ffc107; /* Jaune pour partiel */
            color: #212529; /* Texte sombre pour meilleure lisibilité */
        }

        .progress-unpaid {
            background-color: #dc3545; /* Rouge pour impayé */
        }
        .stat-card {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
        }
        .stat-card h5 {
            font-size: 14px;
            margin-bottom: 5px;
            color: #6c757d;
        }
        .stat-card .value {
            font-size: 24px;
            font-weight: bold;
        }
        .month-header {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        .badge-paid {
            background-color: #28a745;
        }
        .badge-partial {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-unpaid {
            background-color: #dc3545;
        }
        .table-responsive {
            overflow-x: auto;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
                background: white;
            }
            .table-responsive {
                overflow: visible;
            }
        }
    </style>
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
                    <h5 class="text-body-tertiary fw-semibold mb-0">Année ( <span class="badge text-bg-success"><?= htmlspecialchars($annee) ?></span> )</h5>
                </div>

                <form method="get" class="d-flex align-items-center gap-2">
                    <select class="form-select" id="annee" name="annee" onchange="this.form.submit()">
                        <?php 
                        // Générer les 10 dernières années
                        $current_year = date("Y");
                        for ($y = $current_year; $y >= $current_year - 10; $y--) {
                            echo '<option value="'.$y.'" '.($y == $annee ? 'selected' : '').'>'.$y.'</option>';
                        }
                        ?>
                    </select>
                    <?php if (isset($_GET["id_membre"])): ?>
                        <input type="hidden" name="id_membre" value="<?= htmlspecialchars($_GET["id_membre"]) ?>">
                    <?php endif; ?>
                </form>
            </div>
        </div>
                    
        <!-- Statistiques globales -->
        <div class="mt-4">
            <!-- Par ceci -->
            <?php
                // Calcul du pourcentage
                $pourcentage = $total_global['a_payer'] > 0 ? 
                    round(($total_global['paye'] / $total_global['a_payer']) * 100, 2) : 0;

                // Détermination du statut
                if ($pourcentage >= 100) {
                    $status_class = 'progress-paid';
                } elseif ($pourcentage > 0) {
                    $status_class = 'progress-partial';
                } else {
                    $status_class = 'progress-unpaid';
                }
            ?>

            <div class="d-flex justify-content-center">
                <div class="progress-container" style="width: 80%; max-width: 600px;">
                    <div class="progress-bar-custom <?php echo $status_class; ?>" style="width: <?= $pourcentage ?>%">
                        <?= $pourcentage ?>%
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-card bg-light-primary">
                        <h5>Total à payer</h5>
                        <div class="value text-primary"><?= number_format($total_global['a_payer'], 0, ',', ' ') ?> FCFA</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card bg-light-success">
                        <h5>Total payé</h5>
                        <div class="value text-success"><?= number_format($total_global['paye'], 0, ',', ' ') ?> FCFA</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card bg-light-warning">
                        <h5>Reste à payer</h5>
                        <div class="value text-warning"><?= number_format($total_global['reste'], 0, ',', ' ') ?> FCFA</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des paiements -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Mois</th>
                                <th>À payer</th>
                                <th>Payé</th>
                                <th>Reste</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($_SESSION["membre_id"])): ?>
                                <?php foreach ($noms_mois as $num_mois => $nom_mois): ?>
                                    <?php if ($num_mois > 0): ?>
                                        <?php 
                                        $paiement = $paiements[$_SESSION["membre_id"]][$num_mois] ?? null;
                                        $a_payer = $paiement['a_payer'] ?? 0;
                                        $paye = $paiement['paye'] ?? 0;
                                        $reste = $paiement['reste'] ?? $a_payer;
                                        ?>
                                        <tr>
                                            <td><?= $nom_mois ?></td>
                                            <td><?= number_format($a_payer, 0, ',', ' ') ?> FCFA</td>
                                            <td><?= number_format($paye, 0, ',', ' ') ?> FCFA</td>
                                            <td><?= number_format($reste, 0, ',', ' ') ?> FCFA</td>
                                            <td>
                                                <?php if ($paye >= $a_payer && $a_payer > 0): ?>
                                                    <span class="badge badge-paid">Payé</span>
                                                <?php elseif ($paye > 0): ?>
                                                    <span class="badge badge-partial">Partiel</span>
                                                <?php else: ?>
                                                    <span class="badge badge-unpaid">Impayé</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts nécessaires pour le bon fonctionnement de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Réinclus le fichier CSS si nécessaire -->
    <?php include('includes/php/include-css.php'); ?>

    <script>
        // Activer les tooltips
        $(function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
</body>
</html>