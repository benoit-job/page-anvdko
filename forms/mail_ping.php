<?php
/**
 * Diagnostic rapide (sans envoi) — https://anvdko.site/forms/mail_ping.php?key=anvdko-mail-test
 */
header('Content-Type: application/json; charset=UTF-8');

if (($_GET['key'] ?? '') !== 'anvdko-mail-test') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'message' => 'Accès refusé']);
    exit;
}

$root = dirname(__DIR__);
$files = [
    'forms/contact.php' => $root . '/forms/contact.php',
    'forms/mail_config.php' => $root . '/forms/mail_config.php',
    'include/php/contact_mailer.php' => $root . '/include/php/contact_mailer.php',
    'include/php/anvdko_smtp_client.php' => $root . '/include/php/anvdko_smtp_client.php',
];

$status = [];
foreach ($files as $label => $path) {
    $status[$label] = is_readable($path);
}

$configOk = false;
$lwsUser = '';
if (is_readable($root . '/forms/mail_config.php')) {
    $config = require $root . '/forms/mail_config.php';
    $configOk = !empty($config['smtp']['lws']['password']);
    $lwsUser = $config['smtp']['lws']['username'] ?? '';
}

echo json_encode([
    'ok' => !in_array(false, $status, true) && $configOk,
    'host' => $_SERVER['HTTP_HOST'] ?? '',
    'php' => PHP_VERSION,
    'openssl' => extension_loaded('openssl'),
    'files' => $status,
    'lws_user' => $lwsUser,
    'lws_password_set' => $configOk,
    'hint' => 'Uploadez les fichiers manquants via FTP, puis testez mail_test.php',
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
