<?php

function recap_mensuels_load_data($bdd, $annee)
{
    $annee = (int) $annee;
    $paiements = [];
    
    // Config
    $config_sql = "SELECT montant_mensuel FROM configurations LIMIT 1";
    $config_res = mysqli_query($bdd, $config_sql);
    $config_data = mysqli_fetch_assoc($config_res);
    $montant_mensuel_config = floatval($config_data['montant_mensuel'] ?? 2000);

    // Paiements
    $sql = "SELECT p.*, 
                   MONTH(STR_TO_DATE(p.mois_payer, '%Y-%m')) AS mois
            FROM paiements p
            WHERE YEAR(STR_TO_DATE(p.mois_payer, '%Y-%m')) = ?";
    $stmt = mysqli_prepare($bdd, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $annee);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $paiements[$row['id_membre']][$row['mois']] = $row;
        }
    }

    // Membres
    $membres = [];
    $q_membres = "
        SELECT m.id, 
            m.date_heure AS date_inscription,
            UPPER(CONCAT(m.nom, ' ', m.prenom)) AS nom_complet, 
            m.genre,
            a.date_heure AS date_adhesion
        FROM membres m
        LEFT JOIN adhesion a ON a.id_membre = m.id
        ORDER BY m.nom, m.prenom ASC";
    $res_membres = mysqli_query($bdd, $q_membres);
    while ($m = mysqli_fetch_assoc($res_membres)) {
        $membres[$m['id']] = $m;
    }

    $totaux_mois = array_fill(1, 12, ['a_payer' => 0, 'paye' => 0, 'reste' => 0]);
    $total_global = ['a_payer' => 0, 'paye' => 0, 'reste' => 0];

    foreach ($membres as $id_membre => $m) {
        $annee_ins = (int)date('Y', strtotime($m['date_adhesion']));
        $mois_ins = (int)date('n', strtotime($m['date_adhesion']));
        
        $mois_debut = 1;
        if ($annee == $annee_ins) {
            $mois_debut = $mois_ins + 1;
        }
        
        for ($mois = 1; $mois <= 12; $mois++) {
            if ($mois == 4 || ($annee == $annee_ins && $mois < $mois_debut) || ($annee < $annee_ins)) {
                continue;
            }
            
            $p = $paiements[$id_membre][$mois] ?? null;
            $a_payer = $p ? $p['a_payer'] : $montant_mensuel_config;
            $paye = $p ? $p['paye'] : 0;
            $reste = $p ? $p['reste'] : $a_payer;
            
            $totaux_mois[$mois]['a_payer'] += $a_payer;
            $totaux_mois[$mois]['paye'] += $paye;
            $totaux_mois[$mois]['reste'] += $reste;
            
            $total_global['a_payer'] += $a_payer;
            $total_global['paye'] += $paye;
            $total_global['reste'] += $reste;
        }
    }

    return compact('membres', 'paiements', 'totaux_mois', 'total_global', 'annee', 'montant_mensuel_config');
}

function render_recap_mensuels_html($bdd, $annee)
{
    $data = recap_mensuels_load_data($bdd, $annee);
    extract($data);
    $noms_mois = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    $pourcentage = $total_global['a_payer'] > 0
        ? round(($total_global['paye'] / $total_global['a_payer']) * 100, 2) : 0;

    ob_start();
    ?>
    <div class="mt-4" id="recap-stats-zone">
        <div class="progress-container">
            <div class="progress-bar-custom" style="width: <?= $pourcentage ?>%"><?= $pourcentage ?>%</div>
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
    <div class="page-section">
        <div class="card card-fluid">
            <div class="table-responsive">
                <table class="table table-hover m-0" id="ma_table">
                    <thead class="thead-light">
                        <tr>
                            <th rowspan="2">Membre</th>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <?php if ($m == 4) continue; ?>
                                <th class="text-center month-header"><?= substr($noms_mois[$m], 0, 3) ?></th>
                            <?php endfor; ?>
                            <th class="text-center month-header" rowspan="2">Total</th>
                        </tr>
                        <tr>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <?php if ($m == 4) continue; ?>
                                <th class="text-center"><small><?= number_format($totaux_mois[$m]['paye'], 0, ',', ' ') ?> FCFA</small></th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($membres as $id_membre => $membre):
                            $civilite = ($membre['genre'] == 'HOMME') ? 'M.' : (($membre['genre'] == 'FEMME') ? 'Mme' : 'Mlle');
                            
                            $annee_ins = (int)date('Y', strtotime($membre['date_adhesion']));
                            $mois_ins = (int)date('n', strtotime($membre['date_adhesion']));
                            $mois_debut = 1;
                            if ($annee == $annee_ins) {
                                $mois_debut = $mois_ins + 1;
                            }
                            
                            $total_membre = ['a_payer' => 0, 'paye' => 0, 'reste' => 0];
                        ?>
                        <tr>
                            <td><?=  $civilite . ' ' . html_entity_decode($membre['nom_complet'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></td>
                            <?php for ($m = 1; $m <= 12; $m++):
                                if ($m == 4) continue;
                                
                                if (($annee == $annee_ins && $m < $mois_debut) || ($annee < $annee_ins)): ?>
                                    <td class="text-center"><span class="badge bg-secondary">-</span></td>
                                <?php else:
                                    $p = $paiements[$membre['id']][$m] ?? null;
                                    $a_payer = $p ? $p['a_payer'] : $montant_mensuel_config;
                                    $paye = $p ? $p['paye'] : 0;
                                    $reste = $p ? $p['reste'] : $a_payer;
                                    
                                    $total_membre['a_payer'] += $a_payer;
                                    $total_membre['paye'] += $paye;
                                    $total_membre['reste'] += $reste;
                                    
                                    $statut = ($reste <= 0) ? 'Payé' : (($paye > 0) ? 'Partiel' : 'Impayé');
                                    $badge_class = ($reste <= 0) ? 'badge-paid' : (($paye > 0) ? 'badge-partial' : 'badge-unpaid');
                                ?>
                                <td class="text-center">
                                    <span class="badge <?= $badge_class ?>"><?= $statut ?></span>
                                </td>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <td class="text-center">
                                <?= number_format($total_membre['paye'], 0, ',', ' ') ?> / <?= number_format($total_membre['a_payer'], 0, ',', ' ') ?> FCFA
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total par mois</th>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <?php if ($m == 4) continue; ?>
                                <th class="text-center"><small><?= number_format($totaux_mois[$m]['paye'], 0, ',', ' ') ?> FCFA</small></th>
                            <?php endfor; ?>
                            <th class="text-center"><?= number_format($total_global['paye'], 0, ',', ' ') ?> FCFA</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
