<?php
/**
 * Fichier: forms/contact.php
 * VERSION LOCALE - Simulation d'envoi pour développement
 */

// Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configuration
define('RECEIVING_EMAIL', 'kouebenoit@gmail.com');
define('SITE_NAME', 'ANVDKO');
define('MODE_LOCAL', true); // ⚠️ Mettre à false en production

function sendResponse($success, $message, $debug = []) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if (!empty($debug)) {
        $response['debug'] = $debug;
    }
    
    echo json_encode($response);
    exit;
}

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Méthode non autorisée');
}

// Récupérer les données
$name = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : '';
$email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$subject = isset($_POST['subject']) ? strip_tags(trim($_POST['subject'])) : '';
$message = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : '';

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
    sendResponse(false, implode(', ', $errors));
}

// Protection anti-spam
$honeypot = isset($_POST['website']) ? $_POST['website'] : '';
if (!empty($honeypot)) {
    sendResponse(true, 'Message envoyé');
}

// ======================================
// MODE LOCAL : Sauvegarder dans un fichier
// ======================================
if (MODE_LOCAL) {
    $messagesFile = __DIR__ . '/messages_locaux.html';
    
    // Créer le contenu HTML
    $messageHTML = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
            .message-card { 
                background: white; 
                border-radius: 10px; 
                padding: 30px; 
                margin-bottom: 30px; 
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                max-width: 800px;
                margin: 30px auto;
            }
            .header { 
                background: linear-gradient(135deg, #3a336e, #8d4eb5); 
                color: white; 
                padding: 20px; 
                border-radius: 10px;
                margin-bottom: 20px;
            }
            .info-row { 
                padding: 12px; 
                margin: 10px 0; 
                background: #f9f9f9; 
                border-left: 4px solid #3a336e;
                border-radius: 5px;
            }
            .label { font-weight: bold; color: #3a336e; display: inline-block; width: 120px; }
            .message-content { 
                background: #fff; 
                border: 2px solid #e0e0e0; 
                padding: 20px; 
                margin-top: 20px; 
                border-radius: 8px;
                white-space: pre-wrap;
                line-height: 1.6;
            }
            .success-badge {
                display: inline-block;
                background: #28a745;
                color: white;
                padding: 5px 15px;
                border-radius: 20px;
                font-size: 14px;
                margin-bottom: 15px;
            }
            h1 { color: #3a336e; text-align: center; margin-top: 0; }
            .timestamp { 
                text-align: center; 
                color: #666; 
                font-size: 14px; 
                margin-bottom: 20px;
                font-style: italic;
            }
        </style>
    </head>
    <body>
        <div class='message-card'>
            <div class='header'>
                <h1>📧 Message reçu - " . SITE_NAME . "</h1>
            </div>
            
            <div class='success-badge'>✅ Message enregistré en mode LOCAL</div>
            
            <div class='timestamp'>
                📅 " . date('d/m/Y à H:i:s') . "
            </div>
            
            <div class='info-row'>
                <span class='label'>👤 Nom :</span> 
                <span>" . htmlspecialchars($name) . "</span>
            </div>
            
            <div class='info-row'>
                <span class='label'>✉️ Email :</span> 
                <a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a>
            </div>
            
            <div class='info-row'>
                <span class='label'>📋 Sujet :</span> 
                <span>" . htmlspecialchars($subject) . "</span>
            </div>
            
            <div class='info-row'>
                <span class='label'>🎯 Destination :</span> 
                <span>" . RECEIVING_EMAIL . "</span>
            </div>
            
            <h3 style='color: #3a336e; margin-top: 30px;'>💬 Message :</h3>
            <div class='message-content'>" . nl2br(htmlspecialchars($message)) . "</div>
            
            <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #e0e0e0; color: #666;'>
                <p>ℹ️ <strong>Mode développement local</strong></p>
                <p>Ce message a été sauvegardé dans : <code>forms/messages_locaux.html</code></p>
                <p>En production, il sera envoyé par email à : <strong>" . RECEIVING_EMAIL . "</strong></p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Ajouter au fichier (ou créer s'il n'existe pas)
    $existingContent = file_exists($messagesFile) ? file_get_contents($messagesFile) : '';
    
    // Si c'est le premier message, créer la structure
    if (empty($existingContent)) {
        $content = "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Messages locaux - ANVDKO</title></head><body>" . $messageHTML;
    } else {
        // Ajouter le nouveau message avant </body>
        $content = str_replace('</body>', $messageHTML . '</body>', $existingContent);
    }
    
    // Sauvegarder
    if (file_put_contents($messagesFile, $content)) {
        sendResponse(true, 'Message envoyé avec succès ! (En mode local, consultez forms/messages_locaux.html)', [
            'mode' => 'LOCAL',
            'file' => 'forms/messages_locaux.html',
            'destinataire_production' => RECEIVING_EMAIL,
            'conseil' => 'Ouvrez forms/messages_locaux.html dans votre navigateur pour voir tous les messages'
        ]);
    } else {
        sendResponse(false, 'Erreur lors de la sauvegarde du message');
    }
}

// ======================================
// MODE PRODUCTION : Envoi réel par email
// ======================================
else {
    $to = RECEIVING_EMAIL;
    $email_subject = "[" . SITE_NAME . "] " . $subject;
    
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
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>📧 Nouveau message - " . SITE_NAME . "</h2>
            </div>
            <div class='content'>
                <div class='info-row'><span class='label'>👤 Nom :</span> " . htmlspecialchars($name) . "</div>
                <div class='info-row'><span class='label'>✉️ Email :</span> <a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a></div>
                <div class='info-row'><span class='label'>📋 Sujet :</span> " . htmlspecialchars($subject) . "</div>
                <div class='info-row'><span class='label'>📅 Date :</span> " . date('d/m/Y à H:i:s') . "</div>
                <h3 style='color: #3a336e; margin-top: 30px;'>💬 Message :</h3>
                <div class='message-box'>" . nl2br(htmlspecialchars($message)) . "</div>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = "From: " . SITE_NAME . " <noreply@anvdko.site>\r\n";
    $headers .= "Reply-To: " . $name . " <" . $email . ">\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    if (mail($to, $email_subject, $email_body, $headers)) {
        sendResponse(true, 'Votre message a été envoyé avec succès !');
    } else {
        sendResponse(false, 'Erreur lors de l\'envoi du message');
    }
}
?>