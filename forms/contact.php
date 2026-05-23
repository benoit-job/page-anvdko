<?php
/**
 * Fichier: forms/contact.php
 * Version avec debug et gestion CORS
 */

// IMPORTANT: Activer l'affichage des erreurs pour debug (à désactiver en production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Headers CORS et JSON (DOIT être au tout début)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=UTF-8');

// Gérer les requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configuration
define('RECEIVING_EMAIL', 'kouebenoit@gmail.com');
define('SITE_NAME', 'ANVDKO');
define('ADMIN_EMAIL', 'noreply@anvdko.site'); // Email d'envoi

// Log des erreurs dans un fichier
function logError($message) {
    $logFile = __DIR__ . '/contact_errors.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

// Fonction de réponse JSON
function sendResponse($success, $message, $debug = []) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    // Ajouter les infos de debug si erreur
    if (!$success && !empty($debug)) {
        $response['debug'] = $debug;
    }
    
    echo json_encode($response);
    exit;
}

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Méthode non autorisée', [
        'method' => $_SERVER['REQUEST_METHOD'],
        'expected' => 'POST'
    ]);
}

// Log de la requête
logError("Nouvelle requête reçue - IP: " . $_SERVER['REMOTE_ADDR']);

// Récupérer les données POST
$name = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : '';
$email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$subject = isset($_POST['subject']) ? strip_tags(trim($_POST['subject'])) : '';
$message = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : '';

// Log des données reçues
logError("Données reçues - Name: $name, Email: $email, Subject: $subject");

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = 'Le nom est requis';
}

if (empty($email)) {
    $errors[] = 'L\'email est requis';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'L\'email n\'est pas valide';
}

if (empty($subject)) {
    $errors[] = 'Le sujet est requis';
}

if (empty($message)) {
    $errors[] = 'Le message est requis';
} elseif (strlen($message) < 10) {
    $errors[] = 'Le message doit contenir au moins 10 caractères';
}

if (!empty($errors)) {
    logError("Erreurs de validation: " . implode(', ', $errors));
    sendResponse(false, implode(', ', $errors));
}

// Protection anti-spam simple
$honeypot = isset($_POST['website']) ? $_POST['website'] : '';
if (!empty($honeypot)) {
    logError("Spam détecté (honeypot rempli)");
    sendResponse(true, 'Message envoyé'); // Faux succès pour tromper les bots
}

// Préparer l'email
$to = RECEIVING_EMAIL;
$email_subject = "[" . SITE_NAME . "] " . $subject;

// Corps HTML de l'email
$email_body = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; border-radius: 10px; }
        .header { background: linear-gradient(135deg, #3a336e, #8d4eb5); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: white; padding: 30px; border-radius: 0 0 10px 10px; }
        .info-row { margin-bottom: 15px; padding: 10px; background: #f5f5f5; border-left: 4px solid #3a336e; }
        .label { font-weight: bold; color: #3a336e; }
        .message-box { background: #fff; border: 1px solid #ddd; padding: 20px; margin-top: 20px; border-radius: 5px; white-space: pre-wrap; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>📧 Nouveau message depuis " . SITE_NAME . "</h2>
        </div>
        <div class='content'>
            <div class='info-row'><span class='label'>👤 Nom :</span> " . htmlspecialchars($name) . "</div>
            <div class='info-row'><span class='label'>✉️ Email :</span> <a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a></div>
            <div class='info-row'><span class='label'>📋 Sujet :</span> " . htmlspecialchars($subject) . "</div>
            <div class='info-row'><span class='label'>📅 Date :</span> " . date('d/m/Y à H:i:s') . "</div>
            <h3 style='color: #3a336e; margin-top: 30px;'>💬 Message :</h3>
            <div class='message-box'>" . nl2br(htmlspecialchars($message)) . "</div>
            <div class='footer'>
                <p>Ce message a été envoyé depuis le formulaire de contact</p>
                <p>Pour répondre, utilisez : " . htmlspecialchars($email) . "</p>
            </div>
        </div>
    </div>
</body>
</html>
";

// Version texte
$email_body_text = "
Nouveau message depuis " . SITE_NAME . "
=========================================

Nom: $name
Email: $email
Sujet: $subject
Date: " . date('d/m/Y à H:i:s') . "

Message:
--------
$message

---
Pour répondre: $email
";

// Headers
$headers = "From: " . SITE_NAME . " <" . ADMIN_EMAIL . ">\r\n";
$headers .= "Reply-To: " . $name . " <" . $email . ">\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Tentative d'envoi
try {
    $mail_sent = @mail($to, $email_subject, $email_body, $headers);
    
    if ($mail_sent) {
        logError("Email envoyé avec succès à $to");
        sendResponse(true, 'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.');
    } else {
        // Si mail() échoue, essayer une version simplifiée
        logError("Échec de l'envoi mail() - Tentative version simplifiée");
        
        $simple_headers = "From: " . ADMIN_EMAIL . "\r\n";
        $simple_headers .= "Reply-To: " . $email . "\r\n";
        
        $retry = @mail($to, $email_subject, $email_body_text, $simple_headers);
        
        if ($retry) {
            logError("Email envoyé en version simplifiée");
            sendResponse(true, 'Votre message a été envoyé avec succès !');
        } else {
            logError("Échec complet de l'envoi email");
            
            // Sauvegarder le message dans un fichier en cas d'échec
            $backup_file = __DIR__ . '/messages_backup.txt';
            $backup_content = "\n\n=== Message du " . date('Y-m-d H:i:s') . " ===\n";
            $backup_content .= "De: $name ($email)\n";
            $backup_content .= "Sujet: $subject\n";
            $backup_content .= "Message:\n$message\n";
            file_put_contents($backup_file, $backup_content, FILE_APPEND);
            
            sendResponse(false, 'Erreur lors de l\'envoi. Le message a été sauvegardé et sera traité manuellement.', [
                'mail_function' => function_exists('mail') ? 'disponible' : 'indisponible',
                'error_get_last' => error_get_last()
            ]);
        }
    }
} catch (Exception $e) {
    logError("Exception: " . $e->getMessage());
    sendResponse(false, 'Une erreur est survenue: ' . $e->getMessage());
}
?>