<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 
?>

<?php
if(isset($_GET["id_event"]))
{
    $_SESSION["id_event"] = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_GET["id_event"], 'D') )));
    reload_current_page();
}


if (isset($_GET["ajouterevenement"])) {
    // Génère la référence de l'événement
    $evenement      = referenceEvent($bdd, $_SESSION["configuration"]["id"]); 
    $ev_annee       = $evenement[0]; 
    $ev_ordre       = $evenement[1]; 
    $n_evenement    = $evenement[2];

    // Prépare la requête d'insertion
    $query = "INSERT INTO evenements (
                    id_configuration,
                    ev_annee,
                    ev_ordre,
                    n_event,  
                    id_utilisateur, 
                    created_at
              ) VALUES (
                    ".$_SESSION["configuration"]["id"].",
                    \"$ev_annee\",
                    \"$ev_ordre\",
                    \"$n_evenement\", 
                    ".$_SESSION["utilisateur"]["id"].", 
                    '".date('Y-m-d H:i')."')";

    // Exécute l'insertion
    if (@mysqli_query($bdd, $query)) {
        $_SESSION["id_event"] = mysqli_insert_id($bdd); // récupère l'ID auto-incrémenté
        reload_current_page();
    } 
}


if (isset($_POST["actualiserInfosevent"])) {
    $id_event = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST["id_event"], 'D'))));
    $titre = strip_tags(htmlspecialchars(trim($_POST["titre"])));
    $lieu = strip_tags(htmlspecialchars(trim($_POST["lieu"])));
    $date_debut = strip_tags(htmlspecialchars(trim($_POST["date_debut"])));
    $date_fin = strip_tags(htmlspecialchars(trim($_POST["date_fin"])));
    $places_max = strip_tags(htmlspecialchars(trim($_POST["places_max"])));
    $description = strip_tags(htmlspecialchars(trim($_POST["description"])));

        $query = "UPDATE evenements
                  SET titre = \"$titre\",
                      lieu = \"$lieu\",
                      date_debut = \"$date_debut\",
                      date_fin = \"$date_fin\",
                      places_max = \"$places_max\",
                      description = ".empty_to_NULL($description).",
                      updated_at = '".date('Y-m-d H:i')."'
                  WHERE id_configuration = " . $_SESSION['configuration']['id'] . "
                  AND id = $id_event";
        mysqli_query($bdd, $query) or die("Requête non conforme0101");
    }
    

    if(isset($_POST['ids_categorie']))
    {
        foreach($_POST['ids_categorie'] as $id_categorie) 
        {
            $id_categorie = strip_tags(htmlspecialchars(trim($id_categorie)));

            $query = "UPDATE evenements 
                            SET id_cat_event = ".empty_to_NULL($id_categorie)." 
                            WHERE id = ".$id_event." AND id_configuration = '".$_SESSION["configuration"]["id"]."'";
            mysqli_query($bdd, $query) or die("Requête non conforme"); 
        }

    reload_current_page();
}


$query = "SELECT *
           FROM evenements WHERE id_configuration = ".$_SESSION['configuration']['id']." AND id =".$_SESSION['id_event'];
$resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
$_SESSION['event'] = mysqli_fetch_array($resultat);

?>

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Détails event</title>

    <?php include('includes/php/includes-css.php');?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet"> 

    <style type="text/css">  
        .note-editor .note-editable{min-height: 300px;}
        #divPrixevent table tr:first-child .btnSuppr {visibility: hidden;}
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
                        <a href="evenements.php" class='text-secondary'>Retour</a>
                    </li>
                  </ol>
                </nav>
                <h4 class="mb-2">Référence ( <span style='color: #2c2664;'> <?php echo safe_safe_ucfirst($_SESSION['event']['n_event']);?> </span> )</h4>
              </div>

              <div class="page-section">

                  <form id='formulaireevent' method='post' action='evenements_detail.php' onsubmit="prepareSubmit()" enctype="multipart/form-data">
                      <div class='row'>
                            <div class='col-md-8'>
                                <div class="card card-fluid mb-4">
                                    <div class="card-header p-3 text-uppercase">  
                                        Informations sur l'événement
                                    </div>
                                    <div class="card-body p-2"> 
                                        <div class='row'>
                                            <div class='col-12 mb-1'>
                                                <div class="form-floating">
                                                    <input type="text" name="titre" class="form-control mb-1" value="<?php echo $_SESSION['event']['titre'];?>" required>
                                                    <label class="form-label">Titre</label>
                                                </div>                                       
                                            </div>
                                            <div class='col-12 mb-1'>
                                                <div class="form-floating">
                                                    <input type="text" name="lieu" class="form-control mb-1" value="<?php echo $_SESSION['event']['lieu'];?>" required>
                                                    <label class="form-label">Lieu</label>
                                                </div>                                       
                                            </div>
                                            <div class='col-md-6 mb-1'>
                                                <div class="form-floating">
                                                    <input type="datetime-local" name="date_debut" class="form-control" 
                                                        value="<?php echo !empty($_SESSION['event']['date_debut']) ? date('Y-m-d\TH:i', strtotime($_SESSION['event']['date_debut'])) : ''; ?>">
                                                    <label class="form-label">Date de début</label>                      
                                                </div>                                         
                                            </div>

                                            <div class='col-md-6 mb-1'>
                                                <div class="form-floating">
                                                    <input type="datetime-local" name="date_fin" class="form-control" 
                                                        value="<?php echo !empty($_SESSION['event']['date_fin']) ? date('Y-m-d\TH:i', strtotime($_SESSION['event']['date_fin'])) : ''; ?>">
                                                    <label class="form-label">Date de fin</label>                      
                                                </div>                                         
                                            </div>
                                            <!-- <div class='col-md-6 mb-1'>
                                                <div class="form-floating">
                                                    <input type="time" name="heure" class="form-control" value="<?php echo $_SESSION['event']['heure'];?>">
                                                    <label class="form-label">Heure</label>                      
                                                </div>                                         
                                            </div> -->
                                            <div class='col-md-6 mb-1'>
                                                <div class="form-floating">
                                                    <input type="number" name="places_max" class="form-control" value="<?php echo $_SESSION['event']['places_max'];?>">
                                                    <label class="form-label">Places maximum</label>                      
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
                                    <div class="card-header p-3">
                                        Catégorie 
                                    </div>
                                    <div class="card-body">
                                        <div>
                                            <?php
                                            $query = "SELECT id FROM evenements WHERE id = ".$_SESSION['event']['id']." LIMIT 1";
                                            $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
                                            $select_cats_event = mysqli_fetch_array($resultat); 

                                            if(!empty($select_cats_event)){$checked = 'checked';}else{$checked = '';}
                                            ?>

                                            <div class="form-check">
                                                <input id="CatCheck0" type="radio" name='ids_categorie[]' class="form-check-input" value='0' <?php echo $checked;?> />
                                                <label for="CatCheck0" class="form-check-label">Aucun</label>
                                            </div> 
                                            <?php
                                            // Étape 1 : récupérer l'id de la catégorie actuelle de l'event en cours
                                            $id_categorie_actuelle = 0;
                                            $query_cat = "SELECT id_cat_event FROM evenements WHERE id = ".$_SESSION['event']['id']." LIMIT 1";
                                            $result_cat = mysqli_query($bdd, $query_cat);
                                            if ($row = mysqli_fetch_assoc($result_cat)) {
                                                $id_categorie_actuelle = $row['id_cat_event'];
                                            }

                                            // Étape 2 : lister toutes les catégories disponibles
                                            $query = "SELECT id, nom FROM event_cat  
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
                            </div>
                        </div>

                      <div>

                        <input type='hidden' name='id_event' value="<?php echo crypt_decrypt_chaine($_SESSION['event']['id'], 'C');?>"> 

                        <input type='hidden' name='actualiserInfosevent'>
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
                    let aproposContent = `<?php echo !empty($_SESSION['event']['description']) ? htmlspecialchars_decode($_SESSION['event']['description']) : ''; ?>`;
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


  </body>

</html>

<script>

function uploadImgevent(element)
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
                  id_event: "<?php echo crypt_decrypt_chaine($_SESSION['event']['id'], 'C');?>",    
                  uploadImagePrincipaleevent: ''
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


</script>


<script>
    $('#btnSubmit').click(function(){
        $('#formulaireevent').submit(); 
    });
</script>