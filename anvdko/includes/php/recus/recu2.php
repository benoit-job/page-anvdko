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
    <style type="text/css">
        @media print {
            body {
                /*font-size: 30px !important;*/ 
            }
        }
    </style>
</head>
<body onafterprint='myFunction()'>

    <div style='width: 58mm;'>
        <table style='width: 100%; margin-bottom: 30px;'>
            <tr>
                <td colspan='2' style='padding-top: 15px; padding-bottom: 30px; text-align: center !important;'>
                    <div>
                        <img src="<?php echo srcImage($_SESSION['ferme']['logo']);?>" height='60px' style='max-width: 80%; margin-bottom: 5px;'>
                    </div>
                    <div style='display: inline-block; text-align: center; border: 1px solid #808080; border-radius: 5px; padding: 3px 6px;'>
                      <?php echo $titre;?>
                    </div>
                    <span style='white-space: nowrap !important;'>Tél: <?php echo $contacts;?></span> <br> 
                    <span><?php echo $ferme['localisation'];?></span>
                </td>
            </tr>
            <tr>
                <td style='vertical-align: top;'>
                    <span style='white-space: nowrap;'>n°Reçu : </span>
                </td>
                <td style='padding-left: 5px;'><b><?php echo $_SESSION['commande']['n_commande'];?></b></td>
            </tr>
            <tr>
                <td style='vertical-align: top;'>
                    <span style='white-space: nowrap;'>Client : </span>
                </td>
                <td style='padding-left: 5px;'><?php echo ucwords($_SESSION['commande']['client']);?></td>
            </tr>  
            <tr class='serveurBloc'>
                <td style='vertical-align: top;'>
                    <span style='white-space: nowrap;'>Livreur : </span>
                </td>
                <td style='padding-left: 5px;'><?php echo ucwords($_SESSION['commande']['livreur']);?></td>
            </tr>
        </table>

        <table style='width: 100%; margin-top: 30px;'>
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
            <span style='white-space: nowrap; margin-left: 15px;'>
                (".$quantite." x ".$prix_vente." = ".$montant.")
            </span> 
        </td>
    </tr>"; 
}
?>
        </table>


        <?php $infosPay = obtenirInfosPaiementCommande($bdd, $_SESSION['commande']['id']); ?>

        <table style='margin-top: 8px; white-space: nowrap;'>
            <tr style='font-weight: bold;'>
                <td>Sous Total</td>
                <td><?php echo number_format($infosPay['sous_total'],0,'',' ');?></td>
            </tr>
            <tr>
                <td>Remise</td>
                <td><?php echo number_format($infosPay['remise'],0,'',' ');?></td>
            </tr>
            <tr class='serveurBloc'>
                <td>Frais de Livraison</td>
                <td><?php echo number_format($infosPay['frais_livraison'],0,'',' ');?></td>
            </tr>
            <tr style='font-weight: bold;'>
                <td>Total à Payer</td>
                <td><?php echo number_format($infosPay['total_a_payer'],0,'',' ');?></td>
            </tr>
            <tr>
                <td>Montant Reçu</td>
                <td><?php echo number_format($infosPay['montant_recu'],0,'',' ');?></td>
            </tr>
            <tr>
                <td>Montant Rendu</td>
                <td><?php echo number_format($infosPay['montant_rendu'],0,'',' ');?></td>
            </tr>
            <tr style='font-weight: bold;'>
                <td>Reste à Payer</td>
                <td><?php echo number_format($infosPay['reste_a_payer'],0,'',' ');?></td>
            </tr>
            <tr style='font-weight: bold;'>
                <td>Statut Commande</td>
                <td><?php echo safe_safe_ucfirst($infosPay['statut_livraison']);?></td>
            </tr>
            <tr style='font-weight: bold;'>
                <td>Statut Paiement</td>
                <td><?php echo safe_safe_ucfirst($infosPay['statut_paiement']);?></td>
            </tr>
            <tr>
                <td colspan='2'>
                    <hr style='margin: 15px 0 0 0;'>

                    <div>
                        <div style='margin-top: 4px; margin-bottom: 2px;'><b>Merci, Au Revoir et à Bientôt !</b></div>
                        <div>
                            <span>Par: <?php echo safe_safe_ucfirst(break_last_name($_SESSION['commande']['utilisateur'], 15));?></span> <br> 
                            <span>Date: <?php echo date('d/m/Y H:i:s');?></span>
                        </div>
                    </div>
                </td>                  
            </tr>
        </table>
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
    window.print();

    function myFunction() {
      window.close();
    }
</script>