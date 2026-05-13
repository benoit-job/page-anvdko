<?php
$host = 'localhost'; // ou l'adresse de ton serveur
$user = 'root'; // ton utilisateur MySQL
$pass = ''; // ton mot de passe MySQL
$dbname = 'anvdko'; // nom de ta base de données

// Connexion à la base de données
$bdd = new mysqli($host, $user, $pass, $dbname);

// Vérification de la connexion
if ($bdd->connect_error) {
    die("Erreur de connexion à la base de données : " . $bdd->connect_error);
}
?>
