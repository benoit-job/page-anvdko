<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 
?>
<?php
  
  if(isset($_POST['modifierutilisateur']))
  {
    
    if(isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) 
    {
        $destination = createPathFile('../fichiers/uploads/').uniqid().'.'.pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);  

        if(move_uploaded_file($_FILES['logo']['tmp_name'], $destination)) 
        {
            //Suppression d'abord
            $supprFichier = '../fichiers/uploads/'.$_SESSION["utilisateur"]["logo"]; 
            @unlink($supprFichier); 

            //Update 
            $nomFichier = str_replace('../fichiers/uploads/', '', $destination);

            $query ="UPDATE utilisateurs SET logo = '".$nomFichier."'  WHERE id =".$_SESSION['utilisateur']['id']." AND id_configuration  = ".$_SESSION["configuration"]["id"];
            if(@mysqli_query($bdd, $query)){reload_current_page();}else{die("requête non conforme2");}

            
        $query = "SELECT * FROM utilisateurs 
                WHERE id_configuration = ".$_SESSION['configuration']['id']." AND id =".$_SESSION['utilisateur']['id'];
        $resultat = mysqli_query($bdd, $query) or die("Requête non conforme784521");
        $_SESSION['utilisateur'] = mysqli_fetch_array($resultat);
        } 
    }
  }

  if(isset($_POST['modifierInfo']))
  {
    
    $id_utilisateur = strip_tags(htmlspecialchars(trim($_POST['id_utilisateur'])));
    $pseudo                      = strip_tags(htmlspecialchars(trim($_POST['pseudo'])));
    $telephone                   = strip_tags(htmlspecialchars(trim($_POST['telephone'])));
    $email                   = strip_tags(htmlspecialchars(trim($_POST['email'])));
      $query  =  "UPDATE  utilisateurs 
                  SET pseudo = \"$pseudo\",
                      telephone = \"$telephone\",
                      email = \"$email\"
                  WHERE id_configuration = ".$_SESSION['configuration']['id']." AND id =".$_SESSION['utilisateur']['id'];
      if(@mysqli_query($bdd, $query)){reload_current_page();}else{die("requête non conforme2");}
      
        //Actualisation la session de l'utilisateur
        $query = "SELECT * FROM utilisateurs 
                  WHERE id_configuration = ".$_SESSION['configuration']['id']." AND id =".$_SESSION['utilisateur']['id'];
        $resultat = mysqli_query($bdd, $query) or die("Requête non conforme784521");
        $_SESSION['utilisateur'] = mysqli_fetch_array($resultat);
  }

  if( isset($_POST['modifierpassword']) )
{
    if(isset($_POST['ancien_mdp']) AND isset($_POST['nouveau_mdp']) AND isset($_POST['confirmer_mdp']))
    {
        $ancien_mdp    = strip_tags(htmlspecialchars(trim($_POST['ancien_mdp'])));
        $nouveau_mdp   = strip_tags(htmlspecialchars(trim($_POST['nouveau_mdp'])));
        $confirmer_mdp = strip_tags(htmlspecialchars(trim($_POST['confirmer_mdp'])));
        if( anvdko_password_verify($ancien_mdp, $_SESSION['utilisateur']['password'] ?? '') AND $nouveau_mdp == $confirmer_mdp AND strlen($nouveau_mdp) >= 6 )
        {
          $nh = mysqli_real_escape_string($bdd, anvdko_password_hash($confirmer_mdp));
          $query = "UPDATE utilisateurs 
                    SET password = \"$nh\"
                    WHERE id_configuration = ".$_SESSION['configuration']['id']." AND id =".$_SESSION['utilisateur']['id'];
          if(@mysqli_query($bdd, $query))
          {
              alertJS("Mot de passe utilisateur actualisé !", 'mon_compte.php');
          }
        }
        else
        {
            alertJS("Impossible de changer le mot de passe, Veuillez vérifiez vos données d'entrées !", 'mon_compte.php');
        }

        //Actualisation la session de l'utilisateur
        $query = "SELECT * FROM utilisateurs 
                  WHERE id =".$_SESSION['utilisateur']['id'];
        $resultat = mysqli_query($bdd, $query) or die("Requête non conforme784521");
        $_SESSION['utilisateur'] = mysqli_fetch_array($resultat);
    }
}
?>

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="en-US" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Mon compte</title>

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

              <div class="mb-5">
                <h2 class="mb-2">Mon compte</h2>
                <h5 class="text-body-tertiary fw-semibold">Visualiser le récapitulatif des informations</h5>
              </div>

              <div class="page-section">
                
              <?php
                          $query = "SELECT * 
                                    FROM utilisateurs 
                                    WHERE id_configuration = ".$_SESSION['configuration']['id']." AND id =".$_SESSION['utilisateur']['id'];
                          $resultat = mysqli_query($bdd, $query) or die("Requête non conforme753210");  
                          $utilisateurs = mysqli_fetch_array($resultat);
                      ?>
                    
                  <div class="page-section mt-4">
                      <div class="card card-fluid">
                        <div class="card-body">
                            <form action="mon_compte.php" method="post" enctype="multipart/form-data">
                                <div class='text-center mt-2 mb-3'>
                                    <img src="<?php echo getUrlUtilisateur($utilisateurs["logo"]);?>" height='100px' class='border rounded'>
                                    <input type='file' name='logo' class='form-control mx-auto mt-1' accept='image/*' style='width: 250px;' />
                                </div>

                                <div class='text-end'>
                                  <button class="btn btn-primary btn-sm" type="submit" name="modifierutilisateur">Enregistrer image</button>
                                </div>
                            </form>
                        </div>
                      </div>
                  </div>

                  <div class="page-section mt-4">
                
                   <div class="card card-fluid">
                    <div class="row">
                        <div class="col-md-7 bg-white p-3 rounded-3">
                            <div class="row mx-auto mt-3">
                                <form action="mon_compte.php" method="post">
                                    <div class="mb-3">
                                        <label class="mb-1">Nom</label>
                                        <input type="text" name="pseudo" class="form-control" value="<?php echo $utilisateurs['pseudo'];?>">
                                    </div>

                                    <div class="mt-3">
                                        <label class="mb-1">Telephone</label>
                                        <input type="tel" name="telephone" class="form-control" value="<?php echo $utilisateurs['telephone'];?>">
                                    </div>

                                    <div class="mt-3">
                                        <label class="mb-1">E-mail</label>
                                        <input type="email" name="email" class="form-control" value="<?php echo $utilisateurs['email'];?>">
                                    </div>

                                    <div class="mt-3">    
                                    <input type="hidden" name="id_utilisateur" value="<?php echo $utilisateurs['id'];?>">                        
                                        <button type="submit" class="btn btn-primary" name="modifierInfo">Actualiser</button>
                                    </div>
                                </form>
                            </div>
                        </div> 

                        <div class="col-md-4 bg-white ms-3 rounded-3 p-2">  
                            <h5 class="mb-2">Réinitialiser le Mot de Passe</h5>
                            <div class="row">
                                <form action="mon_compte.php" method="post">
                                    <div class="mb-2">
                                        <label>Ancien mot de passe</label>
                                        <input type="password" name="ancien_mdp" class="form-control" required>  
                                    </div>

                                    <div class="mb-2">  
                                        <label>Nouveau mot de passe</label>
                                        <input type="password" name="nouveau_mdp" class="form-control" required>
                                    </div>

                                    <div class="mb-2">
                                        <label>Confirmer mot de passe</label>
                                        <input type="password" name="confirmer_mdp" class="form-control" required>
                                    </div>

                                    <div class="mb-2">                            
                                        <button type="submit" class="btn btn-primary" name="modifierpassword">Changer mot de passe</button>
                                    </div>
                                </form>
                            </div>
                        </div>

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

