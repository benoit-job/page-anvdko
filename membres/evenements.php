 <?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php"); 
requireAdhesionPayee();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements - Mon Association</title>

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
        .event-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1;
        }
        .search-box {
            border-radius: 20px;
            border: 1px solid #dee2e6;
            padding: 10px 15px;
        }
        .filter-btn {
            border-radius: 20px;
            padding: 8px 15px;
            transition: all 0.3s;
        }
        .filter-btn:hover {
            background-color: #f8f9fa;
        }
        .history-section {
            cursor: pointer;
            transition: all 0.3s;
            border-radius: 10px;
            padding: 15px;
        }
        .history-section:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        .btn-inscription {
            transition: all 0.3s;
        }
        .modal-content {
            border-radius: 15px;
        }
        /* Notifications */
#notifications-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    max-width: 350px;
}

.notification {
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 15px;
    margin-bottom: 15px;
    transform: translateX(100%);
    opacity: 0;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border-left: 4px solid #28a745;
}

.notification.show {
    transform: translateX(0);
    opacity: 1;
}

.notification.success {
    border-left-color: #28a745;
}

.notification.error {
    border-left-color: #dc3545;
}

.notification.info {
    border-left-color: #17a2b8;
}

.notification-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    width: 100%;
    background: rgba(0, 0, 0, 0.1);
}

.notification-progress::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    height: 100%;
    width: 100%;
    background: #28a745;
    animation: progress 5s linear forwards;
}

.notification.success .notification-progress::after {
    background: #28a745;
}

.notification.error .notification-progress::after {
    background: #dc3545;
}

.notification.info .notification-progress::after {
    background: #17a2b8;
}

.notification-content {
    display: flex;
    align-items: center;
}

.notification-content i {
    margin-right: 10px;
    font-size: 1.2rem;
}

@keyframes progress {
    from { width: 100%; }
    to { width: 0%; }
}
    </style>
</head>
<body class="bg-light">
    <!-- Modal d'inscription -->
    <div class="modal fade" id="inscriptionModal" tabindex="-1" aria-labelledby="inscriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inscriptionModalLabel">Inscription à l'événement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="inscriptionForm">
                        <div class="mb-3">
                            <label for="nombrePersonnes" class="form-label">Nombre de personnes</label>
                            <input type="number" class="form-control" id="nombrePersonnes" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="commentaire" class="form-label">Commentaire (optionnel)</label>
                            <textarea class="form-control" id="commentaire" rows="3"></textarea>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="newsletter" checked>
                            <label class="form-check-label" for="newsletter">
                                Recevoir les informations sur cet événement
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="confirmInscription">Confirmer l'inscription</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast pour les notifications -->
    <!-- <div class="toast-container">
        <div id="toastInscription" class="toast align-items-center text-white bg-success" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Inscription enregistrée avec succès !
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div> -->


    <div class="container py-4">
    <h1 class="text-center mb-4 fw-bold">Événements à venir</h1>
    
    <!-- Barre de recherche et filtres -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="input-group search-box">
                <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
                <input type="text" id="searchInput" class="form-control border-0" placeholder="Rechercher un événement..." onkeyup="filterEvents()">
            </div>
        </div>
        <div class="col-md-4 d-flex justify-content-end">
            <div class="dropdown">
                <button class="btn btn-outline-primary filter-btn me-2 dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-funnel"></i> Filtres
                </button>
                <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                    <li><a class="dropdown-item" href="#" onclick="filterByCategory('all')">Tous</a></li>
                    <li><a class="dropdown-item" href="#" onclick="filterByCategory('Important')">Important</a></li>
                    <li><a class="dropdown-item" href="#" onclick="filterByCategory('Loisir')">Loisir</a></li>
                    <li><a class="dropdown-item" href="#" onclick="filterByCategory('Sortie')">Sortie</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Liste des événements -->
    <div class="row g-4" id="eventsContainer">
        <?php
$query = "SELECT * FROM evenements 
          WHERE date_debut > NOW()
          ORDER BY date_debut ASC";

$resultat = mysqli_query($bdd, $query) or die("Requête non conforme");

$inscriptionsQuery = "SELECT evenement_id FROM inscription_evenement WHERE membre_id = " . (int)$_SESSION['membre']['id'];
$inscriptionsResult = mysqli_query($bdd, $inscriptionsQuery);
$userInscriptions = [];

while ($inscription = mysqli_fetch_assoc($inscriptionsResult)) {
    $userInscriptions[] = $inscription['evenement_id'];
}

while ($event = mysqli_fetch_array($resultat)): ?>
    <?php
    $dateDebut = new DateTime($event['date_debut']);
    $dateFin = new DateTime($event['date_fin']);
    
    $category = 'Important';
    $badgeClass = 'bg-primary';
    
    $titreLower = strtolower($event['titre']);
    if (strpos($titreLower, 'atelier') !== false) {
        $category = 'Loisir';
        $badgeClass = 'bg-success';
    } elseif (strpos($titreLower, 'sortie') !== false) {
        $category = 'Sortie';
        $badgeClass = 'bg-info';
    }
    
    // Vérifier si l'événement est complet (ajoutez votre propre logique ici)
    $isFull = false;
    
    // Vérifier si l'utilisateur est inscrit
    $isRegistered = in_array($event['id'], $userInscriptions);
    ?>
    
    <div class="col-md-6 col-lg-4 event-item" data-category="<?= htmlspecialchars($category) ?>">
        <div class="event-card card h-100">
            <?php if ($isRegistered): ?>
                <span class="event-badge badge bg-success">Inscrit</span>
            <?php elseif ($isFull): ?>
                <span class="event-badge badge bg-warning text-dark">Complet</span>
            <?php endif; ?>

            <div class="row g-0 h-100">
                <div class="col-3 d-flex align-items-center justify-content-center event-date">
                    <div>
                        <div class="fs-4 fw-bold"><?= $dateDebut->format('d') ?></div>
                        <div class="text-uppercase"><?= $dateDebut->format('M') ?></div>
                    </div>
                </div>
                <div class="col-9">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($event['titre']) ?></h5>
                        <p class="card-text text-muted small">
                            <i class="bi bi-clock"></i> <?= $dateDebut->format('H:i') ?> - <?= $dateFin->format('H:i') ?><br>
                            <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($event['lieu']) ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($category) ?></span>
                            </div>
                            <?php if ($isFull): ?>
                                <button class="btn btn-sm btn-outline-secondary rounded-pill" disabled>
                                    Complet <i class="bi bi-x-circle"></i>
                                </button>
                            <?php elseif ($isRegistered): ?>
                                <button class="btn btn-sm btn-outline-success rounded-pill btn-inscription" 
                                        data-event-id="<?= $event['id'] ?>" 
                                        data-event-title="<?= htmlspecialchars($event['titre']) ?>">
                                    Inscrit <i class="bi bi-check-circle"></i>
                                </button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-primary rounded-pill btn-inscription" 
                                        data-event-id="<?= $event['id'] ?>" 
                                        data-event-title="<?= htmlspecialchars($event['titre']) ?>"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#inscriptionModal">
                                    S'inscrire <i class="bi bi-plus-circle"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endwhile; ?>

    </div>
    <div id="notifications-container"></div>
    
    <!-- Événements passés -->
    <div class="history-section mt-5 p-3" onclick="window.location.href='historique-evenements.php'">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0 fw-bold">Historique des événements</h2>
            <div>
                <span class="badge bg-secondary me-2">Voir tout</span>
                <i class="bi bi-chevron-right"></i>
            </div>
        </div>
        <p class="text-muted mb-0">Consultez tous les événements passés</p>
    </div>
</div>

    <?php include('includes/php/footer.php');?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery CDN (version minifiée, dernière version stable 3.x) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Fonction de filtrage par recherche
        function filterEvents() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const events = document.querySelectorAll('.event-item');
            
            events.forEach(event => {
                const title = event.querySelector('.card-title').textContent.toUpperCase();
                if (title.includes(filter)) {
                    event.style.display = "";
                } else {
                    event.style.display = "none";
                }
            });
        }
        
        // Fonction de filtrage par catégorie
        function filterByCategory(category) {
            const events = document.querySelectorAll('.event-item');
            
            events.forEach(event => {
                if (category === 'all') {
                    event.style.display = "";
                } else {
                    const eventCategory = event.getAttribute('data-category');
                    if (eventCategory === category) {
                        event.style.display = "";
                    } else {
                        event.style.display = "none";
                    }
                }
            });
            
            // Mettre à jour le texte du bouton de filtre
            document.getElementById('filterDropdown').innerHTML = 
                `<i class="bi bi-funnel"></i> ${category === 'all' ? 'Filtres' : category}`;
        }
</script>
</body>
</html>

<script>
    $(document).ready(function() {
        // Initialisation des éléments
        const toastInscription = new bootstrap.Toast($('#toastInscription')[0]);
        
        // Fonction pour afficher les notifications
        function showNotification(message, type = 'success') {
            const notification = $(`
                <div class="notification ${type}">
                    <div class="notification-progress"></div>
                    <div class="notification-content">
                        <i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle'}"></i>
                        <span>${message}</span>
                    </div>
                </div>
            `);

            $('#notifications-container').append(notification);

            setTimeout(() => {
                notification.addClass('show');
                setTimeout(() => {
                    notification.removeClass('show');
                    setTimeout(() => {
                        notification.remove();
                        // Actualisation de la page après que la notification disparaisse
                        location.reload();
                    }, 300); // temps pour que l'effet de disparition se termine
                }, 4000); // durée d'affichage de la notification
            }, 100); // petit délai pour déclencher l'animation
        }

        // Fonction pour mettre à jour l'état du bouton d'inscription
        function updateInscriptionButton(eventId, isRegistered) {
            const $btnInscription = $('.btn-inscription[data-event-id="' + eventId + '"]');
            const $eventCard = $btnInscription.closest('.event-card');
            
            if (isRegistered) {
                $btnInscription.html('Inscrit <i class="bi bi-check-circle"></i>')
                            .removeClass('btn-outline-primary')
                            .addClass('btn-outline-success')
                            .attr('data-bs-toggle', '')
                            .attr('data-bs-target', '')
                            .off('click')
                            .on('click', function(e) {
                                e.preventDefault();
                                showEventOptions(eventId);
                            });

                if ($eventCard.find('.event-badge').length === 0) {
                    $eventCard.prepend('<span class="event-badge badge bg-success">Inscrit</span>');
                }
            } else {
                $btnInscription.html('S\'inscrire <i class="bi bi-plus-circle"></i>')
                            .removeClass('btn-outline-success')
                            .addClass('btn-outline-primary')
                            .off('click')
                            .attr('data-bs-toggle', 'modal')
                            .attr('data-bs-target', '#inscriptionModal')
                            .on('click', function() {
                                $('#confirmInscription').data('event-id', eventId);
                            });

                $eventCard.find('.event-badge').remove();
            }
        }

        // Supprimer le gestionnaire d'événements séparé (celui avec .btn-inscription.btn-success)

        // Gestion du clic sur le bouton d'inscription (pour les états déjà inscrit)
        $(document).on('click', '.btn-inscription.btn-outline-success', function(e) {
            e.preventDefault();
            const eventId = $(this).data('event-id');
            showEventOptions(eventId);
        });

        // Gestion de l'inscription
        $('#confirmInscription').click(function() {
            const eventId = $(this).data('event-id');
            const nombrePersonnes = $('#nombrePersonnes').val();
            const commentaire = $('#commentaire').val();
            const newsletter = $('#newsletter').is(':checked') ? 1 : 0;

            if (nombrePersonnes < 1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Veuillez entrer un nombre valide de participants',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            const $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Envoi...');

            $.post('traitement_inscription.php', {
                action: 'inscription',
                event_id: eventId,
                nombre_personnes: nombrePersonnes,
                commentaire: commentaire,
                newsletter: newsletter
            })
            .done(function(response) {
                $btn.prop('disabled', false).html('Confirmer l\'inscription');

                if (response.success) {
                    showNotification('Inscription réussie !');
                    toastInscription.show();
                    updateInscriptionButton(eventId, true);
                    $('#inscriptionModal').modal('hide');
                    $('#inscriptionModal').on('hidden.bs.modal', function () {
                        $('#eventsContainer').load('evenements.php #eventsContainer > *');
                    });

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: response.message || 'Échec de l\'inscription',
                        confirmButtonColor: '#3085d6'
                    });
                }
            })
            .fail(function() {
                $btn.prop('disabled', false).html('Confirmer l\'inscription');
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Erreur de connexion au serveur',
                    confirmButtonColor: '#3085d6'
                });
            });
        });

        // Fonction pour afficher les options de l'événement
        function showEventOptions(eventId) {
            Swal.fire({
                title: 'Vous êtes déjà inscrit',
                text: 'Que souhaitez-vous faire ?',
                icon: 'info',
                showCancelButton: false,
                confirmButtonText: 'Modifier',
                confirmButtonColor: '#3085d6',
                showDenyButton: true,
                denyButtonText: 'Supprimer',
                denyButtonColor: '#d33',
                showCloseButton: true,
                closeButtonAriaLabel: 'Fermer',
                width: '450px'
            }).then((result) => {
                if (result.isConfirmed) {
                    editInscription(eventId);
                } else if (result.isDenied) {
                    deleteInscription(eventId);
                }
            });
        }

        // Fonction pour modifier une inscription
        function editInscription(eventId) {
            // Récupérer les données actuelles pour pré-remplir le formulaire
            $.post('traitement_inscription.php', {
                action: 'get_inscription',
                event_id: eventId
            })
            .done(function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Modifier votre inscription',
                        html: `
                            <div class="mb-3">
                                <label for="swal-nombre" class="form-label">Nombre de personnes</label>
                                <input type="number" id="swal-nombre" class="form-control" min="1" value="${response.data.nombre_personnes || 1}">
                            </div>
                            <div class="mb-3">
                                <label for="swal-commentaire" class="form-label">Commentaire</label>
                                <textarea id="swal-commentaire" class="form-control" rows="3">${response.data.commentaire || ''}</textarea>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" id="swal-newsletter" class="form-check-input" ${response.data.newsletter ? 'checked' : ''}>
                                <label for="swal-newsletter" class="form-check-label">Recevoir les informations</label>
                            </div>
                        `,
                        focusConfirm: false,
                        preConfirm: () => {
                            return {
                                nombre: $('#swal-nombre').val(),
                                commentaire: $('#swal-commentaire').val(),
                                newsletter: $('#swal-newsletter').is(':checked') ? 1 : 0
                            }
                        },
                        width: '500px'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.post('traitement_inscription.php', {
                                action: 'modifier',
                                event_id: eventId,
                                nombre_personnes: result.value.nombre,
                                commentaire: result.value.commentaire,
                                newsletter: result.value.newsletter
                            })
                            .done(function(response) {
                                if (response.success) {
                                    showNotification('Inscription modifiée avec succès !');
                                    $('#inscriptionModal').on('hidden.bs.modal', function () {
                                        $('#eventsContainer').load('evenements.php #eventsContainer > *');
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Erreur',
                                        text: response.message || 'Échec de la modification',
                                        confirmButtonColor: '#3085d6'
                                    });
                                }
                            })
                            .fail(function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: 'Erreur de connexion au serveur',
                                    confirmButtonColor: '#3085d6'
                                });
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Impossible de charger les données de l\'inscription',
                        confirmButtonColor: '#3085d6'
                    });
                }
            })
            .fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Erreur de connexion au serveur',
                    confirmButtonColor: '#3085d6'
                });
            });
        }

        // Fonction pour supprimer une inscription
        function deleteInscription(eventId) {
            Swal.fire({
                title: 'Confirmer la suppression',
                text: 'Voulez-vous vraiment annuler votre inscription ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                width: '450px'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('traitement_inscription.php', {
                        action: 'supprimer',
                        event_id: eventId
                    })
                    .done(function(response) {
                        if (response.success) {
                            showNotification('Inscription supprimée avec succès !', 'info');
                            updateInscriptionButton(eventId, false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: response.message || 'Échec de la suppression',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    })
                    .fail(function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Erreur de connexion au serveur',
                            confirmButtonColor: '#3085d6'
                        });
                    });
                }
            });
        }

        // Remplir les données de l'événement dans la modal
        $('#inscriptionModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const eventId = button.data('event-id');
            const eventTitle = button.data('event-title');

            $(this).find('.modal-title').text('Inscription à : ' + eventTitle);
            $('#confirmInscription').data('event-id', eventId);

            $('#nombrePersonnes').val(1);
            $('#commentaire').val('');
            $('#newsletter').prop('checked', true);
        });
    });
</script>