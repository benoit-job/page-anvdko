<?php

/**
 * Client SMTP minimal pour LWS (sans dépendance Composer).
 */
class AnvdkoSmtpClient
{
    private $socket;
    private $lastResponse = '';

    public function send(array $options): array
    {
        $host = $options['host'] ?? '';
        $port = (int) ($options['port'] ?? 465);
        $secure = $options['secure'] ?? 'ssl';
        $username = $options['username'] ?? '';
        $password = $options['password'] ?? '';
        $fromEmail = $options['from_email'] ?? $username;
        $fromName = $options['from_name'] ?? 'ANVDKO';
        $to = $options['to'] ?? [];
        $subject = $options['subject'] ?? '';
        $htmlBody = $options['html_body'] ?? '';
        $textBody = $options['text_body'] ?? strip_tags($htmlBody);
        $replyTo = $options['reply_to'] ?? $fromEmail;
        $replyName = $options['reply_name'] ?? '';

        if ($host === '' || $username === '' || $password === '' || empty($to)) {
            return ['success' => false, 'error' => 'Paramètres SMTP incomplets.'];
        }

        try {
            $this->connect($host, $port, $secure);
            $this->expect('220');
            $this->cmd('EHLO ' . $this->serverName(), [250]);
            if ($secure === 'tls') {
                $this->cmd('STARTTLS', [220]);
                if (!stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    throw new RuntimeException('Impossible d\'activer STARTTLS.');
                }
                $this->cmd('EHLO ' . $this->serverName(), [250]);
            }
            $this->cmd('AUTH LOGIN', [334]);
            $this->cmd(base64_encode($username), [334]);
            $this->cmd(base64_encode($password), [235]);
            $this->cmd('MAIL FROM:<' . $fromEmail . '>', [250]);
            foreach ($to as $recipient) {
                $this->cmd('RCPT TO:<' . $recipient . '>', [250, 251]);
            }
            $this->cmd('DATA', [354]);

            $encodedSubject = $this->encodeHeader($subject);
            $fromHeader = $this->formatAddress($fromEmail, $fromName);
            $replyHeader = $this->formatAddress($replyTo, $replyName !== '' ? $replyName : $replyTo);
            $boundary = 'bnd_' . md5((string) microtime(true));
            $messageId = '<' . uniqid('anvdko.', true) . '@anvdko.site>';

            $headers = [
                'Date: ' . date('r'),
                'Message-ID: ' . $messageId,
                'From: ' . $fromHeader,
                'Reply-To: ' . $replyHeader,
                'To: ' . implode(', ', $to),
                'Subject: ' . $encodedSubject,
                'MIME-Version: 1.0',
                'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
                'X-Mailer: ANVDKO-SMTP',
            ];

            $body = implode("\r\n", $headers) . "\r\n\r\n";
            $body .= '--' . $boundary . "\r\n";
            $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
            $body .= $this->normalize($textBody) . "\r\n\r\n";
            $body .= '--' . $boundary . "\r\n";
            $body .= "Content-Type: text/html; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
            $body .= $this->normalize($htmlBody) . "\r\n\r\n";
            $body .= '--' . $boundary . "--\r\n";
            $body = $this->dotStuff($body);

            $this->write($body . "\r\n.\r\n");
            $this->expect('250');
            $this->cmd('QUIT', [221]);
            $this->close();

            return ['success' => true];
        } catch (Throwable $e) {
            $this->close();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function connect(string $host, int $port, string $secure): void
    {
        $errno = 0;
        $errstr = '';
        $target = ($secure === 'ssl') ? 'ssl://' . $host : $host;
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ]);

        $this->socket = @stream_socket_client(
            $target . ':' . $port,
            $errno,
            $errstr,
            15,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$this->socket) {
            throw new RuntimeException("Connexion SMTP impossible ({$target}:{$port}) : {$errstr} ({$errno})");
        }

        stream_set_timeout($this->socket, 15);
    }

    private function serverName(): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'anvdko.site';
        return preg_replace('/:\d+$/', '', $host);
    }

    private function cmd(string $command, array $okCodes): void
    {
        $this->write($command . "\r\n");
        $this->expect(implode('/', $okCodes));
    }

    private function write(string $data): void
    {
        $written = fwrite($this->socket, $data);
        if ($written === false) {
            throw new RuntimeException('Écriture SMTP impossible.');
        }
    }

    private function expect(string $codes): void
    {
        $response = '';
        while (($line = fgets($this->socket, 515)) !== false) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        $this->lastResponse = trim($response);
        $accepted = explode('/', $codes);
        $code = substr($this->lastResponse, 0, 3);
        if (!in_array($code, $accepted, true)) {
            throw new RuntimeException('Réponse SMTP inattendue : ' . $this->lastResponse);
        }
    }

    private function encodeHeader(string $value): string
    {
        if (function_exists('mb_encode_mimeheader')) {
            return mb_encode_mimeheader($value, 'UTF-8', 'B', "\r\n");
        }
        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }

    private function formatAddress(string $email, string $name): string
    {
        $safeName = str_replace(['"', "\r", "\n"], '', $name);
        if ($safeName === '' || $safeName === $email) {
            return $email;
        }
        return '"' . $safeName . '" <' . $email . '>';
    }

    private function normalize(string $text): string
    {
        return preg_replace("/\r\n|\r|\n/", "\r\n", $text) ?? $text;
    }

    private function dotStuff(string $message): string
    {
        return preg_replace('/^\./m', '..', $message) ?? $message;
    }

    private function close(): void
    {
        if (is_resource($this->socket)) {
            fclose($this->socket);
        }
        $this->socket = null;
    }
}

function anvdko_send_via_native_smtp(array $profile, array $recipients, string $replyEmail, string $replyName, string $subject, string $htmlBody, string $textBody): array
{
    $client = new AnvdkoSmtpClient();
    $result = $client->send([
        'host' => $profile['host'],
        'port' => $profile['port'],
        'secure' => $profile['secure'],
        'username' => $profile['username'],
        'password' => $profile['password'],
        'from_email' => $profile['from_email'],
        'from_name' => $profile['from_name'],
        'to' => $recipients,
        'subject' => $subject,
        'html_body' => $htmlBody,
        'text_body' => $textBody,
        'reply_to' => $replyEmail,
        'reply_name' => $replyName,
    ]);

    if (!empty($result['success'])) {
        $result['profile'] = $profile['label'] ?? ($profile['host'] . ':' . $profile['port']);
    }

    return $result;
}

function anvdko_send_via_php_mail(array $recipients, string $fromEmail, string $fromName, string $replyEmail, string $replyName, string $subject, string $htmlBody): array
{
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= 'From: ' . $fromName . ' <' . $fromEmail . ">\r\n";
    $headers .= 'Reply-To: ' . $replyName . ' <' . $replyEmail . ">\r\n";
    $headers .= "X-Mailer: ANVDKO-PHP\r\n";

    $params = '-f' . $fromEmail;
    $sent = 0;
    foreach ($recipients as $recipient) {
        if (@mail($recipient, $subject, $htmlBody, $headers, $params)) {
            $sent++;
        }
    }

    if ($sent === count($recipients)) {
        return ['success' => true, 'profile' => 'mail() LWS'];
    }

    return ['success' => false, 'error' => 'mail() PHP a échoué'];
}
