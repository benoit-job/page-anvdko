<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 
?>

<?php
if(isset($_GET["id_tv"]))
{
    $_SESSION["id_tv"] = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_GET["id_tv"], 'D') )));
    reload_current_page();
}

if (isset($_POST["actualiserInfostv"])) {
    $id_tv = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST["id_tv"], 'D'))));
    $titre = strip_tags(htmlspecialchars(trim($_POST["titre"])));
    $lien_youtube = strip_tags(htmlspecialchars(trim($_POST["lien_youtube"])));
    $date_tv = strip_tags(htmlspecialchars(trim($_POST["date_tv"])));


    $description = $_POST["description"];

        $query = "UPDATE adci_tv
                  SET titre = \"$titre\",
                      date_tv = \"$date_tv\",
                      lien_youtube = \"$lien_youtube\",
                      description = ".empty_to_NULL($description)."
                  WHERE id_configuration = " . $_SESSION['configuration']['id'] . "
                  AND id = $id_tv";
        mysqli_query($bdd, $query) or die("Requête non conforme0101");
    

    if(isset($_POST['ids_categorie']))
    {
        foreach($_POST['ids_categorie'] as $id_categorie) 
        {
            $id_categorie = strip_tags(htmlspecialchars(trim($id_categorie)));

            $query = "UPDATE adci_tv 
                            SET id_cat_tv = ".empty_to_NULL($id_categorie)." 
                            WHERE id = ".$id_tv." AND id_configuration = '".$_SESSION["configuration"]["id"]."'";
            mysqli_query($bdd, $query) or die("Requête non conforme"); 
        }
    }

    reload_current_page();
}


$query = "SELECT *
           FROM adci_tv WHERE id_configuration = ".$_SESSION['configuration']['id']." AND id =".$_SESSION['id_tv'];
$resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
$_SESSION['tv'] = mysqli_fetch_array($resultat);

?>

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Détails tv</title>

    <?php include('includes/php/includes-css.php');?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet"> 

    <style type="text/css">  
        .note-editor .note-editable{min-height: 300px;}
        #divPrixtv table tr:first-child .btnSuppr {visibility: hidden;}
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
                        <a href="adci_tv.php" class='text-secondary'>Retour</a>
                    </li>
                  </ol>
                </nav>
                <h4 class="mb-2">Détail TV </h4>
              </div>

              <div class="page-section">

                  <form id='formulairetv' method='post' action='adci_tv_details.php' onsubmit="prepareSubmit()" enctype="multipart/form-data">
                      <div class='row'>
                          <div class='col-md-8'>

                              <div class="card card-fluid mb-4">
                                <div class="card-header p-3 text-uppercase">  
                                Informations sur l'tv
                                </div>
                                <div class="card-body p-2"> 
                                    <div class='row'>
                                        <div class='col-12 mb-3'>
                                            <div class="form-floating">
                            <input type="text" name="titre" class="form-control mb-1" value="<?php echo $_SESSION['tv']['titre'];?>" required>
                            <label class="form-label">Titre</label>
                                            </div>                                       
                                        </div>
                                        <div class='col-12 mb-3'>
                                            <div class="form-floating">
                            <input type="url" name="lien_youtube" class="form-control mb-1" value="<?php echo $_SESSION['tv']['lien_youtube'];?>" required>
                            <label class="form-label">Lien youtube</label>
                                            </div>                                       
                                        </div>
                                        <div class='col-12 mb-3'>
                                            <div class="form-floating">
                                                <input type="date" name="date_tv" class="form-control" id="" value="<?php echo $_SESSION['tv']['date_tv'];?>">
                                                 <label class="form-label">Date de la rencontre</label>                      
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
        <span>Prévisualisation de la vidéo</span>
    </div>
    <div class="card-body px-4 py-3">
        <div id="divImgPrincipale" class="ratio ratio-16x9 rounded overflow-hidden"  style="min-height: 200px;">
            <?php echo affIlectureVideo('100%', '100%', $_SESSION['tv']['lien_youtube'], 'w-100 h-100 border-0'); ?>
        </div>
    </div>
</div>
   

                              <div class="card card-fluid mb-4">
                                <div class="card-header p-3">
                                    Catégorie 
                                </div>
                                <div class="card-body">
                                    <div>
                                        <?php
                                        $query = "SELECT id FROM adci_tv WHERE id = ".$_SESSION['tv']['id']." LIMIT 1";
                                        $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
                                        $select_cats_tv = mysqli_fetch_array($resultat); 

                                        if(!empty($select_cats_tv)){$checked = 'checked';}else{$checked = '';}
                                        ?>

                                        <div class="form-check">
                                        <input id="CatCheck0" type="radio" name='ids_categorie[]' class="form-check-input" value='0' <?php echo $checked;?> />
                                        <label for="CatCheck0" class="form-check-label">Aucun</label>
                                        </div> 
                                        <?php
                                    // Étape 1 : récupérer l'id de la catégorie actuelle de l'tv en cours
                                    $id_categorie_actuelle = 0;
                                    $query_cat = "SELECT id_cat_tv FROM adci_tv WHERE id = ".$_SESSION['tv']['id']." LIMIT 1";
                                    $result_cat = mysqli_query($bdd, $query_cat);
                                    if ($row = mysqli_fetch_assoc($result_cat)) {
                                        $id_categorie_actuelle = $row['id_cat_tv'];
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
                              
                          </div>
                      </div>

                      <div>

                        <input type='hidden' name='id_tv' value="<?php echo crypt_decrypt_chaine($_SESSION['tv']['id'], 'C');?>"> 

                        <input type='hidden' name='actualiserInfostv'>
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
                        let aproposContent = `<?php echo !empty($_SESSION['tv']['description']) ? htmlspecialchars_decode($_SESSION['tv']['description']) : ''; ?>`;
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
    $('#btnSubmit').click(function(){
        $('#formulairetv').submit(); 
    });
</script>