<?php

function recap_mensuels_load_data($bdd, $annee)
{
    $annee = (int) $annee;
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
    mysqli_stmt_bind_param($stmt, 'i', $annee);
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

    return compact('paiements', 'totaux_mois', 'total_global', 'annee');
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
                                <th class="text-center month-header"><?= substr($noms_mois[$m], 0, 3) ?></th>
                            <?php endfor; ?>
                            <th class="text-center month-header" rowspan="2">Total</th>
                        </tr>
                        <tr>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <th class="text-center"><small><?= number_format($totaux_mois[$m]['paye'], 0, ',', ' ') ?> FCFA</small></th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT id, UPPER(CONCAT(nom, ' ', prenom)) AS nom_complet, genre FROM membres ORDER BY nom, prenom";
                        $result = mysqli_query($bdd, $query);
                        while ($membre = mysqli_fetch_assoc($result)):
                            $civilite = ($membre['genre'] == 'HOMME') ? 'M.' : (($membre['genre'] == 'FEMME') ? 'Mme' : 'Mlle');
                            $total_membre = ['a_payer' => 0, 'paye' => 0, 'reste' => 0];
                        ?>
                        <tr>
                            <td><?= $civilite . ' ' . htmlspecialchars($membre['nom_complet']) ?></td>
                            <?php for ($m = 1; $m <= 12; $m++):
                                $p = $paiements[$membre['id']][$m] ?? null;
                                if ($p) {
                                    $total_membre['paye'] += $p['paye'];
                                    $statut = ($p['reste'] <= 0) ? 'Payé' : (($p['paye'] > 0) ? 'Partiel' : 'Impayé');
                                    $badge_class = ($p['reste'] <= 0) ? 'badge-paid' : (($p['paye'] > 0) ? 'badge-partial' : 'badge-unpaid');
                                }
                            ?>
                            <td class="text-center">
                                <?php if ($p): ?>
                                    <span class="badge <?= $badge_class ?>"><?= $statut ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">-</span>
                                <?php endif; ?>
                            </td>
                            <?php endfor; ?>
                            <td class="text-center"><?= number_format($total_membre['paye'], 0, ',', ' ') ?> FCFA</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total par mois</th>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
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
