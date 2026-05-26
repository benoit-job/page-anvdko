<?php
session_start(); 
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");

$date_now = date("Y-m-d H:i:s");

function moisToDate($mois_str) {
    $mois_fr = [
        'janvier' => '01', 'février' => '02', 'mars' => '03', 'avril' => '04',
        'mai' => '05', 'juin' => '06', 'juillet' => '07', 'août' => '08',
        'septembre' => '09', 'octobre' => '10', 'novembre' => '11', 'décembre' => '12'
    ];
    $parts = explode(' ', strtolower($mois_str));
    if (count($parts) == 2 && isset($mois_fr[$parts[0]]) && is_numeric($parts[1])) {
        return $parts[1] . '-' . $mois_fr[$parts[0]];
    }
    return null;
}

if (!isset($_SESSION["membre"])) die(json_encode(["success" => false, "message" => "Session invalide"]));

$id_membre = $_SESSION["membre"]["id"];
$id_utilisateur = $_SESSION["utilisateur"]["id"] ?? 0;
$id_configuration = $_SESSION["configuration"]["id"] ?? 0;

if (isset($_POST['paiements']) && is_array($_POST['paiements'])) {
    $montant_mensuel = floatval($_POST['montant_mensuel'] ?? $_SESSION["configuration"]["montant_mensuel"] ?? '');

    foreach ($_POST['paiements'] as $mois_str => $paye) {
        $mois_payer = moisToDate($mois_str);
        if (!$mois_payer) continue;

        $paye = floatval($paye);
        $reste = max($montant_mensuel - $paye, 0);
        $statut = $paye == 0 ? 'Non payé' : ($reste > 0 ? 'Moitié payé' : 'Payé');

        $sql_check = "SELECT id FROM paiements WHERE id_membre = '$id_membre' AND mois_payer = '$mois_payer'";
        $res_check = mysqli_query($bdd, $sql_check);

        if (mysqli_num_rows($res_check) > 0) {
            $sql = "UPDATE paiements SET paye='$paye', reste='$reste', statut='$statut', date_heure='$date_now' WHERE id_membre='$id_membre' AND mois_payer='$mois_payer'";
        } else {
            $sql = "INSERT INTO paiements (id_configuration, id_utilisateur, id_membre, mois_payer, a_payer, paye, reste, statut, date_heure) VALUES ('$id_configuration', '$id_utilisateur', '$id_membre', '$mois_payer', '$montant_mensuel', '$paye', '$reste', '$statut', '$date_now')";
        }
        mysqli_query($bdd, $sql);
    }

    echo json_encode(["success" => true]);
    exit;
}

// Traitement "Tout payer"
if (isset($_POST['tout_payer']) && $_POST['tout_payer'] === '1') {
    $montant_mensuel = floatval($_POST['montant_mensuel'] ?? $_SESSION["configuration"]["montant_mensuel"] ?? 1000);
    $annee = date('Y');

    $query = "SELECT date_heure FROM membres WHERE id = '$id_membre'";
    $res = mysqli_query($bdd, $query);
    $inscription = mysqli_fetch_assoc($res)['date_heure'];
    $mois_start = (int)date('n', strtotime($inscription)) + 1;
    if ($mois_start > 12) {
        $mois_start = 1;
    }

    for ($m = $mois_start; $m <= 12; $m++) {
        if ($m == 4) continue; // Ignorer le mois d'avril (neutre)
        $mois_payer = $annee . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);

        $sql_check = "SELECT id FROM paiements WHERE id_membre = '$id_membre' AND mois_payer = '$mois_payer'";
        $res_check = mysqli_query($bdd, $sql_check);

        if (mysqli_num_rows($res_check) > 0) {
            $sql = "UPDATE paiements SET paye='$montant_mensuel', reste='0', statut='Payé', date_heure='$date_now' WHERE id_membre='$id_membre' AND mois_payer='$mois_payer'";
        } else {
            $sql = "INSERT INTO paiements (id_configuration, id_utilisateur, id_membre, mois_payer, a_payer, paye, reste, statut, date_heure) VALUES ('$id_configuration', '$id_utilisateur', '$id_membre', '$mois_payer', '$montant_mensuel', '$montant_mensuel', '0', 'Payé', '$date_now')";
        }
        mysqli_query($bdd, $sql);
    }

    echo json_encode(["success" => true]);
    exit;
}

http_response_code(400);
echo json_encode(["success" => false, "message" => "Requête invalide"]);
exit;
?>
