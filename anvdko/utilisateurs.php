<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 
?>
<?php
   if(isset($_POST["ajouterUtilisateur"])) 
   {
       $pseudo = strip_tags(htmlspecialchars(trim($_POST["pseudo"])));
       $telephone = strip_tags(htmlspecialchars(trim($_POST["telephone"])));
       $email = strip_tags(htmlspecialchars(trim($_POST["email"])));
       $secretaire = strip_tags(htmlspecialchars(trim($_POST["secretaire"])));
       $password = (string) ($_POST['password'] ?? '');
       $password2 = (string) ($_POST['password_confirm'] ?? '');
       if (strlen($password) < 6 || $password !== $password2) {
           die("Mot de passe invalide : minimum 6 caractères et confirmation identique.");
       }
       $hash = mysqli_real_escape_string($bdd, anvdko_password_hash($password));

       $query = "INSERT INTO utilisateurs(pseudo , telephone, email, password, secretaire, id_configuration, date_heure) 
                 VALUES (\"$pseudo\", \"$telephone\", \"$email\", \"$hash\",  \"$secretaire\", ".$_SESSION['configuration']['id'].", '".date('Y-m-d H:i:s')."')";
       mysqli_query($bdd, $query) or die("Requête non conforme0101"); 
       reload_current_page();
   }

   if(isset($_POST['modifierUtilisateur']))  
    {
        $id_utilisateur = strip_tags(htmlspecialchars(trim( crypt_decrypt_chaine($_POST['id_utilisateur'], 'D') )));
        $pseudo = strip_tags(htmlspecialchars(trim($_POST["pseudo"])));
        $telephone = strip_tags(htmlspecialchars(trim($_POST["telephone"])));
        $email = strip_tags(htmlspecialchars(trim($_POST["email"])));
        $secretaire = strip_tags(htmlspecialchars(trim($_POST["secretaire"])));
        $password = (string) ($_POST['password'] ?? '');
        $password2 = (string) ($_POST['password_confirm'] ?? '');

        $sqlPwd = '';
        if ($password !== '' && $password === $password2 && strlen($password) >= 6) {
            $hash = mysqli_real_escape_string($bdd, anvdko_password_hash($password));
            $sqlPwd = ", password = \"$hash\" ";
        }

        $query = "UPDATE utilisateurs  
                  SET pseudo = \"$pseudo\", 
                      telephone = \"$telephone\",
                      email = \"$email\",
                      secretaire = \"$secretaire\"
                      $sqlPwd
                      , date_heure  = '".date('Y-m-d H:i:s')."'
                  WHERE id =".$id_utilisateur;
        if(@mysqli_query($bdd, $query)){reload_current_page();}else{die("Requête non conforme");}
        header('location: utilisateurs.php');
    }

   //DELETE FROM
  if(isset($_POST["supprimerUtilisateur"]))
  {
    $id_utilisateur = crypt_decrypt_chaine( $_POST["id_utilisateur"], 'D');

    $query = "DELETE FROM utilisateurs WHERE id =".$id_utilisateur;
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

    <title>Utilisateurs</title>

    <?php include('includes/php/includes-css.php');?>

  </head>


  <body>

    <main class="main" id="top">
    	
      <?php include('includes/php/menu.php');?>

      <?php include('includes/php/header.php');?>

      <div class="content">

        <div class="pb-5">
            <div class="mb-8">
              <h2 class="mb-2">Utilisateur</h2>
              <h5 class="text-body-tertiary fw-semibold">Gestion des utilisateurs</h5>
            </div>

            <div class="page-section">
                <div class="card card-fluid">
                  <div class="card-header border-0 p-1">
                    <div class="d-flex align-items-center">
                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class='fas fa-plus'></i> Ajouter</button>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-hover m-0">
                      <thead class="thead-">
                        <tr style='font-size: 0.8rem;'>
                          <th>N°</th>
                          <th>Pseudo</th>
                          <th>Contact</th>
                          <th>E-mail</th>
                          <th>Date enreg</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                            
               <?php 
                $query = "SELECT*, date_format(date_heure, '%d/%m/%Y %Hh%i') AS date_heure
                          FROM
                          utilisateurs 
                          ORDER BY pseudo";
              $resultat = mysqli_query($bdd, $query) or die("Requête non conforme00000");
              $ligne = 0;
              while($utilisateur = mysqli_fetch_array($resultat))
              {                           
                  echo  "<tr>
                          <form method='post' action='utilisateurs.php'>
                          <th>".++$ligne."</th>
                          <td>".$utilisateur["pseudo"]."</td>
                          <td>".$utilisateur["telephone"]."</td>
                          <td>".$utilisateur["email"]."</td>
                          <td>".$utilisateur["date_heure"]."</td>
                          <td class='text-end'>
                          
                              <button type='button' class='btn btn-light btn-sm modififierInfos' 
                              id_utilisateur='".crypt_decrypt_chaine($utilisateur['id'], 'C')."'
                              pseudo=\"".$utilisateur['pseudo']."\"
                              telephone=\"".$utilisateur['telephone']."\"
                              email=\"".$utilisateur['email']."\"
                              secretaire=\"".$utilisateur['secretaire']."\">
                              <i class='fas fa-pencil-alt'></i>
                          </button>
                          <button type='submit' name='supprimerUtilisateur' onclick=\"return confirm('Voulez-vous supprimer ?')\"  class='btn btn-light btn-sm'><i class='fas fa-trash-alt'></i></button>
                          </td>

                          <input type='hidden' name='id_utilisateur' value='".crypt_decrypt_chaine($utilisateur['id'], 'C')."'>
                          </form>
                      </tr>";
}
              ?>                            
                        </tbody>
                    </table>
                  </div>
                </div>
            </div>
        </div>

         <!-- Ajouter utilisateur -->
    <!-- ===============================================-->
        <form method="post" action="utilisateurs.php">
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ajouter utilisateur</h5>
                        <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs-9"></span></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pseudo </label>
                            <input type="text" name="pseudo" class="form-control" required/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact</label>
                            <input type="tel" name="telephone" class="form-control" required/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" required/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe (min. 6 caractères)</label>
                            <input type="password" name="password" class="form-control" minlength="6" autocomplete="new-password" required/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmer le mot de passe</label>
                            <input type="password" name="password_confirm" class="form-control" minlength="6" autocomplete="new-password" required/>
                        </div>
                        
                        <div class="col-12 mb-3 text-center">
                            <label class="form-label fw-bold mb-2 d-block"><i class="fa fa-briefcase"></i> Sécrétaire</label>
                            
                            <div class="d-flex justify-content-center gap-4">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="secretaire" id="secretaireOui" value="Oui">
                                    <label class="form-check-label" for="secretaireOui">Oui</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="secretaire" id="secretaireNon" value="Non">
                                    <label class="form-check-label" for="secretaireNon">Non</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="ajouterUtilisateur" class="btn btn-primary">Valider</button>
                        <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Fermer</button>
                    </div>
                    </div>
                </div>
            </div>
        </form>

         <!-- Modifier utilisateur -->
    <!-- ===============================================-->
        <form method="post" action="utilisateurs.php">
            <div class="modal fade" id="exampleModal2" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Modifier utilisateur</h5>
                        <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs-9"></span></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pseudo </label>
                            <input type="text" name="pseudo" class="form-control" required/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact</label>
                            <input type="tel" name="telephone" class="form-control" required/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" required/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nouveau mot de passe (optionnel)</label>
                            <input type="password" name="password" class="form-control" minlength="6" autocomplete="new-password"/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmer le mot de passe</label>
                            <input type="password" name="password_confirm" class="form-control" minlength="6" autocomplete="new-password"/>
                        </div>
                        
                        <div class="col-12 mb-3 text-center">
                            <label class="form-label fw-bold mb-2 d-block"><i class="fa fa-briefcase"></i> Sécrétaire</label>
                            
                            <div class="d-flex justify-content-center gap-4">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="secretaire" id="secretaireOui" value="Oui">
                                    <label class="form-check-label" for="secretaireOui">Oui</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="secretaire" id="secretaireNon" value="Non">
                                    <label class="form-check-label" for="secretaireNon">Non</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id_utilisateur">
                        <button type="submit" name="modifierUtilisateur" class="btn btn-primary">Valider</button>
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

  </body>

</html>

<script>
$('.modififierInfos').click(function(){     

    let secretaireValue = $(this).attr('secretaire');

    $("#exampleModal2 input[name='pseudo']").val($(this).attr('pseudo'));
    $("#exampleModal2 input[name='email']").val($(this).attr('email'));
    $("#exampleModal2 input[name='telephone']").val($(this).attr('telephone')); 
    $("#exampleModal2 input[name='secretaire'][value='" + secretaireValue + "']").prop("checked", true);
    $("#exampleModal2 input[name='id_utilisateur']").val($(this).attr('id_utilisateur')); 


    $('#exampleModal2').modal('show');
});
</script> 