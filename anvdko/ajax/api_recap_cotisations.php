<?php
session_start();
include("../../include/php/connexion_bdd.php");
include("../../include/php/fonctions.php");

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['utilisateur'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

// Récupération des filtres
$annee = isset($_POST['annee']) ? $_POST['annee'] : '';
$semestre = isset($_POST['semestre']) ? $_POST['semestre'] : '';
$trimestre = isset($_POST['trimestre']) ? $_POST['trimestre'] : '';
$mois = !empty($_POST['mois']) ? str_pad($_POST['mois'], 2, '0', STR_PAD_LEFT) : '';
$date_debut = isset($_POST['date_debut']) ? $_POST['date_debut'] : '';
$date_fin = isset($_POST['date_fin']) ? $_POST['date_fin'] : '';
$type_paiement = isset($_POST['type_paiement']) ? $_POST['type_paiement'] : ''; // '' = tout, 'cotisation', 'adhesion'
$recherche_membre = isset($_POST['membre']) ? mysqli_real_escape_string($bdd, trim($_POST['membre'])) : '';

$current_year = (int) date('Y');

// Dates normalisees pour les regroupements et les filtres.
// Si mois_payer n'est pas au format YYYY-MM, on retombe sur date_heure.
$paiement_date_expr = "COALESCE(STR_TO_DATE(CONCAT(LEFT(p.mois_payer, 7), '-01'), '%Y-%m-%d'), DATE(p.date_heure))";
$depense_date_expr = "COALESCE(dm.date_paiement, d.date_depense)";

// Conditions de filtrage pour les cotisations (table paiements)
$cond_paiements = "1=1";
$cond_adhesions = "1=1";
$cond_depenses = "1=1";

if ($annee != '') {
    $cond_paiements .= " AND YEAR($paiement_date_expr) = " . (int)$annee;
    $cond_adhesions .= " AND YEAR(a.date_heure) = " . (int)$annee;
    $cond_depenses .= " AND YEAR($depense_date_expr) = " . (int)$annee;
}
if ($mois != '') {
    $cond_paiements .= " AND MONTH($paiement_date_expr) = " . (int)$mois;
    $cond_adhesions .= " AND MONTH(a.date_heure) = " . (int)$mois;
    $cond_depenses .= " AND MONTH($depense_date_expr) = " . (int)$mois;
}
if ($trimestre != '') {
    $cond_paiements .= " AND QUARTER($paiement_date_expr) = " . (int)$trimestre;
    $cond_adhesions .= " AND QUARTER(a.date_heure) = " . (int)$trimestre;
    $cond_depenses .= " AND QUARTER($depense_date_expr) = " . (int)$trimestre;
}
if ($semestre != '') {
    if ($semestre == '1') {
        $cond_paiements .= " AND MONTH($paiement_date_expr) <= 6";
        $cond_adhesions .= " AND MONTH(a.date_heure) <= 6";
        $cond_depenses .= " AND MONTH($depense_date_expr) <= 6";
    } else {
        $cond_paiements .= " AND MONTH($paiement_date_expr) > 6";
        $cond_adhesions .= " AND MONTH(a.date_heure) > 6";
        $cond_depenses .= " AND MONTH($depense_date_expr) > 6";
    }
}
if ($date_debut != '') {
    $cond_paiements .= " AND DATE($paiement_date_expr) >= '" . mysqli_real_escape_string($bdd, $date_debut) . "'";
    $cond_adhesions .= " AND DATE(a.date_heure) >= '" . mysqli_real_escape_string($bdd, $date_debut) . "'";
    $cond_depenses .= " AND DATE($depense_date_expr) >= '" . mysqli_real_escape_string($bdd, $date_debut) . "'";
}
if ($date_fin != '') {
    // pour le mois payer, on prend le dernier jour du mois
    $cond_paiements .= " AND DATE(LAST_DAY($paiement_date_expr)) <= '" . mysqli_real_escape_string($bdd, $date_fin) . "'";
    $cond_adhesions .= " AND DATE(a.date_heure) <= '" . mysqli_real_escape_string($bdd, $date_fin) . "'";
    $cond_depenses .= " AND DATE($depense_date_expr) <= '" . mysqli_real_escape_string($bdd, $date_fin) . "'";
}

// Recherche membre (ne s'applique qu'aux recettes, pas aux dépenses)
if ($recherche_membre != '') {
    $search_str = "%" . $recherche_membre . "%";
    $cond_paiements .= " AND (m.nom LIKE '$search_str' OR m.prenom LIKE '$search_str')";
    $cond_adhesions .= " AND (m.nom LIKE '$search_str' OR m.prenom LIKE '$search_str')";
}

// ======================== STATISTIQUES GLOBALES ========================

function getMontantStat($bdd, $table, $sum_col, $date_col, $date_format, $year_cond = "") {
    $sql = "SELECT SUM($sum_col) as total FROM $table";
    if ($year_cond) {
        if ($date_format === 'Y-m') {
            $sql .= " WHERE YEAR(STR_TO_DATE($date_col, '%Y-%m')) $year_cond";
        } else {
            $sql .= " WHERE YEAR($date_col) $year_cond";
        }
    }
    $res = mysqli_query($bdd, $sql);
    $data = mysqli_fetch_assoc($res);
    return $data['total'] ? (float)$data['total'] : 0;
}

// 1. Cette année
$cotisations_cette_annee = getMontantStat($bdd, 'paiements', 'paye', 'mois_payer', 'Y-m', "= $current_year");
$adhesions_cette_annee = getMontantStat($bdd, 'adhesion', 'montant', 'date_heure', 'Y-m-d H:i:s', "= $current_year");

// 2. 3 dernières années
$cotisations_3ans = getMontantStat($bdd, 'paiements', 'paye', 'mois_payer', 'Y-m', ">= " . ($current_year - 2));
$adhesions_3ans = getMontantStat($bdd, 'adhesion', 'montant', 'date_heure', 'Y-m-d H:i:s', ">= " . ($current_year - 2));

// 3. 6 dernières années
$cotisations_6ans = getMontantStat($bdd, 'paiements', 'paye', 'mois_payer', 'Y-m', ">= " . ($current_year - 5));
$adhesions_6ans = getMontantStat($bdd, 'adhesion', 'montant', 'date_heure', 'Y-m-d H:i:s', ">= " . ($current_year - 5));

// 4. Global total
$cotisations_total = getMontantStat($bdd, 'paiements', 'paye', 'mois_payer', 'Y-m');
$adhesions_total = getMontantStat($bdd, 'adhesion', 'montant', 'date_heure', 'Y-m-d H:i:s');

// Nb Membres
$res_membres = mysqli_query($bdd, "SELECT COUNT(*) as nb FROM membres");
$membres_actifs = mysqli_fetch_assoc($res_membres)['nb'];

// ======================== DONNEES FILTREES (TABLEAU ET GRAPHIQUES) ========================

// Recettes Cotisations
$sql_cot = "SELECT YEAR($paiement_date_expr) as annee,
                   MONTH($paiement_date_expr) as mois,
                   SUM(p.paye) as total
            FROM paiements p
            LEFT JOIN membres m ON p.id_membre = m.id
            WHERE $cond_paiements AND p.paye > 0";
if ($type_paiement == 'adhesion') $sql_cot .= " AND 1=0 "; // Ne pas compter si on veut juste adhésion
$sql_cot .= " GROUP BY annee, mois";
$res_cot = mysqli_query($bdd, $sql_cot);

// Recettes Adhésions
$sql_adh = "SELECT YEAR(a.date_heure) as annee, 
                   MONTH(a.date_heure) as mois,
                   SUM(a.montant) as total
            FROM adhesion a
            LEFT JOIN membres m ON a.id_membre = m.id
            WHERE $cond_adhesions
              AND a.montant > 0
              AND LOWER(TRIM(a.statut)) NOT LIKE 'non%'
              AND LOWER(TRIM(a.statut)) LIKE '%pay%'";
if ($type_paiement == 'cotisation') $sql_adh .= " AND 1=0 "; // Ne pas compter si on veut juste cotisation
$sql_adh .= " GROUP BY annee, mois";
$res_adh = mysqli_query($bdd, $sql_adh);

// Dépenses
$sql_dep = "SELECT YEAR($depense_date_expr) as annee,
                   MONTH($depense_date_expr) as mois,
                   COALESCE(SUM(dm.montant), 0) as total
            FROM depenses_anvdko d
            LEFT JOIN depense_montants dm ON d.id_depense = dm.id_depense
            WHERE $cond_depenses AND dm.montant > 0";
// Si filtre membre est actif ou type_paiement, on ne montre pas les dépenses, car elles n'y sont pas liées
if ($recherche_membre != '' || $type_paiement != '') {
    $sql_dep .= " AND 1=0";
}
$sql_dep .= " GROUP BY annee, mois";
$res_dep = mysqli_query($bdd, $sql_dep);

if (!$res_cot || !$res_adh || !$res_dep) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur SQL lors du chargement du recapitulatif : ' . mysqli_error($bdd)
    ]);
    exit;
}

// Agrégation par Année/Mois
$data_by_period = [];

while ($row = mysqli_fetch_assoc($res_cot)) {
    $key = $row['annee'] . '-' . str_pad($row['mois'], 2, '0', STR_PAD_LEFT);
    if (!isset($data_by_period[$key])) $data_by_period[$key] = ['annee' => $row['annee'], 'mois' => $row['mois'], 'cotisations' => 0, 'adhesions' => 0, 'depenses' => 0];
    $data_by_period[$key]['cotisations'] += (float)$row['total'];
}
while ($row = mysqli_fetch_assoc($res_adh)) {
    $key = $row['annee'] . '-' . str_pad($row['mois'], 2, '0', STR_PAD_LEFT);
    if (!isset($data_by_period[$key])) $data_by_period[$key] = ['annee' => $row['annee'], 'mois' => $row['mois'], 'cotisations' => 0, 'adhesions' => 0, 'depenses' => 0];
    $data_by_period[$key]['adhesions'] += (float)$row['total'];
}
while ($row = mysqli_fetch_assoc($res_dep)) {
    $key = $row['annee'] . '-' . str_pad($row['mois'], 2, '0', STR_PAD_LEFT);
    if (!isset($data_by_period[$key])) $data_by_period[$key] = ['annee' => $row['annee'], 'mois' => $row['mois'], 'cotisations' => 0, 'adhesions' => 0, 'depenses' => 0];
    $data_by_period[$key]['depenses'] += (float)$row['total'];
}

ksort($data_by_period); // Trier par date

// Données pour le tableau et les graphiques
$chart_annees = [];
$chart_cotisations = [];
$chart_adhesions = [];
$chart_depenses = [];

$chart_mensuel_labels = [];
$chart_mensuel_recettes = [];
$chart_mensuel_depenses = [];
$chart_mensuel_solde = [];

$total_filtre_cotisations = 0;
$total_filtre_adhesions = 0;
$total_filtre_depenses = 0;

$table_data = [];
$data_by_year = [];

// Liste des mois pour affichage
$noms_mois = ["", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];

foreach ($data_by_period as $key => $d) {
    $y = $d['annee'];
    $m = (int)$d['mois'];
    
    // Pour agrégation annuelle (Graphique 1 & Table si pas de filtre précis, on fait les deux)
    if (!isset($data_by_year[$y])) {
        $data_by_year[$y] = ['annee' => $y, 'cotisations' => 0, 'adhesions' => 0, 'depenses' => 0];
    }
    $data_by_year[$y]['cotisations'] += $d['cotisations'];
    $data_by_year[$y]['adhesions'] += $d['adhesions'];
    $data_by_year[$y]['depenses'] += $d['depenses'];
    
    // Pour évolution mensuelle
    $label_mois = $noms_mois[$m] . ' ' . $y;
    $chart_mensuel_labels[] = $label_mois;
    $recettes_mois = $d['cotisations'] + $d['adhesions'];
    $chart_mensuel_recettes[] = $recettes_mois;
    $chart_mensuel_depenses[] = $d['depenses'];
    $chart_mensuel_solde[] = $recettes_mois - $d['depenses'];
    
    // Pour tableau détaillé mensuel
    $solde_mois = $recettes_mois - $d['depenses'];
    $table_data[] = [
        'periode' => $label_mois,
        'cotisations' => $d['cotisations'],
        'adhesions' => $d['adhesions'],
        'depenses' => $d['depenses'],
        'solde' => $solde_mois
    ];
    
    $total_filtre_cotisations += $d['cotisations'];
    $total_filtre_adhesions += $d['adhesions'];
    $total_filtre_depenses += $d['depenses'];
}

// Préparer données annuelles pour graphique 1
ksort($data_by_year);
foreach ($data_by_year as $y => $dy) {
    $chart_annees[] = $y;
    $chart_cotisations[] = $dy['cotisations'];
    $chart_adhesions[] = $dy['adhesions'];
    $chart_depenses[] = $dy['depenses'];
}

// Si la période filtrée couvre plusieurs années et aucun mois spécifié, on affiche par année dans la table principale
if (count($data_by_year) > 1 && $mois == '' && $semestre == '' && $trimestre == '') {
    $table_data = []; // reset
    foreach ($data_by_year as $y => $dy) {
        $solde = ($dy['cotisations'] + $dy['adhesions']) - $dy['depenses'];
        $table_data[] = [
            'periode' => $y,
            'cotisations' => $dy['cotisations'],
            'adhesions' => $dy['adhesions'],
            'depenses' => $dy['depenses'],
            'solde' => $solde
        ];
    }
}

// S'il n'y a pas de données du tout
if (empty($table_data)) {
    // Si table vide, on peut mettre des 0
}

$solde_restant = ($total_filtre_cotisations + $total_filtre_adhesions) - $total_filtre_depenses;

echo json_encode([
    'success' => true,
    'stats_rapides' => [
        'annee_courante' => [
            'cotisations' => $cotisations_cette_annee,
            'adhesions' => $adhesions_cette_annee,
            'total' => $cotisations_cette_annee + $adhesions_cette_annee,
            'membres' => $membres_actifs
        ],
        'trois_ans' => [
            'cotisations' => $cotisations_3ans,
            'adhesions' => $adhesions_3ans,
            'total' => $cotisations_3ans + $adhesions_3ans
        ],
        'six_ans' => [
            'cotisations' => $cotisations_6ans,
            'adhesions' => $adhesions_6ans,
            'total' => $cotisations_6ans + $adhesions_6ans
        ],
        'global' => [
            'cotisations' => $cotisations_total,
            'adhesions' => $adhesions_total,
            'total' => $cotisations_total + $adhesions_total
        ]
    ],
    'bilan_filtre' => [
        'cotisations' => $total_filtre_cotisations,
        'adhesions' => $total_filtre_adhesions,
        'recettes' => $total_filtre_cotisations + $total_filtre_adhesions,
        'depenses' => $total_filtre_depenses,
        'solde' => $solde_restant
    ],
    'charts' => [
        'annuel' => [
            'labels' => $chart_annees,
            'cotisations' => $chart_cotisations,
            'adhesions' => $chart_adhesions,
            'depenses' => $chart_depenses
        ],
        'mensuel' => [
            'labels' => $chart_mensuel_labels,
            'recettes' => $chart_mensuel_recettes,
            'depenses' => $chart_mensuel_depenses,
            'solde' => $chart_mensuel_solde
        ],
        'repartition' => [
            'cotisations' => $total_filtre_cotisations,
            'adhesions' => $total_filtre_adhesions,
            'depenses' => $total_filtre_depenses,
            'solde' => max(0, $solde_restant) // Pour le pie chart, pas de solde négatif
        ]
    ],
    'table_data' => $table_data
]);
