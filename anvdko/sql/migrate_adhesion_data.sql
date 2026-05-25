-- Script de migration : Copier les adhésions existantes vers la nouvelle table adhesion
-- Exécuter ce script après avoir exécuté create_adhesion_table.sql

-- Étape 1 : Copier les adhésions payées existantes
INSERT INTO adhesion (id_membre, montant, statut, date_heure)
SELECT 
    m.id,
    COALESCE(c.montant_adhesion, 2000) as montant,
    COALESCE(m.statut_ad, 'Non payé') as statut,
    m.date_heure as date_heure
FROM membres m
CROSS JOIN configurations c
WHERE m.statut_ad IS NOT NULL AND m.statut_ad != ''
ORDER BY m.id
ON DUPLICATE KEY UPDATE 
    montant = VALUES(montant),
    statut = VALUES(statut),
    date_heure = VALUES(date_heure);

-- Étape 2 : Pour les membres sans statut d'adhésion, créer une entrée "Non payé"
INSERT IGNORE INTO adhesion (id_membre, montant, statut, date_heure)
SELECT 
    m.id,
    COALESCE(c.montant_adhesion, 2000) as montant,
    'Non payé' as statut,
    m.date_heure as date_heure
FROM membres m
CROSS JOIN configurations c
WHERE m.id NOT IN (SELECT DISTINCT id_membre FROM adhesion);

-- Étape 3 : Vérifier les résultats
SELECT 
    COUNT(*) as total_adhesions,
    SUM(CASE WHEN statut = 'Payé' THEN 1 ELSE 0 END) as payees,
    SUM(CASE WHEN statut = 'Non payé' THEN 1 ELSE 0 END) as non_payees,
    SUM(CASE WHEN statut = 'Moitié payé' THEN 1 ELSE 0 END) as moitie_payees
FROM adhesion;

-- Étape 4 : Afficher les montants configurés
SELECT id, montant_adhesion, montant_mensuel FROM configurations;

-- Note : Les colonnes statut_ad et date_statut_ad dans la table membres peuvent rester
-- pour la compatibilité rétro-active, mais les nouvelles opérations utiliseront la table adhesion
