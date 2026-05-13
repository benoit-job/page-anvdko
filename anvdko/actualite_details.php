<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 
?>

<?php
if(isset($_GET["id_actualite"]))
{
    $_SESSION["id_actualite"] = strip_tags(htmlspecialchars(trim( crypt_decrypt_chaine($_GET["id_actualite"], 'D') )));
    reload_current_page();
}


if (isset($_POST["actualiserInfosactualite"])) {
    $id_actualite = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST["id_actualite"], 'D'))));
    $nom = strip_tags(htmlspecialchars(trim($_POST["nom"])));
    $date = strip_tags(htmlspecialchars(trim($_POST["date"])));
    $nom_conferencie = strip_tags(htmlspecialchars(trim($_POST["nom_conferencie"])));
    $fonction_conferencie = strip_tags(htmlspecialchars(trim($_POST["fonction_conferencie"])));


    $description = $_POST["description"];

    // Vérification de l'image
    if (!empty($_FILES['image_conferencie']['tmp_name'])) {
        $fileExtension = pathinfo($_FILES['image_conferencie']['name'], PATHINFO_EXTENSION); // Extension du fichier
        $imageData = file_get_contents($_FILES['image_conferencie']['tmp_name']); // Lecture de l'image
        $destination = createPathFile('../fichiers/uploads/') . uniqid() . '.' . $fileExtension;

        // Sauvegarder l'image dans le dossier uploads
        if (file_put_contents($destination, $imageData)) {
            $image = str_replace('../fichiers/uploads/', '', $destination);

            // Requête SQL de mise à jour
            $query = "UPDATE actualites
                      SET nom = \"$nom\",
                          date_act = \"$date\",
                          nom_conferencie = \"$nom_conferencie\",
                          fonction_conferencie = \"$fonction_conferencie\",
                          image_conferencie = \"$image\",
                          description = ".empty_to_NULL($description)."
                      WHERE id_configuration = " . $_SESSION['configuration']['id'] . "
                      AND id = $id_actualite";
            mysqli_query($bdd, $query) or die("Requête non conforme0101");
        }
    } else {
        // Si aucune image n'a été téléchargée, mettre à jour sans image
        $query = "UPDATE actualites
                  SET nom = \"$nom\",
                      date_act = \"$date\",
                      nom_conferencie = \"$nom_conferencie\",
                      fonction_conferencie = \"$fonction_conferencie\",
                      description = ".empty_to_NULL($description)."
                  WHERE id_configuration = " . $_SESSION['configuration']['id'] . "
                  AND id = $id_actualite";
        mysqli_query($bdd, $query) or die("Requête non conforme0101");
    }
    

    if(isset($_POST['ids_categorie']))
    {
        foreach($_POST['ids_categorie'] as $id_categorie) 
        {
            $id_categorie = strip_tags(htmlspecialchars(trim($id_categorie)));

            $query = "UPDATE actualites 
                            SET id_cat_actualite = ".empty_to_NULL($id_categorie)." 
                            WHERE id = ".$id_actualite." AND id_configuration = '".$_SESSION["configuration"]["id"]."'";
            mysqli_query($bdd, $query) or die("Requête non conforme"); 
        }
    }

    reload_current_page();
}




$query = "SELECT *, DATE_FORMAT(date_act, '%d/%m/%Y') AS date_actualite
           FROM actualites WHERE id_configuration = ".$_SESSION['configuration']['id']." AND id =".$_SESSION['id_actualite'];
$resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
$_SESSION['actualite'] = mysqli_fetch_array($resultat);

// Vérifie si la date est bien au format attendu
$date_mysql = $_SESSION['actualite']['date_act'];

// Si la date n'est pas déjà au bon format, convertis-la
if (strtotime($date_mysql)) {
    $date_mysql = date("Y-m-d", strtotime($date_mysql));
}

?>

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Détails actualité</title>

    <?php include('includes/php/includes-css.php');?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet"> 

    <style type="text/css">  
        .note-editor .note-editable{min-height: 300px;}
        #divPrixactualite table tr:first-child .btnSuppr {visibility: hidden;}
    </style>
  </head>


  <body>

    <main class="main" id="top">
      
      <?php include('includes/php/menu.php');?>

      <?php include('includes/php/header.php');?>

      <div class="content">

        <div class="pb-5">
              <div class="mb-5">
                <nav class="mb-2" aria-label="breadcrumb">
                  <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <i class='breadcrumb-icon fa fa-angle-left mr-2'></i> 
                        <a href="actualites.php" class='text-secondary'>Retour</a>
                    </li>
                  </ol>
                </nav>
                <h4 class="mb-2">Référence actualité ( <span style='color: #2c2664;'> <?php echo safe_safe_ucfirst($_SESSION['actualite']['reference']);?> </span> )</h4>
              </div>

              <div class="page-section">

                  <form id='formulaireactualite' method='post' action='actualite_details.php' onsubmit="prepareSubmit()" enctype="multipart/form-data">
                      <div class='row'>
                          <div class='col-md-8'>

                              <div class="card card-fluid mb-4">
                                <div class="card-header p-3 text-uppercase">  
                                Informations de l'actualité
                                </div>
                                <div class="card-body p-2"> 
                                    <div class='row'>
                                        <div class='col-12 mb-1'>
                                            <div class="form-floating">
                            <input type="text" name="nom" class="form-control mb-1" value="<?php echo $_SESSION['actualite']['nom'];?>" required>
                            <label class="form-label">Titre</label>
                                            </div>                                       
                                        </div>
                                        <div class='col-12 mb-1'>
                                            <div class="form-floating">
                                                <input type="date" name="date" class="form-control" id="" value="<?php echo $date_mysql;?>">
                                                 <label class="form-label">Date de la rencotre</label>                      
                                            </div>                                         
                                        </div>
                                    </div>
                                </div>
                              </div>

                                <div class="card card-fluid mb-4" id='divPrixactualite'>
                                    <div class="card-header p-3 text-uppercase">  
                                        conférencié
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="row">

                                            <div class='col-12 mb-1'>
                                                <div class="form-floating">
                                                    <input type="text" name="nom_conferencie" class="form-control" value="<?php echo $_SESSION['actualite']['nom_conferencie'];?>">
                                                    <label class="form-label">Nom et prénoms</label>
                                                </div>                                       
                                            </div>

                                            <div class='col-12 mb-1'>
                                                <div class="form-floating">
                                                    <input type="text" name="fonction_conferencie" class="form-control" value="<?php echo $_SESSION['actualite']['fonction_conferencie'];?>">
                                                    <label class="form-label">Fonction</label>
                                                </div>                                       
                                            </div>

                                            <div class='col-12 mb-1'>
                                                <div class="form-floating">
                                                <div>
    <label class="form-label fw-bold" for="formFileMultiple">
        Image <span class="fst-italic text-secondary">(si possible)</span>
    </label>
    <!-- Champ de téléchargement d'image -->
    <input class="form-control" id="formFileMultiple" type="file" name="image_conferencie" accept="image/*">
    
    <!-- Afficher l'image actuelle si elle existe -->
    <?php if (!empty($_SESSION['actualite']['image_conferencie'])): ?>
        <div class="mt-3">
            <img src="../fichiers/uploads/<?php echo $_SESSION['actualite']['image_conferencie']; ?>" alt="Image actuelle" class="img-fluid rounded" style="max-width: 50px;">
        </div>
    <?php endif; ?>
</div>

                                                </div>                                       
                                            </div>

                                        </div>
                                    </div>
                                </div>

                              <div class="card card-fluid mb-4">
                                <div class="card-header p-3">
                                    Description
                                </div>
                                <div class="card-body p-2">
                                    <input type="hidden" name="description" id="hiddenDescription">
                                    <div id="editor"></div>
                                </div>
                              </div>

                          </div>
                          <div class='col-md-4'>

                              <div class="card card-fluid mb-4">
                                <div class="card-header p-3 d-flex justify-content-between align-items-center">
                                    <span>Image actualité</span>
                            <div class="d-flex">
                            <label for="fileInput" class="btn btn-link py-0 px-2 text-body fs-9"> <span class="fa-solid fa-plus"></span></label>
                            <input id="fileInput" type="file" class="d-none" onchange="uploadImgactualite(this)" accept="image/*" />
                            </div>
                                </div>
                                <div class="card-body p-5 mx-auto">
                                    <div id='divImgPrincipale'><?php echo affImgAdmin('200px', '200px', $_SESSION['actualite']['image'], '');?></div>
                                </div>
                              </div>   


                              <div class="card card-fluid mb-4">
                                <div class="card-header p-3">
                                    Catégorie 
                                </div>
                                <div class="card-body">
                                    <div>
                                        <?php
                                        $query = "SELECT id FROM actualites WHERE id = ".$_SESSION['actualite']['id']." LIMIT 1";
                                        $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
                                        $select_cats_actualite = mysqli_fetch_array($resultat); 

                                        if(!empty($select_cats_actualite)){$checked = 'checked';}else{$checked = '';}
                                        ?>

                                        <div class="form-check">
                                        <input id="CatCheck0" type="radio" name='ids_categorie[]' class="form-check-input" value='0' <?php echo $checked;?> />
                                        <label for="CatCheck0" class="form-check-label">Aucun</label>
                                        </div> 
                                        <?php
                                    // Étape 1 : récupérer l'id de la catégorie actuelle de l'actualité en cours
                                    $id_categorie_actuelle = 0;
                                    $query_cat = "SELECT id_cat_actualite FROM actualites WHERE id = ".$_SESSION['actualite']['id']." LIMIT 1";
                                    $result_cat = mysqli_query($bdd, $query_cat);
                                    if ($row = mysqli_fetch_assoc($result_cat)) {
                                        $id_categorie_actuelle = $row['id_cat_actualite'];
                                    }

                                    // Étape 2 : lister toutes les catégories disponibles
                                    $query = "SELECT id, nom FROM cat_actualites 
                                            WHERE id_configuration = ".$_SESSION['configuration']['id']." 
                                            ORDER BY id";
                                    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");

                                    // Étape 3 : afficher les radio et cocher celle correspondante
                                    while ($categorie = mysqli_fetch_assoc($resultat)) {
                                        $checked = ($categorie['id'] == $id_categorie_actuelle) ? 'checked' : '';
                                        echo "<div class='form-check mb-2'>
                                                <input id='CatCheck".$categorie['id']."' type='radio' name='ids_categorie[]' class='form-check-input' value='".$categorie['id']."' $checked>
                                                <label for='CatCheck".$categorie['id']."' class='form-check-label'>".safe_safe_ucfirst($categorie['nom'])."</label>
                                            </div>";
                                    }
                                    ?>

                                    </div>
                                </div>
                              </div> 
                              

                              <div class="card card-fluid mb-4">
                                <div class="card-header p-3 d-flex justify-content-between align-items-center">
                                    <span>Galerie</span>
                                    <div class="d-flex">
                                    <label for="fileInput2" class="btn btn-link py-0 px-2 text-body fs-9">
                                        <span class="fa-solid fa-plus"></span>
                                    </label>
                                    <input id="fileInput2" type="file" class="d-none" onchange="uploadGalerieactualite(this)" accept="image/*" multiple/>
                                    </div>          
                                </div>
                                <div class="card-body text-center p-0" id='tbodyGalerieactualite'>

                                </div>
                              </div> 

                          </div>
                      </div>

                      <div>

                        <input type='hidden' name='id_actualite' value="<?php echo crypt_decrypt_chaine($_SESSION['actualite']['id'], 'C');?>"> 

                        <input type='hidden' name='actualiserInfosactualite'>
                        <button id='btnSubmit' type="button" class="btn btn-primary">Valider modification</button>
                      </div>

                  </form>
              </div>
        </div>

        <?php include('includes/php/footer.php');?>

      </div>
      
    </main>

    <?php include('includes/php/includes-js.php');?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
    <script>
        $(document).ready(function() {
           $('#editor').summernote({
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'li', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                callbacks: {
                    onInit: function() {
                        let aproposContent = `<?php echo !empty($_SESSION['actualite']['description']) ? htmlspecialchars_decode($_SESSION['actualite']['description']) : ''; ?>`;
                        $('#editor').summernote('code', aproposContent);
                    },
                    onImageUpload: function(files) {
                        // Handle image upload here
                    },
                    onMediaDelete: function(target) {
                        // Handle media delete here
                    }
                }
            });

            // Fix dropdown toggle issue inside Summernote editor
            $('.note-editor').on('click', '.dropdown-toggle', function(e) {
                var $toggle = $(this);
                if (!$toggle.next().hasClass('show')) {
                    $toggle.parents('.dropdown-menu').first().find('.show').removeClass('show');
                }
                var $parent = $toggle.parents('.dropdown-menu').first();
                $parent.toggleClass('show');

                $toggle.next().toggleClass('show');

                $toggle.parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
                    $('.dropdown-submenu .show').removeClass('show');
                });

                return false;
            });

            // Close dropdowns when clicking outside Summernote editor
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.note-editor .dropdown-toggle').length) {
                    $('.dropdown-menu.show').removeClass('show');
                }
            });

            // Fix Summernote modal close issue with Bootstrap 5
            $(document).on('click', '.note-modal .close', function() {
                $(this).closest('.note-modal').modal('hide');
            });
        });

        function prepareSubmit() {
            var html = $('#editor').summernote('code');
            document.getElementById('hiddenDescription').value = html;
        }
    </script>

    <script>
	    $(document).ready( function () {

	        listeGalerieactualite();
	    });
    </script>

  </body>

</html>

<script>

function uploadImgactualite(element)
{
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

          $.ajax({
              url: 'upload.php',
              type: 'POST',
              data: { 
                  imageBase64: compressedDataUrl, 
                  filename: filename,
                  fileExtension: fileExtension,
                  id_actualite: "<?php echo crypt_decrypt_chaine($_SESSION['actualite']['id'], 'C');?>",    
                  uploadImagePrincipaleactualite: ''
              },
              success: function(response){

                  $('#divImgPrincipale').html(response);
              }
          });

      };
      img.src = e.target.result;
  };
  reader.readAsDataURL(file);
}



function listeGalerieactualite()
{
	$.ajax({
			url: 'upload.php',
			type: 'POST',
			data: { 
				id_actualite: "<?php echo crypt_decrypt_chaine($_SESSION['actualite']['id'], 'C');?>",
				listeGalerieactualite: ''
		    },
		success: function(response) {

			$('#tbodyGalerieactualite').html(response); 
		}
	});
}



function uploadGalerieactualite(element)
{ 
  var files = $(element)[0].files;

  Array.from(files).forEach(file => {
    var filename = file.name;
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

        $.ajax({
          url: 'upload.php',
          type: 'POST',
          data: { 
            imageBase64: compressedDataUrl, 
            filename: filename,
            fileExtension: fileExtension,
            id_actualite: "<?php echo crypt_decrypt_chaine($_SESSION['actualite']['id'], 'C');?>",
            uploadImageGalerieactualite: ''
          },
          success: function(response) {

          	listeGalerieactualite();
          }
        });

      };
      img.src = e.target.result;
    };
    reader.readAsDataURL(file);
  });

}



function supprGalerieactualite(element)
{
	var id_galerie = $(element).attr('id_galerie');

	if(confirm('Voulez-vous supprimer ?')) 
	{
		$.ajax({
				url: 'upload.php',
				type: 'POST',
				data: { 
					id_galerie: id_galerie,
					supprGalerieactualite: ''
			    },
			success: function(response) {

				listeGalerieactualite(); 
			}
		});
	}
}

</script>



<script>
    $('#btnSubmit').click(function(){
        $('#formulaireactualite').submit(); 
    });
</script>