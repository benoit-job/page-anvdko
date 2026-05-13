<?php
  include("includes/connexion_acces_page.php");
  include("includes/connexion_bdd.php");
  include("includes/fonctions.php");
?>

<?php
if(isset($_POST['ajouterProduit'])) 
{
    $nom_produit = strip_tags(htmlspecialchars(trim($_POST["nom_produit"])));   

    $query = "INSERT INTO produits (id_site, nom) VALUES (".$_SESSION["site"]["id"].",\"$nom_produit\")";
    mysqli_query($bdd, $query) or die("Requête non conforme0101");
}


if(isset($_POST['updateProdCatPrincipale']))
{
    if (isset($_POST['id_categorie']) && is_array($_POST['id_categorie'])) {
        $id_categorie = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST['id_categorie'][0], 'D'))));

        if (isset($_POST['ids_produit']) && !empty($_POST['ids_produit'])) {
            foreach (explode(',', $_POST['ids_produit']) as $id_produit) {
                $id_produit = strip_tags(htmlspecialchars(trim($id_produit)));

                // Suppression des catégories précédentes
                $query = "DELETE FROM select_cats_produit WHERE id_produit = $id_produit AND id_cat_produit = $id_categorie"; 
                mysqli_query($bdd, $query) or die("Requête non conforme");

                // Insertion de la nouvelle catégorie
                $query = "INSERT INTO select_cats_produit (id_site, id_produit, id_cat_produit) 
                          VALUES (".$_SESSION['site']['id'].", $id_produit, ".empty_to_NULL($id_categorie).")";
                mysqli_query($bdd, $query) or die("Requête non conforme");
            }
        }
    }

    reload_current_page();
}

//DELETE FROM
if(isset($_POST["supprimerProduit"]))
{
    $id_produit = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST["id_produit"], 'D'))));
    $query = "DELETE FROM produits WHERE id ='".$id_produit."' AND id_site = '".$_SESSION["site"]["id"]."'";
    mysqli_query($bdd, $query) or die("Requête non conforme2222"); 
    reload_current_page();
}
?>

<!DOCTYPE html> 
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Produits</title>

    <?php include('includes/php/includes-css.php');?>

  </head>


  <body>

    <main class="main" id="top">
    	
      <?php include('includes/php/menu.php');?>

      <?php include('includes/php/header.php');?>

      <div class="content">

        <div class="pb-5">
			<div class="mb-6">
				<h2 class="mb-2">Produits</h2>
				<h5 class="text-body-tertiary fw-semibold">Gérer vos différents produits de votre boutique</h5>
			</div>

			<div class="page-section">
			  	<div class="card card-fluid">
				    <div class="card-header border-0 p-1">
						<div class="d-flex justify-content-between align-items-center">    
						    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#ModalAjouterProduit">
						        <i class='fas fa-plus'></i> Ajouter
						    </button>

						    <button id='btnCatProduit' class="btn btn-sm btn-phoenix-secondary" style='display: none;' data-bs-toggle="modal" data-bs-target="#ModalCatPrincipale">
						        <i class='fas fa-list'></i> Catégorie produit
						    </button>

						    <div class="input-group" style="width: 170px;">
						        <span class="input-group-text" id="basic-addon1">
						            <i class="fas fa-search"></i>
						        </span>
						        <input type="text" class="form-control form-control-sm" id="input_recherche" placeholder="Rechercher...">
						    </div>
						</div>
	                </div>
	                <div class="table-responsive">
	                  <table class="table table-hover m-0" id='ma_table'> 
	                    <thead class="thead-">
	                      <tr>
	                        <th><input type='checkbox' class='checkboxTous'></th>
	                        <th>n°</th>
	                        <th></th>
	                        <th>Nom</th>
	                        <th>Catégorie</th>
	                        <th>Prix</th>
	                        <th>Statut</th>
	                        <th></th>
	                      </tr>
	                    </thead>
	                    <tbody id='listeProduits'> 
	                        <tr id='spinner'>
	                            <td colspan='8' class='text-center'><div class='spinner-border spinner-border-sm'></div></td>
	                        </tr>
	                    </tbody>
	                  </table>
	                </div>
                </div>
            </div>
        </div>


        <?php include('includes/php/footer.php');?>

      </div>
      
    </main>

    <?php include('includes/php/includes-js.php');?>


        <form action="produits.php" method="post">  
            <div class="modal fade" id="ModalAjouterProduit" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Ajouter Produit</h5>
                            <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs-9"></span></button>
                        </div>
                        <div class="modal-body">
                            <div>
                                <label class="form-label">Nom Produit</label>
                                <input name="nom_produit" type="text" class="form-control" required />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button name='ajouterProduit' type="submit" class="btn btn-primary">Ajouter</button>
                            <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
 

        <form action="produits.php" method="post">
            <div class="modal fade" id="ModalCatPrincipale" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Catégorie principale</h5>
                            <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs-9"></span></button>
                        </div>
                        <div class="modal-body">
    <?php
    $query = "SELECT id, nom 
              FROM cat_produits 
              WHERE id_site = ".$_SESSION['site']['id']." AND 
                    statut IN ('visible','non visible') AND 
                    id NOT IN (SELECT id_parent FROM cat_nestable WHERE id_site = ".$_SESSION['site']['id'].")
              ORDER BY ordre, id";
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
    while($categorie = mysqli_fetch_array($resultat))
    {
      echo "<div class='form-check'>
              <input class='form-check-input' id='CP".$categorie['id']."' type='radio' name='id_categorie[]' value='".crypt_decrypt_chaine($categorie['id'], 'C')."'>
              <label class='form-check-label' for='CP".$categorie['id']."'>".safe_safe_ucfirst($categorie['nom'])."</label>
            </div>"; 
    }
    ?>
                    
                        </div>
                        <div class="modal-footer">
                            <input type='hidden' name="ids_produit">
                            <button type="submit" name='updateProdCatPrincipale' class="btn btn-primary">Ajouter</button>
                            <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <script>
            $(document).ready(async function () {

                var nbreProduit = 0;
                while (await listeProduits(nbreProduit)) 
                {
                    nbreProduit += 20;
                }

                $('#listeProduits #spinner').remove(); 
            });
        </script>

  </body>

</html>

<script>
function listeProduits(nbreProduit) 
{
    return new Promise(function(resolve, reject) {
        $.ajax({
            url: 'ajax.php',
            type: 'post',
            data: {
                nbreProduit: nbreProduit, 
                listeProduits: ''
            },
            dataType: 'html',
            success: function (data) {

                if(data.trim() !== '') 
                {  
                    $('#listeProduits').append(data);
                    resolve(true); 
                } 
                else 
                {
                    resolve(false); 
                }
            }
        });
    });
}
</script>


<script>
    $(document).ready(function() { 

        $('#input_recherche').on('keyup', function() {

            var value = $(this).val().toLowerCase();
            $('#ma_table tbody tr').filter(function() {

                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>

<script>
function getSelectedCheckboxes()
{
    var selectedIds = [];

    $('.checkboxIdTable:checked').each(function() {
        selectedIds.push($(this).val());
    });

    var ids_produit = selectedIds.join(','); 

    if(ids_produit.trim() == '')
    {
        $('#btnCatProduit').hide('fast'); 
    }
    else 
    {
        $('#btnCatProduit').show('fast'); 
    }

    $('#ModalCatPrincipale input[name="ids_produit"]').val(ids_produit); 
}
</script>
