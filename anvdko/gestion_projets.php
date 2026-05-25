<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");
include("../include/php/admin_image_upload.php");

$admin_img_error = '';

function table_exists_projets($bdd, $table)
{
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
    if ($table === '') {
        return false;
    }
    $q = mysqli_query($bdd, "SHOW TABLES LIKE '" . mysqli_real_escape_string($bdd, $table) . "'");
    return $q && mysqli_num_rows($q) > 0;
}

$tables_ok = table_exists_projets($bdd, 'projets_public');

if ($tables_ok && isset($_POST['save_textes'])) {
    $titre_header = mysqli_real_escape_string($bdd, $_POST['titre_header'] ?? '');
    $sous_titre = mysqli_real_escape_string($bdd, $_POST['sous_titre'] ?? '');
    $intro = mysqli_real_escape_string($bdd, $_POST['intro_paragraph'] ?? '');
    $cta_titre = mysqli_real_escape_string($bdd, $_POST['cta_titre'] ?? '');
    $cta_texte = mysqli_real_escape_string($bdd, $_POST['cta_texte'] ?? '');
    mysqli_query($bdd, "INSERT INTO projets_page_textes (id, titre_header, sous_titre, intro_paragraph, cta_titre, cta_texte) VALUES (1, '$titre_header', '$sous_titre', '$intro', '$cta_titre', '$cta_texte')
        ON DUPLICATE KEY UPDATE titre_header='$titre_header', sous_titre='$sous_titre', intro_paragraph='$intro', cta_titre='$cta_titre', cta_texte='$cta_texte'");
    reload_current_page();
}

if ($tables_ok && isset($_POST['ajouter_projet'])) {
    $image_url = anvdko_admin_image_sql($bdd, 'image_projet', 'image_courante', true);
    if ($image_url !== null) {
    $titre = mysqli_real_escape_string($bdd, strip_tags(trim($_POST['titre'] ?? '')));
    $description = mysqli_real_escape_string($bdd, strip_tags(trim($_POST['description'] ?? '')));
    $lien_url = mysqli_real_escape_string($bdd, strip_tags(trim($_POST['lien_url'] ?? '#')));
    $statut_badge = mysqli_real_escape_string($bdd, strip_tags(trim($_POST['statut_badge'] ?? 'En cours')));
    $ordre = (int) ($_POST['ordre'] ?? 0);
    mysqli_query($bdd, "INSERT INTO projets_public (titre, description, image_url, lien_url, statut_badge, ordre, actif) VALUES ('$titre', '$description', '$image_url', '$lien_url', '$statut_badge', $ordre, 1)");
    reload_current_page();
    } else {
        $admin_img_error = 'Veuillez sélectionner une image pour le projet.';
    }
}

if ($tables_ok && isset($_POST['modifier_projet'])) {
    $id = (int) crypt_decrypt_chaine($_POST['id_projet'] ?? '', 'D');
    $titre = mysqli_real_escape_string($bdd, strip_tags(trim($_POST['titre'] ?? '')));
    $description = mysqli_real_escape_string($bdd, strip_tags(trim($_POST['description'] ?? '')));
    $image_url = anvdko_admin_image_sql($bdd, 'image_projet', 'image_courante', false) ?: '';
    $lien_url = mysqli_real_escape_string($bdd, strip_tags(trim($_POST['lien_url'] ?? '#')));
    $statut_badge = mysqli_real_escape_string($bdd, strip_tags(trim($_POST['statut_badge'] ?? 'En cours')));
    $ordre = (int) ($_POST['ordre'] ?? 0);
    $actif = isset($_POST['actif']) ? 1 : 0;
    mysqli_query($bdd, "UPDATE projets_public SET titre='$titre', description='$description', image_url='$image_url', lien_url='$lien_url', statut_badge='$statut_badge', ordre=$ordre, actif=$actif WHERE id=$id");
    reload_current_page();
}

if ($tables_ok && isset($_POST['supprimer_projet'])) {
    $id = (int) crypt_decrypt_chaine($_POST['id_projet'] ?? '', 'D');
    mysqli_query($bdd, "DELETE FROM projets_public WHERE id=$id");
    reload_current_page();
}

if ($tables_ok && isset($_POST['ajouter_temoignage'])) {
    $citation = mysqli_real_escape_string($bdd, strip_tags(trim($_POST['citation'] ?? '')));
    $auteur = mysqli_real_escape_string($bdd, strip_tags(trim($_POST['auteur'] ?? '')));
    $ordre = (int) ($_POST['ordre_t'] ?? 0);
    mysqli_query($bdd, "INSERT INTO projets_temoignages (citation, auteur, ordre, actif) VALUES ('$citation', '$auteur', $ordre, 1)");
    reload_current_page();
}

if ($tables_ok && isset($_POST['supprimer_temoignage'])) {
    $id = (int) ($_POST['id_temoignage'] ?? 0);
    mysqli_query($bdd, "DELETE FROM projets_temoignages WHERE id=$id");
    reload_current_page();
}

$textes = ['titre_header' => 'Projets de ANVDKO', 'sous_titre' => '', 'intro_paragraph' => '', 'cta_titre' => '', 'cta_texte' => ''];
if ($tables_ok && table_exists_projets($bdd, 'projets_page_textes')) {
    $r = mysqli_query($bdd, "SELECT * FROM projets_page_textes WHERE id=1");
    if ($r && mysqli_num_rows($r)) {
        $textes = array_merge($textes, mysqli_fetch_assoc($r));
    }
}

$liste_projets = [];
if ($tables_ok) {
    $r = mysqli_query($bdd, "SELECT * FROM projets_public ORDER BY ordre ASC, id ASC");
    while ($r && $row = mysqli_fetch_assoc($r)) {
        $liste_projets[] = $row;
    }
}

$liste_temoignages = [];
if ($tables_ok && table_exists_projets($bdd, 'projets_temoignages')) {
    $r = mysqli_query($bdd, "SELECT * FROM projets_temoignages WHERE actif=1 ORDER BY ordre ASC, id ASC");
    while ($r && $row = mysqli_fetch_assoc($r)) {
        $liste_temoignages[] = $row;
    }
}
?>
<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="fr-FR" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion page Projets</title>
    <?php include('includes/php/includes-css.php');?>
</head>
<body>
<main class="main" id="top">
    <?php include('includes/php/menu.php');?>
    <?php include('includes/php/header.php');?>
    <div class="content">
        <div class="pb-5">
            <h2 class="mb-2">Page publique « Projets »</h2>
            <p class="text-body-secondary">Contenu affiché sur <code>../projets.php</code>. Exécutez le script SQL <code>anvdko/sql/enhancements_projets_passwords.sql</code> si les tables n'existent pas encore.</p>

            <?php if (!$tables_ok): ?>
                <div class="alert alert-warning">Table <strong>projets_public</strong> introuvable. Importez le fichier SQL puis rechargez la page.</div>
            <?php else: ?>
            <?php if (!empty($admin_img_error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($admin_img_error); ?></div>
            <?php endif; ?>

            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-textes" type="button">Textes &amp; CTA</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-projets" type="button">Projets</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-temoins" type="button">Témoignages</button></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab-textes">
                    <div class="card card-fluid">
                        <div class="card-body">
                            <form method="post" class="row g-3">
                                <div class="col-12"><label class="form-label">Titre principal</label>
                                    <input type="text" name="titre_header" class="form-control" value="<?php echo htmlspecialchars($textes['titre_header'] ?? ''); ?>"></div>
                                <div class="col-12"><label class="form-label">Sous-titre (sous le titre)</label>
                                    <textarea name="sous_titre" class="form-control" rows="2"><?php echo htmlspecialchars($textes['sous_titre'] ?? ''); ?></textarea></div>
                                <div class="col-12"><label class="form-label">Paragraphe d'introduction</label>
                                    <textarea name="intro_paragraph" class="form-control" rows="4"><?php echo htmlspecialchars($textes['intro_paragraph'] ?? ''); ?></textarea></div>
                                <div class="col-md-6"><label class="form-label">Titre bloc « appel »</label>
                                    <input type="text" name="cta_titre" class="form-control" value="<?php echo htmlspecialchars($textes['cta_titre'] ?? ''); ?>"></div>
                                <div class="col-md-6"><label class="form-label">Texte bloc « appel »</label>
                                    <textarea name="cta_texte" class="form-control" rows="3"><?php echo htmlspecialchars($textes['cta_texte'] ?? ''); ?></textarea></div>
                                <div class="col-12"><button type="submit" name="save_textes" class="btn btn-primary">Enregistrer les textes</button></div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-projets">
                    <div class="card mb-4">
                        <div class="card-header">Ajouter un projet</div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data" class="row g-2">
                                <div class="col-md-6"><input name="titre" class="form-control" placeholder="Titre" required></div>
                                <div class="col-md-3"><input name="lien_url" class="form-control" placeholder="Lien En savoir plus" value="#"></div>
                                <div class="col-md-3"><input name="statut_badge" class="form-control" placeholder="Statut" value="En cours"></div>
                                <div class="col-12"><?php echo anvdko_admin_image_picker_html('image_projet', '', 'image_courante', 'Image du projet', true); ?></div>
                                <div class="col-12"><textarea name="description" class="form-control" rows="2" placeholder="Description"></textarea></div>
                                <div class="col-md-2"><input type="number" name="ordre" class="form-control" placeholder="Ordre" value="0"></div>
                                <div class="col-12"><button type="submit" name="ajouter_projet" class="btn btn-success btn-sm">Ajouter</button></div>
                            </form>
                        </div>
                    </div>
                    <div class="row g-3">
                            <?php foreach ($liste_projets as $p): ?>
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-body">
                                        <form method="post" enctype="multipart/form-data" class="row g-2 align-items-end">
                                            <input type="hidden" name="id_projet" value="<?php echo htmlspecialchars(crypt_decrypt_chaine((string)$p['id'], 'C')); ?>">
                                            <div class="col-md-2"><label class="form-label small">Ordre</label><input type="number" name="ordre" class="form-control form-control-sm" value="<?php echo (int)$p['ordre']; ?>"></div>
                                            <div class="col-md-4"><label class="form-label small">Titre</label><input name="titre" class="form-control form-control-sm" value="<?php echo htmlspecialchars($p['titre']); ?>"></div>
                                            <div class="col-md-3"><label class="form-label small">Statut badge</label><input name="statut_badge" class="form-control form-control-sm" value="<?php echo htmlspecialchars($p['statut_badge']); ?>"></div>
                                            <div class="col-md-3"><label class="form-label small">Actif</label><div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="actif" <?php echo !empty($p['actif']) ? 'checked' : ''; ?>></div></div>
                                            <div class="col-12"><?php echo anvdko_admin_image_picker_html('image_projet', $p['image_url'] ?? '', 'image_courante', 'Image', false, true); ?></div>
                                            <div class="col-12"><label class="form-label small">Lien</label><input name="lien_url" class="form-control form-control-sm" value="<?php echo htmlspecialchars($p['lien_url'] ?? '#'); ?>"></div>
                                            <div class="col-12"><label class="form-label small">Description</label><textarea name="description" class="form-control form-control-sm" rows="2"><?php echo htmlspecialchars($p['description'] ?? ''); ?></textarea></div>
                                            <div class="col-12">
                                                <button type="submit" name="modifier_projet" class="btn btn-primary btn-sm">Sauvegarder</button>
                                                <button type="submit" name="supprimer_projet" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer ce projet ?')">Supprimer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-temoins">
                    <form method="post" class="row g-2 mb-4">
                        <div class="col-md-5"><textarea name="citation" class="form-control" placeholder="Citation" required></textarea></div>
                        <div class="col-md-3"><input name="auteur" class="form-control" placeholder="Auteur"></div>
                        <div class="col-md-2"><input type="number" name="ordre_t" class="form-control" value="0"></div>
                        <div class="col-md-2"><button type="submit" name="ajouter_temoignage" class="btn btn-primary w-100">Ajouter</button></div>
                    </form>
                    <table class="table table-sm">
                        <?php foreach ($liste_temoignages as $t): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($t['citation']); ?></td>
                            <td><?php echo htmlspecialchars($t['auteur']); ?></td>
                            <td>
                                <form method="post" onsubmit="return confirm('Supprimer ?')">
                                    <input type="hidden" name="id_temoignage" value="<?php echo (int)$t['id']; ?>">
                                    <button class="btn btn-sm btn-outline-danger" name="supprimer_temoignage">X</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php include('includes/php/footer.php');?>
    </div>
</main>
<?php include('includes/php/includes-js.php');?>
<?php echo anvdko_admin_image_picker_script(); ?>
</body>
</html>
