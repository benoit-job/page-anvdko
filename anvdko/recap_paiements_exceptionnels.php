<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");

$page_export_title = 'Récapitulatif Global des Paiements Exceptionnels';
?>

<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Récapitulatif Global Paiements Exceptionnels</title>
    
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
            font-size: 1.5rem;
            font-weight: 700;
            color: #5a5c69;
            margin-bottom: 5px;
        }
        
        .bilan-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: 1px solid #dee2e6;
        }
        
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
                    <h2 class="mb-0"><i class="fas fa-star me-2 text-warning"></i> Récapitulatif Global - Paiements Exceptionnels</h2>
                    <button class="btn btn-primary no-print" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Imprimer la page
                    </button>
                </div>

                <!-- Statistiques Rapides -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="stat-card cette-annee">
                            <div class="stat-title">Cette Année</div>
                            <div class="stat-value text-primary" id="stat-annee-tot">0 FCFA</div>
                            <div class="fs-9 text-muted"><span id="stat-annee-nb">0</span> paiements</div>
                        </div>
                    </div>
                    
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="stat-card trois-ans">
                            <div class="stat-title">3 Dernières Années</div>
                            <div class="stat-value text-success" id="stat-3ans-tot">0 FCFA</div>
                            <div class="fs-9 text-muted"><span id="stat-3ans-nb">0</span> paiements</div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="stat-card six-ans">
                            <div class="stat-title">6 Dernières Années</div>
                            <div class="stat-value text-warning" id="stat-6ans-tot">0 FCFA</div>
                            <div class="fs-9 text-muted"><span id="stat-6ans-nb">0</span> paiements</div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="stat-card global">
                            <div class="stat-title">Global (Toutes années)</div>
                            <div class="stat-value text-danger" id="stat-glb-tot">0 FCFA</div>
                            <div class="fs-9 text-muted"><span id="stat-glb-nb">0</span> paiements</div>
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
                                <label class="form-label">Motif du paiement</label>
                                <select class="form-select" name="type_paiement" id="filter-type">
                                    <option value="">Chargement...</option>
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

                <!-- Bilan Filtré -->
                <div class="bilan-card mb-4 text-center">
                    <h4 class="mb-2 text-uppercase fw-bold text-muted">Total sur la période sélectionnée</h4>
                    <h2 class="text-primary fw-bold mb-0" id="bilan-montant">0 FCFA</h2>
                    <div class="text-muted"><span id="bilan-nb">0</span> paiements effectués</div>
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
                                <h6 class="mb-0">Répartition par Motif</h6>
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
                        <h6 class="mb-0"><i class="fas fa-table me-2"></i>Détails des paiements exceptionnels</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped w-100" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>Année</th>
                                        <th>Motif (Type de paiement)</th>
                                        <th class="text-end">Nombre de paiements</th>
                                        <th class="text-end fw-bold">Montant collecté (FCFA)</th>
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

        const formatFCFA = (val) => new Intl.NumberFormat('fr-FR').format(val) + ' FCFA';

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
                order: [[0, 'desc'], [1, 'asc']]
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
        });

        function loadData() {
            $('#loadingOverlay').css('display', 'flex');
            
            const formData = new FormData($('#filterForm')[0]);
            
            fetch('ajax/api_recap_exceptionnels.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                $('#loadingOverlay').hide();
                if (data.success) {
                    if (data.options_motifs) {
                        const valBefore = $('#filter-type').val();
                        $('#filter-type').html(data.options_motifs);
                        if (valBefore) $('#filter-type').val(valBefore);
                    }
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
            $('#stat-annee-tot').text(formatFCFA(data.stats_rapides.annee_courante.total));
            $('#stat-annee-nb').text(data.stats_rapides.annee_courante.nb);

            $('#stat-3ans-tot').text(formatFCFA(data.stats_rapides.trois_ans.total));
            $('#stat-3ans-nb').text(data.stats_rapides.trois_ans.nb);

            $('#stat-6ans-tot').text(formatFCFA(data.stats_rapides.six_ans.total));
            $('#stat-6ans-nb').text(data.stats_rapides.six_ans.nb);

            $('#stat-glb-tot').text(formatFCFA(data.stats_rapides.global.total));
            $('#stat-glb-nb').text(data.stats_rapides.global.nb);

            // 2. Bilan Filtré
            $('#bilan-montant').text(formatFCFA(data.bilan_filtre.montant));
            $('#bilan-nb').text(data.bilan_filtre.nombre);

            // 3. DataTable
            table.clear();
            data.table_data.forEach(row => {
                table.row.add([
                    row.annee,
                    row.type_paiement,
                    `<span class="text-end d-block">${row.nombre}</span>`,
                    `<span class="text-end d-block fw-bold text-primary">${formatFCFA(row.montant)}</span>`
                ]);
            });
            table.draw();

            // 4. Graphiques
            updateCharts(data.charts);
        }

        // Fonction pour générer une couleur aléatoire
        function getRandomColor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        function updateCharts(chartsData) {
            if (barChart) barChart.destroy();
            if (pieChart) pieChart.destroy();
            if (lineChart) lineChart.destroy();

            // Bar Chart (Annuel/Périodique)
            const ctxBar = document.getElementById('barChart').getContext('2d');
            barChart = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: chartsData.annuel.labels,
                    datasets: [{
                        label: 'Montant collecté',
                        data: chartsData.annuel.montants,
                        backgroundColor: '#f6c23e'
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Pie Chart (Répartition par Motif)
            const ctxPie = document.getElementById('pieChart').getContext('2d');
            // Générer des couleurs en fonction du nombre de motifs
            let bgColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'];
            while (bgColors.length < chartsData.repartition.labels.length) {
                bgColors.push(getRandomColor());
            }
            
            pieChart = new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: chartsData.repartition.labels,
                    datasets: [{
                        data: chartsData.repartition.montants,
                        backgroundColor: bgColors.slice(0, chartsData.repartition.labels.length)
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
                    datasets: [{
                        label: 'Évolution mensuelle',
                        data: chartsData.mensuel.montants,
                        borderColor: '#4e73df',
                        fill: true,
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        tension: 0.1
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }
    </script>
</body>
</html>
