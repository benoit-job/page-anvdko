<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");

// Au début du script, avant la requête SQL
if (!isset($_SESSION["annee"])) {
    $_SESSION["annee"] = date("Y"); // Année courante par défaut
}
// Vérifier si on a une année spécifique
if (isset($_GET['annee'])) {
    $_SESSION["annee"] = strip_tags(htmlspecialchars(trim($_GET['annee'])));
    reload_current_page();
}

// Récupérer tous les paiements pour cette année
$paiements = [];
$sql = "SELECT p.*, 
               CONCAT(m.nom, ' ', m.prenom) AS nom_complet,
               m.genre,
               MONTH(STR_TO_DATE(p.mois_payer, '%Y-%m')) AS mois
        FROM paiements p
        JOIN membres m ON p.id_membre = m.id
        WHERE YEAR(STR_TO_DATE(p.mois_payer, '%Y-%m')) = ".$_SESSION["annee"]."
        ORDER BY m.nom, m.prenom, p.mois_payer";
$res = mysqli_query($bdd, $sql);
if($res) {
    while($row = mysqli_fetch_assoc($res)) {
        $paiements[$row['id_membre']][$row['mois']] = $row;
    }
}

// Calculer les totaux globaux
$totaux_mois = array_fill(1, 12, ['a_payer' => 0, 'paye' => 0, 'reste' => 0]);
$total_global = ['a_payer' => 0, 'paye' => 0, 'reste' => 0];

foreach($paiements as $id_membre => $p_mois) {
    foreach($p_mois as $mois => $p) {
        $totaux_mois[$mois]['a_payer'] += $p['a_payer'];
        $totaux_mois[$mois]['paye'] += $p['paye'];
        $totaux_mois[$mois]['reste'] += $p['reste'];
        
        $total_global['a_payer'] += $p['a_payer'];
        $total_global['paye'] += $p['paye'];
        $total_global['reste'] += $p['reste'];
    }
}

// Liste des mois
$noms_mois = ["", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", 
              "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
?>

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANVDKO - Récapitulatif paiements mensuels</title>
    <?php include('includes/php/includes-css.php');?>
    
    <style>
        .progress-container {
            height: 30px;
            background: #e9ecef;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .progress-bar-custom {
            height: 100%;
            border-radius: 5px;
            background: linear-gradient(90deg, #6a5acd, #00ced1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            transition: width 0.5s ease;
        }
        .stat-card {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stat-card h5 {
            font-size: 1rem;
            margin-bottom: 10px;
        }
        .stat-card .value {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .month-header {
            background-color: #f8f9fa;
            font-weight: bold;
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
    </style>
</head>
<body>
    <main class="main" id="top">
        <?php include('includes/php/menu.php');?>
        <?php include('includes/php/header.php');?>

        <div class="content">
            <div class="pb-5">
                <div class="mb-5">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-2">Récapitulatif des paiements mensuels</h3>
                            <h5 class="text-body-tertiary fw-semibold">Année <?= $_SESSION["annee"] ?></h5>
                        </div>
                        <a href="pay_mensuels.php" class="btn btn-phoenix-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Retour
                        </a>
                    </div>
                    
                    <div class="mt-4">
                        <div class="progress-container">
                            <?php 
                            $pourcentage = $total_global['a_payer'] > 0 ? 
                                round(($total_global['paye'] / $total_global['a_payer']) * 100, 2) : 0;
                            ?>
                            <div class="progress-bar-custom" style="width: <?= $pourcentage ?>%">
                                <?= $pourcentage ?>%
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
                </div>

                <div class="page-section">
                    <div class="card card-fluid">
                        <div class="card-header p-2 border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-primary me-2">Année <?= $_SESSION["annee"] ?></span>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-phoenix-primary" onclick="window.print()">
                                        <i class="fas fa-print me-2"></i>Imprimer
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover m-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th rowspan="2">Membre</th>
                                        <?php for($m = 1; $m <= 12; $m++): ?>
                                            <th class="text-center month-header"><?= substr($noms_mois[$m], 0, 3) ?></th>
                                        <?php endfor; ?>
                                        <th class="text-center month-header" rowspan="2">Total</th>
                                    </tr>
                                    <tr>
                                        <?php for($m = 1; $m <= 12; $m++): ?>
                                            <th class="text-center">
                                                <small><?= number_format($totaux_mois[$m]['paye'], 0, ',', ' ') ?>FCFA</small>
                                            </th>
                                        <?php endfor; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Récupérer tous les membres
                                    $query = "SELECT id, UPPER(CONCAT(nom, ' ', prenom)) AS nom_complet, genre FROM membres ORDER BY nom, prenom";
                                    $result = mysqli_query($bdd, $query);
                                    
                                    while($membre = mysqli_fetch_assoc($result)): 
                                        $civilite = ($membre['genre'] == 'HOMME') ? 'M.' : 
                                                  (($membre['genre'] == 'FEMME') ? 'Mme' : 'Mlle');
                                        $total_membre = ['a_payer' => 0, 'paye' => 0, 'reste' => 0];
                                    ?>
                                        <tr>
                                            <td><?= htmlspecialchars($membre['nom_complet']) ?></td>
                                            <?php for($m = 1; $m <= 12; $m++): 
                                                $p = $paiements[$membre['id']][$m] ?? null;
                                                
                                                if($p) {
                                                    $total_membre['a_payer'] += $p['a_payer'];
                                                    $total_membre['paye'] += $p['paye'];
                                                    $total_membre['reste'] += $p['reste'];
                                                    
                                                    $statut = '';
                                                    $badge_class = '';
                                                    
                                                    if($p['reste'] <= 0) {
                                                        $statut = 'Payé';
                                                        $badge_class = 'badge-paid';
                                                    } elseif($p['paye'] > 0) {
                                                        $statut = 'Partiel';
                                                        $badge_class = 'badge-partial';
                                                    } else {
                                                        $statut = 'Impayé';
                                                        $badge_class = 'badge-unpaid';
                                                    }
                                                }
                                            ?>
                                                <td class="text-center">
                                                    <?php if($p): ?>
                                                        <span class="badge <?= $badge_class ?>" 
                                                              data-bs-toggle="tooltip" 
                                                              title="<?= number_format($p['paye'], 0, ',', ' ') ?>FCFA / <?= number_format($p['a_payer'], 2, ',', ' ') ?>FCFA">
                                                            <?= $statut ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endfor; ?>
                                            <td class="text-center">
                                                <?= number_format($total_membre['paye'], 0, ',', ' ') ?>FCFA
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total par mois</th>
                                        <?php for($m = 1; $m <= 12; $m++): ?>
                                            <th class="text-center">
                                                <small><?= number_format($totaux_mois[$m]['paye'], 0, ',', ' ') ?> FCFA</small>
                                            </th>

                                        <?php endfor; ?>
                                        <th class="text-center">
                                            <?= number_format($total_global['paye'], 0, ',', ' ') ?>FCFA
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('includes/php/footer.php');?>
        </div>
    </main>

    <?php include('includes/php/includes-js.php');?>
    
    <script>
        // Activer les tooltips
        $(function () {
            $('[data-bs-toggle="tooltip"]').tooltip()
        });
        
        // Script pour l'impression
        function printRecap() {
            window.print();
        }
    </script>
</body>
</html>