<?php
session_start();
include("../../include/php/connexion_bdd.php");
include("../../include/php/fonctions.php");
include("../../include/php/recap_exceptionnels_render.php");

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['utilisateur'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$annee = isset($_POST['annee_exceptionnelle']) ? (int) $_POST['annee_exceptionnelle'] : (int) date('Y');
$id_motif = isset($_POST['id_motif']) ? (int) $_POST['id_motif'] : 0;

$_SESSION['annee_exceptionnelle'] = $annee;
if ($id_motif > 0) {
    $_SESSION['id_motif'] = $id_motif;
}

$motifs = recap_exceptionnels_motifs($bdd, $annee);
if ($id_motif <= 0 && !empty($motifs)) {
    $id_motif = (int) array_key_first($motifs);
    $_SESSION['id_motif'] = $id_motif;
}

echo json_encode([
    'success' => true,
    'annee' => $annee,
    'id_motif' => $id_motif,
    'motifs_options_html' => render_motifs_options_html($bdd, $annee, $id_motif),
    'content_html' => render_recap_exceptionnels_html($bdd, $annee, $id_motif),
]);
