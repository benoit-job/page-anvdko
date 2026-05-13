<?php
header('Content-Type: application/json');
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");

$id_membre_encrypted = $_GET['id_membre'] ?? '';
$id_motif = intval($_GET['id_motif'] ?? 0);

if (empty($id_membre_encrypted) || $id_motif <= 0) {
    echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
    exit;
}

$id_membre = crypt_decrypt_chaine(trim($id_membre_encrypted), 'D');
if (!is_numeric($id_membre)) {
    echo json_encode(['success' => false, 'message' => 'ID membre invalide']);
    exit;
}

$id_membre = intval($id_membre);

// Récupérer le statut membre_bureau et le genre du membre
$sql_membre = "SELECT membre_bureau, genre FROM membres WHERE id = $id_membre";
$res_membre = mysqli_query($bdd, $sql_membre);
$membre_data = $res_membre ? mysqli_fetch_assoc($res_membre) : null;
$is_membre_bureau = isset($membre_data['membre_bureau']) && $membre_data['membre_bureau'] == 1;
$genre = isset($membre_data['genre']) ? $membre_data['genre'] : '';
$is_femme = ($genre == 'FEMME' || $genre == 'MADEMOISELLE');

// Récupérer les montants depuis la table config_cotisations_exceptionnelles
$sql_montant = "SELECT montant_standard, montant_homme, montant_femme, montant_mademoiselle, montant_bureau FROM config_cotisations_exceptionnelles WHERE id = $id_motif";
$res_montant = mysqli_query($bdd, $sql_montant);
$montant_data = $res_montant ? mysqli_fetch_assoc($res_montant) : null;
$montant_standard = $montant_data && !is_null($montant_data['montant_standard']) ? floatval($montant_data['montant_standard']) : null;
$montant_homme = $montant_data && !is_null($montant_data['montant_homme']) ? floatval($montant_data['montant_homme']) : null;
$montant_femme = $montant_data && !is_null($montant_data['montant_femme']) ? floatval($montant_data['montant_femme']) : null;
$montant_mademoiselle = $montant_data && !is_null($montant_data['montant_mademoiselle']) ? floatval($montant_data['montant_mademoiselle']) : null;
$montant_bureau = $montant_data && !is_null($montant_data['montant_bureau']) ? floatval($montant_data['montant_bureau']) : null;

// Déterminer le montant à utiliser selon la priorité :
// 1. Si membre du bureau ET montant_bureau défini → montant_bureau
// 2. Si genre = HOMME ET montant_homme défini → montant_homme
// 3. Si genre = FEMME ET montant_femme défini → montant_femme
// 4. Si genre = MADEMOISELLE ET montant_mademoiselle défini → montant_mademoiselle
// 5. Sinon → montant_standard (peut être null)
$montant_a_payer = $montant_standard;
if ($is_membre_bureau && $montant_bureau !== null) {
    $montant_a_payer = $montant_bureau;
} elseif ($genre == 'HOMME' && $montant_homme !== null) {
    $montant_a_payer = $montant_homme;
} elseif ($genre == 'FEMME' && $montant_femme !== null) {
    $montant_a_payer = $montant_femme;
} elseif ($genre == 'MADEMOISELLE' && $montant_mademoiselle !== null) {
    $montant_a_payer = $montant_mademoiselle;
}

// Si aucun montant n'est trouvé, utiliser 0 par défaut
if ($montant_a_payer === null) {
    $montant_a_payer = 0;
}

echo json_encode([
    'success' => true,
    'montant_a_payer' => $montant_a_payer,
    'montant_standard' => $montant_standard,
    'montant_homme' => $montant_homme,
    'montant_femme' => $montant_femme,
    'montant_mademoiselle' => $montant_mademoiselle,
    'montant_bureau' => $montant_bureau,
    'is_membre_bureau' => $is_membre_bureau,
    'genre' => $genre
]);
?>

