<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 

function ProgressBarFacturePay($bdd, $id_membre, $moisAnnee)
{
    $sql = " SELECT a_payer, paye, reste 
        FROM paiements 
        WHERE id_membre = '$id_membre' AND mois_payer = '$moisAnnee' 
        LIMIT 1";

    $res = mysqli_query($bdd, $sql) or die("Erreur SQL");
    $paiement = mysqli_fetch_assoc($res);

    if (!$paiement) {
        return "<div style='width: 70px;'></div>";
    }

    $a_payer = floatval($paiement['a_payer']);
    $paye = floatval($paiement['paye']);
    $reste = floatval($paiement['reste']);

    $pourcentage = $a_payer > 0 ? round(($paye * 100) / $a_payer, 2) : 0;

    $url = "paiements_membre.php?id_membre=" . crypt_decrypt_chaine($id_membre, 'C');
    $baseStyle = "height:25px; width:70px; margin:0 1px;";
    $tooltip = "data-bs-toggle='tooltip' data-bs-placement='top' title='Paiement du mois $moisAnnee'";

    if ($paye >= $a_payer && $a_payer > 0) {
        $class = "bg-success";
        $label = "{$pourcentage}%";
    } elseif ($paye > 0 && $paye < $a_payer) {
        $class = "bg-warning text-white fw-bold";
        $label = "{$pourcentage}%";
    } else {
        $class = "bg-danger";
        $label = "non payé";
        $pourcentage = 100;
    }

    return "<a href='$url' class='progress inline-block text-decoration-none aProgressBar' style='$baseStyle' $tooltip>
            <div class='progress-bar rounded-3 d-flex justify-content-center align-items-center $class' 
                 role='progressbar' style='width: {$pourcentage}%; font-size: 11px;' 
                 aria-valuenow='{$pourcentage}' aria-valuemin='0' aria-valuemax='100'>
                 <span>$label</span>
            </div>
        </a>";
}

?>
<?php
if(empty($_SESSION['annee_actuelle']))       
{
    $_SESSION["annee_actuelle"] = date('Y');
}

if(isset($_POST["submitAnnee"]))       
{
	  $_SESSION['annee_actuelle'] = strip_tags(htmlspecialchars(trim($_POST["annee_actuelle"])));
    reload_current_page();
}
?>

<!DOCTYPE html> 
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ANVDKO - paiement mensuels</title>

    <?php include('includes/php/includes-css.php');?>

    <style>
.checkbox-style {
  appearance: none;
  -webkit-appearance: none;
  width: 15px;
  height: 15px;
  border: 2px solid #ccc;
  border-radius: 4px;
  cursor: pointer;
  position: relative;
  outline: none;
  background-color: #fff;
  transition: all 0.3s ease;
}

/* Koulè lè li chwazi */
.checkbox-style:checked {
  background: linear-gradient(45deg, #ff8c00, #6a5acd, #00ced1, #ff1493);
  border-color: transparent;
}

/* Senbòl "check" anndan */
.checkbox-style:checked::after {
  content: '✔';
  color: white;
  font-size: 16px;
  position: absolute;
  top: 0;
  left: 5px;
  font-weight: bold;
}
</style>


  </head>


  <body>

    <main class="main" id="top">
    	
      <?php include('includes/php/menu.php');?>

      <?php include('includes/php/header.php');?>

      <div class="content">

        <div class="pb-5">
              <div class="mb-5">
                <h3 class="mb-2">Paiements mensuels</h3>
                <h5 class="text-body-tertiary fw-semibold">Visualiser et gérer vos paiements mensuels</h5>
              </div>

              <div class="page-section">
                  <div class="card card-fluid">
                    <div class="card-header p-2 border-0"> 
                      <form method='post' action='pay_mensuels.php'>
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                          
                          <!-- Bloc gauche : Année -->
                          <div class="input-group" style="max-width: 200px;">
                            <input type="number" name="annee_actuelle" class="form-control" placeholder="Année" value='<?php echo $_SESSION['annee_actuelle'];?>'>
                            <button type="submit" name="submitAnnee" class="btn btn-primary">Valider</button>
                          </div>
                          
                          <!-- Bloc centre : SMS -->
                          <div class="d-flex justify-content-center flex-grow-1">
                            <a href="plusieursPaiements.php?ids_membres" id='btnSmsMembre' class="btn btn-sm btn-phoenix-success" style='display: none;'>
                              <i class='fas fa-money-check-alt'></i> Effectuer plusieurs paiements
                            </a>
                          </div>
                          
                          <!-- Bloc droite : Recherche -->
                          <div class="input-group" style="max-width: 260px;">
                            <span class="input-group-text" id="basic-addon1">
                              <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control form-control-sm" onclick='rechercheInput(this)' placeholder="Rechercher...">
                          </div>

                        </div>
                      </form>
                    </div>
                    <div class="table-responsive">
                      <table class="table table-hover m-0" id='ma_table' style='white-space: nowrap;'>
                        <thead class="thead-light">
                          <tr>
                            <th class='text-center'><input type='checkbox' class="checkbox-style" id="checkboxTous" onclick="toggleAllCheckboxes(this)"></th>
                            <th>Membre</th>
                            <?php
                              $mois = ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Aoû", "Sep", "Oct", "Nov", "Déc"];
                              foreach ($mois as $month) {echo "<th class='text-center'>$month</th>";}
                            ?>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
            <?php
                  $query = "SELECT *, 
                    CONCAT( CASE WHEN genre = 'HOMME' THEN 'M' WHEN genre = 'FEMME' THEN 'Mme' 
                        WHEN genre = 'MADEMOISELLE' THEN 'Mlle' ELSE genre END, '. ', nom, ' ', prenom) AS nom_prenom 
                FROM membres
              ORDER BY nom ASC";
                  $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
                  
                  while ($membre = mysqli_fetch_array($resultat)) {

                  echo "<tr>
                <td class='text-center py-3'>
                    <input type='checkbox' class='checkboxIdTable checkbox-style' onchange='getSelectedCheckboxes()' value='".crypt_decrypt_chaine($membre['id'], 'C')."'>
                </td>
                <td>
                  ".ucwords(strtolower($membre['nom_prenom']))."<br>
                  (<span class='badge' style='font-size: 10px; color: #fff; padding: 2px; background: linear-gradient(45deg, #ff8c00, #6a5acd, #00ced1, #ff1493);'>".safe_safe_ucfirst($membre['num_adhesion'])."</span>)
                </td>
                  <td class='p-0'>".ProgressBarFacturePay($bdd, $membre['id'], $_SESSION["annee_actuelle"].'-01')."</td>
                  <td class='p-0'>".ProgressBarFacturePay($bdd, $membre['id'], $_SESSION["annee_actuelle"].'-02')."</td>
                  <td class='p-0'>".ProgressBarFacturePay($bdd, $membre['id'], $_SESSION["annee_actuelle"].'-03')."</td>
                  <td class='p-0'>".ProgressBarFacturePay($bdd, $membre['id'], $_SESSION["annee_actuelle"].'-04')."</td>
                  <td class='p-0'>".ProgressBarFacturePay($bdd, $membre['id'], $_SESSION["annee_actuelle"].'-05')."</td>
                  <td class='p-0'>".ProgressBarFacturePay($bdd, $membre['id'], $_SESSION["annee_actuelle"].'-06')."</td>
                  <td class='p-0'>".ProgressBarFacturePay($bdd, $membre['id'], $_SESSION["annee_actuelle"].'-07')."</td>
                  <td class='p-0'>".ProgressBarFacturePay($bdd, $membre['id'], $_SESSION["annee_actuelle"].'-08')."</td>
                  <td class='p-0'>".ProgressBarFacturePay($bdd, $membre['id'], $_SESSION["annee_actuelle"].'-09')."</td>
                  <td class='p-0'>".ProgressBarFacturePay($bdd, $membre['id'], $_SESSION["annee_actuelle"].'-10')."</td>
                  <td class='p-0'>".ProgressBarFacturePay($bdd, $membre['id'], $_SESSION["annee_actuelle"].'-11')."</td>
                  <td class='p-0'>".ProgressBarFacturePay($bdd, $membre['id'], $_SESSION["annee_actuelle"].'-12')."</td>
                  <td></td>
              </tr>";
                }
            ?>                   
                        </tbody>
                        <tfoot>
                          <tr>
                            <th class='text-center'>
                              <input type='checkbox' class="checkbox-style" id="checkboxTous" onclick="toggleAllCheckboxes(this)">
                              
                            <a href="plusieursPaiements.php?ids_membres" id='btnSmsMembre' class="btn btn-sm btn-phoenix-success" style='display: none;'>
                              <i class='fas fa-money-check-alt'></i> Effectuer plusieurs paiements
                            </a>
                            </th>
                            
                          </tr>
                        </tfoot>
                      </table>
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

<script>
function toggleAllCheckboxes(source) {
    let checkboxes = document.querySelectorAll('.checkboxIdTable');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = source.checked;
    });

    // Met à jour les ID sélectionnés
    getSelectedCheckboxes();
}

function getSelectedCheckboxes() {
    var selectedIds = [];
    var selectedNumeros = [];

    document.querySelectorAll('.checkboxIdTable:checked').forEach(function(cb) {
        selectedIds.push(cb.value);
        selectedNumeros.push(cb.getAttribute('data-numero'));
    });

    var ids_membre = selectedIds.join(',');

    // Mise à jour du lien
    var lienSms = document.getElementById('btnSmsMembre');

    if (ids_membre.trim() === '') {
        $('#btnSmsMembre').hide('fast');
        lienSms.href = "plusieursPaiements.php?ids_membres="; // réinitialiser
    } else {
        $('#btnSmsMembre').show('fast');
        lienSms.href = "plusieursPaiements.php?ids_membres=" + encodeURIComponent(ids_membre);
    }

}
</script>

<script>
  
function rechercheInput(element) {
    $(element).on('keyup', function () {
        var value = $(this).val().toLowerCase();
        var rows = $('#ma_table tbody tr');
        var matchFound = false;

        rows.each(function () {
            var rowText = $(this).text().toLowerCase();
            var isMatch = rowText.indexOf(value) > -1;
            $(this).toggle(isMatch);

            if (isMatch) {
                matchFound = true;
            }
        });

        // Supprime tout message précédent
        $('#no-result-message').remove();

        // Si aucun résultat trouvé, affiche un message
        if (!matchFound) {
            $('#ma_table tbody').append(
                '<tr id="no-result-message"><td colspan="100%" style="text-align:center; color:red;">Aucun résultat trouvé</td></tr>'
            );
        }
    });
}
</script>