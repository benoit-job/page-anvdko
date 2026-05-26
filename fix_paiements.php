<?php
include('include/php/connexion_bdd.php');

// Récupérer le montant mensuel configuré
$res = mysqli_query($bdd, 'SELECT montant_mensuel, montant_adhesion FROM configurations LIMIT 1');
$config = mysqli_fetch_assoc($res);
$montant_config = floatval($config['montant_mensuel']);
echo "Montant mensuel configuré: " . $montant_config . " FCFA\n";
echo "Montant adhésion configuré: " . $config['montant_adhesion'] . " FCFA\n\n";

// Vérifier les paiements avec a_payer incorrect
$res2 = mysqli_query($bdd, 'SELECT p.id, p.id_membre, p.mois_payer, p.a_payer, p.paye, p.reste, p.statut, m.nom, m.prenom, m.date_heure as date_inscription FROM paiements p JOIN membres m ON p.id_membre = m.id ORDER BY m.nom, p.mois_payer');
echo "Paiements existants:\n";
while ($row = mysqli_fetch_assoc($res2)) {
    $flag = '';
    if (floatval($row['a_payer']) != $montant_config) {
        $flag = ' <<< a_payer INCORRECT (devrait être ' . $montant_config . ')';
    }
    echo $row['nom'] . ' ' . $row['prenom'] . ' | ' . $row['mois_payer'] . ' | a_payer=' . $row['a_payer'] . ' | paye=' . $row['paye'] . ' | reste=' . $row['reste'] . ' | statut=' . $row['statut'] . $flag . "\n";
}

echo "\n--- Correction: mise à jour des a_payer incorrects ---\n";
$update_sql = "UPDATE paiements SET a_payer = $montant_config, reste = GREATEST($montant_config - paye, 0), statut = CASE WHEN paye = 0 THEN 'Non payé' WHEN paye >= $montant_config THEN 'Payé' ELSE 'Moitié payé' END WHERE a_payer != $montant_config";
echo "SQL: $update_sql\n";
$result = mysqli_query($bdd, $update_sql);
if ($result) {
    echo "Lignes modifiées: " . mysqli_affected_rows($bdd) . "\n";
} else {
    echo "Erreur: " . mysqli_error($bdd) . "\n";
}

echo "\nVérification après correction:\n";
$res3 = mysqli_query($bdd, 'SELECT p.id, p.id_membre, p.mois_payer, p.a_payer, p.paye, p.reste, p.statut, m.nom, m.prenom FROM paiements p JOIN membres m ON p.id_membre = m.id ORDER BY m.nom, p.mois_payer');
while ($row = mysqli_fetch_assoc($res3)) {
    echo $row['nom'] . ' ' . $row['prenom'] . ' | ' . $row['mois_payer'] . ' | a_payer=' . $row['a_payer'] . ' | paye=' . $row['paye'] . ' | reste=' . $row['reste'] . ' | statut=' . $row['statut'] . "\n";
}
?>
