<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 
?>

<?php
// Récupération des paramètres de recherche et filtres
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_desc';

// Construction de la requête SQL
$sql = "SELECT d.*, m.nom as auteur_nom, m.prenom as auteur_prenom
        FROM documents d 
        LEFT JOIN membres m ON d.auteur_id = m.id 
        WHERE d.statut = 'actif'";

$params = [];

if (!empty($search)) {
    $sql .= " AND (d.nom_document LIKE ? OR d.description LIKE ? OR d.tags LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $sql .= " AND d.categorie = ?";
    $params[] = $category;
}

// Tri
switch ($sort) {
    case 'date_desc':
        $sql .= " ORDER BY d.date_creation DESC";
        break;
    case 'date_asc':
        $sql .= " ORDER BY d.date_creation ASC";
        break;
    case 'name_asc':
        $sql .= " ORDER BY d.nom_document ASC";
        break;
    case 'name_desc':
        $sql .= " ORDER BY d.nom_document DESC";
        break;
    default:
        $sql .= " ORDER BY d.date_creation DESC";
}

// Préparer et exécuter la requête avec les paramètres
$stmt = $bdd->prepare($sql);

// Lier les paramètres si nécessaire
if (!empty($params)) {
    $types = str_repeat('s', count($params)); // 's' pour string
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$documents = $result->fetch_all(MYSQLI_ASSOC);

// Statistiques des documents par catégorie
$categories_sql = "SELECT categorie, COUNT(*) as count FROM documents WHERE statut = 'actif' GROUP BY categorie";
$categories_stmt = $bdd->prepare($categories_sql);
$categories_stmt->execute();
$categories_result = $categories_stmt->get_result();
$categories_stats = [];

while ($row = $categories_result->fetch_assoc()) {
    $categories_stats[$row['categorie']] = $row['count'];
}

// Fonction pour obtenir l'icône selon le type de fichier
function getFileIcon($type) {
    switch (strtolower($type)) {
        case 'pdf':
            return 'bi-file-earmark-pdf-fill text-danger';
        case 'doc':
        case 'docx':
            return 'bi-file-earmark-word-fill text-primary';
        case 'xls':
        case 'xlsx':
            return 'bi-file-earmark-excel-fill text-success';
        case 'ppt':
        case 'pptx':
            return 'bi-file-earmark-ppt-fill text-warning';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
            return 'bi-file-earmark-image-fill text-info';
        case 'zip':
        case 'rar':
            return 'bi-file-earmark-zip-fill text-secondary';
        default:
            return 'bi-file-earmark-fill text-muted';
    }
}

function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' Go';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' Mo';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' Ko';
    } else {
        return $bytes . ' octets';
    }
}

function getCategoryBadge($category) {
    $badges = [
        'rapport' => 'bg-primary',
        'compte-rendu' => 'bg-info',
        'budget' => 'bg-success',
        'statuts' => 'bg-warning',
        'reglement' => 'bg-secondary',
        'presentation' => 'bg-info',
        'autre' => 'bg-light text-dark'
    ];
    return isset($badges[$category]) ? $badges[$category] : 'bg-light text-dark';
}

function getVisibilityIcon($visibility) {
    switch ($visibility) {
        case 'public':
            return 'bi-globe text-success';
        case 'membres':
            return 'bi-people text-info';
        case 'bureau':
            return 'bi-shield text-warning';
        case 'prive':
            return 'bi-lock text-danger';
        default:
            return 'bi-question text-muted';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents - Mon Association</title>
    <?php include('includes/php/include-css.php');?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4F46E5;
            --primary-hover: #4338CA;
            --secondary-color: #8B5CF6;
            --background: #F8FAFC;
            --card-bg: #FFFFFF;
            --text-primary: #1E293B;
            --text-secondary: #64748B;
            --border-color: #E2E8F0;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        body {
            background-color: var(--background);
            color: var(--text-primary);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        /* En-tête */
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 3rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 2rem 2rem;
            box-shadow: var(--shadow-md);
        }

        .page-header h1 {
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }

        .btn-upload {
            background: white;
            color: var(--primary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-upload:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            color: var(--primary-hover);
        }

        /* Barre de recherche et filtres */
        .search-filter-section {
            background: var(--card-bg);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            border: 2px solid var(--border-color);
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--background);
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 1.2rem;
        }

        .filter-select {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 0.75rem;
            background: var(--background);
            transition: all 0.3s ease;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        /* Catégories */
        .categories-section {
            background: var(--card-bg);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }

        .category-chip {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1.25rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 2px solid var(--border-color);
            background: white;
            color: var(--text-primary);
            margin: 0.25rem;
        }

        .category-chip:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .category-chip.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .category-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
            height: 24px;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            margin-left: 0.5rem;
            font-size: 0.75rem;
        }

        .category-chip.active .category-count {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Cartes de documents */
        .document-card {
            background: var(--card-bg);
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            height: 100%;
            position: relative;
        }

        .document-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .document-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary-color);
            cursor: pointer;
        }

        .document-card:hover::before {
            opacity: 1;
        }

        .document-icon-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--background);
            border-radius: 1rem;
            transition: all 0.3s ease;
        }

        .document-card:hover .document-icon-container {
            transform: scale(1.05);
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.1), rgba(139, 92, 246, 0.1));
        }

        .document-icon {
            font-size: 2.5rem;
        }

        .category-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.375rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .document-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }

        .document-description {
            color: var(--text-secondary);
            font-size: 0.875rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .document-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-top: 1px solid var(--border-color);
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .author-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .author-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--border-color);
        }

        .author-placeholder {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.75rem;
        }

        .document-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn-doc-action {
            flex: 1;
            padding: 0.625rem;
            border-radius: 0.5rem;
            border: 2px solid var(--border-color);
            background: white;
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.375rem;
        }

        .btn-doc-action:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        .btn-doc-action.primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-color: transparent;
        }

        .btn-doc-action.primary:hover {
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .btn-doc-action.secondary:hover {
            background: var(--background);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        /* Espace de stockage */
        .storage-card {
            background: var(--card-bg);
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }

        .storage-header {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.1), rgba(139, 92, 246, 0.1));
            padding: 1.25rem;
            border-bottom: 1px solid var(--border-color);
        }

        .storage-header h5 {
            margin: 0;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .progress-custom {
            height: 12px;
            background: var(--background);
            border-radius: 6px;
            overflow: hidden;
        }

        .progress-bar-custom {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 6px;
            transition: width 0.6s ease;
        }

        /* Modal */
        .modal-content {
            border-radius: 1.5rem;
            border: none;
            box-shadow: var(--shadow-lg);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            padding: 1.5rem 2rem;
            border-radius: 1.5rem 1.5rem 0 0;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.8;
        }

        .modal-body {
            padding: 2rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
            padding: 1.5rem 2rem;
        }

        /* État vide */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state i {
            font-size: 5rem;
            color: var(--text-secondary);
            opacity: 0.5;
            margin-bottom: 1.5rem;
        }

        .empty-state h3 {
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--text-secondary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header {
                padding: 2rem 0;
                border-radius: 0 0 1.5rem 1.5rem;
            }

            .page-header h1 {
                font-size: 1.75rem;
            }

            .categories-section {
                overflow-x: auto;
                white-space: nowrap;
            }

            .category-chip {
                display: inline-flex;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .document-card {
            animation: fadeInUp 0.4s ease-out;
            animation-fill-mode: both;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="page-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1>Documents partagés</h1>
                </div>
                <button class="btn btn-upload" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="bi bi-cloud-upload me-2"></i>Ajouter un document
                </button>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <!-- Barre de recherche et filtres -->
        <div class="search-filter-section">
            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input type="text" name="search" 
                                   placeholder="Rechercher un document par nom, description ou tags..." 
                                   value="<?= htmlspecialchars($search) ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="sort" class="form-select filter-select" onchange="this.form.submit()">
                            <option value="date_desc" <?= $sort == 'date_desc' ? 'selected' : '' ?>>Plus récent</option>
                            <option value="date_asc" <?= $sort == 'date_asc' ? 'selected' : '' ?>>Plus ancien</option>
                            <option value="name_asc" <?= $sort == 'name_asc' ? 'selected' : '' ?>>Nom (A-Z)</option>
                            <option value="name_desc" <?= $sort == 'name_desc' ? 'selected' : '' ?>>Nom (Z-A)</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-doc-action primary w-100">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Catégories -->
        <div class="categories-section">
            <a href="?<?= http_build_query(array_merge($_GET, ['category' => ''])) ?>" 
               class="category-chip <?= empty($category) ? 'active' : '' ?>">
                <span>Tous</span>
                <span class="category-count"><?= count($documents) ?></span>
            </a>
            <?php
            $categories_labels = [
                'rapport' => 'Rapports',
                'compte-rendu' => 'Comptes-rendus',
                'budget' => 'Budget',
                'statuts' => 'Statuts',
                'reglement' => 'Règlements',
                'presentation' => 'Présentations',
                'autre' => 'Autres'
            ];
            foreach ($categories_labels as $cat => $label):
                $count = isset($categories_stats[$cat]) ? $categories_stats[$cat] : 0;
                if ($count > 0):
            ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['category' => $cat])) ?>" 
               class="category-chip <?= $category == $cat ? 'active' : '' ?>">
                <span><?= $label ?></span>
                <span class="category-count"><?= $count ?></span>
            </a>
            <?php endif; endforeach; ?>
        </div>
        
        <!-- Documents en grille -->
        <?php if (count($documents) > 0): ?>
        <div class="row g-4 mb-4">
            <?php foreach ($documents as $document): ?>
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <div class="document-card">
                    <div class="card-body p-3">
                        <span class="badge <?= getCategoryBadge($document['categorie']) ?> category-badge">
                            <?= safe_safe_ucfirst($document['categorie']) ?>
                        </span>
                        
                        <div class="document-icon-container">
                            <i class="bi <?= getFileIcon($document['type_fichier']) ?> document-icon"></i>
                        </div>
                        
                        <h6 class="document-title"><?= htmlspecialchars($document['nom_document']) ?></h6>
                        
                        <?php if ($document['description']): ?>
                        <p class="document-description">
                            <?= htmlspecialchars(substr($document['description'], 0, 80)) ?>
                            <?= strlen($document['description']) > 80 ? '...' : '' ?>
                        </p>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi <?= getVisibilityIcon($document['visibilite']) ?>"></i>
                                <small class="text-muted"><?= formatFileSize($document['taille_fichier']) ?></small>
                            </div>
                            <small class="text-muted">v<?= $document['version'] ?></small>
                        </div>
                        
                        <div class="document-meta">
                            <div class="author-info">
                                <?php if ($document['auteur_prenom']): ?>
                                <div class="author-placeholder">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <?php endif; ?>
                                <span><?= htmlspecialchars($document['auteur_prenom'] . ' ' . substr($document['auteur_nom'], 0, 1) . '.') ?></span>
                            </div>
                            <span><?= date('d/m/Y', strtotime($document['date_creation'])) ?></span>
                        </div>
                        
                        <div class="document-actions">
                            <a href="<?= htmlspecialchars($document['chemin_fichier']) ?>" 
                               class="btn-doc-action primary" target="_blank">
                                <i class="bi bi-eye-fill"></i>
                                <span>Voir</span>
                            </a>
                            <a href="<?= htmlspecialchars($document['chemin_fichier']) ?>" 
                               class="btn-doc-action secondary" download>
                                <i class="bi bi-download"></i>
                            </a>
                            <button class="btn-doc-action secondary" 
                                    onclick="shareDocument(<?= $document['id'] ?>)">
                                <i class="bi bi-share"></i>
                            </button>
                        </div>
                        
                        <?php if ($document['telechargements'] > 0): ?>
                        <div class="mt-2 text-center">
                            <small class="text-muted">
                                <i class="bi bi-download"></i> <?= $document['telechargements'] ?> téléchargement<?= $document['telechargements'] > 1 ? 's' : '' ?>
                            </small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-folder2-open"></i>
            <h3>Aucun document trouvé</h3>
            <p class="text-muted">Essayez de modifier vos critères de recherche ou ajoutez un nouveau document</p>
        </div>
        <?php endif; ?>
        
        <!-- Espace de stockage -->
        <div class="storage-card">
            <div class="storage-header">
                <h5><i class="bi bi-hdd-stack-fill"></i> Espace de stockage</h5>
            </div>
            <div class="card-body p-4">
                <?php
                $total_size_sql = "SELECT SUM(taille_fichier) as total FROM documents WHERE statut = 'actif'";
                $total_size_stmt = $bdd->prepare($total_size_sql);
                $total_size_stmt->execute();
                $total_size_result = $total_size_stmt->get_result();
                $row = $total_size_result->fetch_assoc();
                $total_used = $row['total'] ?? 0;
                $total_available = 20 * 1024 * 1024 * 1024;
                $percentage = ($total_used / $total_available) * 100;
                ?>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold"><?= formatFileSize($total_used) ?> utilisés</span>
                        <span class="text-muted">sur <?= formatFileSize($total_available) ?></span>
                    </div>
                    <div class="progress-custom">
                        <div class="progress-bar-custom" style="width: <?= min($percentage, 100) ?>%;"></div>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted"><?= round($percentage, 1) ?>% utilisé</small>
                    </div>
                </div>

                <button class="btn btn-doc-action primary w-100">
                    <i class="bi bi-arrow-up-circle"></i>
                    <span>Augmenter l'espace de stockage</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal d'upload -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-cloud-upload me-2"></i>Ajouter un document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Nom du document</label>
                            <input type="text" class="form-control" name="nom_document" 
                                   placeholder="Ex: Rapport d'activité 2024" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" 
                                      placeholder="Décrivez brièvement le contenu du document..."></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Catégorie</label>
                                <select class="form-select" name="categorie" required>
                                    <option value="" disabled selected>Sélectionner...</option>
                                    <option value="rapport">Rapport</option>
                                    <option value="compte-rendu">Compte-rendu</option>
                                    <option value="budget">Budget</option>
                                    <option value="statuts">Statuts</option>
                                    <option value="reglement">Règlement</option>
                                    <option value="presentation">Présentation</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Visibilité</label>
                                <select class="form-select" name="visibilite" required>
                                    <option value="membres">Membres uniquement</option>
                                    <option value="public">Public</option>
                                    <option value="bureau">Bureau seulement</option>
                                    <option value="prive">Privé</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Fichier</label>
                            <div class="position-relative">
                                <input type="file" class="form-control" name="fichier" id="fileInput" required>
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-info-circle"></i> Formats acceptés: PDF, DOC, XLS, PPT, Images, ZIP
                                </small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tags (optionnel)</label>
                            <input type="text" class="form-control" name="tags" 
                                   placeholder="budget, 2024, finances (séparés par des virgules)">
                            <small class="text-muted">Ajoutez des mots-clés pour faciliter la recherche</small>
                        </div>

                        <!-- Prévisualisation du fichier -->
                        <div id="filePreview" class="alert alert-info d-none">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-earmark-fill fs-3 me-3"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold" id="fileName"></div>
                                    <small id="fileSize"></small>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="button" class="btn btn-doc-action secondary flex-fill" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i> Annuler
                            </button>
                            <button type="submit" class="btn btn-doc-action primary flex-fill">
                                <i class="bi bi-cloud-upload"></i> Ajouter le document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/php/footer.php');?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Prévisualisation du fichier
        document.getElementById('fileInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('filePreview');
            
            if (file) {
                preview.classList.remove('d-none');
                document.getElementById('fileName').textContent = file.name;
                document.getElementById('fileSize').textContent = formatBytes(file.size);
            } else {
                preview.classList.add('d-none');
            }
        });

        function formatBytes(bytes) {
            if (bytes >= 1073741824) {
                return (bytes / 1073741824).toFixed(2) + ' Go';
            } else if (bytes >= 1048576) {
                return (bytes / 1048576).toFixed(2) + ' Mo';
            } else if (bytes >= 1024) {
                return (bytes / 1024).toFixed(2) + ' Ko';
            } else {
                return bytes + ' octets';
            }
        }

        // Fonction de partage
        function shareDocument(documentId) {
            const url = window.location.origin + '/document.php?id=' + documentId;
            
            if (navigator.share) {
                navigator.share({
                    title: 'Document partagé',
                    text: 'Consultez ce document',
                    url: url
                }).catch(err => console.log('Erreur de partage:', err));
            } else {
                navigator.clipboard.writeText(url).then(() => {
                    // Afficher une notification
                    showNotification('Lien copié dans le presse-papiers!');
                }).catch(err => {
                    console.error('Erreur lors de la copie:', err);
                });
            }
        }

        // Fonction de notification
        function showNotification(message) {
            const notification = document.createElement('div');
            notification.className = 'alert alert-success position-fixed bottom-0 end-0 m-3';
            notification.style.zIndex = '9999';
            notification.innerHTML = `
                <i class="bi bi-check-circle me-2"></i>${message}
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Gestion de l'upload
        // === UPLOAD AJAX ===
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const btn = this.querySelector('button[type="submit"]');
    const original = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi...';

    fetch('upload_document.php', {
        method: 'POST',
        body: formData
    })
    .then(r => {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('Document ajouté !');
            setTimeout(() => location.reload(), 1500);
        } else {
            alert('Erreur : ' + data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Erreur réseau. Vérifiez la console (F12).');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = original;
    });
});

        // Animation d'apparition des cartes
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.document-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.05}s`;
            });
        });
    </script>
</body>
</html>