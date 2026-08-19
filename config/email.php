<?php
/**
 * ============================================================
 * CONFIGURAÇÃO DE EMAIL SMTP - CURICAS PET
 * ============================================================
 * 
 * Configurações para envio de email via SMTP da Hostinger
 * ============================================================
 */

// Configurações SMTP
define('SMTP_HOST', 'smtp.hostinger.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'contato@curicaspet.com.br');
define('SMTP_PASS', 'UmaNovaHistoria@2026');
define('SMTP_FROM_NAME', 'Curicas Pet');
define('SMTP_FROM_EMAIL', 'contato@curicaspet.com.br');

/**
 * Envia email usando SMTP
 * 
 * @param string $to Email do destinatário
 * @param string $subject Assunto
 * @param string $body Corpo do email
 * @param string|null $replyTo Email para resposta
 * @return bool True se enviou, false se falhou
 */
function sendSmtpEmail(string $to, string $subject, string $body, ?string $replyTo = null): bool
{
    try {
        // Conectar ao servidor SMTP
        $socket = @fsockopen('tls://' . SMTP_HOST, SMTP_PORT, $errno, $errstr, 30);

        if (!$socket) {
            // Tentar sem TLS primeiro e depois STARTTLS
            $socket = @fsockopen(SMTP_HOST, SMTP_PORT, $errno, $errstr, 30);
            if (!$socket) {
                error_log("SMTP: Falha ao conectar - $errstr ($errno)");
                return false;
            }

            // Ler resposta inicial
            $response = fgets($socket, 515);

            // EHLO
            fputs($socket, "EHLO curicaspet.com.br\r\n");
            $response = '';
            while ($line = fgets($socket, 515)) {
                $response .= $line;
                if (substr($line, 3, 1) == ' ')
                    break;
            }

            // STARTTLS
            fputs($socket, "STARTTLS\r\n");
            $response = fgets($socket, 515);

            // Upgrade para TLS
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

            // EHLO novamente após STARTTLS
            fputs($socket, "EHLO curicaspet.com.br\r\n");
            $response = '';
            while ($line = fgets($socket, 515)) {
                $response .= $line;
                if (substr($line, 3, 1) == ' ')
                    break;
            }
        } else {
            fgets($socket, 515);
            fputs($socket, "EHLO curicaspet.com.br\r\n");
            while ($line = fgets($socket, 515)) {
                if (substr($line, 3, 1) == ' ')
                    break;
            }
        }

        // AUTH LOGIN
        fputs($socket, "AUTH LOGIN\r\n");
        fgets($socket, 515);

        fputs($socket, base64_encode(SMTP_USER) . "\r\n");
        fgets($socket, 515);

        fputs($socket, base64_encode(SMTP_PASS) . "\r\n");
        $authResponse = fgets($socket, 515);

        if (substr($authResponse, 0, 3) != '235') {
            error_log("SMTP: Falha na autenticação - $authResponse");
            fclose($socket);
            return false;
        }

        // MAIL FROM
        fputs($socket, "MAIL FROM:<" . SMTP_FROM_EMAIL . ">\r\n");
        fgets($socket, 515);

        // RCPT TO
        fputs($socket, "RCPT TO:<$to>\r\n");
        fgets($socket, 515);

        // DATA
        fputs($socket, "DATA\r\n");
        fgets($socket, 515);

        // Headers e corpo
        $headers = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
        $headers .= "To: $to\r\n";
        $headers .= "Subject: $subject\r\n";
        if ($replyTo) {
            $headers .= "Reply-To: $replyTo\r\n";
        }
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "Date: " . date('r') . "\r\n";
        $headers .= "\r\n";

        fputs($socket, $headers . $body . "\r\n.\r\n");
        $dataResponse = fgets($socket, 515);

        // QUIT
        fputs($socket, "QUIT\r\n");
        fclose($socket);

        if (substr($dataResponse, 0, 3) == '250') {
            return true;
        }

        error_log("SMTP: Falha ao enviar - $dataResponse");
        return false;

    } catch (Exception $e) {
        error_log("SMTP Exception: " . $e->getMessage());
        return false;
    }
}
