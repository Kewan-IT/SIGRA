<?php
/**
 * SIGRA - Bootstrap
 * Carrega variáveis de ambiente, inicia sessão e regista o autoloader.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

date_default_timezone_set('Africa/Maputo');

// ---------------------------------------------------------------
// Carregar .env (parser simples, sem dependências externas)
// ---------------------------------------------------------------
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (!str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        // remove aspas envolventes, se existirem
        if (preg_match('/^"(.*)"$/', $value, $m) || preg_match("/^'(.*)'$/", $value, $m)) {
            $value = $m[1];
        }
        if (!array_key_exists($key, $_ENV)) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
        }
    }
}

// ---------------------------------------------------------------
// Sessão
// ---------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------------------------
// Autoload simples (core + app)
// ---------------------------------------------------------------
spl_autoload_register(function (string $class) {
    $paths = [
        __DIR__ . '/core/' . $class . '.php',
        __DIR__ . '/app/Controllers/' . $class . '.php',
        __DIR__ . '/app/Models/' . $class . '.php',
        __DIR__ . '/app/Services/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});
