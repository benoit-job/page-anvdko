<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 


function statutpaiement($paye, $reste)
{
    if ($paye == 0 || $paye == '') {
        return "<span class='badge text-bg-danger'>Non payé</span>";
    } elseif ($paye > 0 && $reste > 0) {
        return "<span class='badge text-bg-warning'>Moitié payé</span>";
    } elseif ($reste == 0) {
        return "<span class='badge text-bg-success'>Payé</span>";
    } else {
        return '';
    }
}

?>
<?php

if(isset($_GET["id_membre"]))
{
    $_SESSION["id_membre"] = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_GET["id_membre"], 'D') )));
    reload_current_page();
}

$query = "SELECT *
           FROM membres WHERE id =".$_SESSION['id_membre'];
$resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
$_SESSION['membre'] = mysqli_fetch_array($resultat);

?>

<!DOCTYPE html> 
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Paiment membre</title>

    <?php include('includes/php/includes-css.php');?>

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
			  	<div class="card card-fluid">
                    <div class="card-header border-0 p-1 d-flex justify-content-end">
                        <div class="input-group" style="width: 260px;">
                            <span class="input-group-text" id="basic-addon1">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control form-control-sm" onclick='rechercheInput(this)' placeholder="Rechercher...">
                        </div>
                    </div>

	                <div class="table-responsive">
	                  <table class="table table-hover m-0" id='ma_table'> 
	                    <thead class="thead-">
	                      <tr>
	                        <th>Mois</th>
	                        <th class='text-center'>Montant à payer</th>
	                        <th class='text-center'>Montant payé</th>
	                        <th class='text-center'>Reste</th>
	                        <th class='text-center'>Statut</th>
	                        <th></th>
	                      </tr>
	                    </thead>
	                    <tbody> 
                        
<?php
$annee_courante = date("Y");

$query_date = "SELECT date_heure FROM adhesion WHERE id_membre = '".$_SESSION["membre"]["id"]."'";
$result_date = mysqli_query($bdd, $query_date) or die(mysqli_error($bdd));
$row_membre = mysqli_fetch_assoc($result_date);

// Stocker la date d'inscription
$date_inscription = $row_membre['date_heure'];
$timestamp_inscription = strtotime($date_inscription);

$mois_inscription = (int)date("n", $timestamp_inscription);
$annee_inscription = (int)date("Y", $timestamp_inscription);

// Le paiement commence le mois suivant l'inscription
if ($annee_inscription == $annee_courante) {
    $mois_depart = $mois_inscription + 1;
    $annee_depart = $annee_courante;
    if ($mois_depart > 12) {
        $mois_depart = 1;
        $annee_depart += 1;
    }
} elseif ($annee_inscription < $annee_courante) {
    // Inscrit l’année précédente : paiement depuis janvier
    $mois_depart = 1;
    $annee_depart = $annee_courante;
} else {
    // Inscription dans le futur ? On ne fait rien.
    $mois_depart = 12;
    $annee_depart = $annee_courante;
}

$montant_par_mois = floatval($_SESSION["configuration"]["montant_mensuel"] ?? 2000);

$mois_noms = [
    'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
    'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'
];

// Récupérer les paiements existants
$query = "SELECT * FROM paiements WHERE id_membre = '".$_SESSION["membre"]["id"]."'";
$resultat = mysqli_query($bdd, $query) or die("Requête non conforme");

$paiements_existants = [];
while ($row = mysqli_fetch_assoc($resultat)) {
    $paiements_existants[$row['mois_payer']] = $row;
}

// Initialisation des totaux
$total_a_payer = 0;
$total_paye = 0;
$total_reste = 0;

// Boucle des mois restants de l’année en cours (ou année suivante si besoin)
for ($mois = $mois_depart; $mois <= 12; $mois++) {
    if ($mois == 4) continue; // Ignorer le mois d'avril (neutre)
    $mois_format = $annee_depart . '-' . str_pad($mois, 2, '0', STR_PAD_LEFT); // ex: 2025-07
    $nom_mois = safe_safe_ucfirst($mois_noms[$mois - 1]);

    $paiement = isset($paiements_existants[$mois_format]) ? $paiements_existants[$mois_format] : null;

    $a_payer = $montant_par_mois;
    $paye = $paiement['paye'] ?? '';
    $reste = $paiement ? $paiement['reste'] : $montant_par_mois;

    // Totaux
    $total_a_payer += $a_payer;
    $total_paye += floatval($paye);
    $total_reste += floatval($reste);

    $id_crypt = $paiement ? crypt_decrypt_chaine($paiement['id'], 'C') : '';
    $badge_statut = statutpaiement($paye, $reste);

    echo "<tr>   
           <form method='post'>
            <td>".$nom_mois . " " . $annee_depart."</td>  
            <td class='text-center'>".$a_payer."</td>  
            <td class='text-center'>
              <input type='number' name='a_payer' class='form-control' value='".$paye."' placeholder='Paiement' style='width: 180px; margin: 2px; display: inline-block;'>
            </td> 
            <td class='text-center'>".$reste."</td>
            <td class='text-end'> 
                <span class='line-clamp-3'>".$badge_statut."</span> 
            </td>  
            <td class='text-end' style='width: 100px;'>
                <button class='btn btn-success btn-sm' id_produit='".$id_crypt."' onclick='PayerSelectionner(this)'>Payer</button>
            </td>
           </form>
          </tr>"; 
}

// Ligne de récapitulatif
echo "<tr style='font-weight: bold; background: #f8f9fa;' class='total-row'>
        <td class='text-end'>Total :</td>
        <td class='text-center'>".$total_a_payer."</td>
        <td class='text-center'>".$total_paye."</td>
        <td class='text-center'>".$total_reste."</td>
        <td></td>
        <td>                
          <button class='btn btn-success btn-sm' id_produit='".$id_crypt."' onclick='PayerSelectionner(this)'>Payer selctionner</button>
          <button class='btn btn-secondary btn-sm' id_produit='".$id_crypt."' onclick='TousPayer(this)'>Tout payer</button>
        </td>
      </tr>";
?>

	                    </tbody>
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

function PayerSelectionner(button) {
    var $button = $(button);
    var $row = $button.closest('tr');

    var originalContent = $button.html();
    $button.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);

    var data = {};
    var isGlobal = !$row.find("input[name='a_payer']").length; // Si pas de champ input dans la ligne => bouton global

    if (isGlobal) {
        // Bouton "Payer sélectionner" => chercher tous les inputs avec valeur > 0
        $("input[name='a_payer']").each(function () {
            var $input = $(this);
            var montant = parseFloat($input.val()) || 0;
            if (montant > 0) {
                var mois = $.trim($input.closest('tr').find('td:first').text());
                data["paiements[" + mois + "]"] = montant;
            }
        });
    } else {
        // Paiement ligne par ligne
        var mois = $.trim($row.find('td:first').text());
        var montant = parseFloat($row.find("input[name='a_payer']").val()) || 0;
        data["paiements[" + mois + "]"] = montant;
    }

    data["montant_mensuel"] = <?= $montant_par_mois ?>;

    $.ajax({
        url: 'paiement_ajax.php',
        method: 'POST',
        data: data,
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                afficherToast("Paiement effectué avec succès !", 'top-right', 'success', 3000);
                setTimeout(function () {
                    location.reload();
                }, 1000);
            }
        },
        complete: function () {
            $button.html(originalContent).prop('disabled', false);
        }
    });
}


function TousPayer(button) {
    var $btn = $(button);
    var original = $btn.html();
    $btn.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);

    $.post('paiement_ajax.php', {
        tout_payer: 1,
        montant_mensuel: <?= $montant_par_mois ?>
    }, function(res) {
        if (res.success) {
            afficherToast("Tous les mois ont été payés !", 'top-right', 'success', 3000);
            setTimeout(() => location.reload(), 1000);
        }
    }, 'json').always(function() {
        $btn.html(original).prop('disabled', false);
    });
}

$(document).ready(function () {
    $("button.btn-light").on("click", function () {
        var paiements = {};
        $("tr").each(function () {
            var $row = $(this);
            var cols = $row.find("td");
            if (cols.length > 2 && !$row.hasClass('total-row')) {  // Ignorer la ligne de récapitulation
                var mois = $.trim(cols.eq(0).text());
                var montant = parseFloat($row.find("input[name='a_payer']").val()) || 0;
                if (montant > 0) {
                    paiements["paiements[" + mois + "]"] = montant;
                }
            }
        });

        if ($.isEmptyObject(paiements)) {
            alert("Aucun paiement renseigné");
            return;
        }

        paiements["montant_mensuel"] = <?= $montant_par_mois ?>;

        var $btn = $(this);
        var original = $btn.html();
        $btn.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);

        $.ajax({
            url: 'paiement_ajax.php',
            method: 'POST',
            data: paiements,
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    
                afficherToast("Paiement Multiple effectué avec succès !", 'top-right', 'success', 3000); 
                 // Attendre 3 secondes avant de recharger la page
                setTimeout(function() {  location.reload(); }, 1000);
                }
            },
            complete: function () {
                $btn.html(original).prop('disabled', false);
            }
        });
    });
});

</script>