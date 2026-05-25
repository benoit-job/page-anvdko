<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 
?>

<?php
if(isset($_GET["id_membre"]))
{
    $_SESSION["id_membre"] = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_GET["id_membre"], 'D') )));
    reload_current_page();
}


if (isset($_GET["ajouterAdherent"])) {
    // Génère la référence de l'événement
    $adhesion      = generer_numero_adhesion($bdd); 
    $ev_annee       = $adhesion[0]; 
    $ev_ordre       = $adhesion[1]; 
    $n_adhesion    = $adhesion[2];

    // Prépare la requête d'insertion
    $query = "INSERT INTO membres (
                    ev_annee,
                    ev_ordre,
                    num_adhesion,  
                    id_utilisateur, 
                    date_heure
              ) VALUES (\"$ev_annee\",
                    \"$ev_ordre\",
                    \"$n_adhesion\", 
                    ".$_SESSION["utilisateur"]["id"].", 
                    '".date('Y-m-d H:i')."')";

    // Exécute l'insertion
    if (@mysqli_query($bdd, $query)) {
        $_SESSION["id_membre"] = mysqli_insert_id($bdd); // récupère l'ID auto-incrémenté
        reload_current_page(); // recharge la page actuelle
    } 
}


if (isset($_POST["btnSubmit"])) {
    $id_membre = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST["id_membre"], 'D'))));
    $nom          = strip_tags(htmlspecialchars(trim($_POST["nom"])));
    $prenom     = strip_tags(htmlspecialchars(trim($_POST["prenom"])));
    $genre     = strip_tags(htmlspecialchars(trim($_POST["genre"])));
    $date_naissance     = strip_tags(htmlspecialchars(trim($_POST["date_naissance"])));
    $lieu_naissance     = strip_tags(htmlspecialchars(trim($_POST["lieu_naissance"])));
    $profession     = strip_tags(htmlspecialchars(trim($_POST["profession"])));
    $nationnalite     = strip_tags(htmlspecialchars(trim($_POST["nationnalite"])));
    $num_telephone     = strip_tags(htmlspecialchars(trim($_POST["num_telephone"])));
    $lieu_residence     = strip_tags(htmlspecialchars(trim($_POST["lieu_residence"])));
    $ville_commune     = strip_tags(htmlspecialchars(trim($_POST["ville_commune"])));
    $email        = strip_tags(htmlspecialchars(trim($_POST["email"])));
    $poste_occupe = strip_tags(htmlspecialchars(trim($_POST["poste_occupe"])));
    $date_adhesion = strip_tags(htmlspecialchars(trim($_POST["date_adhesion"])));
    $membre_bureau = isset($_POST["membre_bureau"]) ? 1 : 0;

        $query = "UPDATE membres 
             SET nom = \"$nom\", 
                 prenom = \"$prenom\", 
                 genre = \"$genre\", 
                 date_naissance = \"$date_naissance\",
                 lieu_naissance = \"$lieu_naissance\",
                 profession = \"$profession\",
                 nationnalite = \"$nationnalite\",
                 date_naissance = \"$date_naissance\",
                 num_telephone = \"$num_telephone\", 
                 lieu_residence = \"$lieu_residence\",
                 ville_commune = \"$ville_commune\",
                 email = \"$email\",
                 poste_occupe = \"$poste_occupe\",
                 date_adhesion = \"$date_adhesion\",
                 membre_bureau = $membre_bureau
             WHERE id = ".$id_membre;
        mysqli_query($bdd, $query) or die("Requête non conforme0101");
    reload_current_page();
}


$query = "SELECT *
           FROM membres WHERE id =".$_SESSION['id_membre'];
$resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
$_SESSION['membre'] = mysqli_fetch_array($resultat);

// Charger le statut d'adhésion depuis la table adhesion
$query_adhesion = "SELECT statut, montant, date_heure FROM adhesion WHERE id_membre = ".$_SESSION['id_membre']." ORDER BY date_heure DESC LIMIT 1";
$resultat_adhesion = mysqli_query($bdd, $query_adhesion);
$adhesion_data = mysqli_fetch_assoc($resultat_adhesion);
$_SESSION['membre']['statut_ad'] = $adhesion_data ? $adhesion_data['statut'] : 'Non payé';

$signature = isset($_SESSION["membre"]["signature"]) && !empty($_SESSION["membre"]["signature"])
    ? $_SESSION["membre"]["signature"]
    : '';

$SignaturePath = $signature;


?>

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Détails membre</title>

    <?php include('includes/php/includes-css.php');?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet"> 

    <style type="text/css">  
        .note-editor .note-editable{min-height: 300px;}
        #divPrixmembre table tr:first-child .btnSuppr {visibility: hidden;}
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
                        <a href="adherents.php" class='text-secondary'>Retour</a>
                    </li>
                  </ol>
                </nav>
                <h4 class="mb-2">N° Adhésion ( <span style='color: #2c2664;'> <?php echo safe_safe_ucfirst($_SESSION['membre']['num_adhesion']);?> </span> )</h4>
              </div>

              <div class="page-section">

                    <form method='post' action='adherent_details.php'>
                      <div class='row'>

                          <div class='col-md-8'>

                              <div class="card card-fluid mb-4">
                                <div class="card-header p-3 text-uppercase">  
                                Informations sur le membre
                                </div>
                                <div class="card-body p-2"> 
                                    <div class='row'>
                                        <div class='col-12 mb-2'>
                                            <div class="form-floating">
                            <input type="text" name="nom" class="form-control mb-1" value="<?php echo $_SESSION['membre']['nom'];?>" required>
                            <label class="form-label">nom</label>
                                            </div>                                       
                                        </div>
                                        <div class='col-12 mb-2'>
                                            <div class="form-floating">
                            <input type="text" name="prenom" class="form-control mb-1" value="<?php echo $_SESSION['membre']['prenom'];?>" required>
                            <label class="form-label">Prénoms</label>
                                            </div>                                       
                                        </div>

                                        <div class="col-12 mb-3 text-center">
                                            <label class="form-label fw-bold mb-2 d-block"><i class="fa fa-venus-mars"></i> Genre</label>
                                            
                                            <div class="d-flex justify-content-center gap-4">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="genre" id="genreHomme" value="HOMME" 
                                                        <?php if ($_SESSION["membre"]["genre"] == 'HOMME') echo 'checked'; ?> required>
                                                    <label class="form-check-label" for="genreHomme">Homme</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="genre" id="genreFemme" value="FEMME" 
                                                        <?php if ($_SESSION["membre"]["genre"] == 'FEMME') echo 'checked'; ?> required>
                                                    <label class="form-check-label" for="genreFemme">Femme</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="genre" id="genreMademoiselle" value="MADEMOISELLE" 
                                                        <?php if ($_SESSION["membre"]["genre"] == 'MADEMOISELLE') echo 'checked'; ?> required>
                                                    <label class="form-check-label" for="genreMademoiselle">Mademoiselle</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class='col-12 mb-2'>
                                            <div class="form-floating">
                                                <input type="date" name="date_naissance" class="form-control" id="" value="<?php echo $_SESSION['membre']['date_naissance'];?>">
                                                 <label class="form-label">Date de naissance</label>                      
                                            </div>                                         
                                        </div>
                                        <div class='col-12 mb-2'>
                                            <div class="form-floating">
                                                <input type="text" name="lieu_naissance" class="form-control" id="" value="<?php echo $_SESSION['membre']['lieu_naissance'];?>">
                                                 <label class="form-label">Lieu de naissance</label>                      
                                            </div>                                         
                                        </div>
                                        <div class='col-12 mb-2'>
                                            <div class="form-floating">
                                                <input type="text" name="profession" class="form-control" id="" value="<?php echo $_SESSION['membre']['profession'];?>">
                                                 <label class="form-label">Profession</label>                      
                                            </div>                                         
                                        </div>
                                        <div class='col-12 mb-2'>
                                            <div class="form-floating">
                                                <input type="text" name="nationnalite" class="form-control" id="" value="<?php echo $_SESSION['membre']['nationnalite'];?>">
                                                 <label class="form-label">Nationnalité</label>                      
                                            </div>                                         
                                        </div>
                                        <div class='col-12 mb-2'>
                                            <div class="form-floating">
                                                <input type="tel" name="num_telephone" class="form-control" id="" value="<?php echo $_SESSION['membre']['num_telephone'];?>">
                                                 <label class="form-label">Numéro de téléphone</label>                      
                                            </div>                                         
                                        </div>
                                        <!-- <div class='col-12 mb-2'>
                                            <div class="form-floating">
                                                <input type="text" name="lieu_residence" class="form-control" id="" value="<?php echo $_SESSION['membre']['lieu_residence'];?>">
                                                 <label class="form-label">Lieu de résidence</label>                      
                                            </div>                                         
                                        </div> -->
                                        <div class='col-12 mb-2'>
                                            <div class="form-floating">
                                                <input type="text" name="ville_commune" class="form-control" id="" value="<?php echo $_SESSION['membre']['ville_commune'];?>">
                                                 <label class="form-label">Lieu de résidence</label>                      
                                            </div>                                         
                                        </div>
                                        <div class='col-12 mb-2'>
                                            <div class="form-floating">
                                                <input type="email" name="email" class="form-control" id="" value="<?php echo $_SESSION['membre']['email'];?>">
                                                 <label class="form-label">E-mail</label>                      
                                            </div>                                         
                                        </div>
                                        <div class='col-12 mb-2'>
                                            <div class="form-floating">
                                                <input type="text" name="poste_occupe" class="form-control" id="" value="<?php echo $_SESSION['membre']['poste_occupe'];?>">
                                                 <label class="form-label">Poste occupé</label>                      
                                            </div>                                         
                                        </div>
                                        <!-- <div class='col-12 mb-2'>
                                            <div class="form-floating">
                                            <select  name="poste_occupe" class="form-control" required>
                                                <option value=''></option>
                                                <option value="Membre 1000 FCFA" <?php echo selected_option($_SESSION['membre']['poste_occupe'], 'Membre 1000 FCFA'); ?>>Membre 1000 FCFA</option>
                                                <option value="Membre 2000 FCFA" <?php echo selected_option($_SESSION['membre']['poste_occupe'], 'Membre 2000 FCFA'); ?>>Membre 2000 FCFA</option>
                                                <option value="Membre 3000 FCFA" <?php echo selected_option($_SESSION['membre']['poste_occupe'], 'Membre 3000 FCFA'); ?>>Membre 3000 FCFA</option>
                                                <option value="Membre 4000 FCFA" <?php echo selected_option($_SESSION['membre']['poste_occupe'], 'Membre 4000 FCFA'); ?>>Membre 4000 FCFA</option>
                                                <option value="Membre 5000 FCFA" <?php echo selected_option($_SESSION['membre']['poste_occupe'], 'Membre 5000 FCFA'); ?>>Membre 5000 FCFA</option>
                                            </select>  
                                                 <label class="form-label">catégorie...</label>                      
                                            </div>                                         
                                        </div> -->
                                        

                                        <div class='col-12 mb-2'>
                                            <div class="form-floating">
                                                <input type="date" name="date_adhesion" class="form-control" id="" value="<?php echo $_SESSION['membre']['date_adhesion'];?>">
                                                 <label class="form-label">Date d'adhésion</label>                      
                                            </div>                                         
                                        </div>
                                        
                                        <div class='col-12 mb-2'>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="membre_bureau" id="membre_bureau" value="1" 
                                                    <?php echo (isset($_SESSION['membre']['membre_bureau']) && $_SESSION['membre']['membre_bureau'] == 1) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="membre_bureau">
                                                    <strong>Membre du bureau</strong>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                              </div>

                          </div>

                          <div class='col-md-4'>

                              <div class="card card-fluid mb-4">
                                <div class="card-header p-3 d-flex justify-content-between align-items-center">
                                    <span>IMAGE</span>
                                </div>
                                <div class="card-body p-5 mx-auto">
                                    <div id='divImgPrincipale'><?php echo affImgAdmin('200px', '200px', $_SESSION['membre']['logo'], '');?></div>
                                </div>
                              </div>   


                              <div class="card card-fluid mb-4">
                                <div class="card-header p-3">
                                    SIGNATURE 
                                </div>
                                <div class="card-body">
                                    
                                    <div class="col-md-12 mb-2 d-flex justify-content-center align-items-center">
                                        
                                        <img src="<?php echo $SignaturePath; ?>"  class="form-control" style="max-width: 100%; height: 80px; border-radius: .25rem;">
                                    </div>

                                    <div class="col-md-12 mb-2 d-flex justify-content-center align-items-center gap-2">
                                        <a href="../membres/voir_badge.php?id_membre=<?php echo crypt_decrypt_chaine($_SESSION['membre']['id'], 'C'); ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-id-badge"></i> Voir badge
                                        </a>
                                        <a href="paiements_membre.php?id_membre=<?php echo crypt_decrypt_chaine($_SESSION['membre']['id'], 'C'); ?>" class="btn btn-success btn-sm">
                                            <i class="fas fa-credit-card"></i> Paiements
                                        </a>
                                    </div>

                                </div>
                              </div> 

                            <div class="card card-fluid mb-4">
                            <div class="card-header p-3">
                                <h4 class="text-body text-uppercase">Adhésion</h4> 
                            </div>
                            <div class="card-body p-2">
                                <div id='divCheckedStatutadhesion'>
                                    <div class="form-check">
                                        <input type="radio" name="statut_adhesion" class="form-check-input" 
                                            <?php echo comp_2_val_retour($_SESSION['membre']['statut_ad'], 'Payé', 'checked');?> 
                                            id="flexRadioDefault3" value='Payé'>
                                        <label class="form-check-label text-success" for="flexRadioDefault3">Payé</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="statut_adhesion" class="form-check-input"
                                            <?php echo comp_2_val_retour($_SESSION['membre']['statut_ad'], 'Non payé', 'checked');?> 
                                            id="flexRadioDefault4" value='Non payé'>
                                        <label class="form-check-label text-warning" for="flexRadioDefault4">Non payé</label>
                                    </div>
                                </div>
                                <input type="hidden" id="id_adhesion_statut" value="<?php echo crypt_decrypt_chaine($_SESSION['membre']['id'], 'C');?>">
                                <button type="button" id="btnActualiserStatut" onclick="actualiserStatut()" class="btn btn-primary btn-sm mt-3">Actualiser</button>

                            </div>
                            </div>
                              
                          </div>
                      </div>

                      <div>

                        <input type='hidden' name='id_membre' value="<?php echo crypt_decrypt_chaine($_SESSION['membre']['id'], 'C');?>"> 
                        <button type="submit" name='btnSubmit' class="btn btn-primary">Valider modification</button>
                      </div>

                    </form>
              </div>
        </div>

        <?php include('includes/php/footer.php');?>

      </div>
      
    </main>

    <?php include('includes/php/includes-js.php');?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>

  </body>

</html>



<script>
function actualiserStatut() {
    try {
        // Vérifie si une option est cochée
        var radio = document.querySelector('input[name="statut_adhesion"]:checked');
        if (!radio) {
            console.log("Aucun statut sélectionné.");
            return;
        }

        var statut = radio.value;
        var idadhesionInput = document.getElementById('id_adhesion_statut');
        if (!idadhesionInput) {
            console.log("Champ #id_adhesion_statut introuvable.");
            return;
        }

        var idadhesion = idadhesionInput.value;

        // Trouver le bon bouton déclencheur
        var btn = document.querySelector('#btnActualiserStatut'); // Ajoute un id au bouton
        if (!btn) {
            console.log("Bouton introuvable.");
            return;
        }

        // Affiche le spinner
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> En cours...';
        btn.disabled = true;

        // Envoi AJAX
        $.ajax({
            url: 'ajax_autre.php',
            type: 'POST',
            data: { 
                id_adhesion_statut: idadhesion,
                statut_adhesion: statut,
                actualiserStatutAdhesion: ''
            },
            dataType: 'html',
            success: function(response) {
                console.log("Réponse du serveur :", response);

                    afficherToast("Statut agenda actualisé !", 'top-right', 'success', 3000); 
                    setTimeout(() => {
                        location.reload();
                    }, 2000);

                // Si tu veux remettre le bouton à l'état normal
                btn.innerHTML = 'Actualiser';
                btn.disabled = false;
            }
        });
    } catch (e) {
        console.error("Erreur JS : ", e);
    }
}
</script>