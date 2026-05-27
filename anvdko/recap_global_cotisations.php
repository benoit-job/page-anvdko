<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");

$page_export_title = 'Récapitulatif Global des Cotisations et Adhésions';
?>

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Récapitulatif Global Cotisations</title>
    
    <?php include('includes/php/includes-css.php');?>
    
    <!-- DataTables & SweetAlert -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            border-top: 4px solid #fff;
            overflow: hidden;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .stat-card.cette-annee { border-top-color: #4e73df; }
        .stat-card.trois-ans { border-top-color: #1cc88a; }
        .stat-card.six-ans { border-top-color: #f6c23e; }
        .stat-card.global { border-top-color: #e74a3b; }
        
        .stat-title {
            font-size: 0.9rem;
            color: #858796;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .stat-value {
            font-size: clamp(1rem, 1.6vw, 1.25rem);
            font-weight: 700;
            color: #5a5c69;
            margin-bottom: 5px;
            line-height: 1.2;
            overflow-wrap: anywhere;
        }

        .stat-total-line {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 12px;
            align-items: end;
        }

        .stat-total-amount {
            font-size: clamp(1.35rem, 2.2vw, 2rem);
            line-height: 1.1;
            text-align: right;
            max-width: 100%;
            overflow-wrap: anywhere;
            white-space: normal;
        }
        
        .bilan-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: 1px solid #dee2e6;
        }

        .bilan-card h2,
        .bilan-card h3 {
            font-size: clamp(1.6rem, 3vw, 2.5rem);
            line-height: 1.15;
            overflow-wrap: anywhere;
        }
        
        .solde-positif { color: #1cc88a !important; }
        .solde-negatif { color: #e74a3b !important; }
        
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255,255,255,0.8);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body>
    <main class="main" id="top">
        <?php include('includes/php/menu.php');?>
        <?php include('includes/php/header.php');?>
        
        <div class="content" data-export-region>
            <div class="pb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i> Récapitulatif Global - Cotisations & Adhésions</h2>
                    <button class="btn btn-primary no-print" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Imprimer la page
                    </button>
                </div>

                <!-- Statistiques Rapides -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="stat-card cette-annee">
                            <div class="stat-title">Cette Année</div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="fs-9 text-muted mb-1">Cotisations</div>
                                    <div class="stat-value text-primary" id="stat-annee-cot">0 F</div>
                                </div>
                                <div class="text-end">
                                    <div class="fs-9 text-muted mb-1">Adhésions</div>
                                    <div class="stat-value text-info" id="stat-annee-adh">0 F</div>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="stat-total-line">
                                <span class="fw-bold">Total</span>
                                <span class="fw-bold text-dark stat-total-amount" id="stat-annee-tot">0 F</span>
                            </div>
                            <div class="text-end mt-2 fs-9 text-muted"><span id="stat-membres">0</span> membres actifs</div>
                        </div>
                    </div>
                    
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="stat-card trois-ans">
                            <div class="stat-title">3 Dernières Années</div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="fs-9 text-muted mb-1">Cotisations</div>
                                    <div class="stat-value text-primary" id="stat-3ans-cot">0 F</div>
                                </div>
                                <div class="text-end">
                                    <div class="fs-9 text-muted mb-1">Adhésions</div>
                                    <div class="stat-value text-info" id="stat-3ans-adh">0 F</div>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="stat-total-line">
                                <span class="fw-bold">Total cumulé</span>
                                <span class="fw-bold text-dark stat-total-amount" id="stat-3ans-tot">0 F</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="stat-card six-ans">
                            <div class="stat-title">6 Dernières Années</div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="fs-9 text-muted mb-1">Cotisations</div>
                                    <div class="stat-value text-primary" id="stat-6ans-cot">0 F</div>
                                </div>
                                <div class="text-end">
                                    <div class="fs-9 text-muted mb-1">Adhésions</div>
                                    <div class="stat-value text-info" id="stat-6ans-adh">0 F</div>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="stat-total-line">
                                <span class="fw-bold">Total cumulé</span>
                                <span class="fw-bold text-dark stat-total-amount" id="stat-6ans-tot">0 F</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="stat-card global">
                            <div class="stat-title">Global (Toutes années)</div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="fs-9 text-muted mb-1">Cotisations</div>
                                    <div class="stat-value text-primary" id="stat-glb-cot">0 F</div>
                                </div>
                                <div class="text-end">
                                    <div class="fs-9 text-muted mb-1">Adhésions</div>
                                    <div class="stat-value text-info" id="stat-glb-adh">0 F</div>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="stat-total-line">
                                <span class="fw-bold">Total général</span>
                                <span class="fw-bold text-dark stat-total-amount" id="stat-glb-tot">0 F</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtres Avancés -->
                <div class="filter-section no-print">
                    <h5 class="mb-3"><i class="fas fa-filter me-2"></i>Filtres Avancés</h5>
                    <form id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Année</label>
                                <select class="form-select" name="annee" id="filter-annee">
                                    <option value="">Toutes</option>
                                    <?php
                                    $cy = (int) date('Y');
                                    for ($y = $cy + 1; $y >= $cy - 10; $y--) {
                                        echo '<option value="' . $y . '">' . $y . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Semestre</label>
                                <select class="form-select" name="semestre" id="filter-semestre">
                                    <option value="">Tous</option>
                                    <option value="1">Semestre 1</option>
                                    <option value="2">Semestre 2</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Trimestre</label>
                                <select class="form-select" name="trimestre" id="filter-trimestre">
                                    <option value="">Tous</option>
                                    <option value="1">T1</option>
                                    <option value="2">T2</option>
                                    <option value="3">T3</option>
                                    <option value="4">T4</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Mois</label>
                                <select class="form-select" name="mois" id="filter-mois">
                                    <option value="">Tous</option>
                                    <option value="1">Janvier</option>
                                    <option value="2">Février</option>
                                    <option value="3">Mars</option>
                                    <option value="4">Avril</option>
                                    <option value="5">Mai</option>
                                    <option value="6">Juin</option>
                                    <option value="7">Juillet</option>
                                    <option value="8">Août</option>
                                    <option value="9">Septembre</option>
                                    <option value="10">Octobre</option>
                                    <option value="11">Novembre</option>
                                    <option value="12">Décembre</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Type</label>
                                <select class="form-select" name="type_paiement" id="filter-type">
                                    <option value="">Tous</option>
                                    <option value="cotisation">Cotisations</option>
                                    <option value="adhesion">Adhésions</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-secondary w-100" id="btn-reset">Réinitialiser</button>
                            </div>
                        </div>
                        
                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label class="form-label">Période personnalisée</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" name="date_debut" id="filter-debut">
                                    <span class="input-group-text">au</span>
                                    <input type="date" class="form-control" name="date_fin" id="filter-fin">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Recherche par membre (nom/prénom)</label>
                                <input type="text" class="form-control" name="membre" id="filter-membre" placeholder="Entrez un nom...">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-2"></i>Filtrer</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Bilan Financier (Mise à jour selon les filtres) -->
                <div class="bilan-card mb-4 text-center">
                    <h4 class="mb-4 text-uppercase fw-bold text-muted">Bilan Financier <span id="bilan-periode-texte" class="text-primary">(Global)</span></h4>
                    <div class="row">
                        <div class="col-md-4 border-end border-2 border-white">
                            <h5 class="text-muted mb-2">Total Recettes</h5>
                            <h3 class="text-primary fw-bold" id="bilan-recettes">0 F</h3>
                            <div class="fs-9 text-muted">Cotisations : <span id="bilan-cot">0</span> | Adhésions : <span id="bilan-adh">0</span></div>
                        </div>
                        <div class="col-md-4 border-end border-2 border-white">
                            <h5 class="text-muted mb-2">Total Dépenses</h5>
                            <h3 class="text-danger fw-bold" id="bilan-depenses">0 F</h3>
                            <div class="fs-9 text-muted">Sur la période filtrée</div>
                        </div>
                        <div class="col-md-4">
                            <h5 class="text-muted mb-2">Solde Restant</h5>
                            <h2 class="fw-bold" id="bilan-solde">0 F</h2>
                            <div class="fs-9 text-muted">Recettes - Dépenses</div>
                        </div>
                    </div>
                </div>

                <!-- Graphiques -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card h-100">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">Évolution Annuelle / Périodique</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="barChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">Répartition Financière</h6>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="chart-container" style="height: 250px;">
                                    <canvas id="pieChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Évolution Mensuelle</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="lineChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Tableau détaillé -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="fas fa-table me-2"></i>Détails par période</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped w-100" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>Période (Année/Mois)</th>
                                        <th class="text-end">Cotisations (F)</th>
                                        <th class="text-end">Adhésions (F)</th>
                                        <th class="text-end text-danger">Dépenses (F)</th>
                                        <th class="text-end fw-bold">Solde (F)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Rempli par AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            
            <?php include('includes/php/footer.php');?>
        </div>
    </main>

    <!-- Overlay de chargement -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Chargement...</span>
        </div>
    </div>

    <?php include('includes/php/includes-js.php');?>
    
    <!-- DataTables & Chart.js & SweetAlert -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Variables globales pour les graphiques
        let barChart = null;
        let pieChart = null;
        let lineChart = null;
        let table = null;

        const formatFCFA = (val) => new Intl.NumberFormat('fr-FR').format(val) + ' F';

        $(document).ready(function() {
            // Init DataTable
            table = $('#dataTable').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' },
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'excel', className: 'btn btn-success btn-sm' },
                    { extend: 'pdf', className: 'btn btn-danger btn-sm' },
                    { extend: 'print', className: 'btn btn-info btn-sm' }
                ],
                order: [[0, 'desc']]
            });

            // Charger les données initiales
            loadData();

            // Gestionnaire de soumission du filtre
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                loadData();
            });

            // Bouton de réinitialisation
            $('#btn-reset').on('click', function() {
                $('#filterForm')[0].reset();
                loadData();
            });
            
            // Logique conditionnelle des filtres
            $('#filter-annee').on('change', function() {
                if ($(this).val() !== '') {
                    $('#bilan-periode-texte').text('(Année ' + $(this).val() + ')');
                } else {
                    $('#bilan-periode-texte').text('(Global)');
                }
            });
        });

        function loadData() {
            $('#loadingOverlay').css('display', 'flex');
            
            const formData = new FormData($('#filterForm')[0]);
            
            fetch('ajax/api_recap_cotisations.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                $('#loadingOverlay').hide();
                if (data.success) {
                    updateUI(data);
                } else {
                    Swal.fire('Erreur', data.message || 'Une erreur est survenue', 'error');
                }
            })
            .catch(err => {
                $('#loadingOverlay').hide();
                console.error(err);
                Swal.fire('Erreur', 'Impossible de communiquer avec le serveur', 'error');
            });
        }

        function updateUI(data) {
            // 1. Stats rapides
            $('#stat-annee-cot').text(formatFCFA(data.stats_rapides.annee_courante.cotisations));
            $('#stat-annee-adh').text(formatFCFA(data.stats_rapides.annee_courante.adhesions));
            $('#stat-annee-tot').text(formatFCFA(data.stats_rapides.annee_courante.total));
            $('#stat-membres').text(data.stats_rapides.annee_courante.membres);

            $('#stat-3ans-cot').text(formatFCFA(data.stats_rapides.trois_ans.cotisations));
            $('#stat-3ans-adh').text(formatFCFA(data.stats_rapides.trois_ans.adhesions));
            $('#stat-3ans-tot').text(formatFCFA(data.stats_rapides.trois_ans.total));

            $('#stat-6ans-cot').text(formatFCFA(data.stats_rapides.six_ans.cotisations));
            $('#stat-6ans-adh').text(formatFCFA(data.stats_rapides.six_ans.adhesions));
            $('#stat-6ans-tot').text(formatFCFA(data.stats_rapides.six_ans.total));

            $('#stat-glb-cot').text(formatFCFA(data.stats_rapides.global.cotisations));
            $('#stat-glb-adh').text(formatFCFA(data.stats_rapides.global.adhesions));
            $('#stat-glb-tot').text(formatFCFA(data.stats_rapides.global.total));

            // 2. Bilan Filtré
            $('#bilan-cot').text(formatFCFA(data.bilan_filtre.cotisations));
            $('#bilan-adh').text(formatFCFA(data.bilan_filtre.adhesions));
            $('#bilan-recettes').text(formatFCFA(data.bilan_filtre.recettes));
            $('#bilan-depenses').text(formatFCFA(data.bilan_filtre.depenses));
            
            const elSolde = $('#bilan-solde');
            elSolde.text(formatFCFA(data.bilan_filtre.solde));
            elSolde.removeClass('solde-positif solde-negatif');
            if (data.bilan_filtre.solde >= 0) {
                elSolde.addClass('solde-positif');
            } else {
                elSolde.addClass('solde-negatif');
            }

            // 3. DataTable
            table.clear();
            data.table_data.forEach(row => {
                let soldeCls = row.solde >= 0 ? 'text-success' : 'text-danger';
                table.row.add([
                    row.periode,
                    formatFCFA(row.cotisations),
                    formatFCFA(row.adhesions),
                    `<span class="text-danger">${formatFCFA(row.depenses)}</span>`,
                    `<span class="${soldeCls} fw-bold">${formatFCFA(row.solde)}</span>`
                ]);
            });
            table.draw();

            // 4. Graphiques
            updateCharts(data.charts);
        }

        function updateCharts(chartsData) {
            // Destroy existing charts
            if (barChart) barChart.destroy();
            if (pieChart) pieChart.destroy();
            if (lineChart) lineChart.destroy();

            // Bar Chart (Annuel/Périodique)
            const ctxBar = document.getElementById('barChart').getContext('2d');
            barChart = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: chartsData.annuel.labels,
                    datasets: [
                        { label: 'Cotisations', data: chartsData.annuel.cotisations, backgroundColor: '#4e73df' },
                        { label: 'Adhésions', data: chartsData.annuel.adhesions, backgroundColor: '#36b9cc' },
                        { label: 'Dépenses', data: chartsData.annuel.depenses, backgroundColor: '#e74a3b' }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Pie Chart (Répartition)
            const ctxPie = document.getElementById('pieChart').getContext('2d');
            pieChart = new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: ['Cotisations', 'Adhésions', 'Dépenses', 'Reste dispo.'],
                    datasets: [{
                        data: [
                            chartsData.repartition.cotisations,
                            chartsData.repartition.adhesions,
                            chartsData.repartition.depenses,
                            chartsData.repartition.solde
                        ],
                        backgroundColor: ['#4e73df', '#36b9cc', '#e74a3b', '#1cc88a']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
            });

            // Line Chart (Mensuel)
            const ctxLine = document.getElementById('lineChart').getContext('2d');
            lineChart = new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: chartsData.mensuel.labels,
                    datasets: [
                        { label: 'Recettes', data: chartsData.mensuel.recettes, borderColor: '#1cc88a', fill: false, tension: 0.1 },
                        { label: 'Dépenses', data: chartsData.mensuel.depenses, borderColor: '#e74a3b', fill: false, tension: 0.1 },
                        { label: 'Solde', data: chartsData.mensuel.solde, borderColor: '#f6c23e', borderDash: [5, 5], fill: false, tension: 0.1 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }
    </script>
</body>
</html>
