<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 

$should_reload = false;

if (!empty($_GET["id_membre"])) {
    $_SESSION["membre_id"] = intval(strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_GET["id_membre"], 'D')))));

    $id = $_SESSION["membre_id"];
    $result = mysqli_query($bdd, "SELECT * FROM membres WHERE id = $id");
    $_SESSION['membre'] = ($result && mysqli_num_rows($result) > 0 ? mysqli_fetch_assoc($result) : []);
    $should_reload = true;
}

requireAdhesionPayee();

// Gestion de l'année
$_SESSION["annee_exceptionnelle"] = !empty($_GET['annee_exceptionnelle']) 
    ? intval($_GET["annee_exceptionnelle"]) 
    : (empty($_SESSION['annee_exceptionnelle']) ? date('Y') : $_SESSION['annee_exceptionnelle']);

// Gestion du motif
if (!empty($_GET['id_motif'])) {
    $_SESSION['id_motif'] = intval($_GET["id_motif"]);
    unset($_SESSION['motif_exceptionnel']);
    $should_reload = true;
}

if ($should_reload) {
    reload_current_page();
    exit;
}

$annee = intval($_SESSION['annee_exceptionnelle']);
$id_motif = $_SESSION['id_motif'] ?? null;

// Récupération des motifs disponibles pour l'année
$motifs = [];
$sql = "SELECT id, motif, mois_debut, mois_fin 
        FROM config_cotisations_exceptionnelles 
        WHERE YEAR(STR_TO_DATE(mois_debut, '%Y-%m')) = '$annee' 
        ORDER BY motif";

$resMotifs = mysqli_query($bdd, $sql);
while ($row = mysqli_fetch_assoc($resMotifs)) {
    $motifs[$row['id']] = $row;
}

// Vérification correspondance année/motif
if ($id_motif && !array_key_exists($id_motif, $motifs)) {
    echo "<script>alert('Le motif sélectionné ne correspond pas à l\\'année choisie');</script>";
    unset($_SESSION['id_motif']);
    $id_motif = null;
}

// Récupération des paiements si membre et motif valides
$motif_info = [];
$paiements = [];
$totals = ['a_payer' => 0, 'paye' => 0, 'reste' => 0];

if ($id_motif && !empty($_SESSION['membre_id'])) {
    $id_membre = intval($_SESSION['membre_id']);
    
    // Info motif
    $res = mysqli_query($bdd, "SELECT * FROM config_cotisations_exceptionnelles WHERE id = $id_motif");
    $motif_info = ($res && mysqli_num_rows($res)) > 0 ? mysqli_fetch_assoc($res) : [];
    
    // Paiements
    $sql = "SELECT ep.*, UPPER(CONCAT(m.nom, ' ', m.prenom)) AS nom_complet, m.genre
            FROM exceptionnels_pay ep
            JOIN membres m ON ep.id_membre = m.id
            WHERE ep.id_motif = $id_motif AND ep.id_membre = $id_membre";
    
    $res = mysqli_query($bdd, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        $paiements[] = $row;
        $totals['a_payer'] += floatval($row['a_payer']);
        $totals['paye'] += floatval($row['paye']);
    }
    $totals['reste'] = $totals['a_payer'] - $totals['paye'];
}

$membre_info = $_SESSION['membre'] ?? [];
?>

<!DOCTYPE html>
<html lang="fr-FR" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Récapitulatif Cotisation</title>
    
    <?php include('includes/php/include-css.php'); ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body { padding-top: 70px; }
        .progress-container { 
            width: 100%; 
            max-width: 600px;
            margin: 0 auto 20px;
            background: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
        }
        .progress-bar-custom {
            background: #4e73df;
            color: white;
            text-align: center;
            padding: 5px 0;
            font-weight: bold;
        }
        .stat-card {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .stat-card h5 {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .stat-card .value {
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include('includes/php/header.php'); ?>

    <div class="container">
        <div class="d-flex align-items-center my-4">
            <a href="accueil.php" class="btn btn-xs btn-secondary rounded-circle me-2">
                <i class="uil uil-arrow-left"></i>
            </a>
            <h3 class="mb-0">Retour</h3>
        </div>

        <div class="card card-fluid mb-5">
            <div class="card-header px-3 py-2 border-bottom d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-2">
                    <h3 class="mb-0">Récapitulatif des cotisations exceptionnelles</h3>
                    <h5 class="text-body-tertiary fw-semibold mb-0">Année <?= $annee ?></h5>
                </div>

                <form method="get" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="id_membre" value="<?= htmlspecialchars($_GET['id_membre'] ?? '') ?>">
                    <div class="input-group" style="max-width: 600px;">
                        <select class="form-select" name="annee_exceptionnelle" onchange="this.form.submit()">
                            <?php for ($y = date("Y"); $y >= date("Y") - 10; $y--): ?>
                                <option value="<?= $y ?>" <?= ($y == $annee ? 'selected' : '') ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>

                        <select class="form-select" name="id_motif" onchange="this.form.submit()">
                            <option value="">-- Choisir un motif --</option>
                            <?php foreach ($motifs as $id => $data): ?>
                                <option value="<?= $id ?>" <?= ($id == $id_motif ? 'selected' : '') ?>>
                                    <?= htmlspecialchars($data['motif']) ?> 
                                    (<?= date('m/Y', strtotime($data['mois_debut'] . '-01')) ?> - 
                                    <?= date('m/Y', strtotime($data['mois_fin'] . '-01')) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        
       <?php if (!empty($motifs) && !empty($id_motif) && isset($motifs[$id_motif])): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center text-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-tag fa-1x text-primary"></i> <!-- Changé l'icône pour mieux correspondre -->
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-1">
                                <?= htmlspecialchars($motifs[$id_motif]['motif']) ?> <!-- Correction de la variable -->
                            </h4>
                            <p class="mb-0 text-muted">
                                Période: <?= date('m/Y', strtotime($motifs[$id_motif]['mois_debut'] . '-01')) ?> - 
                                <?= date('m/Y', strtotime($motifs[$id_motif]['mois_fin'] . '-01')) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($motif_info) && !empty($paiements)): ?>
            <?php $pourcentage = $totals['a_payer'] > 0 ? round(($totals['paye'] / $totals['a_payer']) * 100, 2) : 0; ?>
            
            <div class="mt-4">
                <div class="progress-container">
                    <div class="progress-bar-custom" style="width: <?= $pourcentage ?>%">
                        <?= $pourcentage ?>%
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="stat-card bg-light-primary">
                            <h5>Total à payer</h5>
                            <div class="value text-primary"><?= number_format($totals['a_payer'], 0, ',', ' ') ?> FCFA</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-light-success">
                            <h5>Total payé</h5>
                            <div class="value text-success"><?= number_format($totals['paye'], 0, ',', ' ') ?> FCFA</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-light-warning">
                            <h5>Reste à payer</h5>
                            <div class="value text-warning"><?= number_format($totals['reste'], 0, ',', ' ') ?> FCFA</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-section mt-4">
                <div class="card card-fluid">
                    <div class="card-header p-2 border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary me-2">Période</span>
                                <?= date('m/Y', strtotime($motif_info['mois_debut'] . '-01')) ?> - 
                                <?= date('m/Y', strtotime($motif_info['mois_fin'] . '-01')) ?>
                            </div>
                            <button class="btn btn-sm btn-phoenix-primary" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>Imprimer
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover m-0">
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
                                <?php foreach ($paiements as $i => $p): 
                                    $reste = $p['a_payer'] - $p['paye'];
                                    $statut = $reste <= 0 ? 'Payé' : ($p['paye'] > 0 ? 'Partiel' : 'Impayé');
                                    $badge_class = $statut === 'Payé' ? 'bg-success' : ($statut === 'Partiel' ? 'bg-warning' : 'bg-danger');
                                ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= ($p['genre'] === 'HOMME' ? 'M.' : ($p['genre'] === 'FEMME' ? 'Mme' : 'Mlle')) . ' ' . $p['nom_complet'] ?></td>
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

        <?php elseif (!empty($motif_info) && empty($paiements)): ?>
            <div class="alert alert-info mt-4">Aucun paiement trouvé pour ce membre et ce motif.</div>
        <?php elseif (empty($motifs)): ?>
            <div class="alert alert-info text-center mt-4" role="alert">
                Veuillez sélectionner un motif et une année pour afficher les détails.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
</body>
</html>
