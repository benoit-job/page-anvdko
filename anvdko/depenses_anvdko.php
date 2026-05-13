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
    
    <title>Gestion des Dépenses ANVDKO</title>
    
    <?php include('includes/php/includes-css.php');?>
    
    <!-- CSS supplémentaires pour le module -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 5px solid;
            margin-bottom: 20px;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .stat-card.total { border-left-color: #2563eb; }
        .stat-card.month { border-left-color: #10b981; }
        .stat-card.count { border-left-color: #f59e0b; }
        
        .filter-section {
            background: #f8fafc;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            border: 2px solid #e2e8f0;
        }
        
        .badge-category {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .action-btn {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 3px;
            transition: all 0.3s;
            border: none;
        }
        
        .action-btn:hover {
            transform: scale(1.1);
        }
        
        .montant-row {
            background: #f8fafc;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 10px;
            border-left: 4px solid #2563eb;
        }
        
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        
        .spinner-border-custom {
            width: 3rem;
            height: 3rem;
            border-width: 0.4rem;
        }
    </style>
</head>

<body>
    <main class="main" id="top">
        <?php include('includes/php/menu.php');?>
        <?php include('includes/php/header.php');?>
        
        <div class="content">
            <div class="pb-5">
                <!-- En-tête -->
                <div class="mb-4">
                    <h2 class="mb-2">
                        <i class="fas fa-wallet me-2"></i>Gestion des Dépenses ANVDKO
                    </h2>
                    <p class="text-muted">
                        <i class="fas fa-user me-2"></i>
                        Connecté en tant que: <strong><?= htmlspecialchars($_SESSION['utilisateur']['pseudo'] ?? 'Utilisateur') ?></strong>
                    </p>
                </div>

                <!-- Statistiques -->
                <div class="row mb-4" id="statsSection">
                    <div class="col-md-4">
                        <div class="stat-card total">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Total des Dépenses</h6>
                                    <h3 class="mb-0 fw-bold" id="totalDepenses">0 FCFA</h3>
                                </div>
                                <div class="fs-1 text-primary"><i class="fas fa-coins"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card month">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Dépenses ce Mois</h6>
                                    <h3 class="mb-0 fw-bold" id="totalMois">0 FCFA</h3>
                                </div>
                                <div class="fs-1 text-success"><i class="fas fa-calendar-check"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card count">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Nombre de Dépenses</h6>
                                    <h3 class="mb-0 fw-bold" id="nombreDepenses">0</h3>
                                </div>
                                <div class="fs-1 text-warning"><i class="fas fa-receipt"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtres -->
                <div class="filter-section">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-filter me-2"></i>Période
                            </label>
                            <select class="form-select" id="filtrePeriode">
                                <option value="annee_courante">Année en cours (<?= date('Y') ?>)</option>
                                <option value="ce_mois">Ce mois</option>
                                <option value="3_mois">3 derniers mois</option>
                                <option value="6_mois">6 derniers mois</option>
                                <option value="annee_precedente">Année précédente (<?= date('Y') - 1 ?>)</option>
                                <option value="tout">Tout</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-tags me-2"></i>Catégorie
                            </label>
                            <select class="form-select" id="filtreCategorie">
                                <option value="">Toutes les catégories</option>
                                <option value="Réunion et Fonctionnement">Réunion et Fonctionnement</option>
                                <option value="Projets de Développement">Projets de Développement</option>
                                <option value="Aide Sociale">Aide Sociale</option>
                                <option value="Transport">Transport</option>
                                <option value="Équipement et Matériel">Équipement et Matériel</option>
                                <option value="Infrastructure Villageoise">Infrastructure Villageoise</option>
                                <option value="Santé">Santé</option>
                                <option value="Éducation">Éducation</option>
                                <option value="Festivités et Cérémonies">Festivités et Cérémonies</option>
                                <option value="Communication">Communication</option>
                                <option value="Divers">Divers</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-sort me-2"></i>Trier par
                            </label>
                            <select class="form-select" id="filtreOrdre">
                                <option value="date_desc">Date (récent d'abord)</option>
                                <option value="date_asc">Date (ancien d'abord)</option>
                                <option value="montant_desc">Montant (élevé d'abord)</option>
                                <option value="montant_asc">Montant (faible d'abord)</option>
                                <option value="categorie">Catégorie</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalAjout">
                                <i class="fas fa-plus-circle me-2"></i>Nouvelle Dépense
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tableau des dépenses -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="tableDepenses">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Titre</th>
                                        <th>Catégorie</th>
                                        <th>Montant Total</th>
                                        <th>Nb Paiements</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyDepenses">
                                    <!-- Données chargées via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            
            <?php include('includes/php/footer.php');?>
        </div>
    </main>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border spinner-border-custom text-light" role="status">
            <span class="visually-hidden">Chargement...</span>
        </div>
    </div>

    <!-- Modal Ajout/Modification -->
    <div class="modal fade" id="modalAjout" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-plus-circle me-2"></i>Nouvelle Dépense
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formDepense">
                    <div class="modal-body">
                        <input type="hidden" id="idDepense" name="id_depense">
                        <input type="hidden" name="id_user" value="<?= $_SESSION['utilisateur']['id'] ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Titre *</label>
                                <input type="text" class="form-control" id="titre" name="titre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Catégorie *</label>
                                <select class="form-select" id="categorie" name="categorie" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="Réunion et Fonctionnement">Réunion et Fonctionnement</option>
                                    <option value="Projets de Développement">Projets de Développement</option>
                                    <option value="Aide Sociale">Aide Sociale</option>
                                    <option value="Transport">Transport</option>
                                    <option value="Équipement et Matériel">Équipement et Matériel</option>
                                    <option value="Infrastructure Villageoise">Infrastructure Villageoise</option>
                                    <option value="Santé">Santé</option>
                                    <option value="Éducation">Éducation</option>
                                    <option value="Festivités et Cérémonies">Festivités et Cérémonies</option>
                                    <option value="Communication">Communication</option>
                                    <option value="Divers">Divers</option>
                                    <option value="Autre">Autre</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Date de la dépense *</label>
                            <input type="date" class="form-control" id="dateDepense" name="date_depense" required>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0">
                                <i class="fas fa-money-bill-wave me-2"></i>Montants
                            </h6>
                            <button type="button" class="btn btn-sm btn-primary" onclick="ajouterMontant()">
                                <i class="fas fa-plus me-1"></i>Ajouter un montant
                            </button>
                        </div>
                        
                        <div id="montantsContainer">
                            <div class="montant-row" data-index="0">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <label class="form-label">Montant (FCFA)</label>
                                        <input type="number" class="form-control" name="montants[]" step="0.01" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Date de paiement</label>
                                        <input type="date" class="form-control" name="dates_paiement[]" required>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <label class="form-label d-block">&nbsp;</label>
                                        <button type="button" class="btn btn-danger action-btn" onclick="supprimerMontant(0)" style="display:none;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include('includes/php/includes-js.php');?>
    
    <!-- Scripts supplémentaires pour le module -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Variables globales
        let tableDepenses;
        let montantIndex = 0;
        let modeEdition = false;

        // Initialisation
        $(document).ready(function() {
            // Initialiser DataTable
            tableDepenses = $('#tableDepenses').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                },
                pageLength: 25,
                order: [[0, 'desc']],
                columnDefs: [
                    { orderable: false, targets: 5 }
                ]
            });
            
            // Charger les données
            chargerDepenses();
            calculerStats();
            
            // Événements des filtres
            $('#filtrePeriode, #filtreCategorie, #filtreOrdre').on('change', function() {
                chargerDepenses();
                calculerStats();
            });
            
            // Événement soumission formulaire
            $('#formDepense').on('submit', function(e) {
                e.preventDefault();
                if (modeEdition) {
                    modifierDepense();
                } else {
                    ajouterDepense();
                }
            });
            
            // Réinitialiser le formulaire
            $('#modalAjout').on('show.bs.modal', function() {
                if (!modeEdition) {
                    resetFormulaire();
                }
            });
            
            // Dates par défaut
            $('#dateDepense').val(new Date().toISOString().split('T')[0]);
            $('input[name="dates_paiement[]"]').first().val(new Date().toISOString().split('T')[0]);
        });

        // Fonction: Charger les dépenses
        function chargerDepenses() {
            showLoading();
            
            $.post('ajax/depenses_api.php', {
                action: 'charger_depenses',
                periode: $('#filtrePeriode').val(),
                categorie: $('#filtreCategorie').val(),
                ordre: $('#filtreOrdre').val()
            }, function(response) {
                hideLoading();
                
                if (response.success) {
                    afficherDepenses(response.data);
                } else {
                    Swal.fire('Erreur', response.message, 'error');
                }
            }, 'json').fail(function() {
                hideLoading();
                Swal.fire('Erreur', 'Impossible de charger les dépenses', 'error');
            });
        }

        // Fonction: Afficher les dépenses
        function afficherDepenses(depenses) {
            tableDepenses.clear();
            
            depenses.forEach(function(depense) {
                const badge = getBadgeCategorie(depense.categorie);
                const montantFormate = formatMontant(depense.montant_total);
                
                const actions = `
                    <button class="btn btn-info action-btn" onclick="voirDetail(${depense.id_depense})" title="Voir détails">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-primary action-btn" onclick="editerDepense(${depense.id_depense})" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger action-btn" onclick="supprimerDepense(${depense.id_depense})" title="Supprimer">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
                
                tableDepenses.row.add([
                    formatDate(depense.date_depense),
                    depense.titre,
                    badge,
                    montantFormate,
                    depense.nb_paiements,
                    actions
                ]);
            });
            
            tableDepenses.draw();
        }

        // Fonction: Ajouter une dépense
        function ajouterDepense() {
            showLoading();
            
            const formData = new FormData($('#formDepense')[0]);
            formData.append('action', 'ajouter_depense');
            
            $.ajax({
                url: 'ajax/depenses_api.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    hideLoading();
                    
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        
                        $('#modalAjout').modal('hide');
                        chargerDepenses();
                        calculerStats();
                    } else {
                        Swal.fire('Erreur', response.message, 'error');
                    }
                },
                error: function() {
                    hideLoading();
                    Swal.fire('Erreur', 'Une erreur est survenue', 'error');
                }
            });
        }

        // Fonction: Éditer une dépense
        function editerDepense(idDepense) {
            showLoading();
            
            $.get('ajax/depenses_api.php', {action: 'charger_detail', id_depense: idDepense}, function(response) {
                hideLoading();
                
                if (response.success) {
                    modeEdition = true;
                    const depense = response.depense;
                    const montants = response.montants;
                    
                    $('#modalTitle').html('<i class="fas fa-edit me-2"></i>Modifier la Dépense');
                    $('#idDepense').val(depense.id_depense);
                    $('#titre').val(depense.titre);
                    $('#categorie').val(depense.categorie);
                    $('#description').val(depense.description);
                    $('#dateDepense').val(depense.date_depense);
                    
                    $('#montantsContainer').empty();
                    montantIndex = 0;
                    
                    montants.forEach(function(m) {
                        ajouterMontant(m.montant, m.date_paiement);
                    });
                    
                    $('#modalAjout').modal('show');
                } else {
                    Swal.fire('Erreur', response.message, 'error');
                }
            }, 'json').fail(function() {
                hideLoading();
                Swal.fire('Erreur', 'Impossible de charger la dépense', 'error');
            });
        }

        // Fonction: Modifier une dépense
        function modifierDepense() {
            showLoading();
            
            const formData = new FormData($('#formDepense')[0]);
            formData.append('action', 'modifier_depense');
            
            $.ajax({
                url: 'ajax/depenses_api.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    hideLoading();
                    
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        
                        $('#modalAjout').modal('hide');
                        modeEdition = false;
                        chargerDepenses();
                        calculerStats();
                    } else {
                        Swal.fire('Erreur', response.message, 'error');
                    }
                },
                error: function() {
                    hideLoading();
                    Swal.fire('Erreur', 'Une erreur est survenue', 'error');
                }
            });
        }

        // Fonction: Supprimer une dépense
        function supprimerDepense(idDepense) {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Cette action supprimera la dépense et tous ses montants/commentaires !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading();
                    
                    $.post('ajax/depenses_api.php', {
                        action: 'supprimer_depense',
                        id_depense: idDepense
                    }, function(response) {
                        hideLoading();
                        
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Supprimé',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            
                            chargerDepenses();
                            calculerStats();
                        } else {
                            Swal.fire('Erreur', response.message, 'error');
                        }
                    }, 'json').fail(function() {
                        hideLoading();
                        Swal.fire('Erreur', 'Impossible de supprimer la dépense', 'error');
                    });
                }
            });
        }

        // Fonction: Voir détail
        function voirDetail(idDepense) {
            window.location.href = 'depense_detail.php?id=' + idDepense;
        }

        // Fonction: Calculer les statistiques
        function calculerStats() {
            $.post('ajax/depenses_api.php', {
                action: 'calculer_stats',
                periode: $('#filtrePeriode').val()
            }, function(response) {
                if (response.success) {
                    $('#totalDepenses').text(formatMontant(response.total));
                    $('#totalMois').text(formatMontant(response.total_mois));
                    $('#nombreDepenses').text(response.nombre);
                }
            }, 'json');
        }

        // Fonction: Ajouter un champ montant
        function ajouterMontant(montant = '', datePaiement = '') {
            montantIndex++;
            
            const html = `
                <div class="montant-row" data-index="${montantIndex}">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <label class="form-label">Montant (FCFA)</label>
                            <input type="number" class="form-control" name="montants[]" step="0.01" value="${montant}" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Date de paiement</label>
                            <input type="date" class="form-control" name="dates_paiement[]" value="${datePaiement}" required>
                        </div>
                        <div class="col-md-2 text-center">
                            <label class="form-label d-block">&nbsp;</label>
                            <button type="button" class="btn btn-danger action-btn" onclick="supprimerMontant(${montantIndex})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            $('#montantsContainer').append(html);
            
            if (!datePaiement) {
                $(`div[data-index="${montantIndex}"] input[name="dates_paiement[]"]`).val(new Date().toISOString().split('T')[0]);
            }
        }

        // Fonction: Supprimer un champ montant
        function supprimerMontant(index) {
            if ($('.montant-row').length <= 1) {
                Swal.fire('Attention', 'Vous devez avoir au moins un montant', 'warning');
                return;
            }
            
            $(`div[data-index="${index}"]`).remove();
        }

        // Fonction: Réinitialiser le formulaire
        function resetFormulaire() {
            modeEdition = false;
            $('#modalTitle').html('<i class="fas fa-plus-circle me-2"></i>Nouvelle Dépense');
            $('#formDepense')[0].reset();
            $('#idDepense').val('');
            
            $('#montantsContainer').empty();
            montantIndex = 0;
            ajouterMontant();
            
            $('#dateDepense').val(new Date().toISOString().split('T')[0]);
        }

        // Fonctions utilitaires
        function formatMontant(montant) {
            return new Intl.NumberFormat('fr-FR', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(montant) + ' FCFA';
        }

        function formatDate(date) {
            return new Date(date).toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }

        function getBadgeCategorie(categorie) {
            const couleurs = {
                'Salaire': 'success',
                'Transport': 'primary',
                'Alimentation': 'warning',
                'Logement': 'info',
                'Santé': 'danger',
                'Éducation': 'secondary',
                'Loisirs': 'dark',
                'Autre': 'light'
            };
            
            const couleur = couleurs[categorie] || 'secondary';
            return `<span class="badge bg-${couleur} badge-category">${categorie}</span>`;
        }

        function showLoading() {
            $('#loadingOverlay').css('display', 'flex');
        }

        function hideLoading() {
            $('#loadingOverlay').hide();
        }
    </script>
</body>
</html>