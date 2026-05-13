<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 
?>

<?php
if(isset($_POST['ajouterCategorieevent'])) 
{
	$nom         = strip_tags(htmlspecialchars(trim($_POST["nom"])));   
	$description = strip_tags(htmlspecialchars(trim($_POST["description"])));  

    $query = "INSERT INTO event_cat (id_configuration, id_utilisateur, nom, description, date_heure) 
              VALUES (".$_SESSION["configuration"]["id"].", ".$_SESSION["utilisateur"]["id"].", \"$nom\", \"$description\", '".date('Y-m-d H:i:s')."')";
    mysqli_query($bdd, $query) or die("Requête non conforme0101");
    reload_current_page();
}


if(isset($_POST['modifierCategorieevent'])) 
{
	$id_cat_event = strip_tags(htmlspecialchars(trim( crypt_decrypt_chaine($_POST['id_cat_event'], 'D') ))); 
	$nom            = strip_tags(htmlspecialchars(trim($_POST["nom"])));  
	$description    = strip_tags(htmlspecialchars(trim($_POST["description"]))); 
    
	    $query = "UPDATE event_cat 
	    		  SET nom = \"$nom\",
	    		      description = \"$description\"
	    		  WHERE id_configuration = ".$_SESSION["configuration"]["id"]." AND 
	    		        id=".$id_cat_event;
	    mysqli_query($bdd, $query) or die("Requête non conforme0101");

    reload_current_page();
} 


if(isset($_POST['supprimerCategorieevent']))  
{
    $id_event_cat = strip_tags(htmlspecialchars(trim( crypt_decrypt_chaine($_POST['id_event_cat'], 'D') ))); 

	$query = "SELECT * FROM event_cat WHERE id_configuration = ".$_SESSION['configuration']['id']." AND id = ".$id_event_cat;
	$result = mysqli_query($bdd,$query) or die ("system error");
	$catevent = mysqli_fetch_assoc($result);


	$query = "DELETE FROM event_cat WHERE id_configuration = ".$_SESSION['configuration']['id']." AND id = ".$id_event_cat;
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

    <title>Catégories évenement</title>

    <?php include('includes/php/includes-css.php');?>

  </head>


  <body>

    <main class="main" id="top">
    	
      <?php include('includes/php/menu.php');?>

      <?php include('includes/php/header.php');?>

      <div class="content">

        <div class="pb-5">
              <div class="mb-6">
                <h2 class="mb-2">Catégories évenement</h2>
                <h5 class="text-body-tertiary fw-semibold">Gérer les catégories évenement</h5>
              </div>

              <div class="page-section">
                  <div class="card card-fluid">
                    <div class="card-header border-0 p-1">
                        <div class="d-flex justify-content-end align-items-center">
                            <button class="btn btn-primary btn-sm" type="button" id="exampleModal" data-bs-toggle="modal" data-bs-target="#ModalAjouterCategoieevent">
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
                            $query = "SELECT *, UPPER(nom) AS nom_event
                                      FROM event_cat 
                                      WHERE id_configuration =".$_SESSION["configuration"]["id"]." 
                                      ORDER BY nom, id";
                            $resultat = mysqli_query($bdd, $query) or die("Requête non conforme"); 
                            $ligne = 0; 
                            while($event_cat = mysqli_fetch_array($resultat))
                            {
                                

                              echo "<form method='post' action='event_cats.php'>
                                      <tr>
                                          <td>".++$ligne."</td>         
                                          <td  style='white-space: normal;'>".safe_safe_ucfirst($event_cat["nom_event"])."</td>              
                                          <td class='text-truncate ps-3' style='max-width: 250px;' title='".safe_safe_ucfirst($event_cat["description"])."'>".safe_safe_ucfirst($event_cat["description"])."</td>  
                                          <td class='text-end'>

<button type='button' class='btn btn-light btn-sm modififierInfos' 
    id_cat_event='".crypt_decrypt_chaine($event_cat['id'], 'C')."'
    nom=\"".$event_cat['nom']."\"
    description=\"".$event_cat['description']."\">
    <i class='fas fa-cogs'></i>
</button>

<button type='submit' name='supprimerCategorieevent' class='btn btn-light supprimer btn-sm'  onclick=\"return confirm('Voulez-vous supprimer ?')\"><i class='fas fa-trash-alt'></i></button>

<input type='hidden' name='id_event_cat' value='".crypt_decrypt_chaine($event_cat['id'], 'C')."'>
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

        <form action="event_cats.php" method="post">
            <div class="modal fade" id="ModalAjouterCategoieevent" tabindex="-1" aria-hidden="true">
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
                            <button name='ajouterCategorieevent' type="submit" class="btn btn-primary">Ajouter</button>
                            <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>


        <form action="event_cats.php" method="post">
            <div class="modal fade" id="ModalModifCategoieevent" tabindex="-1" aria-hidden="true">
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

                            </div>
                        </div>
                        <div class="modal-footer">
                        	<input type='hidden' name='id_cat_event'>
                            <button type="submit"  name='modifierCategorieevent'class="btn btn-primary">Ajouter</button>
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

    $("#ModalModifCategoieevent input[name='nom']").val($(this).attr('nom'));
    $("#ModalModifCategoieevent textarea[name='description']").val($(this).attr('description'));
    $("#ModalModifCategoieevent input[name='id_cat_event']").val($(this).attr('id_cat_event')); 


    $('#ModalModifCategoieevent').modal('show');
});
</script> 
