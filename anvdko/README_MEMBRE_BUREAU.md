# Système de paiement différencié pour les membres du bureau et par genre

## Description

Ce système permet de différencier les cotisations exceptionnelles selon :

- Le statut du membre (membre du bureau ou membre ordinaire)
- Le genre du membre (Homme, Femme, Mademoiselle)

## Modifications apportées

### 1. Base de données

- **Fichier SQL 1** : `add_membre_bureau_column.sql`
  - **Action** : Ajouter la colonne `membre_bureau` (TINYINT, 0 ou 1) dans la table `membres`
  - **Valeur par défaut** : 0 (membre ordinaire)
- **Fichier SQL 2** : `add_montant_femme_column.sql`
  - **Action** : Ajouter la colonne `montant_femme` (DECIMAL) dans la table `config_cotisations_exceptionnelles`
  - **Valeur par défaut** : NULL (optionnel)
- **À exécuter** : Exécuter les deux scripts SQL dans votre base de données

### 2. Gestion des membres (ANVDKO)

- **Fichier** : `anvdko/adherent_details.php`
- **Modification** :
  - Ajout d'un checkbox "Membre du bureau" dans le formulaire de modification
  - Mise à jour de la requête UPDATE pour inclure le champ `membre_bureau`
- **Accès** : Seuls les super administrateurs dans le dossier ANVDKO peuvent modifier ce statut

### 3. Cotisations exceptionnelles

- **Fichier** : `anvdko/exceptionnels_pay.php`
- **Modifications** :
  - La fonction `ProgressBarPeriodeGlobale` détermine automatiquement le montant à utiliser selon le statut du membre
  - Les dialogues de paiement affichent le bon montant (standard ou bureau)
  - Le système utilise `montant_bureau` si le membre est du bureau ET si un montant bureau est défini
  - Sinon, le système utilise `montant_standard` pour tous

### 4. Fichiers de traitement

- **Fichier** : `anvdko/get_multiple_membres_info.php`

  - Prend en compte le statut `membre_bureau` pour déterminer le montant à payer
  - Retourne `montant_a_payer` selon le statut du membre

- **Fichier** : `anvdko/get_membre_info.php` (nouveau)
  - Récupère les informations d'un membre et détermine son montant à payer

## Fonctionnement

### Lors de la création d'une cotisation exceptionnelle :

1. L'administrateur spécifie :
   - Un `montant_standard` (obligatoire) pour les hommes
   - Un `montant_femme` (optionnel) pour les femmes et mademoiselles
   - Un `montant_bureau` (optionnel) pour les membres du bureau

### Lors du paiement :

Le système détermine le montant selon la priorité suivante :

1. **Priorité 1** : Si `membre_bureau = 1` ET `montant_bureau` est défini
   - Le membre paie le `montant_bureau`
2. **Priorité 2** : Si genre = `FEMME` ou `MADEMOISELLE` ET `montant_femme` est défini
   - Le membre paie le `montant_femme`
3. **Priorité 3** : Sinon
   - Le membre paie le `montant_standard`

### Règles :

- **Par défaut**, tous les membres paient le `montant_standard` (hommes)
- **Si `montant_femme` est spécifié** : Les femmes et mademoiselles paient ce montant (sauf si elles sont membres du bureau avec un montant_bureau défini)
- **Si `montant_bureau` est spécifié** : Les membres du bureau paient ce montant (priorité la plus haute)
- **Si aucun montant spécial n'est spécifié** : Tous les membres paient le `montant_standard`

### Exemples :

- **Homme ordinaire** : paie `montant_standard`
- **Femme ordinaire** : paie `montant_femme` (si défini), sinon `montant_standard`
- **Mademoiselle ordinaire** : paie `montant_femme` (si défini), sinon `montant_standard`
- **Homme du bureau** : paie `montant_bureau` (si défini), sinon `montant_standard`
- **Femme du bureau** : paie `montant_bureau` (si défini), sinon `montant_femme` (si défini), sinon `montant_standard`

## Notes importantes

- Le dossier `membres/` est pour les membres individuels et ne permet PAS de modifier le statut membre_bureau
- Seul le dossier `anvdko/` permet aux super administrateurs de modifier ce statut
- Le statut est modifiable uniquement lors de la modification d'un membre dans `adherent_details.php`
