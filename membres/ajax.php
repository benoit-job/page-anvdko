<?php
session_start();
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");


if (isset($_POST['enregistrerSignature'])) {
    $id_membre = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST['id_membre'], 'D'))));
    $image_data = $_POST['signature'];
    $pathFolder = rtrim(strip_tags(htmlspecialchars(trim($_POST['pathFolder']))), '/') . '/';

    if (!file_exists($pathFolder)) {
        mkdir($pathFolder, 0755, true); // Crée le dossier s'il n'existe pas
    }

    $image_data = str_replace('data:image/png;base64,', '', $image_data);
    $image_data = str_replace(' ', '+', $image_data);
    $image = base64_decode($image_data);

    $file_name = 'signature_' . time() . '.png';
    $file_path = $pathFolder . $file_name;

    if (file_put_contents($file_path, $image)) {

        $query ="UPDATE membres SET signature = '".$file_path."'  WHERE id = ".$_SESSION["membres"]["id"];
        mysqli_query($bdd, $query) or die("Requête non conforme");

        echo $file_path; // Le JS se charge d’enlever les ../
    } else {
        echo "Erreur : impossible d'enregistrer l'image";
    }
}

if (isset($_POST['SupprimerSignature'])) {
    
    $query ="UPDATE membres SET signature = ' '  WHERE id = ".$_SESSION["membres"]["id"];
    mysqli_query($bdd, $query) or die("Requête non conforme");

    echo ' '; // Retour vide (src vide = suppression visuelle)
}
?>
