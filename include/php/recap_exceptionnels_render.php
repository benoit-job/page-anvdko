<?php

function recap_exceptionnels_motifs($bdd, $annee)
{
    $annee = (int) $annee;
    $motifs = [];
    $sql = "SELECT DISTINCT id, motif, 
                   DATE_FORMAT(STR_TO_DATE(mois_debut, '%Y-%m'), '%M %Y') as debut_format, 
                   DATE_FORMAT(STR_TO_DATE(mois_fin, '%Y-%m'), '%M %Y') as fin_format 
            FROM config_cotisations_exceptionnelles 
            WHERE YEAR(STR_TO_DATE(mois_debut, '%Y-%m')) = $annee 
            ORDER BY motif";
    $res = mysqli_query($bdd, $sql);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $motifs[$row['id']] = $row;
        }
    }
    return $motifs;
}

function render_recap_exceptionnels_html($bdd, $annee, $id_motif)
{
    $annee = (int) $annee;
    $id_motif = (int) $id_motif;
    $motifs = recap_exceptionnels_motifs($bdd, $annee);

    if (empty($motifs)) {
        return '<div class="alert alert-warning text-center mt-4">Aucune cotisation exceptionnelle pour l\'année ' . $annee . '.</div>';
    }
    if ($id_motif <= 0 || !isset($motifs[$id_motif])) {
        $id_motif = (int) array_key_first($motifs);
    }

    $sql = "SELECT * FROM config_cotisations_exceptionnelles WHERE id = $id_motif AND YEAR(STR_TO_DATE(mois_debut, '%Y-%m')) = $annee";
    $res = mysqli_query($bdd, $sql);
    if (!$res || !mysqli_num_rows($res)) {
        return '<div class="alert alert-info text-center mt-4">Sélectionnez un motif.</div>';
    }
    $motif_info = mysqli_fetch_assoc($res);

    $paiements = [];
    $total_a_payer = 0;
    $total_paye = 0;
    $sql = "SELECT ep.*, UPPER(CONCAT(m.nom, ' ', m.prenom)) AS nom_complet, m.genre
            FROM exceptionnels_pay ep
            JOIN membres m ON ep.id_membre = m.id
            WHERE ep.id_motif = $id_motif
            ORDER BY m.nom, m.prenom";
    $res = mysqli_query($bdd, $sql);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $paiements[] = $row;
            $total_a_payer += (float) $row['a_payer'];
            $total_paye += (float) $row['paye'];
        }
    }
    $total_reste = $total_a_payer - $total_paye;
    $pourcentage = $total_a_payer > 0 ? round(($total_paye / $total_a_payer) * 100, 2) : 0;

    ob_start();
    ?>
    <div class="mt-4" id="recap-stats-zone">
        <p class="text-body-secondary small mb-2">
            <span class="badge bg-primary"><?= htmlspecialchars($motif_info['motif']) ?></span>
            <?= date('d/m/Y', strtotime($motif_info['mois_debut'])) ?> — <?= date('d/m/Y', strtotime($motif_info['mois_fin'])) ?>
        </p>
        <div class="progress-container">
            <div class="progress-bar-custom" style="width: <?= $pourcentage ?>%"><?= $pourcentage ?>%</div>
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
            <div class="table-responsive">
                <table class="table table-hover m-0" id="ma_table">
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
                        <?php foreach ($paiements as $index => $p):
                            $reste = $p['a_payer'] - $p['paye'];
                            if ($reste <= 0) {
                                $statut = 'Payé';
                                $badge_class = 'bg-success';
                            } elseif ($p['paye'] > 0) {
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
                            <td><?= $civilite . ' ' . html_entity_decode($p['nom_complet'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></td>
                            <td class="text-end"><?= number_format($p['a_payer'], 0, ',', ' ') ?> FCFA</td>
                            <td class="text-end"><?= number_format($p['paye'], 0, ',', ' ') ?> FCFA</td>
                            <td class="text-end"><?= number_format($reste, 0, ',', ' ') ?> FCFA</td>
                            <td><?= $p['date_paiement'] ? date('d/m/Y', strtotime($p['date_paiement'])) : '-' ?></td>
                            <td><span class="badge <?= $badge_class ?>"><?= $statut ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($paiements)): ?>
                        <tr><td colspan="7" class="text-center text-body-tertiary">Aucun paiement pour ce motif.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function render_motifs_options_html($bdd, $annee, $selectedId)
{
    $motifs = recap_exceptionnels_motifs($bdd, $annee);
    $html = '<option value="">-- Choisir un motif --</option>';
    foreach ($motifs as $id => $data) {
        $sel = ((int) $selectedId === (int) $id) ? ' selected' : '';
        $html .= '<option value="' . (int) $id . '"' . $sel . '>'
            . htmlspecialchars($data['motif']) . ' ('
            . htmlspecialchars($data['debut_format']) . ' - '
            . htmlspecialchars($data['fin_format']) . ')</option>';
    }
    return $html;
}
