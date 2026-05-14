<?php
session_start();
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");

if (isset($_POST['connexion'])) {
    $login = isset($_POST['login_identifier']) ? trim((string) $_POST['login_identifier']) : '';
    $login = strip_tags($login);
    $password = isset($_POST['password']) ? (string) $_POST['password'] : '';

    if ($login === '' || $password === '') {
        echo "failed !! incorrect";
        exit;
    }

    $esc = mysqli_real_escape_string($bdd, $login);
    $query = "SELECT * FROM utilisateurs
              WHERE statut = 'actif'
              AND (pseudo = \"$esc\" OR telephone = \"$esc\" OR email = \"$esc\")
              LIMIT 1";

    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme utilisateur");
    $user = mysqli_fetch_assoc($resultat);

    if (!empty($user) && anvdko_password_verify($password, $user['password'] ?? '')) {
        anvdko_password_maybe_upgrade_mysqli($bdd, 'utilisateurs', 'id', (int) $user['id'], $password, $user['password'] ?? '');
        $rid = (int) $user['id'];
        $resultat = mysqli_query($bdd, "SELECT * FROM utilisateurs WHERE id = $rid LIMIT 1");
        $_SESSION['utilisateur'] = mysqli_fetch_assoc($resultat);

        $query = "SELECT * FROM configurations WHERE id = " . (int) $_SESSION["utilisateur"]["id_configuration"];
        $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");
        $_SESSION["configuration"] = mysqli_fetch_array($resultat);
        echo "succes";
    } else {
        echo "failed !! incorrect";
    }
}

?>
