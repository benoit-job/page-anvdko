<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function anvdko_load_phpmailer_autoload(): bool
{
    static $loaded = null;
    if ($loaded === true) {
        return true;
    }

    $candidates = [
        dirname(__DIR__, 2) . '/vendor/autoload.php',
        dirname(__DIR__, 1) . '/../vendor/autoload.php',
        __DIR__ . '/../../vendor/autoload.php',
    ];

    foreach ($candidates as $autoload) {
        if (is_readable($autoload)) {
            require_once $autoload;
            $loaded = true;
            return true;
        }
    }

    $loaded = false;
    return false;
}

function anvdko_is_production_host(): bool
{
    $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
    return $host !== ''
        && strpos($host, 'localhost') === false
        && strpos($host, '127.0.0.1') === false;
}

function anvdko_build_contact_email_html(string $siteName, string $name, string $email, string $subject, string $message): string
{
    $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $safeEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $safeSubject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
    $safeMessage = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
    $date = date('d/m/Y à H:i:s');

    return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"></head>
<body style="font-family:Arial,sans-serif;line-height:1.6;color:#333;">
  <div style="max-width:600px;margin:0 auto;padding:20px;background:#f9f9f9;border-radius:10px;">
    <div style="background:linear-gradient(135deg,#3a336e,#8d4eb5);color:#fff;padding:20px;text-align:center;border-radius:10px 10px 0 0;">
      <h2 style="margin:0;">Nouveau message - {$siteName}</h2>
    </div>
    <div style="background:#fff;padding:30px;border-radius:0 0 10px 10px;">
      <p><strong>Nom :</strong> {$safeName}</p>
      <p><strong>Email :</strong> <a href="mailto:{$safeEmail}">{$safeEmail}</a></p>
      <p><strong>Sujet :</strong> {$safeSubject}</p>
      <p><strong>Date :</strong> {$date}</p>
      <h3 style="color:#3a336e;">Message</h3>
      <div style="background:#fff;border:1px solid #ddd;padding:20px;border-radius:5px;">{$safeMessage}</div>
      <p style="margin-top:20px;color:#666;font-size:12px;">Répondre à : {$safeEmail}</p>
    </div>
  </div>
</body>
</html>
HTML;
}

function anvdko_build_contact_email_text(string $siteName, string $name, string $email, string $subject, string $message): string
{
    return "Nouveau message depuis {$siteName}\n"
        . "================================\n\n"
        . "Nom : {$name}\n"
        . "Email : {$email}\n"
        . "Sujet : {$subject}\n"
        . "Date : " . date('d/m/Y à H:i:s') . "\n\n"
        . "Message :\n{$message}\n\n"
        . "---\nRépondre à : {$email}\n";
}

function anvdko_get_smtp_profiles(array $smtp): array
{
    if (!empty($smtp['profiles']) && is_array($smtp['profiles'])) {
        return $smtp['profiles'];
    }

    $username = $smtp['username'] ?? '';
    $password = preg_replace('/\s+/', '', (string) ($smtp['password'] ?? ''));
    $fromEmail = $smtp['from_email'] ?? $username;
    $fromName = $smtp['from_name'] ?? 'ANVDKO';

    $profiles = [
        [
            'label' => 'Gmail TLS 587',
            'host' => $smtp['host'] ?? 'smtp.gmail.com',
            'port' => 587,
            'secure' => 'tls',
            'username' => $username,
            'password' => $password,
            'from_email' => $fromEmail,
            'from_name' => $fromName,
        ],
        [
            'label' => 'Gmail SSL 465',
            'host' => $smtp['host'] ?? 'smtp.gmail.com',
            'port' => 465,
            'secure' => 'ssl',
            'username' => $username,
            'password' => $password,
            'from_email' => $fromEmail,
            'from_name' => $fromName,
        ],
    ];

    if (!empty($smtp['lws']['enabled'])) {
        $profiles[] = [
            'label' => 'LWS SMTP',
            'host' => $smtp['lws']['host'] ?? 'mail.anvdko.site',
            'port' => (int) ($smtp['lws']['port'] ?? 587),
            'secure' => $smtp['lws']['secure'] ?? 'tls',
            'username' => $smtp['lws']['username'] ?? '',
            'password' => preg_replace('/\s+/', '', (string) ($smtp['lws']['password'] ?? '')),
            'from_email' => $smtp['lws']['from_email'] ?? ($smtp['lws']['username'] ?? ''),
            'from_name' => $fromName,
        ];
    }

    return $profiles;
}

function anvdko_try_smtp_send(array $profile, array $recipients, string $replyEmail, string $replyName, string $subject, string $htmlBody, string $textBody): array
{
    if (!anvdko_load_phpmailer_autoload()) {
        return ['success' => false, 'error' => 'PHPMailer introuvable (dossier vendor/ manquant sur le serveur).'];
    }

    if (empty($profile['password']) || empty($profile['username'])) {
        return ['success' => false, 'error' => 'Identifiants SMTP manquants.'];
    }

    $mail = new PHPMailer(true);

    try {
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host = $profile['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $profile['username'];
        $mail->Password = $profile['password'];
        $mail->SMTPSecure = $profile['secure'];
        $mail->Port = (int) $profile['port'];
        $mail->Timeout = 25;
        $mail->SMTPKeepAlive = false;

        if (anvdko_is_production_host()) {
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];
        }

        $mail->setFrom($profile['from_email'], $profile['from_name']);
        $mail->addReplyTo($replyEmail, $replyName);

        foreach ($recipients as $recipient) {
            $mail->addAddress($recipient);
        }

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = $textBody;
        $mail->send();

        return [
            'success' => true,
            'profile' => $profile['label'] ?? ($profile['host'] . ':' . $profile['port']),
        ];
    } catch (Exception $e) {
        $error = !empty($mail->ErrorInfo) ? $mail->ErrorInfo : $e->getMessage();
        return [
            'success' => false,
            'error' => $error,
            'profile' => $profile['label'] ?? ($profile['host'] . ':' . $profile['port']),
        ];
    }
}

function anvdko_send_contact_email(array $config, string $name, string $email, string $subject, string $message): array
{
    $siteName = $config['site_name'] ?? 'ANVDKO';
    $recipients = array_values(array_filter($config['recipients'] ?? [], static function ($addr) {
        return is_string($addr) && filter_var($addr, FILTER_VALIDATE_EMAIL);
    }));

    if (empty($recipients)) {
        return ['success' => false, 'message' => 'Aucun destinataire configuré.'];
    }

    $emailSubject = '[' . $siteName . '] ' . $subject;
    $emailHtml = anvdko_build_contact_email_html($siteName, $name, $email, $subject, $message);
    $emailText = anvdko_build_contact_email_text($siteName, $name, $email, $subject, $message);
    $smtp = $config['smtp'] ?? [];
    $errors = [];

    if (!empty($smtp['enabled']) && !empty($smtp['password'])) {
        foreach (anvdko_get_smtp_profiles($smtp) as $profile) {
            $attempt = anvdko_try_smtp_send($profile, $recipients, $email, $name, $emailSubject, $emailHtml, $emailText);
            if (!empty($attempt['success'])) {
                return [
                    'success' => true,
                    'message' => 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.',
                    'transport' => $attempt['profile'] ?? 'smtp',
                ];
            }
            $errors[] = ($attempt['profile'] ?? 'SMTP') . ' : ' . ($attempt['error'] ?? 'échec');
        }
    }

    if (!anvdko_load_phpmailer_autoload()) {
        $errors[] = 'vendor/autoload.php introuvable';
    }

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= 'From: ' . $siteName . ' <' . ($smtp['from_email'] ?? 'noreply@anvdko.site') . ">\r\n";
    $headers .= 'Reply-To: ' . $name . ' <' . $email . ">\r\n";

    $sentCount = 0;
    foreach ($recipients as $recipient) {
        if (@mail($recipient, $emailSubject, $emailHtml, $headers)) {
            $sentCount++;
        }
    }

    if ($sentCount === count($recipients)) {
        return [
            'success' => true,
            'message' => 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.',
            'transport' => 'mail()',
        ];
    }

    $backupFile = dirname(__DIR__, 2) . '/forms/messages_backup.txt';
    $backup = "\n\n=== Message du " . date('Y-m-d H:i:s') . " ===\n";
    $backup .= "Destinataires : " . implode(', ', $recipients) . "\n";
    $backup .= "De : {$name} ({$email})\nSujet : {$subject}\nMessage :\n{$message}\n";
    $backup .= "Erreurs SMTP :\n" . implode("\n", $errors) . "\n";
    @file_put_contents($backupFile, $backup, FILE_APPEND);

    return [
        'success' => false,
        'message' => 'Erreur lors de l\'envoi du message. Veuillez réessayer plus tard.',
        'debug' => implode(' | ', $errors),
    ];
}
