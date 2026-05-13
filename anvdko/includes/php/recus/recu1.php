<?php 
$query = "SELECT *, 
                 IF(id_client is NULL, concat(nom_client,' ',tel_client), (SELECT concat(nom,' ',contact1) FROM clients WHERE id = commandes.id_client)) AS client,
                 IF(id_livreur is NULL, concat(nom_livreur,' ',tel_livreur), (SELECT concat(nom,' ',contact1) FROM livreurs WHERE id = commandes.id_livreur)) AS livreur, 
                 (SELECT pseudo FROM utilisateurs WHERE id = commandes.id_utilisateur) AS utilisateur   
          FROM commandes WHERE id =".$_SESSION['id_commande']; 
$resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
$_SESSION['commande'] = mysqli_fetch_array($resultat);  


$query = "SELECT * FROM fermes WHERE id =".$_SESSION['commande']['id_fermes']; 
$resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
$ferme = mysqli_fetch_array($resultat); 


$url_logo     = srcImage($ferme['logo']); 
$titre        = safe_safe_ucfirst($ferme['nom']); 
$localisation = safe_safe_ucfirst($ferme['localisation']); 

$ferme['contact1'] = '(+225) '.wordwrap($ferme['contact1'],2,' ' , true);

if($ferme['contact2'] != ''){$ferme['contact2'] = ' / '.wordwrap($ferme['contact2'],2,' ' , true);}

$contacts = $ferme['contact1'].$ferme['contact2'];
?>

<!DOCTYPE html>
<html>
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"> 

    <title>Imprimer reçu de vente</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.2/html2canvas.min.js"></script>

</head>
<body style="padding: 50px 0;">

    <div style="width: 70mm; padding: 10px 0;" class="border rounded-lg shadow-sm mx-auto">

        <div id="div_recu" class='mx-auto' style="width: 58mm; padding-top: 5px; padding-bottom: 5px;">

            <div style='text-align: center; margin-bottom: 10px;'>
            	<div>
            		<img src="<?php echo srcImage($_SESSION['ferme']['logo']);?>" height='60px' style='max-width: 80%; margin-bottom: 5px;'>
            	</div>
                <div style='display: inline-block; text-align: center; max-width: 56mm; border: 1px solid #808080; border-radius: 5px; padding: 3px 6px; font-size: 14px;'>
                  <?php echo $titre;?>
                </div>
                <div style='font-size: 10px; margin-top: 5px;'>Tél: <?php echo $contacts;?></div> 
                <div style='font-size: 11px;'><?php echo $ferme['localisation'];?></div>
            </div>

            <p> 
                <div style='font-size: 12px;'>n°Reçu : <b><?php echo $_SESSION['commande']['n_commande'];?></b></div>

                <table style='margin-top: 5px;'>  
                    <tr style='font-size: 12px;'>
                        <td style='vertical-align: top;'>
                            <span style='white-space: nowrap;'>Client : </span>
                        </td>
                        <td style='padding-left: 5px;'><?php echo ucwords($_SESSION['commande']['client']);?></td>
                    </tr>
                    <tr style='font-size: 12px;' class='serveurBloc'>
                        <td style='vertical-align: top;'>
                            <span style='white-space: nowrap;'>Livreur : </span>
                        </td>
                        <td style='padding-left: 5px;'><?php echo ucwords($_SESSION['commande']['livreur']);?></td>
                    </tr>
                </table>
            </p>

            <table style='width: 100%; font-size: 12px;'>
<?php
$query = "SELECT produits.nom AS nom_produit, 
                 produits_commande.quantite AS qte_produit,
                 produits_commande.prix_unitaire AS prix_vente,
                 produits_commande.montant AS montant, 
                 produits.id AS id_produit, 
                 produits.type AS type_produit,   
                 commandes.statut_commande AS statut_commande   
          FROM produits_commande 
          INNER JOIN commandes ON commandes.id = produits_commande.id_commande 
          INNER JOIN produits ON produits.id = produits_commande.id_produit  
          WHERE commandes.id_fermes = ".$_SESSION['ferme']['id']." AND 
                commandes.id = '".$_SESSION['commande']['id']."'
          GROUP BY produits_commande.id 
          ORDER BY produits_commande.id";
$resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
while($produitCommande = mysqli_fetch_array($resultat))
{
    $nom_produit = $produitCommande['nom_produit'];

    if(!empty($produitCommande['autre_infos']))
    {
        $nom_produit = $nom_produit." (".$produitCommande['autre_infos'].")"; 
    } 

    $quantite   = $produitCommande['qte_produit'];
    $prix_vente = number_format($produitCommande['prix_vente'],0,'',' ').' F'; 
    $montant    = number_format($produitCommande['montant'],0,'',' ').' F';

  echo "<tr>
            <td style='padding-bottom: 5px;'>
                <span>".$nom_produit."</span> 
                <span style='white-space: nowrap; float: right;'>
                    (".$quantite." x ".$prix_vente." = ".$montant.")
                </span> 
            </td>
        </tr>"; 
}
?>
            </table>


            <?php $infosPay = obtenirInfosPaiementCommande($bdd, $_SESSION['commande']['id']); ?>

      <table style='width: 100%; font-size: 12px; margin-top: 8px;'>
          <tr>
              <td>
                  <div style='font-weight: bold;'>
                      <span>Sous Total</span>
                      <span style='float: right;'><?php echo number_format($infosPay['sous_total'],0,'',' ');?></span>
                  </div>
              </td>
          </tr>
          <tr>
              <td>
                  <div>
                      <span>Remise</span>
                      <span style='float: right;'><?php echo number_format($infosPay['remise'],0,'',' ');?></span>
                  </div>
              </td>
          </tr>
          <tr class='serveurBloc'>
              <td>
                  <div>
                      <span>Frais de Livraison</span>
                      <span style='float: right;'><?php echo number_format($infosPay['frais_livraison'],0,'',' ');?></span>
                  </div>
              </td>
          </tr>
                <tr>
                    <td>
                        <div style='font-weight: bold;'>
                            <span>Total à Payer</span>
                            <span style='float: right;'><?php echo number_format($infosPay['total_a_payer'],0,'',' ');?></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>
                            <span>Montant Reçu</span>
                            <span style='float: right;'><?php echo number_format($infosPay['montant_recu'],0,'',' ');?></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>
                            <span>Montant Rendu</span>
                            <span style='float: right;'><?php echo number_format($infosPay['montant_rendu'],0,'',' ');?></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style='font-weight: bold;'>
                            <span>Reste à Payer</span>
                            <span style='float: right;'><?php echo number_format($infosPay['reste_a_payer'],0,'',' ');?></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>
                            <span>Statut Commande</span>
                            <span style='float: right; font-weight: bold;'><?php echo safe_safe_ucfirst($infosPay['statut_livraison']);?></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>
                            <span>Statut Paiement</span>
                            <span style='float: right; font-weight: bold;'><?php echo safe_safe_ucfirst($infosPay['statut_paiement']);?></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <hr style='margin: 5px 0 0 0;'>

                        <div style='text-align: center; font-size: 12px;'>
                            <div style='margin-top: 4px; margin-bottom: 2px;'><b>Merci, Au Revoir et à Bientôt !</b></div>
                            <div>
            <span style='float: left;'>Par: <?php echo safe_safe_ucfirst(break_last_name($_SESSION['commande']['utilisateur'], 15));?></span>
            <span style='float: right;'><?php echo date('d/m/Y H:i:s');?></span>
                            </div>
                        </div>
                    </td>                   
                </tr>
      </table>

        </div>

        <div style="text-align: center; margin-top: 50px;">
            <div class='row'>
                <div class='col-6'>
    <a id='btnTelecharger' href="javascript:;" class="btn btn-sm btn-success d-block" style='position: relative; left: 10px;'>
        <i class="fas fa-download"></i> Télécharger
    </a>
                </div> 
                <div class='col-6'>
    <a id='btnImprimer' href="javascript:;" class="btn btn-sm btn-warning d-block" style='position: relative; right: 10px; color: white;'>
        <i class="fas fa-print"></i> Imprimer
    </a>
                </div>
            </div>
        </div>

    </div>

</body>
</html>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>

  if ("<?php echo $_SESSION['domaine_admin']['type_activite'];?>" == 'lavage')
  {
      document.querySelectorAll('.serveurBloc').forEach(element => {
          element.remove();
      });

      document.querySelectorAll('.serveurNom').forEach(element => {
          element.innerHTML = 'Laveur(se) : ';
      });
  }
</script>

<script>
    const contentDiv  = document.getElementById("div_recu");
    const btnImprimer = document.getElementById("btnImprimer");
    const btnTelecharger = document.getElementById("btnTelecharger");

    // Utiliser html2canvas pour capturer le contenu de la div
    html2canvas(contentDiv).then(canvas => {

        // Obtenir la représentation base64 de l'image
        const imageDataURL = canvas.toDataURL("image/png");

        // Mettre à jour le lien pour imprimer avec la valeur base64
        btnImprimer.href = "rawbt:" + imageDataURL;

        // Ajouter un gestionnaire d'événements au bouton de téléchargement
        btnTelecharger.addEventListener("click", () => {
            // Créer un élément d'ancrage pour le téléchargement
            const downloadLink = document.createElement("a");
            downloadLink.href = imageDataURL;

            var nomRecu = "Reçu <?php echo $_SESSION['commande']['n_commande'];?>";

            downloadLink.download = nomRecu + ".png";
            downloadLink.click();
        });
    });
</script>