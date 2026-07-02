<?php
/**
 * SIGRA - Flash
 * Mensagens flash de sucesso/erro guardadas em sessão para um único pedido.
 */

class Flash
{
    public static function sucesso(string $mensagem): void
    {
        $_SESSION['flash_sucesso'] = $mensagem;
    }

    public static function erro(string $mensagem): void
    {
        $_SESSION['flash_erro'] = $mensagem;
    }

    public static function getSucesso(): ?string
    {
        $msg = $_SESSION['flash_sucesso'] ?? null;
        unset($_SESSION['flash_sucesso']);
        return $msg;
    }

    public static function getErro(): ?string
    {
        $msg = $_SESSION['flash_erro'] ?? null;
        unset($_SESSION['flash_erro']);
        return $msg;
    }
}
