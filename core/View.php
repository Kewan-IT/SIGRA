<?php
/**
 * SIGRA - View
 * Renderiza views em app/Views usando notação de ponto (ex: 'processos.index').
 */

class View
{
    public static function render(string $view, array $data = [], string $layout = 'layouts.base'): void
    {
        extract($data, EXTR_SKIP);

        $viewPath = self::resolvePath($view);
        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo "View não encontrada: {$view}";
            return;
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        if ($layout === null || $layout === '') {
            echo $content;
            return;
        }

        $layoutPath = self::resolvePath($layout);
        if (!file_exists($layoutPath)) {
            echo $content;
            return;
        }

        require $layoutPath;
    }

    public static function renderPartial(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewPath = self::resolvePath($view);
        if (file_exists($viewPath)) {
            require $viewPath;
        }
    }

    private static function resolvePath(string $dotted): string
    {
        $relative = str_replace('.', '/', $dotted) . '.php';
        return __DIR__ . '/../app/Views/' . $relative;
    }

    /** Escapa texto para uso seguro em HTML */
    public static function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}
