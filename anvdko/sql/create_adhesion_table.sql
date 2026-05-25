-- Table pour gérer les adhésions des membres
CREATE TABLE IF NOT EXISTS adhesion (
    id_adhesion BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_membre BIGINT UNSIGNED NOT NULL,
    id_utilisateur BIGINT UNSIGNED NULL,
    montant DECIMAL(10, 2) NOT NULL,
    date_heure DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('Payé', 'Non payé', 'Moitié payé') DEFAULT 'Non payé',
    notes TEXT,

    INDEX idx_id_membre (id_membre),
    INDEX idx_statut (statut),
    INDEX idx_date_heure (date_heure),

    CONSTRAINT fk_adhesion_membre
        FOREIGN KEY (id_membre)
        REFERENCES membres(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_adhesion_utilisateur
        FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateurs(id)
        ON DELETE SET NULL

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

-- Ajouter les colonnes montant_adhesion et montant_mensuel dans configurations s'ils n'existent pas
ALTER TABLE configurations
ADD COLUMN montant_adhesion DECIMAL(10,2) DEFAULT 2000;

ALTER TABLE configurations
ADD COLUMN montant_mensuel DECIMAL(10,2) DEFAULT 2000;