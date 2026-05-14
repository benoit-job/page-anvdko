<?php
session_start();
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");



if (isset($_POST['connexion'])) {
    $num_telephone = strip_tags(htmlspecialchars(trim($_POST["num_telephone"])));
    $password = (string) ($_POST["password"] ?? '');

    $nt = mysqli_real_escape_string($bdd, strtolower($num_telephone));
    $query = "SELECT * FROM membres
                WHERE LOWER(num_telephone) = '$nt'
              AND statut = 'actif' 
              LIMIT 1";
              
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme utilisateur");

    $row = mysqli_fetch_assoc($resultat);

    if (!empty($row) && anvdko_password_verify($password, $row['password'] ?? '')) {
        anvdko_password_maybe_upgrade_mysqli($bdd, 'membres', 'id', (int) $row['id'], $password, $row['password'] ?? '');
        $_SESSION['membre'] = $row;
        $_SESSION['membre_id'] = $row['id'];
        echo "succes";
    } else {
        echo "failed !! veuillez vérifier vos informations et réessayer";
    }
}

?>
