<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");
$annee = (int) date('Y');
$page_export_title = 'Récapitulatif adhésions ANVDKO';
?>

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANVDKO - Récapitulatif adhésions</title>
    <?php include('includes/php/includes-css.php');?>
</head>
<body>
    <main class="main" id="top">
        <?php include('includes/php/menu.php');?>
        <?php include('includes/php/header.php');?>

        <div class="content" data-export-region>
            <div class="pb-5">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                    <div>
                        <h3 class="mb-2">Récapitulatif des adhésions</h3>
                        <h5 class="text-body-tertiary fw-semibold">Comptabilité — adhésions par période</h5>
                    </div>
                    <a href="adhesion.php" class="btn btn-phoenix-secondary btn-sm">Gérer les adhésions</a>
                </div>

                <div class="card mb-4 no-print">
                    <div class="card-body">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="annee-adhesion" class="form-label">Année</label>
                                <select class="form-select annee-ajax-recap" id="annee-adhesion" name="annee">
                                    <?php
                                    $cy = (int) date('Y');
                                    for ($y = $cy; $y >= $cy - 10; $y--) {
                                        echo '<option value="' . $y . '"' . ($y === $annee ? ' selected' : '') . '>' . $y . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-9">
                                <?php include('includes/php/export_buttons.php'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="recap-stats-zone"></div>

                <div class="page-section mt-4" id="recap-ajax-content">
                    <div class="card card-fluid">
                        <div class="table-responsive">
                            <table class="table table-hover m-0" id="ma_table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>N° adhésion</th>
                                        <th>Membre</th>
                                        <th class="text-end">Montant</th>
                                        <th class="text-center">Statut</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody id="recap-table-body">
                                    <tr><td colspan="5" class="text-center"><div class="spinner-border spinner-border-sm"></div></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('includes/php/footer.php');?>
        </div>
    </main>

    <?php include('includes/php/includes-js.php');?>
    <script src="assets/js/annee-ajax-recap.js"></script>
    <script>
    function loadAdhesionRecap() {
        var annee = document.getElementById('annee-adhesion').value;
        fetch('ajax/adhesion_recap_ajax.php?annee=' + encodeURIComponent(annee))
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.success) throw new Error(data.message || 'Erreur');
                document.getElementById('recap-stats-zone').innerHTML = data.stats_html;
                document.getElementById('recap-table-body').innerHTML = data.table_html;
            })
            .catch(function(e) {
                if (typeof afficherToast === 'function') afficherToast(e.message, 'top', 'danger', 3500);
            });
    }
    document.getElementById('annee-adhesion').addEventListener('change', loadAdhesionRecap);
    document.addEventListener('DOMContentLoaded', loadAdhesionRecap);
    </script>
</body>
</html>
