<?php
session_start();
if (empty($_SESSION['membre']) || ($_SESSION['membre']['statut'] ?? '') !== 'actif') {
    header('Location: index.php');
    exit;
}

function getAdhesionStatut($bdd = null) {
    global $bdd;
    $statut = '';
    
    // Essayer de récupérer le statut depuis la table adhesion
    if (isset($_SESSION['membre']['id']) && $bdd) {
        $query = "SELECT statut FROM adhesion WHERE id_membre = ".(int)$_SESSION['membre']['id']." ORDER BY date_heure DESC LIMIT 1";
        $result = mysqli_query($bdd, $query);
        if ($result && $row = mysqli_fetch_assoc($result)) {
            $statut = $row['statut'];
        }
    }
    
    // Fallback sur la session s'il n'y a rien dans la base
    if (empty($statut)) {
        if (!empty($_SESSION['membre']['statut_ad'])) {
            $statut = $_SESSION['membre']['statut_ad'];
        } elseif (!empty($_SESSION['membres']['statut_ad'])) {
            $statut = $_SESSION['membres']['statut_ad'];
        }
    }
    
    return trim(strtolower($statut));
}

function isAdhesionPayee($bdd = null) {
    $statut = getAdhesionStatut($bdd);
    return $statut === 'payé' || $statut === 'paye';
}

function requireAdhesionPayee($returnUrl = 'accueil.php', $bdd = null) {
    if (!isAdhesionPayee($bdd)) {
        $returnUrl = htmlspecialchars($returnUrl, ENT_QUOTES, 'UTF-8');
        $message = "Vous devez payer votre adhésion avant d'accéder à cette page.";
        echo '<!DOCTYPE html>';
        echo '<html lang="fr">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<title>Accès restreint</title>';
        echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">';
        echo '</head>';
        echo '<body>';
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script>';
        echo 'Swal.fire({title: "Accès restreint", text: "'.$message.'", icon: "warning", confirmButtonText: "Retour", allowOutsideClick: false, allowEscapeKey: false}).then(function() { window.location.href = "'.$returnUrl.'"; });';
        echo '</script>';
        echo '</body>';
        echo '</html>';
        exit;
    }
}
?>