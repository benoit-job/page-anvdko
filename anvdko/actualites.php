<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 
?>

<?php
if(isset($_POST['ajouteractualite'])) 
{
    $nom_actualite = strip_tags(htmlspecialchars(trim($_POST["nom_actualite"])));   
    $reference = strip_tags(htmlspecialchars(trim($_POST["reference"])));   
    $date = strip_tags(htmlspecialchars(trim($_POST["date"])));   

    $date_formatee = DateTime::createFromFormat('d/m/Y', $date);
    $date_mysql = $date_formatee->format('Y-m-d');

    $query = "INSERT INTO actualites (id_configuration, id_utilisateur, nom, date_act, reference, date_heure) VALUES (".$_SESSION["configuration"]["id"].",  ".$_SESSION["utilisateur"]["id"].", \"$nom_actualite\", \"$date_mysql\",  \"$reference\", '".date('Y-m-d H:i:s')."')";
    mysqli_query($bdd, $query) or die("Requête non conforme0101");
}


if(isset($_POST['updateProdCatPrincipale']))
{
    if (isset($_POST['id_categorie']) && is_array($_POST['id_categorie'])) {
        $id_categorie = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST['id_categorie'][0], 'D'))));

        if (isset($_POST['ids_actualite']) && !empty($_POST['ids_actualite'])) {
            foreach (explode(',', $_POST['ids_actualite']) as $id_actualite) {
                $id_actualite = strip_tags(htmlspecialchars(trim($id_actualite)));

                $query = "UPDATE actualites 
                            SET id_cat_actualite = ".empty_to_NULL($id_categorie)." 
                            WHERE id = ".$id_actualite." AND id_configuration = '".$_SESSION["configuration"]["id"]."'";

                mysqli_query($bdd, $query) or die("Requête non conforme");

            }
        }
    }

    reload_current_page();
}

//DELETE FROM
if(isset($_POST["supprimeractualite"]))
{
    $id_actualite = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST["id_actualite"], 'D'))));
    $query = "DELETE FROM actualites WHERE id ='".$id_actualite."'";
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

    <title>actualites</title>

    <?php include('includes/php/includes-css.php');?>

  </head>


  <body>

    <main class="main" id="top">
    	
      <?php include('includes/php/menu.php');?>

      <?php include('includes/php/header.php');?>

      <div class="content">

        <div class="pb-5">
			<div class="mb-6">
				<h2 class="mb-2">Actualités</h2>
				<h5 class="text-body-tertiary fw-semibold">Gérer vos différents actualités </h5>
			</div>

			<div class="page-section">
			  	<div class="card card-fluid">
				    <div class="card-header border-0 p-1">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">    
                            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#ModalAjouteractualite">
                                <i class='fas fa-newspaper'></i> Actualité
                            </button>

                            <button id='btnCatactualite' class="btn btn-sm btn-phoenix-secondary" style='display: none;' data-bs-toggle="modal" data-bs-target="#ModalCatPrincipale">
                                <i class='fas fa-list'></i> Catégorie actualité
                            </button>

                            <div class="input-group" style="width: 170px;">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control form-control-sm" id="input_recherche" placeholder="Rechercher...">
                            </div>

                            <div class="input-group" style="width: 170px;">
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
	                        <th>Date actualité</th>
	                        <th>Référence</th>
	                        <th></th>
	                      </tr>
	                    </thead>
	                    <tbody id='listeactualites'> 
	                        <tr id='spinner'>
	                            <td colspan='6' class='text-center'>
                                    <div class='spinner-border spinner-border-sm' role='status' aria-hidden="false">
                                       <span class='visually-hidden'>Chargement...</span>
                                    </div>
                                </td>
	                        </tr>
	                    </tbody>
	                  </table>
	                </div>
                </div>
            </div>
        </div>

        <form action="actualites.php" method="post">  
            <div class="modal fade" id="ModalAjouteractualite" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Ajouter actualité</h5>
                            <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs-9"></span></button>
                        </div>
                        <div class="modal-body">
                            <div>
                                <label class="form-label">Titre</label>
                                <input name="nom_actualite" type="text" class="form-control" required />
                            </div>
                            <div>
                                <label class="form-label" for="datepickerVal">Date actualité</label>
                                <input class="form-control datetimepicker" id="datepickerVal" name="date" type="text" placeholder="d/m/y" required="required"
                                    data-options='{
                                        "disableMobile": true,
                                        "allowInput": true,
                                        "dateFormat": "d/m/Y"
                                    }' />
                                <div class="invalid-feedback">This field is required</div>                        
                                </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" class="form-control" name="reference" id="reference">
                            <button name='ajouteractualite' type="submit" class="btn btn-primary">Ajouter</button>
                            <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
 

        <form action="actualites.php" method="post">
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
                            <input type='hidden' name="ids_actualite">
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

        var nbreactualite = 0;

        while (await listeactualites(nbreactualite)) {
            nbreactualite += 20;
        }

        $('#listeactualites #spinner').remove(); 
    });
</script>


  </body>

</html>

<script>
  // Simuler un compteur par catégorie (devrait venir d'une BDD en vrai)
  let compteurParCategorie = {};

  $(document).ready(function () {
    $('input[name="date"]').on('change', function () {
      let dateStr = $(this).val(); // ex: "08/04/2025"
      let titre = $('input[name="nom_actualite"]').val().trim();

      if (dateStr && titre) {
        let dateParts = dateStr.split('/');
        let jour = dateParts[0];
        let mois = dateParts[1];

        // Clé de catégorie pour compteur
        let cleCategorie = titre.toLowerCase().replace(/\s+/g, '_'); // ex: covid_update

        if (!compteurParCategorie[cleCategorie]) {
          compteurParCategorie[cleCategorie] = 1;
        } else {
          compteurParCategorie[cleCategorie]++;
        }

        // Format numéro sur 2 chiffres
        let numActu = compteurParCategorie[cleCategorie].toString().padStart(2, '0');

        let reference = `REF_ANVDKO${mois}${jour}_${numActu}-CI`;

        $('#reference').val(reference);
      }
    });

    // Si on modifie le titre, remettre le compteur à jour au changement de date
    $('input[name="nom_actualite"]').on('blur', function () {
      $('input[name="date"]').trigger('change');
    });
  });

</script>


<script>
function listeactualites(nbreactualite) {
    return new Promise(function(resolve, reject) {

        $.ajax({
            url: 'ajax_autre.php',
            type: 'post',
            data: {
                nbreactualite: nbreactualite, 
                liste_actualites: ''
            },
            dataType: 'html',
            success: function (data) {

                if(data.trim() !== '') {
                    $('#listeactualites').append(data);
                    resolve(true); 
                } 
                else {
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
        var dateActu = $(this).find('.date-actualite').text().trim();
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

    var ids_actualite = selectedIds.join(','); 

    if(ids_actualite.trim() == '')
    {
        $('#btnCatactualite').hide('fast'); 
    }
    else 
    {
        $('#btnCatactualite').show('fast'); 
    }

    $('#ModalCatPrincipale input[name="ids_actualite"]').val(ids_actualite); 
}
</script>
