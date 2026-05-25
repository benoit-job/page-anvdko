-- CMS site public ANVDKO (accueil) — exécuter sur anvdko_oussoukro

CREATE TABLE IF NOT EXISTS site_slides (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_configuration BIGINT UNSIGNED NOT NULL DEFAULT 1,
  titre VARCHAR(500) NOT NULL DEFAULT '',
  sous_titre VARCHAR(500) DEFAULT '',
  texte TEXT,
  image_url VARCHAR(1024) NOT NULL DEFAULT '',
  lien_bouton VARCHAR(512) DEFAULT '#about',
  texte_bouton VARCHAR(128) DEFAULT 'En savoir plus',
  ordre BIGINT UNSIGNED NOT NULL DEFAULT 0,
  actif TINYINT(1) NOT NULL DEFAULT 1,
  date_heure DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS site_about (
  id BIGINT UNSIGNED PRIMARY KEY,
  id_configuration BIGINT UNSIGNED NOT NULL DEFAULT 1,
  qui_titre VARCHAR(255) DEFAULT 'Qui sommes-nous ?',
  qui_sous_titre VARCHAR(500) DEFAULT '',
  qui_texte TEXT,
  image_url VARCHAR(1024) DEFAULT 'assets/img/reunion1.jpg',
  mission_titre VARCHAR(255) DEFAULT 'Notre mission',
  mission_texte TEXT,
  equipe_titre VARCHAR(255) DEFAULT 'Notre équipe',
  equipe_texte TEXT,
  points_liste TEXT COMMENT 'JSON array de strings',
  citation TEXT,
  pdf_url VARCHAR(1024) DEFAULT 'documents/Imprimer_badges.pdf',
  date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS site_accueil_cartes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_configuration BIGINT UNSIGNED NOT NULL DEFAULT 1,
  titre VARCHAR(255) NOT NULL,
  resume TEXT,
  contenu_complet TEXT,
  image_url VARCHAR(1024) NOT NULL DEFAULT '',
  ordre BIGINT UNSIGNED NOT NULL DEFAULT 0,
  actif TINYINT(1) NOT NULL DEFAULT 1,
  date_heure DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS site_bureau (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_configuration BIGINT UNSIGNED NOT NULL DEFAULT 1,
  role_label VARCHAR(128) NOT NULL DEFAULT '',
  nom VARCHAR(255) NOT NULL DEFAULT '',
  image_url VARCHAR(1024) NOT NULL DEFAULT '',
  linkedin VARCHAR(512) DEFAULT '',
  twitter VARCHAR(512) DEFAULT '',
  facebook VARCHAR(512) DEFAULT '',
  instagram VARCHAR(512) DEFAULT '',
  ordre BIGINT UNSIGNED NOT NULL DEFAULT 0,
  actif TINYINT(1) NOT NULL DEFAULT 1,
  date_heure DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS site_galerie (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_configuration BIGINT UNSIGNED NOT NULL DEFAULT 1,
  titre VARCHAR(255) NOT NULL DEFAULT '',
  description TEXT,
  image_url VARCHAR(1024) NOT NULL DEFAULT '',
  ordre BIGINT UNSIGNED NOT NULL DEFAULT 0,
  actif TINYINT(1) NOT NULL DEFAULT 1,
  date_heure DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS site_faq (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_configuration BIGINT UNSIGNED NOT NULL DEFAULT 1,
  question VARCHAR(500) NOT NULL,
  reponse TEXT NOT NULL,
  ordre BIGINT UNSIGNED NOT NULL DEFAULT 0,
  actif TINYINT(1) NOT NULL DEFAULT 1,
  date_heure DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO site_about (
  id,
  id_configuration,
  qui_titre,
  qui_sous_titre,
  qui_texte,
  image_url
) VALUES (
  1,
  1,
  'Qui sommes-nous ?',
  'L''Association de la Nouvelle Vision pour le Développement de Kouakou Oussoukro (ANVDKO)',
  'Fondée par des jeunes engagés originaires de Kouakou Oussoukro et de la sous-préfecture de Djébonoua, notre association œuvre pour le développement durable de notre circonscription.',
  'assets/img/reunion1.jpg'
);

-- Colonnes événements (ignorer l'erreur si la colonne existe déjà)
-- ALTER TABLE evenements ADD COLUMN contact_telephone VARCHAR(32) DEFAULT NULL;
-- ALTER TABLE evenements ADD COLUMN afficher_alerte_site TINYINT(1) NOT NULL DEFAULT 1;