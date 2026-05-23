<?php
session_start();
if (empty($_SESSION['membre']) || ($_SESSION['membre']['statut'] ?? '') !== 'actif') {
    header('Location: index.php');
    exit;
}

function getAdhesionStatut() {
    $statut = '';
    if (!empty($_SESSION['membre']['statut_ad'])) {
        $statut = $_SESSION['membre']['statut_ad'];
    } elseif (!empty($_SESSION['membres']['statut_ad'])) {
        $statut = $_SESSION['membres']['statut_ad'];
    }
    return trim(strtolower($statut));
}

function isAdhesionPayee() {
    $statut = getAdhesionStatut();
    return $statut === 'payé' || $statut === 'paye';
}

function requireAdhesionPayee($returnUrl = 'accueil.php') {
    if (!isAdhesionPayee()) {
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