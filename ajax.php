<?php
session_start();
include("include/php/connexion_bdd.php");
include("include/php/fonctions.php"); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    
    $adhesion      = generer_numero_adhesion($bdd); 
    $ev_annee       = $adhesion[0]; 
    $ev_ordre       = $adhesion[1]; 
    $n_adhesion    = $adhesion[2];

    $nom          = strip_tags(htmlspecialchars(trim($_POST["nom"])));
    $prenom     = strip_tags(htmlspecialchars(trim($_POST["prenom"])));
    $genre     = strip_tags(htmlspecialchars(trim($_POST["genre"])));
    $num_telephone     = strip_tags(htmlspecialchars(trim($_POST["num_telephone"])));
    $ville_commune     = strip_tags(htmlspecialchars(trim($_POST["ville_commune"])));
    $passwordPlain = strip_tags(htmlspecialchars(trim($_POST["password"])));
    $passwordHash = anvdko_password_hash($passwordPlain);
    $passwordSql = mysqli_real_escape_string($bdd, $passwordHash);

    $jour = (int) $_POST["jour"];
    $mois = (int) $_POST["mois"];
    $annee = (int) $_POST["annee"];

    if (checkdate($mois, $jour, $annee)) {
        $date_naissance = sprintf('%04d-%02d-%02d', $annee, $mois, $jour);
    } else {
        echo "La date de naissance est invalide.";
    } 

    //Vérification de l'existence des données avant l'insertion
    $verif_existe = "SELECT * FROM membres 
                    WHERE (nom = \"$nom\" AND prenom = \"$prenom\") 
                    OR num_telephone = \"$num_telephone\" 
                    LIMIT 1";
    $result = mysqli_query($bdd, $verif_existe);
    if (mysqli_num_rows($result) > 0) 
    {
        $existe = mysqli_fetch_assoc($result);
        
        if ($existe['nom'] == $nom && $existe['prenom'] == $prenom) {
            echo "Le nom et le prénom sont déjà enregistrés.";
        } elseif ($existe['num_telephone'] == $num_telephone) {
            echo "Le contact est déjà enregistré.";
        }
        exit;
    }


    //Insertion des données dans la base de données
    $query = "INSERT INTO membres (nom, 
                                    prenom, 
                                    genre,
                                    date_naissance, 
                                    num_telephone, 
                                    ville_commune, 
                                    password, 
                                    ev_annee,
                                    ev_ordre,
                                    num_adhesion,  
                                    date_heure) 
              VALUES (\"$nom\", 
                      \"$prenom\", 
                      \"$genre\", 
                      \"$date_naissance\",  
                      \"$num_telephone\", 
                      \"$ville_commune\", 
                      \"$passwordSql\", 
                      \"$ev_annee\",
                    \"$ev_ordre\",
                    \"$n_adhesion\",  
                      '".date('Y-m-d H:i:s')."')";

if (mysqli_query($bdd, $query)) {
    
    echo "success|".$n_adhesion;
} else {
    echo "Erreur : " . mysqli_error($bdd);
}

}

?>
