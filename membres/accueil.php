<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 
?>


<?php
if (empty($_SESSION['membre']) || !is_array($_SESSION['membre'])) {
    header("Location: index.php");
    exit;
}

if(isset($_POST['modifiermembres']))  
{
    $id_membre          = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST["id_membre"], 'D'))));
    $nom          = strip_tags(htmlspecialchars(trim($_POST["nom"])));
    $prenom     = strip_tags(htmlspecialchars(trim($_POST["prenom"])));
    $genre     = strip_tags(htmlspecialchars(trim($_POST["genre"])));
    $date_naissance     = strip_tags(htmlspecialchars(trim($_POST["date_naissance"])));
    $lieu_naissance     = strip_tags(htmlspecialchars(trim($_POST["lieu_naissance"])));
    $profession     = strip_tags(htmlspecialchars(trim($_POST["profession"])));
    $nationnalite     = strip_tags(htmlspecialchars(trim($_POST["nationnalite"])));
    $num_telephone     = strip_tags(htmlspecialchars(trim($_POST["num_telephone"])));
    $ville_commune     = strip_tags(htmlspecialchars(trim($_POST["ville_commune"])));
    $poste_occupe     = strip_tags(htmlspecialchars(trim($_POST["poste_occupe"])));
    $email        = strip_tags(htmlspecialchars(trim($_POST["email"])));
    $passwordField = strip_tags(htmlspecialchars(trim($_POST["password"] ?? '')));
    $passwordSql = '';
    if ($passwordField !== '') {
        $passwordSql = ", password = \"" . mysqli_real_escape_string($bdd, anvdko_password_hash($passwordField)) . "\"";
    }

    if(isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) 
    {
        $destination = createPathFile('../fichiers/uploads/').uniqid().'.'.pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);  

        if(move_uploaded_file($_FILES['logo']['tmp_name'], $destination)) 
        {
            //Suppression d'abord
            $supprFichier = '../fichiers/uploads/'.$_SESSION["membre"]["logo"]; 
            @unlink($supprFichier); 

            //Update 
            $nomFichier = str_replace('../fichiers/uploads/', '', $destination);

            $query ="UPDATE membres SET logo = '".$nomFichier."'  WHERE id = ".$_SESSION["membre"]["id"];
            mysqli_query($bdd, $query) or die("Requête non conforme");
        } 
    }


    $query = "UPDATE membres   
             SET nom = \"$nom\", 
                 prenom = \"$prenom\", 
                 genre = \"$genre\", 
                 date_naissance = \"$date_naissance\",
                 lieu_naissance = \"$lieu_naissance\",
                 profession = \"$profession\",
                 nationnalite = \"$nationnalite\",
                 num_telephone = \"$num_telephone\", 
                 ville_commune = \"$ville_commune\",
                 poste_occupe = \"$poste_occupe\",
                 email = \"$email\"
                 $passwordSql
             WHERE id = ".$id_membre;
    mysqli_query($bdd, $query) or die("Requête non conforme"); 

    reload_current_page();
}


$query ="SELECT *, CONCAT(UPPER(nom), ' ', UPPER(prenom)) AS nom_prenom
 FROM membres WHERE id = ".$_SESSION['membre']['id'];
$resultat = mysqli_query($bdd, $query) or die("Requête non conforme");
$_SESSION["membres"] = mysqli_fetch_array($resultat);

$logo = isset($_SESSION["membres"]["logo"]) && !empty($_SESSION["membres"]["logo"]) 
    ? $_SESSION["membres"]["logo"] 
    : 'no_image.jpg';

$imagePath = "../fichiers/uploads/" . $logo;

if ($logo === 'no_image.jpg') {
    $imagePath = "../fichiers/images/no_image.jpg";
}
$signature = isset($_SESSION["membres"]["signature"]) && !empty($_SESSION["membres"]["signature"])
    ? $_SESSION["membres"]["signature"]
    : '';

$SignaturePath = $signature;
?>

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Membre - ANVDKO </title>

   <?php include('includes/php/include-css.php');?>
    <!-- Bootstrap JS (v5) + Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
body {
  padding-top: 70px; /* si tu as un header fixe */
  margin: 0;
  position: relative;
  z-index: 0;
}

/* Fond animé qui suit le scroll */
.background-animated {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: url('../../assets/img/LOGO.jpg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  animation: zoomBack 30s ease-in-out infinite;
  z-index: -2;
}

/* Overlay sombre pour lisibilité */
.background-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  z-index: -1;
}

/* Animation zoom effet dynamique */
@keyframes zoomBack {
  0%   { background-size: 100%; }
  50%  { background-size: 105%; }
  100% { background-size: 100%; }
}


  /* Effet zoom doux */
  @keyframes zoomBack {
    0% {
      transform: scale(1);
    }
    50% {
      transform: scale(1.05);
    }
    100% {
      transform: scale(1);
    }
  }

@keyframes rainbowGlow {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}

.card-rainbow {
  background: linear-gradient(135deg, #4B0082, #2F4F4F, #3CB371, #1E90FF, #8B4513);
  background-size: 300% 300%;
  animation: rainbowGlow 10s ease infinite;

  color: white;
  border: none;
  border-radius: 1rem;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
  overflow: hidden;

  /* Apparition douce */
  opacity: 0;
  transform: translateY(20px);
  animation: fadeInUp 0.8s ease-out forwards;
  animation-delay: 0.3s;
}

@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.card-rainbow:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 25px rgba(0, 0, 0, 0.4);
  transition: all 0.3s ease;
}

.card-rainbow::before {
  content: "";
  position: absolute;
  top: -3px;
  left: -3px;
  right: -3px;
  bottom: -3px;
  background: linear-gradient(45deg, #ff8c00, #6a5acd, #00ced1, #ff1493);
  background-size: 400% 400%;
  border-radius: 1.2rem;
  z-index: -1;
  animation: pulseBorder 8s linear infinite;
  filter: blur(5px);
}

@keyframes pulseBorder {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}


.card-rainbow .card-header {
  background-color: rgba(0, 0, 0, 0.2);
  font-weight: bold;
  font-size: 1.2rem;
  text-transform: uppercase;
  border: none;
}

.card-rainbow .card-body {
  background-color: rgba(255, 255, 255, 0.1);
  border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.card-rainbow img {
  border-radius: 0.5rem;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.card-rainbow h4 {
  font-weight: bold;
  color: #fff;
  text-shadow: 1px 1px 2px #000;
}

.card-rainbow .btn {
  background-color: white;
  color: #333;
  font-weight: bold;
  border: none;
  transition: transform 0.3s, background 0.3s;
}

.card-rainbow .btn:hover {
  transform: scale(1.05);
  background: #eee;
}

    
    div span i {
      font-size: 100px;
    }
 /* LABELS : majuscules */
    .form-label {
      text-transform: uppercase;
    font-size: 1.1rem;         /* Texte plus grand */
    }

    /* CHAMPS : plus hauts + texte italic & majuscule (placeholder ET saisie) */
    
    .form-control {
    font-weight: bold;         /* Texte en gras */
    font-size: 1.1rem;         /* Texte plus grand */
    text-transform: uppercase; /* Tout en MAJUSCULES (si tu veux aussi ici) */
    font-style: italic;
    }

    .form-control::placeholder {
    padding-top: 0.85rem;
    padding-bottom: 0.85rem;
    font-style: italic;
    text-transform: uppercase;
    font-weight: normal;       /* Ne pas hériter du gras ici */
    font-size: 1rem;
    }

    .form-control:focus {
      border-color: #FF7F00;
      box-shadow: 0 0 0 0.25rem rgba(255, 127, 0, 0.25);
    }

    .btn {
         font-size: 10px; 
         background: linear-gradient(45deg, #ff8c00, #6a5acd, #00ced1, #ff1493)
    }
    
<style>
    /* Animation au survol */
    .hover-scale {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-scale:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    /* Style pour le header de la card */
    .card-rainbow .card-header {
        border-radius: 0.35rem 0.35rem 0 0 !important;
    }
    
    /* Style pour les cartes */
    .card-rainbow .card-body .rounded {
        overflow: hidden;
    }
</style>

</style>

  </head>


  <body>
    <div class="background-animated"></div>
    <div class="background-overlay"></div>


  <?php include('includes/php/header.php');?>

  	<div class='container'>
		<div class='row'>
			<div class='col-md-4'>
				<div class="card card-rainbow mb-5">
                    <div class="card-header p-3 border-bottom text-uppercase"> 
                        PHOTO D'IDENTITÉ
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center g-3 text-center text-xxl-start">
                        <div class="col-12 col-xxl-auto">
                            <div class="avatar avatar-5xl">
                            <img class="rounded img-thumbnail" src="<?php echo $imagePath; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-sm-auto flex-1">
                            <h4 class="fw-bolder mb-2"><?php echo $_SESSION["membres"]["nom_prenom"]; ?></h4>
                            <!-- <a href="badge1.php" class="btn btn-sm rounded mt-2">Voir mon badge</a> -->
                            <!-- <a href="voir_badge.php?id_membre=<?php echo crypt_decrypt_chaine($_SESSION["membres"]["id"], 'C'); ?>" class="btn btn-sm rounded mt-2">Voir mon badge</a> -->
                            <a href="badge.php?id_membre=<?php echo crypt_decrypt_chaine($_SESSION["membres"]["id"], 'C'); ?>" class="btn btn-sm rounded mt-2">Mon badge</a>
                        </div>
                        </div>
                    </div>
                </div>

                <div class="card card-rainbow mb-3">
                    <div class="card-header p-3 border-bottom"> 
                        SIGNATURE
                    </div>
                    <div class="card-body">
                        <div class="row">
                        <div class="col-md-12 mb-2 d-flex justify-content-center align-items-center">
                            <img id="imgSignatureAffiche" src="<?php echo $SignaturePath; ?>" class="form-control AfficheSignature" style="max-width: 100%; height: 100px;">
                        </div>
                        <div class="col-md-12 text-center mb-3"> 
                            <button class="btn btn-sm" type="button" onclick='ouvrirModalSignature()'>Signer ici</button>
                        </div>
                        </div>
                    </div>
                </div>
			</div>
            <div class='col-md-8 divMenu'>

                <!-- FORMULAIRE MEMBRE -->
                <div class="card card-rainbow mb-5">
                    <div class="card-header p-3 border-bottom text-uppercase"> 
                        Information Membre
                    </div>
                    <div class="card-body">
                        <form action="accueil.php" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nom </label>
                                        <input type="text" name="nom" class="form-control" value="<?php echo $_SESSION["membres"]["nom"];?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Prénoms </label>
                                        <input type="text" name="prenom" class="form-control" value="<?php echo $_SESSION["membres"]["prenom"];?>"/>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="text-center">
                                        <label class="form-label d-block mb-2"><i class="fa fa-venus-mars"></i> Genre</label>
                                        <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
                                            <div class="form-check form-check-inline m-0">
                                                <input class="form-check-input" type="radio" name="genre" id="genreHomme" value="HOMME" 
                                                    <?php if ($_SESSION["membres"]["genre"] == 'HOMME') echo 'checked'; ?>>
                                                <label class="form-check-label" for="genreHomme">Homme</label>
                                            </div>
                                            <div class="form-check form-check-inline m-0">
                                                <input class="form-check-input" type="radio" name="genre" id="genreFemme" value="FEMME" 
                                                    <?php if ($_SESSION["membres"]["genre"] == 'FEMME') echo 'checked'; ?>>
                                                <label class="form-check-label" for="genreFemme">Femme</label>
                                            </div>
                                            <div class="form-check form-check-inline m-0">
                                                <input class="form-check-input" type="radio" name="genre" id="genreMademoiselle" value="MADEMOISELLE" 
                                                    <?php if ($_SESSION["membres"]["genre"] == 'MADEMOISELLE') echo 'checked'; ?>>
                                                <label class="form-check-label" for="genreMademoiselle">Mademoiselle</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Date de naissance </label>
                                        <input type="date" name="date_naissance" class="form-control" value="<?php echo $_SESSION["membres"]["date_naissance"];?>"/>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Lieu de naissance </label>
                                        <input type="text" name="lieu_naissance" class="form-control" value="<?php echo $_SESSION["membres"]["lieu_naissance"];?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Profession</label>
                                        <input type="text" name="profession" class="form-control" value="<?php echo $_SESSION["membres"]["profession"];?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nationnalite </label>
                                        <input type="text" name="nationnalite" class="form-control" value="<?php echo $_SESSION["membres"]["nationnalite"];?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Contact </label>
                                        <input type="tel" name="num_telephone" class="form-control" value="<?php echo $_SESSION["membres"]["num_telephone"];?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Lieu de résidence</label>
                                        <input type="text" name="ville_commune" class="form-control" value="<?php echo $_SESSION["membres"]["ville_commune"];?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" value="<?php echo $_SESSION["membres"]["email"];?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Poste occupé</label>
                                        <input type="text" name="poste_occupe" class="form-control" value="<?php echo $_SESSION["membres"]["poste_occupe"];?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 text-start">
                                        <label class="form-label" for="password">Modifer Mot de passe</label>
                                        <div class="position-relative">
                                            <span class="fas fa-key text-body fs-9 position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%);"></span>
                                            <input class="form-control ps-5 pe-5 password" id="password" type="password" name="password" placeholder="Laisser vide pour ne pas changer" />
                                            <span class="fas fa-eye-slash text-body fs-9 position-absolute" id="toggle-password"
                                            onclick="togglePassword()" style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-2 mb-3">
                                    <label for="logo" class="form-label d-block mb-2">Ajouter une photo ici</label>

                                    <!-- Image d'aperçu centrée -->
                                    <img src="<?php echo $imagePath; ?>" height="100px" class="border rounded mb-2" style="display: block; margin: 0 auto;">

                                    <!-- Champ d'upload contenu dans un div pour un centrage sûr -->
                                    <div style="width: 250px; margin: 0 auto;">
                                        <input type="file" id="logo" name="logo" class="form-control" accept="image/*" />
                                    </div>
                                </div>

                                <div class='text-end'>
                                    <input type="hidden" name="id_membre" value="<?php echo crypt_decrypt_chaine($_SESSION['membres']['id'], 'C'); ?>">
                                    <button class="btn btn-sm" type="submit" name="modifiermembres">Valider les modifications</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- COTISATIONS -->
                <div class="card card-rainbow mb-5">
                    <div class="card-header p-3 border-bottom text-uppercase" style="background: linear-gradient(90deg, #4e73df 0%, #224abe 100%); color: white;"> 
                        Mes Cotisations
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center g-2 m-0">
                            <!-- Première carte - Mensuelles -->
                            <div class="col-6 col-sm-4 col-md-3">
                                <a href="voir_cotisation.php?id_membre=<?php echo crypt_decrypt_chaine($_SESSION["membres"]['id'], 'C')?>" 
                                class="ratio ratio-1x1 d-block text-decoration-none border-0 rounded shadow-sm hover-scale"
                                style="background: linear-gradient(135deg, #f6c23e 0%, #f8d568 100%);">
                                    <div class="d-flex flex-column justify-content-center align-items-center h-100 p-2">
                                        <i class="fas fa-coins fa-3x mb-2" style="color: #2c3e50;"></i>
                                        <div class="text-center fw-bold" style="color: #2c3e50;">
                                            VOIR MES COTISATIONS MENSUELLES
                                        </div>
                                    </div>
                                </a>
                            </div>
                            
                            <!-- Deuxième carte - Exceptionnelles -->
                            <div class="col-6 col-sm-4 col-md-3">
                                <a href="recap_pay_exeptionnels.php?id_membre=<?php echo crypt_decrypt_chaine($_SESSION["membres"]['id'], 'C')?>" 
                                class="ratio ratio-1x1 d-block text-decoration-none border-0 rounded shadow-sm hover-scale"
                                style="background: linear-gradient(135deg, #e74a3b 0%, #eb675a 100%);">
                                    <div class="d-flex flex-column justify-content-center align-items-center h-100 p-2">
                                        <i class="fas fa-coins fa-3x mb-2" style="color: white;"></i>
                                        <div class="text-center fw-bold" style="color: white;">
                                            VOIR MES COTISATIONS EXCEPTIONNELLES
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <form method="post" action="accueil.php">
                <div class="modal fade" id="modalSignature" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Signature</h5>
                            <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs-9"></span></button>
                        </div>
                        <div class="modal-body d-flex justify-content-center align-items-center p-1" style="height: 100%;">
                            <canvas id="signature" class="border" width="'500" height="200"></canvas>
                        </div>
                        <div class="modal-footer">
                            <button id='clear' type="button" class='btn btn-warning'>Effacer</button>
                            <button id='save' type="button" class='btn btn-primary' style='width: 150px;'>Valider</button>
                        </div>
                        </div>
                    </div>
                </div>
            </form>
		</div>

        
  	</div>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

   <?php include('includes/php/footer.php');?>
   <?php include('includes/php/include-css.php');?>
  </body>

</html>
 

<script>
  // Fonction pour afficher/masquer le mot de passe
  function togglePassword() {
    var passwordField = document.getElementById("password");
    var toggleIcon = document.getElementById("toggle-password");
    
    // Vérifier si le mot de passe est visible
    if (passwordField.type === "password") {
      passwordField.type = "text";  // Afficher le mot de passe
      toggleIcon.classList.remove("fa-eye-slash");
      toggleIcon.classList.add("fa-eye");
    } else {
      passwordField.type = "password";  // Cacher le mot de passe
      toggleIcon.classList.remove("fa-eye");
      toggleIcon.classList.add("fa-eye-slash");
    }
  }
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.3.4/signature_pad.min.js" integrity="sha512-Mtr2f9aMp/TVEdDWcRlcREy9NfgsvXvApdxrm3/gK8lAMWnXrFsYaoW01B5eJhrUpBT7hmIjLeaQe0hnL7Oh1w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    var signaturePad = new SignaturePad(document.getElementById('signature'), {
        backgroundColor: 'rgba(255, 255, 255, 0)',
        penColor: '#0000FF'
    });

    var saveButton = document.getElementById('save');
    var cancelButton = document.getElementById('clear');

    function ouvrirModalSignature() {
        var modal = new bootstrap.Modal(document.getElementById('modalSignature'));
        modal.show();
    }

    saveButton.addEventListener('click', function () {
    if (!signaturePad.isEmpty()) {
        var signature = signaturePad.toDataURL('image/png');
        console.log("Signature générée :", signature.substring(0, 100)); // raccourci pour aperçu
        $('#save').html("<div class='spinner-border spinner-border-sm' role='status'></div> Enregistrement...");
      
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: {
                id_membre: <?php echo json_encode(crypt_decrypt_chaine($_SESSION['membres']['id'], 'C')); ?>,
                signature: signature,
                pathFolder: '../fichiers/uploads/',  
                enregistrerSignature: '1'
            },
            success: function (response) {
                $('#save').html('Enregistrer');

                var chemin = response.trim();
                $('#imgSignatureAffiche').attr('src', chemin);
                
                location.reload();
                $('#modalSignature').modal('hide');
            }
        });
    } else {
        if (confirm('Aucune signature ! Voulez-vous enregistrer quand même ?')) {
            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                data: {
                    id_membre: <?php echo json_encode(crypt_decrypt_chaine($_SESSION['membres']['id'], 'C')); ?>,
                    signature: '',
                    pathFolder: '../fichiers/uploads/',
                        SupprimerSignature: '1'
                    },
                    success: function (response) {
                        
                        $('#imgSignatureAffiche').attr('src', '');
                
                        location.reload();
                        $('#modalSignature').modal('hide');
                    }
                });
            }
        }
    });


    cancelButton.addEventListener('click', function () {
        signaturePad.clear();
    });
</script>


<script>
// function verifierStatut(statut, id_membre) {
//     if (statut !== "Payé") {
//         Swal.fire({
//             title: "Paiement requis",
//             text: "Vous devez payer votre adhésion pour accéder à votre badge.",
//             icon: "warning",
//             showCancelButton: true,
//             confirmButtonText: "Payer maintenant",
//             cancelButtonText: "Quitter",
//             reverseButtons: true,
//             customClass: {
//                 confirmButton: 'btn btn-success me-2',
//                 cancelButton: 'btn btn-secondary'
//             },
//             buttonsStyling: false
//         }).then((result) => {
//             if (result.isConfirmed) {
//                 window.location.assign("tel:+2252724584789");
//             }
//         });
//     } else {
//         window.location.href = "voir_badge.php?id_membre=" + id_membre;
//     }
// }
</script>