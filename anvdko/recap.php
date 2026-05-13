<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");

// Vérifier si on a un ID de motif spécifique
if (isset($_GET['id_motif'])) {
    $_SESSION["id_motif"] = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_GET['id_motif'], 'D'))));
    reload_current_page();
}


// Récupérer les informations du motif
$motif_info = [];
if($_SESSION["id_motif"] > 0) {
    $sql = "SELECT * FROM config_cotisations_exceptionnelles WHERE id =".$_SESSION["id_motif"];
    $res = mysqli_query($bdd, $sql);
    if($res && mysqli_num_rows($res) > 0) {
        $motif_info = mysqli_fetch_assoc($res);
    }
}

if(empty($motif_info)) {
    header("Location: exceptionnels_pay.php");
    exit();
}

$paiements = [];
$sql = "SELECT ep.*, 
               UPPER(CONCAT(m.nom, ' ', m.prenom)) AS nom_complet,
               m.genre
        FROM exceptionnels_pay ep
        JOIN membres m ON ep.id_membre = m.id
        WHERE ep.id_motif = ".$_SESSION["id_motif"]."
        ORDER BY m.nom, m.prenom";
$res = mysqli_query($bdd, $sql);
if($res) {
    while($row = mysqli_fetch_assoc($res)) {
        $paiements[] = $row;
    }
}

$total_a_payer = 0;
$total_paye = 0;
foreach($paiements as $p) {
    $total_a_payer += $p['a_payer'];
    $total_paye += $p['paye'];
}
$total_reste = $total_a_payer - $total_paye;
?>

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANVDKO - Récapitulatif paiements exceptionnels</title>
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
                            <h3 class="mb-2">Récapitulatif des paiements</h3>
                            <h5 class="text-body-tertiary fw-semibold"><?= htmlspecialchars($motif_info['motif']) ?></h5>
                        </div>
                        <a href="exceptionnels_pay.php" class="btn btn-phoenix-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Retour
                        </a>
                    </div>
                    
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
            </div>
            <?php include('includes/php/footer.php');?>
        </div>
    </main>

    <?php include('includes/php/includes-js.php');?>
    
    <script>
        // Script pour l'impression
        function printRecap() {
            window.print();
        }
    </script>
</body>
</html>