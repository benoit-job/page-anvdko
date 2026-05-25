# Documentation - Système de Gestion des Adhésions

## 📋 Résumé des modifications

Ce document décrit les changements apportés pour implémenter un système complet de gestion des adhésions avec montants configurables et stockage en base de données.

---

## 1. 🗄️ Modifications de la base de données

### Fichier SQL : `sql/create_adhesion_table.sql`

**Créer la table `adhesion`** :

```sql
CREATE TABLE IF NOT EXISTS adhesion (
    id_adhesion INT AUTO_INCREMENT PRIMARY KEY,
    id_membre INT NOT NULL,
    id_utilisateur INT,
    montant DECIMAL(10, 2) NOT NULL,
    date_heure DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('Payé', 'Non payé', 'Moitié payé') DEFAULT 'Non payé',
    notes TEXT,
    FOREIGN KEY (id_membre) REFERENCES membres(id) ON DELETE CASCADE,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE SET NULL
);
```

**Modifier la table `configurations`** :

- Ajouter colonne `montant_adhesion` (DECIMAL)
- Ajouter colonne `montant_mensuel` (DECIMAL)

---

## 2. ⚙️ Configuration des montants

### Fichier modifié : `configuration.php`

**Nouveaux champs ajoutés au formulaire** :

- **Montant d'adhésion (FCFA)** : Définir le montant que les nouveaux membres doivent payer
- **Cotisation mensuelle (FCFA)** : Remplace la valeur en dur de 1000 FCFA

**Traitement** :

- Les deux montants sont sauvegardés dans la table `configurations`
- Les fonctions qui utilisent ces montants vont les récupérer depuis la configuration

---

## 3. 📄 Nouvelle page : Gestion des Adhésions

### Fichier : `adhesion.php`

**Fonctionnalités** :

1. **Tableau des adhésions** :
   - Liste tous les membres inscrits
   - Affiche le montant d'adhésion payé
   - Affiche le statut (Payé / Non payé)
   - Checkboxes pour sélectionner les membres

2. **Récapitulatif** :
   - Nombre de membres inscrits
   - Nombre d'adhésions payées
   - Montant total collecté
   - Nombre de non-payants

3. **Actions** :
   - **Sélectionner tout** : Checkbox pour sélectionner/désélectionner tous les membres
   - **Payer adhésion** : Bouton pour enregistrer le paiement des adhésions sélectionnées
   - **Recherche** : Champ pour filtrer les membres par nom

---

## 4. 🔧 Fonctions AJAX

### Fichier : `ajax/adhesion_ajax.php`

**Actions disponibles** :

#### `action=loadAdhesions`

- Récupère tous les membres et leurs statuts d'adhésion
- Jointure avec la table `adhesion` pour récupérer le statut et le montant
- Retour en JSON avec la liste des adhésions

#### `action=payerAdhesion`

- Reçoit une liste d'ID de membres
- Utilise le montant configuré depuis `$_SESSION["configuration"]["montant_adhesion"]`
- Crée ou met à jour l'adhésion pour chaque membre sélectionné
- Définit le statut à 'Payé'
- Enregistre l'utilisateur qui a effectué l'action

---

## 5. 📝 Modifications des pages existantes

### `adherent_details.php`

- **Ligne 73** : Charge le statut d'adhésion depuis la table `adhesion` au lieu de `membres`
- **Section Adhésion** : Affiche les radios pour modifier le statut
- **Bouton Actualiser** : Met à jour la table `adhesion` au lieu de `membres`

### `ajax_autre.php`

- **Endpoint `actualiserStatutAdhesion`** :
  - Met à jour la table `adhesion` au lieu de `membres`
  - Crée une nouvelle ligne si aucune adhésion n'existe
- **Endpoint `liste_cartes`** :
  - Récupère les statuts d'adhésion depuis la table `adhesion`
  - Jointure LEFT JOIN pour gérer les cas où aucune adhésion existe

### `paiement_ajax.php`

- **Montant mensuel** : Utilise maintenant `$_SESSION["configuration"]["montant_mensuel"]` au lieu de valeur en dur

### `membres/includes/php/connexion_acces_page.php`

- **Fonction `getAdhesionStatut()`** : Récupère le statut depuis la table `adhesion`
- **Fonction `isAdhesionPayee()`** : Vérifie si le statut est "Payé"

---

## 6. 📌 Instruction d'installation

### Étape 1 : Exécuter le script SQL

```bash
# Exécuter le fichier : sql/create_adhesion_table.sql
# Cela créera la table adhesion et modifiera configurations
```

### Étape 2 : Accéder à la configuration

1. Aller dans **Configuration**
2. Modifier **Montant d'adhésion (FCFA)** et **Cotisation mensuelle (FCFA)**
3. Cliquer sur **Valider**

### Étape 3 : Gérer les adhésions

1. Aller dans **Gestion des Adhésions** (nouvelle page)
2. Voir la liste des adhésions
3. Sélectionner les membres pour qui marquer l'adhésion comme payée
4. Cliquer sur **Payer adhésion**

### Étape 4 : Détail d'un membre

1. Accéder à un profil membre via **Adhérents > Voir**
2. Section **Adhésion** : Voir et modifier le statut d'adhésion
3. Cliquer sur **Actualiser** pour mettre à jour

---

## 7. 💾 Statut de l'adhésion

Le statut d'adhésion peut être l'un des trois suivants :

- **Payé** : Adhesion complètement payée ✓
- **Non payé** : Aucun paiement effectué ✗
- **Moitié payé** : Paiement partiel (réservé pour les paiements partiels)

---

## 8. 🔐 Sécurité

- Tous les identifiants sont cryptés lors de la transmission
- Les montants sont validés et formatés correctement
- Vérification de session obligatoire pour toutes les modifications
- Transactions MySQL pour garantir l'intégrité des données

---

## 9. 📊 Migration des données existantes

Si vous avez des données existantes dans le champ `statut_ad` de la table `membres` :

```sql
-- Copier les adhésions payées existantes vers la nouvelle table
INSERT INTO adhesion (id_membre, montant, statut, date_heure)
SELECT id, 1000, statut_ad, NOW()
FROM membres
WHERE statut_ad IS NOT NULL AND statut_ad != ''
ON DUPLICATE KEY UPDATE statut = VALUES(statut);
```

---

## 10. ⚠️ Notes importantes

1. **Montants par configuration** : Les montants sont maintenant stockés dans la table `configurations` et doivent être modifiés via la page de configuration
2. **Historique** : Chaque paiement d'adhésion crée une entrée dans la table `adhesion` avec la date et l'utilisateur qui l'a effectué
3. **Compatibilité** : Le système est rétro-compatible avec l'ancien champ `statut_ad` pour la migration progressive

---

## 11. 🆘 Dépannage

### Le statut d'adhésion ne s'affiche pas correctement

- Vérifier que la table `adhesion` existe : `SHOW TABLES LIKE 'adhesion';`
- Vérifier que la colonne `montant_adhesion` existe : `DESCRIBE configurations;`

### Les montants ne sont pas à jour

- Vérifier la configuration dans `configuration.php`
- S'assurer que les sessions sont correctement rechargées après la modification

### L'action "Payer adhésion" ne fonctionne pas

- Vérifier les logs du serveur
- S'assurer que la table `adhesion` est accessible en écriture
- Vérifier que l'utilisateur a les droits nécessaires
