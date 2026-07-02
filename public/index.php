<?php
/**
 * SIGRA - Ponto de entrada da aplicação
 */

require __DIR__ . '/../bootstrap.php';

$router = new Router();
require __DIR__ . '/../routes/web.php';

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
