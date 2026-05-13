<?php
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");
while (ob_get_level()) ob_end_clean();
header('Content-Type: application/json');

$required_fields = ['id_membre', 'id_motif', 'mois_payer', 'a_payer', 'paye', 'reste', 'date_paiement'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => 'Champ manquant: ' . $field]);
        exit;
    }
}

$id_membre = intval($_POST['id_membre']);
$id_motif = intval($_POST['id_motif']);
$mois_payer = mysqli_real_escape_string($bdd, $_POST['mois_payer']); 
$a_payer = floatval($_POST['a_payer']);
$paye = floatval($_POST['paye']);
$reste = floatval($_POST['reste']);
$date_paiement = mysqli_real_escape_string($bdd, $_POST['date_paiement']);

$check_sql = "SELECT id FROM exceptionnels_pay 
              WHERE id_membre = '$id_membre' 
              AND id_motif = '$id_motif'";
$check_res = mysqli_query($bdd, $check_sql);

if (mysqli_num_rows($check_res) > 0) {
    $row = mysqli_fetch_assoc($check_res);
    $update_sql = "UPDATE exceptionnels_pay 
                   SET paye = paye + $paye, 
                       reste = a_payer - (paye + $paye),
                       date_paiement = '$date_paiement'
                   WHERE id = " . $row['id'];
    
    if (mysqli_query($bdd, $update_sql)) {
        echo json_encode(['success' => true, 'message' => 'Paiement mis à jour avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour: ' . mysqli_error($bdd)]);
    }
} else {
    $insert_sql = "INSERT INTO exceptionnels_pay 
                  (id_membre, id_motif, mois_payer, a_payer, paye, reste, date_paiement) 
                  VALUES 
                  ('$id_membre', '$id_motif', '$mois_payer', '$a_payer', '$paye', '$reste', '$date_paiement')";
    
    if (mysqli_query($bdd, $insert_sql)) {
        echo json_encode(['success' => true, 'message' => 'Paiement enregistré avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement: ' . mysqli_error($bdd)]);
    }
}
?>