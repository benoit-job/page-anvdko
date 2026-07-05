<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

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

    if (!empty($smtp['enabled']) && !empty($smtp['password'])) {
        try {
            $mail = new PHPMailer(true);
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = $smtp['host'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $smtp['username'] ?? '';
            $mail->Password = preg_replace('/\s+/', '', (string) $smtp['password']);
            $mail->SMTPSecure = $smtp['secure'] ?? PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = (int) ($smtp['port'] ?? 587);
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                    'allow_self_signed' => false,
                ],
            ];
            $mail->Timeout = 30;

            $fromEmail = $smtp['from_email'] ?? $smtp['username'];
            $fromName = $smtp['from_name'] ?? $siteName;
            $mail->setFrom($fromEmail, $fromName);
            $mail->addReplyTo($email, $name);

            foreach ($recipients as $recipient) {
                $mail->addAddress($recipient);
            }

            $mail->isHTML(true);
            $mail->Subject = $emailSubject;
            $mail->Body = $emailHtml;
            $mail->AltBody = $emailText;
            $mail->send();

            return [
                'success' => true,
                'message' => 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.',
            ];
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            if (isset($mail) && !empty($mail->ErrorInfo)) {
                $errorMessage = $mail->ErrorInfo;
            }

            return [
                'success' => false,
                'message' => 'Erreur lors de l\'envoi du message. Veuillez réessayer plus tard.',
                'debug' => $errorMessage,
            ];
        }
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
        ];
    }

    if ($sentCount > 0) {
        return [
            'success' => true,
            'message' => 'Votre message a été envoyé à une partie des destinataires.',
        ];
    }

    $backupFile = dirname(__DIR__, 2) . '/forms/messages_backup.txt';
    $backup = "\n\n=== Message du " . date('Y-m-d H:i:s') . " ===\n";
    $backup .= "Destinataires : " . implode(', ', $recipients) . "\n";
    $backup .= "De : {$name} ({$email})\nSujet : {$subject}\nMessage :\n{$message}\n";
    @file_put_contents($backupFile, $backup, FILE_APPEND);

    return [
        'success' => false,
        'message' => 'Envoi impossible. Configurez SMTP dans forms/mail_config.local.php (mot de passe d\'application Gmail). Le message a été sauvegardé localement.',
    ];
}
