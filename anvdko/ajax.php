<?php
session_start();
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 

if (isset($_POST['connexion'])) {
    $pseudo = strip_tags(htmlspecialchars(trim($_POST["pseudo"])));
    $telephone = strip_tags(htmlspecialchars(trim($_POST["telephone"])));
    $password = strip_tags(htmlspecialchars(trim($_POST["password"])));

    // Connexion utilisateur classique
    $query = "SELECT * FROM utilisateurs
              WHERE pseudo = \"$pseudo\" 
              AND (telephone = \"$telephone\" OR email = \"$telephone\") 
              AND password = \"$password\" 
              AND statut = 'actif' 
              LIMIT 1";
              
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme utilisateur");

    $_SESSION['utilisateur'] = mysqli_fetch_assoc($resultat);

    if (!empty($_SESSION['utilisateur'])) {
        
        $query ="SELECT * FROM configurations WHERE id =  ".$_SESSION["utilisateur"]["id_configuration"];
        $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");
        $_SESSION["configuration"] = mysqli_fetch_array($resultat);
        echo "succes";
    } else {
        echo "failed !! incorrect";
    }
}

?>
