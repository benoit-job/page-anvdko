<?php
include("includes/php/connexion_acces_page.php");
include("../include/php/connexion_bdd.php");
include("../include/php/fonctions.php");
include("../include/php/site_public.php");
include("../include/php/admin_image_upload.php");

$id_conf = (int) ($_SESSION['configuration']['id'] ?? 1);
$tables_ok = anvdko_site_table_exists($bdd, 'site_slides');
$admin_img_error = '';

function gs_need_image($bdd, $fileField, $hidden = 'image_courante', $required = true)
{
    global $admin_img_error;
    $sql = anvdko_admin_image_sql($bdd, $fileField, $hidden, $required);
    if ($sql === null) {
        $admin_img_error = 'Veuillez sélectionner une image.';
        return false;
    }
    return $sql;
}

if ($tables_ok && isset($_POST['save_about'])) {
    $qui_titre = mysqli_real_escape_string($bdd, $_POST['qui_titre'] ?? '');
    $qui_sous = mysqli_real_escape_string($bdd, $_POST['qui_sous_titre'] ?? '');
    $qui_texte = mysqli_real_escape_string($bdd, $_POST['qui_texte'] ?? '');
    $image_url = anvdko_admin_image_sql($bdd, 'image_about', 'image_courante', false);
    if ($image_url === null) {
        $image_url = '';
    }
    $mission_titre = mysqli_real_escape_string($bdd, $_POST['mission_titre'] ?? '');
    $mission_texte = mysqli_real_escape_string($bdd, $_POST['mission_texte'] ?? '');
    $equipe_titre = mysqli_real_escape_string($bdd, $_POST['equipe_titre'] ?? '');
    $equipe_texte = mysqli_real_escape_string($bdd, $_POST['equipe_texte'] ?? '');
    $points = array_filter(array_map('trim', explode("\n", $_POST['points_liste'] ?? '')));
    $points_json = mysqli_real_escape_string($bdd, json_encode(array_values($points), JSON_UNESCAPED_UNICODE));
    $citation = mysqli_real_escape_string($bdd, $_POST['citation'] ?? '');
    $pdf_url = mysqli_real_escape_string($bdd, $_POST['pdf_url'] ?? '');
    mysqli_query($bdd, "INSERT INTO site_about (id, id_configuration, qui_titre, qui_sous_titre, qui_texte, image_url, mission_titre, mission_texte, equipe_titre, equipe_texte, points_liste, citation, pdf_url)
        VALUES (1, $id_conf, '$qui_titre', '$qui_sous', '$qui_texte', '$image_url', '$mission_titre', '$mission_texte', '$equipe_titre', '$equipe_texte', '$points_json', '$citation', '$pdf_url')
        ON DUPLICATE KEY UPDATE qui_titre='$qui_titre', qui_sous_titre='$qui_sous', qui_texte='$qui_texte', image_url='$image_url',
        mission_titre='$mission_titre', mission_texte='$mission_texte', equipe_titre='$equipe_titre', equipe_texte='$equipe_texte',
        points_liste='$points_json', citation='$citation', pdf_url='$pdf_url'");
    reload_current_page();
}

if ($tables_ok && isset($_POST['ajouter_slide'])) {
    $image_url = gs_need_image($bdd, 'image_slide', 'image_courante', true);
    if ($image_url === false) {
        // erreur affichée en haut de page
    } else {
    $titre = mysqli_real_escape_string($bdd, strip_tags($_POST['titre'] ?? ''));
    $texte = mysqli_real_escape_string($bdd, $_POST['texte'] ?? '');
    $lien = mysqli_real_escape_string($bdd, $_POST['lien_bouton'] ?? '#about');
    $txt_btn = mysqli_real_escape_string($bdd, $_POST['texte_bouton'] ?? 'En savoir plus');
    $ordre = (int) ($_POST['ordre'] ?? 0);
    mysqli_query($bdd, "INSERT INTO site_slides (id_configuration, titre, texte, image_url, lien_bouton, texte_bouton, ordre, actif) VALUES ($id_conf, '$titre', '$texte', '$image_url', '$lien', '$txt_btn', $ordre, 1)");
    reload_current_page();
    }
}

if ($tables_ok && isset($_POST['supprimer_slide'])) {
    $id = (int) ($_POST['id_slide'] ?? 0);
    mysqli_query($bdd, "DELETE FROM site_slides WHERE id=$id AND id_configuration=$id_conf");
    reload_current_page();
}

if ($tables_ok && isset($_POST['modifier_slide'])) {
    $id = (int) ($_POST['id_slide'] ?? 0);
    $titre = mysqli_real_escape_string($bdd, strip_tags($_POST['titre'] ?? ''));
    $texte = mysqli_real_escape_string($bdd, $_POST['texte'] ?? '');
    $image_url = anvdko_admin_image_sql($bdd, 'image_slide', 'image_courante', false) ?: '';
    $lien = mysqli_real_escape_string($bdd, $_POST['lien_bouton'] ?? '#about');
    $txt_btn = mysqli_real_escape_string($bdd, $_POST['texte_bouton'] ?? '');
    $ordre = (int) ($_POST['ordre'] ?? 0);
    $actif = isset($_POST['actif']) ? 1 : 0;
    mysqli_query($bdd, "UPDATE site_slides SET titre='$titre', texte='$texte', image_url='$image_url', lien_bouton='$lien', texte_bouton='$txt_btn', ordre=$ordre, actif=$actif WHERE id=$id");
    reload_current_page();
}

if ($tables_ok && isset($_POST['ajouter_carte'])) {
    $image_url = gs_need_image($bdd, 'image_carte', 'image_courante', true);
    if ($image_url !== false) {
    $titre = mysqli_real_escape_string($bdd, strip_tags($_POST['titre'] ?? ''));
    $resume = mysqli_real_escape_string($bdd, $_POST['resume'] ?? '');
    $contenu = mysqli_real_escape_string($bdd, $_POST['contenu_complet'] ?? '');
    $ordre = (int) ($_POST['ordre'] ?? 0);
    mysqli_query($bdd, "INSERT INTO site_accueil_cartes (id_configuration, titre, resume, contenu_complet, image_url, ordre, actif) VALUES ($id_conf, '$titre', '$resume', '$contenu', '$image_url', $ordre, 1)");
    reload_current_page();
    }
}

if ($tables_ok && isset($_POST['modifier_carte'])) {
    $id = (int) ($_POST['id_carte'] ?? 0);
    $titre = mysqli_real_escape_string($bdd, strip_tags($_POST['titre'] ?? ''));
    $resume = mysqli_real_escape_string($bdd, $_POST['resume'] ?? '');
    $contenu = mysqli_real_escape_string($bdd, $_POST['contenu_complet'] ?? '');
    $image_url = anvdko_admin_image_sql($bdd, 'image_carte', 'image_courante', false) ?: '';
    $ordre = (int) ($_POST['ordre'] ?? 0);
    $actif = isset($_POST['actif']) ? 1 : 0;
    mysqli_query($bdd, "UPDATE site_accueil_cartes SET titre='$titre', resume='$resume', contenu_complet='$contenu', image_url='$image_url', ordre=$ordre, actif=$actif WHERE id=$id");
    reload_current_page();
}

if ($tables_ok && isset($_POST['supprimer_carte'])) {
    $id = (int) ($_POST['id_carte'] ?? 0);
    mysqli_query($bdd, "DELETE FROM site_accueil_cartes WHERE id=$id");
    reload_current_page();
}

if ($tables_ok && isset($_POST['ajouter_bureau'])) {
    $image_url = gs_need_image($bdd, 'image_bureau', 'image_courante', true);
    if ($image_url !== false) {
    $role = mysqli_real_escape_string($bdd, $_POST['role_label'] ?? '');
    $nom = mysqli_real_escape_string($bdd, $_POST['nom'] ?? '');
    $ordre = (int) ($_POST['ordre'] ?? 0);
    $li = mysqli_real_escape_string($bdd, $_POST['linkedin'] ?? '');
    $tw = mysqli_real_escape_string($bdd, $_POST['twitter'] ?? '');
    $fb = mysqli_real_escape_string($bdd, $_POST['facebook'] ?? '');
    $ig = mysqli_real_escape_string($bdd, $_POST['instagram'] ?? '');
    mysqli_query($bdd, "INSERT INTO site_bureau (id_configuration, role_label, nom, image_url, linkedin, twitter, facebook, instagram, ordre, actif) VALUES ($id_conf, '$role', '$nom', '$image_url', '$li', '$tw', '$fb', '$ig', $ordre, 1)");
    reload_current_page();
    }
}

if ($tables_ok && isset($_POST['modifier_bureau'])) {
    $id = (int) ($_POST['id_bureau'] ?? 0);
    $role = mysqli_real_escape_string($bdd, $_POST['role_label'] ?? '');
    $nom = mysqli_real_escape_string($bdd, $_POST['nom'] ?? '');
    $image_url = anvdko_admin_image_sql($bdd, 'image_bureau', 'image_courante', false) ?: '';
    $ordre = (int) ($_POST['ordre'] ?? 0);
    $actif = isset($_POST['actif']) ? 1 : 0;
    $li = mysqli_real_escape_string($bdd, $_POST['linkedin'] ?? '');
    $tw = mysqli_real_escape_string($bdd, $_POST['twitter'] ?? '');
    $fb = mysqli_real_escape_string($bdd, $_POST['facebook'] ?? '');
    $ig = mysqli_real_escape_string($bdd, $_POST['instagram'] ?? '');
    mysqli_query($bdd, "UPDATE site_bureau SET role_label='$role', nom='$nom', image_url='$image_url', linkedin='$li', twitter='$tw', facebook='$fb', instagram='$ig', ordre=$ordre, actif=$actif WHERE id=$id");
    reload_current_page();
}

if ($tables_ok && isset($_POST['supprimer_bureau'])) {
    mysqli_query($bdd, "DELETE FROM site_bureau WHERE id=" . (int) ($_POST['id_bureau'] ?? 0));
    reload_current_page();
}

if ($tables_ok && isset($_POST['ajouter_galerie'])) {
    $image_url = gs_need_image($bdd, 'image_galerie', 'image_courante', true);
    if ($image_url !== false) {
    $titre = mysqli_real_escape_string($bdd, $_POST['titre'] ?? '');
    $desc = mysqli_real_escape_string($bdd, $_POST['description'] ?? '');
    $ordre = (int) ($_POST['ordre'] ?? 0);
    mysqli_query($bdd, "INSERT INTO site_galerie (id_configuration, titre, description, image_url, ordre, actif) VALUES ($id_conf, '$titre', '$desc', '$image_url', $ordre, 1)");
    reload_current_page();
    }
}

if ($tables_ok && isset($_POST['supprimer_galerie'])) {
    mysqli_query($bdd, "DELETE FROM site_galerie WHERE id=" . (int) ($_POST['id_galerie'] ?? 0));
    reload_current_page();
}

if ($tables_ok && isset($_POST['ajouter_faq'])) {
    $q = mysqli_real_escape_string($bdd, $_POST['question'] ?? '');
    $r = mysqli_real_escape_string($bdd, $_POST['reponse'] ?? '');
    $ordre = (int) ($_POST['ordre'] ?? 0);
    mysqli_query($bdd, "INSERT INTO site_faq (id_configuration, question, reponse, ordre, actif) VALUES ($id_conf, '$q', '$r', $ordre, 1)");
    reload_current_page();
}

if ($tables_ok && isset($_POST['supprimer_faq'])) {
    mysqli_query($bdd, "DELETE FROM site_faq WHERE id=" . (int) ($_POST['id_faq'] ?? 0));
    reload_current_page();
}

$about = anvdko_load_site_data($bdd, $id_conf)['about'];
$slides = [];
$cartes = [];
$bureau = [];
$galerie = [];
$faq = [];
if ($tables_ok) {
    $r = mysqli_query($bdd, "SELECT * FROM site_slides WHERE id_configuration=$id_conf ORDER BY ordre, id");
    while ($r && ($row = mysqli_fetch_assoc($r))) {
        $slides[] = $row;
    }
    $r = mysqli_query($bdd, "SELECT * FROM site_accueil_cartes WHERE id_configuration=$id_conf ORDER BY ordre, id");
    while ($r && ($row = mysqli_fetch_assoc($r))) {
        $cartes[] = $row;
    }
    $r = mysqli_query($bdd, "SELECT * FROM site_bureau WHERE id_configuration=$id_conf ORDER BY ordre, id");
    while ($r && ($row = mysqli_fetch_assoc($r))) {
        $bureau[] = $row;
    }
    $r = mysqli_query($bdd, "SELECT * FROM site_galerie WHERE id_configuration=$id_conf ORDER BY ordre, id");
    while ($r && ($row = mysqli_fetch_assoc($r))) {
        $galerie[] = $row;
    }
    $r = mysqli_query($bdd, "SELECT * FROM site_faq WHERE id_configuration=$id_conf ORDER BY ordre, id");
    while ($r && ($row = mysqli_fetch_assoc($r))) {
        $faq[] = $row;
    }
}
$points_txt = is_array($about['points_liste'] ?? null) ? implode("\n", $about['points_liste']) : '';
?>
<!DOCTYPE html>
<html data-navigation-type="default" lang="fr-FR" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion site public</title>
    <?php include('includes/php/includes-css.php');?>
</head>
<body>
<main class="main" id="top">
    <?php include('includes/php/menu.php');?>
    <?php include('includes/php/header.php');?>
    <div class="content pb-5">
        <h2 class="mb-2">Site public (accueil)</h2>
        <p class="text-body-secondary mb-4">Contenu affiché sur <a href="../index.php" target="_blank">index.php</a> — hors section Contact. Importez <code>anvdko/sql/create_site_cms.sql</code> si besoin.</p>
        <?php if (!$tables_ok): ?>
            <div class="alert alert-warning">Tables CMS introuvables. Exécutez le script SQL puis rechargez.</div>
        <?php else: ?>
        <?php if (!empty($admin_img_error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($admin_img_error); ?></div>
        <?php endif; ?>
        <ul class="nav nav-tabs mb-3 flex-wrap">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#t-slides" type="button">Slides</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t-about" type="button">Qui sommes-nous</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t-cartes" type="button">Actualités (cartes)</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t-bureau" type="button">Bureau</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t-galerie" type="button">Galerie</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t-faq" type="button">FAQ (+)</button></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="t-slides">
                <div class="card mb-3"><div class="card-body">
                    <form method="post" enctype="multipart/form-data" class="row g-2">
                        <div class="col-md-6"><input name="titre" class="form-control" placeholder="Titre" required></div>
                        <div class="col-12"><?php echo anvdko_admin_image_picker_html('image_slide', '', 'image_courante', 'Image du slide', true); ?></div>
                        <div class="col-12"><textarea name="texte" class="form-control" rows="2" placeholder="Texte"></textarea></div>
                        <div class="col-md-4"><input name="lien_bouton" class="form-control" value="#about"></div>
                        <div class="col-md-4"><input name="texte_bouton" class="form-control" value="En savoir plus"></div>
                        <div class="col-md-2"><input type="number" name="ordre" class="form-control" value="0"></div>
                        <div class="col-md-2"><button name="ajouter_slide" class="btn btn-success w-100">Ajouter</button></div>
                    </form>
                </div></div>
                <?php foreach ($slides as $s): ?>
                <div class="card mb-2"><div class="card-body">
                    <form method="post" enctype="multipart/form-data" class="row g-2">
                        <input type="hidden" name="id_slide" value="<?php echo (int)$s['id']; ?>">
                        <div class="col-md-4"><input name="titre" class="form-control form-control-sm" value="<?php echo htmlspecialchars($s['titre']); ?>"></div>
                        <div class="col-md-2"><input name="ordre" type="number" class="form-control form-control-sm" value="<?php echo (int)$s['ordre']; ?>"></div>
                        <div class="col-12"><?php echo anvdko_admin_image_picker_html('image_slide', $s['image_url'] ?? '', 'image_courante', 'Image', false, true); ?></div>
                        <div class="col-md-2"><label class="form-check"><input type="checkbox" name="actif" <?php echo $s['actif'] ? 'checked' : ''; ?>> Actif</label></div>
                        <div class="col-12"><textarea name="texte" class="form-control form-control-sm" rows="2"><?php echo htmlspecialchars($s['texte'] ?? ''); ?></textarea></div>
                        <div class="col-md-4"><input name="lien_bouton" class="form-control form-control-sm" value="<?php echo htmlspecialchars($s['lien_bouton'] ?? ''); ?>"></div>
                        <div class="col-md-4"><input name="texte_bouton" class="form-control form-control-sm" value="<?php echo htmlspecialchars($s['texte_bouton'] ?? ''); ?>"></div>
                        <div class="col-md-4">
                            <button name="modifier_slide" class="btn btn-primary btn-sm">Sauver</button>
                            <button name="supprimer_slide" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer ?')">X</button>
                        </div>
                    </form>
                </div></div>
                <?php endforeach; ?>
            </div>
            <div class="tab-pane fade" id="t-about">
                <div class="card"><div class="card-body">
                    <form method="post" enctype="multipart/form-data" class="row g-3">
                        <div class="col-md-6"><label class="form-label">Titre</label><input name="qui_titre" class="form-control" value="<?php echo htmlspecialchars($about['qui_titre'] ?? ''); ?>"></div>
                        <div class="col-md-6"><label class="form-label">Sous-titre</label><input name="qui_sous_titre" class="form-control" value="<?php echo htmlspecialchars($about['qui_sous_titre'] ?? ''); ?>"></div>
                        <div class="col-12"><label class="form-label">Texte présentation</label><textarea name="qui_texte" class="form-control" rows="3"><?php echo htmlspecialchars($about['qui_texte'] ?? ''); ?></textarea></div>
                        <div class="col-12"><?php echo anvdko_admin_image_picker_html('image_about', $about['image_url'] ?? '', 'image_courante', 'Photo « Qui sommes-nous »'); ?></div>
                        <div class="col-12"><label class="form-label">Mission</label><textarea name="mission_texte" class="form-control" rows="2"><?php echo htmlspecialchars($about['mission_texte'] ?? ''); ?></textarea></div>
                        <div class="col-12"><label class="form-label">Équipe</label><textarea name="equipe_texte" class="form-control" rows="2"><?php echo htmlspecialchars($about['equipe_texte'] ?? ''); ?></textarea></div>
                        <div class="col-12"><label class="form-label">Points (un par ligne)</label><textarea name="points_liste" class="form-control" rows="4"><?php echo htmlspecialchars($points_txt); ?></textarea></div>
                        <div class="col-12"><label class="form-label">Citation</label><input name="citation" class="form-control" value="<?php echo htmlspecialchars($about['citation'] ?? ''); ?>"></div>
                        <div class="col-12"><label class="form-label">Lien PDF statuts</label><input name="pdf_url" class="form-control" value="<?php echo htmlspecialchars($about['pdf_url'] ?? ''); ?>"></div>
                        <input type="hidden" name="mission_titre" value="Notre mission"><input type="hidden" name="equipe_titre" value="Notre équipe">
                        <div class="col-12"><button name="save_about" class="btn btn-primary">Enregistrer</button></div>
                    </form>
                </div></div>
            </div>
            <div class="tab-pane fade" id="t-cartes">
                <div class="card mb-3"><div class="card-body">
                    <form method="post" enctype="multipart/form-data" class="row g-2">
                        <div class="col-md-6"><input name="titre" class="form-control" placeholder="Titre" required></div>
                        <div class="col-12"><?php echo anvdko_admin_image_picker_html('image_carte', '', 'image_courante', 'Image de la carte', true); ?></div>
                        <div class="col-12"><textarea name="resume" class="form-control" rows="2" placeholder="Résumé carte"></textarea></div>
                        <div class="col-12"><textarea name="contenu_complet" class="form-control" rows="3" placeholder="Contenu « Lire plus »"></textarea></div>
                        <div class="col-md-2"><input type="number" name="ordre" class="form-control" value="0"></div>
                        <div class="col-md-2"><button name="ajouter_carte" class="btn btn-success">Ajouter</button></div>
                    </form>
                </div></div>
                <?php foreach ($cartes as $c): ?>
                <div class="card mb-2"><div class="card-body">
                    <form method="post" enctype="multipart/form-data" class="row g-2">
                        <input type="hidden" name="id_carte" value="<?php echo (int)$c['id']; ?>">
                        <div class="col-md-5"><input name="titre" class="form-control form-control-sm" value="<?php echo htmlspecialchars($c['titre']); ?>"></div>
                        <div class="col-md-2"><input name="ordre" type="number" class="form-control form-control-sm" value="<?php echo (int)$c['ordre']; ?>"></div>
                        <div class="col-12"><?php echo anvdko_admin_image_picker_html('image_carte', $c['image_url'] ?? '', 'image_courante', 'Image', false, true); ?></div>
                        <div class="col-md-2"><label class="form-check"><input type="checkbox" name="actif" <?php echo $c['actif'] ? 'checked' : ''; ?>> Actif</label></div>
                        <div class="col-12"><textarea name="resume" class="form-control form-control-sm" rows="2"><?php echo htmlspecialchars($c['resume'] ?? ''); ?></textarea></div>
                        <div class="col-12"><textarea name="contenu_complet" class="form-control form-control-sm" rows="3"><?php echo htmlspecialchars($c['contenu_complet'] ?? ''); ?></textarea></div>
                        <div class="col-12"><button name="modifier_carte" class="btn btn-primary btn-sm">Sauver</button><button name="supprimer_carte" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer ?')">X</button></div>
                    </form>
                </div></div>
                <?php endforeach; ?>
            </div>
            <div class="tab-pane fade" id="t-bureau">
                <p class="small text-muted">Maximum 4 membres affichés sur l'accueil (ordre croissant).</p>
                <div class="card mb-3"><div class="card-body">
                    <form method="post" enctype="multipart/form-data" class="row g-2">
                        <div class="col-md-4"><input name="role_label" class="form-control" placeholder="Fonction" required></div>
                        <div class="col-md-4"><input name="nom" class="form-control" placeholder="Nom" required></div>
                        <div class="col-12"><?php echo anvdko_admin_image_picker_html('image_bureau', '', 'image_courante', 'Photo du membre', true); ?></div>
                        <div class="col-md-3"><input name="linkedin" class="form-control" placeholder="LinkedIn"></div>
                        <div class="col-md-3"><input name="twitter" class="form-control" placeholder="Twitter"></div>
                        <div class="col-md-3"><input name="facebook" class="form-control" placeholder="Facebook"></div>
                        <div class="col-md-3"><input name="instagram" class="form-control" placeholder="Instagram"></div>
                        <div class="col-md-2"><input name="ordre" type="number" class="form-control" value="0"></div>
                        <div class="col-md-2"><button name="ajouter_bureau" class="btn btn-success">Ajouter</button></div>
                    </form>
                </div></div>
                <?php foreach ($bureau as $b): ?>
                <div class="card mb-2"><div class="card-body">
                    <form method="post" enctype="multipart/form-data" class="row g-2">
                        <input type="hidden" name="id_bureau" value="<?php echo (int)$b['id']; ?>">
                        <div class="col-md-3"><input name="role_label" class="form-control form-control-sm" value="<?php echo htmlspecialchars($b['role_label']); ?>"></div>
                        <div class="col-md-4"><input name="nom" class="form-control form-control-sm" value="<?php echo htmlspecialchars($b['nom']); ?>"></div>
                        <div class="col-md-1"><input name="ordre" type="number" class="form-control form-control-sm" value="<?php echo (int)$b['ordre']; ?>"></div>
                        <div class="col-12"><?php echo anvdko_admin_image_picker_html('image_bureau', $b['image_url'] ?? '', 'image_courante', 'Photo', false, true); ?></div>
                        <div class="col-md-1"><label class="form-check"><input type="checkbox" name="actif" <?php echo $b['actif'] ? 'checked' : ''; ?>></label></div>
                        <div class="col-md-2"><input name="linkedin" class="form-control form-control-sm" value="<?php echo htmlspecialchars($b['linkedin'] ?? ''); ?>" placeholder="LinkedIn"></div>
                        <div class="col-md-2"><input name="facebook" class="form-control form-control-sm" value="<?php echo htmlspecialchars($b['facebook'] ?? ''); ?>"></div>
                        <div class="col-12"><button name="modifier_bureau" class="btn btn-primary btn-sm">Sauver</button><button name="supprimer_bureau" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer ?')">X</button></div>
                    </form>
                </div></div>
                <?php endforeach; ?>
            </div>
            <div class="tab-pane fade" id="t-galerie">
                <form method="post" enctype="multipart/form-data" class="row g-2 mb-3">
                    <div class="col-md-4"><input name="titre" class="form-control" placeholder="Titre"></div>
                    <div class="col-md-2"><input name="ordre" type="number" class="form-control" value="0"></div>
                    <div class="col-md-2"><button name="ajouter_galerie" class="btn btn-success w-100">Ajouter</button></div>
                    <div class="col-12"><?php echo anvdko_admin_image_picker_html('image_galerie', '', 'image_courante', 'Photo galerie', true); ?></div>
                    <div class="col-12"><textarea name="description" class="form-control" rows="2" placeholder="Description"></textarea></div>
                </form>
                <table class="table table-sm"><?php foreach ($galerie as $g): ?>
                <tr><td><?php echo htmlspecialchars($g['titre']); ?></td><td><img src="<?php echo htmlspecialchars(anvdko_admin_preview_src($g['image_url'] ?? '')); ?>" alt="" style="height:40px;width:60px;object-fit:cover;border-radius:4px;"></td>
                <td><form method="post" onsubmit="return confirm('Supprimer ?')"><input type="hidden" name="id_galerie" value="<?php echo (int)$g['id']; ?>"><button name="supprimer_galerie" class="btn btn-sm btn-outline-danger">X</button></form></td></tr>
                <?php endforeach; ?></table>
            </div>
            <div class="tab-pane fade" id="t-faq">
                <p class="small text-muted">Les 9 questions par défaut restent sur le site. Ajoutez ici des questions supplémentaires.</p>
                <form method="post" class="row g-2 mb-3">
                    <div class="col-12"><input name="question" class="form-control" placeholder="Question" required></div>
                    <div class="col-12"><textarea name="reponse" class="form-control" rows="2" placeholder="Réponse" required></textarea></div>
                    <div class="col-md-2"><input name="ordre" type="number" class="form-control" value="0"></div>
                    <div class="col-md-2"><button name="ajouter_faq" class="btn btn-success">Ajouter</button></div>
                </form>
                <?php foreach ($faq as $f): ?>
                <div class="border rounded p-2 mb-2 d-flex justify-content-between">
                    <div><strong><?php echo htmlspecialchars($f['question']); ?></strong><br><span class="small"><?php echo htmlspecialchars($f['reponse']); ?></span></div>
                    <form method="post" onsubmit="return confirm('Supprimer ?')"><input type="hidden" name="id_faq" value="<?php echo (int)$f['id']; ?>"><button name="supprimer_faq" class="btn btn-sm btn-outline-danger">X</button></form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php include('includes/php/footer.php');?>
</main>
<?php include('includes/php/includes-js.php');?>
<?php echo anvdko_admin_image_picker_script(); ?>
</body>
</html>
