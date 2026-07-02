<?php
/**
 * SIGRA - Router
 * Router simples baseado em arrays, com suporte a {id} wildcards.
 * IMPORTANTE: rotas estáticas devem ser registadas antes das rotas com wildcard.
 */

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        if ($uri === '') {
            $uri = '/';
        }

        $routesForMethod = $this->routes[$method] ?? [];

        // 1) Tenta correspondência exacta primeiro (rotas estáticas)
        if (isset($routesForMethod[$uri])) {
            $this->invoke($routesForMethod[$uri], []);
            return;
        }

        // 2) Tenta rotas com wildcards {param}
        foreach ($routesForMethod as $routePath => $handler) {
            $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([^/]+)', $routePath);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $this->invoke($handler, $matches);
                return;
            }
        }

        http_response_code(404);
        View::render('errors.404', []);
    }

    private function invoke(callable|array $handler, array $params): void
    {
        if (is_array($handler)) {
            [$controllerClass, $methodName] = $handler;
            $controller = new $controllerClass();
            call_user_func_array([$controller, $methodName], $params);
            return;
        }

        call_user_func_array($handler, $params);
    }
}
