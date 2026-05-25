<?php

function cotisations_ref_dates($annee)
{
    $annee = (int) $annee;
    $nowY = (int) date('Y');
    $mois1 = ($annee === $nowY) ? date('Y-m') : ($annee . '-12');
    $mois2 = date('Y-m', strtotime($mois1 . '-01 -1 month'));
    $mois3 = date('Y-m', strtotime($mois1 . '-01 -2 month'));
    $moisListSix = [];
    for ($i = 0; $i < 6; $i++) {
        $moisListSix[] = date('Y-m', strtotime($mois1 . '-01 -' . $i . ' month'));
    }
    return [
        'annee' => $annee,
        'mois1' => $mois1,
        'mois2' => $mois2,
        'mois3' => $mois3,
        'mois_list_six' => $moisListSix,
    ];
}

function stats_paiements_mensuels_where($bdd, $whereSql)
{
    $sql = "SELECT 
              SUM(CASE WHEN LOWER(TRIM(statut)) IN ('payé','paye') THEN paye ELSE 0 END) AS total_paye,
              SUM(CASE WHEN LOWER(TRIM(statut)) IN ('moitié payé','moitie paye','moitié paye') THEN paye ELSE 0 END) AS total_partiel
            FROM paiements 
            WHERE ($whereSql) AND mois_payer != 'total :'";
    $result = $bdd->query($sql);
    $data = $result ? $result->fetch_assoc() : [];
    $paye = (float) ($data['total_paye'] ?? 0);
    $partiel = (float) ($data['total_partiel'] ?? 0);
    return [
        'paye' => $paye,
        'partiel' => $partiel,
        'total' => $paye + $partiel,
    ];
}

function stats_adhesion_where($bdd, $whereSql)
{
    $sql = "SELECT 
              SUM(CASE WHEN LOWER(TRIM(statut)) IN ('payé','paye') THEN montant ELSE 0 END) AS total_paye,
              SUM(CASE WHEN LOWER(TRIM(statut)) IN ('moitié payé','moitie paye','moitié paye') THEN montant ELSE 0 END) AS total_partiel
            FROM adhesion 
            WHERE ($whereSql)";
    $result = $bdd->query($sql);
    $data = $result ? $result->fetch_assoc() : [];
    $paye = (float) ($data['total_paye'] ?? 0);
    $partiel = (float) ($data['total_partiel'] ?? 0);
    return [
        'paye' => $paye,
        'partiel' => $partiel,
        'total' => $paye + $partiel,
    ];
}

function stats_exceptionnels_where($bdd, $whereSql)
{
    $sql = "SELECT 
              SUM(CASE WHEN paye >= a_payer OR (a_payer > 0 AND reste <= 0) THEN paye ELSE 0 END) AS total_paye,
              SUM(CASE WHEN paye > 0 AND paye < a_payer THEN paye ELSE 0 END) AS total_partiel
            FROM exceptionnels_pay 
            WHERE ($whereSql)";
    $result = $bdd->query($sql);
    $data = $result ? $result->fetch_assoc() : [];
    $paye = (float) ($data['total_paye'] ?? 0);
    $partiel = (float) ($data['total_partiel'] ?? 0);
    return [
        'paye' => $paye,
        'partiel' => $partiel,
        'total' => $paye + $partiel,
    ];
}

function get_cotisations_stats_bundle($bdd, $annee)
{
    $refs = cotisations_ref_dates($annee);
    $mois1 = mysqli_real_escape_string($bdd, $refs['mois1']);
    $mois2 = mysqli_real_escape_string($bdd, $refs['mois2']);
    $mois3 = mysqli_real_escape_string($bdd, $refs['mois3']);
    $anneeEsc = (int) $refs['annee'];
    $moisListStr = "'" . implode("','", array_map(function ($m) use ($bdd) {
        return mysqli_real_escape_string($bdd, $m);
    }, $refs['mois_list_six'])) . "'";

    $debutMois1 = $mois1 . '-01';
    $finMois1 = date('Y-m-d', strtotime($debutMois1 . ' +1 month'));
    $debutMois3 = $mois3 . '-01';

    return [
        'annee' => $anneeEsc,
        'mois_ref' => $refs['mois1'],
        'adhesion' => [
            'mois' => stats_adhesion_where($bdd, "DATE(date_heure) >= '$debutMois1' AND DATE(date_heure) < '$finMois1'"),
            'trois_mois' => stats_adhesion_where($bdd, "DATE(date_heure) >= '$debutMois3' AND DATE(date_heure) < '$finMois1'"),
            'annee' => stats_adhesion_where($bdd, "YEAR(date_heure) = $anneeEsc"),
        ],
        'mensuelle' => [
            'mois' => stats_paiements_mensuels_where($bdd, "mois_payer = '$mois1'"),
            'trois_mois' => stats_paiements_mensuels_where($bdd, "mois_payer IN ('$mois1','$mois2','$mois3')"),
            'six_mois' => stats_paiements_mensuels_where($bdd, "mois_payer IN ($moisListStr)"),
            'annee' => stats_paiements_mensuels_where($bdd, "mois_payer LIKE '$anneeEsc-%'"),
        ],
        'exceptionnelle' => [
            'mois' => stats_exceptionnels_where($bdd, "DATE(date_paiement) >= '$debutMois1' AND DATE(date_paiement) < '$finMois1'"),
            'trois_mois' => stats_exceptionnels_where($bdd, "DATE(date_paiement) >= '$debutMois3' AND DATE(date_paiement) < '$finMois1'"),
            'annee' => stats_exceptionnels_where($bdd, "YEAR(date_paiement) = $anneeEsc"),
        ],
    ];
}

function format_stats_montants_html($stats)
{
    return [
        'paye' => formatMontant($stats['paye']),
        'partiel' => formatMontant($stats['partiel']),
        'total' => formatMontant($stats['total']),
    ];
}

function render_recap_cards_row($titres, $statsByPeriod, $periodKeys)
{
    $html = '<div class="row mt-2">';
    foreach ($periodKeys as $key => $label) {
        $s = format_stats_montants_html($statsByPeriod[$key]);
        $html .= '<div class="col-sm-6 col-md-4 p-1">
            <div class="card text-dark h-100">
                <div class="card-body text-center p-2">
                    <div><b>' . htmlspecialchars($label) . '</b></div>
                    <div class="d-flex align-items-center mt-1">
                        <div class="me-auto">
                            <span class="fa-solid fa-circle text-primary" style="position:relative;bottom:-2px;"></span>
                            <span class="fw-bold fs-9 text-body lh-2">Payé</span>
                        </div>
                        <div class="fw-bold fs-9 text-body lh-2">' . $s['paye'] . '</div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="me-auto">
                            <span class="fa-solid fa-circle text-warning" style="position:relative;bottom:-2px;"></span>
                            <span class="fw-bold fs-9 text-body lh-2">Moitié payé</span>
                        </div>
                        <div class="fw-bold fs-9 text-body lh-2">' . $s['partiel'] . '</div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="me-auto">
                            <span class="fa-solid fa-circle text-success" style="position:relative;bottom:-2px;"></span>
                            <span class="fw-bold fs-9 text-body lh-2">Total</span>
                        </div>
                        <div class="fw-bold fs-9 text-body lh-2">' . $s['total'] . '</div>
                    </div>
                </div>
            </div>
        </div>';
    }
    $html .= '</div>';
    return $html;
}
