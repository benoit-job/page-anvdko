<?php
require_once '../../includes/main_include.php';
checkUserAccess('gestion_depenses');

header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => 'Une erreur est survenue',
    'data' => null
];

try {
    if (empty($_POST['id_depense'])) {
        throw new Exception("ID de dépense manquant");
    }
    
    $id_depense = $_POST['id_depense'];
    $id_user = $_SESSION['user_id'];
    
    // Get expense details
    $stmt = $pdo->prepare("
        SELECT d.*, u.nom, u.prenom 
        FROM depenses_anvdko d
        LEFT JOIN users u ON d.id_user = u.id
        WHERE d.id_depense = :id_depense
        AND (d.id_user = :id_user OR :is_admin = 1)
    ");
    
    $stmt->execute([
        ':id_depense' => $id_depense,
        ':id_user' => $id_user,
        ':is_admin' => $_SESSION['is_admin'] ?? 0
    ]);
    
    $expense = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$expense) {
        throw new Exception("Dépense non trouvée ou accès refusé");
    }
    
    // Get payments
    $stmt = $pdo->prepare("
        SELECT * FROM depense_montants 
        WHERE id_depense = :id_depense
        ORDER BY date_paiement ASC
    ");
    
    $stmt->execute([':id_depense' => $id_depense]);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate total amount
    $totalAmount = 0;
    foreach ($payments as $payment) {
        $totalAmount += $payment['montant'];
    }
    
    $expense['montants'] = $payments;
    $expense['montant_total'] = $totalAmount;
    
    $response = [
        'status' => 'success',
        'message' => 'Détails de la dépense récupérés avec succès',
        'data' => $expense
    ];
    
} catch (Exception $e) {
    http_response_code(400);
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
