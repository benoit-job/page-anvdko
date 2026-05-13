<?php
session_start();
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");

if (!isset($_POST['ids']) || empty($_POST['ids'])) {
    die("Aucun membre sélectionné");
}

$ids = explode(',', $_POST['ids']);
$ids = array_map('intval', $ids); // Sécurité contre les injections SQL

// Charger la bibliothèque TCPDF ou autre
require_once('../fichiers/tcpdf/tcpdf.php');

// Créer un nouveau PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Pour chaque membre, générer sa carte
foreach ($ids as $id) {
    $query = "SELECT * FROM membres WHERE id = ?";
    $stmt = mysqli_prepare($bdd, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($membre = mysqli_fetch_assoc($result)) {
        // Ajouter une page pour ce membre
        $pdf->AddPage();
        
        // Générer le HTML de la carte (similaire à votre badge.php)
        $html = genererHtmlCarte($membre);
        
        // Écrire le HTML dans le PDF
        $pdf->writeHTML($html, true, false, true, false, '');
    }
}

// Envoyer le PDF au navigateur
$pdf->Output('cartes_membres.pdf', 'D');

function genererHtmlCarte($membre) {
    // Ici, vous devez générer le HTML pour une carte individuelle
    // Similaire à ce que vous avez dans badge.php mais adapté pour TCPDF
    // Retourne le HTML comme une chaîne
    ob_start();
    ?>
    <div style="width: 500px; height: 310px;">
        <!-- Votre structure de carte ici -->
        <h1><?= htmlspecialchars($membre['nom']) ?></h1>
        <!-- etc. -->
    </div>
    <?php
    return ob_get_clean();
}
?>