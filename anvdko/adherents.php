<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");
?>
<?php

//DELETE FROM
if(isset($_POST["supprimermembre"]))
{
    $id_membre = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST["id_membre"], 'D'))));
    $query = "DELETE FROM membres WHERE id ='".$id_membre."'";
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

    <title>Membres</title>

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
              <h3>Rechercher un membre</h3>
              <p class="my-3">Trouvez facilement un membre déjà enregistré en effectuant une recherche</p>
              <div class="search-box w-100">
                <form class="position-relative" data-bs-toggle="search" data-bs-display="static">
                  <input class="form-control search-input search" id="input_recherche" type="search" placeholder="Rechercher un membre..." aria-label="Search" /><span class="fas fa-search search-box-icon"></span>
                </form>
              </div>
            </div>
          </div>

          <div class="page-section">
			  	<div class="card card-fluid">
				    <div class="card-header border-0 p-1">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">    
                             <a href="adherent_details.php?ajouterAdherent=" class="btn btn-primary btn-sm"><i class='fas fa-users'></i> Adhérent </a>

                            <button id='btnSmsMembre' class="btn btn-sm btn-phoenix-secondary" style='display: none;' data-bs-toggle="modal" data-bs-target="#ModalCatPrincipale">
                                <i class='fas fa-sms'></i> Envoyer SMS
                            </button>

                            <button class="btn btn-sm btn-phoenix-secondary" style='visibility: hidden;' data-bs-toggle="modal" data-bs-target="#ModalCatPrincipale">
                                <i class='fas fa-sms'></i> Envoyer SMS
                            </button>
                        </div>

	                </div>
	                <div class="table-responsive">
	                  <table class="table table-hover m-0" id='ma_table'> 
	                    <thead class="thead-">
	                      <tr>
                            <th>
                            <input type="checkbox" id="checkboxTous" onclick="toggleAllCheckboxes(this)">
                            </th>
	                        <th>n°</th>
	                        <th></th>
	                        <th>Nom & prénoms</th>
	                        <th>Téléphone</th>
	                        <th>Proféssion</th>
	                        <th>N° Adhésion</th>
	                        <th></th>
	                      </tr>
	                    </thead>
	                    <tbody id="listeadherent"> 
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

        
        <form action="adherents.php" method="post">
            <div class="modal fade" id="ModalCatPrincipale" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Envoyer un SMS</h5>
                    <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span class="fas fa-times fs-9"></span>
                    </button>
                    </div>
                    <div class="modal-body">
                    <!-- Conteneur où on affiche les IDs -->
                    <div id="affichageIds" class="mb-3 text-primary fw-bold"></div>

                    
                        <!-- Zone pour préremplir le message à envoyer -->
                        <div class="mb-3">
                        <label for="messageSms" class="form-label">Message à envoyer</label>
                        <textarea class="form-control" id="messageSms" name="messageSms" rows="4">
                        Bonjour cher membre, nous vous rappelons que votre adhésion est toujours active. Merci de votre fidélité !
                        </textarea>
                    </div>
                    <div class="modal-footer">
                    <input type="hidden" name="ids_membre">
                    <button type="submit" name="updateProdCatPrincipale" class="btn btn-primary">Ajouter</button>
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

            var nbreadherent = 0;

            while (await listeadherent(nbreadherent)) {
                nbreadherent += 20;
            }

            $('#listeadherent #spinner').remove(); 
        });
    </script>

  </body>

</html>



<script>
  function listeadherent(nbreadherent) {
      return new Promise(function(resolve, reject) {

          $.ajax({
              url: 'ajax_autre.php',
              type: 'post',
              data: {
                  nbreadherent: nbreadherent, 
                  liste_adherent: ''
              },
              dataType: 'html',
              success: function (data) {
                  if (data.trim() !== '') {
                      $('#listeadherent').append(data);  // Affiche les données dans le tableau
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
function toggleAllCheckboxes(source) {
    let checkboxes = document.querySelectorAll('.checkboxIdTable');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = source.checked;
    });

    // Met à jour les ID sélectionnés
    getSelectedCheckboxes();
}

function getSelectedCheckboxes() {
    var selectedIds = [];
    var selectedNumeros = [];

    document.querySelectorAll('.checkboxIdTable:checked').forEach(function(cb) {
        selectedIds.push(cb.value);
        selectedNumeros.push(cb.getAttribute('data-numero'));
    });

    var ids_membre = selectedIds.join(',');

    if (ids_membre.trim() === '') {
        $('#btnSmsMembre').hide('fast');
    } else {
        $('#btnSmsMembre').show('fast');
    }

    // On stocke les IDs sélectionnés dans le champ caché
    $('#ModalCatPrincipale input[name="ids_membre"]').val(ids_membre);

    // On affiche les NUMÉROS au lieu des IDs
    document.getElementById('affichageIds').innerHTML =
        selectedNumeros.map(num => `<span class="badge bg-success me-1">${num}</span>`).join(', ');
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