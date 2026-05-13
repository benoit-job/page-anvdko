<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 
?>
<?php
 $membre_id = isset($_GET['id']) ? intval(htmlspecialchars(trim(crypt_decrypt_chaine($_GET['id'], 'D')))) : (isset($_SESSION['membre']['id']) ? $_SESSION['membre_id'] : null);
//     reload_current_page(); 

//  if (isset($_GET["id"])) {
//     $membre_id = strip_tags(htmlspecialchars(trim(crypt_decrypt_chaine($_GET["id"], 'D'))));
//     reload_current_page(); 
// }

// Requête pour récupérer les infos du membre
$query = "SELECT *, CONCAT(UPPER(nom), ' ', UPPER(prenom)) AS nom_prenom 
          FROM membres 
          WHERE id = $membre_id";
$result = mysqli_query($bdd, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Membre non trouvé.");
}

$membre = mysqli_fetch_assoc($result);

// Gestion de l'image
$logo = isset($membre["logo"]) && !empty($membre["logo"]) ? $membre["logo"] : 'no_image.jpg';
$imagePath = "../fichiers/uploads/" . $logo;
if ($logo === 'no_image.jpg') {
    $imagePath = "../fichiers/images/no_image.jpg";
}

// Formatage des dates
$date_adhesion = '----';
$annee_adhesion = '----';

if (!empty($membre["date_adhesion"])) {
    setlocale(LC_TIME, 'fr_FR.utf8', 'fra'); // Pour les mois en français
    $timestamp = strtotime($membre["date_adhesion"]);
    
    if ($timestamp) {
        $date_adhesion = strftime('%d %B %Y', $timestamp); 
        $annee_adhesion = date('Y', $timestamp);
    }
}


// Statistiques
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM inscription_evenement WHERE membre_id = $membre_id) as nb_evenements,
    (SELECT COUNT(*) FROM documents) as nb_documents,
    (SELECT COUNT(*) FROM membres WHERE id != $membre_id) as nb_contacts";
$stats_result = mysqli_query($bdd, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

// Activités récentes
$activity_query = "SELECT 
    'document' as type, 
    nom_document as titre, 
    date_creation as date_debut,
    'bi-file-earmark-text-fill' as icone,
    'text-success' as couleur
    FROM documents 
    WHERE auteur_id = $membre_id
    
    UNION ALL
    
    SELECT 
    'evenement' as type,
    e.titre as titre,
    e.date_debut as date_debut,
    'bi-calendar-event-fill' as icone,
    'text-primary' as couleur
    FROM evenements e
    JOIN inscription_evenement ie ON e.id = ie.evenement_id
    WHERE ie.membre_id = $membre_id
    
    ORDER BY date_debut DESC 
    LIMIT 5";

$activity_result = mysqli_query($bdd, $activity_query);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil de <?php echo htmlspecialchars($membre['nom_prenom']); ?> - Mon Association</title>

   <?php include('includes/php/include-css.php');?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .profile-avatar {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .profile-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .stat-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            border: none;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .nav-pills .nav-link.active {
            background-color: #0d6efd;
            font-weight: 500;
        }
        .nav-pills .nav-link {
            color: #495057;
            border-radius: 8px;
        }
        .badge-role {
            font-size: 0.8rem;
            padding: 5px 10px;
        }
        .edit-icon {
            position: absolute;
            right: 15px;
            top: 15px;
            opacity: 0;
            transition: all 0.3s;
        }
        .profile-section:hover .edit-icon {
            opacity: 1;
        }
        .activity-item {
            border-left: 3px solid #0d6efd;
            transition: all 0.2s;
        }
        .activity-item:hover {
            background-color: #f8f9fa;
        }
    </style>
    
<!-- CSS personnalisé pour améliorer le design -->
<style>
    /* Style général */
    .profile-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 500;
        padding: 0.75rem 1.25rem;
        transition: all 0.3s;
    }
    
    .profile-tabs .nav-link.active {
        color: #0d6efd;
        background-color: transparent;
        border-bottom: 3px solid #0d6efd;
    }
    
    .profile-tabs .nav-link:hover:not(.active) {
        color: #0d6efd;
    }
    
    /* Cartes de profil */
    .profile-card {
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .profile-card .card-header {
        padding: 1rem 1.5rem;
    }
    
    /* Sections d'information */
    .info-item {
        padding: 0.5rem 0;
    }
    
    .info-label {
        display: block;
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        font-size: 1rem;
        font-weight: 500;
        margin-bottom: 0;
    }
    
    /* Boutons d'édition */
    .edit-btn {
        position: absolute;
        top: 1rem;
        right: 1rem;
        padding: 0.25rem 0.75rem;
    }
    
    /* Compétences */
    .skills-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .skill-badge {
        background-color: #e9ecef;
        color: #495057;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 0.85rem;
    }
    
    /* Activités */
    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 0.75rem;
        border-radius: 0.5rem;
        transition: background-color 0.2s;
    }
    
    .activity-item:hover {
        background-color: #f8f9fa;
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
    }
    
    .activity-content {
        flex-grow: 1;
    }
    
    .activity-title {
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
        font-weight: 500;
    }
    
    .activity-date {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .no-activity {
        color: #6c757d;
    }
    
    .no-activity-icon {
        font-size: 2rem;
        color: #dee2e6;
    }
    
    /* Paramètres */
    .settings-section {
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }
    
    .settings-title {
        font-size: 1rem;
        margin-bottom: 1rem;
        color: #495057;
    }
    
    .setting-item {
        padding: 0.5rem 0;
    }
    
    .settings-actions {
        margin-top: 2rem;
    }
</style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <!-- En-tête du profil -->
        <div class="profile-card card mb-4 overflow-hidden">
            <div class="card-header bg-primary text-white position-relative">
                <div class="cover-photo" style="height: 150px; background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);"></div>
                <div class="position-absolute top-100 start-50 translate-middle" style="transform: translate(-50%, -50%);">
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Avatar" class="profile-avatar">
                </div>
            </div>
            <div class="card-body text-center mt-5 pt-4">
                <h2 class="mb-1"><?php echo htmlspecialchars($membre['nom_prenom']); ?></h2>
                <p class="text-muted mb-2"><?php echo htmlspecialchars($membre['email'] ?? 'Email non renseigné'); ?></p>
                <span class="badge bg-primary badge-role mb-3"><?php echo htmlspecialchars($membre['poste_occupe'] ?? 'Membre'); ?></span>
                <div class="d-flex justify-content-center gap-3 mb-3">
                    <?php if (isset($_SESSION['membre_id']) && $_SESSION['membre_id'] == $membre_id): ?>
                    <button class="btn btn-outline-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="bi bi-pencil"></i> Modifier
                    </button>
                    <?php endif; ?>
                    <button class="btn btn-primary rounded-pill px-4" onclick="partagerProfil()">
                        <i class="bi bi-share"></i> Partager
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="stat-card card h-100 bg-white">
                    <div class="card-body text-center">
                        <div class="text-primary mb-2">
                            <i class="bi bi-calendar-event fs-1"></i>
                        </div>
                        <h3 class="mb-1"><?php echo $stats['nb_evenements'] ?? 0; ?></h3>
                        <p class="text-muted mb-0">Événements</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card card h-100 bg-white">
                    <div class="card-body text-center">
                        <div class="text-success mb-2">
                            <i class="bi bi-file-earmark-text fs-1"></i>
                        </div>
                        <h3 class="mb-1"><?php echo $stats['nb_documents'] ?? 0; ?></h3>
                        <p class="text-muted mb-0">Documents</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card card h-100 bg-white">
                    <div class="card-body text-center">
                        <div class="text-warning mb-2">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                        <h3 class="mb-1"><?php echo $stats['nb_contacts'] ?? 0; ?></h3>
                        <p class="text-muted mb-0">Contacts</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation onglets - version améliorée -->
<ul class="nav nav-tabs mb-4 profile-tabs" id="profileTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="infos-tab" data-bs-toggle="pill" data-bs-target="#infos" type="button">
            <i class="bi bi-person-fill me-2"></i>Informations
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="activity-tab" data-bs-toggle="pill" data-bs-target="#activity" type="button">
            <i class="bi bi-activity me-2"></i>Activité
        </button>
    </li>
    <?php if (isset($_SESSION['membre']['id']) && $_SESSION['membre']['id'] == $membre_id): ?>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="settings-tab" data-bs-toggle="pill" data-bs-target="#settings" type="button">
            <i class="bi bi-gear-fill me-2"></i>Paramètres
        </button>
    </li>
    <?php endif; ?>
</ul>

    <!-- Contenu des onglets - version améliorée -->
    <div class="tab-content" id="profileTabsContent">
        <!-- Onglet Informations -->
        <div class="tab-pane fade show active" id="infos" role="tabpanel">
            
            <div class="card profile-card border-0 shadow-sm">
        <div class="card-header bg-primary text-white rounded-top">
            <h5 class="mb-0"><i class="bi bi-file-person-fill me-2"></i>À propos</h5>
        </div>
        <div class="card-body profile-section position-relative">
            <?php if (isset($_SESSION['membre']['id']) && $_SESSION['membre']['id'] == $membre_id): ?>
            <a href="#" class="btn btn-sm btn-outline-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editAboutModal">
                <i class="bi bi-pencil-square"></i> Modifier
            </a>
            <?php endif; ?>
            
            <div class="row">
                <!-- Colonne de gauche - Informations personnelles -->
                <div class="col-md-6">
                    <div class="personal-info mb-4">
                        <h6 class="info-title"><i class="bi bi-person-vcard me-2"></i>Informations personnelles</h6>
                        <ul class="list-unstyled">
                            <?php if (!empty($membre['profession'])): ?>
                            <li class="mb-2">
                                <strong>Profession:</strong> <?php echo htmlspecialchars($membre['profession']); ?>
                            </li>
                            <?php endif; ?>
                            
                            <?php if (!empty($membre['date_naissance'])): ?>
                            <li class="mb-2">
                                <strong>Né(e) le:</strong> <?php echo date('d/m/Y', strtotime($membre['date_naissance'])); ?>
                                <?php if (!empty($membre['lieu_naissance'])): ?>
                                à <?php echo htmlspecialchars($membre['lieu_naissance']); ?>
                                <?php endif; ?>
                            </li>
                            <?php endif; ?>
                            
                            <?php if (!empty($membre['nationnalite'])): ?>
                            <li class="mb-2">
                                <strong>Nationalité:</strong> <?php echo htmlspecialchars($membre['nationnalite']); ?>
                            </li>
                            <?php endif; ?>
                            
                            <?php if (!empty($membre['ville_commune'])): ?>
                            <li class="mb-2">
                                <strong>Ville:</strong> <?php echo htmlspecialchars($membre['ville_commune']); ?>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <div class="contact-info mb-4">
                        <h6 class="info-title"><i class="bi bi-telephone me-2"></i>Contact</h6>
                        <ul class="list-unstyled">
                            <?php if (!empty($membre['email'])): ?>
                            <li class="mb-2">
                                <strong>Email:</strong> <?php echo htmlspecialchars($membre['email']); ?>
                            </li>
                            <?php endif; ?>
                            
                            <?php if (!empty($membre['num_telephone'])): ?>
                            <li class="mb-2">
                                <strong>Téléphone:</strong> <?php echo htmlspecialchars($membre['num_telephone']); ?>
                            </li>
                            <?php endif; ?>
                            
                            <?php if (!empty($membre['lieu_residence'])): ?>
                            <li class="mb-2">
                                <strong>Adresse:</strong> <?php echo htmlspecialchars($membre['lieu_residence']); ?>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Colonne de droite - Description et compétences -->
                <div class="col-md-6">
                    <div class="about-content mb-4">
                        <h6 class="info-title"><i class="bi bi-info-circle me-2"></i>Description</h6>
                        <p class="about-text"><?php echo htmlspecialchars($membre['description'] ?? 'Membre actif de l\'association depuis ' . $annee_adhesion . '.'); ?></p>
                    </div>
                    
                    <div class="association-info mb-4">
                        <h6 class="info-title"><i class="bi bi-building me-2"></i>Dans l'association</h6>
                        <ul class="list-unstyled">
                            <?php if (!empty($membre['poste_occupe'])): ?>
                            <li class="mb-2">
                                <strong>Poste:</strong> <?php echo htmlspecialchars($membre['poste_occupe']); ?>
                            </li>
                            <?php endif; ?>
                            
                            <?php if (!empty($membre['date_adhesion'])): ?>
                            <li class="mb-2">
                                <strong>Membre depuis:</strong> <?php echo date('F Y', strtotime($membre['date_adhesion'])); ?>
                            </li>
                            <?php endif; ?>
                            
                            <?php if (!empty($membre['num_adhesion'])): ?>
                            <li class="mb-2">
                                <strong>N° adhésion:</strong> <?php echo htmlspecialchars($membre['num_adhesion']); ?>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <div class="skills-section">
                        <h6 class="skills-title"><i class="bi bi-tools me-2"></i>Compétences</h6>
                        <div class="skills-container">
                            <?php
                            $competences = explode(',', $membre['competences'] ?? 'Gestion,Communication,Organisation');
                            foreach ($competences as $competence) {
                                $competence = trim($competence);
                                if (!empty($competence)) {
                                    echo '<span class="badge bg-primary me-1 mb-1">' . htmlspecialchars($competence) . '</span>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    
    <!-- Onglet Activité -->
    <div class="tab-pane fade" id="activity" role="tabpanel">
        <div class="card profile-card border-0 shadow-sm">
            <div class="card-header bg-primary text-white rounded-top">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Activité récente</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($activity_result) > 0): ?>
                    <div class="activity-list">
                        <?php while ($activity = mysqli_fetch_assoc($activity_result)): ?>
                        <div class="activity-item">
                            <div class="activity-icon bg-<?php echo $activity['couleur']; ?>">
                                <i class="bi <?php echo $activity['icone']; ?>"></i>
                            </div>
                            <div class="activity-content">
                                <h6 class="activity-title">
                                    <?php if ($activity['type'] == 'document'): ?>
                                        A modifié le document "<?php echo htmlspecialchars($activity['titre']); ?>"
                                    <?php else: ?>
                                        A participé à l'événement "<?php echo htmlspecialchars($activity['titre']); ?>"
                                    <?php endif; ?>
                                </h6>
                                <span class="activity-date"><?php echo date('d F Y, H:i', strtotime($activity['date_debut'])); ?></span>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="no-activity text-center py-4">
                        <i class="bi bi-activity no-activity-icon"></i>
                        <p class="text-muted mt-2">Aucune activité récente</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Onglet Paramètres -->
    <?php if (isset($_SESSION['membre']['id']) && $_SESSION['membre']['id'] == $membre_id): ?>
    <div class="tab-pane fade" id="settings" role="tabpanel">
    <div class="card profile-card border-0 shadow-sm">
        <div class="card-header bg-primary text-white rounded-top">
            <h5 class="mb-0"><i class="bi bi-sliders me-2"></i>Paramètres du compte</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="update_settings.php">
                <div class="row">
                    <!-- Colonne de gauche - Notifications -->
                    <div class="col-md-6">
                        <div class="settings-section mb-4 p-3 border rounded">
                            <h6 class="settings-title text-primary mb-3">
                                <i class="bi bi-bell-fill me-2"></i>Préférences de notifications
                            </h6>
                            
                            <div class="form-check form-switch mb-3 setting-item">
                                <input class="form-check-input" type="checkbox" id="notifEmail" name="notif_email" <?php echo ($membre['notif_email'] ?? 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="notifEmail">
                                    <i class="bi bi-envelope-fill me-1"></i> Recevoir les notifications par email
                                </label>
                            </div>
                            
                            <div class="form-check form-switch mb-3 setting-item">
                                <input class="form-check-input" type="checkbox" id="notifSMS" name="notif_sms" <?php echo ($membre['notif_sms'] ?? 0) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="notifSMS">
                                    <i class="bi bi-phone-fill me-1"></i> Recevoir les notifications par SMS
                                </label>
                            </div>
                            
                            <div class="alert alert-info mt-3 p-2 small">
                                <i class="bi bi-info-circle-fill me-1"></i> Vous pouvez ajuster vos préférences de notification à tout moment.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Colonne de droite - Confidentialité -->
                    <div class="col-md-6">
                        <div class="settings-section mb-4 p-3 border rounded">
                            <h6 class="settings-title text-primary mb-3">
                                <i class="bi bi-shield-lock-fill me-2"></i>Paramètres de confidentialité
                            </h6>
                            
                            <div class="mb-3 setting-item">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="privacy" id="privacyPublic" value="public" <?php echo ($membre['privacy'] ?? 'public') == 'public' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="privacyPublic">
                                        <i class="bi bi-globe me-1"></i> Profil public
                                    </label>
                                    <small class="form-text text-muted d-block">Visible par tous les membres de l'association</small>
                                </div>
                            </div>
                            
                            <div class="mb-3 setting-item">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="privacy" id="privacyPrivate" value="private" <?php echo ($membre['privacy'] ?? 'public') == 'private' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="privacyPrivate">
                                        <i class="bi bi-lock-fill me-1"></i> Profil privé
                                    </label>
                                    <small class="form-text text-muted d-block">Visible uniquement par les administrateurs</small>
                                </div>
                            </div>
                            
                            <div class="alert alert-warning mt-3 p-2 small">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Les changements de confidentialité peuvent prendre quelques heures à s'appliquer.
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions en bas de page -->
                <div class="settings-actions mt-4">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save-fill me-2"></i>Enregistrer les modifications
                        </button>
                        
                        <div class="d-flex justify-content-between mt-2">
                            <a href="change_password.php" class="btn btn-outline-secondary">
                                <i class="bi bi-key-fill me-1"></i> Changer le mot de passe
                            </a>
                            <a href="logout.php" class="btn btn-outline-danger">
                                <i class="bi bi-box-arrow-right me-1"></i> Déconnexion
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
    <?php endif; ?>
</div>

    </div>

    <?php include('includes/php/footer.php');?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <button class="btn btn-primary rounded-pill px-4" onclick="partagerProfil()">
    <i class="bi bi-share me-1"></i> Partager
</button>

<script>
function partagerProfil() {
    const url = window.location.href;
    const nomComplet = "<?php echo addslashes(htmlspecialchars($membre['nom_prenom'] ?? 'Membre')); ?>";
    const titre = `Profil de ${nomComplet} - Notre Association`;
    
    // Vérifie si l'API Web Share est disponible (mobile principalement)
    if (navigator.share) {
        navigator.share({
            title: titre,
            text: `Découvrez le profil de ${nomComplet} sur notre plateforme`,
            url: url
        }).catch(err => {
            console.error('Erreur de partage:', err);
            copierLien(url);
        });
    } else {
        // Fallback pour les navigateurs sans Web Share API
        copierLien(url);
    }
}

function copierLien(url) {
    navigator.clipboard.writeText(url).then(() => {
        // Utilisation de Toast au lieu d'alert()
        afficherToast('Lien du profil copié dans le presse-papiers !', 'top-right', 'success', 3000);
    }).catch(err => {
        console.error('Erreur lors de la copie:', err);
        afficherToast('Impossible de copier le lien', 'top-right', 'danger', 3000);
        
        // Fallback pour les vieux navigateurs
        const input = document.createElement('input');
        input.value = url;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        afficherToast('Lien copié !', 'top-right', 'success', 3000);
    });
}

// Fonction générique pour afficher des notifications (à adapter selon votre framework)
function afficherToast(message, position, type, duration) {
    // Implémentation basique - à remplacer par votre système de notifications
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, duration);
}
</script>
</body>
</html>