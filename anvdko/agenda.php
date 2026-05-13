<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");
?>
<?php

if(isset($_POST['updateProdCatPrincipale']))
{
    if (isset($_POST['id_categorie']) && is_array($_POST['id_categorie'])) {
        $id_categorie = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST['id_categorie'][0], 'D'))));

        if (isset($_POST['ids_agenda']) && !empty($_POST['ids_agenda'])) {
            foreach (explode(',', $_POST['ids_agenda']) as $id_agenda) {
                $id_agenda = strip_tags(htmlspecialchars(trim($id_agenda)));

                $query = "UPDATE agenda 
                            SET id_cat_agenda = ".empty_to_NULL($id_categorie)." 
                            WHERE id = ".$id_agenda." AND id_configuration = '".$_SESSION["configuration"]["id"]."'";

                mysqli_query($bdd, $query) or die("Requête non conforme");

            }
        }
    }

    reload_current_page();
}

//DELETE FROM
if(isset($_POST["supprimeragenda"]))
{
    $id_agenda = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST["id_agenda"], 'D'))));
    $query = "DELETE FROM agenda WHERE id ='".$id_agenda."' AND id_configuration = '".$_SESSION["configuration"]["id"]."'";
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

    <title>Agenda</title>

    <?php include('includes/php/includes-css.php');?>

  </head>


  <body>

    <main class="main" id="top">
    	
      <?php include('includes/php/menu.php');?>

      <?php include('includes/php/header.php');?>

      <div class="content">

        
        <div class="mb-9">
          <div class="mx-n4 mx-lg-n6 mt-n5 position-relative mb-md-9" style="height:208px">
            <div class="bg-holder bg-card d-dark-none" style="background-image:url(assets/img/bg/bg-40.png);background-size:cover;">
            </div>
            <!--/.bg-holder-->

            <div class="bg-holder bg-card d-light-none" style="background-image:url(assets/img/bg/bg-dark-40.png);background-size:cover;">
            </div>
            <!--/.bg-holder-->

            <div class="faq-title-box position-relative bg-body-emphasis border border-translucent p-6 rounded-3 text-center mx-auto">
              <h3>Rechercher dans agenda</h3>
              <p class="my-3">Trouvez facilement un événement déjà enregistré en effectuant une recherche</p>
              <div class="search-box w-100">
                <form class="position-relative" data-bs-toggle="search" data-bs-display="static">
                  <input class="form-control search-input search" id="input_recherche" type="search" placeholder="Rechercher un événement..." aria-label="Search" /><span class="fas fa-search search-box-icon"></span>
                </form>
              </div>
            </div>
          </div>

          <div class="page-section">
			  	<div class="card card-fluid">
				    <div class="card-header border-0 p-1">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">    
                             <a href="agenda_details.php?ajouterAgenda=" class="btn btn-primary btn-sm"><i class='fas fa-calendar-alt'></i> agenda</a>

                            <button id='btnCatagenda' class="btn btn-sm btn-phoenix-secondary" style='display: none;' data-bs-toggle="modal" data-bs-target="#ModalCatPrincipale">
                                <i class='fas fa-list'></i> Catégorie agenda
                            </button>

                            <div class="input-group" style="width: 250px;">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                                <input type="date" class="form-control form-control-sm" id="filtre_date">
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
	                        <th>Titre</th>
	                        <th>Catégorie</th>
	                        <th>Date agenda</th>
	                        <th>Référence</th>
	                        <th>Satut</th>
	                        <th></th>
	                      </tr>
	                    </thead>
	                    <tbody id="listeagendas"> 
                          <tr id="spinner">
                              <td colspan="6" class="text-center">
                                  <div class="spinner-border spinner-border-sm" role="status" aria-hidden="false">
                                      <span class="visually-hidden">Chargement...</span>
                                  </div>
                              </td>
                          </tr>
                      </tbody>
	                  </table>
	                </div>
                </div>
            </div>

        </div>

        
        <form action="agenda.php" method="post">
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
                    FROM cat_actualites ORDER BY id";
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
                            <input type='hidden' name="ids_agenda">
                            <button type="submit" name='updateProdCatPrincipale' class="btn btn-primary">Ajouter</button>
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

    
    <script>
        $(document).ready(async function () {

            var nbreagenda = 0;

            while (await listeagendas(nbreagenda)) {
                nbreagenda += 20;
            }

            $('#listeagendas #spinner').remove(); 
        });
    </script>

  </body>

</html>



<script>
  function listeagendas(nbreagenda) {
      return new Promise(function(resolve, reject) {

          $.ajax({
              url: 'ajax_autre.php',
              type: 'post',
              data: {
                  nbreagenda: nbreagenda, 
                  liste_agendas: ''
              },
              dataType: 'html',
              success: function (data) {
                  if (data.trim() !== '') {
                      $('#listeagendas').append(data);  // Affiche les données dans le tableau
                      resolve(true);
                  } else {
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

    $('#filtre_date').on('change', function () {
    var valeurRecherchee = $(this).val(); // yyyy-mm-dd
    if (!valeurRecherchee) {
        $('table tbody tr').show();
        return;
    }

    // Convertir en dd/mm/yyyy
    var partie = valeurRecherchee.split('-'); // [yyyy, mm, dd]
    var dateFormatee = partie[2] + '/' + partie[1] + '/' + partie[0]; // dd/mm/yyyy

    $('table tbody tr').each(function () {
        var dateActu = $(this).find('.date-agenda').text().trim();
        if (dateActu === dateFormatee) {
            $(this).show();
        } else {
            $(this).hide();
        }
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

    var ids_agenda = selectedIds.join(','); 

    if(ids_agenda.trim() == '')
    {
        $('#btnCatagenda').hide('fast'); 
    }
    else 
    {
        $('#btnCatagenda').show('fast'); 
    }

    $('#ModalCatPrincipale input[name="ids_agenda"]').val(ids_agenda); 
}
</script>