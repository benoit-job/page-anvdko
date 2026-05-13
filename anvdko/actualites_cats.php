<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 
?>

<?php
if(isset($_POST['ajouterCategorieactualite'])) 
{
	$nom         = strip_tags(htmlspecialchars(trim($_POST["nom"])));   
	$description = strip_tags(htmlspecialchars(trim($_POST["description"])));  

    $query = "INSERT INTO cat_actualites (id_configuration, id_utilisateur, nom, description, date_heure) 
              VALUES (".$_SESSION["configuration"]["id"].", ".$_SESSION["utilisateur"]["id"].", \"$nom\", \"$description\", '".date('Y-m-d H:i:s')."')";
    mysqli_query($bdd, $query) or die("Requête non conforme0101");
    reload_current_page();
}


if(isset($_POST['modifierCategorieactualite'])) 
{
	$id_cat_actualite = strip_tags(htmlspecialchars(trim( crypt_decrypt_chaine($_POST['id_cat_actualite'], 'D') ))); 
	$nom            = strip_tags(htmlspecialchars(trim($_POST["nom"])));  
	$description    = strip_tags(htmlspecialchars(trim($_POST["description"])));  

    $imageBase64   = $_POST['imageBase64'];
    $fileExtension = $_POST['fileExtension'];   

    if(!empty($imageBase64))
    {
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
		    $query = "UPDATE cat_actualites 
		    		  SET nom = \"$nom\",
		    		      description = \"$description\",
		    		      image = \"$image\"  
		    		  WHERE id_configuration = ".$_SESSION["configuration"]["id"]." AND 
		    		        id=".$id_cat_actualite;
		    mysqli_query($bdd, $query) or die("Requête non conforme0101");
	    }
    }
    else
    {
	    $query = "UPDATE cat_actualites 
	    		  SET nom = \"$nom\",
	    		      description = \"$description\"
	    		  WHERE id_configuration = ".$_SESSION["configuration"]["id"]." AND 
	    		        id=".$id_cat_actualite;
	    mysqli_query($bdd, $query) or die("Requête non conforme0101");
    }

    reload_current_page();
} 


if(isset($_POST['supprimerCategorieactualite']))  
{
    $id_cat_actualites = strip_tags(htmlspecialchars(trim( crypt_decrypt_chaine($_POST['id_cat_actualites'], 'D') ))); 

	$query = "SELECT * FROM cat_actualites WHERE id_configuration = ".$_SESSION['configuration']['id']." AND id = ".$id_cat_actualites;
	$result = mysqli_query($bdd,$query) or die ("system error");
	$catactualite = mysqli_fetch_assoc($result);

	$supprImage = '../fichiers/uploads/'.$catactualite['image']; 
	@unlink($supprImage); 

	$query = "DELETE FROM cat_actualites WHERE id_configuration = ".$_SESSION['configuration']['id']." AND id = ".$id_cat_actualites;
	mysqli_query($bdd,$query) or die ("system error"); 

	reload_current_page();
}
?>

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Catégories actualités</title>

    <?php include('includes/php/includes-css.php');?>

  </head>


  <body>

    <main class="main" id="top">
    	
      <?php include('includes/php/menu.php');?>

      <?php include('includes/php/header.php');?>

      <div class="content">

        <div class="pb-5">
              <div class="mb-6">
                <h2 class="mb-2">Catégories actualités</h2>
                <h5 class="text-body-tertiary fw-semibold">Gérer les catégories actualites</h5>
              </div>

              <div class="page-section">
                  <div class="card card-fluid">
                    <div class="card-header border-0 p-1">
                        <div class="d-flex justify-content-end align-items-center">
                            <button class="btn btn-primary btn-sm" type="button" id="exampleModal" data-bs-toggle="modal" data-bs-target="#ModalAjouterCategoieactualite">
                            <i class='fas fa-list'></i> Catégorie
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                      <table class="table table-hover m-0">
                        <thead class="thead-">
                          <tr>
                            <th>n°</th>
                            <th></th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $query = "SELECT *, UPPER(nom) AS nom_actualite
                                      FROM cat_actualites 
                                      WHERE id_configuration =".$_SESSION["configuration"]["id"]." 
                                      ORDER BY nom, id";
                            $resultat = mysqli_query($bdd, $query) or die("Requête non conforme"); 
                            $ligne = 0; 
                            while($cat_actualites = mysqli_fetch_array($resultat))
                            {
                                

                              echo "<form method='post' action='actualites_cats.php'>
                                      <tr>
                                          <td>".++$ligne."</td>        
                                          <td>".affImgAdmin('50px', '50px', $cat_actualites['image'], '')."</td>  
                                          <td  style='white-space: normal;'>".safe_safe_ucfirst($cat_actualites["nom_actualite"])."</td>              
                                          <td class='text-truncate ps-3' style='max-width: 250px;' title='".safe_safe_ucfirst($cat_actualites["description"])."'>".safe_safe_ucfirst($cat_actualites["description"])."</td>  
                                          <td class='text-end'>

<button type='button' class='btn btn-light btn-sm modififierInfos' 
    id_cat_actualite='".crypt_decrypt_chaine($cat_actualites['id'], 'C')."'
    nom=\"".$cat_actualites['nom']."\"
    description=\"".$cat_actualites['description']."\">
    <i class='fas fa-cogs'></i>
</button>

<button type='submit' name='supprimerCategorieactualite' class='btn btn-light supprimer btn-sm'  onclick=\"return confirm('Voulez-vous supprimer ?')\"><i class='fas fa-trash-alt'></i></button>

<input type='hidden' name='id_cat_actualites' value='".crypt_decrypt_chaine($cat_actualites['id'], 'C')."'>
                                          </td>  
                                      </tr>
                                    </form>";
                      
                            }
                            ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
              </div>
        </div>

        <form action="actualites_cats.php" method="post">
            <div class="modal fade" id="ModalAjouterCategoieactualite" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Ajouter catégorie actualité</h5>
                            <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs-9"></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                              <div class="col-md-12">
                                  <label class="form-label">Nom</label>
                                  <input name="nom" type="text" class="form-control"/>
                              </div>

                              <div class="col-md-12">
                                  <label class="form-label">Description</label>
                                  <textarea name="description" rows="4" class="form-control"></textarea>
                              </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button name='ajouterCategorieactualite' type="submit" class="btn btn-primary">Ajouter</button>
                            <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>


        <form action="actualites_cats.php" method="post">
            <div class="modal fade" id="ModalModifCategoieactualite" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Modifier infos catégorie actualité</h5>
                            <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs-9"></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                              <div class="col-md-12">
                                  <label class="form-label">Nom</label>
                                  <input name="nom" type="text" class="form-control"/>
                              </div>

                              <div class="col-md-12">
                                  <label class="form-label">Description</label>
                                  <textarea name="description" rows="4" class="form-control"></textarea>
                              </div>

                              <div class="col-md-12">
			                    	<div class='mb-3'>
			                            <label class="form-label">Image</label>
			                            <input type="file" class="form-control" onchange="uploadImgactualite(this)" accept="image/*" />
			                    	</div>
                              </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                        	<input type='hidden' name='id_cat_actualite'>
                        	<input type="hidden" name='imageBase64'> 
                        	<input type="hidden" name='fileExtension'> 
                            <button type="submit"  name='modifierCategorieactualite'class="btn btn-primary">Ajouter</button>
                            <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
 
        
        <?php include('includes/php/footer.php');?>

      </div>
      
    </main>

    <?php include('includes/php/includes-js.php');?>

  </body>

</html>


<script>
$('.modififierInfos').click(function(){     

    $("#ModalModifCategoieactualite input[name='nom']").val($(this).attr('nom'));
    $("#ModalModifCategoieactualite textarea[name='description']").val($(this).attr('description'));
    $("#ModalModifCategoieactualite input[name='id_cat_actualite']").val($(this).attr('id_cat_actualite')); 


    $('#ModalModifCategoieactualite').modal('show');
});
</script> 


<script>
function uploadImgactualite(element)
{
  $("#ModalModifCategoieactualite input[name='imageBase64']").val(''); 
  $("#ModalModifCategoieactualite input[name='fileExtension']").val(''); 

  var file          = $(element)[0].files[0];
  var filename      = file.name;
  var fileExtension = filename.split('.').pop();
  
  const reader = new FileReader();
  reader.onload = function (e) {
      const img = new Image();
      img.onload = function() {
          const canvas = document.createElement('canvas');
          const ctx = canvas.getContext('2d');
          const maxWidth = 1160;
          const maxHeight = 600;

          let width = img.width;
          let height = img.height;

          if (width > maxWidth || height > maxHeight) {
              const ratio = Math.min(maxWidth / width, maxHeight / height);
              width *= ratio;
              height *= ratio;
          }

          canvas.width = width;
          canvas.height = height;

          ctx.drawImage(img, 0, 0, width, height);

          const compressedDataUrl = canvas.toDataURL('image/jpeg', 0.7);

          $("#ModalModifCategoieactualite input[name='imageBase64']").val(compressedDataUrl); 
          $("#ModalModifCategoieactualite input[name='fileExtension']").val(fileExtension); 

      };
      img.src = e.target.result;
  };
  reader.readAsDataURL(file);
}
</script>