<?php
/**
 * Router para o servidor de desenvolvimento embutido do PHP.
 * Uso: php -S 0.0.0.0:8000 -t public public/router.php
 *
 * Serve ficheiros estáticos existentes directamente (CSS, JS, imagens,
 * anexos) e encaminha todo o resto para o index.php da aplicação.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$caminhoFisico = __DIR__ . $uri;

if ($uri !== '/' && file_exists($caminhoFisico) && !is_dir($caminhoFisico)) {
    return false; // deixa o servidor embutido servir o ficheiro estático
}

require __DIR__ . '/index.php';
