<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");

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

$sql = "SELECT p.*, 
               UPPER(CONCAT(m.nom, ' ', m.prenom)) AS nom_complet,
               m.genre,
               MONTH(STR_TO_DATE(p.mois_payer, '%Y-%m')) AS mois
        FROM paiements p
        JOIN membres m ON p.id_membre = m.id
        WHERE YEAR(STR_TO_DATE(p.mois_payer, '%Y-%m')) = ?
        ORDER BY m.nom, m.prenom, p.mois_payer";

$stmt = mysqli_prepare($bdd, $sql);
mysqli_stmt_bind_param($stmt, "i", $annee);
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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANVDKO - Récapitulatif des cotisations mensuelles</title>
    <?php include('includes/php/includes-css.php');?>
    
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
            background-color: #0d6efd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            transition: width 0.6s ease;
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
<body>
    <main class="main" id="top">
        <?php include('includes/php/menu.php');?>
        <?php include('includes/php/header.php');?>

        <div class="content">
            <div class="pb-5">
                <div class="mb-5">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-2">Récapitulatif des cotisations mensuelles</h3>
                            <h5 class="text-body-tertiary fw-semibold">Année <?= htmlspecialchars($annee) ?></h5>
                        </div>
                    </div>
                    
                    <!-- Formulaire de sélection d'année -->
                    <div class="card mb-4 no-print">
                        <div class="card-body">
                            <form method="get" class="row g-3 align-items-end">
                               <div class="row">
                                    <div class="col-md-6">
                                        <label for="annee" class="form-label">Année</label>
                                        <select class="form-select" id="annee" name="annee" onchange="this.form.submit()">
                                            <?php 
                                            // Générer les 10 dernières années
                                            $current_year = date("Y");
                                            for ($y = $current_year; $y >= $current_year - 10; $y--) {
                                                echo '<option value="'.$y.'" '.($y == $annee ? 'selected' : '').'>'.$y.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        
                                            <label class="form-label">Réchercher</label>
                                        <div class="input-group flex-shrink-0">
                                            <span class="input-group-text" id="basic-addon1">
                                                <i class="fas fa-search"></i>
                                            </span>
                                            <input type="text"
                                                class="form-control"
                                                onclick='rechercheInput(this)'
                                                placeholder="Rechercher...">
                                        </div>
                                    </div>
                               </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Statistiques globales -->
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

                <!-- Tableau récapitulatif -->
                <div class="page-section">
                    <div class="card card-fluid">
                        <div class="card-header p-2 border-0 no-print">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-primary me-2">Année <?= htmlspecialchars($annee) ?></span>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-phoenix-primary" onclick="window.print()">
                                        <i class="fas fa-print me-2"></i>Imprimer
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover m-0" id="ma_table">
                                <thead class="thead-light">
                                    <tr>
                                        <th rowspan="2">Membre</th>
                                        <?php for ($m = 1; $m <= 12; $m++): ?>
                                            <th class="text-center month-header"><?= substr($noms_mois[$m], 0, 3) ?></th>
                                        <?php endfor; ?>
                                        <th class="text-center month-header" rowspan="2">Total</th>
                                    </tr>
                                    <tr>
                                        <?php for ($m = 1; $m <= 12; $m++): ?>
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
                                    
                                    while ($membre = mysqli_fetch_assoc($result)): 
                                        $civilite = ($membre['genre'] == 'HOMME') ? 'M.' : 
                                                  (($membre['genre'] == 'FEMME') ? 'Mme' : 'Mlle');
                                        $total_membre = ['a_payer' => 0, 'paye' => 0, 'reste' => 0];
                                    ?>
                                        <tr>
                                            <td><?= $civilite . ' ' . htmlspecialchars($membre['nom_complet']) ?></td>
                                            <?php for ($m = 1; $m <= 12; $m++): 
                                                $p = $paiements[$membre['id']][$m] ?? null;
                                                
                                                if ($p) {
                                                    $total_membre['a_payer'] += $p['a_payer'];
                                                    $total_membre['paye'] += $p['paye'];
                                                    $total_membre['reste'] += $p['reste'];
                                                    
                                                    $statut = '';
                                                    $badge_class = '';
                                                    
                                                    if ($p['reste'] <= 0) {
                                                        $statut = 'Payé';
                                                        $badge_class = 'badge-paid';
                                                    } elseif ($p['paye'] > 0) {
                                                        $statut = 'Partiel';
                                                        $badge_class = 'badge-partial';
                                                    } else {
                                                        $statut = 'Impayé';
                                                        $badge_class = 'badge-unpaid';
                                                    }
                                                }
                                            ?>
                                                <td class="text-center">
                                                    <?php if ($p): ?>
                                                        <span class="badge <?= $badge_class ?>" 
                                                              data-bs-toggle="tooltip" 
                                                              title="<?= number_format($p['paye'], 0, ',', ' ') ?>FCFA / <?= number_format($p['a_payer'], 0, ',', ' ') ?>FCFA">
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
                                        <?php for ($m = 1; $m <= 12; $m++): ?>
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
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
        
        // Fonction de recherche
        function rechercheInput(element) {
            $(element).on('keyup', function () {
                var value = $(this).val().toLowerCase();
                var rows = $('#ma_table tbody tr');
                var matchFound = false;

                rows.each(function () {
                    var rowText = $(this).text().toLowerCase();
                    var isMatch = rowText.indexOf(value) > -1;
                    $(this).toggle(isMatch);

                    if (isMatch) {
                        matchFound = true;
                    }
                });

                // Supprime tout message précédent
                $('#no-result-message').remove();

                // Si aucun résultat trouvé, affiche un message
                if (!matchFound) {
                    $('#ma_table tbody').append(
                        '<tr id="no-result-message"><td colspan="14" style="text-align:center; color:red;">Aucun résultat trouvé</td></tr>'
                    );
                }
            });
        }
    </script>
</body>
</html>