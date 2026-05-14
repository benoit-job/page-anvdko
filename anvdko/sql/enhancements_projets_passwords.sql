-- À exécuter sur la base anvdko_oussoukro (ou votre base active)
-- Projets page publique + témoignages + textes d'intro

CREATE TABLE IF NOT EXISTS projets_public (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titre VARCHAR(255) NOT NULL,
  description TEXT,
  image_url VARCHAR(1024) NOT NULL DEFAULT '',
  lien_url VARCHAR(1024) DEFAULT '#',
  statut_badge VARCHAR(64) DEFAULT 'En cours',
  ordre INT NOT NULL DEFAULT 0,
  actif TINYINT(1) NOT NULL DEFAULT 1,
  date_heure DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS projets_temoignages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  citation TEXT NOT NULL,
  auteur VARCHAR(255) DEFAULT '',
  ordre INT NOT NULL DEFAULT 0,
  actif TINYINT(1) NOT NULL DEFAULT 1,
  date_heure DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS projets_page_textes (
  id INT PRIMARY KEY,
  titre_header VARCHAR(255) DEFAULT 'Projets de ANVDKO',
  sous_titre TEXT,
  intro_paragraph TEXT,
  cta_titre VARCHAR(255) DEFAULT 'Participez à nos projets !',
  cta_texte TEXT,
  date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO projets_page_textes (id, titre_header, sous_titre, intro_paragraph, cta_titre, cta_texte) VALUES
(1,
 'Projets de ANVDKO',
 'Découvrez les initiatives qui font avancer notre village et rejoignez-nous pour construire l''avenir ensemble.',
 'Depuis sa création, l''association ANVDKO s''engage dans des projets durables qui visent à améliorer les conditions de vie dans notre village.',
 'Participez à nos projets !',
 'Vous souhaitez soutenir nos actions ? Que ce soit par du bénévolat, un don, ou un partenariat, votre engagement est précieux.'
);

-- Utilisateurs : mot de passe haché (longueur suffisante pour bcrypt)
ALTER TABLE utilisateurs MODIFY COLUMN password VARCHAR(255) NULL;
ALTER TABLE membres MODIFY COLUMN password VARCHAR(255) NOT NULL DEFAULT '';
