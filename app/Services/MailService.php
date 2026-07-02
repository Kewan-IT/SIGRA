<?php

/**
 * SIGRA - MailService
 * Envio de e-mail simples via SMTP (sockets nativos, sem dependências externas).
 * Devolve false quando SMTP não está configurado, permitindo fallback
 * para senha temporária exibida em tela.
 */
class MailService
{
    private ?string $host;
    private int $port;
    private ?string $username;
    private ?string $password;
    private string $from;
    private string $fromName;

    public function __construct()
    {
        $this->host = getenv('MAIL_HOST') ?: null;
        $this->port = (int) (getenv('MAIL_PORT') ?: 587);
        $this->username = getenv('MAIL_USERNAME') ?: null;
        $this->password = getenv('MAIL_PASSWORD') ?: null;
        $this->from = getenv('MAIL_FROM') ?: 'noreply@sigra.gov.mz';
        $this->fromName = getenv('MAIL_FROM_NAME') ?: 'SIGRA';
    }

    public function enviarRecuperacaoSenha(string $paraEmail, string $paraNome, string $link): bool
    {
        if (empty($this->host) || empty($this->username)) {
            return false; // SMTP não configurado -> controller usa fallback
        }

        $assunto = 'Recuperação de senha - SIGRA';
        $corpo = "Olá {$paraNome},\r\n\r\n"
            . "Recebemos um pedido de recuperação de senha para a sua conta no SIGRA.\r\n"
            . "Clique no link abaixo para definir uma nova senha (válido por 1 hora):\r\n\r\n"
            . "{$link}\r\n\r\n"
            . "Se não solicitou esta recuperação, ignore este e-mail.\r\n\r\n"
            . "Gabinete do Governador da Província da Zambézia";

        return $this->enviarSmtp($paraEmail, $assunto, $corpo);
    }

    private function enviarSmtp(string $paraEmail, string $assunto, string $corpo): bool
    {
        try {
            $timeout = 10;
            $socket = @fsockopen($this->host, $this->port, $errno, $errstr, $timeout);
            if (!$socket) {
                return false;
            }

            $this->leerResposta($socket);
            $this->comando($socket, "EHLO sigra.local\r\n");
            $this->comando($socket, "AUTH LOGIN\r\n");
            $this->comando($socket, base64_encode($this->username) . "\r\n");
            $this->comando($socket, base64_encode($this->password) . "\r\n");
            $this->comando($socket, "MAIL FROM:<{$this->from}>\r\n");
            $this->comando($socket, "RCPT TO:<{$paraEmail}>\r\n");
            $this->comando($socket, "DATA\r\n");

            $mensagem = "From: {$this->fromName} <{$this->from}>\r\n";
            $mensagem .= "To: <{$paraEmail}>\r\n";
            $mensagem .= "Subject: {$assunto}\r\n";
            $mensagem .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
            $mensagem .= $corpo . "\r\n.\r\n";

            $this->comando($socket, $mensagem);
            $this->comando($socket, "QUIT\r\n");
            fclose($socket);

            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    private function comando($socket, string $comando): string
    {
        fwrite($socket, $comando);
        return $this->leerResposta($socket);
    }

    private function leerResposta($socket): string
    {
        $resposta = '';
        while ($linha = fgets($socket, 515)) {
            $resposta .= $linha;
            if (substr($linha, 3, 1) === ' ') {
                break;
            }
        }
        return $resposta;
    }
}
