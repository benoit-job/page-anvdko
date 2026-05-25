<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");
include("../include/php/recap_mensuels_render.php");

if (!isset($_SESSION["annee"])) {
    $_SESSION["annee"] = date("Y");
}
$annee = (int) $_SESSION["annee"];
$page_export_title = 'Récapitulatif cotisations mensuelles ' . $annee;
$recap_initial_html = render_recap_mensuels_html($bdd, $annee);
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

        <div class="content" data-export-region>
            <div class="pb-5">
                <div class="mb-5">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h3 class="mb-2">Récapitulatif des cotisations mensuelles</h3>
                            <h5 class="text-body-tertiary fw-semibold" id="recap-annee-label">Année <?= htmlspecialchars($annee) ?></h5>
                        </div>
                        <button class="btn btn-sm btn-phoenix-primary no-print" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Imprimer
                        </button>
                    </div>
                    
                    <div class="card mb-4 no-print">
                        <div class="card-body">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label for="annee" class="form-label">Année</label>
                                    <select class="form-select annee-ajax-recap" id="annee" name="annee">
                                        <?php
                                        $current_year = (int) date("Y");
                                        for ($y = $current_year; $y >= $current_year - 10; $y--) {
                                            echo '<option value="'.$y.'" '.($y == $annee ? 'selected' : '').'>'.$y.'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Rechercher</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control" id="recap-search" placeholder="Rechercher...">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <?php include('includes/php/export_buttons.php'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="recap-dynamic-zone"><?= $recap_initial_html ?></div>
                </div>
            </div>
            <?php include('includes/php/footer.php');?>
        </div>
    </main>

    <?php include('includes/php/includes-js.php');?>
    <script src="assets/js/annee-ajax-recap.js"></script>
    <script>
    function bindRecapSearch() {
        $('#recap-search').off('keyup').on('keyup', function () {
            var value = $(this).val().toLowerCase();
            var rows = $('#ma_table tbody tr');
            var matchFound = false;
            rows.each(function () {
                var isMatch = $(this).text().toLowerCase().indexOf(value) > -1;
                $(this).toggle(isMatch);
                if (isMatch) matchFound = true;
            });
            $('#no-result-message').remove();
            if (!matchFound && value) {
                $('#ma_table tbody').append('<tr id="no-result-message"><td colspan="14" class="text-center text-danger">Aucun résultat</td></tr>');
            }
        });
    }
    function loadMensuelsRecap() {
        var fd = new FormData();
        fd.append('annee', document.getElementById('annee').value);
        var zone = document.getElementById('recap-dynamic-zone');
        zone.style.opacity = '0.5';
        fetch('ajax/recap_mensuels_ajax.php', { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.success) throw new Error(data.message || 'Erreur');
                zone.innerHTML = data.content_html;
                zone.style.opacity = '1';
                document.getElementById('recap-annee-label').textContent = 'Année ' + data.annee;
                bindRecapSearch();
            })
            .catch(function(e) {
                zone.style.opacity = '1';
                if (typeof afficherToast === 'function') afficherToast(e.message, 'top', 'danger', 3500);
            });
    }
    document.getElementById('annee').addEventListener('change', loadMensuelsRecap);
    document.addEventListener('DOMContentLoaded', bindRecapSearch);
    </script>
</body>
</html>