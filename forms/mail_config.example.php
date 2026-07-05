<?php
/**
 * Modèle de configuration e-mail — copiez vers mail_config.php sur le serveur.
 */
return [
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
        'password' => 'VOTRE_MOT_DE_PASSE_APPLICATION',
        'from_email' => 'kouebenoit@gmail.com',
        'from_name' => 'ANVDKO - Formulaire contact',
    ],
];
