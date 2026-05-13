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
    <title>Membres - Mon Association</title>
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
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .page-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            margin: 0.5rem 0 0 0;
        }

        /* Barre de recherche */
        .search-wrapper {
            background: var(--card-bg);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }

        .search-box {
            position: relative;
            max-width: 600px;
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

        /* Navigation alphabétique */
        .alphabet-nav {
            background: var(--card-bg);
            border-radius: 1rem;
            padding: 1rem;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 20px;
            max-height: calc(100vh - 40px);
            overflow-y: auto;
        }

        .alphabet-nav::-webkit-scrollbar {
            width: 4px;
        }

        .alphabet-nav::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 2px;
        }

        .alphabet-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            margin: 0 auto 0.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            background: var(--background);
        }

        .alphabet-link:hover {
            background: var(--primary-color);
            color: white;
            transform: translateX(3px);
        }

        .alphabet-link.active {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 4px 8px rgba(79, 70, 229, 0.3);
        }

        /* Version mobile de la navigation alphabétique */
        .alphabet-mobile {
            background: var(--card-bg);
            border-radius: 1rem;
            padding: 1rem;
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .alphabet-mobile::-webkit-scrollbar {
            display: none;
        }

        .alphabet-mobile .alphabet-link {
            display: inline-flex;
            margin: 0 0.25rem;
            min-width: 36px;
        }

        /* Cartes des membres */
        .member-card {
            background: var(--card-bg);
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            height: 100%;
            position: relative;
        }

        .member-card::before {
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

        .member-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary-color);
        }

        .member-card:hover::before {
            opacity: 1;
        }

        .card-body {
            padding: 1.5rem;
            text-align: center;
        }

        .member-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--background);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }

        .member-card:hover .member-avatar {
            transform: scale(1.05);
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .member-since {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-bottom: 0.75rem;
        }

        .role-badge {
            display: inline-block;
            padding: 0.375rem 0.875rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-action {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            border: 2px solid var(--border-color);
            background: white;
            color: var(--text-secondary);
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        .btn-action.whatsapp:hover {
            background: #25D366;
            border-color: #25D366;
            color: white;
        }

        .btn-action.phone:hover {
            background: #3B82F6;
            border-color: #3B82F6;
            color: white;
        }

        .btn-action.sms:hover {
            background: #F59E0B;
            border-color: #F59E0B;
            color: white;
        }

        .btn-action.profile:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        /* Modal */
        .modal-content {
            border-radius: 1.5rem;
            border: none;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            padding: 2rem;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.8;
            transition: opacity 0.2s;
        }

        .modal-header .btn-close:hover {
            opacity: 1;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 6px solid white;
            box-shadow: var(--shadow-md);
            margin: -4rem auto 1.5rem;
            display: block;
        }

        .modal-name {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .modal-subtitle {
            color: var(--text-secondary);
            font-size: 1rem;
            text-align: center;
            margin-bottom: 1rem;
        }

        .modal-role-badge {
            display: inline-block;
            padding: 0.5rem 1.25rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 2rem;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 2rem;
        }

        .info-section {
            background: var(--background);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .info-item {
            display: flex;
            align-items: start;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-item i {
            color: var(--primary-color);
            font-size: 1.25rem;
            margin-right: 1rem;
            margin-top: 0.125rem;
        }

        .info-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .info-value {
            color: var(--text-secondary);
            word-break: break-word;
        }

        .modal-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .btn-modal-action {
            flex: 1;
            max-width: 200px;
            padding: 0.875rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            border: none;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-modal-action i {
            font-size: 1.25rem;
        }

        .btn-whatsapp {
            background: #25D366;
            color: white;
        }

        .btn-whatsapp:hover {
            background: #20BA5A;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(37, 211, 102, 0.3);
        }

        .btn-call {
            background: #3B82F6;
            color: white;
        }

        .btn-call:hover {
            background: #2563EB;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.3);
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

            .page-header p {
                font-size: 0.95rem;
            }

            .modal-body {
                padding: 1.5rem;
            }

            .modal-avatar {
                width: 100px;
                height: 100px;
                margin-top: -3rem;
            }

            .modal-actions {
                flex-direction: column;
            }

            .btn-modal-action {
                max-width: 100%;
            }
        }

        /* Animation d'apparition */
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

        .member-item {
            animation: fadeInUp 0.4s ease-out;
            animation-fill-mode: both;
        }

        .member-item:nth-child(1) { animation-delay: 0.05s; }
        .member-item:nth-child(2) { animation-delay: 0.1s; }
        .member-item:nth-child(3) { animation-delay: 0.15s; }
        .member-item:nth-child(4) { animation-delay: 0.2s; }
    </style>
</head>
<body>
    <!-- En-tête de la page -->
    <div class="page-header">
        <div class="container">
            <h1 class="text-center">Annuaire des membres</h1>
            <p class="text-center">Découvrez et contactez tous les membres de notre association</p>
        </div>
    </div>

    <div class="container pb-5">
        <!-- Barre de recherche -->
        <div class="search-wrapper">
            <div class="search-box mx-auto">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Rechercher un membre par nom...">
            </div>
        </div>
        
        <?php
            // Récupérer les initiales uniques des membres
            $queryInitials = "SELECT DISTINCT UPPER(LEFT(nom, 1)) AS initial FROM membres ORDER BY initial ASC";
            $resultInitials = mysqli_query($bdd, $queryInitials) or die("Requête non conforme");

            $initials = [];
            while ($row = mysqli_fetch_assoc($resultInitials)) {
                $initials[] = $row['initial'];
            }
        ?>

        <div class="row">
            <!-- Navigation alphabétique - Version Mobile -->
            <div class="col-12 d-md-none">
                <div class="alphabet-mobile">
                    <a href="#" class="alphabet-link active" data-letter="all">Tous</a>
                    <?php foreach ($initials as $letter): ?>
                        <a href="#" class="alphabet-link" data-letter="<?= $letter ?>"><?= $letter ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Navigation alphabétique - Version Desktop -->
            <div class="col-md-2 d-none d-md-block">
                <div class="alphabet-nav">
                    <a href="#" class="alphabet-link active" data-letter="all">Tous</a>
                    <?php foreach ($initials as $letter): ?>
                        <a href="#" class="alphabet-link" data-letter="<?= $letter ?>"><?= $letter ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Liste des membres -->
            <div class="col-md-10">
                <div class="row g-4" id="membersContainer">
                    <?php
                        $query ="SELECT *, CONCAT(UPPER(nom), ' ', UPPER(prenom)) AS nom_prenom
                        FROM membres ORDER BY nom ASC";

                        $resultat = mysqli_query($bdd, $query) or die("Requête non conforme");
                        while ($membre = mysqli_fetch_assoc($resultat)) {
                            $initial = strtoupper(substr($membre['nom'], 0, 1));
                            $logo = isset($membre["logo"]) && !empty($membre["logo"]) 
                                ? $membre["logo"] 
                                : 'no_image.jpg';

                            $imagePath = "../fichiers/uploads/" . $logo;

                            if ($logo === 'no_image.jpg') {
                                $imagePath = "../fichiers/images/no_image.jpg";
                            }
                            
                            $annee = (!empty($membre["date_adhesion"]) && strtotime($membre["date_adhesion"])) 
                                ? date('Y', strtotime($membre["date_adhesion"])) 
                                : '----';
                            
                            echo '
                            <div class="col-sm-6 col-lg-4 col-xl-3 member-item" data-name="'.$membre["nom_prenom"].'" data-letter="'.$initial.'">
                                <div class="member-card">
                                    <div class="card-body">
                                        <img src="'.$imagePath.'" alt="Avatar" class="member-avatar">
                                        <h5 class="card-title">'.$membre["nom_prenom"].'</h5>
                                        <p class="member-since">Membre depuis '.$annee.'</p>
                                        <span class="role-badge">'.$membre["poste_occupe"].'</span>
                                        
                                        <div class="action-buttons">
                                            <a href="https://wa.me/+225'.$membre["num_telephone"].'" target="_blank" class="btn-action whatsapp" title="WhatsApp">
                                                <i class="bi bi-whatsapp"></i>
                                            </a>
                                            <a href="tel:+225'.$membre["num_telephone"].'" class="btn-action phone" title="Appeler">
                                                <i class="bi bi-telephone-fill"></i>
                                            </a>
                                            <a href="sms:+225'.$membre["num_telephone"].'" class="btn-action sms" title="SMS">
                                                <i class="bi bi-chat-dots-fill"></i>
                                            </a>
                                            <button type="button" class="btn-action profile" data-bs-toggle="modal" data-bs-target="#memberModal'.$membre["id"].'" title="Voir le profil">
                                                <i class="bi bi-person-fill"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Modal -->
                            <div class="modal fade" id="memberModal'.$membre["id"].'" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Profil du membre</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <img src="'.$imagePath.'" alt="Avatar" class="modal-avatar">
                                            <h4 class="modal-name">'.$membre["nom_prenom"].'</h4>
                                            <p class="modal-subtitle">Membre depuis '.$annee.'</p>
                                            <div class="text-center">
                                                <span class="modal-role-badge">'.$membre["poste_occupe"].'</span>
                                            </div>
                                            
                                            <div class="info-section">
                                                <div class="info-item">
                                                    <i class="bi bi-telephone-fill"></i>
                                                    <div class="flex-grow-1">
                                                        <div class="info-label">Téléphone</div>
                                                        <div class="info-value">+225 '.$membre["num_telephone"].'</div>
                                                    </div>
                                                </div>
                                                '.(!empty($membre["email"]) ? '
                                                <div class="info-item">
                                                    <i class="bi bi-envelope-fill"></i>
                                                    <div class="flex-grow-1">
                                                        <div class="info-label">Email</div>
                                                        <div class="info-value">'.$membre["email"].'</div>
                                                    </div>
                                                </div>' : '').'
                                                '.(!empty($membre["adresse"]) ? '
                                                <div class="info-item">
                                                    <i class="bi bi-geo-alt-fill"></i>
                                                    <div class="flex-grow-1">
                                                        <div class="info-label">Adresse</div>
                                                        <div class="info-value">'.$membre["adresse"].'</div>
                                                    </div>
                                                </div>' : '').'
                                            </div>
                                            
                                            <div class="modal-actions">
                                                <a href="https://wa.me/+225'.$membre["num_telephone"].'" target="_blank" class="btn-modal-action btn-whatsapp">
                                                    <i class="bi bi-whatsapp"></i>
                                                    <span>WhatsApp</span>
                                                </a>
                                                <a href="tel:+225'.$membre["num_telephone"].'" class="btn-modal-action btn-call">
                                                    <i class="bi bi-telephone-fill"></i>
                                                    <span>Appeler</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/php/footer.php');?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const alphabetLinks = document.querySelectorAll('.alphabet-link');
            const memberItems = document.querySelectorAll('.member-item');
            
            // Filtrage par recherche
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                filterMembers(searchTerm, 'all');
                
                alphabetLinks.forEach(link => link.classList.remove('active'));
                document.querySelectorAll('.alphabet-link[data-letter="all"]').forEach(link => {
                    link.classList.add('active');
                });
            });
            
            // Filtrage par lettre
            alphabetLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const letter = this.getAttribute('data-letter');
                    
                    alphabetLinks.forEach(l => l.classList.remove('active'));
                    document.querySelectorAll('.alphabet-link[data-letter="' + letter + '"]').forEach(l => {
                        l.classList.add('active');
                    });
                    
                    if (letter === 'all') {
                        filterMembers('', 'all');
                    } else {
                        filterMembers('', letter);
                    }
                    
                    searchInput.value = '';
                });
            });
            
            // Fonction de filtrage
            function filterMembers(searchTerm, letter) {
                memberItems.forEach(item => {
                    const name = item.getAttribute('data-name').toLowerCase();
                    const nameLetter = item.getAttribute('data-letter');
                    
                    const matchesSearch = name.includes(searchTerm);
                    const matchesLetter = letter === 'all' || nameLetter === letter;
                    
                    if (matchesSearch && matchesLetter) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>