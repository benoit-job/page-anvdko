<?php
session_start(); 
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");

function statutAgenda($statut)
{
    if($statut == 'en cours'){return "<span class='badge text-bg-primary'>en cours</span>";}
    elseif($statut == 'terminé'){return "<span class='badge text-bg-success'>terminé</span>";}
    else{return "";}
}

if (isset($_POST['liste_actualites'])) {
    $nbreactualite = strip_tags(htmlspecialchars(trim($_POST['nbreactualite'])));

    $query = "SELECT *, DATE_FORMAT(date_act, '%d/%m/%Y') AS date_actualite,
                     (SELECT nom FROM cat_actualites WHERE id = actualites.id_cat_actualite) AS categorie
              FROM actualites
              ORDER BY nom, id
              LIMIT ".$nbreactualite.", 20";
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
    $ligne = 0;
    while ($actualite = mysqli_fetch_array($resultat)) {
        echo "<tr>
                <td>
                    <input type='checkbox' class='checkboxIdTable' onchange='getSelectedCheckboxes()' value='".$actualite['id']."'>
                </td>
                <td>".++$ligne."</td>
                <td>".affImgAdmin('60px', '60px', $actualite['image'], '')."</td>
                <td class='text-truncate ps-3' style='white-space: normal; min-width: 200px; max-width: 250px;'  title='".safe_safe_ucfirst($actualite["nom"])."'> 
                    <span class='line-clamp-3'>".safe_safe_ucfirst($actualite['nom'])."</span> 
                </td>
                <td> 
                    <span class='line-clamp-3'>".safe_safe_ucfirst($actualite['categorie'])."</span> 
                </td>
                <td  class='date-actualite'> 
                    <span class='line-clamp-3'>".safe_safe_ucfirst($actualite['date_actualite'])."</span> 
                </td>
                <td>
                    <span class='badge' style='background-color: #2c2664; color: white; padding: 0.5em 0.75em; font-size: 0.875em; border-radius: 0.5rem; display: inline-block; line-height: 1.2; max-height: 4.2em; overflow: hidden; text-overflow: ellipsis;'>
                        ".safe_safe_ucfirst($actualite['reference'])."
                    </span>
                </td>
                <td class='text-end'>
                    <form method='post' action='actualites.php'>
                        <a href='actualite_details.php?id_actualite=".crypt_decrypt_chaine($actualite['id'], 'C')."' class='btn btn-light btn-sm'>🔍 Voirs</a>
                        <button type='submit' name='supprimeractualite' class='btn btn-light supprimer btn-sm' onclick=\"return confirm('Voulez-vous supprimer ?')\">
                            <i class='fas fa-trash-alt'></i>
                        </button>
                        <input type='hidden' name='id_actualite' value='".crypt_decrypt_chaine($actualite['id'], 'C')."'>
                    </form>
                </td>
              </tr>"; 
    }
}

if (isset($_POST['liste_agendas'])) {
    $nbreagenda = strip_tags(htmlspecialchars(trim($_POST['nbreagenda'])));

    $query = "SELECT *, DATE_FORMAT(date_agenda, '%d/%m/%Y') AS date_agenda,
                     (SELECT nom FROM cat_actualites WHERE id = agenda.id_cat_agenda) AS categorie
              FROM agenda
              ORDER BY nom, id
              LIMIT ".$nbreagenda.", 20";
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
    $ligne = 0;
    while ($agenda = mysqli_fetch_array($resultat)) {
        echo "<tr>
                <td>
                    <input type='checkbox' class='checkboxIdTable' onchange='getSelectedCheckboxes()' value='".$agenda['id']."'>
                </td>
                <td>".++$ligne."</td>
                <td>".affImgAdmin('60px', '60px', $agenda['image'], '')."</td>
                <td class='text-truncate ps-3' style='white-space: normal; min-width: 200px; max-width: 250px;'  title='".safe_safe_ucfirst($agenda["nom"])."'> 
                    <span class='line-clamp-3'>".safe_safe_ucfirst($agenda['nom'])."</span> 
                </td>
                <td> 
                    <span class='line-clamp-3'>".safe_safe_ucfirst($agenda['categorie'])."</span> 
                </td>
                <td  class='date-agenda'> 
                    <span class='line-clamp-3'>".safe_safe_ucfirst($agenda['date_agenda'])."</span> 
                </td>
                <td>
                    <span class='badge' style='background-color: #2c2664; color: white; padding: 0.5em 0.75em; font-size: 0.875em; border-radius: 0.5rem; display: inline-block; line-height: 1.2; max-height: 4.2em; overflow: hidden; text-overflow: ellipsis;'>
                        ".safe_safe_ucfirst($agenda['n_agenda'])."
                    </span>
                </td>
                <td> 
                    <span class='line-clamp-3'>".statutAgenda($agenda['statut'])."</span> 
                </td>
                <td class='text-end'>
                    <form method='post' action='agenda.php'>
                        <a href='agenda_details.php?id_agenda=".crypt_decrypt_chaine($agenda['id'], 'C')."' class='btn btn-light btn-sm'>🔍 Voirs</a>
                        <button type='submit' name='supprimeragenda' class='btn btn-light supprimer btn-sm' onclick=\"return confirm('Voulez-vous supprimer ?')\">
                            <i class='fas fa-trash-alt'></i>
                        </button>
                        <input type='hidden' name='id_agenda' value='".crypt_decrypt_chaine($agenda['id'], 'C')."'>
                    </form>
                </td>
              </tr>"; 
    }
}

if (isset($_POST['liste_evenements'])) {
    $nbreevenement = strip_tags(htmlspecialchars(trim($_POST['nbreevenement'])));

    $query = "SELECT *, DATE_FORMAT(date_debut, '%d/%m/%Y') AS date_debut,
                        DATE_FORMAT(date_fin, '%d/%m/%Y') AS date_fin,
                     (SELECT nom FROM event_cat WHERE id = evenements.id_cat_event) AS categorie
              FROM evenements
              ORDER BY titre, id
              LIMIT ".$nbreevenement.", 20";
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
    $ligne = 0;
    while ($evenement = mysqli_fetch_array($resultat)) {
        echo "<tr>
                <td>
                    <input type='checkbox' class='checkboxIdTable' onchange='getSelectedCheckboxes()' value='".$evenement['id']."'>
                </td>
                <td>".++$ligne."</td>
                <td class='text-truncate ps-3' style='white-space: normal; min-width: 200px; max-width: 250px;'  title='".safe_safe_ucfirst($evenement["titre"])."'> 
                    <span class='line-clamp-3'>".safe_safe_ucfirst($evenement['titre'])."</span> 
                </td>
                <td> 
                    <span class='line-clamp-3'>".safe_safe_ucfirst($evenement['categorie'])."</span> 
                </td>
                <td  class='date-debut'> 
                    <span class='line-clamp-3'>".safe_safe_ucfirst($evenement['date_debut'])."</span> 
                </td>
                <td  class='date-fin'> 
                    <span class='line-clamp-3'>".safe_safe_ucfirst($evenement['date_fin'])."</span> 
                </td>
                <td  class='date-fin'> 
                    <span class='line-clamp-3'>".safe_safe_ucfirst($evenement['lieu'])."</span> 
                </td>
                <td  class='date-fin'> 
                    <span class='line-clamp-3'>".safe_safe_ucfirst($evenement['places_max'])."</span> 
                </td>
                <td>
                    <span class='badge' style='background-color: #2c2664; color: white; padding: 0.5em 0.75em; font-size: 0.875em; border-radius: 0.5rem; display: inline-block; line-height: 1.2; max-height: 4.2em; overflow: hidden; text-overflow: ellipsis;'>
                        ".safe_safe_ucfirst($evenement['n_event'])."
                    </span>
                </td>
                <td class='text-end'>
                    <form method='post' action='evenements.php'>
                        <a href='evenements_detail.php?id_event=".crypt_decrypt_chaine($evenement['id'], 'C')."' class='btn btn-light btn-sm'>🔍 Voir</a>
                        <button type='submit' name='supprimerevenement' class='btn btn-light supprimer btn-sm' onclick=\"return confirm('Voulez-vous supprimer ?')\">
                            <i class='fas fa-trash-alt'></i>
                        </button>
                        <input type='hidden' name='id_evenement' value='".crypt_decrypt_chaine($evenement['id'], 'C')."'>
                    </form>
                </td>
              </tr>"; 
    }
}

if (isset($_POST['liste_tv'])) {
    $nbretv = strip_tags(htmlspecialchars(trim($_POST['nbretv'])));

    $query = "SELECT *, DATE_FORMAT(date_tv, '%d/%m/%Y') AS date_tv,
                     (SELECT nom FROM cat_actualites WHERE id = adci_tv.id_cat_tv) AS categorie
              FROM adci_tv
              ORDER BY lien_youtube, id
              LIMIT ".$nbretv.", 20";
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
    $ligne = 0;
    while ($tv = mysqli_fetch_array($resultat)) {
        echo "<tr>
                <td>
                    <input type='checkbox' class='checkboxIdTable' onchange='getSelectedCheckboxes()' value='".$tv['id']."'>
                </td>
                <td>".++$ligne."</td>
                <td>".affyoutubeAdmin('60px', '60px', $tv['lien_youtube'], '')."</td>
                <td class='text-truncate ps-3' style='white-space: normal; min-width: 200px; max-width: 250px;' title='".safe_safe_ucfirst($tv["lien_youtube"])."'> 
                    <div class='input-group flex-nowrap my-2'>
                        <span class='input-group-text'>
                            <a href='".$tv['lien_youtube']."' target='_blank' class='text-danger'>
                                <i class='fab fa-youtube fa-lg'></i>
                            </a>
                        </span>
                        <input type='text' class='form-control' placeholder='Lien' aria-label='Lien YouTube' value='" . $tv['lien_youtube'] . "' readonly />
                    </div> 
                </td>
                <td> 
                    <span class='line-clamp-3'>".safe_safe_ucfirst($tv['categorie'])."</span> 
                </td>
                <td class='date-tv'> 
                    <span class='line-clamp-3'>".safe_safe_ucfirst($tv['date_tv'])."</span> 
                </td>
                <td class='text-end'>
                    <form method='post' action='adci_tv.php'>
                        <a href='adci_tv_details.php?id_tv=".crypt_decrypt_chaine($tv['id'], 'C')."' class='btn btn-light btn-sm'>🔍 Voirs</a>
                        <button type='submit' name='supprimertv' class='btn btn-light supprimer btn-sm' onclick=\"return confirm('Voulez-vous supprimer ?')\">
                            <i class='fas fa-trash-alt'></i>
                        </button>
                        <input type='hidden' name='id_tv' value='".crypt_decrypt_chaine($tv['id'], 'C')."'>
                    </form>
                </td>
              </tr>"; 
    }
    
}

if (isset($_POST['liste_adherent'])) {
    $nbreadherent = strip_tags(htmlspecialchars(trim($_POST['nbreadherent'])));

    $query = "SELECT *, 
                    CONCAT( CASE WHEN genre = 'HOMME' THEN 'M' WHEN genre = 'FEMME' THEN 'Mme' 
                        WHEN genre = 'MADEMOISELLE' THEN 'Mlle' ELSE genre END, '. ', nom, ' ', prenom) AS nom_prenom 
                FROM membres
              ORDER BY  nom ASC
              LIMIT ".$nbreadherent.", 20";
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");  
    $ligne = 0;
    
    while ($membre = mysqli_fetch_array($resultat)) {

        echo "<tr>
                <td>
                  <input type='checkbox' class='checkboxIdTable' onchange='getSelectedCheckboxes()' value='".$membre['id']."' data-numero='".$membre['num_telephone']."'>
                </td>
                <td>".++$ligne."</td>
                <td>".affImgAdmin('60px', '60px', $membre['logo'], '')."</td>
                <td class='text-truncate ps-3' style='white-space: normal; min-width: 200px; max-width: 250px;'  title='".safe_safe_ucfirst($membre["nom_prenom"])."'> 
                    <span class='line-clamp-3'>".safe_safe_ucfirst($membre['nom_prenom'])."</span> 
                </td>
                <td> 
                    <span class='line-clamp-3'>".safe_safe_ucfirst($membre['num_telephone'])."</span> 
                </td>
                <td> 
                    <span class='line-clamp-3'>".safe_safe_ucfirst($membre['profession'])."</span> 
                </td>
                <td>
                    <span class='badge' style='background-color: #2c2664; color: white; padding: 0.5em 0.75em; font-size: 0.875em; border-radius: 0.5rem; display: inline-block; line-height: 1.2; max-height: 4.2em; overflow: hidden; text-overflow: ellipsis;'>
                        ".safe_safe_ucfirst($membre['num_adhesion'])."
                    </span>
                </td>
                <td class='text-end'>
                    <form method='post' action='adherents.php'>
                        <a href='adherent_details.php?id_membre=".crypt_decrypt_chaine($membre['id'], 'C')."' class='btn btn-light btn-sm'>🔍 Voirs</a>
                        <a href='paiements_membre.php?id_membre=".crypt_decrypt_chaine($membre['id'], 'C')."' class='btn btn-success btn-sm'>Paiements</a>
                        <a href='voir_badge.php.php?id_membre=".crypt_decrypt_chaine($membre['id'], 'C')."' class='btn btn-light btn-sm d-none'>Badge</a>
                        <button type='submit' name='supprimermembre' class='btn btn-light supprimer btn-sm' onclick=\"return confirm('Voulez-vous supprimer ?')\">
                            <i class='fas fa-trash-alt'></i>
                        </button>
                        <input type='hidden' name='id_membre' value='".crypt_decrypt_chaine($membre['id'], 'C')."'>
                    </form>
                </td>
              </tr>"; 
    }
}

if (isset($_POST['liste_cartes'])) {
    $nbreCarte = (int) $_POST['nbreCarte'];
    $statut_ad = isset($_POST['statut_ad']) ? $_POST['statut_ad'] : '';

    $query = "SELECT *, 
                    CONCAT(CASE 
                        WHEN genre = 'HOMME' THEN 'M' 
                        WHEN genre = 'FEMME' THEN 'Mme' 
                        WHEN genre = 'MADEMOISELLE' THEN 'Mlle' 
                        ELSE genre END, '. ', nom, ' ', prenom) AS nom_prenom 
              FROM membres 
              WHERE statut_ad = '".mysqli_real_escape_string($bdd, $statut_ad)."' 
              ORDER BY nom ASC 
              LIMIT $nbreCarte, 20";

    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");

    $ligne = $nbreCarte;
    while ($membre = mysqli_fetch_array($resultat)) {
        echo "<tr>
                <td>
                    <input type='checkbox' class='checkboxIdTableCarte' onchange='getSelectedCheckboxesCarte()' value='".$membre['id']."' data-numero='".$membre['num_telephone']."'>
                </td>
                <td>".++$ligne."</td>
                <td>".affImgAdmin('60px', '60px', $membre['logo'], '')."</td>
                <td class='text-truncate ps-3' style='white-space: normal; min-width: 200px; max-width: 250px;' title='".safe_safe_ucfirst($membre["nom_prenom"])."'>
                    <span class='line-clamp-3'>".safe_safe_ucfirst($membre['nom_prenom'])."</span>
                </td>
                <td>
                    <span class='badge' style='background-color: #2c2664; color: white; padding: 0.5em 0.75em; font-size: 0.875em; border-radius: 0.5rem; display: inline-block; line-height: 1.2; max-height: 4.2em; overflow: hidden; text-overflow: ellipsis;'>
                        ".safe_safe_ucfirst($membre['num_adhesion'])."
                    </span>
                </td>
                <td class='text-end'>
                    <form method='post' action='cartesAdherents.php'>
                        <a href='/membres/badge.php?id_membre=".crypt_decrypt_chaine($membre['id'], 'C')."' class='btn btn-warning btn-sm'>
                            <i class='bi bi-person-badge text-white'></i>Badge
                        </a>
                        <input type='hidden' name='id_membre' value='".crypt_decrypt_chaine($membre['id'], 'C')."'>
                    </form>
                </td>
              </tr>";
    }
}

if (isset($_POST['liste_dons'])) {
    $nbredon = strip_tags(htmlspecialchars(trim($_POST['nbredon'])));

    $query = "SELECT * FROM faire_don
              ORDER BY id DESC
              LIMIT $nbredon, 20";
    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");

    $ligne = 0;

    while ($faire_don = mysqli_fetch_array($resultat)) {
        echo "<tr>
                <td>".++$ligne."</td>
                <td>".safe_safe_ucfirst($faire_don['nom'])." ".safe_safe_ucfirst($faire_don['prenom'])."</td>
                <td>".$faire_don['telephone']."</td>
                <td>".$faire_don['email']."</td>
                <td>".$faire_don['nationnalite']."</td>
                <td>".$faire_don['montant']." FCFA</td>
                <td>".$faire_don['moyen_paiement']."</td>
                <td class='date-don'>".date("d/m/Y à H:i", strtotime($faire_don['date_heure']))."</td>
                <td class='text-end'>
                    <form method='post' action='adherents.php'>
                        <button type='submit' name='supprimerfaire_don' class='btn btn-light btn-sm supprimer' onclick=\"return confirm('Voulez-vous supprimer ce don ?')\">
                            <i class='fas fa-trash-alt'></i>
                        </button>
                        <input type='hidden' name='id_faire_don' value='".crypt_decrypt_chaine($faire_don['id'], 'C')."'>
                    </form>
                </td>
              </tr>";
    }
}


if(isset($_POST['actualiserStatutAdhesion'])) {
    $id_adhesion_statut = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST['id_adhesion_statut'], 'D'))));
    $statut_adhesion = strip_tags(htmlspecialchars(trim($_POST["statut_adhesion"]))); 

       $query = "UPDATE membres 
                  SET statut_ad = \"$statut_adhesion\",  
                      date_statut_ad = '".date('Y-m-d H:i')."' 
                  WHERE id='".$id_adhesion_statut."'"; 
        mysqli_query($bdd, $query) or die("Requête non conforme");

    echo $statut_adhesion;
}


if(isset($_POST['actualiserStatut'])) {
    $id_agenda_statut = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_POST['id_agenda_statut'], 'D'))));
    $statut_agenda = strip_tags(htmlspecialchars(trim($_POST["statut_agenda"]))); 

    if($statut_agenda != 'supprimé') {
        $query = "UPDATE agenda 
                  SET statut = \"$statut_agenda\",  
                      date_statut = '".date('Y-m-d H:i')."' 
                  WHERE id='".$id_agenda_statut."'"; 
        mysqli_query($bdd, $query) or die("Requête non conforme");
    } else {
        $query = "DELETE FROM agenda WHERE id = ".$id_agenda_statut;
        mysqli_query($bdd, $query) or die("Requête non conforme0101"); 
    }

    echo $statut_agenda;
}

?>
