<?php

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function loginForm(): void
    {
        if (Auth::check()) {
            header('Location: /dashboard');
            exit;
        }
        View::render('auth.login', [], 'layouts.auth');
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($email === '' || $senha === '') {
            Flash::erro('Preencha o e-mail e a senha.');
            header('Location: /login');
            exit;
        }

        $user = $this->userModel->findByEmail($email);

        if ($user === null || !password_verify($senha, $user['senha'])) {
            Flash::erro('E-mail ou senha incorrectos.');
            header('Location: /login');
            exit;
        }

        if ((int) $user['ativo'] !== 1) {
            Flash::erro('A sua conta está inactiva. Contacte o administrador.');
            header('Location: /login');
            exit;
        }

        Auth::login([
            'id' => $user['id'],
            'nome' => $user['nome'],
            'email' => $user['email'],
            'role_id' => $user['role_id'],
            'role_slug' => $user['role_slug'],
            'role_nome' => $user['role_nome'],
            'department_id' => $user['department_id'],
            'department_chave' => $user['department_chave'] ?? null,
        ]);

        $_SESSION['trocar_senha_obrigatorio'] = (int) $user['trocar_senha_obrigatorio'] === 1;

        $this->userModel->registarAcesso($user['id']);

        (new AuditLog())->registar($user['id'], 'login', 'users', $user['id'], 'Login efectuado');

        if ($_SESSION['trocar_senha_obrigatorio']) {
            header('Location: /trocar-senha');
            exit;
        }

        header('Location: ' . $this->destinoPosLogin($user['role_slug']));
        exit;
    }

    private function destinoPosLogin(string $roleSlug): string
    {
        // Perfis restritos vão directamente para "nova venda"-equivalente: nova entrada de processo
        $restritos = ['recepcao_dfp', 'tecnico'];
        if (in_array($roleSlug, $restritos, true)) {
            return '/dashboard';
        }
        return '/dashboard';
    }

    public function logout(): void
    {
        if (Auth::check()) {
            (new AuditLog())->registar(Auth::id(), 'logout', 'users', Auth::id(), 'Logout efectuado');
        }
        Auth::logout();
        header('Location: /login');
        exit;
    }

    public function trocarSenhaForm(): void
    {
        Auth::requireLogin();
        View::render('auth.trocar_senha', []);
    }

    public function trocarSenha(): void
    {
        Auth::requireLogin();

        $novaSenha = $_POST['nova_senha'] ?? '';
        $confirmar = $_POST['confirmar_senha'] ?? '';

        if (strlen($novaSenha) < 6) {
            Flash::erro('A nova senha deve ter pelo menos 6 caracteres.');
            header('Location: /trocar-senha');
            exit;
        }

        if ($novaSenha !== $confirmar) {
            Flash::erro('As senhas não coincidem.');
            header('Location: /trocar-senha');
            exit;
        }

        $hash = password_hash($novaSenha, PASSWORD_BCRYPT);
        $this->userModel->atualizarSenha(Auth::id(), $hash, false);
        $_SESSION['trocar_senha_obrigatorio'] = false;

        Flash::sucesso('Senha alterada com sucesso.');
        header('Location: /dashboard');
        exit;
    }

    public function esqueciSenhaForm(): void
    {
        View::render('auth.esqueci_senha', [], 'layouts.auth');
    }

    public function esqueciSenha(): void
    {
        $email = trim($_POST['email'] ?? '');
        $user = $this->userModel->findByEmail($email);

        // Mensagem genérica por segurança (não revela se o e-mail existe)
        Flash::sucesso('Se o e-mail existir no sistema, receberá instruções de recuperação.');

        if ($user !== null) {
            $token = bin2hex(random_bytes(32));
            $expira = date('Y-m-d H:i:s', time() + 3600);
            $this->userModel->setTokenRecuperacao($user['id'], $token, $expira);

            $linkRecuperacao = rtrim(getenv('APP_URL') ?: '', '/') . '/redefinir-senha?token=' . $token;

            $enviado = false;
            try {
                $enviado = (new MailService())->enviarRecuperacaoSenha($user['email'], $user['nome'], $linkRecuperacao);
            } catch (Throwable $e) {
                $enviado = false;
            }

            if (!$enviado) {
                // Fallback: gera senha temporária alfanumérica e mostra na tela (ambiente sem SMTP)
                $senhaTemp = $this->gerarSenhaTemporaria();
                $hash = password_hash($senhaTemp, PASSWORD_BCRYPT);
                $this->userModel->atualizarSenha($user['id'], $hash, true);

                View::render('auth.senha_temporaria', ['senhaTemporaria' => $senhaTemp, 'email' => $user['email']], 'layouts.auth');
                return;
            }
        }

        header('Location: /login');
        exit;
    }

    public function redefinirSenhaForm(): void
    {
        $token = $_GET['token'] ?? '';
        $user = $this->userModel->findByToken($token);

        if ($user === null) {
            Flash::erro('O link de recuperação é inválido ou expirou.');
            header('Location: /login');
            exit;
        }

        View::render('auth.redefinir_senha', ['token' => $token], 'layouts.auth');
    }

    public function redefinirSenha(): void
    {
        $token = $_POST['token'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';
        $confirmar = $_POST['confirmar_senha'] ?? '';

        $user = $this->userModel->findByToken($token);
        if ($user === null) {
            Flash::erro('O link de recuperação é inválido ou expirou.');
            header('Location: /login');
            exit;
        }

        if (strlen($novaSenha) < 6 || $novaSenha !== $confirmar) {
            Flash::erro('As senhas devem coincidir e ter pelo menos 6 caracteres.');
            header('Location: /redefinir-senha?token=' . urlencode($token));
            exit;
        }

        $hash = password_hash($novaSenha, PASSWORD_BCRYPT);
        $this->userModel->atualizarSenha($user['id'], $hash, false);

        Flash::sucesso('Senha redefinida com sucesso. Pode agora entrar.');
        header('Location: /login');
        exit;
    }

    private function gerarSenhaTemporaria(int $tamanho = 10): string
    {
        $caracteres = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
        $senha = '';
        for ($i = 0; $i < $tamanho; $i++) {
            $senha .= $caracteres[random_int(0, strlen($caracteres) - 1)];
        }
        return $senha;
    }
}
