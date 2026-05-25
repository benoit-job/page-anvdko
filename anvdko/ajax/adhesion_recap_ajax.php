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
$statsHtml = render_recap_cards_row('Adhésion', $bundle['adhesion'], [
    'mois' => 'Ce mois',
    'trois_mois' => 'Trois (3) mois',
    'annee' => 'Année ' . $annee,
]);

$anneeEsc = (int) $annee;
$sql = "SELECT a.id_adhesion, a.montant, a.statut, a.date_heure,
               m.num_adhesion, UPPER(CONCAT(m.nom, ' ', m.prenom)) AS nom_complet
        FROM adhesion a
        INNER JOIN membres m ON m.id = a.id_membre
        WHERE YEAR(a.date_heure) = $anneeEsc
        ORDER BY a.date_heure DESC, m.nom, m.prenom";
$res = mysqli_query($bdd, $sql);

$rowsHtml = '';
if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        $badge = (stripos($row['statut'], 'pay') !== false && stripos($row['statut'], 'non') === false)
            ? '<span class="badge bg-success">' . htmlspecialchars($row['statut']) . '</span>'
            : '<span class="badge bg-warning text-dark">' . htmlspecialchars($row['statut']) . '</span>';
        $rowsHtml .= '<tr>
            <td>' . htmlspecialchars($row['num_adhesion']) . '</td>
            <td>' . htmlspecialchars($row['nom_complet']) . '</td>
            <td class="text-end">' . number_format((float) $row['montant'], 0, ',', ' ') . ' FCFA</td>
            <td class="text-center">' . $badge . '</td>
            <td>' . date('d/m/Y H:i', strtotime($row['date_heure'])) . '</td>
        </tr>';
    }
} else {
    $rowsHtml = '<tr><td colspan="5" class="text-center text-body-tertiary">Aucune adhésion pour cette année.</td></tr>';
}

echo json_encode([
    'success' => true,
    'annee' => $annee,
    'stats_html' => $statsHtml,
    'table_html' => $rowsHtml,
]);
