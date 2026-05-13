-- Table principale : depenses_anvdko
CREATE TABLE IF NOT EXISTS depenses_anvdko (
    id_depense INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    categorie VARCHAR(100) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    date_depense DATE NOT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des montants multiples : depense_montants
CREATE TABLE IF NOT EXISTS depense_montants (
    id_montant INT AUTO_INCREMENT PRIMARY KEY,
    id_depense INT NOT NULL,
    montant DECIMAL(15,2) NOT NULL,
    date_paiement DATE NOT NULL,
    FOREIGN KEY (id_depense) REFERENCES depenses_anvdko(id_depense) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des commentaires : depense_commentaires
CREATE TABLE IF NOT EXISTS depense_commentaires (
    id_commentaire INT AUTO_INCREMENT PRIMARY KEY,
    id_depense INT NOT NULL,
    id_user INT NOT NULL,
    commentaire TEXT NOT NULL,
    date_commentaire TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_depense) REFERENCES depenses_anvdko(id_depense) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
