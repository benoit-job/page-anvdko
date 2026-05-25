<?php
session_start();
include("../../include/php/connexion_bdd.php");
include("../../include/php/fonctions.php");
include("../../include/php/cotisations_recap.php");

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['utilisateur'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$annee = isset($_GET['annee']) ? (int) $_GET['annee'] : (int) date('Y');
if ($annee < 2000 || $annee > 2100) {
    $annee = (int) date('Y');
}

$bundle = get_cotisations_stats_bundle($bdd, $annee);

$periods = [
    'mois' => 'Ce mois',
    'trois_mois' => 'Trois (3) mois',
    'annee' => 'Année ' . $annee,
];

$html = [
    'adhesion' => render_recap_cards_row('Adhésion', $bundle['adhesion'], $periods),
    'mensuelle' => render_recap_cards_row('Mensuelle', $bundle['mensuelle'], array_merge($periods, ['six_mois' => 'Six (6) mois'])),
    'exceptionnelle' => render_recap_cards_row('Exceptionnelle', $bundle['exceptionnelle'], $periods),
];

// Mensuelle : 4 cartes (avec six_mois)
$html['mensuelle'] = render_recap_cards_row('Mensuelle', $bundle['mensuelle'], [
    'mois' => 'Ce mois',
    'trois_mois' => 'Trois (3) mois',
    'six_mois' => 'Six (6) mois',
    'annee' => 'Année ' . $annee,
]);

echo json_encode([
    'success' => true,
    'annee' => $annee,
    'mois_ref' => $bundle['mois_ref'],
    'html' => $html,
    'stats' => $bundle,
]);
