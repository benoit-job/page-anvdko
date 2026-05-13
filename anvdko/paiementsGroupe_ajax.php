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

$id_utilisateur = $_SESSION["utilisateur"]["id"] ?? 0;
$id_configuration = $_SESSION["configuration"]["id"] ?? 0;
$montant_mensuel = 1000; // Valeur par défaut

// Validation du montant
function validerMontant($montant) {
    // Vérifier si le montant est un nombre valide
    if (!is_numeric($montant)) {
        return [
            'success' => false,
            'errors' => ["Le format du montant est invalide"]
        ];
    }
    
    // Convertir en float en remplaçant les virgules par des points si nécessaire
    $montant = is_string($montant) ? str_replace(',', '.', $montant) : $montant;
    $montant = floatval($montant);
    
    // Arrondir à 2 décimales
    $montant = round($montant, 2);
    
    if ($montant <= 0) {
        return [
            'success' => false,
            'errors' => ["Le montant doit être supérieur à 0"]
        ];
    }
    
    if ($montant > 10000) {
        return [
            'success' => false,
            'errors' => ["Le montant ne peut pas dépasser 10 000 FCFA"]
        ];
    }
    
    return [
        'success' => true,
        'montant' => $montant
    ];
}

header('Content-Type: application/json');

// Récupérez l'action
$action = $_POST['action'] ?? '';

// Nouveau traitement pour payer un mois pour plusieurs membres
if ($action === 'payer_mois' && isset($_POST['ids_membres'])) {
    error_log('Données reçues: ' . print_r($_POST, true));
    
    $ids_membres = array_map('intval', (array)$_POST['ids_membres']);
    $mois = trim($_POST['mois'] ?? '');
    
    // Récupération et nettoyage du montant
    $montant_str = str_replace(' ', '', $_POST['montant'] ?? '0');
    $montant = str_replace(',', '.', $montant_str);
    
    error_log("Montant brut: " . $montant);
    
    // Validation du montant
    $validation = validerMontant($montant);
    if (!$validation['success']) {
        error_log('Erreur de validation: ' . json_encode($validation));
        echo json_encode($validation);
        exit;
    }
    
    // Utiliser le montant validé et formaté
    $montant = $validation['montant'];
    error_log("Montant validé: " . $montant);
    
    $success = true;
    $errors = [];
    
    // Vérifier que le mois est valide (format YYYY-MM)
    if (!preg_match('/^\d{4}-\d{2}$/', $mois)) {
        echo json_encode([
            'success' => false,
            'errors' => ["Format de mois invalide. Utilisez le format AAAA-MM"]
        ]);
        exit;
    }
    
    // Vérifier qu'il y a des membres sélectionnés
    if (empty($ids_membres)) {
        echo json_encode([
            'success' => false,
            'errors' => ["Aucun membre sélectionné"]
        ]);
        exit;
    }
    
    // Démarrer une transaction pour garantir l'intégrité des données
    mysqli_begin_transaction($bdd);
    
    try {
        foreach ($ids_membres as $id_membre) {
            $reste = max($montant_mensuel - $montant, 0);
            $statut = $montant == 0 ? 'Non payé' : ($reste > 0 ? 'Moitié payé' : 'Payé');
            
            // Échapper les valeurs pour éviter les injections SQL
            $id_membre_esc = mysqli_real_escape_string($bdd, $id_membre);
            $mois_esc = mysqli_real_escape_string($bdd, $mois);
            $montant_esc = mysqli_real_escape_string($bdd, $montant);
            $reste_esc = mysqli_real_escape_string($bdd, $reste);
            $statut_esc = mysqli_real_escape_string($bdd, $statut);
            $date_now_esc = mysqli_real_escape_string($bdd, $date_now);
            
            $sql_check = "SELECT id FROM paiements WHERE id_membre = '$id_membre_esc' AND mois_payer = '$mois_esc'";
            $res_check = mysqli_query($bdd, $sql_check);
            
            if (!$res_check) {
                throw new Exception("Erreur lors de la vérification du paiement: " . mysqli_error($bdd));
            }

            if (mysqli_num_rows($res_check) > 0) {
                $sql = "UPDATE paiements SET 
                        paye = '$montant_esc', 
                        reste = '$reste_esc', 
                        statut = '$statut_esc', 
                        date_heure = '$date_now_esc'
                        WHERE id_membre = '$id_membre_esc' AND mois_payer = '$mois_esc'";
            } else {
                $sql = "INSERT INTO paiements 
                        (id_configuration, id_utilisateur, id_membre, mois_payer, a_payer, paye, reste, statut, date_heure) 
                        VALUES 
                        ('$id_configuration', '$id_utilisateur', '$id_membre_esc', '$mois_esc', '$montant_mensuel', '$montant_esc', '$reste_esc', '$statut_esc', '$date_now_esc')";
            }
            
            if (!mysqli_query($bdd, $sql)) {
                throw new Exception("Erreur lors de l'enregistrement du paiement pour le membre $id_membre: " . mysqli_error($bdd));
            }
        }
        
        // Tout s'est bien passé, on valide la transaction
        mysqli_commit($bdd);
        
    } catch (Exception $e) {
        // En cas d'erreur, on annule la transaction
        mysqli_rollback($bdd);
        $success = false;
        $errors[] = $e->getMessage();
        error_log('Erreur de transaction: ' . $e->getMessage());
    }
    
    echo json_encode([
        'success' => $success,
        'errors' => $errors
    ]);
    exit;
}

// Nouveau traitement pour tout payer pour plusieurs membres
if ($action === 'tout_payer' && isset($_POST['ids_membres'])) {
    $ids_membres = array_map('intval', (array)$_POST['ids_membres']);
    $annee = date('Y');
    $success = true;
    $errors = [];
    
    foreach ($ids_membres as $id_membre) {
        // Récupérer le mois d'inscription
        $query = "SELECT date_heure FROM membres WHERE id = '$id_membre'";
        $res = mysqli_query($bdd, $query);
        
        if (!$res) {
            $errors[] = "Erreur lors de la récupération de la date d'inscription pour le membre $id_membre";
            $success = false;
            continue;
        }
        
        $row = mysqli_fetch_assoc($res);
        $inscription = $row['date_heure'];
        $mois_start = (int)date('n', strtotime($inscription));
        
        // Payer tous les mois restants de l'année
        for ($m = $mois_start; $m <= 12; $m++) {
            $mois_payer = $annee . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
            
            $sql_check = "SELECT id FROM paiements WHERE id_membre = '$id_membre' AND mois_payer = '$mois_payer'";
            $res_check = mysqli_query($bdd, $sql_check);
            
            $sql = "";
            if (mysqli_num_rows($res_check) > 0) {
                $sql = "UPDATE paiements SET paye='$montant_mensuel', reste='0', statut='Payé', date_heure='$date_now' 
                        WHERE id_membre='$id_membre' AND mois_payer='$mois_payer'";
            } else {
                $sql = "INSERT INTO paiements (id_configuration, id_utilisateur, id_membre, mois_payer, a_payer, paye, reste, statut, date_heure) 
                        VALUES ('$id_configuration', '$id_utilisateur', '$id_membre', '$mois_payer', '$montant_mensuel', '$montant_mensuel', '0', 'Payé', '$date_now')";
            }
            
            if (!mysqli_query($bdd, $sql)) {
                $errors[] = "Erreur pour le membre $id_membre, mois $mois_payer: " . mysqli_error($bdd);
                $success = false;
            }
        }
    }
    
    echo json_encode([
        'success' => $success,
        'errors' => $errors
    ]);
    exit;
}

// Si aucune action valide n'a été détectée
http_response_code(400);
echo json_encode([
    'success' => false,
    'message' => 'Requête invalide'
]);
exit;
?>