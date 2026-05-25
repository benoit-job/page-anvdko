<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");
?>

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gestion des Adhésions</title>

    <?php include('includes/php/includes-css.php');?>

  </head>


  <body>

    <main class="main" id="top">
    	
      <?php include('includes/php/menu.php');?>

      <?php include('includes/php/header.php');?>

      <div class="content">

        <div class="pb-5">
          <div class="row g-4">
            <div class="col-12">

              <div class="mb-8">
                <h2 class="mb-2">Gestion des Adhésions</h2>
                <h5 class="text-body-tertiary fw-semibold">Visualiser et gérer les adhésions des membres</h5>
              </div>

              <div class="page-section">
                  <div class="card card-fluid">
                    <div class="card-header p-2 border-0"> 
                      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                          <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Rechercher..." style="width: 250px;">
                        </div>
                        <div>
                          <button class="btn btn-primary btn-sm" id="btnPayerAdhesion" style="display: none;">
                            <i class="fas fa-money-bill-wave me-2"></i> Payer adhésion
                          </button>
                        </div>
                      </div>
                    </div>
                    
                    <div class="table-responsive">
                      <table class="table table-hover m-0" id='ma_table'> 
                        <thead class="thead-light">
                          <tr>
                            <th class="text-center"><input type="checkbox" id="checkboxTous" onclick="toggleAllCheckboxes(this)"></th>
                            <th>N°</th>
                            <th></th>
                            <th>Nom & prénoms</th>
                            <th>N° Adhésion</th>
                            <th class="text-center">Montant payé (FCFA)</th>
                            <th class="text-center">Statut</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody class="listeadherent"> 
                          <tr id="spinner">
                            <td colspan="8" class="text-center">
                              <div class="spinner-border spinner-border-sm" role="status" aria-hidden="false">
                                <span class="visually-hidden">Chargement...</span>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>

                    <!-- Résumé -->
                    <div class="card-footer p-3 border-top">
                      <div class="row">
                        <div class="col-md-3">
                          <p class="mb-0"><strong>Membres inscrits:</strong></p>
                          <p class="mb-0 text-primary fs-5"><span id="totalMembres">0</span></p>
                        </div>
                        <div class="col-md-3">
                          <p class="mb-0"><strong>Adhésions payées:</strong></p>
                          <p class="mb-0 text-success fs-5"><span id="totalPayes">0</span></p>
                        </div>
                        <div class="col-md-3">
                          <p class="mb-0"><strong>Montant total collecté:</strong></p>
                          <p class="mb-0 text-success fs-5"><span id="totalMontant">0</span> FCFA</p>
                        </div>
                        <div class="col-md-3">
                          <p class="mb-0"><strong>Non payées:</strong></p>
                          <p class="mb-0 text-danger fs-5"><span id="totalNonPayes">0</span></p>
                        </div>
                      </div>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
      let selectedCheckboxes = [];

      $(document).ready(function() {
        loadAdhesions();
      });
      function formatMontant(montant) {
        if (!montant || isNaN(montant)) return '0';
        return new Intl.NumberFormat('fr-FR').format(montant);
        }

        function formatMontantSimple(montant) {
        if (!montant || isNaN(montant)) return '0';
        return new Intl.NumberFormat('fr-FR').format(montant);
        }

      function loadAdhesions() {
        $.ajax({
          url: 'ajax/adhesion_ajax.php',
          method: 'POST',
          data: {
            action: 'loadAdhesions'
          },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
                console.log('Adhésions chargées avec succès:', response.data);
              displayAdhesions(response.data);
            } else {
              // Remplacer le spinner par un message d'erreur utile
              const message = response.message || 'Erreur lors du chargement des adhésions';
              $('.listeadherent').html(`<tr><td colspan="8" class="text-center text-danger">${message}</td></tr>`);
              console.error('LoadAdhesions failed:', message, response);
            }
          },
          error: function() {
            console.error('Erreur lors du chargement des adhésions (requête AJAX)');
            $('.listeadherent').html('<tr><td colspan="8" class="text-center text-danger">Erreur réseau lors du chargement des adhésions</td></tr>');
          }
        });
      }

      function displayAdhesions(membres) {
        let html = '';
        let ligne = 0;
        let totalPayes = 0;
        let totalMontant = 0;
        let totalNonPayes = 0;

        $.each(membres, function(index, membre) {
          ligne++;
          const statusClass = membre.statut_adhesion === 'Payé' ? 'badge bg-success' : 
                             (membre.statut_adhesion === 'Moitié payé' ? 'badge bg-warning' : 'badge bg-danger');
          const statusText = membre.statut_adhesion;
          const montantPaye = parseFloat(membre.montant_adhesion) || 0;

          if (statusText === 'Payé') {
            totalPayes++;
            totalMontant += montantPaye;
          } else if (statusText !== 'Payé') {
            totalNonPayes++;
          } 


          html += `<tr>
            <td class="text-center">
              <input type="checkbox" class="checkboxIdTable" onchange="updateSelection()" value="${membre.id}" data-montant="${montantPaye}">
            </td>
            <td>${ligne}</td>
            <td></td>
            <td><strong>${(membre.prenom ? membre.prenom : '') + ' ' + (membre.nom ? membre.nom : '')}</strong></td>
            <td><span class="badge bg-primary">${membre.num_adhesion}</span></td>
            <td class="text-center">${formatMontant(montantPaye)}</td>
            <td class="text-center"><span class="${statusClass}">${statusText}</span></td>
            <td class="text-end">
              <a href="adherent_details.php?id_membre=${membre.id_crypte}" class="btn btn-light btn-sm">
                <i class="fas fa-eye"></i> Voir
              </a>
            </td>
          </tr>`;
        });

        if (ligne === 0) {
          html = '<tr><td colspan="8" class="text-center text-muted">Aucun membre trouvé</td></tr>';
        }

        $('.listeadherent').html(html);
        $('#totalMembres').text(membres.length);
        $('#totalPayes').text(totalPayes);
        $('#totalMontant').text(formatMontantSimple(totalMontant));
        $('#totalNonPayes').text(totalNonPayes);
      }

      function toggleAllCheckboxes(checkbox) {
        $('.checkboxIdTable').prop('checked', checkbox.checked);
        updateSelection();
      }

      function updateSelection() {
        selectedCheckboxes = [];
        $('.checkboxIdTable:checked').each(function() {
          selectedCheckboxes.push($(this).val());
        });

        if (selectedCheckboxes.length > 0) {
          $('#btnPayerAdhesion').show();
        } else {
          $('#btnPayerAdhesion').hide();
        }
      }

      $('#btnPayerAdhesion').click(function() {
        if (selectedCheckboxes.length === 0) {
          Swal.fire('Attention', 'Veuillez sélectionner au moins un membre', 'warning');
          return;
        }

        Swal.fire({
          title: 'Confirmer le paiement',
          text: `Vous allez payer l'adhésion de ${selectedCheckboxes.length} membre(s). Êtes-vous sûr?`,
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Oui, payer',
          cancelButtonText: 'Annuler',
          customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-secondary'
          }
        }).then((result) => {
          if (result.isConfirmed) {
            payerAdhesions();
          }
        });
      });

      function payerAdhesions() {
        $.ajax({
          url: 'ajax/adhesion_ajax.php',
          method: 'POST',
          data: {
            action: 'payerAdhesion',
            ids_membres: selectedCheckboxes
          },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              Swal.fire('Succès', response.message, 'success');
              loadAdhesions();
              $('#checkboxTous').prop('checked', false);
              selectedCheckboxes = [];
              $('#btnPayerAdhesion').hide();
            } else {
              Swal.fire('Erreur', response.message, 'error');
            }
          },
          error: function() {
            Swal.fire('Erreur', 'Une erreur est survenue lors du paiement', 'error');
          }
        });
      }

      $('#searchInput').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('#ma_table tbody tr').each(function() {
          const text = $(this).text().toLowerCase();
          $(this).toggle(text.includes(searchTerm));
        });
      });
    </script>

  </body>

</html>
