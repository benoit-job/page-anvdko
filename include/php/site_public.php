<?php

function anvdko_site_table_exists($bdd, $table)
{
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
    if ($table === '') {
        return false;
    }
    $q = mysqli_query($bdd, "SHOW TABLES LIKE '" . mysqli_real_escape_string($bdd, $table) . "'");
    return $q && mysqli_num_rows($q) > 0;
}

function anvdko_site_column_exists($bdd, $table, $column)
{
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
    $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
    if ($table === '' || $column === '') {
        return false;
    }
    $q = mysqli_query($bdd, "SHOW COLUMNS FROM `$table` LIKE '" . mysqli_real_escape_string($bdd, $column) . "'");
    return $q && mysqli_num_rows($q) > 0;
}

function anvdko_public_base_path()
{
    if (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false)) {
        return '/anvdko';
    }
    return '';
}

function anvdko_public_img_url($path)
{
    $path = trim((string) $path);
    if ($path === '') {
        return anvdko_public_base_path() . '/assets/img/LOGO.jpg';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    $base = anvdko_public_base_path();
    if (strpos($path, 'fichiers/') === 0) {
        return $base . '/' . $path;
    }
    if (strpos($path, '/') === 0) {
        return $path;
    }
    return $base . '/' . ltrim($path, '/');
}

function anvdko_site_default_slides()
{
    return [
        ['titre' => 'Association <span>de la Nouvelle Vision pour le Développement de Kouakou Oussoukro (ANVDKO)</span>', 'texte' => 'Bienvenue sur la plateforme de l\'ANVDKO, une association de jeunes dynamiques originaires de Kouakou Oussoukro.', 'image_url' => 'assets/img/slide/reunion.png', 'lien_bouton' => '#about', 'texte_bouton' => 'En savoir plus'],
        ['titre' => 'Nos valeurs : Engagement & Solidarité', 'texte' => 'L\'ANVDKO rassemble des jeunes motivés pour promouvoir l\'entraide et la cohésion sociale.', 'image_url' => 'assets/img/slide/reunion2.jpg', 'lien_bouton' => '#about', 'texte_bouton' => 'Découvrir nos valeurs'],
        ['titre' => 'Un avenir meilleur par l\'éducation', 'texte' => 'L\'association soutient la réussite scolaire des jeunes du village.', 'image_url' => 'assets/img/slide/reunion3.jpg', 'lien_bouton' => '#about', 'texte_bouton' => 'Accéder aux ressources'],
    ];
}

function anvdko_site_default_cartes()
{
    return [
        ['titre' => 'Calendrier des événements', 'resume' => 'Retrouvez toutes les dates importantes de nos activités à venir dans le village.', 'contenu_complet' => 'Consultez l\'agenda de l\'association pour ne manquer aucune activité.', 'image_url' => 'assets/img/compte_rendu.jpg'],
        ['titre' => 'Comptes-rendus de réunions', 'resume' => 'Accédez aux résumés de nos assemblées générales et réunions de coordination.', 'contenu_complet' => 'Les comptes-rendus détaillés sont publiés après chaque réunion du bureau.', 'image_url' => 'assets/img/compte_rendu2.jpg'],
        ['titre' => 'Projets en cours', 'resume' => 'Suivez l\'évolution des projets de développement portés par notre association.', 'contenu_complet' => 'Découvrez nos initiatives sur la page Projets du site.', 'image_url' => 'assets/img/slide/reunion.png'],
        ['titre' => 'Séances de sensibilisation', 'resume' => 'Des campagnes locales pour informer les jeunes sur des sujets clés.', 'contenu_complet' => 'Santé, citoyenneté, environnement : des thèmes abordés régulièrement.', 'image_url' => 'assets/img/slide/reunion1.jpg'],
        ['titre' => 'Activités communautaires', 'resume' => 'Nettoyage, reboisement, aménagement… l\'ANVDKO agit pour le bien de la communauté.', 'contenu_complet' => 'Rejoignez-nous lors des journées citoyennes et actions de terrain.', 'image_url' => 'assets/img/slide/reunion2.jpg'],
        ['titre' => 'Suivi scolaire des élèves', 'resume' => 'Des activités pour aider les élèves en difficulté scolaire.', 'contenu_complet' => 'Accompagnement et mentorat pour la réussite des jeunes du village.', 'image_url' => 'assets/img/reunion1.jpg'],
    ];
}

function anvdko_site_default_bureau()
{
    return [
        ['role_label' => 'Président', 'nom' => 'KONAN VICTORIEN KOUADIO', 'image_url' => 'assets/img/team/team-1.jpg'],
        ['role_label' => 'Secrétaire Général(e)', 'nom' => 'N\'GUESSAN BENOIT KOUE', 'image_url' => 'assets/img/team/identite.jpg'],
        ['role_label' => 'Présidente des Femmes', 'nom' => 'AYA FLORENTINE KOUASSI', 'image_url' => 'assets/img/team/team-4.jpg'],
        ['role_label' => 'Commissaire aux Comptes', 'nom' => 'FRANCIS ARNAUD YAO', 'image_url' => 'assets/img/team/arnaud.jpg'],
    ];
}

function anvdko_site_stats($bdd)
{
    $stats = ['membres' => 0, 'evenements' => 0, 'projets' => 0, 'prets' => 0];

    if (anvdko_site_table_exists($bdd, 'adhesion')) {
        $q = mysqli_query($bdd, "SELECT COUNT(DISTINCT id_membre) AS n FROM adhesion WHERE statut = 'Payé'");
        if ($q && ($r = mysqli_fetch_assoc($q))) {
            $stats['membres'] = (int) $r['n'];
        }
    }

    if (anvdko_site_table_exists($bdd, 'evenements')) {
        $q = mysqli_query($bdd, "SELECT COUNT(*) AS n FROM evenements");
        if ($q && ($r = mysqli_fetch_assoc($q))) {
            $stats['evenements'] = (int) $r['n'];
        }
    } elseif (anvdko_site_table_exists($bdd, 'agenda')) {
        $q = mysqli_query($bdd, "SELECT COUNT(*) AS n FROM agenda");
        if ($q && ($r = mysqli_fetch_assoc($q))) {
            $stats['evenements'] = (int) $r['n'];
        }
    }

    if (anvdko_site_table_exists($bdd, 'projets_public')) {
        $q = mysqli_query($bdd, "SELECT COUNT(*) AS n FROM projets_public WHERE actif = 1");
        if ($q && ($r = mysqli_fetch_assoc($q))) {
            $stats['projets'] = (int) $r['n'];
        }
    }

    if (anvdko_site_table_exists($bdd, 'faire_don')) {
        $q = mysqli_query($bdd, "SELECT COUNT(*) AS n FROM faire_don");
        if ($q && ($r = mysqli_fetch_assoc($q))) {
            $stats['prets'] = (int) $r['n'];
        }
    }

    return $stats;
}

function anvdko_site_event_date_valid($value)
{
    $value = trim((string) $value);
    if ($value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
        return false;
    }
    return strtotime($value) !== false;
}

/** Événement à venir ou sans date renseignée (brouillon publié en alerte). */
function anvdko_site_event_is_upcoming($row)
{
    $debut = $row['date_debut'] ?? '';
    $fin = $row['date_fin'] ?? '';
    $has_debut = anvdko_site_event_date_valid($debut);
    $has_fin = anvdko_site_event_date_valid($fin);

    if (!$has_debut && !$has_fin) {
        return true;
    }

    $now = time();
    if ($has_fin && strtotime($fin) >= $now) {
        return true;
    }
    if ($has_debut && strtotime($debut) >= $now) {
        return true;
    }

    return false;
}

function anvdko_site_evenements_alerte($bdd, $id_configuration = 1)
{
    if (!anvdko_site_table_exists($bdd, 'evenements')) {
        return [];
    }
    $id_configuration = (int) $id_configuration;
    $filtre_alerte = anvdko_site_column_exists($bdd, 'evenements', 'afficher_alerte_site')
        ? ' AND (afficher_alerte_site = 1 OR afficher_alerte_site IS NULL)'
        : '';
    $q = mysqli_query(
        $bdd,
        "SELECT id, titre, lieu, date_debut, date_fin, description, n_event
         FROM evenements
         WHERE id_configuration = $id_configuration
         $filtre_alerte
         ORDER BY id DESC
         LIMIT 30"
    );
    $rows = [];
    $config_tel = '';
    if (anvdko_site_table_exists($bdd, 'configurations')) {
        $cq = mysqli_query($bdd, "SELECT contact1, contact2 FROM configurations WHERE id = $id_configuration LIMIT 1");
        if ($cq && ($c = mysqli_fetch_assoc($cq))) {
            $config_tel = trim($c['contact1'] ?? '') ?: trim($c['contact2'] ?? '');
        }
    }
    while ($q && ($row = mysqli_fetch_assoc($q))) {
        if (!anvdko_site_event_is_upcoming($row)) {
            continue;
        }
        $tel = $config_tel;
        if (anvdko_site_column_exists($bdd, 'evenements', 'contact_telephone') && !empty($row['contact_telephone'])) {
            $tel = $row['contact_telephone'];
        }
        $row['contact_telephone'] = $tel;
        $row['description_plain'] = trim(strip_tags(html_entity_decode((string) ($row['description'] ?? ''), ENT_QUOTES, 'UTF-8')));
        $rows[] = $row;
        if (count($rows) >= 10) {
            break;
        }
    }
    return $rows;
}

function anvdko_load_site_data($bdd, $id_configuration = 1)
{
    $id_configuration = (int) $id_configuration;
    $data = [
        'slides' => anvdko_site_default_slides(),
        'about' => [
            'qui_titre' => 'Qui sommes-nous ?',
            'qui_sous_titre' => 'L\'Association de la Nouvelle Vision pour le Développement de Kouakou Oussoukro (ANVDKO)',
            'qui_texte' => 'Fondée par des jeunes engagés originaires de Kouakou Oussoukro et de la sous-préfecture de Djébonoua, notre association œuvre pour le développement durable de notre circonscription.',
            'image_url' => 'assets/img/reunion1.jpg',
            'mission_titre' => 'Notre mission',
            'mission_texte' => 'Promouvoir l\'accès aux ressources éducatives, encourager le partage de connaissances, organiser des activités culturelles et éducatives.',
            'equipe_titre' => 'Notre équipe',
            'equipe_texte' => 'L\'ANVDKO est dirigée par un bureau composé de jeunes motivés et passionnés.',
            'points_liste' => [
                'Encourager la solidarité et le partage de connaissances.',
                'Offrir des ressources et des outils pour la réussite scolaire.',
                'Organiser des événements éducatifs et culturels.',
            ],
            'citation' => 'Ensemble, construisons un avenir prometteur pour Kouakou Oussoukro et ses jeunes !',
            'pdf_url' => 'documents/Imprimer_badges.pdf',
        ],
        'cartes' => anvdko_site_default_cartes(),
        'bureau' => anvdko_site_default_bureau(),
        'galerie' => [],
        'faq_extra' => [],
        'stats' => anvdko_site_stats($bdd),
        'evenements_alerte' => anvdko_site_evenements_alerte($bdd, $id_configuration),
    ];

    if (anvdko_site_table_exists($bdd, 'site_slides')) {
        $q = mysqli_query($bdd, "SELECT * FROM site_slides WHERE id_configuration = $id_configuration AND actif = 1 ORDER BY ordre ASC, id ASC");
        $slides = [];
        while ($q && ($row = mysqli_fetch_assoc($q))) {
            $slides[] = $row;
        }
        if (count($slides) > 0) {
            $data['slides'] = $slides;
        }
    }

    if (anvdko_site_table_exists($bdd, 'site_about')) {
        $q = mysqli_query($bdd, "SELECT * FROM site_about WHERE id = 1 LIMIT 1");
        if ($q && mysqli_num_rows($q)) {
            $about = mysqli_fetch_assoc($q);
            $pts = json_decode($about['points_liste'] ?? '[]', true);
            $about['points_liste'] = is_array($pts) ? $pts : $data['about']['points_liste'];
            $data['about'] = array_merge($data['about'], $about);
        }
    }

    if (anvdko_site_table_exists($bdd, 'site_accueil_cartes')) {
        $q = mysqli_query($bdd, "SELECT * FROM site_accueil_cartes WHERE id_configuration = $id_configuration AND actif = 1 ORDER BY ordre ASC, id ASC");
        $cartes = [];
        while ($q && ($row = mysqli_fetch_assoc($q))) {
            $cartes[] = $row;
        }
        if (count($cartes) > 0) {
            $data['cartes'] = $cartes;
        }
    }

    if (anvdko_site_table_exists($bdd, 'site_bureau')) {
        $q = mysqli_query($bdd, "SELECT * FROM site_bureau WHERE id_configuration = $id_configuration AND actif = 1 ORDER BY ordre ASC, id ASC LIMIT 4");
        $bureau = [];
        while ($q && ($row = mysqli_fetch_assoc($q))) {
            $bureau[] = $row;
        }
        if (count($bureau) > 0) {
            $data['bureau'] = $bureau;
        }
    }

    if (anvdko_site_table_exists($bdd, 'site_galerie')) {
        $q = mysqli_query($bdd, "SELECT * FROM site_galerie WHERE id_configuration = $id_configuration AND actif = 1 ORDER BY ordre ASC, id ASC");
        while ($q && ($row = mysqli_fetch_assoc($q))) {
            $data['galerie'][] = $row;
        }
    }

    if (anvdko_site_table_exists($bdd, 'site_faq')) {
        $q = mysqli_query($bdd, "SELECT * FROM site_faq WHERE id_configuration = $id_configuration AND actif = 1 ORDER BY ordre ASC, id ASC");
        while ($q && ($row = mysqli_fetch_assoc($q))) {
            $data['faq_extra'][] = $row;
        }
    }

    return $data;
}

function anvdko_esc_html($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function anvdko_esc_attr($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}
