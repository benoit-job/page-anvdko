<?php
session_start();
include('../../../../includes/php/connection_bdd.php');
include_once('../../../../includes/php/fonctions.php');
?>

<?php
if(isset($_POST['actualisationInfosCommande']))  
{
    parse_str($_POST['formData'], $formDataArray);


  	$id_commande   = strip_tags(htmlspecialchars(trim( crypt_decrypt_chaine($formDataArray['id_commande'], 'D') )));
  	$date_commande = strip_tags(htmlspecialchars(trim($formDataArray['date_commande'])));
  	$id_client     = strip_tags(htmlspecialchars(trim( crypt_decrypt_chaine($formDataArray['id_client'], 'D') )));
  	$nom_client    = strip_tags(htmlspecialchars(trim($formDataArray['nom_client'])));
  	$tel_client    = strip_tags(htmlspecialchars(trim($formDataArray['tel_client'])));
  	$loc_client    = strip_tags(htmlspecialchars(trim($formDataArray['loc_client'])));
  	$id_livreur    = strip_tags(htmlspecialchars(trim( crypt_decrypt_chaine($formDataArray['id_livreur'], 'D') )));
  	$nom_livreur   = strip_tags(htmlspecialchars(trim($formDataArray['nom_livreur'])));
  	$tel_livreur   = strip_tags(htmlspecialchars(trim($formDataArray['tel_livreur'])));


    $query = "SELECT id_facture, statut_livraison  FROM commandes  WHERE id =".$_SESSION['commande']['id'];
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
    $commande = mysqli_fetch_array($resultat); 
    if($commande['statut_livraison'] != 'livré')
    {
        $query = "UPDATE commandes 
                  SET date_commande = ".empty_to_NULL($date_commande).",  
                      id_client = ".empty_to_NULL($id_client).", 
                      nom_client = ".empty_to_NULL($nom_client).", 
                      tel_client = ".empty_to_NULL($tel_client).", 
                      loc_client = ".empty_to_NULL($loc_client).", 
                      id_livreur = ".empty_to_NULL($id_livreur).", 
                      nom_livreur = ".empty_to_NULL($nom_livreur).",
                      tel_livreur = ".empty_to_NULL($tel_livreur).", 
                      id_utilisateur = '".$_SESSION['utilisateur']['id']."' 
                  WHERE id_fermes = ".$_SESSION['ferme']['id']." AND 
                        id=".$id_commande;
        mysqli_query($bdd, $query) or die("Requête non conforme"); 
        echo 'succes';
    }
    else
    {
        echo 'echec';
    }
}



if(isset($_POST['remiseFraisLivraison']))  
{
	  $id_commande     = strip_tags(htmlspecialchars(trim( crypt_decrypt_chaine($_POST['id_commande'], 'D') )));
	  $remise          = strip_tags(htmlspecialchars(trim($_POST['remise'])));
	  $frais_livraison = strip_tags(htmlspecialchars(trim($_POST['frais_livraison'])));

    if(empty($remise)){$remise = 0;} 
    if(empty($frais_livraison)){$frais_livraison = 0;}

    $query = "UPDATE commandes 
              SET remise = ".empty_to_NULL($remise).",  
                  frais_livraison = ".empty_to_NULL($frais_livraison).", 
                  id_utilisateur = '".$_SESSION['utilisateur']['id']."' 
              WHERE id_fermes = ".$_SESSION['ferme']['id']." AND 
                    id=".$id_commande;
    mysqli_query($bdd, $query) or die("Requête non conforme"); 
    echo 'succes';
}


if(isset($_POST['listeProduitsCommande']))  
{
      $id_commande = strip_tags(htmlspecialchars(trim( crypt_decrypt_chaine($_POST['id_commande'], 'D') )));

      $query = "SELECT *, 
                   (SELECT nom FROM produits WHERE id = produits_commande.id_produit) AS produit 
                FROM produits_commande
                WHERE id_fermes = ".$_SESSION['ferme']['id']." AND
                      id_commande =".$id_commande."  
                ORDER BY id";
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme77777");
    $ligne = 0;
    $montantT = 0;
    $montantTQ = 0;
    while($produitCommande = mysqli_fetch_array($resultat))
    {

      $id_produit_commande = crypt_decrypt_chaine($produitCommande["id"], 'C'); 

       echo  "<tr>
                <form method='post' action='details_commandes.php'>
                      <td>".++$ligne."</td>
                      <td>".$produitCommande["produit"]."</td>
                      <td>".$produitCommande["prix_unitaire"]."</td>
                      <td class='text-center align-middle'>".$produitCommande["quantite"]."</td>
                      <td>".$produitCommande['montant']."</td>
                      <td class='text-right align-middle pr-1'>

                      <button type='button' class='btn btn-sm btn-icon btn-secondary' 
                          id_produit = '".crypt_decrypt_chaine($produitCommande["id_produit"], 'C')."' 
                          prix_unitaire = '".$produitCommande["prix_unitaire"]."'
                          quantite = '".$produitCommande["quantite"]."'
                          montant = '".$produitCommande['montant']."'
                          id_produit_commande = '".$id_produit_commande."' onclick='getInfosProduitCommande(this)'>
                            <i class='fa fa-pencil-alt'></i>
                      </button>

                    <button type='button' class='btn btn-sm btn-icon btn-secondary' id_produit_commande='".$id_produit_commande."'  onclick='supprProduitsCommande(this)'><i class='far fa-trash-alt'></i></button>

                    </td>
                    </form>
              </tr>";
               
              $montantT += $produitCommande['montant'];
              $montantTQ += $produitCommande['quantite'];
    }


    echo "<tr>
              <td></td>
              <td>SOUS TOTAL</td>
              <td class='text-center'></td>
              <td class='text-center'>".$montantTQ."</td>
              <td>".$montantT."</td>
              <td></td>
          </tr>"; 
} 



if(isset($_POST['supprProduitsCommande']))  
{
    $id_produit_commande = strip_tags(htmlspecialchars(trim( crypt_decrypt_chaine($_POST['id_produit_commande'], 'D') )));

    $query = "SELECT id_facture, statut_livraison  FROM commandes  WHERE id =".$_SESSION['commande']['id']; 
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
    $commande = mysqli_fetch_array($resultat); 
    if($commande['statut_livraison'] != 'livré')
    {
        $query = "DELETE FROM produits_commande WHERE id_fermes = ".$_SESSION['ferme']['id']." AND id =".$id_produit_commande;
        mysqli_query($bdd, $query) or die("Requête non conforme"); 
        echo 'succes'; 
    }
    else
    {
        echo 'echec';
    }
}

if(isset($_POST['ajouterCommandeProduit']))
{
    $id_produit    = strip_tags(htmlspecialchars(trim( crypt_decrypt_chaine($_POST['id_produit'], 'D') )));  
    $type_produit  = strip_tags(htmlspecialchars(trim($_POST['type_produit']))); 
    $prix_unitaire = strip_tags(htmlspecialchars(trim($_POST['prix_unitaire']))); 


    $query = "SELECT id_facture, statut_livraison  FROM commandes  WHERE id =".$_SESSION['commande']['id']; 
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
    $commande = mysqli_fetch_array($resultat); 
    if($commande['statut_livraison'] != 'livré')
    {
        $query = "INSERT INTO produits_commande  (id_fermes,
                                                  id_poulailler, 
                                                  id_produit, 
                                                  id_utilisateur, 
                                                  id_commande,  
                                                  prix_unitaire, 
                                                  quantite, 
                                                  montant,
                                                  date_enreg) 
                                      VALUES     (".$_SESSION['ferme']['id'].", 
                                                  ".$_SESSION['commande']['id_poulailler'].", 
                                                  \"$id_produit\", 
                                                  ".$_SESSION['utilisateur']['id'].", 
                                                  ".$_SESSION['commande']['id'].",  
                                                  \"$prix_unitaire\", 
                                                  '1', 
                                                  \"$prix_unitaire\",
                                                  '".date('Y-m-d H:i:s')."')"; 
        mysqli_query($bdd, $query) or die("Requête non conforme"); 
        echo 'succes';  
    }
    else
    {
        echo 'echec';
    }     
}




if(isset($_POST['ModifierCommandeProduit']))  
{
    parse_str($_POST['formData'], $formDataArray);
    $prix_unitaire       = strip_tags(htmlspecialchars(trim($formDataArray['prix_unitaire'])));
    $quantite            = strip_tags(htmlspecialchars(trim($formDataArray['quantite'])));
    $montant             = strip_tags(htmlspecialchars(trim($formDataArray['montant'])));
    $id_produit_commande = strip_tags(htmlspecialchars(trim( crypt_decrypt_chaine($formDataArray['id_produit_commande'], 'D') )));    


    $query = "SELECT id_facture, statut_livraison  FROM commandes  WHERE id =".$_SESSION['commande']['id']; 
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
    $commande = mysqli_fetch_array($resultat); 
    if($commande['statut_livraison'] != 'livré')
    {
        $query = "UPDATE produits_commande 
                  SET prix_unitaire = ".empty_to_NULL($prix_unitaire).", 
                      quantite = ".empty_to_NULL($quantite).", 
                      montant = ".empty_to_NULL($montant).", 
                      id_utilisateur = '".$_SESSION['utilisateur']['id']."' 
                  WHERE id_fermes = ".$_SESSION['ferme']['id']." AND 
                        id=".$id_produit_commande;
        mysqli_query($bdd, $query) or die("Requête non conforme"); 
        echo 'succes';
    }
    else
    {
        echo 'echec';
    }
}


if(isset($_POST['actualiserStatutCommande']))
{
    $statut_commande  = strip_tags(htmlspecialchars(trim($_POST['statut_commande'])));
    $statut_livraison = strip_tags(htmlspecialchars(trim($_POST['statut_livraison'])));   

    if($statut_commande != 'supprimé')
    {
        $query = "SELECT statut_livraison 
                  FROM commandes 
                  WHERE id_fermes = ".$_SESSION['ferme']['id']." AND 
                        id =".$_SESSION['commande']['id'];
        $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
        $commande = mysqli_fetch_array($resultat);

        if($commande['statut_livraison'] != 'livré' AND $statut_livraison == 'livré')
        {
            decompteSortiePoulailler($bdd, $_SESSION['commande']['id'], '+'); 
        }

        if($commande['statut_livraison'] == 'livré' AND $statut_livraison != 'livré')
        {
            decompteSortiePoulailler($bdd, $_SESSION['commande']['id'], '-');  
        }


        $query = "UPDATE commandes 
                  SET statut_commande = '".$statut_commande."', 
                      statut_livraison = '".$statut_livraison."' 
                  WHERE id_fermes = ".$_SESSION['ferme']['id']." AND 
                        id =".$_SESSION['commande']['id']; 
        mysqli_query($bdd, $query) or die("Requête non conforme"); 
        echo 'succes'; 
    }
    else
    {
        $id_commande = $_SESSION['commande']['id'];  

        decompteSortiePoulailler($bdd, $_SESSION['commande']['id'], '+'); 

        $query = "DELETE commandes.*, produits_commande.*, factures.*, journal_caisse.* 
                  FROM commandes 
                  LEFT JOIN produits_commande ON produits_commande.id_commande = commandes.id 
                  LEFT JOIN factures ON factures.id = commandes.id_facture  
                  LEFT JOIN journal_caisse ON journal_caisse.id_type_element = commandes.id_facture AND 
                            journal_caisse.type_element = 'facture_vente' 
                  WHERE commandes.id_fermes = ".$_SESSION['ferme']['id']." AND 
                        commandes.statut_livraison != 'livré' AND 
                        commandes.id =".$_SESSION['commande']['id'];
        mysqli_query($bdd, $query) or die("Requête non conforme");  
        echo 'supprimé';
    }
}



if(isset($_POST['infosPaiementCommande']))
{
  $id_commande = strip_tags(htmlspecialchars(trim( crypt_decrypt_chaine($_POST['id_commande'], 'D') )));  

  $infosPay    = obtenirInfosPaiementCommande($bdd, $id_commande);

  echo "<table class='w-100'>
          <tr class='font-weight-bold'>
              <td class='p-1'>Sous Total</td>
              <td class='p-1 text-right'>".number_format($infosPay['sous_total'],0,'',' ')." F</td>
          </tr>
          <tr>
              <td class='p-1'>Remise</td>
              <td class='p-1 text-right'>".number_format($infosPay['remise'],0,'',' ')." F</td>
          </tr>
          <tr>
              <td class='p-1'>Frais de Livraison</td>
              <td class='p-1 text-right'>".number_format($infosPay['frais_livraison'],0,'',' ')." F</td>
          </tr>
          <tr class='font-weight-bold'>
              <td class='p-1'>Total à Payer</td>
              <td class='p-1 text-right'>".number_format($infosPay['total_a_payer'],0,'',' ')." F</td>
          </tr>
          <tr>
              <td class='p-1'>Montant Réçu</td>
              <td class='p-1 text-right'>".number_format($infosPay['montant_recu'],0,'',' ')." F</td>
          </tr>
          <tr>
              <td class='p-1'>Montant Rendu</td>
              <td class='p-1 text-right'>".number_format($infosPay['montant_rendu'],0,'',' ')." F</td>
          </tr>
          <tr class='font-weight-bold'>
              <td class='p-1'>Reste à Payer</td>
              <td class='p-1 text-right'>".number_format($infosPay['reste_a_payer'],0,'',' ')." F</td>
          </tr>
          <tr>
              <td class='p-1'>Statut Commande</td>  
              <td class='p-1 pr-1 text-right font-weight-bold'>".safe_safe_ucfirst($infosPay['statut_livraison'])."</td>
          </tr>
          <tr>
              <td class='p-1'>Statut Paiement</td>
              <td class='p-1 pr-1 text-right font-weight-bold'>".safe_safe_ucfirst($infosPay['statut_paiement'])."</td>  
          </tr>
      </table>"; 
}



if(isset($_POST['listePaiementCommande']))
{
    $id_commande = strip_tags(htmlspecialchars(trim( crypt_decrypt_chaine($_POST['id_commande'], 'D') )));

    $query = "SELECT id, id_type_element, montant_recu, date_format(date_montant, '%d/%m/%Y %Hh%imin') AS date_montant  
              FROM journal_caisse 
              WHERE id_fermes = ".$_SESSION['ferme']['id']." AND 
                    type_element = 'facture_vente' AND 
                    id_type_element = (SELECT id_facture FROM commandes WHERE id = ".$id_commande.") 
              ORDER BY journal_caisse.date_montant, journal_caisse.id";
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
    while($journalCaisse = mysqli_fetch_array($resultat))
    {
        echo "<div class='border rounded p-1 mb-1' style='position: relative;'>
                  <div style='padding-right: 40px;'>
                      <span>Reçu: <b><span class='span_aff_prix_monnaie' style='white-space: nowrap;'>".$journalCaisse['montant_recu']." F</span></b></span>
                  </div> 
                  <div>Date: <b>".$journalCaisse['date_montant']."</b></div> 

                  <button type='button' class='btn btn-sm btn-icon btn-secondary' style='position: absolute; top: 11px; right: 5px;'   
                      id_journal_caisse='".crypt_decrypt_chaine($journalCaisse['id'], 'C')."' 
                      id_facture='".crypt_decrypt_chaine($journalCaisse['id_type_element'], 'C')."' onclick='supprPaiementCommande(this)'>
                      <i class='far fa-trash-alt'></i>
                  </button>
              </div>";
    } 
}



if(isset($_POST['ajouterPaiementCommande']))  
{
    parse_str($_POST['formData'], $formDataArray);

    $id_commande       = strip_tags(htmlspecialchars(trim( crypt_decrypt_chaine($formDataArray['id_commande'], 'D') )));
    $montant_recu      = strip_tags(htmlspecialchars(trim($formDataArray['montant_recu'])));
    $date_montant_recu = strip_tags(htmlspecialchars(trim($formDataArray['date_montant_recu']))); 

    //Vérif id_facture dans table commandes
    $query = "SELECT id FROM factures WHERE id = (SELECT id_facture FROM commandes WHERE id = ".$id_commande.")";
    $resultat   = mysqli_query($bdd, $query) or die("Requête non conforme");  
    $facture    = mysqli_fetch_array($resultat);
    if(empty($facture['id'])){$id_facture = '';}else{$id_facture = $facture['id'];}   

    if(empty($id_facture))
    {
        $facture   = annee_ordre_nFacture_AV($bdd, $_SESSION['ferme']['id'], 'facture_vente'); 
        $nc_annee  = $facture[0];
        $nc_ordre  = $facture[1];
        $n_facture = $facture[2];

        //Creons nouvelle facture
        $query = "INSERT INTO factures (id_fermes,
                                        date_facture,
                                        nc_annee, 
                                        nc_ordre, 
                                        n_facture, 
                                        type, 
                                        id_utilisateur, 
                                        date_enreg) 
                                VALUES (".$_SESSION['ferme']['id'].", 
                                        \"$date_montant_recu\",
                                        \"$nc_annee\",
                                        \"$nc_ordre\",
                                        \"$n_facture\",
                                        'facture_vente', 
                                        ".$_SESSION['utilisateur']['id'].", 
                                        '".date('Y-m-d H:i:s')."')";
        if(@mysqli_query($bdd, $query))
        {
            //INSERTION DANS JOURNAL_CAISSE

            $id_facture = LAST_INSERT_ID($bdd); 
            $query = "UPDATE commandes SET id_facture = ".$id_facture." WHERE id_fermes = ".$_SESSION['ferme']['id']." AND id=".$id_commande;
            mysqli_query($bdd, $query) or die("Requête non conforme");

            //Obtenons le montant sur le montant_recu
            $infosPay = obtenirInfosPaiementCommande($bdd, $id_commande);
            if($montant_recu >= $infosPay['reste_a_payer']){$montant = $infosPay['reste_a_payer'];}else{$montant = $montant_recu;}


            $caisse   = annee_ordre_npiece($bdd, $_SESSION['ferme']['id']);
            $nc_annee = $caisse[0]; 
            $nc_ordre = $caisse[1]; 
            $n_piece  = $caisse[2];

            $condition = "id=".$id_facture; 
            $facture = selectTable($bdd, 'n_facture', 'factures', $condition); 
            $libelle   = "Paiement vente n°Facture: ".$facture['n_facture']; 

            $query = "INSERT INTO journal_caisse (id_fermes,
                                                  id_utilisateur,
                                                  id_poulailler,
                                                  date_montant,
                                                  nc_annee,
                                                  nc_ordre,  
                                                  n_piece,
                                                  libelle,
                                                  montant_recu,
                                                  montant, 
                                                  type,
                                                  id_type_element, 
                                                  type_element, 
                                                  date_heure)
                                        VALUES  (".$_SESSION['ferme']['id'].",
                                                 ".$_SESSION['utilisateur']['id'].",
                                                 ".empty_to_NULL($_SESSION['commande']['id_poulailler']).", 
                                                 \"$date_montant_recu\", 
                                                 ".empty_to_NULL($nc_annee).",
                                                 ".empty_to_NULL($nc_ordre).",
                                                 ".empty_to_NULL($n_piece).",
                                                 \"$libelle\",
                                                 \"$montant_recu\",
                                                 \"$montant\",
                                                 'entrée',
                                                 \"$id_facture\",
                                                 'facture_vente', 
                                                 '".date('Y-m-d H:i:s')."')";
            mysqli_query($bdd, $query) or die("Requête non conforme3333");
        }
    }
    else
    {
        //INSERTION DANS JOURNAL_CAISSE


        //Obtenons le montant sur le montant_recu
        $infosPay = obtenirInfosPaiementCommande($bdd, $id_commande);
        if($montant_recu >= $infosPay['reste_a_payer']){$montant = $infosPay['reste_a_payer'];}else{$montant = $montant_recu;}

        $caisse   = annee_ordre_npiece($bdd, $_SESSION['ferme']['id']);
        $nc_annee = $caisse[0]; 
        $nc_ordre = $caisse[1]; 
        $n_piece  = $caisse[2];

        $condition = "id=".$id_facture; 
        $facture = selectTable($bdd, 'n_facture', 'factures', $condition); 
        $libelle   = "Vente effectuée n°Facture: ".$facture['n_facture']; 

        $query = "INSERT INTO journal_caisse (id_fermes,
                                              id_utilisateur,
                                              id_poulailler,
                                              date_montant,
                                              nc_annee,
                                              nc_ordre,  
                                              n_piece,
                                              libelle,
                                              montant_recu,
                                              montant, 
                                              type,
                                              id_type_element, 
                                              type_element, 
                                              date_heure)
                                    VALUES  (".$_SESSION['ferme']['id'].",
                                             ".$_SESSION['utilisateur']['id'].",
                                             ".empty_to_NULL($_SESSION['commande']['id_poulailler']).", 
                                             \"$date_montant_recu\", 
                                             ".empty_to_NULL($nc_annee).",
                                             ".empty_to_NULL($nc_ordre).",
                                             ".empty_to_NULL($n_piece).",
                                             \"$libelle\",
                                             \"$montant_recu\",
                                             \"$montant\",
                                             'entrée',
                                             \"$id_facture\",
                                             'facture_vente', 
                                             '".date('Y-m-d H:i:s')."')";
        mysqli_query($bdd, $query) or die("Requête non conforme3333");
    }
    echo 'succes';
}


if(isset($_POST['supprPaiementCommande']))
{
    $id_journal_caisse = strip_tags(htmlspecialchars(trim( crypt_decrypt_chaine($_POST['id_journal_caisse'], 'D') ))); 
    $id_facture = 0; 

    supprPaiementCommande($bdd, $id_journal_caisse, $id_facture);
}
?>


