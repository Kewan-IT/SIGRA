<?php
/**
 * SIGRA - Auth
 * Gestão de sessão, autenticação e permissões por perfil (role).
 */

class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function roleSlug(): ?string
    {
        return $_SESSION['user']['role_slug'] ?? null;
    }

    public static function departmentId(): ?int
    {
        $id = $_SESSION['user']['department_id'] ?? null;
        return $id !== null ? (int) $id : null;
    }

    public static function departmentChave(): ?string
    {
        return $_SESSION['user']['department_chave'] ?? null;
    }

    /** Utilizador pode registar novos processos (Secretaria, recepção ou admin) */
    public static function podeRegistarProcesso(): bool
    {
        return self::isAdmin()
            || self::hasRole('recepcao_dfp')
            || self::departmentChave() === 'secretaria';
    }

    public static function login(array $user): void
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = $user;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    /** Verifica se o utilizador tem um dos perfis indicados */
    public static function hasRole(array|string $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        return in_array(self::roleSlug(), $roles, true);
    }

    public static function isAdmin(): bool
    {
        return self::hasRole('admin');
    }

    /** Exige autenticação; redireciona para login se necessário */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: /login');
            exit;
        }

        if (!empty($_SESSION['trocar_senha_obrigatorio']) && !self::isChangePasswordRoute()) {
            header('Location: /trocar-senha');
            exit;
        }
    }

    /** Exige um ou mais perfis; devolve 403 se não autorizado */
    public static function requireRole(array|string $roles): void
    {
        self::requireLogin();
        if (!self::hasRole($roles) && !self::isAdmin()) {
            http_response_code(403);
            View::render('errors.403', []);
            exit;
        }
    }

    private static function isChangePasswordRoute(): bool
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        return in_array($uri, ['/trocar-senha', '/logout'], true);
    }
}
