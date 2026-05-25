<?php
session_start();
include("../../include/php/connexion_bdd.php");
include("../../include/php/fonctions.php");
include("../../include/php/recap_mensuels_render.php");

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['utilisateur'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$annee = isset($_POST['annee']) ? (int) $_POST['annee'] : (isset($_GET['annee']) ? (int) $_GET['annee'] : (int) date('Y'));
$_SESSION['annee'] = $annee;

echo json_encode([
    'success' => true,
    'annee' => $annee,
    'content_html' => render_recap_mensuels_html($bdd, $annee),
]);
