<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");

header('Content-Type: application/json');

// Récupérer les paramètres
$ids_cryptes = isset($_POST['ids']) ? $_POST['ids'] : '';
$id_motif = isset($_POST['id_motif']) ? intval($_POST['id_motif']) : 0;

if (empty($ids_cryptes) || $id_motif <= 0) {
    echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
    exit;
}

// Décrypter les IDs
$ids_array = explode(',', $ids_cryptes);
$ids_decryptes = [];

foreach ($ids_array as $id_crypte) {
    $id = crypt_decrypt_chaine(trim($id_crypte), 'D');
    if (is_numeric($id) && $id > 0) {
        $ids_decryptes[] = intval($id);
    }
}

if (empty($ids_decryptes)) {
    echo json_encode(['success' => false, 'message' => 'Aucun ID valide']);
    exit;
}

// Récupérer les montants de la configuration
$sql_config = "SELECT montant_standard, montant_homme, montant_femme, montant_mademoiselle, montant_bureau 
               FROM config_cotisations_exceptionnelles 
               WHERE id = $id_motif";
$res_config = mysqli_query($bdd, $sql_config);
$config = mysqli_fetch_assoc($res_config);

if (!$config) {
    echo json_encode(['success' => false, 'message' => 'Configuration introuvable']);
    exit;
}

$montant_standard = !is_null($config['montant_standard']) ? floatval($config['montant_standard']) : 0;
$montant_homme = !is_null($config['montant_homme']) ? floatval($config['montant_homme']) : null;
$montant_femme = !is_null($config['montant_femme']) ? floatval($config['montant_femme']) : null;
$montant_mademoiselle = !is_null($config['montant_mademoiselle']) ? floatval($config['montant_mademoiselle']) : null;
$montant_bureau = !is_null($config['montant_bureau']) ? floatval($config['montant_bureau']) : null;

// Récupérer les informations des membres
$ids_list = implode(',', $ids_decryptes);
$sql_membres = "SELECT id, 
                       CONCAT(
                           CASE 
                               WHEN genre = 'HOMME' THEN 'M' 
                               WHEN genre = 'FEMME' THEN 'Mme' 
                               WHEN genre = 'MADEMOISELLE' THEN 'Mlle' 
                               ELSE genre 
                           END, '. ', nom, ' ', prenom
                       ) AS nom_prenom,
                       genre,
                       membre_bureau
                FROM membres 
                WHERE id IN ($ids_list)";

$res_membres = mysqli_query($bdd, $sql_membres);

if (!$res_membres) {
    echo json_encode(['success' => false, 'message' => 'Erreur requête: ' . mysqli_error($bdd)]);
    exit;
}

$membres = [];
while ($membre = mysqli_fetch_assoc($res_membres)) {
    $is_membre_bureau = isset($membre['membre_bureau']) && $membre['membre_bureau'] == 1;
    $genre = $membre['genre'] ?? '';
    
    // Déterminer le montant à utiliser selon la priorité
    $montant_a_utiliser = $montant_standard;
    
    if ($is_membre_bureau && $montant_bureau !== null && $montant_bureau > 0) {
        $montant_a_utiliser = $montant_bureau;
    } elseif ($genre === 'HOMME' && $montant_homme !== null && $montant_homme > 0) {
        $montant_a_utiliser = $montant_homme;
    } elseif ($genre === 'FEMME' && $montant_femme !== null && $montant_femme > 0) {
        $montant_a_utiliser = $montant_femme;
    } elseif ($genre === 'MADEMOISELLE' && $montant_mademoiselle !== null && $montant_mademoiselle > 0) {
        $montant_a_utiliser = $montant_mademoiselle;
    }
    
    // Si aucun montant valide n'est trouvé, utiliser le montant standard (même s'il est 0)
    if ($montant_a_utiliser === null || $montant_a_utiliser <= 0) {
        $montant_a_utiliser = $montant_standard;
    }
    
    $membres[] = [
        'id_membre' => $membre['id'],
        'nom_prenom' => $membre['nom_prenom'],
        'genre' => $genre,
        'is_membre_bureau' => $is_membre_bureau,
        'montant_standard' => $montant_standard,
        'montant_homme' => $montant_homme,
        'montant_femme' => $montant_femme,
        'montant_mademoiselle' => $montant_mademoiselle,
        'montant_bureau' => $montant_bureau,
        'montant_a_payer' => $montant_a_utiliser
    ];
}

echo json_encode([
    'success' => true,
    'membres' => $membres,
    'count' => count($membres)
]);
?>