<?php
/**
 * Configuration e-mail du formulaire de contact ANVDKO.
 * Ce fichier doit être présent sur le serveur en ligne (anvdko.site).
 */

$mailConfig = [
    'site_name' => 'ANVDKO',
    'recipients' => [
        'kouebenoit@gmail.com',
        'belandekouassi@gmail.com',
        'nguessan.koue@uvci.edu.ci',
    ],
    'smtp' => [
        'enabled' => true,
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'secure' => 'tls',
        'username' => 'kouebenoit@gmail.com',
        'password' => 'iuapxyrtjoakqzef',
        'from_email' => 'kouebenoit@gmail.com',
        'from_name' => 'ANVDKO - Formulaire contact',
        // Optionnel : SMTP LWS si Gmail est bloqué par l'hébergeur
        'lws' => [
            'enabled' => false,
            'host' => 'mail.anvdko.site',
            'port' => 587,
            'secure' => 'tls',
            'username' => 'contact@anvdko.site',
            'password' => '',
            'from_email' => 'contact@anvdko.site',
        ],
    ],
];

$secretFiles = [
    __DIR__ . '/mail_config.local.php',
    __DIR__ . '/mail_secrets.php',
    dirname(__DIR__) . '/include/php/mail_secrets.php',
];

foreach ($secretFiles as $secretFile) {
    if (!is_readable($secretFile)) {
        continue;
    }
    $overrides = require $secretFile;
    if (!is_array($overrides)) {
        continue;
    }
    if (!empty($overrides['smtp']['password'])) {
        $mailConfig['smtp']['password'] = $overrides['smtp']['password'];
    }
    if (!empty($overrides['smtp']['username'])) {
        $mailConfig['smtp']['username'] = $overrides['smtp']['username'];
        $mailConfig['smtp']['from_email'] = $overrides['smtp']['username'];
    }
    if (!empty($overrides['smtp']['lws']) && is_array($overrides['smtp']['lws'])) {
        $mailConfig['smtp']['lws'] = array_merge($mailConfig['smtp']['lws'], $overrides['smtp']['lws']);
    }
}

return $mailConfig;
