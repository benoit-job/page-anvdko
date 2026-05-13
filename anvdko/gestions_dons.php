<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");
?>
<?php
if (isset($_POST["ajouterdon"])) {
    $nom = strip_tags(htmlspecialchars(trim($_POST["nom"])));
    $prenom = strip_tags(htmlspecialchars(trim($_POST["prenom"])));
    $telephone = strip_tags(htmlspecialchars(trim($_POST["telephone"])));
    $email = strip_tags(htmlspecialchars(trim($_POST["email"])));
    $nationnalite = strip_tags(htmlspecialchars(trim($_POST["nationnalite"])));
    $montant = strip_tags(htmlspecialchars(trim($_POST["montant"])));
    $moyen_paiement = strip_tags(htmlspecialchars(trim($_POST["moyen_paiement"])));

    $query = "INSERT INTO faire_don (id_configuration, id_utilisateur, nom, prenom, telephone, email, nationnalite, montant, moyen_paiement, date_heure) 
              VALUES (".$_SESSION['configuration']['id'].", ".$_SESSION['utilisateur']['id'].", 
                      \"$nom\", \"$prenom\", \"$telephone\", \"$email\", \"$nationnalite\", 
                      \"$montant\", \"$moyen_paiement\", '".date('Y-m-d H:i:s')."')";

    mysqli_query($bdd, $query) or die("Requête non conforme DON");

    reload_current_page();
}


//DELETE FROM
if(isset($_POST["supprimerdon"]))
{
    $id_faire_don = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST["id_faire_don"], 'D'))));
    $query = "DELETE FROM faire_don WHERE id ='".$id_faire_don."' AND id_configuration = '".$_SESSION["configuration"]["id"]."'";
    mysqli_query($bdd, $query) or die("Requête non conforme"); 
    reload_current_page();
}
?>

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gestion don</title>

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
              <h3>Effectuez une Recherche</h3>
              <p class="my-3">Trouvez facilement en effectuant une recherche</p>
              <div class="search-box w-100">
                <form class="position-relative" data-bs-toggle="search" data-bs-display="static">
                  <input class="form-control search-input search" id="input_recherche" type="search" placeholder="Rechercher un ..." aria-label="Search" /><span class="fas fa-search search-box-icon"></span>
                </form>
              </div>
            </div>
          </div>

          <div class="page-section">
			  	<div class="card card-fluid">
				    <div class="card-header border-0 p-1">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">    
                             <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class='fas fa-calendar-alt'></i> Faire un don</button>

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
	                        <th>n°</th>
	                        <th>Nom & prénoms </th>
	                        <th>Téléphone </th>
	                        <th>E-mail</th>
	                        <th>Nationnalité</th>
	                        <th>Montant</th>
	                        <th>Moyen paiement</th>
	                        <th>Date heure</th>
	                        <th></th>
	                      </tr>
	                    </thead>
	                    <tbody id="listedons"> 
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

        
    <!-- ===============================================-->
    <form method="post" action="gestions_dons.php">
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ajouter un don</h5>
                        <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close">
                            <span class="fas fa-times fs-9"></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text" name="nom" class="form-control" required/>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prénoms</label>
                                <input type="text" name="prenom" class="form-control" required/>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Téléphone</label>
                                <input type="tel" name="telephone" class="form-control" required/>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">E-mail</label>
                                <input type="email" name="email" class="form-control" required/>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nationalité</label>
                                <input type="text" name="nationnalite" class="form-control" required/>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3 text-center">
                            <label class="form-label d-block"><i class="fa fa-money-bill-wave"></i> Montant du don</label>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="montant" id="montant100" value="1000" required>
                                <label class="form-check-label" for="montant100">1000 F CFA</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="montant" id="montant2000" value="2000" required>
                                <label class="form-check-label" for="montant2000">2 000 F CFA</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="montant" id="montant5000" value="5000" required>
                                <label class="form-check-label" for="montant5000">5 000 F CFA</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="montant" id="montant10000" value="10000" required>
                                <label class="form-check-label" for="montant10000">10 000 F CFA</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="montant" id="montant20000" value="20000" required>
                                <label class="form-check-label" for="montant20000">20 000 F CFA</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="montant" id="montant30000" value="30000" required>
                                <label class="form-check-label" for="montant30000">30 000 F CFA</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="montant" id="montant40000" value="40000" required>
                                <label class="form-check-label" for="montant40000">40 000 F CFA</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="montant" id="montant50000" value="50000" required>
                                <label class="form-check-label" for="montant50000">50 000 F CFA</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="montant" id="montant60000" value="60000" required>
                                <label class="form-check-label" for="montant60000">60 000 F CFA</label>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3 text-center">
                            <label class="form-label d-block"><i class="fa fa-credit-card"></i> Moyen de paiement</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="moyen_paiement" value="mobile Money" required>
                                <label class="form-check-label"><i class="fa fa-mobile-alt"></i> Mobile Money</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="moyen_paiement" value="carte Bancaire">
                                <label class="form-check-label"><i class="fa fa-credit-card"></i> Carte Bancaire</label>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" name="ajouterdon" class="btn btn-primary">Valider</button>
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

            var nbredon = 0;

            while (await listedons(nbredon)) {
                nbredon += 20;
            }

            $('#listedons #spinner').remove(); 
        });
    </script>

  </body>

</html>

<script>
  function listedons(nbredon) {
      return new Promise(function(resolve, reject) {

          $.ajax({
              url: 'ajax_autre.php',
              type: 'post',
              data: {
                  nbredon: nbredon, 
                  liste_dons: ''
              },
              dataType: 'html',
              success: function (data) {
                  if (data.trim() !== '') {
                      $('#listedons').append(data);  // Affiche les données dans le tableau
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
        var dateActu = $(this).find('.date-don').text().trim(); // exemple : 13/04/2025 à 13:20
        var dateSansHeure = dateActu.split(' à ')[0]; // extrait juste "13/04/2025"

        if (dateSansHeure === dateFormatee) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});


</script>