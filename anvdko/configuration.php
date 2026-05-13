<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 
?>


<?php
if(isset($_POST['modifierConfiguration']))  
{
    $nom          = strip_tags(htmlspecialchars(trim($_POST["nom"])));
    $contact1     = strip_tags(htmlspecialchars(trim($_POST["contact1"])));
    $contact2     = strip_tags(htmlspecialchars(trim($_POST["contact2"])));
    $email        = strip_tags(htmlspecialchars(trim($_POST["email"])));
    $localisation = strip_tags(htmlspecialchars(trim($_POST["localisation"])));

    if(isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) 
    {
        $destination = createPathFile('../fichiers/uploads/').uniqid().'.'.pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);  

        if(move_uploaded_file($_FILES['logo']['tmp_name'], $destination)) 
        {
            //Suppression d'abord
            $supprFichier = '../fichiers/uploads/'.$_SESSION["configuration"]["logo"]; 
            @unlink($supprFichier); 

            //Update 
            $nomFichier = str_replace('../fichiers/uploads/', '', $destination);

            $query ="UPDATE configurations SET logo = '".$nomFichier."'  WHERE id = ".$_SESSION["configuration"]["id"];
            mysqli_query($bdd, $query) or die("Requête non conforme");
        } 
    }


    $query = "UPDATE configurations   
             SET nom = \"$nom\", 
                 contact1 = \"$contact1\", 
                 contact2 = \"$contact2\",
                 email = \"$email\", 
                 localisation = \"$localisation\"
             WHERE id = ".$_SESSION['configuration']['id'];
    mysqli_query($bdd, $query) or die("Requête non conforme"); 

    reload_current_page();
}


$query ="SELECT * FROM configurations WHERE id = ".$_SESSION['configuration']['id'];
$resultat = mysqli_query($bdd, $query) or die("Requête non conforme");
$_SESSION["configuration"] = mysqli_fetch_array($resultat);
?>

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Configuration </title>

    <?php include('includes/php/includes-css.php');?>

  </head>


  <body>

    <main class="main" id="top">

      <?php include('includes/php/menu.php');?>

      <?php include('includes/php/header.php');?>

      <div class="content">

        <div class="pb-5">
          <div class="row g-4">
            <div class="col-12 col-xxl-6">

              <div class="mb-8">
                <h2 class="mb-2">Configuration </h2>
                <h5 class="text-body-tertiary fw-semibold">Gérer la configuration générale</h5>
              </div>

              <div class="page-section">
                  <div class="card card-fluid">
                    <div class="card-body">
                        <form action="configuration.php" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Sigle</label>
                                        <input type="text" name="nom" class="form-control" value="<?php echo $_SESSION["configuration"]["nom"];?>" required/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Contact 1</label>
                                        <input type="tel" name="contact1" class="form-control" value="<?php echo $_SESSION["configuration"]["contact1"];?>" required/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Contact 2</label>
                                        <input type="tel" name="contact2" class="form-control" value="<?php echo $_SESSION["configuration"]["contact2"];?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" value="<?php echo $_SESSION["configuration"]["email"];?>" required/>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">localisation</label>
                                        <input type="text" name="localisation" class="form-control" value="<?php echo $_SESSION["configuration"]["localisation"];?>" required/>
                                    </div>
                                </div>
                            </div>

                            <div class='text-center mt-2 mb-3'>
                                <img src="<?php echo getUrlFichier($_SESSION["configuration"]["logo"]);?>" height='100px' class='border rounded'>
                                <input type='file' name='logo' class='form-control mx-auto mt-1' accept='image/*' style='width: 250px;' />
                            </div>

                            <div class='text-end'>
                              <button class="btn btn-primary btn-sm" type="submit" name="modifierConfiguration">Valider</button>
                            </div>
                        </form>
                    </div>
                  </div>
              </div>

            </div>
          </div>
        </div>

        <?php include('includes/php/footer.php');?>

      </div>

    </main>

    <?php include('includes/php/includes-js.php');?>

  </body>

</html>