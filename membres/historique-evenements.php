<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Événements - Mon Association</title>

    <?php include('includes/php/include-css.php');?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .event-card {
            transition: all 0.3s ease;
            border-radius: 15px;
            overflow: hidden;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .event-date {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 10px 0 0 10px;
        }
        .search-box {
            border-radius: 20px;
            border: 1px solid #dee2e6;
            padding: 10px 15px;
        }
        .past-event {
            opacity: 0.9;
        }
        .past-event:hover {
            opacity: 1;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold">Historique des événements</h1>
            <a href="evenements.php" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Retour aux événements
            </a>
        </div>
        
        <!-- Barre de recherche -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="input-group search-box">
                    <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchHistoryInput" class="form-control border-0" placeholder="Rechercher dans l'historique..." onkeyup="filterHistory()">
                </div>
            </div>
        </div>
        
        <!-- Liste des événements passés -->
        <div class="row g-4" id="historyContainer">
            <?php
$queryPast = "SELECT * FROM evenements 
              WHERE date_debut < NOW()
              ORDER BY date_debut DESC";

$resultPast = mysqli_query($bdd, $queryPast) or die("Erreur requête événements passés");

while ($event = mysqli_fetch_array($resultPast)) {
    $ds = $event['date_debut'] ?? '';
    if ($ds === null || $ds === '' || trim($ds) === '') {
        continue;
    }
    $df = (!empty($event['date_fin']) && $event['date_fin'] !== null) ? $event['date_fin'] : $ds;
    $dateDebut = new DateTime((string)$ds);
    $dateFin = new DateTime((string)$df);

    $category = 'Important';
    $badgeClass = 'bg-primary';

    $titreLower = strtolower((string)($event['titre'] ?? ''));
    if (strpos($titreLower, 'atelier') !== false) {
        $category = 'Loisir';
        $badgeClass = 'bg-success';
    } elseif (strpos($titreLower, 'sortie') !== false) {
        $category = 'Sortie';
        $badgeClass = 'bg-info';
    }
    ?>

    <div class="col-md-6 col-lg-4 history-item">
        <div class="event-card card h-100 past-event">
            <div class="row g-0 h-100">
                <div class="col-3 d-flex align-items-center justify-content-center event-date">
                    <div>
                        <div class="fs-4 fw-bold"><?= $dateDebut->format('d') ?></div>
                        <div class="text-uppercase"><?= $dateDebut->format('M') ?></div>
                    </div>
                </div>
                <div class="col-9">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars((string)($event['titre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h5>
                        <p class="card-text text-muted small">
                            <i class="bi bi-clock"></i> <?= $dateDebut->format('H:i') ?> - <?= $dateFin->format('H:i') ?><br>
                            <i class="bi bi-geo-alt"></i> <?= htmlspecialchars((string)($event['lieu'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($category) ?></span>
                                <span class="badge bg-secondary ms-1">Terminé</span>
                            </div>
                            <button class="btn btn-sm btn-outline-primary rounded-pill">
                                <?php
                                if (strpos($titreLower, 'atelier') !== false || strpos($titreLower, 'sortie') !== false) {
                                    echo 'Photos <i class="bi bi-images"></i>';
                                } else {
                                    echo 'Détails <i class="bi bi-arrow-right"></i>';
                                }
                                ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php } ?>

        </div>
    </div>

    <?php include('includes/php/footer.php');?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fonction de filtrage pour l'historique
        function filterHistory() {
            const input = document.getElementById('searchHistoryInput');
            const filter = input.value.toUpperCase();
            const items = document.querySelectorAll('.history-item');
            
            items.forEach(item => {
                const title = item.querySelector('.card-title').textContent.toUpperCase();
                if (title.includes(filter)) {
                    item.style.display = "";
                } else {
                    item.style.display = "none";
                }
            });
        }
    </script>
</body>
</html>