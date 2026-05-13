<?php
session_start();
require_once '../../include/php/connexion_bdd.php';
require_once '../../include/php/fonctions.php';

// Sécurité: vérifier la session
if (!isset($_SESSION['utilisateur']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expirée']);
    exit();
}

$id_user = $_SESSION['utilisateur']['id'];
$action = $_POST['action'] ?? '';

// Vérifier la connexion MySQLi
if (!isset($bdd) || !$bdd) {
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
    exit();
}

try {
    switch ($action) {
        case 'ajouter':
            ajouterCommentaire($bdd, $id_user);
            break;
            
        case 'modifier':
            modifierCommentaire($bdd, $id_user);
            break;
            
        case 'supprimer':
            supprimerCommentaire($bdd, $id_user);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Action inconnue']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}

// ========================================
// FONCTION: Ajouter un commentaire
// ========================================
function ajouterCommentaire($bdd, $id_user) {
    $id_depense = intval($_POST['id_depense'] ?? 0);
    $commentaire = trim($_POST['commentaire'] ?? '');
    
    // Validation
    if (empty($commentaire)) {
        echo json_encode(['success' => false, 'message' => 'Le commentaire ne peut pas être vide']);
        return;
    }
    
    // Vérifier que la dépense appartient à l'utilisateur
    $sql = "SELECT id_depense FROM depenses_anvdko WHERE id_depense = $id_depense AND id_user = $id_user";
    $result = mysqli_query($bdd, $sql);
    
    if (mysqli_num_rows($result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Dépense non trouvée']);
        return;
    }
    
    // Échapper les données
    $commentaire = mysqli_real_escape_string($bdd, $commentaire);
    
    try {
        $sql = "INSERT INTO depense_commentaires (id_depense, id_user, commentaire) 
                VALUES ($id_depense, $id_user, '$commentaire')";
        
        if (mysqli_query($bdd, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Commentaire ajouté avec succès']);
        } else {
            throw new Exception(mysqli_error($bdd));
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout: ' . $e->getMessage()]);
    }
}

// ========================================
// FONCTION: Modifier un commentaire
// ========================================
function modifierCommentaire($bdd, $id_user) {
    $id_commentaire = intval($_POST['id_commentaire'] ?? 0);
    $commentaire = trim($_POST['commentaire'] ?? '');
    
    // Validation
    if (empty($commentaire)) {
        echo json_encode(['success' => false, 'message' => 'Le commentaire ne peut pas être vide']);
        return;
    }
    
    // Vérifier que le commentaire appartient à l'utilisateur
    $sql = "SELECT id_commentaire FROM depense_commentaires WHERE id_commentaire = $id_commentaire AND id_user = $id_user";
    $result = mysqli_query($bdd, $sql);
    
    if (mysqli_num_rows($result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Commentaire non trouvé']);
        return;
    }
    
    // Échapper les données
    $commentaire = mysqli_real_escape_string($bdd, $commentaire);
    
    try {
        $sql = "UPDATE depense_commentaires 
                SET commentaire = '$commentaire' 
                WHERE id_commentaire = $id_commentaire";
        
        if (mysqli_query($bdd, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Commentaire modifié avec succès']);
        } else {
            throw new Exception(mysqli_error($bdd));
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la modification: ' . $e->getMessage()]);
    }
}

// ========================================
// FONCTION: Supprimer un commentaire
// ========================================
function supprimerCommentaire($bdd, $id_user) {
    $id_commentaire = intval($_POST['id_commentaire'] ?? 0);
    
    // Vérifier que le commentaire appartient à l'utilisateur
    $sql = "SELECT id_commentaire FROM depense_commentaires WHERE id_commentaire = $id_commentaire AND id_user = $id_user";
    $result = mysqli_query($bdd, $sql);
    
    if (mysqli_num_rows($result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Commentaire non trouvé']);
        return;
    }
    
    try {
        $sql = "DELETE FROM depense_commentaires WHERE id_commentaire = $id_commentaire";
        
        if (mysqli_query($bdd, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Commentaire supprimé avec succès']);
        } else {
            throw new Exception(mysqli_error($bdd));
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
    }
}
?>