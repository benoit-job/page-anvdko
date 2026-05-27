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
$motif_id = isset($_POST['type_paiement']) ? $_POST['type_paiement'] : ''; // ID du motif
$recherche_membre = isset($_POST['membre']) ? mysqli_real_escape_string($bdd, trim($_POST['membre'])) : '';

$current_year = (int) date('Y');

$periode_expr = "COALESCE(NULLIF(NULLIF(ep.date_paiement, '0000-00-00'), '0000-00-00 00:00:00'), STR_TO_DATE(CONCAT(LEFT(ep.mois_payer, 7), '-01'), '%Y-%m-%d'), STR_TO_DATE(CONCAT(LEFT(c.mois_debut, 7), '-01'), '%Y-%m-%d'), DATE(c.date_creation))";

// Conditions de filtrage pour les cotisations exceptionnelles
// On se base sur la date_paiement pour les filtres si elle existe, sinon sur mois_debut du motif (approximatif)
// On va plutôt filtrer sur date_paiement si elle est renseignée.

$cond = "1=1";

if ($annee != '') {
    $cond .= " AND YEAR($periode_expr) = " . (int)$annee;
}
if ($mois != '') {
    $cond .= " AND MONTH($periode_expr) = " . (int)$mois;
}
if ($trimestre != '') {
    $cond .= " AND QUARTER($periode_expr) = " . (int)$trimestre;
}
if ($semestre != '') {
    if ($semestre == '1') {
        $cond .= " AND MONTH($periode_expr) <= 6";
    } else {
        $cond .= " AND MONTH($periode_expr) > 6";
    }
}
if ($date_debut != '') {
    $cond .= " AND DATE($periode_expr) >= '" . mysqli_real_escape_string($bdd, $date_debut) . "'";
}
if ($date_fin != '') {
    $cond .= " AND DATE($periode_expr) <= '" . mysqli_real_escape_string($bdd, $date_fin) . "'";
}

if ($motif_id != '') {
    $cond .= " AND ep.id_motif = " . (int)$motif_id;
}

if ($recherche_membre != '') {
    $words = explode(' ', $recherche_membre);
    $cond .= " AND (";
    $subconds = [];
    foreach ($words as $word) {
        if (trim($word) != '') {
            $sw = "%" . mysqli_real_escape_string($bdd, trim($word)) . "%";
            $subconds[] = "(m.nom LIKE '$sw' OR m.prenom LIKE '$sw')";
        }
    }
    $cond .= implode(' AND ', $subconds) . ")";
}

// ======================== STATISTIQUES GLOBALES ========================

function getMontantExcepStat($bdd, $periode_expr, $year_cond = "") {
    $sql = "SELECT SUM(ep.paye) as total, COUNT(NULLIF(ep.paye, 0)) as nb 
            FROM exceptionnels_pay ep 
            LEFT JOIN config_cotisations_exceptionnelles c ON ep.id_motif = c.id";
    if ($year_cond) {
        $sql .= " WHERE YEAR($periode_expr) $year_cond";
    }
    $res = mysqli_query($bdd, $sql);
    $data = mysqli_fetch_assoc($res);
    return ['total' => (float)$data['total'], 'nb' => (int)$data['nb']];
}

// 1. Cette année
$stat_annee = getMontantExcepStat($bdd, $periode_expr, "= $current_year");
// 2. 3 dernières années
$stat_3ans = getMontantExcepStat($bdd, $periode_expr, ">= " . ($current_year - 2));
// 3. 6 dernières années
$stat_6ans = getMontantExcepStat($bdd, $periode_expr, ">= " . ($current_year - 5));
// 4. Global total
$stat_global = getMontantExcepStat($bdd, $periode_expr);

// ======================== DONNEES FILTREES (TABLEAU ET GRAPHIQUES) ========================

$sql_excep = "SELECT 
                YEAR($periode_expr) as annee,
                MONTH($periode_expr) as mois,
                COALESCE(c.motif, CONCAT('Motif #', ep.id_motif)) as motif,
                SUM(ep.paye) as total,
                COUNT(NULLIF(ep.paye, 0)) as nb_paiements
            FROM exceptionnels_pay ep
            LEFT JOIN config_cotisations_exceptionnelles c ON ep.id_motif = c.id
            LEFT JOIN membres m ON ep.id_membre = m.id
            WHERE $cond AND ep.paye > 0
            GROUP BY annee, mois, motif";

$res_excep = mysqli_query($bdd, $sql_excep);

if (!$res_excep) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur SQL lors du chargement du recapitulatif : ' . mysqli_error($bdd)
    ]);
    exit;
}

$data_by_period = [];
$total_filtre_montant = 0;
$total_filtre_nb = 0;

$data_by_motif = []; // Pour le pie chart

while ($row = mysqli_fetch_assoc($res_excep)) {
    $y = $row['annee'];
    $m = $row['mois'];
    $motif = $row['motif'];
    $total = (float)$row['total'];
    $nb = (int)$row['nb_paiements'];
    
    $key = $y . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
    if (!isset($data_by_period[$key])) {
        $data_by_period[$key] = ['annee' => $y, 'mois' => $m, 'total' => 0, 'nb' => 0];
    }
    $data_by_period[$key]['total'] += $total;
    $data_by_period[$key]['nb'] += $nb;
    
    // Groupement par motif pour graphiques/tableau détaillé
    if (!isset($data_by_motif[$motif])) {
        $data_by_motif[$motif] = 0;
    }
    $data_by_motif[$motif] += $total;
    
    $total_filtre_montant += $total;
    $total_filtre_nb += $nb;
}

ksort($data_by_period); // Trier par date

$chart_annees = [];
$chart_montants = [];

$chart_mensuel_labels = [];
$chart_mensuel_montants = [];

$table_data = [];
$data_by_year = [];

$noms_mois = ["", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];

foreach ($data_by_period as $key => $d) {
    $y = $d['annee'];
    $m = (int)$d['mois'];
    
    if (!isset($data_by_year[$y])) {
        $data_by_year[$y] = 0;
    }
    $data_by_year[$y] += $d['total'];
    
    $label_mois = $noms_mois[$m] . ' ' . $y;
    $chart_mensuel_labels[] = $label_mois;
    $chart_mensuel_montants[] = $d['total'];
}

ksort($data_by_year);
foreach ($data_by_year as $y => $total_y) {
    $chart_annees[] = $y;
    $chart_montants[] = $total_y;
}

// Pour le tableau détaillé, on va faire une requête un peu différente pour avoir les lignes précises : par Année et Motif
$sql_table = "SELECT 
                YEAR($periode_expr) as annee,
                COALESCE(c.motif, CONCAT('Motif #', ep.id_motif)) as motif,
                COUNT(NULLIF(ep.paye, 0)) as nb,
                SUM(ep.paye) as total
            FROM exceptionnels_pay ep
            LEFT JOIN config_cotisations_exceptionnelles c ON ep.id_motif = c.id
            LEFT JOIN membres m ON ep.id_membre = m.id
            WHERE $cond AND ep.paye > 0
            GROUP BY annee, motif
            ORDER BY annee DESC, motif ASC";

$res_table = mysqli_query($bdd, $sql_table);
if (!$res_table) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur SQL lors du chargement du tableau : ' . mysqli_error($bdd)
    ]);
    exit;
}
while ($row = mysqli_fetch_assoc($res_table)) {
    $table_data[] = [
        'annee' => $row['annee'],
        'type_paiement' => $row['motif'],
        'nombre' => $row['nb'],
        'montant' => $row['total']
    ];
}

// Liste des motifs pour le filtre dropdown
$sql_motifs_all = "SELECT id, motif FROM config_cotisations_exceptionnelles ORDER BY motif";
$res_motifs_all = mysqli_query($bdd, $sql_motifs_all);
$options_motifs = '<option value="">Tous les motifs</option>';
while ($row = mysqli_fetch_assoc($res_motifs_all)) {
    $options_motifs .= '<option value="' . $row['id'] . '">' . htmlspecialchars($row['motif']) . '</option>';
}

echo json_encode([
    'success' => true,
    'options_motifs' => $options_motifs,
    'stats_rapides' => [
        'annee_courante' => $stat_annee,
        'trois_ans' => $stat_3ans,
        'six_ans' => $stat_6ans,
        'global' => $stat_global
    ],
    'bilan_filtre' => [
        'montant' => $total_filtre_montant,
        'nombre' => $total_filtre_nb
    ],
    'charts' => [
        'annuel' => [
            'labels' => $chart_annees,
            'montants' => $chart_montants
        ],
        'mensuel' => [
            'labels' => $chart_mensuel_labels,
            'montants' => $chart_mensuel_montants
        ],
        'repartition' => [
            'labels' => array_keys($data_by_motif),
            'montants' => array_values($data_by_motif)
        ]
    ],
    'table_data' => $table_data
]);
