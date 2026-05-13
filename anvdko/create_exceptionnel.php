<?php
header('Content-Type: application/json');
include("../include/php/connexion_bdd.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage
    $motif = isset($_POST['motif']) ? mysqli_real_escape_string($bdd, trim($_POST['motif'])) : '';
    $mois_debut = isset($_POST['mois_debut']) ? mysqli_real_escape_string($bdd, $_POST['mois_debut']) : '';
    $mois_fin = isset($_POST['mois_fin']) ? mysqli_real_escape_string($bdd, $_POST['mois_fin']) : '';
    
    // Fonction pour convertir en float ou NULL
    function convertToFloatOrNull($value) {
        // Si la valeur n'existe pas, est vide, null, ou la chaîne "null"
        if (!isset($value) || $value === '' || $value === null || 
            (is_string($value) && (trim($value) === '' || strtolower(trim($value)) === 'null'))) {
            return 'NULL';
        }
        $floatVal = floatval($value);
        // Accepter 0 et les valeurs positives
        return ($floatVal >= 0) ? $floatVal : 'NULL';
    }
    
    $montant_standard = convertToFloatOrNull($_POST['montant_standard'] ?? null);
    $montant_homme = convertToFloatOrNull($_POST['montant_homme'] ?? null);
    $montant_femme = convertToFloatOrNull($_POST['montant_femme'] ?? null);
    $montant_mademoiselle = convertToFloatOrNull($_POST['montant_mademoiselle'] ?? null);
    $montant_bureau = convertToFloatOrNull($_POST['montant_bureau'] ?? null);

    // Vérifier qu'au moins un montant est défini
    $hasMontant = ($montant_standard !== 'NULL') || 
                  ($montant_homme !== 'NULL') || 
                  ($montant_femme !== 'NULL') || 
                  ($montant_mademoiselle !== 'NULL') || 
                  ($montant_bureau !== 'NULL');
    
    if (!$hasMontant) {
        echo json_encode(['success' => false, 'error' => 'Au moins un montant doit être défini']);
        exit;
    }
    
    if (empty($motif) || empty($mois_debut) || empty($mois_fin)) {
        echo json_encode(['success' => false, 'error' => 'Les champs Motif, Mois de début et Mois de fin sont obligatoires']);
        exit;
    }

    // Construction de la requête SQL
    // Toujours inclure toutes les colonnes (elles doivent exister si les scripts SQL ont été exécutés)
    $query = "INSERT INTO config_cotisations_exceptionnelles 
        (motif, montant_standard, montant_homme, montant_femme, montant_mademoiselle, montant_bureau, mois_debut, mois_fin, date_creation) 
        VALUES (
            '$motif', 
            $montant_standard, 
            $montant_homme, 
            $montant_femme, 
            $montant_mademoiselle, 
            $montant_bureau, 
            '$mois_debut', 
            '$mois_fin', 
            NOW()
        )";

    // Exécution
    if (mysqli_query($bdd, $query)) {
        echo json_encode(['success' => true, 'message' => 'Configuration enregistrée avec succès']);
    } else {
        $error = mysqli_error($bdd);
        $errorMsg = 'Erreur SQL: ' . $error;
        
        // Vérifier si c'est une erreur de colonne manquante
        if (strpos($error, "Unknown column") !== false) {
            $errorMsg .= '. Veuillez exécuter les scripts SQL pour ajouter les colonnes manquantes (add_montant_homme_mademoiselle_columns.sql, add_montant_femme_column.sql)';
        }
        
        echo json_encode([
            'success' => false, 
            'error' => $errorMsg
        ]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
}
?>
