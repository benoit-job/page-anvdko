<?php
session_start();
header('Content-Type: application/json');

include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");

// Vérifier connexion
if (!isset($_SESSION['membre']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Connectez-vous d\'abord']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

try {
    // Récupération des champs
    $nom_document = trim($_POST['nom_document'] ?? 'VIDE');
    $description  = trim($_POST['description'] ?? '');
    $categorie    = trim($_POST['categorie'] ?? 'VIDE');
    $visibilite   = trim($_POST['visibilite'] ?? 'membres');
    $tags         = trim($_POST['tags'] ?? '');
    $auteur_id    = $_SESSION['membre']['id'];

    // Validation
    if (empty($nom_document) || empty($categorie)) {
        echo json_encode(['success' => false, 'message' => 'Nom et catégorie requis']);
        exit;
    }

    if (!isset($_FILES['fichier']) || $_FILES['fichier']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Aucun fichier sélectionné']);
        exit;
    }

    $file = $_FILES['fichier'];
    $original_name = $file['name'];
    $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    $allowed = ['pdf','doc','docx','xls','xlsx','ppt','pptx','jpg','jpeg','png','gif','zip','rar','txt','csv'];

    if (!in_array($ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Type de fichier non autorisé']);
        exit;
    }

    if ($file['size'] > 50 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'Fichier trop volumineux (max 50 Mo)']);
        exit;
    }

    // Dossier d'upload
    $upload_dir = '../fichiers/documents/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    // Nom unique
    $nom_fichier = uniqid('doc_') . '_' . time() . '.' . $ext;
    $chemin_fichier = $upload_dir . $nom_fichier;

    if (!move_uploaded_file($file['tmp_name'], $chemin_fichier)) {
        echo json_encode(['success' => false, 'message' => 'Échec enregistrement fichier']);
        exit;
    }

    // Insertion en base
    $sql = "INSERT INTO documents (
                nom_document, description, nom_fichier, chemin_fichier, type_fichier,
                taille_fichier, auteur_id, categorie, visibilite, tags,
                version, date_creation, date_modification, statut, telechargements,
                approuve_par, date_approbation
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '1.0', NOW(), NOW(), 'actif', 0, NULL, NULL
            )";

    $stmt = $bdd->prepare($sql);
    if (!$stmt) {
        unlink($chemin_fichier);
        echo json_encode(['success' => false, 'message' => 'Erreur préparation requête']);
        exit;
    }

    $stmt->bind_param(
        'ssssssisss',
        $nom_document,
        $description,
        $nom_fichier,
        $chemin_fichier,
        $ext,
        $file['size'],
        $auteur_id,
        $categorie,
        $visibilite,
        $tags
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Document ajouté avec succès !']);
    } else {
        unlink($chemin_fichier);
        echo json_encode(['success' => false, 'message' => 'Erreur insertion base']);
    }

    $stmt->close();

} catch (Exception $e) {
    if (isset($chemin_fichier) && file_exists($chemin_fichier)) unlink($chemin_fichier);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}
?>