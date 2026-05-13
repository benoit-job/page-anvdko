<?php
session_start();
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");



if (isset($_POST['connexion'])) {
    $num_telephone = strip_tags(htmlspecialchars(trim($_POST["num_telephone"])));
    $password = strip_tags(htmlspecialchars(trim($_POST["password"])));

    $query = "SELECT * FROM membres
                WHERE LOWER(num_telephone) = \"$num_telephone\" 
                AND LOWER(password) = \"$password\"
              AND statut = 'actif' 
              LIMIT 1";
              
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme utilisateur");

    $_SESSION['membre'] = mysqli_fetch_assoc($resultat);

    if (!empty($_SESSION['membre'])) {
        echo "succes";
    } else {
        echo "failed !! veuillez vérifier vos informations et réessayer";
    }
}

?>
