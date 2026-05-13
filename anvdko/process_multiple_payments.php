<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 

header('Content-Type: application/json');

// Vérifier si des données ont été envoyées
$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['paiements'])) {
    echo json_encode(['success' => false, 'message' => 'Aucun paiement reçu']);
    exit;
}

$count = 0;
$errors = [];

foreach ($input['paiements'] as $paiement) {
    try {
        // L'ID membre arrive déjà décrypté depuis get_multiple_membres_info.php
        $id_membre = $paiement['id_membre'] ?? '';
        
        if (empty($id_membre)) {
            $errors[] = "ID membre manquant dans les données";
            continue;
        }
        
        // Validation de l'ID membre
        if (!is_numeric($id_membre) || $id_membre <= 0) {
            $errors[] = "ID membre invalide: '$id_membre'";
            continue;
        }
        
        $id_membre = intval($id_membre);
        
        // Vérifier que le membre existe
        $check_membre = mysqli_query($bdd, "SELECT id FROM membres WHERE id = $id_membre LIMIT 1");
        if (mysqli_num_rows($check_membre) == 0) {
            $errors[] = "Membre ID $id_membre introuvable dans la base de données";
            continue;
        }
        
        // Récupération et validation des autres données
        $id_motif = isset($paiement['id_motif']) ? intval($paiement['id_motif']) : 0;
        $mois_payer = mysqli_real_escape_string($bdd, $paiement['mois_payer'] ?? '');
        $a_payer = floatval($paiement['a_payer'] ?? 0);
        $paye = floatval($paiement['paye'] ?? 0);
        
        // Validation du motif
        if ($id_motif <= 0) {
            $errors[] = "Membre ID $id_membre : ID motif invalide ($id_motif)";
            continue;
        }
        
        // Validation du montant à payer
        if ($a_payer <= 0) {
            $errors[] = "Membre ID $id_membre : montant à payer invalide ($a_payer)";
            continue;
        }
        
        // Validation du montant payé
        if ($paye <= 0) {
            $errors[] = "Membre ID $id_membre : montant payé invalide ($paye)";
            continue;
        }
        
        // Validation du mois
        if (empty($mois_payer) || !preg_match('/^\d{4}-\d{2}$/', $mois_payer)) {
            $errors[] = "Membre ID $id_membre : format de mois invalide ($mois_payer)";
            continue;
        }
        
        $reste = max(0, $a_payer - $paye);
        $date_paiement = mysqli_real_escape_string($bdd, $paiement['date_paiement'] ?? date('Y-m-d'));

        // Vérifier si un paiement existe déjà
        $check = mysqli_query($bdd, "SELECT id, paye, a_payer FROM exceptionnels_pay 
                                    WHERE id_membre = $id_membre 
                                    AND mois_payer = '$mois_payer'
                                    AND id_motif = $id_motif");
        
        if (mysqli_num_rows($check) > 0) {
            // Mise à jour du paiement existant
            $existing = mysqli_fetch_assoc($check);
            $nouveau_paye = floatval($existing['paye']) + $paye;
            $nouveau_reste = max(0, floatval($existing['a_payer']) - $nouveau_paye);
            
            $query = "UPDATE exceptionnels_pay 
                      SET paye = $nouveau_paye,
                          reste = $nouveau_reste,
                          date_paiement = '$date_paiement'
                      WHERE id_membre = $id_membre 
                      AND mois_payer = '$mois_payer'
                      AND id_motif = $id_motif";
        } else {
            // Insertion d'un nouveau paiement
            $query = "INSERT INTO exceptionnels_pay 
                      (id_membre, id_motif, mois_payer, a_payer, paye, reste, date_paiement)
                      VALUES ($id_membre, $id_motif, '$mois_payer', $a_payer, $paye, $reste, '$date_paiement')";
        }
        
        if (mysqli_query($bdd, $query)) {
            $count++;
        } else {
            $errors[] = "Membre ID $id_membre : Erreur SQL - " . mysqli_error($bdd);
        }
        
    } catch (Exception $e) {
        $errors[] = "Erreur générale : " . $e->getMessage();
    }
}

// Réponse finale
if (!empty($errors)) {
    echo json_encode([
        'success' => $count > 0,
        'count' => $count,
        'message' => "$count paiement(s) enregistré(s)" . (count($errors) > 0 ? " avec " . count($errors) . " erreur(s)" : ""),
        'errors' => $errors
    ]);
} else {
    echo json_encode([
        'success' => true,
        'count' => $count,
        'message' => "$count paiement(s) enregistré(s) avec succès"
    ]);
}
?>