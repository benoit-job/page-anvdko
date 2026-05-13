
<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 

?>
<?php
if (isset($_GET["ids_membres"])) {
    $ids_cryptes = explode(',', $_GET["ids_membres"]); // Séparer chaque ID
    $ids_decryptes = [];

    foreach ($ids_cryptes as $id_crypte) {
        $id = crypt_decrypt_chaine(trim($id_crypte), 'D');
        if (is_numeric($id)) { // On ne garde que les ID valides
            $ids_decryptes[] = intval($id);
        }
    }

    // Stocker proprement en session sous forme de tableau
    $_SESSION["ids_membres"] = $ids_decryptes;

    // Redirection vers la même page (optionnel, selon ton flux)
    reload_current_page();
}

$ids = $_SESSION["ids_membres"];
$ids_list_sql = implode(',', array_map('intval', $ids)); // sécurise les ids
$query = "SELECT * FROM membres WHERE id IN ($ids_list_sql)";
$resultat = mysqli_query($bdd, $query) or die("Requête non conforme");

$_SESSION['membres'] = [];
while ($row = mysqli_fetch_assoc($resultat)) {
    $_SESSION['membres'][] = $row;
}

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
                <h4 class="mb-2">Plusieurs paiements</h4>
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
	                        <th class='text-center'>Montant payé</th>
	                        <th></th>
	                      </tr>
	                    </thead>
	                    <tbody>
<?php 
$annee_courante = date("Y");
$montant_par_mois = 1000;
$mois_noms = [
    'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
    'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'
];

// Récupère les IDs des membres dans la session
$ids = $_SESSION["ids_membres"] ?? [];
$ids_list = implode(',', array_map('intval', $ids)); // sécurise

if (!empty($ids)) {
    for ($mois = 1; $mois <= 12; $mois++) {
        $mois_format = $annee_courante . '-' . str_pad($mois, 2, '0', STR_PAD_LEFT);
        $nom_mois = safe_safe_ucfirst($mois_noms[$mois - 1]);
        
        // Vérifier si tous les membres ont payé ce mois
        $tous_payes = true;
        $total_membres = count($ids);
        $total_payes = 0;
        
        foreach ($ids as $id_membre) {
            $sql = "SELECT paye FROM paiements 
                    WHERE id_membre = $id_membre 
                    AND mois_payer = '$mois_format' 
                    AND paye >= $montant_par_mois";
            $res = mysqli_query($bdd, $sql);
            
            if (mysqli_num_rows($res) == 0) {
                $tous_payes = false;
            } else {
                $total_payes++;
            }
        }
        
        // Déterminer la valeur à afficher
        $value = '';
        if ($tous_payes) {
            $value = 'value="'.$montant_par_mois.'"';
        } elseif ($total_payes > 0) {
            // Optionnel: Afficher le montant partiel si certains ont payé
            $value = 'value="'.($montant_par_mois * $total_payes / $total_membres).'"';
        }

        echo "<tr data-mois='$mois_format'>
                <td>$nom_mois $annee_courante</td>
                <td class='text-center'>
                    <input type='text' 
       name='a_payer' 
       class='form-control form-control-sm text-end' 
       placeholder='Montant en FCFA' 
       oninput='formatMontant(this)'
       required
       pattern='[0-9, ]+'
       title='Entrez un montant valide (ex: 1 000,50)'>
<small class='text-muted'>Utilisez des virules pour les centimes</small>
                </td>
                <td class='text-end'>
                    <button type='button' class='btn btn-success btn-sm' onclick='PayerSelectionner(this)'>Payer ce mois</button>
                </td>
              </tr>";
    }
}
?>

</tbody>
<tfoot>
    <tr>
        <td colspan="3" class="text-end">
            <button class="btn btn-success btn-sm d-none" onclick="PayerSelectionner(this)">Payer mois sélectionné</button>
            <button class="btn btn-secondary btn-sm" onclick="TousPayer(this)">Tout payer l’année</button>
        </td>
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

<script>
  function PayerSelectionner(button) {
    var $button = $(button);
    var $row = $button.closest('tr');
    var originalContent = $button.html();
    $button.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);

    var mois = $row.data('mois');
    var $montantInput = $row.find("input[name='a_payer']");
    
    // Récupération et nettoyage du montant
    var montantStr = $montantInput.val().trim()
        .replace(/\s/g, '')  // Supprime les espaces
        .replace(/,/g, '.');  // Remplace les virgules par des points
    
    // Conversion en nombre avec 2 décimales
    var montant = parseFloat(montantStr);
    if (isNaN(montant)) {
        afficherToast("Veuillez entrer un montant valide", 'top-right', 'error', 3000);
        $button.html(originalContent).prop('disabled', false);
        return;
    }
    
    // Arrondi à 2 décimales
    montant = Math.round(montant * 100) / 100;
    
    console.log('Montant traité:', montant, 'à partir de:', $montantInput.val());

    // Validation côté client
    if (montant <= 0) {
        afficherToast("Le montant doit être supérieur à 0", 'top-right', 'error', 3000);
        $montantInput.focus();
        $button.html(originalContent).prop('disabled', false);
        return;
    }

    // Vérifier que le montant ne dépasse pas 10 000 FCFA
    if (montant > 10000) {
        afficherToast("Le montant ne peut pas dépasser 10 000 FCFA", 'top-right', 'error', 3000);
        $montantInput.focus();
        $button.html(originalContent).prop('disabled', false);
        return;
    }

    console.log('Envoi des données:', {
        action: 'payer_mois',
        ids_membres: <?php echo json_encode($_SESSION["ids_membres"] ?? []); ?>,
        mois: mois,
        montant: montant.toFixed(2) // Format avec 2 décimales
    });
    
    $.ajax({
        url: 'paiementsGroupe_ajax.php',
        method: 'POST',
        data: {
            action: 'payer_mois',
            ids_membres: <?php echo json_encode($_SESSION["ids_membres"] ?? []); ?>,
            mois: mois,
            montant: montant.toFixed(2) // Format avec 2 décimales
        },
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                afficherToast("Paiement effectué avec succès !", 'top-right', 'success', 3000);
                setTimeout(() => location.reload(), 1000);
            } else if (res.errors && res.errors.length > 0) {
                // Afficher la première erreur rencontrée
                afficherToast(res.errors[0], 'top-right', 'error', 5000);
            } else {
                afficherToast("Une erreur inattendue est survenue", 'top-right', 'error', 3000);
            }
        },
        error: function(xhr, status, error) {
            console.error("Erreur AJAX:", status, error);
            afficherToast("Erreur lors de la communication avec le serveur", 'top-right', 'error', 3000);
        },
        complete: function() {
            $button.html(originalContent).prop('disabled', false);
        }
    });
}

// Fonction pour formater le montant pendant la saisie
function formatMontant(input) {
    // Récupérer la position du curseur
    var start = input.selectionStart;
    var end = input.selectionEnd;
    var length = input.value.length;
    
    // Remplacer les points par des virgules et supprimer tout ce qui n'est pas chiffre ou virgule
    var value = input.value.replace(/\./g, ',').replace(/[^\d,]/g, '');
    
    // Sépare les parties entière et décimale
    var parts = value.split(',');
    var integerPart = parts[0] || '';
    var decimalPart = parts.length > 1 ? ',' + parts[1].substring(0, 2) : '';
    
    // Formate la partie entière avec des espaces comme séparateurs de milliers
    integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    
    // Reconstruit la valeur formatée
    var newValue = integerPart + decimalPart;
    
    // Si la valeur a changé, met à jour le champ
    if (newValue !== input.value) {
        input.value = newValue;
        
        // Ajuste la position du curseur
        var diff = newValue.length - length;
        start = Math.max(0, start + diff);
        end = Math.max(0, end + diff);
        input.setSelectionRange(start, end);
    }
}

function TousPayer(button) {
    var $btn = $(button);
    var original = $btn.html();
    $btn.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);

    $.post('paiementsGroupe_ajax.php', {
        action: 'tout_payer',
        ids_membres: <?php echo json_encode($_SESSION["ids_membres"] ?? []); ?>
    }, function(res) {
        if (res.success) {
            afficherToast("Tous les mois ont été payés !", 'top-right', 'success', 3000);
            setTimeout(() => location.reload(), 1000);
        }
    }, 'json').always(function() {
        $btn.html(original).prop('disabled', false);
    });
}
</script>