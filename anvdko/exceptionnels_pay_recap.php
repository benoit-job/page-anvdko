<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");


// Définir l'année par défaut si elle n'existe pas
if (empty($_SESSION['annee_exceptionnelle'])) {
    $_SESSION["annee_exceptionnelle"] = date('Y');
}

// Initialisations
$motifs = [];
$last_motif = null;

if (isset($_POST['submitFiltrage'])) {
    // Sécurisation et enregistrement de l'année
    if (!empty($_POST["annee_exceptionnelle"])) {
        $_SESSION['annee_exceptionnelle'] = htmlspecialchars(trim($_POST["annee_exceptionnelle"]));
    }

    // Sécurisation et enregistrement du motif (corriger ici)
    if (!empty($_POST['id_motif'])) {
        $_SESSION['id_motif'] = intval($_POST["id_motif"]);
        // Supprimer l'ancienne variable inutile
        unset($_SESSION['motif_exceptionnel']);
    }
}

$annee = $_SESSION['annee_exceptionnelle'];

// Récupération des motifs disponibles pour l'année donnée
$sql = "SELECT DISTINCT id, motif, 
               DATE_FORMAT(STR_TO_DATE(mois_debut, '%Y-%m'), '%M %Y') as debut_format, 
               DATE_FORMAT(STR_TO_DATE(mois_fin, '%Y-%m'), '%M %Y') as fin_format 
        FROM config_cotisations_exceptionnelles 
        WHERE YEAR(STR_TO_DATE(mois_debut, '%Y-%m')) = '".mysqli_real_escape_string($bdd, $annee)."' 
        ORDER BY motif";

$resMotifs = mysqli_query($bdd, $sql);
if ($resMotifs) {
    while ($row = mysqli_fetch_assoc($resMotifs)) {
        $motifs[$row['id']] = [
            'motif' => $row['motif'],
            'debut' => $row['debut_format'],
            'fin' => $row['fin_format']
        ];
    }
}

// Définir le dernier motif utilisé
if (isset($_SESSION['id_motif']) && array_key_exists($_SESSION['id_motif'], $motifs)) {
    $last_motif = $_SESSION['id_motif'];
} elseif (!empty($motifs)) {
    $last_motif = array_key_first($motifs); // PHP 7.3+
    $_SESSION['id_motif'] = $last_motif;
}

// Initialiser les données
$motif_info = [];
$paiements = [];
$total_a_payer = 0;
$total_paye = 0;
$total_reste = 0;

// Charger les paiements si année + motif définis
if (!empty($_SESSION['annee_exceptionnelle']) && !empty($_SESSION['id_motif'])) {

    $id_motif = intval($_SESSION['id_motif']);
    $annee = intval($_SESSION['annee_exceptionnelle']);

    // Récupérer les infos du motif sélectionné
    $sql = "SELECT * FROM config_cotisations_exceptionnelles 
            WHERE id = $id_motif 
            AND YEAR(STR_TO_DATE(mois_debut, '%Y-%m')) = $annee";

    $res = mysqli_query($bdd, $sql);

    if ($res && mysqli_num_rows($res) > 0) {
        $motif_info = mysqli_fetch_assoc($res);

        // Récupérer les paiements associés
        $sql = "SELECT ep.*, 
                       UPPER(CONCAT(m.nom, ' ', m.prenom)) AS nom_complet,
                       m.genre
                FROM exceptionnels_pay ep
                JOIN membres m ON ep.id_membre = m.id
                WHERE ep.id_motif = $id_motif
                ORDER BY m.nom, m.prenom";

        $res = mysqli_query($bdd, $sql);

        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $paiements[] = $row;
                $total_a_payer += (float)$row['a_payer'];
                $total_paye += (float)$row['paye'];
            }
            $total_reste = $total_a_payer - $total_paye;
        }

    } else {
        unset($_SESSION['id_motif']); 
    }
}

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

        <div class="content">
            <div class="pb-5">
                <div class="mb-5">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-2">Récapitulatif global des paiements exceptionnels</h3>
                        </div>
                    </div>
                    
                    <!-- Formulaire de sélection -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="post" class="row g-3 align-items-end">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <!-- Bloc : Année + Motif + Valider -->
                                    <div class="input-group" style="max-width: 600px;">
                                        <input type="number" name="annee_exceptionnelle" class="form-control"
                                            value="<?= htmlspecialchars($_SESSION['annee_exceptionnelle']) ?>">

                                        <select class="form-select" id="id_motif" name="id_motif">
                                            <option value="">-- Choisir un motif --</option>
                                            <?php if(!empty($motifs)): ?>
                                                <?php foreach($motifs as $id => $data): ?>
                                                    <option value="<?= $id ?>" 
                                                        <?= isset($_SESSION['id_motif']) && $_SESSION['id_motif'] == $id ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($data['motif']) ?> 
                                                        (<?= htmlspecialchars($data['debut']) ?> - <?= htmlspecialchars($data['fin']) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>

                                        <button type="submit" name="submitFiltrage" class="btn btn-primary">
                                            Valider
                                        </button>
                                    </div>

                                    <!-- Recherche -->
                                    <div class="input-group flex-shrink-0" style="max-width: 250px;">
                                        <span class="input-group-text" id="basic-addon1">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text"
                                            class="form-control"
                                            onclick='rechercheInput(this)'
                                            placeholder="Rechercher...">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <?php if(!empty($motif_info)): ?>
                        <!-- Afficher le récapitulatif -->
                        <div class="mt-4">
                            <div class="progress-container">
                                <?php 
                                $pourcentage = $total_a_payer > 0 ? round(($total_paye / $total_a_payer) * 100, 2) : 0;
                                ?>
                                <div class="progress-bar-custom" style="width: <?= $pourcentage ?>%">
                                    <?= $pourcentage ?>%
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stat-card bg-light-primary">
                                        <h5>Total à payer</h5>
                                        <div class="value text-primary"><?= number_format($total_a_payer, 0, ',', ' ') ?> FCFA</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stat-card bg-light-success">
                                        <h5>Total payé</h5>
                                        <div class="value text-success"><?= number_format($total_paye, 0, ',', ' ') ?> FCFA</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stat-card bg-light-warning">
                                        <h5>Reste à payer</h5>
                                        <div class="value text-warning"><?= number_format($total_reste, 0, ',', ' ') ?> FCFA</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="page-section">
                            <div class="card card-fluid">
                                <div class="card-header p-2 border-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-primary me-2">Période</span>
                                            <?= date('d/m/Y', strtotime($motif_info['mois_debut'])) ?> - <?= date('d/m/Y', strtotime($motif_info['mois_fin'])) ?>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-phoenix-primary" onclick="window.print()">
                                                <i class="fas fa-print me-2"></i>Imprimer
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover m-0" id='ma_table'>
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Membre</th>
                                                <th class="text-end">À payer</th>
                                                <th class="text-end">Payé</th>
                                                <th class="text-end">Reste</th>
                                                <th>Date paiement</th>
                                                <th>Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($paiements as $index => $p): ?>
                                                <?php
                                                $reste = $p['a_payer'] - $p['paye'];
                                                $statut = '';
                                                $badge_class = '';
                                                
                                                if($reste <= 0) {
                                                    $statut = 'Payé';
                                                    $badge_class = 'bg-success';
                                                } elseif($p['paye'] > 0) {
                                                    $statut = 'Partiel';
                                                    $badge_class = 'bg-warning';
                                                } else {
                                                    $statut = 'Impayé';
                                                    $badge_class = 'bg-danger';
                                                }
                                                
                                                $civilite = ($p['genre'] == 'HOMME') ? 'M.' : (($p['genre'] == 'FEMME') ? 'Mme' : 'Mlle');
                                                ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td><?= $civilite . ' ' . htmlspecialchars($p['nom_complet']) ?></td>
                                                    <td class="text-end"><?= number_format($p['a_payer'], 0, ',', ' ') ?> FCFA</td>
                                                    <td class="text-end"><?= number_format($p['paye'], 0, ',', ' ') ?> FCFA</td>
                                                    <td class="text-end"><?= number_format($reste, 0, ',', ' ') ?> FCFA</td>
                                                    <td><?= $p['date_paiement'] ? date('d/m/Y', strtotime($p['date_paiement'])) : '-' ?></td>
                                                    <td><span class="badge <?= $badge_class ?>"><?= $statut ?></span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php elseif(empty($motifs)): ?>
                        <!-- Message quand aucun motif n'est trouvé -->
                        <div class="d-flex justify-content-center mt-4">
                            <div class="alert alert-warning text-center" role="alert" style="max-width: 600px;">
                                Aucune cotisation exceptionnelle n'a été trouvé pour l'année sélectionnée.
                                <br>
                                <a href="parametres_cotisations.php" class="alert-link">Cliquez ici pour configurer les motifs</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Message quand aucun motif n'est sélectionné -->
                        <div class="d-flex justify-content-center mt-4">
                            <div class="alert alert-info text-center" role="alert" style="max-width: 600px;">
                                Veuillez sélectionner un motif et une année pour afficher les détails.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php include('includes/php/footer.php');?>
        </div>
    </main>
    <?php include('includes/php/includes-js.php');?>
</body>
</html>


<script>
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
                '<tr id="no-result-message"><td colspan="100%" style="text-align:center; color:red;">Aucun résultat trouvé</td></tr>'
            );
        }
    });
}
</script>