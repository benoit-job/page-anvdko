<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");

function ProgressBarPeriodeGlobale($bdd, $id_membre, $moisDebut, $moisFin, $id_motif, $motif)
{
    $debut = new DateTime($moisDebut);
    $fin = new DateTime($moisFin);
    $fin->modify('first day of next month'); // pour inclure le mois de fin

    $total_paye = 0;
    $total_a_payer = 0;

    while ($debut < $fin) {
        $moisAnnee = $debut->format('Y-m');

        $sql = "SELECT ep.a_payer, ep.paye
                FROM exceptionnels_pay ep
                JOIN config_cotisations_exceptionnelles cce ON ep.id_motif = cce.id
                WHERE ep.id_membre = '".mysqli_real_escape_string($bdd, $id_membre)."'
                  AND ep.mois_payer = '$moisAnnee'
                  AND cce.id = '".intval($id_motif)."'
                LIMIT 1";

        $res = mysqli_query($bdd, $sql);
        $data = mysqli_fetch_assoc($res);

        if ($data) {
            $total_a_payer += floatval($data['a_payer']);
            $total_paye += floatval($data['paye']);
        }

        $debut->modify('+1 month');
    }

    $pourcentage = $total_a_payer > 0 ? round(($total_paye * 100) / $total_a_payer, 2) : 0;

    if ($pourcentage >= 100) {
        $class = "bg-success";
        $label = "100%";
    } elseif ($pourcentage > 0) {
        $class = "bg-warning text-white fw-bold";
        $label = "{$pourcentage}%";
    } else {
        $class = "bg-danger";
        $label = "Non payé";
    }

    $start = new DateTime($moisDebut);
    $end = new DateTime($moisFin);
    $interval = $start->diff($end);
    $moisCount = ($interval->y * 12 + $interval->m) + 1;

    $width = 70 * $moisCount;
    $mois = date('Y-m'); // mois actuel


    $tooltip = "data-bs-toggle='tooltip' title='Paiement global $motif ($moisDebut → $moisFin)'";

    // Récupérer le statut membre_bureau et le genre du membre
    $sql_membre = "SELECT membre_bureau, genre FROM membres WHERE id = '".intval($id_membre)."'";
    $res_membre = mysqli_query($bdd, $sql_membre);
    $membre_data = $res_membre ? mysqli_fetch_assoc($res_membre) : null;
    $is_membre_bureau = isset($membre_data['membre_bureau']) && $membre_data['membre_bureau'] == 1;
    $genre = isset($membre_data['genre']) ? $membre_data['genre'] : '';
    $is_femme = ($genre == 'FEMME' || $genre == 'MADEMOISELLE');

    // Récupérer les montants depuis la table config_cotisations_exceptionnelles
    $sql_montant = "SELECT montant_standard, montant_homme, montant_femme, montant_mademoiselle, montant_bureau FROM config_cotisations_exceptionnelles WHERE id = '".intval($id_motif)."'";
    $res_montant = mysqli_query($bdd, $sql_montant);
    $montant_data = $res_montant ? mysqli_fetch_assoc($res_montant) : null;
    $montant_standard = $montant_data && !is_null($montant_data['montant_standard']) ? round(floatval($montant_data['montant_standard'])) : null;
    $montant_homme = $montant_data && !is_null($montant_data['montant_homme']) ? round(floatval($montant_data['montant_homme'])) : null;
    $montant_femme = $montant_data && !is_null($montant_data['montant_femme']) ? round(floatval($montant_data['montant_femme'])) : null;
    $montant_mademoiselle = $montant_data && !is_null($montant_data['montant_mademoiselle']) ? round(floatval($montant_data['montant_mademoiselle'])) : null;
    $montant_bureau = $montant_data && !is_null($montant_data['montant_bureau']) ? round(floatval($montant_data['montant_bureau'])) : null;

    // Déterminer le montant à utiliser selon la priorité :
    // 1. Si membre du bureau ET montant_bureau défini → montant_bureau
    // 2. Si genre = HOMME ET montant_homme défini → montant_homme
    // 3. Si genre = FEMME ET montant_femme défini → montant_femme
    // 4. Si genre = MADEMOISELLE ET montant_mademoiselle défini → montant_mademoiselle
    // 5. Sinon → montant_standard (peut être null)
    $montant_a_utiliser = $montant_standard;
    if ($is_membre_bureau && $montant_bureau !== null) {
        $montant_a_utiliser = $montant_bureau;
    } elseif ($genre == 'HOMME' && $montant_homme !== null) {
        $montant_a_utiliser = $montant_homme;
    } elseif ($genre == 'FEMME' && $montant_femme !== null) {
        $montant_a_utiliser = $montant_femme;
    } elseif ($genre == 'MADEMOISELLE' && $montant_mademoiselle !== null) {
        $montant_a_utiliser = $montant_mademoiselle;
    }
    
    // Si aucun montant n'est trouvé, utiliser 0 par défaut
    if ($montant_a_utiliser === null) {
        $montant_a_utiliser = 0;
    }

    // Convertir les données en JSON pour les passer au JavaScript
    $data_attr = htmlspecialchars(json_encode([
        'id_membre' => $id_membre,
        'id_motif' => $id_motif,
        'mois_payer' => $mois,
        'montant_standard' => $montant_standard,
        'montant_homme' => $montant_homme,
        'montant_femme' => $montant_femme,
        'montant_mademoiselle' => $montant_mademoiselle,
        'montant_bureau' => $montant_bureau,
        'montant_a_payer' => $montant_a_utiliser,
        'is_membre_bureau' => $is_membre_bureau,
        'genre' => $genre,
        'motif' => $motif,
        'periode' => "$moisDebut → $moisFin"
    ]), ENT_QUOTES, 'UTF-8');

    return "<div class='progress inline-block aProgressBar' style='height:25px; width:{$width}px; margin:0 1px; cursor:pointer;' $tooltip 
            onclick='showPaymentDialog($data_attr)'>
        <div class='progress-bar rounded-3 d-flex justify-content-center align-items-center $class' 
             role='progressbar' style='width: {$pourcentage}%; font-size: 11px;' 
             aria-valuenow='{$pourcentage}' aria-valuemin='0' aria-valuemax='100'>
             <span>$label</span>
        </div>
    </div>";

}

// Définir année par défaut si non définie
if (empty($_SESSION['annee_exceptionnelle'])) {
    $_SESSION["annee_exceptionnelle"] = date('Y');
}

// Initialisation
$motifs = [];
$last_motif = null;

if (isset($_POST['submitFiltrage'])) {
    // Enregistrer l'année dans la session
    $_SESSION['annee_exceptionnelle'] = strip_tags(htmlspecialchars(trim($_POST["annee_exceptionnelle"])));

    // Enregistrer le motif sélectionné dans la session
    if (!empty($_POST['motif_exceptionnel'])) {
        $_SESSION['motif_exceptionnel'] = strip_tags(htmlspecialchars(trim($_POST["motif_exceptionnel"])));
    }
}

$annee = $_SESSION['annee_exceptionnelle'];
$sql = "SELECT DISTINCT motif 
        FROM config_cotisations_exceptionnelles WHERE YEAR(STR_TO_DATE(mois_debut, '%Y-%m')) = '$annee' ORDER BY motif";

$resMotifs = mysqli_query($bdd, $sql);
if ($resMotifs) {
    while ($row = mysqli_fetch_assoc($resMotifs)) {
        $motifs[] = $row['motif'];
    }
}

if (isset($_SESSION['motif_exceptionnel']) && in_array($_SESSION['motif_exceptionnel'], $motifs)) {
    $last_motif = $_SESSION['motif_exceptionnel'];
} elseif (!empty($motifs)) {
    $last_motif = end($motifs);
    $_SESSION['motif_exceptionnel'] = $last_motif;
}

// Récupérer les détails complets du motif sélectionné pour les passer au JS d'édition
$motif_details_json = "{}";
if ($last_motif) {
    $sqlDetails = "SELECT * FROM config_cotisations_exceptionnelles WHERE motif = '".mysqli_real_escape_string($bdd, $last_motif)."' LIMIT 1";
    $resDetails = mysqli_query($bdd, $sqlDetails);
    if ($details = mysqli_fetch_assoc($resDetails)) {
        $motif_details_json = json_encode($details);
    }
}
?>


<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANVDKO - Cotisations exceptionnelles</title>
    <?php include('includes/php/includes-css.php');?>
    
    <!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

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

/* Style pour les montants optionnels */
#montants_optionnels {
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.form-check-input:checked {
  background-color: #0d6efd;
  border-color: #0d6efd;
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
                    <h3 class="mb-2">Cotisations exceptionnelles</h3>
                    <h5 class="text-body-tertiary fw-semibold">Visualiser et gérer les cotisations exceptionnelles</h5>
                </div>

                <div class="page-section">
                    <div class="card card-fluid">
                        <div class="card-header p-2 border-0">
                            <form method="post" action="exceptionnels_pay.php">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">

                                    <!-- Bloc 1 : Année + Motif + Valider -->
                                    <div class="input-group" style="max-width: 300px;">
                                        <input type="number" name="annee_exceptionnelle" class="form-control"
                                            value="<?= htmlspecialchars($_SESSION['annee_exceptionnelle']) ?>">

                                        <select name="motif_exceptionnel" class="form-select">
                                            <?php foreach ($motifs as $motif): ?>
                                                <option value="<?= htmlspecialchars($motif) ?>"
                                                    <?= $motif == $last_motif ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($motif) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <button type="submit" name="submitFiltrage" class="btn btn-primary">
                                            Valider
                                        </button>
                                    </div>

                                    <!-- Bloc 2 : Plusieurs paiements -->
                                    <div class="text-center" id="btnSmsWrapper" style="display: none;">
                                        <button type="button" id="btnSmsMembre" class="btn btn-sm btn-phoenix-success" 
                                                onclick="showPaymentDialogMultiple(event)">
                                            <i class="fas fa-money-check-alt"></i> Plusieurs paiements
                                        </button>
                                    </div>

                                    <!-- Bloc 3 : Bouton + Recherche, qui doivent TOUJOURS rester ensemble -->
                                    <div class="d-flex align-items-center gap-2 flex-nowrap" style="overflow-x: auto;">
                                        
                                        <!-- Bouton Générer une cotisation -->
                                        <button type="button"
                                                class="btn btn-phoenix-primary flex-shrink-0"
                                                onclick="showCreateExceptionnel()">
                                            <i class="fas fa-plus"></i> Créer
                                        </button>

                                        <?php if ($last_motif): ?>
                                        <button type="button"
                                                class="btn btn-phoenix-warning flex-shrink-0"
                                                onclick="editExceptionnel()">
                                            <i class="fas fa-edit"></i> Modifier
                                        </button>
                                        <?php endif; ?>

                                        <!-- Recherche -->
                                        <div class="input-group flex-shrink-0" style="max-width: 200px;">
                                            <span class="input-group-text" id="basic-addon1">
                                                <i class="fas fa-search"></i>
                                            </span>
                                            <input type="text"
                                                class="form-control form-control-sm"
                                                onclick='rechercheInput(this)'
                                                placeholder="Rechercher...">
                                        </div>

                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover m-0" id='ma_table' style='white-space: nowrap;'>
                                <thead class="thead-light">
                                <tr>
                                    <th class='text-center'>
                                    <input type='checkbox' class="checkbox-style" id="checkboxTous" onclick="toggleAllCheckboxes(this)">                                    
                                    </th>
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
                    CONCAT( 
                        CASE 
                            WHEN genre = 'HOMME' THEN 'M' 
                            WHEN genre = 'FEMME' THEN 'Mme' 
                            WHEN genre = 'MADEMOISELLE' THEN 'Mlle' 
                            ELSE genre 
                        END, '. ', nom, ' ', prenom
                    ) AS nom_prenom 
                FROM membres ORDER BY nom ASC";

    $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");

    // Dans la boucle d'affichage des membres, modifier l'appel à ProgressBarFacturePay
    while ($membre = mysqli_fetch_array($resultat)) {
        echo "<tr>
            <td class='text-center py-3'>
                <input type='checkbox' class='checkboxIdTable checkbox-style' 
           data-id='".crypt_decrypt_chaine($membre['id'], 'C')."'
           data-nom='". htmlspecialchars($membre['nom_prenom'])."'>
            </td>
            <td style=' text-transform: uppercase; font-size: 13px; font-weight: bold;'>
            " . ucwords(strtolower($membre['nom_prenom'])) . "
        </td>";
        
            $queryPeriode = "SELECT mois_debut, mois_fin, id 
                FROM config_cotisations_exceptionnelles 
                WHERE motif = '".mysqli_real_escape_string($bdd, $last_motif ?? '')."' 
                LIMIT 1";

            $resPeriode = mysqli_query($bdd, $queryPeriode);
            $moisDebutIndex = 0;
            $moisFinIndex = 0;
            $id_motif = null;
            $moisDebutStr = "";

            if ($resPeriode && mysqli_num_rows($resPeriode) > 0) {
                $periode = mysqli_fetch_assoc($resPeriode);
                $moisDebut = DateTime::createFromFormat('Y-m', $periode['mois_debut']);
                $moisFin = DateTime::createFromFormat('Y-m', $periode['mois_fin']);
                $moisDebutIndex = intval($moisDebut->format('n'));
                $moisFinIndex = intval($moisFin->format('n'));
                $id_motif = $periode['id'];
                $moisDebutStr = $moisDebut->format('Y-m');
            }

            for ($i = 1; $i <= 12; $i++) {
                if ($i < $moisDebutIndex || $i > $moisFinIndex) {
                    echo "<td></td>";
                } elseif ($i == $moisDebutIndex) {
                    $colspan = $moisFinIndex - $moisDebutIndex + 1;
                    echo "<td class='p-0' colspan='$colspan'>"
                        . ProgressBarPeriodeGlobale($bdd, $membre['id'], $periode['mois_debut'], $periode['mois_fin'], $periode['id'], $last_motif)
                        . "</td>";
                    $i = $moisFinIndex; // on saute les mois couverts par la barre
                }
            }

            echo "<td></td>
        </tr>"; 
        
    }
?>
                                </tbody>
                                <tfoot>
    <tr>
        <!-- 12 colonnes fusionnées à gauche pour centrer le bouton dans la dernière -->
        <th colspan="14">
            <input type='checkbox' class="checkbox-style" id="checkboxTous" onclick="toggleAllCheckboxes(this)">
        </th>
        <th class="text-end">
            <a href='recap.php?id_motif=<?= crypt_decrypt_chaine($id_motif, 'C') ?>'
               class="btn btn-primary text-decoration-none"
               target='_blank' rel='noopener noreferrer'>
                Voir le récapitulatif
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

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    
    <script>
function showCreateExceptionnel() {
    // Obtenir l'année/mois courant pour les valeurs par défaut
    const now = new Date();
    const currentMonth = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');
    
    Swal.fire({
        title: 'Nouvelle cotisation exceptionnelle',
        html: `
            <div class="mb-3">
                <label class="form-label fw-bold">Motif</label>
                <input type="text" id="motif" class="form-control" placeholder="Ex: Décès, Mariage..." required>
                <small class="text-muted d-block mt-1">L'ajout d'un motif existant le mettra à jour automatiquement.</small>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Montant standard <span class="text-danger">*</span></label>
                <input type="number" id="montant_standard" class="form-control" placeholder="Montant pour tous les membres" step="0" min="0">
                <small class="text-muted">Ce montant sera appliqué à tous les membres par défaut</small>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="montants_differencies" onchange="toggleMontantsOptionnels()">
                    <label class="form-check-label fw-bold" for="montants_differencies">
                        Montants différenciés (optionnel)
                    </label>
                </div>
                <small class="text-muted">Cochez cette case pour définir des montants spécifiques par catégorie</small>
            </div>
            
            <div id="montants_optionnels" style="display: none;" class="border rounded p-3 bg-light">
                <h6 class="mb-3 text-primary"><i class="fas fa-info-circle"></i> Montants optionnels</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Montant Homme</label>
                        <input type="number" id="montant_homme" class="form-control" placeholder="Montant pour les hommes" step="0" min="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Montant Femme</label>
                        <input type="number" id="montant_femme" class="form-control" placeholder="Montant pour les femmes" step="0" min="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Montant Mademoiselle</label>
                        <input type="number" id="montant_mademoiselle" class="form-control" placeholder="Montant pour les mademoiselles" step="0" min="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Montant Membre du bureau</label>
                        <input type="number" id="montant_bureau" class="form-control" placeholder="Montant pour membres du bureau" step="0" min="0">
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Mois de début <span class="text-danger">*</span></label>
                    <input type="month" id="mois_debut" class="form-control" value="${currentMonth}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Mois de fin <span class="text-danger">*</span></label>
                    <input type="month" id="mois_fin" class="form-control" required>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Valider',
        cancelButtonText: 'Annuler',
        width: '600px',
        didOpen: () => {
            // Initialiser la fonction toggle
            window.toggleMontantsOptionnels = function() {
                const checkbox = document.getElementById('montants_differencies');
                const divOptionnels = document.getElementById('montants_optionnels');
                if (checkbox.checked) {
                    divOptionnels.style.display = 'block';
                } else {
                    divOptionnels.style.display = 'none';
                    // Réinitialiser les champs optionnels
                    document.getElementById('montant_homme').value = '';
                    document.getElementById('montant_femme').value = '';
                    document.getElementById('montant_mademoiselle').value = '';
                    document.getElementById('montant_bureau').value = '';
                }
            };
        },
        preConfirm: () => {
            const motif = document.getElementById('motif').value;
            const montantStandard = document.getElementById('montant_standard').value.trim();
            const moisDebut = document.getElementById('mois_debut').value;
            const moisFin = document.getElementById('mois_fin').value;
            const montantsDifferencies = document.getElementById('montants_differencies').checked;
            
            // Validation des champs obligatoires
            if (!motif || !moisDebut || !moisFin) {
                Swal.showValidationMessage('Veuillez remplir tous les champs obligatoires (Motif, Mois de début, Mois de fin)');
                return false;
            }
            
            if (moisDebut > moisFin) {
                Swal.showValidationMessage('La date de fin doit être après la date de début');
                return false;
            }
            
            // Vérifier qu'au moins un montant est défini
            const montantHomme = montantsDifferencies ? (document.getElementById('montant_homme').value.trim() || null) : null;
            const montantFemme = montantsDifferencies ? (document.getElementById('montant_femme').value.trim() || null) : null;
            const montantMademoiselle = montantsDifferencies ? (document.getElementById('montant_mademoiselle').value.trim() || null) : null;
            const montantBureau = montantsDifferencies ? (document.getElementById('montant_bureau').value.trim() || null) : null;
            
            const hasMontantStandard = montantStandard !== '';
            const hasMontantDiff = montantHomme !== null || montantFemme !== null || montantMademoiselle !== null || montantBureau !== null;
            
            if (!hasMontantStandard && !hasMontantDiff) {
                Swal.showValidationMessage('Veuillez définir au moins un montant (standard ou différencié)');
                return false;
            }
            
            return {
                motif: motif,
                montant_standard: montantStandard || null,
                montant_homme: montantHomme,
                montant_femme: montantFemme,
                montant_mademoiselle: montantMademoiselle,
                montant_bureau: montantBureau,
                mois_debut: moisDebut,
                mois_fin: moisFin
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Envoyer les données au serveur
            $.ajax({
                url: 'create_exceptionnel.php',
                method: 'POST',
                data: result.value,
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire('Succès!', res.message, 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Erreur', res.error || 'Erreur lors de l\'enregistrement', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erreur AJAX:', xhr.responseText);
                    let errorMsg = 'Erreur lors de l\'enregistrement';
                    try {
                        const res = JSON.parse(xhr.responseText);
                        errorMsg = res.error || errorMsg;
                    } catch(e) {
                        errorMsg = xhr.responseText || errorMsg;
                    }
                    Swal.fire('Erreur', errorMsg, 'error');
                }
            });
        }
    });
}

function editExceptionnel() {
    const details = <?= $motif_details_json ?>;
    if (!details || !details.motif) {
        Swal.fire('Erreur', 'Aucun détail trouvé pour ce motif', 'error');
        return;
    }
    
    showCreateExceptionnel();
    
    // Attendre que le modal soit affiché pour remplir les champs
    setTimeout(() => {
        document.getElementById('motif').value = details.motif;
        document.getElementById('motif').setAttribute('readonly', true); // Empêcher le changement de nom
        
        if (details.montant_standard !== null) document.getElementById('montant_standard').value = Math.round(details.montant_standard);
        
        const hasDiff = details.montant_homme !== null || details.montant_femme !== null || 
                        details.montant_mademoiselle !== null || details.montant_bureau !== null;
                        
        if (hasDiff) {
            document.getElementById('montants_differencies').checked = true;
            window.toggleMontantsOptionnels();
            
            if (details.montant_homme !== null) document.getElementById('montant_homme').value = Math.round(details.montant_homme);
            if (details.montant_femme !== null) document.getElementById('montant_femme').value = Math.round(details.montant_femme);
            if (details.montant_mademoiselle !== null) document.getElementById('montant_mademoiselle').value = Math.round(details.montant_mademoiselle);
            if (details.montant_bureau !== null) document.getElementById('montant_bureau').value = Math.round(details.montant_bureau);
        }
        
        document.getElementById('mois_debut').value = details.mois_debut;
        document.getElementById('mois_fin').value = details.mois_fin;
    }, 100);
}
</script>


<script>
    function showPaymentDialog(data) {
        
        // Formater la date pour l'affichage (ex: "Juillet 2025")
        const moisAnnee = new Date(data.mois_payer);
        const moisNom = moisAnnee.toLocaleString('fr-FR', { month: 'long' });
        const annee = moisAnnee.getFullYear();
        
        Swal.fire({
            title: `Paiement ${data.motif}`,
            html: `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Membre</label>
                        <input type="text" class="form-control" value="${data.nom_prenom || 'Membre ID: '+data.id_membre}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Période</label>
                        <input type="text" class="form-control" value="${data.periode}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Montant à payer</label>
                        <input type="number" id="montantAPayer" class="form-control" value="${data.montant_a_payer}" disabled>
                        ${(() => {
                            if (data.is_membre_bureau && data.montant_bureau !== null) return '<small class="text-muted d-block"><i class="fas fa-info-circle"></i> Montant bureau appliqué</small>';
                            if (data.genre === 'HOMME' && data.montant_homme !== null) return '<small class="text-muted d-block"><i class="fas fa-info-circle"></i> Montant homme appliqué</small>';
                            if (data.genre === 'FEMME' && data.montant_femme !== null) return '<small class="text-muted d-block"><i class="fas fa-info-circle"></i> Montant femme appliqué</small>';
                            if (data.genre === 'MADEMOISELLE' && data.montant_mademoiselle !== null) return '<small class="text-muted d-block"><i class="fas fa-info-circle"></i> Montant mademoiselle appliqué</small>';
                            return '<small class="text-muted d-block"><i class="fas fa-info-circle"></i> Montant standard appliqué</small>';
                        })()}
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date de paiement</label>
                        <input type="date" id="datePaiement" class="form-control" value="${new Date().toISOString().split('T')[0]}">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Montant payé</label>
                        <input type="number" id="montantPaye" class="form-control" placeholder="Entrez le montant payé">
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Valider le paiement',
            cancelButtonText: 'Annuler',
            preConfirm: () => {
                const montantPaye = parseFloat(document.getElementById('montantPaye').value);
                const datePaiement = document.getElementById('datePaiement').value;
                
                if (!montantPaye || montantPaye <= 0) {
                    Swal.showValidationMessage('Veuillez entrer un montant valide');
                    return false;
                }
                
                if (!datePaiement) {
                    Swal.showValidationMessage('Veuillez sélectionner une date de paiement');
                    return false;
                }
                
                return {
                    id_membre: data.id_membre,
                    id_motif: data.id_motif,
                    mois_payer: data.mois_payer,
                    a_payer: data.montant_a_payer,
                    paye: montantPaye,
                    reste: data.montant_a_payer - montantPaye,
                    date_paiement: datePaiement
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'get_montant.php',
                    method: 'POST',
                    dataType: 'json', 
                    data: result.value,
                    success: function(response) {
        if (response.success) {
            Swal.fire({
                title: 'Succès !',
                width: '300px', 
                padding: '1em',
                customClass: {
                    popup: 'small-swal'
                }
            }).then(() => location.reload());
        } 
    }


                });
            }
        });
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

</body>
</html>
<script>
    // Gestion des sélections
function toggleAllCheckboxes(source) {
    const checkboxes = document.querySelectorAll('.checkboxIdTable');
    checkboxes.forEach(checkbox => {
        checkbox.checked = source.checked;
    });
    updatePaymentButtons();
}

function getSelectedMembers() {
    const selected = [];
    document.querySelectorAll('.checkboxIdTable:checked').forEach(checkbox => {
        selected.push({
            id: checkbox.dataset.id,
            nom: checkbox.dataset.nom
        });
    });
    return selected;
}

function updatePaymentButtons() {
    const selected = getSelectedMembers();
    const hasSelection = selected.length > 0;

    // Desktop
    document.getElementById('btnSmsWrapper').style.display = hasSelection ? 'block' : 'none';
    
    // Mobile
    document.getElementById('btnSmsMobileWrapper').style.display = hasSelection ? 'block' : 'none';
}

// Écouteurs d'événements
document.querySelectorAll('.checkboxIdTable').forEach(checkbox => {
    checkbox.addEventListener('change', updatePaymentButtons);
});

async function showPaymentDialogMultiple(event) {
    if (event) event.preventDefault();

    const selectedMembers = getSelectedMembers();
    if (selectedMembers.length === 0) {
        Swal.fire('Aucun membre sélectionné', 'Veuillez sélectionner au moins un membre', 'warning');
        return;
    }

    // Récupérer les infos du premier motif
    const firstBar = document.querySelector('.aProgressBar');
    if (!firstBar) {
        Swal.fire('Erreur', 'Impossible de trouver les informations de paiement', 'error');
        return;
    }

    const motifData = JSON.parse(firstBar.getAttribute('onclick').match(/showPaymentDialog\((.*)\)/)[1]);
    
    // S'assurer que id_motif est présent
    if (!motifData.id_motif) {
        Swal.fire('Erreur', 'ID motif manquant', 'error');
        return;
    }

    const today = new Date().toISOString().split('T')[0];
    
    // Récupérer la période de la cotisation pour déterminer le mois_payer
    // Utiliser le mois de début par défaut, ou le mois actuel
    let moisPayerDefault = new Date().toISOString().slice(0, 7);
    if (motifData.periode) {
        // Extraire le mois de début de la période (format: "2025-11 → 2025-12")
        const match = motifData.periode.match(/(\d{4}-\d{2})/);
        if (match) {
            moisPayerDefault = match[1];
        }
    }

    // Construction du formulaire
    let html = `
        <div class="row mt-3">
            <div class="col-md-6 mb-3">
                <label class="form-label">Période</label>
                <input type="text" class="form-control" value="${motifData.periode}" disabled>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Montant standard (Hommes)</label>
                <input type="number" class="form-control" value="${motifData.montant_standard || 0}" disabled>
            </div>
            ${motifData.montant_femme !== null ? `
            <div class="col-md-6 mb-3">
                <label class="form-label">Montant femmes</label>
                <input type="number" class="form-control" value="${motifData.montant_femme}" disabled>
            </div>
            ` : ''}
            ${motifData.montant_bureau !== null ? `
            <div class="col-md-6 mb-3">
                <label class="form-label">Montant bureau</label>
                <input type="number" class="form-control" value="${motifData.montant_bureau}" disabled>
            </div>
            ` : ''}
        </div>
        <hr>
        <h5>Membres sélectionnés (${selectedMembers.length})</h5>
        <div class="alert alert-info">
            <small><i class="fas fa-info-circle"></i> Le montant à payer sera calculé automatiquement pour chaque membre selon son genre et son statut.</small>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Montant payé (pour tous)</label>
                <input type="number" id="montantUnique" class="form-control" 
                       placeholder="Entrez le montant payé" min="0" step="0.01" required>
                <small class="text-muted">Ce montant sera appliqué à tous les membres sélectionnés</small>
            </div>
            <div class="col-md-6">
                <label>Date paiement</label>
                <input type="date" id="dateUnique" class="form-control" 
                       value="${today}" required>
            </div>
        </div>
    `;

    Swal.fire({
        title: `Paiement : ${motifData.motif}`,
        html: html,
        showCancelButton: true,
        confirmButtonText: 'Valider les paiements',
        cancelButtonText: 'Annuler',
        focusConfirm: false,
        preConfirm: async () => {
            const montant = parseFloat(document.getElementById('montantUnique').value);
            const datePaiement = document.getElementById('dateUnique').value;
            const moisPayer = moisPayerDefault; // Utiliser le mois de début de la période

            if (!montant || montant <= 0 || !datePaiement) {
                Swal.showValidationMessage('Veuillez remplir correctement tous les champs');
                return false;
            }

            // Récupérer les informations des membres pour déterminer leur montant
            const ids_membres = selectedMembers.map(m => m.id).join(',');
            
            try {
                const response = await fetch('get_multiple_membres_info.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `ids=${encodeURIComponent(ids_membres)}&id_motif=${motifData.id_motif}`
                });
                
                const data = await response.json();
                
                if (data.success && data.membres) {
                    const paiements = data.membres.map(membre => {
                        // S'assurer que le montant à payer est bien défini et non null
                        let montantAPayer = membre.montant_a_payer;
                        
                        // Si montant_a_payer est null, undefined, ou 0, essayer de trouver un montant
                        if (!montantAPayer || montantAPayer === 0 || montantAPayer === null || montantAPayer === 'null') {
                            // Essayer selon le genre et le statut
                            if (membre.is_membre_bureau && membre.montant_bureau && membre.montant_bureau > 0) {
                                montantAPayer = membre.montant_bureau;
                            } else if (membre.genre === 'HOMME' && membre.montant_homme && membre.montant_homme > 0) {
                                montantAPayer = membre.montant_homme;
                            } else if (membre.genre === 'FEMME' && membre.montant_femme && membre.montant_femme > 0) {
                                montantAPayer = membre.montant_femme;
                            } else if (membre.genre === 'MADEMOISELLE' && membre.montant_mademoiselle && membre.montant_mademoiselle > 0) {
                                montantAPayer = membre.montant_mademoiselle;
                            } else if (membre.montant_standard && membre.montant_standard > 0) {
                                montantAPayer = membre.montant_standard;
                            } else {
                                montantAPayer = 0;
                            }
                        }
                        
                        const montantFinal = parseFloat(montantAPayer) || 0;
                        
                        console.log('Membre:', membre.nom_prenom, 'ID:', membre.id_membre, 'Genre:', membre.genre, 'Bureau:', membre.is_membre_bureau);
                        console.log('  - Montants disponibles:', {
                            standard: membre.montant_standard,
                            homme: membre.montant_homme,
                            femme: membre.montant_femme,
                            mademoiselle: membre.montant_mademoiselle,
                            bureau: membre.montant_bureau,
                            calculé: membre.montant_a_payer,
                            final: montantFinal
                        });
                        
                        return {
                            id_membre: membre.id_membre,
                            id_motif: motifData.id_motif,
                            mois_payer: moisPayer,
                            a_payer: montantFinal,
                            paye: montant,
                            date_paiement: datePaiement
                        };
                    });
                    
                    // Vérifier qu'aucun paiement n'a un montant à payer de 0
                    const paiementsInvalides = paiements.filter(p => p.a_payer <= 0);
                    if (paiementsInvalides.length > 0) {
                        Swal.showValidationMessage(`${paiementsInvalides.length} membre(s) n'ont pas de montant à payer défini. Vérifiez la configuration de la cotisation.`);
                        return false;
                    }
                    
                    return paiements;
                }
            } catch (error) {
                console.error('Erreur lors de la récupération des infos membres:', error);
            }
            
            // Fallback: utiliser montant standard pour tous
            return selectedMembers.map(member => ({
                id_membre: member.id,
                id_motif: motifData.id_motif,
                mois_payer: moisPayer,
                a_payer: motifData.montant_standard || 0,
                paye: montant,
                date_paiement: datePaiement
            }));
        }
    }).then(result => {
        if (result.isConfirmed) {
            processMultiplePayments(result.value);
        }
    });
}



function processMultiplePayments(paiements) {
    // Afficher un indicateur de chargement
    Swal.fire({
        title: 'Traitement en cours',
        html: 'Veuillez patienter pendant le paiements...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Envoyer les données au serveur
    fetch('process_multiple_payments.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ paiements: paiements })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let message = `${data.count} paiements effectués`;
            if (data.errors && data.errors.length > 0) {
                message += `\n\n${data.errors.length} erreur(s) :\n${data.errors.join('\n')}`;
            }
            Swal.fire({
                title: 'Succès!',
                text: message,
                icon: data.errors && data.errors.length > 0 ? 'warning' : 'success'
            }).then(() => {
                location.reload(); // Recharger la page après succès
            });
        } else {
            let errorMsg = data.message || 'Une erreur est survenue';
            if (data.errors && data.errors.length > 0) {
                errorMsg += '\n\n' + data.errors.join('\n');
            }
            Swal.fire('Erreur', errorMsg, 'error');
        }
    })
    .catch(error => {
        Swal.fire('Erreur', 'Erreur de communication avec le serveur: ' + error, 'error');
    });
}
</script>
