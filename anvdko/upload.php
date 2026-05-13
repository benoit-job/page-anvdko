<?php
  session_start(); 
  include("../include/php/connexion_bdd.php");
  include("../include/php/fonctions.php"); 
?>

<?php
if(isset($_POST['uploadImagePrincipaleactualite']))
{
    // Récupération des données envoyées
    $id_actualite    = strip_tags(htmlspecialchars(trim( crypt_decrypt_chaine($_POST['id_actualite'], 'D') )));
    $imageBase64   = $_POST['imageBase64'];
    $filename      = $_POST['filename'];
    $fileExtension = $_POST['fileExtension'];   

    // Supprimer l'en-tête de l'image base64
    $imageData    = str_replace('data:image/jpeg;base64,', '', $imageBase64);
    $imageData    = str_replace(' ', '+', $imageData);
    $imageDecoded = base64_decode($imageData);

    // Chemin où enregistrer l'image
    $destination = createPathFile('../fichiers/uploads/').uniqid().'.'.$fileExtension;

    // Enregistrer l'image dans le dossier uploads
    if(file_put_contents($destination, $imageDecoded))
    {
        //Supprimer ancienne image 
        $query = "SELECT image FROM actualites WHERE id =".$id_actualite;
        $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
        $produit = mysqli_fetch_array($resultat);

        $ancienneImage = '../fichiers/uploads/'.$produit['image']; 
        @unlink($ancienneImage); 

        $image = str_replace('../fichiers/uploads/', '', $destination);

        $query = "UPDATE actualites SET image = ".empty_to_NULL($image)." WHERE id =".$id_actualite;
        mysqli_query($bdd, $query) or die("Requête non conforme");

        echo affImgAdmin('200px', '200px', $image, '');
    }
} 


if(isset($_POST['uploadImageGalerieactualite']))
{
    $id_actualite = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST['id_actualite'], 'D'))));

    // Récupération des données envoyées
    $imageBase64   = $_POST['imageBase64'];
    $filename      = $_POST['filename'];
    $fileExtension = $_POST['fileExtension'];

    // Supprimer l'en-tête de l'image base64
    $imageData    = str_replace('data:image/jpeg;base64,', '', $imageBase64);
    $imageData    = str_replace(' ', '+', $imageData);
    $imageDecoded = base64_decode($imageData);

    // Chemin où enregistrer l'image
    $destination = createPathFile('../fichiers/uploads/').uniqid().'.'.$fileExtension;

    // Enregistrer l'image dans le dossier uploads
    if(file_put_contents($destination, $imageDecoded))
    {
        $image = str_replace('../fichiers/uploads/', '', $destination); 

        $query = "INSERT INTO galerie_actualites (id_configuration, id_utilisateur, id_actualite, image, date_heure) 
                  VALUES (".$_SESSION["configuration"]["id"].", ".$_SESSION["utilisateur"]["id"].", \"$id_actualite\", \"$image\", '".date('Y-m-d H:i:s')."')";
        mysqli_query($bdd, $query) or die("Requête non conforme");
    }
}



if(isset($_POST['listeGalerieactualite']))
{
    $id_actualite = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST['id_actualite'], 'D'))));

    $query = "SELECT * 
              FROM galerie_actualites 
              WHERE id_configuration = ".$_SESSION['configuration']['id']." AND 
                    id_actualite = ".$id_actualite."  
              ORDER BY id";
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
    while($galerie = mysqli_fetch_array($resultat))
    {
      echo "<div class='position-relative m-1' style='display: inline-block; width: 60px; height: 60px;'>
                ".affImgAdmin('60px', '60px', $galerie['image'], '')."   
                <div class='position-absolute top-0 end-0'> 
                    <i class='fas fa-times text-danger rounded bg-danger text-light'  style='box-shadow: 0 0 2px black; display:inline-block; width: 15px; height: 15px; margin: 3px;' onclick='supprGalerieactualite(this)' id_galerie='".crypt_decrypt_chaine($galerie['id'], 'C')."'></i> 
                </div>                     
            </div>";
    }
}



if(isset($_POST['supprGalerieactualite']))
{
    $id_galerie = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST['id_galerie'], 'D'))));

    $query = "SELECT * FROM galerie_actualites WHERE id_configuration = ".$_SESSION['configuration']['id']." AND id = ".$id_galerie;
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
    $galerie = mysqli_fetch_array($resultat);

        $image = '../fichiers/uploads/'.$galerie['image']; 
        @unlink($image); 

        $query = "DELETE FROM galerie_actualites WHERE id_configuration = ".$_SESSION['configuration']['id']." AND id = ".$id_galerie;
        mysqli_query($bdd, $query) or die("Requête non conforme"); 

}

//  ACTION POUR AGENDA


if(isset($_POST['uploadImagePrincipaleagenda']))
{
    // Récupération des données envoyées
    $id_agenda    = strip_tags(htmlspecialchars(trim( crypt_decrypt_chaine($_POST['id_agenda'], 'D') )));
    $imageBase64   = $_POST['imageBase64'];
    $filename      = $_POST['filename'];
    $fileExtension = $_POST['fileExtension'];   

    // Supprimer l'en-tête de l'image base64
    $imageData    = str_replace('data:image/jpeg;base64,', '', $imageBase64);
    $imageData    = str_replace(' ', '+', $imageData);
    $imageDecoded = base64_decode($imageData);

    // Chemin où enregistrer l'image
    $destination = createPathFile('../fichiers/uploads/').uniqid().'.'.$fileExtension;

    // Enregistrer l'image dans le dossier uploads
    if(file_put_contents($destination, $imageDecoded))
    {
        //Supprimer ancienne image 
        $query = "SELECT image FROM agenda WHERE id =".$id_agenda;
        $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
        $produit = mysqli_fetch_array($resultat);

        $ancienneImage = '../fichiers/uploads/'.$produit['image']; 
        @unlink($ancienneImage); 

        $image = str_replace('../fichiers/uploads/', '', $destination);

        $query = "UPDATE agenda SET image = ".empty_to_NULL($image)." WHERE id =".$id_agenda;
        mysqli_query($bdd, $query) or die("Requête non conforme");

        echo affImgAdmin('200px', '200px', $image, '');
    }
} 


if(isset($_POST['uploadImageGalerieagenda']))
{
    $id_agenda = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST['id_agenda'], 'D'))));

    // Récupération des données envoyées
    $imageBase64   = $_POST['imageBase64'];
    $filename      = $_POST['filename'];
    $fileExtension = $_POST['fileExtension'];

    // Supprimer l'en-tête de l'image base64
    $imageData    = str_replace('data:image/jpeg;base64,', '', $imageBase64);
    $imageData    = str_replace(' ', '+', $imageData);
    $imageDecoded = base64_decode($imageData);

    // Chemin où enregistrer l'image
    $destination = createPathFile('../fichiers/uploads/').uniqid().'.'.$fileExtension;

    // Enregistrer l'image dans le dossier uploads
    if(file_put_contents($destination, $imageDecoded))
    {
        $image = str_replace('../fichiers/uploads/', '', $destination); 

        $query = "INSERT INTO galerie_actualites (id_configuration, id_utilisateur, id_agenda, image, date_heure) 
                  VALUES (".$_SESSION["configuration"]["id"].", ".$_SESSION["utilisateur"]["id"].", \"$id_agenda\", \"$image\", '".date('Y-m-d H:i:s')."')";
        mysqli_query($bdd, $query) or die("Requête non conforme");
    }
}



if(isset($_POST['listeGalerieagenda']))
{
    $id_agenda = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST['id_agenda'], 'D'))));

    $query = "SELECT * 
              FROM galerie_actualites 
              WHERE id_configuration = ".$_SESSION['configuration']['id']." AND 
                    id_agenda = ".$id_agenda."  
              ORDER BY id";
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
    while($galerie = mysqli_fetch_array($resultat))
    {
      echo "<div class='position-relative m-1' style='display: inline-block; width: 60px; height: 60px;'>
                ".affImgAdmin('60px', '60px', $galerie['image'], '')."   
                <div class='position-absolute top-0 end-0'> 
                    <i class='fas fa-times text-danger rounded bg-danger text-light'  style='box-shadow: 0 0 2px black; display:inline-block; width: 15px; height: 15px; margin: 3px;' onclick='supprGalerieagenda(this)' id_galerie='".crypt_decrypt_chaine($galerie['id'], 'C')."'></i> 
                </div>                     
            </div>";
    }
}



if(isset($_POST['supprGalerieagenda']))
{
    $id_galerie = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST['id_galerie'], 'D'))));

    $query = "SELECT * FROM galerie_actualites WHERE id_configuration = ".$_SESSION['configuration']['id']." AND id = ".$id_galerie;
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
    $galerie = mysqli_fetch_array($resultat);

        $image = '../fichiers/uploads/'.$galerie['image']; 
        @unlink($image); 

        $query = "DELETE FROM galerie_actualites WHERE id_configuration = ".$_SESSION['configuration']['id']." AND id = ".$id_galerie;
        mysqli_query($bdd, $query) or die("Requête non conforme"); 

}
?>
