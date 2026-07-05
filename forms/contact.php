<?php
/**
 * Traitement du formulaire de contact ANVDKO.
 */

ini_set('display_errors', '0');
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../include/php/contact_mailer.php';

function contact_log(string $message): void
{
    $logFile = __DIR__ . '/contact_errors.log';
    file_put_contents($logFile, '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL, FILE_APPEND);
}

function contact_response(bool $success, string $message, array $extra = []): void
{
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message,
    ], $extra));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    contact_response(false, 'Méthode non autorisée.');
}

$configFile = __DIR__ . '/mail_config.php';
if (!is_readable($configFile)) {
    $configFile = __DIR__ . '/mail_config.example.php';
}
$config = require $configFile;

$name = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : '';
$email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$subject = isset($_POST['subject']) ? strip_tags(trim($_POST['subject'])) : '';
$message = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : '';

$errors = [];
if ($name === '') {
    $errors[] = 'Le nom est requis';
}
if ($email === '') {
    $errors[] = 'L\'email est requis';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'L\'email n\'est pas valide';
}
if ($subject === '') {
    $errors[] = 'Le sujet est requis';
}
if ($message === '') {
    $errors[] = 'Le message est requis';
} elseif (strlen($message) < 10) {
    $errors[] = 'Le message doit contenir au moins 10 caractères';
}

if (!empty($errors)) {
    contact_response(false, implode(', ', $errors));
}

if (!empty($_POST['website'])) {
    contact_response(true, 'Votre message a été envoyé avec succès.');
}

contact_log('Nouveau message de ' . $name . ' <' . $email . '> — ' . $subject);

$result = anvdko_send_contact_email($config, $name, $email, $subject, $message);

if ($result['success']) {
    $transport = $result['transport'] ?? 'smtp';
    contact_log('Envoi réussi via ' . $transport . ' vers : ' . implode(', ', $config['recipients']));
    contact_response(true, $result['message'], ['transport' => $transport]);
}

contact_log('Échec envoi : ' . ($result['debug'] ?? $result['message']));
$extra = [];
if (!empty($result['debug'])) {
    $extra['debug'] = $result['debug'];
}
contact_response(false, $result['message'], $extra);
