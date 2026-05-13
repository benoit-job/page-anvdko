<?php
header('Content-Type: application/json');
session_start();
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");

if (!isset($_SESSION['membre']) || empty($_SESSION['membre'])) {
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit;
}

$user_id = (int)$_SESSION['membre']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'inscription') {
        $event_id = (int)$_POST['event_id'];
        $nombre_personnes = (int)$_POST['nombre_personnes'];
        $commentaire = mysqli_real_escape_string($bdd, $_POST['commentaire'] ?? '');
        $newsletter = isset($_POST['newsletter']) ? (int)$_POST['newsletter'] : 0;
        $date_inscription = date('Y-m-d H:i:s');

        // Vérifier si déjà inscrit
        $check = mysqli_query($bdd, "SELECT id FROM inscription_evenement WHERE evenement_id=$event_id AND membre_id=$user_id");
        if (mysqli_num_rows($check) > 0) {
            echo json_encode(['success' => false, 'message' => 'Vous êtes déjà inscrit à cet événement']);
            exit;
        }

        // Insertion
        $query = "INSERT INTO inscription_evenement (evenement_id, membre_id, nombre_personnes, commentaire, newsletter, date_inscription)
                  VALUES ($event_id, $user_id, $nombre_personnes, '$commentaire', $newsletter, '$date_inscription')";
        
        if (mysqli_query($bdd, $query)) {
            echo json_encode(['success' => true, 'message' => 'Inscription réussie']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription']);
        }
    }
    elseif ($_POST['action'] === 'modifier') {
        $event_id = (int)$_POST['event_id'];
        $nombre_personnes = (int)$_POST['nombre_personnes'];
        $commentaire = mysqli_real_escape_string($bdd, $_POST['commentaire'] ?? '');
        $newsletter = isset($_POST['newsletter']) ? (int)$_POST['newsletter'] : 0;

        $query = "UPDATE inscription_evenement 
                  SET nombre_personnes=$nombre_personnes, 
                      commentaire='$commentaire', 
                      newsletter=$newsletter 
                  WHERE evenement_id=$event_id AND membre_id=$user_id";
        
        if (mysqli_query($bdd, $query)) {
            echo json_encode(['success' => true, 'message' => 'Inscription modifiée']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la modification']);
        }
    }
    elseif ($_POST['action'] === 'supprimer') {
        $event_id = (int)$_POST['event_id'];

        $query = "DELETE FROM inscription_evenement WHERE evenement_id=$event_id AND membre_id=$user_id";
        
        if (mysqli_query($bdd, $query)) {
            echo json_encode(['success' => true, 'message' => 'Inscription supprimée']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
        }
    }
    elseif ($_POST['action'] === 'get_inscription') {
        $event_id = (int)$_POST['event_id'];

        $query = "SELECT nombre_personnes, commentaire, newsletter 
                  FROM inscription_evenement 
                  WHERE evenement_id=$event_id AND membre_id=$user_id";
        $result = mysqli_query($bdd, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Inscription non trouvée']);
        }
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Action invalide']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requête invalide']);
}
?>