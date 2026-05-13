<?php
include("../include/php/connexion_bdd.php");

$data = json_decode(file_get_contents("php://input"), true);

try {
    $stmt = $pdo->prepare("INSERT INTO exceptionnels_pay 
        (id_membre, id_motif, mois_payer, a_payer, paye, reste, date_paiement)
        VALUES (?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $data['id_membre'],
        $data['id_motif'],
        $data['mois_payer'],
        $data['a_payer'],
        $data['paye'],
        $data['reste'],
        $data['date_paiement']
    ]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
