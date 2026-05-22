<?php 

function createPathFile($path_save)
{
    $year  = date('Y');
    $month = date('m');
    if(is_dir($path_save."$year"))
    {
        if(is_dir($path_save."$year/$month")) 
        {
            $path_save = $path_save."$year/$month";
        }
        else 
        {
            mkdir($path_save."$year/$month"); //On crée le dossier mois
            $path_save = $path_save."$year/$month";
        }
    }
    else
    {
        mkdir($path_save."$year"); //On crée le dossier annee
        mkdir($path_save."$year/$month"); //On crée le dossier mois
        $path_save = $path_save."$year/$month";
    }
    return $path_save.'/';   
}

//Fonction upload file, peut être utiliser avec la fonction createPathFile()
//$chemin   = uploadFile(createPathFile(), $_FILES['photo_eleve_upload'], 'jpg,jpeg', 1000000);
function uploadFile($path, $fichier, $type_fichier, $taille)
{
    $infosfichier = pathinfo($fichier['name']);
    $ext_upload   = $infosfichier['extension'];
    if($fichier['size'] <= $taille AND (in_array($ext_upload, explode(',', $type_fichier))))
    {
        $chars      = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"; 
        $length     = rand(10, 16); 
        $nom_random = substr( str_shuffle(sha1(rand() . time()) . $chars ), 0, $length );

        $nom_fichier = $nom_random . '.' . $ext_upload;
        $chemin = $path.$nom_fichier;
        move_uploaded_file($fichier['tmp_name'], $chemin);//Enreg du fichier
        return $chemin;
    }
}

//Génère un mot de passe de 10 à 16 caractères;
function MotDePasse()
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"; 
    $length = rand(10, 16); 
    $password = substr( str_shuffle(sha1(rand() . time()) . $chars ), 0, $length );
     return $password;
}

// Génération d'une chaine aléatoire Ex: chaine_aleatoire(8)
function chaine_aleatoire($nb_car, $chaine = 'azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMXCVBN123456789')
{
    $nb_lettres = strlen($chaine) - 1;
    $generation = '';
    for($i=0; $i < $nb_car; $i++)
    {
        $pos = mt_rand(0, $nb_lettres);
        $car = $chaine[$pos];
        $generation .= $car;
    }
    return $generation;
}
function chaine_aleatoire_lettre($nb_car, $chaine = 'AZERTYUIOPQSDFGHJKLMXCVBN')
{
    $nb_lettres = strlen($chaine) - 1;
    $generation = '';
    for($i=0; $i < $nb_car; $i++)
    {
        $pos = mt_rand(0, $nb_lettres);
        $car = $chaine[$pos];
        $generation .= $car;
    }
    return $generation;
}
function chaine_aleatoire_chiffre($nb_car, $chaine = '0123456789')
{
    $nb_lettres = strlen($chaine) - 1;
    $generation = '';
    for($i=0; $i < $nb_car; $i++)
    {
        $pos = mt_rand(0, $nb_lettres);
        $car = $chaine[$pos];
        $generation .= $car;
    }
    return $generation;
}

//alert js avec php
    //window.location = \"$page_php\"; Encienne ligne
function alertJS($message, $reloadPage)
{
  if(trim($reloadPage) == '')
  {
    $reloadPage = basename($_SERVER['SCRIPT_NAME']);
  }
  echo "<script> 
    		if(!alert(\"$message\"))
    		{
    			window.location = '$reloadPage';
    		} 
    	</script>";
}

//génerer l'entete print des documents pour ecole
function headerPrint($headerTitle)
{
  echo "<table style='width:100%; border-bottom: 1px solid black; height: 79px;'> <tr><td style='width=80px;'><img style='float: left; position: relative; z-index: 99999; border: 1px solid #ccc;' width='75' height='75' src='".srcImage($_SESSION['ferme']['logo'])."' style='position:absolute; top:0; left:0;'/></td> <td style='text-align:center;'><h5 style='margin: 0 50px;'> ".$headerTitle." </h5></td> <td style='text-align:right;width=100px;'><h5><b>".$_SESSION['ferme']['nom']."</b><br>".date('d/m/Y')."</h5></td></tr></table>";
}

//fonction qui gère le dépassement de caractére et qui les remplace par de 3 points de  suspension
function break_words_points($myStr, $lenghtMax)
{
    if(strlen($myStr) > $lenghtMax)
    {
        return  ( mb_substr($myStr, 0, $lenghtMax-3 ).'...' );
    }
    else
    {
        return $myStr;
    }
}


//Convertir chiffre en lettre
function chiffre_en_lettre($montant, $devise1='', $devise2='')
{
    if(empty($devise1)) $dev1='';
    else $dev1=$devise1;
    if(empty($devise2)) $dev2='';
    else $dev2=$devise2;
    $valeur_entiere=intval($montant);
    $valeur_decimal=intval(round($montant-intval($montant), 2)*100);
    $dix_c=intval($valeur_decimal%100/10);
    $cent_c=intval($valeur_decimal%1000/100);
    $unite[1]=$valeur_entiere%10;
    $dix[1]=intval($valeur_entiere%100/10);
    $cent[1]=intval($valeur_entiere%1000/100);
    $unite[2]=intval($valeur_entiere%10000/1000);
    $dix[2]=intval($valeur_entiere%100000/10000);
    $cent[2]=intval($valeur_entiere%1000000/100000);
    $unite[3]=intval($valeur_entiere%10000000/1000000);
    $dix[3]=intval($valeur_entiere%100000000/10000000);
    $cent[3]=intval($valeur_entiere%1000000000/100000000);
    $chif=array('', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix sept', 'dix huit', 'dix neuf');
        $secon_c='';
        $trio_c='';
    for($i=1; $i<=3; $i++){
        $prim[$i]='';
        $secon[$i]='';
        $trio[$i]='';
        if($dix[$i]==0){
            $secon[$i]='';
            $prim[$i]=$chif[$unite[$i]];
        }
        else if($dix[$i]==1){
            $secon[$i]='';
            $prim[$i]=$chif[($unite[$i]+10)];
        }
        else if($dix[$i]==2){
            if($unite[$i]==1){
            $secon[$i]='vingt et';
            $prim[$i]=$chif[$unite[$i]];
            }
            else {
            $secon[$i]='vingt';
            $prim[$i]=$chif[$unite[$i]];
            }
        }
        else if($dix[$i]==3){
            if($unite[$i]==1){
            $secon[$i]='trente et';
            $prim[$i]=$chif[$unite[$i]];
            }
            else {
            $secon[$i]='trente';
            $prim[$i]=$chif[$unite[$i]];
            }
        }
        else if($dix[$i]==4){
            if($unite[$i]==1){
            $secon[$i]='quarante et';
            $prim[$i]=$chif[$unite[$i]];
            }
            else {
            $secon[$i]='quarante';
            $prim[$i]=$chif[$unite[$i]];
            }
        }
        else if($dix[$i]==5){
            if($unite[$i]==1){
            $secon[$i]='cinquante et';
            $prim[$i]=$chif[$unite[$i]];
            }
            else {
            $secon[$i]='cinquante';
            $prim[$i]=$chif[$unite[$i]];
            }
        }
        else if($dix[$i]==6){
            if($unite[$i]==1){
            $secon[$i]='soixante et';
            $prim[$i]=$chif[$unite[$i]];
            }
            else {
            $secon[$i]='soixante';
            $prim[$i]=$chif[$unite[$i]];
            }
        }
        else if($dix[$i]==7){
            if($unite[$i]==1){
            $secon[$i]='soixante et';
            $prim[$i]=$chif[$unite[$i]+10];
            }
            else {
            $secon[$i]='soixante';
            $prim[$i]=$chif[$unite[$i]+10];
            }
        }
        else if($dix[$i]==8){
            if($unite[$i]==1){
            $secon[$i]='quatre-vingts et';
            $prim[$i]=$chif[$unite[$i]];
            }
            else {
            $secon[$i]='quatre-vingt';
            $prim[$i]=$chif[$unite[$i]];
            }
        }
        else if($dix[$i]==9){
            if($unite[$i]==1){
            $secon[$i]='quatre-vingts et';
            $prim[$i]=$chif[$unite[$i]+10];
            }
            else {
            $secon[$i]='quatre-vingts';
            $prim[$i]=$chif[$unite[$i]+10];
            }
        }
        if($cent[$i]==1) $trio[$i]='cent';
        else if($cent[$i]!=0 || $cent[$i]!='') $trio[$i]=$chif[$cent[$i]] .' cents';
    }
     
     
    $chif2=array('', 'dix', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingts', 'quatre-vingts dix');
    $secon_c=$chif2[$dix_c];
    if($cent_c==1) $trio_c='cent';
    else if($cent_c!=0 || $cent_c!='') $trio_c=$chif[$cent_c] .' cents';
     
    if(($cent[3]==0 || $cent[3]=='') && ($dix[3]==0 || $dix[3]=='') && ($unite[3]==1))
        echo $trio[3]. '  ' .$secon[3]. ' ' . $prim[3]. ' million ';
    else if(($cent[3]!=0 && $cent[3]!='') || ($dix[3]!=0 && $dix[3]!='') || ($unite[3]!=0 && $unite[3]!=''))
        echo $trio[3]. ' ' .$secon[3]. ' ' . $prim[3]. ' millions ';
    else
        echo $trio[3]. ' ' .$secon[3]. ' ' . $prim[3];
     
    if(($cent[2]==0 || $cent[2]=='') && ($dix[2]==0 || $dix[2]=='') && ($unite[2]==1))
        echo ' mille ';
    else if(($cent[2]!=0 && $cent[2]!='') || ($dix[2]!=0 && $dix[2]!='') || ($unite[2]!=0 && $unite[2]!=''))
        echo $trio[2]. ' ' .$secon[2]. ' ' . $prim[2]. ' milles ';
    else
        echo $trio[2]. ' ' .$secon[2]. ' ' . $prim[2];
     
    echo $trio[1]. ' ' .$secon[1]. ' ' . $prim[1];
     
    echo ' '. $dev1 .' ' ;
     
    if(($cent_c=='0' || $cent_c=='') && ($dix_c=='0' || $dix_c==''))
        echo ' ';
    else
        echo $trio_c. ' ' .$secon_c. ' ' . $dev2;
}

//Nouvelle fonction affciche en lettre montant facture
function montantEnLettres($montant) {
  $unités = array("", "un", "deux", "trois", "quatre", "cinq", "six","sept", "huit", "neuf", "dix", "onze", "douze",
                  "treize", "quatorze", "quinze", "seize", "dix-sept","dix-huit", "dix-neuf");
  $dizaines = array("", "", "vingt", "trente", "quarante", "cinquante","soixante", "soixante-dix", "quatre-vingt", "quatre-vingt-dix");
  $centaines = array("", "cent", "deux cents", "trois cents", "quatre cents",
                     "cinq cents", "six cents", "sept cents", "huit cents","neuf cents");
  
  if ($montant == 0) {
    return "zéro franc";
  }
  
  if ($montant < 0) {
    return "moins ".montantEnLettres(abs($montant));
  }
  
  $resultat = "";
  if ($montant >= 1000) {
    $resultat .= montantEnLettres($montant / 1000). " mille ";
    $montant %= 1000;
  }
  if ($montant >= 100) {
    $resultat .= $centaines[$montant / 100]. " ";
    $montant %= 100;
  }
  if ($montant >= 20) {
    $resultat .= $dizaines[$montant / 10]. " ";
    $montant %= 10;
  }
  if ($montant > 0) {
    $resultat .= $unités[$montant]. " ";
  }
  
  return $resultat. "francs cfa";
}


//Fonction qui permet de dimunier les prenoms (idéa pour afficher les prenoms des badges)
function break_last_name($nom_complet, $lenghtMax)
{
    if(strlen($nom_complet) > $lenghtMax)
    {
        $nom_complet_Tab = explode(' ', $nom_complet); //On transforme la chaine en tableau
        $nom_complet_Tab = array_reverse($nom_complet_Tab); //On inverse le variable du tableau
        foreach ($nom_complet_Tab AS $key => $value)
        {
            $nom_complet_Tab[$key] = mb_substr($nom_complet_Tab[$key], 0, 1).'.'; //On obtient le premier cacactere
            if(strlen(implode($nom_complet_Tab)) <= $lenghtMax)
            {
                $nom_complet = implode(' ', array_reverse($nom_complet_Tab));
                break;
            }
        }
        return $nom_complet;
    }
    else
    {
        return $nom_complet;
    }
}

function espace_blanc($nbre_espace)
{
    $variable = '';
    for ($i=0; $i < $nbre_espace; $i++) { 
        $variable .= '&nbsp;';
    }
    return $variable;
}

function date_min_max_limit($nom_annee)
{
    if(count(explode('/', $nom_annee)) == 2)
    {
        $annee_min = explode('/', $nom_annee)['0'];
        $annee_max = explode('/', $nom_annee)['1'];
        $date_limit = "min='".$annee_min."-01-01' max='".$annee_max."-12-31'";
    }
    elseif(count(explode('-', $nom_annee)) == 2)
    {
        $annee_min = explode('-', $nom_annee)['0'];
        $annee_max = explode('-', $nom_annee)['1'];
        $date_limit = "min='".$annee_min."-01-01' max='".$annee_max."-12-31'";
    }
    else
    {
        $date_limit = "";
    }
    return $date_limit;
}

// Génère un numéro d'adhésion de type ANVDKO-2025-001
function generer_numero_adhesion($bdd)
{
    $annee = date('y'); // année sur 2 chiffres (ex: 25)

    // Sélectionner le plus grand ordre déjà existant pour l'année
    $query = "SELECT COALESCE(MAX(ev_ordre), 0) AS ev_ordre_max
              FROM membres
              WHERE ev_annee = '" . $annee . "'";

    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");
    $data = mysqli_fetch_array($resultat);

    $ev_ordre = $data['ev_ordre_max'] + 1;

    // Génère une référence de type : ANVDKO-25-001
    $reference = 'ANVDKO-' . $annee . '-' . str_pad($ev_ordre, 3, '0', STR_PAD_LEFT);

    return array($annee, $ev_ordre, $reference);
}



function annee_ordre_referenceagenda($bdd, $id_configuration)
{
    $annee = date('y'); // année sur 2 chiffres
    $query = "SELECT COALESCE(MAX(ev_ordre), 0) AS ev_ordre_max
              FROM agenda
              WHERE id_configuration = " . intval($id_configuration) . "
                AND ev_annee = '" . $annee . "'";

    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");
    $data = mysqli_fetch_array($resultat);

    $ev_ordre = $data['ev_ordre_max'] + 1;

    // Génère une référence de type : EV24000001
    $reference = 'EV' . $annee . str_pad($ev_ordre, 6, '0', STR_PAD_LEFT);

    return array($annee, $ev_ordre, $reference);
}

function referenceEvent($bdd, $id_configuration)
{
    $annee = date('y'); // année sur 2 chiffres
    $query = "SELECT COALESCE(MAX(ev_ordre), 0) AS ev_ordre_max
              FROM evenements
              WHERE id_configuration = " . intval($id_configuration) . "
                AND ev_annee = '" . $annee . "'";

    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");
    $data = mysqli_fetch_array($resultat);

    $ev_ordre = $data['ev_ordre_max'] + 1;

    // Génère une référence de type : EVT25001
    $reference = 'EVT' . $annee . str_pad($ev_ordre, 3, '0', STR_PAD_LEFT);

    return array($annee, $ev_ordre, $reference);
}

function annee_scolaire($nom_annee, $num)
{
   $nom_annee = str_replace('-', ',', str_replace('/', ',', $nom_annee)); 
   $annees = explode(',', $nom_annee);
   if($num == 1)
   {
       return $annees[0];
   }
   else
   {
       return $annees[1];
   }
}

function aller_page_action_effectuee($couleur, $message)
{
   echo "<script language='javascript'>document.location=\"action_effectuee.php?couleur=$couleur&message=$message\"</script>";
}

function ids_in_ids($valeur1, $valeur2)//Ex: ids_in_ids($_SESSION['utilisateur']['id_niv_enseignement'], $personnel['id_niv_enseignements'])
{
    $autorisation = 0;
    foreach (explode(',', $valeur1) as $value1)
    {
        if(in_array($value1, explode(',', $valeur2)))
        {
            $autorisation = 1;
            break;
        }
    }
    return $autorisation;
}

//Fonction qui traite les failles xxs des ids (401,458,785,215)
function ids_virgule_clean_xxs($ids_virgule)
{
   return str_replace('_', ',', strip_tags(htmlspecialchars(trim(str_replace(',', '_', $ids_virgule)))));
}

//Redimensionner une image
function redimensionner_image($chemin, $width, $height)
{
	list($width_img, $height_img, $type_img, $attr_img) = getimagesize($chemin);
	if($width_img < $width){$width = $width_img;}
	if($height_img < $height){$height = $height_img;}

    $source = imagecreatefromjpeg($chemin); // La photo est la source    
    $destination = imagecreatetruecolor($width, $height); // On crée la miniature vide
    // Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
    $largeur_source = imagesx($source);
    $hauteur_source = imagesy($source);
    $largeur_destination = imagesx($destination);
    $hauteur_destination = imagesy($destination);
    // On crée la miniature
    imagecopyresampled($destination, $source, 0, 0, 0, 0,$largeur_destination, $hauteur_destination, $largeur_source,$hauteur_source);
    // On enregistre la miniature sous le nom
    imagejpeg($destination, $chemin);
}

//Fonction pour enlever les failles pour ids (254,586,752,45)
function htmlspecialchars_ids($ids_separes_par_virgule)
{
    $ids_separes_par_virgule = str_replace(',', '_', $ids_separes_par_virgule);
    $ids_separes_par_virgule = strip_tags(htmlspecialchars(trim($ids_separes_par_virgule)));
    $ids_separes_par_virgule = str_replace('_', ',', $ids_separes_par_virgule);
    return $ids_separes_par_virgule;
}

//Fonction pour rendre un tableau de value en : 52,54,36 Idéal pour le select multiple
function id_select_multiple_en_ids($id_select_multiple)
{
    if(!empty($id_select_multiple) AND is_array($id_select_multiple))
    {
        $resultat = implode(',', $id_select_multiple);
        $resultat = str_replace(',', '_', $resultat);
        $resultat = strip_tags(htmlspecialchars(trim($resultat)));
        return str_replace('_', ',', $resultat);
    }
    else
    {
        return 0;
    }
}

//Fonction pour convetir le temps en seconde
function timeToSeconds($time)
{
     $timeExploded = explode(':', $time);
     if (isset($timeExploded[2])) {
         return $timeExploded[0] * 3600 + $timeExploded[1] * 60 + $timeExploded[2];
     }
     return $timeExploded[0] * 3600 + $timeExploded[1] * 60;
}

//fonction intersection ids_ niveau d'enseignement
function ids_intersection_ids($ids_1, $ids_2)
{
    $resultat = 0;
    foreach (explode(',', $ids_1) AS $id_1)
    {
        if(in_array($id_1, explode(',', $ids_2)))
        {
            $resultat = 1;
            break;
        }
    }
    return $resultat;
}

//Fonction pour convertir Ex:  é en e éàç en eac
function remove_accents($string)
{
    if ( !preg_match('/[\x80-\xff]/', $string) )
        return $string;

    $chars = array(
    // Decompositions for Latin-1 Supplement
    chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
    chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
    chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
    chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
    chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
    chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
    chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
    chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
    chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
    chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
    chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
    chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
    chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
    chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
    chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
    chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
    chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
    chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
    chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
    chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
    chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
    chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
    chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
    chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
    chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
    chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
    chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
    chr(195).chr(191) => 'y',
    // Decompositions for Latin Extended-A
    chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
    chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
    chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
    chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
    chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
    chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
    chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
    chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
    chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
    chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
    chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
    chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
    chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
    chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
    chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
    chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
    chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
    chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
    chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
    chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
    chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
    chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
    chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
    chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
    chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
    chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
    chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
    chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
    chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
    chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
    chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
    chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
    chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
    chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
    chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
    chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
    chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
    chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
    chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
    chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
    chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
    chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
    chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
    chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
    chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
    chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
    chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
    chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
    chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
    chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
    chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
    chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
    chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
    chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
    chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
    chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
    chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
    chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
    chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
    chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
    chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
    chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
    chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
    chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
    );

    $string = strtr($string, $chars);

    return $string;
}

//Génnérer nom utilisateur des élèves inscrits Ex: dag2415_2019_2020
function generer_username_eleve($nom, $nom_annee)
{
   $nom = remove_accents(strtolower(substr(str_replace("'", "", $nom), 0, 3)));
   $mot_de_passe = chaine_aleatoire(4, $chaine = '0123456789');
   $annee = str_replace('-', '_', str_replace('/', '_', $nom_annee));
   return $nom.$mot_de_passe.'_'.$annee;
}

//Conversion de la taille du fichier (Octets) en Ko ou Mo ou Go
function ConvertSizeOctets($bytes)
{
    if ($bytes >= 1073741824){$bytes = number_format($bytes / 1073741824, 2) . ' Go';}
    elseif ($bytes >= 1048576){$bytes = number_format($bytes / 1048576, 2) . ' Mo';}
    elseif ($bytes >= 1024){$bytes = number_format($bytes / 1024, 2) . ' Ko';}
    elseif ($bytes > 1){$bytes = $bytes . ' octets';}
    elseif ($bytes == 1){$bytes = $bytes . ' octet';}
    else{$bytes = '0 octet';}
    return $bytes;
}

//Fonction pour année scolaire âge de l'élève
function annee_scolaire_age()
{
   $annee_scolaire = explode('_', str_replace('-', '_', str_replace('/', '_', $_SESSION['annee_scolaire']['nom'])));
   if($annee_scolaire[0] == date('Y'))
   {
       return $annee_scolaire[0];
   }
   else
   {
       return $annee_scolaire[1];  
   }
}

//Fonction gestion envoi SMS WEB
function sms_web($senderID, $numero, $message)
{
    $numero  = str_replace(' ', '', $numero);
    $message = urlencode($message);

    $url = "https://rest.mojosms.com/api/single?senderID=".$senderID."&apiKey=LxEiJXtKHgNOJBrSk15Kr4tqTtx1&partnerID=AKZ9J-TT&message=".$message."&contact=".$numero;

    $json = implode('', file($url)); 
    $obj = json_decode($json);
    $resultat = $obj->{'success'};
    if($resultat)
    {
        return 'Message envoyé';
    }
    else
    {
        return 'Message non envoyé';
    }
}

//Convertit string en nombre d'octet (Idéal pour les sms) Ex: é = 2 octets
function string_en_nbre_octet($string)
{
    $characters = str_split($string);

    $binary = [];
    foreach ($characters as $character) {
        $data = unpack('H*', $character);
        $binary[] = base_convert($data[1], 16, 2);
    }

    return count($binary);    
}

function mois_fr($nbre_mois)
{   
    $nbre_mois = intval($nbre_mois);
    $mois_fr = Array("", "janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre");
    return $mois_fr[$nbre_mois];
}

function mois_annee_fr($mois)
{   
    return mois_fr(explode('-', $mois)[1]).' '.explode('-', $mois)[0];
}

function comp_2_val_retour($value1, $value2, $retour)
{
    if($value1 == $value2){return $retour;}else{return '';}
}

function selected_option($str1, $str2)
{
  if($str1 == $str2){return 'selected';}else{return '';}
}

function selected_return($str1, $str2, $return)
{
  if($str1 == $str2){return $return;}else{return '';}
}

function date_num_fr($date_debut, $date_fin)
{
    $date_debut = strtotime($date_debut);
    $date_debut = date("d/m/Y", $date_debut);
    
    $date_fin = strtotime($date_fin);
    $date_fin = date("d/m/Y", $date_fin);

    $resultat = '';
    if($date_debut == $date_fin)
    {
        $resultat = $date_debut;
    }
    else
    {
        $resultat = $date_debut.' - '.$date_fin;
    }
    return $resultat;
}

function reload_current_page()
{
    header("location: ".substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1));
}

function crypt_decrypt_chaine($string, $action)
{
    // you may change these values to your own
    $secret_key = 'HeNocH_key';
    $secret_iv = 'HeNocH_iv';

    if(!empty($string))
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if (!is_string($string)) {
            // Si $string n'est pas une chaîne, vous pouvez gérer l'erreur de votre choix ici.
            // Vous pouvez lancer une exception, enregistrer un message d'erreur, etc.
            return $output;
        }

        if ($action == 'C') {
            $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
        } elseif ($action == 'D') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
    }
    else
    {
        $output = ''; 
    }

    return $output;
}


//Calcul de pourcentage
function calcul_pourc($montant, $pourcentage)
{
    $montant = intval($montant);
    $resultat = ($montant * $pourcentage) / 100;
    return round($resultat, 2);
}

//Tranforme chaine vide en NULL pour les insertion en base de données
function empty_to_NULL($variable)
{
    $variable = strip_tags(htmlspecialchars(trim($variable)));
    if($variable==''){$variable='NULL';}else{$variable="\"".$variable."\"";}
    return $variable;
}

function href_controleur($href)
{
    if(str_contains($_SERVER['PHP_SELF'], '/admin/'))
    {
        $_SESSION['domaine']['nom'] = $_SESSION['domaine_admin']['nom'];
    }

    if(empty($href))
    {
        return get_cur_domaine().'/'.$_SESSION['domaine']['nom'];
    }
    else
    {
        return get_cur_domaine().'/'.$_SESSION['domaine']['nom'].'/'.$href;
    }
}

function get_numbers_in_str($str)
{

  $point = '';
  $result = ''; 
  $lignes = preg_split('/[\n\r]+/', $str);

  foreach($lignes as $str)
  {
    $str = $str.' ';
    
    for($i = 0, $len = strlen($str); $i < $len; $i++)
    {
        if($str[$i] == ':')
        {
          $point = $str[$i];
            continue;
        }
        
        if($point == ':')
        {
          if(is_numeric($str[$i]))
            {
              $result .= $str[$i];  
            }
            else
            {
              $point = '';
              if($result != '')
              {
                if(substr(trim($result), -1) != ' ')
                {
                  $result .= ' ';
                }
            }
              
            }   
        }   
    }
  }

  return trim($result);
}

function get_numerics($str)
{
    $str = preg_replace('/[\n\r]+/', ')', $str);
    $str = preg_replace('/\s+/', '', $str).' ';
    preg_match_all('/[:][0-9]+[ |)|\n\r]/', $str, $matches);
    $arrayResult = str_replace(':', '', $matches[0]);
    $result = implode(',', $arrayResult);
    preg_match_all('/[0-9]+/', $result, $matches);
    return implode(',', $matches[0]);
}

function aff_prix_monnaie($montant)
{
    if(trim($montant) != '')
    {
        if(str_contains($_SERVER['PHP_SELF'], '/admin/'))
        {
            $_SESSION['domaine']['position_monnaie'] = $_SESSION['domaine_admin']['position_monnaie'];
            $_SESSION['domaine']['monnaie']          = $_SESSION['domaine_admin']['monnaie'];
        }

        if($_SESSION['domaine']['position_monnaie'] == 'droit')
        {
            $resultat = $montant.' '.$_SESSION['domaine']['monnaie'];
        }
        else
        {
            $resultat = $_SESSION['domaine']['monnaie'].' '.$montant;
        }

        return "<span class='span_aff_prix_monnaie' style='white-space: nowrap;'>".$resultat."</span>";
    }
    else
    {
        return '';
    }
}

function prix_position_monnaie($montant, $position, $symbole)
{
    if($position == 'droit'){return $montant.' '.$symbole;}else{return $symbole.' '.$montant;}
}

//Affiche attribut produt commande
function badgeAttrs($attrs)
{
    $resul = '';
    foreach(explode(',', $attrs) as $attr)
    {
        $resul .= " <span class='badge text-dark bg_E3EBF1'>". str_replace(':', ' : ', $attr) ."</span> ";
    }
    return $resul; 
}


function statutLivraison($statut)
{
    if($statut == 'livré')
    {
        return "<span class='badge rounded-pill bg-success badge_statut_livraison_span'>livré</span>"; 
    }
    else
    {
        return "<span class='badge rounded-pill bg-danger badge_statut_livraison_span'>non livré</span>";
    }
}


function badgeStatut($statut)
{
    switch($statut)
    {
        case 'en attente':
          $result = "<span class='badge rounded-pill bg-warning badge_statut_span'>".$statut."</span>";
        break;
        case 'en cours':
          $result = "<span class='badge rounded-pill bg-primary badge_statut_span'>".$statut."</span>";
        break;
        case 'terminé':
          $result = "<span class='badge rounded-pill bg-success badge_statut_span'>".$statut."</span>";
        break;
        case 'annulé':
          $result = "<span class='badge rounded-pill bg-secondary badge_statut_span'>".$statut."</span>";
        break;
        default:
          $result = '';
        break;
    }
    return $result;
}

function etat_pay($totap_pay, $paye)
{
    if($paye >= $totap_pay){return "<span class='badge rounded-pill bg-success'>payé</span>";}
    elseif($paye > 0 && $paye < $totap_pay){return "<span class='badge rounded-pill bg-warning'>en partie</span>";}
    else{return "<span class='badge rounded-pill bg-danger'>non payé</span>";}
}

function LAST_INSERT_ID($bdd)
{
    $query = "SELECT LAST_INSERT_ID() AS dernier_id";
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
    $resultat = mysqli_fetch_array($resultat);
    return $resultat['dernier_id'];
}

function ids_crypt_decrypt($ids_crypt)
{
    $ids_decrypt = '';
    foreach(explode(',', $ids_crypt) AS $id_crypt)
    {
        if($ids_decrypt == '')
        {
            $ids_decrypt = crypt_decrypt_chaine($id_crypt, 'D');
        }
        else
        {
            $ids_decrypt .= ','.crypt_decrypt_chaine($id_crypt, 'D');
        }
    }

    if(str_contains($ids_decrypt, ','))
    {
        $ids_decrypt = explode(',', $ids_decrypt);
        sort($ids_decrypt);//On ordonne les id pour faciliter les recherches
        $ids_decrypt = implode(',', $ids_decrypt);
        return $ids_decrypt;
    }
    else{return $ids_decrypt;}
}


function selectTable($bdd, $alias, $table, $condition) 
{
    $query = "SELECT ".$alias." FROM ".$table." WHERE ".$condition;
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
    $resultatTable = mysqli_fetch_array($resultat);
    return $resultatTable;
}

function insertTable($bdd, $table, $col_insert, $donnees_insert)
{
    $col_insert     = implode(',', $col_insert);
    $donnees_insert = '"'.implode('","', $donnees_insert).'"';
    $query = "INSERT INTO ".$table." (".$col_insert.") VALUES (".$donnees_insert.")";
    return mysqli_query($bdd, $query) or die("Requête non conforme");
}

function updateTable($bdd, $table, $cols_update, $condition)
{
    $cols_update = implode(',', $cols_update);
    $query = "UPDATE ".$table." SET ".$cols_update." WHERE ".$condition;
    return mysqli_query($bdd, $query) or die("Requête non conforme");
}

function srcImage($fichier)
{ 
    return "https://laboutiquedubio.com/application/includes/img/all/".$fichier; 
}

function src_image_theme($fichier)
{ 
    if(trim($fichier) != '')
    {
        if(str_contains($fichier, 'video.png'))
        {
            return 'https://boomtik.net/fichiers/icons/video.png'; 
        }
        else
        {
            if(str_contains($fichier, 'https://')){return $fichier;}else{return 'https://boomtik.net/fichiers/uploads/'.$fichier;}
        }
    }
    else
    {
        return "https://boomtik.net/fichiers/images/no_image.png";
    }
}

function get_cur_domaine()
{
    return 'https://'.explode('/', $_SERVER['HTTP_HOST'], 3)[0];
}


function aff_attrs_vals($bdd, $ids_val_attr)
{
    $result = '';
    if(!empty($ids_val_attr))
    {
        $query = "SELECT ec_attributs.nom AS nom_attr, 
                         ec_attributs_valeurs.valeur AS val_attr
                  FROM ec_attributs 
                  INNER JOIN ec_attributs_valeurs ON ec_attributs_valeurs.id_attributs = ec_attributs.id
                  WHERE ec_attributs_valeurs.id IN (".$ids_val_attr.") 
                  ORDER BY ec_attributs.ordre, ec_attributs.id";
        $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
        while($attr = mysqli_fetch_array($resultat))
        {
            $result .= "<div style='white-space: nowrap; font-size: 10px;'>
                            <span>".$attr['nom_attr']."</span> : <b>".$attr['val_attr']."</b>
                        </div>";
        }
    }
    return $result;
}






function ids_decrypt($ids)
{
    if(str_contains($ids, ','))
    {
        $ids_decrypt = '';
        foreach(explode(',', $ids) AS $id)
        {
            if($ids_decrypt == '')
            {
                $ids_decrypt = strip_tags(htmlspecialchars(trim(  crypt_decrypt_chaine($id, 'D') )));
            }
            else
            {
                $ids_decrypt .= ','.strip_tags(htmlspecialchars(trim(  crypt_decrypt_chaine($id, 'D') )));
            }
        }
    }
    else
    {
        $ids_decrypt = strip_tags(htmlspecialchars(trim(  crypt_decrypt_chaine($ids, 'D') )));
    }
    return $ids_decrypt;
}


function annee_ordre_nColis($bdd)
{
    $annee = date('y');
    $query = "SELECT COALESCE(MAX(nc_ordre), 0) AS nc_ordre_max 
              FROM colis 
              WHERE nc_annee = '".$annee."'";
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
    $colis = mysqli_fetch_array($resultat);
    $nc_ordre = $colis['nc_ordre_max'] + 1;
    $n_piece = 'CL'.$annee.str_pad($nc_ordre,6,'0',STR_PAD_LEFT);

    return array($annee, $nc_ordre, $n_piece);
}


//Compresser une image 
/*
    $source_image      = 'eleve.jpeg';
    $compression_level = 75;
    $output_path       = 'eleve.jpeg';
    compress_image($source_image, $compression_level, $output_path); 
*/
function compress_image($source_image, $compression_level, $output_path)
{
    $info = getimagesize($source_image);
    if ($info['mime'] == 'image/jpeg') 
        $image = imagecreatefromjpeg($source_image);
    elseif ($info['mime'] == 'image/gif') 
        $image = imagecreatefromgif($source_image);
    elseif ($info['mime'] == 'image/png') 
        $image = imagecreatefrompng($source_image);
    imagejpeg($image, $output_path, $compression_level);
    return $output_path;
}

//Compresser une video Ex: compress_video('/path/to/video.mp4', 0.5, '/path/to/compressed_video.mp4');
function compress_video($input_video, $compression_rate, $output_video)
{
    // Construire la commande FFmpeg
    $ffmpeg_command = "ffmpeg -i $input_video -vcodec h264 -acodec aac -b:v ".$compression_rate."M -b:a 128k -y $output_video";
    // Exécuter la commande FFmpeg
    exec($ffmpeg_command);
}


function affiche_date($date_debut, $date_fin)
{
    //if($date_debut == $date_fin){return $date_debut;}
}



function checkNaN($val1, $val2, $val3)
{ 
    if(is_numeric($val3) && $val3 != 0)
    {
        return ($val1 * $val2) / $val3;
    }
    else
    {
        return 0;
    }
}

function QtesNbreDeSac($val1, $val2)
{ 
    if(is_numeric($val1) && is_numeric($val2) && $val2 != 0)
    {
        return round($val1 / $val2, 2);
    }
    else
    {
        return 0;
    }
}


function setEmptyVal($montant)
{ 
    if(is_numeric($montant))
    {
        return $montant;
    }
    else
    {
        return 0;
    }
}

function affyoutubeAdmin($width, $height, $lienYoutube, $class) 
{
    $videoId = '';

    if (strpos($lienYoutube, 'youtube.com') !== false) {
        parse_str(parse_url($lienYoutube, PHP_URL_QUERY), $params);
        $videoId = $params['v'] ?? '';
    } elseif (strpos($lienYoutube, 'youtu.be') !== false) {
        $path = parse_url($lienYoutube, PHP_URL_PATH);
        $videoId = trim($path, '/');
    }

    if (!empty($videoId)) {
        $image = "https://img.youtube.com/vi/$videoId/hqdefault.jpg";
        $youtubeUrl = "https://www.youtube.com/watch?v=$videoId";
        return "<a href='$youtubeUrl' target='_blank'>
                    <div class='border rounded $class' style='display: inline-block; width: $width; height: $height; background-position: center; background-size: cover; background-image: url($image);'></div>
                </a>";
    } else {
        $image = "https://anvdko.site//fichiers/images/no_image.jpg";
        return "<div class='border rounded $class' style='display: inline-block; width: $width; height: $height; background-position: center; background-size: cover; background-image: url($image);'></div>";
    }
}

function affIlectureVideo($width, $height, $lienYoutube, $class = '')
{
    // Extraire l'ID de la vidéo depuis différents formats de liens YouTube
    $videoId = '';

    if (preg_match('/youtu\.be\/([^\?&]+)/', $lienYoutube, $matches)) {
        $videoId = $matches[1];
    } elseif (preg_match('/youtube\.com.*v=([^\&]+)/', $lienYoutube, $matches)) {
        $videoId = $matches[1];
    }

    if (!empty($videoId)) {
        return "<div class='$class' style='width: $width; height: $height; overflow: hidden;'>
                    <iframe width='100%' height='100%' 
                            class='rounded shadow-sm border border-1' 
                            src='https://www.youtube.com/embed/$videoId' 
                            title='YouTube video player' 
                            frameborder='0' 
                            allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share' 
                            allowfullscreen>
                    </iframe>
                </div>";
    } else {
        return "<div class='$class' style='width: $width; height: $height; background: #f8f9fa; display: flex; align-items: center; justify-content: center;'>
                    <span>Vidéo non disponible</span>
                </div>";
    }
}

function affIlectureVideoSite($width, $height, $lienYoutube, $class = '')
{
    // Extraire l'ID de la vidéo depuis différents formats de liens YouTube
    $videoId = '';
    if (preg_match('/youtu\.be\/([^\?&]+)/', $lienYoutube, $matches)) {
        $videoId = $matches[1];
    } elseif (preg_match('/youtube\.com.*v=([^\&]+)/', $lienYoutube, $matches)) {
        $videoId = $matches[1];
    }

    if (!empty($videoId)) {
        // Générer l'URL de la miniature YouTube
        $thumbnailUrl = "https://img.youtube.com/vi/$videoId/0.jpg";

        return "
        <div class='col-md-6'>
            <div class='$class video-container' style='width: $width; height: $height; background-image: url($thumbnailUrl); background-size: cover; background-position: center; position: relative;'>
                <div class='play-button' onclick='playVideo(this, \"$lienYoutube\")' style='position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); cursor: pointer;'>
                    <img src='https://img.icons8.com/ios/50/ffffff/play.png' alt='Play' />
                </div>
                <iframe id='yt-video' frameborder='0' allow='autoplay; fullscreen' allowfullscreen style='display:none;width:100%;height:100%;'></iframe>
                <button class='exitBtn' onclick='exitFullscreen(this)' style='display:none; position:absolute; top:10px; right:10px; z-index:10;'>Retour</button>
            </div>
        </div>";
    } else {
        return "<div class='$class' style='width: $width; height: $height; background: #f8f9fa; display: flex; align-items: center; justify-content: center;'>
                    <span>Vidéo non disponible</span>
                </div>";
    }
}



function affImgAdmin($width, $height, $image, $class) 
{
      
    if(!empty($image))
    {
        $image = 'https://anvdko.site//fichiers/uploads/'.$image; 
    }
    else
    {
        $image = 'https://anvdko.site//fichiers/images/no_image.jpg'; 
    }

    return "<div class='border rounded ".$class."' style='display: inline-block; width: ".$width."; height: ".$height."; background-position: center; background-size: cover; background-image: url(".$image.");'></div>"; 
}

function getUrlFichier($fichier)
{
    $url_fichier = 'https://anvdko.site//fichiers/uploads/'.$fichier;

    if(strlen(@file_get_contents($url_fichier)) > 0) 
    {
        return $url_fichier;
    } 
    else 
    {
        return '../assets/img/LOGO.jpg';
    }
}
function getUrlUtilisateur($image)
{
    if (empty($image)) {
        return '../assets/img/LOGO.jpg';
    }
    $url_image = 'https://anvdko.site//fichiers/uploads/'.$image;

    if(strlen(@file_get_contents($url_image)) > 0) 
    {
        return $url_image;
    } 
    else 
    {
        return '../assets/img/LOGO.jpg';
    }
}


function setSrcImg($lienImage)
{
    if(!empty($lienImage))
    {
        return 'https://anvdko.site//fichiers/uploads/'.$lienImage; 
    } 
    else
    {
        return '../assets/img/LOGO.jpg';
    }
}

function AffImagSite($lienImage)
{
    if(!empty($lienImage))
    {
        return 'https://anvdko.site//fichiers/uploads/'.$lienImage; 
    } 
    else
    {
        return '../assets/img/LOGO.jpg';
    }
}
function safe_safe_ucfirst($value) {
    return $value !== null && $value !== '' ? ucfirst(trim($value)) : '';
}
?> 