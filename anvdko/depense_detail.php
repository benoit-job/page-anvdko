<?php 
include("includes/php/connexion_acces_page.php"); 
include("../include/php/connexion_bdd.php"); 
include("../include/php/fonctions.php");

$id_depense = intval($_GET['id'] ?? 0);
$id_user = $_SESSION['utilisateur']['id'];

// Charger la dépense
$sql = "SELECT * FROM depenses_anvdko WHERE id_depense = $id_depense AND id_user = $id_user";
$result = mysqli_query($bdd, $sql);

if (mysqli_num_rows($result) == 0) {
    header('Location: depenses_anvdko.php');
    exit();
}

$depense = mysqli_fetch_assoc($result);

// Charger les montants
$sql = "SELECT * FROM depense_montants WHERE id_depense = $id_depense ORDER BY date_paiement";
$result = mysqli_query($bdd, $sql);

$montants = [];
while ($row = mysqli_fetch_assoc($result)) {
    $montants[] = $row;
}

// Calculer le total
$total = 0;
foreach ($montants as $m) {
    $total += $m['montant'];
}

// Charger les commentaires avec le nom de l'utilisateur
$sql = "SELECT c.*, u.pseudo AS nom
        FROM depense_commentaires c
        LEFT JOIN utilisateurs u ON c.id_user = u.id
        WHERE c.id_depense = $id_depense
        ORDER BY c.date_commentaire DESC";
$result = mysqli_query($bdd, $sql);

$commentaires = [];
while ($row = mysqli_fetch_assoc($result)) {
    $commentaires[] = $row;
}
?>
<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Détail de la Dépense - <?= htmlspecialchars($depense['titre']) ?></title>
    
    <?php include('includes/php/includes-css.php');?>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        .detail-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-detail {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .info-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            border-left: 5px solid #2563eb;
        }
        
        .montant-item {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .montant-item:hover {
            transform: translateX(5px);
        }
        
        .commentaire-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #10b981;
        }
        
        .badge-total {
            font-size: 1.5rem;
            padding: 15px 30px;
            border-radius: 30px;
        }
        
        .comment-form {
            background: #f8fafc;
            padding: 25px;
            border-radius: 15px;
            border: 2px solid #e2e8f0;
        }
    </style>
</head>

<body>
    <main class="main" id="top">
        <?php include('includes/php/menu.php');?>
        <?php include('includes/php/header.php');?>
        
        <div class="content">
            <div class="pb-5">
                <!-- Header -->
                <div class="header-detail">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2><i class="fas fa-file-invoice-dollar me-3"></i><?= htmlspecialchars($depense['titre']) ?></h2>
                        <a href="depenses_anvdko.php" class="btn btn-light">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <small class="d-block opacity-75">Catégorie</small>
                            <strong class="fs-5"><?= htmlspecialchars($depense['categorie']) ?></strong>
                        </div>
                        <div class="col-md-4">
                            <small class="d-block opacity-75">Date</small>
                            <strong class="fs-5"><?= date('d/m/Y', strtotime($depense['date_depense'])) ?></strong>
                        </div>
                        <div class="col-md-4">
                            <small class="d-block opacity-75">Montant Total</small>
                            <span class="badge bg-warning badge-total"><?= number_format($total, 0, ',', ' ') ?> FCFA</span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <?php if (!empty($depense['description'])): ?>
                <div class="info-card">
                    <h5 class="mb-3"><i class="fas fa-align-left me-2"></i>Description</h5>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($depense['description'])) ?></p>
                </div>
                <?php endif; ?>

                <!-- Montants -->
                <div class="info-card">
                    <h5 class="mb-3">
                        <i class="fas fa-money-bill-wave me-2"></i>Détail des Paiements 
                        <span class="badge bg-primary"><?= count($montants) ?> paiement(s)</span>
                    </h5>
                    
                    <?php foreach ($montants as $index => $m): ?>
                    <div class="montant-item">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <div class="text-center">
                                    <div class="badge bg-secondary" style="font-size: 1.2rem; padding: 10px 15px;">
                                        #<?= $index + 1 ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <small class="text-muted d-block">Montant</small>
                                <h4 class="mb-0 text-primary fw-bold">
                                    <?= number_format($m['montant'], 0, ',', ' ') ?> FCFA
                                </h4>
                            </div>
                            <div class="col-md-5">
                                <small class="text-muted d-block">Date de paiement</small>
                                <h5 class="mb-0">
                                    <i class="fas fa-calendar me-2"></i>
                                    <?= date('d/m/Y', strtotime($m['date_paiement'])) ?>
                                </h5>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Commentaires -->
                <div class="info-card">
                    <h5 class="mb-3">
                        <i class="fas fa-comments me-2"></i>Commentaires 
                        <span class="badge bg-success"><?= count($commentaires) ?></span>
                    </h5>
                    
                    <!-- Formulaire ajout commentaire -->
                    <div class="comment-form mb-4">
                        <form id="formCommentaire">
                            <input type="hidden" name="id_depense" value="<?= $id_depense ?>">
                            <input type="hidden" name="id_user" value="<?= $id_user ?>">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Ajouter un commentaire</label>
                                <textarea class="form-control" name="commentaire" rows="3" placeholder="Votre commentaire..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane me-2"></i>Ajouter
                            </button>
                        </form>
                    </div>

                    <!-- Liste des commentaires -->
                    <div id="listeCommentaires">
                        <?php if (empty($commentaires)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>Aucun commentaire pour le moment</p>
                        </div>
                        <?php else: ?>
                            <?php foreach ($commentaires as $c): ?>
                            <div class="commentaire-card" data-id="<?= $c['id_commentaire'] ?>">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong class="text-primary">
                                            <i class="fas fa-user-circle me-2"></i>
                                            <?= htmlspecialchars($c['nom'] ?? 'Utilisateur') ?>
                                        </strong>
                                        <small class="text-muted ms-3">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= date('d/m/Y à H:i', strtotime($c['date_commentaire'])) ?>
                                        </small>
                                    </div>
                                    <?php if ($c['id_user'] == $id_user): ?>
                                    <button class="btn btn-sm btn-danger" onclick="supprimerCommentaire(<?= $c['id_commentaire'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($c['commentaire'])) ?></p>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Informations système -->
                <div class="text-center text-muted mt-4">
                    <small>
                        <i class="fas fa-info-circle me-2"></i>
                        Créé le <?= date('d/m/Y à H:i', strtotime($depense['date_created'])) ?>
                        <?php if ($depense['date_updated'] != $depense['date_created']): ?>
                        | Modifié le <?= date('d/m/Y à H:i', strtotime($depense['date_updated'])) ?>
                        <?php endif; ?>
                    </small>
                </div>

            </div>
            
            <?php include('includes/php/footer.php');?>
        </div>
    </main>

    <?php include('includes/php/includes-js.php');?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Ajouter un commentaire
        $('#formCommentaire').on('submit', function(e) {
            e.preventDefault();
            
            $.post('ajax/commentaires_api.php', $(this).serialize() + '&action=ajouter', function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Commentaire ajouté',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire('Erreur', response.message, 'error');
                }
            }, 'json');
        });
        
        // Supprimer un commentaire
        function supprimerCommentaire(idCommentaire) {
            Swal.fire({
                title: 'Supprimer ce commentaire ?',
                text: "Cette action est irréversible",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('ajax/commentaires_api.php', {
                        action: 'supprimer',
                        id_commentaire: idCommentaire
                    }, function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Supprimé',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            
                            $(`div[data-id="${idCommentaire}"]`).fadeOut(300, function() {
                                $(this).remove();
                            });
                        } else {
                            Swal.fire('Erreur', response.message, 'error');
                        }
                    }, 'json');
                }
            });
        }
    </script>
</body>
</html>