<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");
include("../include/php/recap_exceptionnels_render.php");

if (empty($_SESSION['annee_exceptionnelle'])) {
    $_SESSION["annee_exceptionnelle"] = date('Y');
}
$annee = (int) $_SESSION['annee_exceptionnelle'];
$id_motif = isset($_SESSION['id_motif']) ? (int) $_SESSION['id_motif'] : 0;
$page_export_title = 'Récapitulatif cotisations exceptionnelles';
$motifs_options = render_motifs_options_html($bdd, $annee, $id_motif);
$recap_initial_html = render_recap_exceptionnels_html($bdd, $annee, $id_motif);
?>

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANVDKO - Récapitulatif global des paiements exceptionnels</title>
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
    </style>
</head>
<body>
    <main class="main" id="top">
        <?php include('includes/php/menu.php');?>
        <?php include('includes/php/header.php');?>

        <div class="content" data-export-region>
            <div class="pb-5">
                <div class="mb-5">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                        <div>
                            <h3 class="mb-2">Récapitulatif global des paiements exceptionnels</h3>
                        </div>
                        <button class="btn btn-sm btn-phoenix-primary no-print" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Imprimer
                        </button>
                    </div>
                    
                    <div class="card mb-4 no-print">
                        <div class="card-body">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">Année</label>
                                    <input type="number" id="annee_exceptionnelle" name="annee_exceptionnelle" class="form-control annee-ajax-recap"
                                        value="<?= (int) $annee ?>" min="2000" max="2100">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Motif</label>
                                    <select class="form-select" id="id_motif" name="id_motif"><?= $motifs_options ?></select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Rechercher</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control" id="recap-search-excep" placeholder="Rechercher...">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <?php include('includes/php/export_buttons.php'); ?>
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
    <script>
    function bindExcepSearch() {
        $('#recap-search-excep').off('keyup').on('keyup', function () {
            var value = $(this).val().toLowerCase();
            var matchFound = false;
            $('#ma_table tbody tr').each(function () {
                var isMatch = $(this).text().toLowerCase().indexOf(value) > -1;
                $(this).toggle(isMatch);
                if (isMatch) matchFound = true;
            });
            $('#no-result-message').remove();
            if (!matchFound && value) {
                $('#ma_table tbody').append('<tr id="no-result-message"><td colspan="7" class="text-center text-danger">Aucun résultat</td></tr>');
            }
        });
    }
    function loadExceptionnelsRecap() {
        var fd = new FormData();
        fd.append('annee_exceptionnelle', document.getElementById('annee_exceptionnelle').value);
        fd.append('id_motif', document.getElementById('id_motif').value);
        var zone = document.getElementById('recap-dynamic-zone');
        zone.style.opacity = '0.5';
        fetch('ajax/recap_exceptionnels_ajax.php', { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.success) throw new Error(data.message || 'Erreur');
                if (data.motifs_options_html) {
                    document.getElementById('id_motif').innerHTML = data.motifs_options_html;
                }
                zone.innerHTML = data.content_html;
                zone.style.opacity = '1';
                bindExcepSearch();
            })
            .catch(function(e) {
                zone.style.opacity = '1';
                if (typeof afficherToast === 'function') afficherToast(e.message, 'top', 'danger', 3500);
            });
    }
    document.getElementById('annee_exceptionnelle').addEventListener('change', loadExceptionnelsRecap);
    document.getElementById('id_motif').addEventListener('change', loadExceptionnelsRecap);
    document.addEventListener('DOMContentLoaded', bindExcepSearch);
    </script>
</body>
</html>