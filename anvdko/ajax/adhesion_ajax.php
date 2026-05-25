<?php
session_start();
include("../../include/php/connexion_bdd.php");
include("../../include/php/fonctions.php");

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Requête invalide'];

if (!isset($_SESSION["utilisateur"]) || !isset($_SESSION["configuration"])) {
    echo json_encode(['success' => false, 'message' => 'Session invalide']);
    exit;
}

$id_utilisateur = $_SESSION["utilisateur"]["id"] ?? 0;
$id_configuration = $_SESSION["configuration"]["id"] ?? 0;
$montant_adhesion = $_SESSION["configuration"]["montant_adhesion"] ?? 1000;
$date_now = date("Y-m-d H:i:s");

// Action: Charger les adhésions
if (isset($_POST['action']) && $_POST['action'] === 'loadAdhesions') {
    $query = "SELECT 
                m.id,
                m.nom,
                m.prenom,
                m.logo,
                m.num_adhesion,
                COALESCE(a.statut, 'Non payé') as statut_adhesion,
                COALESCE(a.montant, 0) as montant_adhesion,
                MAX(a.date_heure) as derniere_adhesion
              FROM membres m
              LEFT JOIN adhesion a ON m.id = a.id_membre
              GROUP BY m.id
              ORDER BY m.nom, m.prenom ASC";
    
    $result = mysqli_query($bdd, $query);
    $membres = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $row['id_crypte'] = crypt_decrypt_chaine($row['id'], 'C');
        $row['id'] = $row['id'];
        $membres[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $membres]);
    exit;
}

// Action: Payer l'adhésion
if (isset($_POST['action']) && $_POST['action'] === 'payerAdhesion') {
    if (!isset($_POST['ids_membres']) || empty($_POST['ids_membres'])) {
        echo json_encode(['success' => false, 'message' => 'Aucun membre sélectionné']);
        exit;
    }

    $ids_membres = array_map('intval', (array)$_POST['ids_membres']);
    
    // Utiliser une transaction
    mysqli_begin_transaction($bdd);
    
    try {
        $compteur_success = 0;
        
        foreach ($ids_membres as $id_membre) {
            $check_query = "SELECT id_adhesion FROM adhesion WHERE id_membre = $id_membre ORDER BY date_heure DESC LIMIT 1";
            $check_result = mysqli_query($bdd, $check_query);
            
            if ($check_result && mysqli_num_rows($check_result) > 0) {
                $row_adh = mysqli_fetch_assoc($check_result);
                $id_adhesion = (int) $row_adh['id_adhesion'];
                $update_query = "UPDATE adhesion 
                                 SET montant = $montant_adhesion,
                                     statut = 'Payé',
                                     id_utilisateur = $id_utilisateur,
                                     date_heure = '$date_now'
                                 WHERE id_adhesion = $id_adhesion";
                if (mysqli_query($bdd, $update_query)) {
                    $compteur_success++;
                }
            } else {
                // Insérer une nouvelle adhésion
                $insert_query = "INSERT INTO adhesion (id_membre, id_utilisateur, montant, statut, date_heure)
                                 VALUES ($id_membre, $id_utilisateur, $montant_adhesion, 'Payé', '$date_now')";
                if (mysqli_query($bdd, $insert_query)) {
                    $compteur_success++;
                }
            }
        }

        if ($compteur_success === 0) {
            mysqli_rollback($bdd);
            echo json_encode(['success' => false, 'message' => 'Aucune adhésion enregistrée. Vérifiez que la table adhesion existe (script SQL create_adhesion_table.sql).']);
            exit;
        }
        
        mysqli_commit($bdd);
        
        echo json_encode([
            'success' => true,
            'message' => $compteur_success . ' adhésion(s) payée(s) avec succès'
        ]);
    } catch (Exception $e) {
        mysqli_rollback($bdd);
        echo json_encode(['success' => false, 'message' => 'Erreur lors du paiement: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode($response);
exit;
?>
