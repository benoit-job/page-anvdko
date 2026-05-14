<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");
?>
<?php

?>

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Carte de Membres</title>

    <?php include('includes/php/includes-css.php');?>
    <style>
        .gap-2 {
    gap: 0.5rem;
}
    </style>

  </head>


  <body>

    <main class="main" id="top">
    	
      <?php include('includes/php/menu.php');?>

      <?php include('includes/php/header.php');?>

      <div class="content">

        
        <div class="mb-9">
          <div class="mx-n4 mx-lg-n6 mt-n5 position-relative mb-md-9" style="height:208px">
            <div class="bg-holder bg-card d-dark-none" style="background-image:url(assets/img/bg/bg-40.png);background-size:cover;">
            </div>
            <!--/.bg-holder-->

            <div class="bg-holder bg-card d-light-none" style="background-image:url(assets/img/bg/bg-dark-40.png);background-size:cover;">
            </div>
            <!--/.bg-holder-->

            <div class="faq-title-box position-relative bg-body-emphasis border border-translucent p-6 rounded-3 text-center mx-auto">
              <h3>Rechercher une carte</h3>
              <p class="my-3">Trouvez facilement une carte de membre</p>
              <div class="search-box w-100">
                <form class="position-relative" data-bs-toggle="search" data-bs-display="static">
                  <input class="form-control search-input search" id="input_recherche" type="search" placeholder="Rechercher un membre..." aria-label="Search" /><span class="fas fa-search search-box-icon"></span>
                </form>
              </div>
            </div>
          </div>

            <div class="page-section">
			  	<div class="card card-fluid">
                    <div class="card-header border-0 p-1">
                        <!-- Navigation par onglets -->
                        <nav>
                            <div class="nav nav-tabs justify-content-center" id="nav-tab" role="tablist">
                                <button class="nav-link active" id="nav-payes-tab" data-bs-toggle="tab" data-bs-target="#nav-payes" type="button" role="tab" aria-controls="nav-payes" aria-selected="true">
                                    <i class="fas fa-check-circle me-1"></i> Membres à jour
                                </button>
                                <button class="nav-link" id="nav-nonpayes-tab" data-bs-toggle="tab" data-bs-target="#nav-nonpayes" type="button" role="tab" aria-controls="nav-nonpayes" aria-selected="false">
                                    <i class="fas fa-exclamation-circle me-1"></i> Membres en retard
                                </button>
                            </div>
                            
                            <!-- Boutons alignés à droite sur la même ligne -->
                            <div class="d-flex justify-content-center mt-2 gap-2">
                                <button id="btnTelechargerCarte" class="btn btn-primary btn-sm" style="display: none;" onclick="telechargerCartesSelectionnees()">
                                    <i class="fas fa-download me-1"></i> Télécharger cartes
                                </button>
                                <button id="btnVoirCarte" class="btn btn-success btn-sm" style="display: none;" onclick="voirCartesSelectionnees()">
                                    <i class="fas fa-eye me-1"></i> Voir les cartes
                                </button>
                            </div>
                        </nav>

                    </div>
                    
                    <div class="tab-content" id="nav-tabContent">
                        <!-- Onglet Membres à jour -->
                        <div class="tab-pane fade show active" id="nav-payes" role="tabpanel" aria-labelledby="nav-payes-tab">
                            <div class="table-responsive">
                                <table class="table table-hover m-0" id='ma_table_payes'> 
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="checkboxTousPayes" onclick="toggleAllCheckboxesCarte(this, 'payes')"></th>
                                            <th>n°</th>
                                            <th></th>
                                            <th>Nom & prénoms</th>
                                            <th>N° Adhésion</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="listeadherentCarte"> 
                                        <tr id="spinner-payes">
                                            <td colspan="6" class="text-center">
                                                <div class="spinner-border spinner-border-sm" role="status">
                                                    <span class="visually-hidden">Chargement...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Onglet Membres en retard -->
                        <div class="tab-pane fade" id="nav-nonpayes" role="tabpanel" aria-labelledby="nav-nonpayes-tab">
                            <div class="table-responsive">
                                <table class="table table-hover m-0" id='ma_table_nonpayes'> 
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="checkboxTousNonPayes" onclick="toggleAllCheckboxesCarte(this, 'nonpayes')"></th>
                                            <th>n°</th>
                                            <th></th>
                                            <th>Nom & prénoms</th>
                                            <th>N° Adhésion</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="listeadherentNonPayes"> 
                                        <tr id="spinner-nonpayes">
                                            <td colspan="6" class="text-center">
                                                <div class="spinner-border spinner-border-sm" role="status">
                                                    <span class="visually-hidden">Chargement...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        


        <?php include('includes/php/footer.php');?>

      </div>
      
    </main>

    <?php include('includes/php/includes-js.php');?>

    <!-- SweetAlert2 (si pas encore inclus) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    
    <script>
        $(document).ready(async function () {
            var nbreadherentPayes = 0;
            var nbreadherentNonPayes = 0;

            while (await listeadherentCarte(nbreadherentPayes, 'Payé')) {
                nbreadherentPayes += 20;
            }

            $('#spinner-payes').remove();

            while (await listeadherentCarte(nbreadherentNonPayes, 'Non payé')) {
                nbreadherentNonPayes += 20;
            }

            $('#spinner-nonpayes').remove();
        });

    </script>

  </body>

</html>



<script>
  function listeadherentCarte(nbreadherent, statut) {
    return new Promise(function(resolve, reject) {
        $.ajax({
            url: 'ajax_autre.php',
            type: 'post',
            data: {
                nbreCarte: nbreadherent,
                liste_cartes: true,
                statut_ad: statut
            },
            dataType: 'html',
            success: function (data) {
                if (data.trim() !== '') {
                    if (statut === 'Payé') {
                        $('#listeadherentCarte').append(data);
                    } else {
                        $('#listeadherentNonPayes').append(data);
                    }
                    resolve(true);
                } else {
                    resolve(false);
                }
            }
        });
    });
}
</script>

<script>
function toggleAllCheckboxesCarte(source, type) {
    let checkboxes;
    if (type === 'payes') {
        checkboxes = document.querySelectorAll('#nav-payes .checkboxIdTableCarte');
    } else {
        checkboxes = document.querySelectorAll('#nav-nonpayes .checkboxIdTableCarte');
    }
    
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = source.checked;
    });

    getSelectedCheckboxesCarte();
}

function getSelectedCheckboxesCarte() {
    var selectedIds = [];
    var selectedNumeros = [];

    // Vérifie les deux onglets
    document.querySelectorAll('.checkboxIdTableCarte:checked').forEach(function(cb) {
        selectedIds.push(cb.value);
        selectedNumeros.push(cb.getAttribute('data-numero'));
    });

    var ids_membre = selectedIds.join(',');

    // Animation d'affichage des boutons
    if (ids_membre.trim() === '') {
        $('#btnTelechargerCarte').hide('fast');
        $('#btnVoirCarte').hide('fast');
    } else {
        $('#btnTelechargerCarte').show('fast');
        $('#btnVoirCarte').show('fast');
    }

    // Stocke les IDs sélectionnés
    $('#ModalCatPrincipale input[name="ids_membre"]').val(ids_membre);

    // Affiche les numéros sélectionnés
    document.getElementById('affichageIds').innerHTML =
        selectedNumeros.map(num => `<span class="badge bg-success me-1">${num}</span>`).join(', ');
}

</script>

<script>
 async function telechargerCartesSelectionnees() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Aucun membre sélectionné',
            text: 'Veuillez sélectionner au moins un membre avant de télécharger.'
        });
        return;
    }

    const btn = document.getElementById('btnTelechargerCarte');
    const originalBtnText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Téléchargement...';
    btn.disabled = true;

    Swal.fire({
        title: '<b><i>Préparation du téléchargement</i></b>',
        html: `
            <div id="progressText" style="font-size: 1.2em; font-weight: bold; font-style: italic; margin-bottom: 20px;">
                Chargement de la page...
            </div>
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
            <div id="progressBarContainer" style="display: none; margin-top: 20px;">
                <div class="progress" style="height: 30px;">
                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                        role="progressbar" style="width: 0%; font-size: 1.1em; font-weight: bold;">
                        0%
                    </div>
                </div>
                <div id="progressDetails" style="margin-top: 10px; font-size: 0.95em; color: #666;">
                    Traitement en cours...
                </div>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false
    });

    try {
        await genererToutesLesCartesPDF(selectedIds);
        
        Swal.close();
        
        Swal.fire({
            icon: 'success',
            title: 'Téléchargement terminé',
            text: `${selectedIds.length} carte(s) téléchargée(s) avec succès (recto + verso).`,
            confirmButtonText: 'OK'
        });
    } catch (error) {
        console.error("Erreur générale:", error);
        Swal.close();
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Une erreur est survenue lors du téléchargement des badges: ' + error.message
        });
    } finally {
        btn.innerHTML = originalBtnText;
        btn.disabled = false;
    }
}

function updateProgress(current, total, message = '') {
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const progressDetails = document.getElementById('progressDetails');
    const progressBarContainer = document.getElementById('progressBarContainer');
    
    if (progressBar && progressBarContainer) {
        const spinner = document.querySelector('.spinner-border');
        if (spinner) {
            spinner.style.display = 'none';
        }
        progressBarContainer.style.display = 'block';
        
        const percentage = Math.round((current / total) * 100);
        
        progressBar.style.width = percentage + '%';
        progressBar.textContent = percentage + '%';
        
        if (progressText) {
            progressText.innerHTML = `Téléchargement des badges (${current}/${total})`;
        }
        
        if (progressDetails && message) {
            progressDetails.textContent = message;
        }
    }
}

async function genererToutesLesCartesPDF(ids) {
    return new Promise((resolve, reject) => {
        const iframe = document.createElement("iframe");
        iframe.style.display = "block";
        iframe.style.position = "absolute";
        iframe.style.left = "-9999px";
        iframe.style.width = "1200px";
        iframe.style.height = "2000px";
        
        const idsEncoded = encodeURIComponent(ids.join(','));
        iframe.src = `voir_cartes.php?ids=${idsEncoded}&t=${Date.now()}`;
        document.body.appendChild(iframe);

        let timeoutId;

        iframe.onload = async () => {
            try {
                const progressText = document.getElementById('progressText');
                console.log("Iframe chargé");
                
                if (progressText) {
                    progressText.innerHTML = 'Chargement des badges...';
                }

                // Attendre le chargement complet (QR codes, images, etc.)
                await new Promise(resolve => setTimeout(resolve, 8000));
                
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                
                // Chercher tous les conteneurs de cartes
                let containers = iframeDoc.querySelectorAll('.member-card-pack');
                console.log("Nombre de cartes trouvées:", containers.length);
                
                if (containers.length === 0) {
                    throw new Error(`Aucune carte trouvée. Vérifiez que voir_cartes.php charge correctement.`);
                }

                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });

                let isFirstPage = true;
                let successCount = 0;
                const totalCards = containers.length;

                updateProgress(0, totalCards, 'Préparation de la capture...');

                for (let i = 0; i < containers.length; i++) {
                    const pack = containers[i];
                    
                    // CAPTURER LE RECTO
                    let imgDataRecto = null;
                    let canvasRectoWidth = 0;
                    let canvasRectoHeight = 0;
                    
                    try {
                        updateProgress(i, totalCards, `Capture du recto ${i + 1}/${totalCards}...`);

                        const recto = pack.querySelector('.badge');
                        if (!recto) {
                            console.warn(`Recto ${i + 1} introuvable`);
                            continue;
                        }
                        
                        // S'assurer que le recto est visible
                        pack.classList.remove('flipped');
                        
                        // Forcer l'affichage du recto
                        recto.style.display = 'block';
                        recto.style.visibility = 'visible';
                        recto.style.opacity = '1';
                        recto.style.position = 'relative';
                        recto.style.transform = 'none';
                        recto.style.backfaceVisibility = 'visible';
                        recto.style.zIndex = '10';
                        
                        // Masquer le verso pendant la capture du recto
                        const verso = pack.querySelector('.card-back');
                        if (verso) {
                            verso.style.display = 'none';
                            verso.style.visibility = 'hidden';
                            verso.style.zIndex = '1';
                        }
                        
                        // Masquer le bouton flip
                        const flipBtnRecto = recto.querySelector('.flip-button');
                        if (flipBtnRecto) {
                            flipBtnRecto.style.display = 'none';
                        }
                        
                        // Ajouter la classe capturing pour appliquer les styles CSS
                        recto.classList.add('capturing');
                        
                        // Masquer le logo en filigrane qui peut cacher le contenu
                        const bgLogoRecto = recto.querySelector('.background-logo');
                        if (bgLogoRecto) {
                            bgLogoRecto.style.display = 'none';
                            bgLogoRecto.style.opacity = '0';
                        }
                        
                        // Forcer la visibilité de tous les éléments du recto
                        const rectoElements = recto.querySelectorAll('*');
                        rectoElements.forEach(el => {
                            if (!el.classList.contains('flip-button') && !el.classList.contains('background-logo')) {
                                el.style.visibility = 'visible';
                                el.style.opacity = '1';
                            }
                        });
                        
                        // Attendre le rendu complet
                        await new Promise(resolve => setTimeout(resolve, 2000));

                        const canvasRecto = await html2canvas(recto, { 
                            scale: 4,
                            useCORS: true,
                            allowTaint: true,
                            logging: false,
                            backgroundColor: '#3a336e',
                            width: 500,
                            height: 310,
                            windowWidth: 1200,
                            windowHeight: 2000,
                            onclone: function(clonedDoc) {
                                const clonedRecto = clonedDoc.querySelector('.badge');
                                if (clonedRecto) {
                                    clonedRecto.style.transform = 'none';
                                    clonedRecto.style.backfaceVisibility = 'visible';
                                    clonedRecto.style.display = 'block';
                                    clonedRecto.style.visibility = 'visible';
                                    clonedRecto.style.position = 'relative';
                                    
                                    // Masquer le bouton dans le clone aussi
                                    const flipBtn = clonedRecto.querySelector('.flip-button');
                                    if (flipBtn) {
                                        flipBtn.style.display = 'none';
                                    }
                                    
                                    // Masquer le logo en filigrane dans le clone
                                    const bgLogo = clonedRecto.querySelector('.background-logo');
                                    if (bgLogo) {
                                        bgLogo.style.display = 'none';
                                        bgLogo.style.opacity = '0';
                                    }
                                    
                                    const allElements = clonedRecto.querySelectorAll('*');
                                    allElements.forEach(el => {
                                        if (!el.classList.contains('flip-button') && !el.classList.contains('background-logo')) {
                                            el.style.visibility = 'visible';
                                            el.style.opacity = '1';
                                        }
                                    });
                                }
                            }
                        });

                        if (canvasRecto.width > 0 && canvasRecto.height > 0) {
                            imgDataRecto = canvasRecto.toDataURL('image/png', 1.0);
                            canvasRectoWidth = canvasRecto.width;
                            canvasRectoHeight = canvasRecto.height;
                            console.log(`✓ Recto ${i + 1} capturé (${canvasRecto.width}x${canvasRecto.height})`);
                        }

                    } catch (err) {
                        console.error(`✗ Erreur recto ${i + 1}:`, err);
                    }

                    // CAPTURER LE VERSO
                    let imgDataVerso = null;
                    let canvasVersoWidth = 0;
                    let canvasVersoHeight = 0;
                    
                    try {
                        updateProgress(i, totalCards, `Capture du verso ${i + 1}/${totalCards}...`);

                        const verso = pack.querySelector('.card-back');
                        const recto = pack.querySelector('.badge');
                        
                        if (!verso) {
                            console.warn(`Verso ${i + 1} introuvable`);
                            continue;
                        }
                        
                        // Masquer le recto
                        if (recto) {
                            recto.style.display = 'none';
                            recto.style.visibility = 'hidden';
                            recto.style.zIndex = '1';
                        }
                        
                        // Afficher le verso
                        verso.style.display = 'block';
                        verso.style.visibility = 'visible';
                        verso.style.opacity = '1';
                        verso.style.position = 'absolute';
                        verso.style.transform = 'none';
                        verso.style.backfaceVisibility = 'visible';
                        verso.style.zIndex = '10';
                        
                        // Masquer le bouton flip du verso
                        const flipBtnVerso = verso.querySelector('.flip-button');
                        if (flipBtnVerso) {
                            flipBtnVerso.style.display = 'none';
                        }
                        
                        // Masquer le logo en filigrane du verso
                        const bgLogoVerso = verso.querySelector('.background-logo1');
                        if (bgLogoVerso) {
                            bgLogoVerso.style.display = 'none';
                        }
                        
                        // Forcer la visibilité de tous les éléments du verso
                        const versoElements = verso.querySelectorAll('*');
                        versoElements.forEach(el => {
                            if (!el.classList.contains('flip-button') && !el.classList.contains('background-logo1')) {
                                el.style.visibility = 'visible';
                                el.style.opacity = '1';
                            }
                        });
                        
                        // Attendre le rendu
                        await new Promise(resolve => setTimeout(resolve, 2000));

                        const canvasVerso = await html2canvas(verso, { 
                            scale: 4,
                            useCORS: true,
                            allowTaint: true,
                            logging: false,
                            backgroundColor: '#3a336e',
                            width: 500,
                            height: 310,
                            windowWidth: 1200,
                            windowHeight: 2000,
                            onclone: function(clonedDoc) {
                                const clonedVerso = clonedDoc.querySelector('.card-back');
                                if (clonedVerso) {
                                    clonedVerso.style.transform = 'none';
                                    clonedVerso.style.backfaceVisibility = 'visible';
                                    clonedVerso.style.display = 'block';
                                    clonedVerso.style.visibility = 'visible';
                                    clonedVerso.style.position = 'relative';
                                    
                                    // Masquer le bouton
                                    const flipBtn = clonedVerso.querySelector('.flip-button');
                                    if (flipBtn) {
                                        flipBtn.style.display = 'none';
                                    }
                                    
                                    // Masquer le logo en filigrane dans le clone du verso
                                    const bgLogo = clonedVerso.querySelector('.background-logo1');
                                    if (bgLogo) {
                                        bgLogo.style.display = 'none';
                                        bgLogo.style.opacity = '0';
                                    }
                                    
                                    const allElements = clonedVerso.querySelectorAll('*');
                                    allElements.forEach(el => {
                                        if (!el.classList.contains('flip-button') && !el.classList.contains('background-logo1')) {
                                            el.style.visibility = 'visible';
                                            el.style.opacity = '1';
                                        }
                                    });
                                }
                            }
                        });

                        if (canvasVerso.width > 0 && canvasVerso.height > 0) {
                            imgDataVerso = canvasVerso.toDataURL('image/png', 1.0);
                            canvasVersoWidth = canvasVerso.width;
                            canvasVersoHeight = canvasVerso.height;
                            console.log(`✓ Verso ${i + 1} capturé (${canvasVerso.width}x${canvasVerso.height})`);
                        }
                        
                        // Réafficher le recto pour la prochaine itération
                        if (recto) {
                            recto.style.display = 'block';
                            recto.style.visibility = 'visible';
                        }
                        verso.style.display = 'block';
                        verso.style.visibility = 'visible';

                    } catch (err) {
                        console.error(`✗ Erreur verso ${i + 1}:`, err);
                    }

                    // AJOUTER RECTO ET VERSO SUR LA MÊME PAGE
                    if (imgDataRecto && imgDataVerso) {
                        if (!isFirstPage) {
                            pdf.addPage();
                        }
                        isFirstPage = false;

                        const pageWidth = pdf.internal.pageSize.getWidth();
                        const pageHeight = pdf.internal.pageSize.getHeight();
                        
                        // Fond blanc
                        pdf.setFillColor(255, 255, 255);
                        pdf.rect(0, 0, pageWidth, pageHeight, 'F');
                        
                        // Dimensions des cartes (côte à côte)
                        const cardWidthMM = 85;
                        
                        // AJOUTER LE RECTO (à gauche)
                        const cardHeightMMRecto = (canvasRectoHeight * cardWidthMM) / canvasRectoWidth;
                        const xRecto = 15;
                        const yRecto = (pageHeight - cardHeightMMRecto) / 2;
                        
                        pdf.addImage(imgDataRecto, 'PNG', xRecto, yRecto, cardWidthMM, cardHeightMMRecto, undefined, 'FAST');
                        
                        // Label "RECTO"
                        pdf.setFontSize(12);
                        pdf.setTextColor(58, 51, 110);
                        pdf.setFont(undefined, 'bold');
                        pdf.text('RECTO', xRecto + cardWidthMM / 2, yRecto - 5, { align: 'center' });
                        
                        // AJOUTER LE VERSO (à droite)
                        const cardHeightMMVerso = (canvasVersoHeight * cardWidthMM) / canvasVersoWidth;
                        const xVerso = pageWidth - cardWidthMM - 15;
                        const yVerso = (pageHeight - cardHeightMMVerso) / 2;
                        
                        pdf.addImage(imgDataVerso, 'PNG', xVerso, yVerso, cardWidthMM, cardHeightMMVerso, undefined, 'FAST');
                        
                        // Label "VERSO"
                        pdf.setFontSize(12);
                        pdf.setTextColor(58, 51, 110);
                        pdf.setFont(undefined, 'bold');
                        pdf.text('VERSO', xVerso + cardWidthMM / 2, yVerso - 5, { align: 'center' });
                        
                        // Titre et numéro de page en bas
                        pdf.setFontSize(10);
                        pdf.setTextColor(100, 100, 100);
                        pdf.setFont(undefined, 'normal');
                        pdf.text(`Carte ANVDKO - Membre ${i + 1}/${totalCards}`, pageWidth / 2, pageHeight - 10, { align: 'center' });
                        
                        successCount++;
                        console.log(`✓ Carte ${i + 1} ajoutée au PDF (recto + verso)`);
                    } else {
                        console.warn(`⚠ Carte ${i + 1} incomplète (recto: ${!!imgDataRecto}, verso: ${!!imgDataVerso})`);
                    }
                    
                    updateProgress(i + 1, totalCards, `Carte ${i + 1}/${totalCards} terminée`);
                }

                console.log(`\n=== RÉSUMÉ ===`);
                console.log(`Total: ${successCount}/${totalCards} cartes générées`);

                if (successCount === 0) {
                    throw new Error("Aucune carte n'a pu être générée. Consultez la console pour plus de détails.");
                }

                // Finalisation
                updateProgress(totalCards, totalCards, 'Génération du fichier PDF...');
                await new Promise(resolve => setTimeout(resolve, 1000));

                // Sauvegarder le PDF
                const dateStr = new Date().toISOString().split('T')[0];
                pdf.save(`Badges_ANVDKO_${dateStr}.pdf`);
                
                clearTimeout(timeoutId);
                document.body.removeChild(iframe);
                resolve();

            } catch (err) {
                console.error("❌ Erreur dans la génération:", err);
                clearTimeout(timeoutId);
                if (document.body.contains(iframe)) {
                    document.body.removeChild(iframe);
                }
                reject(err);
            }
        };

        iframe.onerror = (err) => {
            console.error("❌ Erreur de chargement iframe:", err);
            clearTimeout(timeoutId);
            if (document.body.contains(iframe)) {
                document.body.removeChild(iframe);
            }
            reject(new Error("Erreur de chargement de la page voir_cartes.php"));
        };

        // Timeout de 5 minutes
        timeoutId = setTimeout(() => {
            console.error("⏱ Timeout lors du téléchargement");
            if (document.body.contains(iframe)) {
                document.body.removeChild(iframe);
            }
            reject(new Error("Timeout - Le téléchargement a pris trop de temps"));
        }, 300000);
    });
}

function voirCartesSelectionnees() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Aucun membre sélectionné',
            text: 'Veuillez sélectionner au moins un membre.'
        });
        return;
    }

    const idsEncoded = encodeURIComponent(selectedIds.join(','));
    window.open(`voir_cartes.php?ids=${idsEncoded}`, '_blank');
}

function getSelectedIds() {
    const checkboxes = document.querySelectorAll('.checkboxIdTableCarte:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}
</script>

<script>
    $(document).ready(function() {
        $('#input_recherche').on('keyup', function() {
            const searchValue = $(this).val().toLowerCase();

            let totalMatched = 0;

            // Recherche dans le tableau des membres à jour
            $('#ma_table_payes tbody tr').each(function() {
                const rowText = $(this).text().toLowerCase();
                const isMatch = rowText.includes(searchValue);
                $(this).toggle(isMatch);
                if (isMatch) totalMatched++;
            });

            // Recherche dans le tableau des membres en retard
            $('#ma_table_nonpayes tbody tr').each(function() {
                const rowText = $(this).text().toLowerCase();
                const isMatch = rowText.includes(searchValue);
                $(this).toggle(isMatch);
                if (isMatch) totalMatched++;
            });

            // Si aucun résultat trouvé
            if (totalMatched === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Aucun membre trouvé',
                    text: 'Aucun résultat ne correspond à votre recherche.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    });
</script>
