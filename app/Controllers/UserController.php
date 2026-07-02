<?php

class UserController
{
    private User $model;
    private Role $roleModel;
    private Department $departmentModel;

    public function __construct()
    {
        $this->model = new User();
        $this->roleModel = new Role();
        $this->departmentModel = new Department();
    }

    public function index(): void
    {
        Auth::requireRole('admin');
        View::render('usuarios.index', ['usuarios' => $this->model->allWithRole()]);
    }

    public function createForm(): void
    {
        Auth::requireRole('admin');
        View::render('usuarios.create', [
            'roles' => $this->roleModel->all(),
            'departamentos' => $this->departmentModel->all(),
        ]);
    }

    public function store(): void
    {
        Auth::requireRole('admin');

        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $roleId = (int) ($_POST['role_id'] ?? 0);
        $departmentId = $_POST['department_id'] ?? null;
        $cargo = trim($_POST['cargo'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');

        if ($nome === '' || $email === '' || $roleId === 0) {
            Flash::erro('Preencha nome, e-mail e perfil.');
            header('Location: /utilizadores/novo');
            exit;
        }

        if ($this->model->findByEmail($email) !== null) {
            Flash::erro('Já existe um utilizador com este e-mail.');
            header('Location: /utilizadores/novo');
            exit;
        }

        $senhaTemp = $this->gerarSenhaTemporaria();
        $hash = password_hash($senhaTemp, PASSWORD_BCRYPT);

        $id = $this->model->insert([
            'nome' => $nome,
            'email' => $email,
            'senha' => $hash,
            'role_id' => $roleId,
            'department_id' => $departmentId ?: null,
            'cargo' => $cargo,
            'telefone' => $telefone,
            'trocar_senha_obrigatorio' => 1,
        ]);

        (new AuditLog())->registar(Auth::id(), 'criar', 'users', $id, "Utilizador {$email} criado");

        View::render('usuarios.senha_gerada', [
            'email' => $email,
            'senhaTemporaria' => $senhaTemp,
        ]);
    }

    public function editForm(string $id): void
    {
        Auth::requireRole('admin');
        $usuario = $this->model->findWithRole((int) $id);
        if ($usuario === null) {
            http_response_code(404);
            View::render('errors.404', []);
            return;
        }
        View::render('usuarios.edit', [
            'usuario' => $usuario,
            'roles' => $this->roleModel->all(),
            'departamentos' => $this->departmentModel->all(),
        ]);
    }

    public function update(string $id): void
    {
        Auth::requireRole('admin');
        $id = (int) $id;

        $nome = trim($_POST['nome'] ?? '');
        $roleId = (int) ($_POST['role_id'] ?? 0);
        $departmentId = $_POST['department_id'] ?? null;
        $cargo = trim($_POST['cargo'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $ativo = isset($_POST['ativo']) ? 1 : 0;

        $this->model->update($id, [
            'nome' => $nome,
            'role_id' => $roleId,
            'department_id' => $departmentId ?: null,
            'cargo' => $cargo,
            'telefone' => $telefone,
            'ativo' => $ativo,
        ]);

        (new AuditLog())->registar(Auth::id(), 'editar', 'users', $id, 'Dados do utilizador actualizados');

        Flash::sucesso('Utilizador actualizado.');
        header('Location: /utilizadores');
        exit;
    }

    public function resetarSenha(string $id): void
    {
        Auth::requireRole('admin');
        $id = (int) $id;
        $usuario = $this->model->find($id);
        if ($usuario === null) {
            Flash::erro('Utilizador não encontrado.');
            header('Location: /utilizadores');
            exit;
        }

        $senhaTemp = $this->gerarSenhaTemporaria();
        $hash = password_hash($senhaTemp, PASSWORD_BCRYPT);
        $this->model->atualizarSenha($id, $hash, true);

        (new AuditLog())->registar(Auth::id(), 'reset_senha', 'users', $id, 'Senha redefinida pelo administrador');

        View::render('usuarios.senha_gerada', [
            'email' => $usuario['email'],
            'senhaTemporaria' => $senhaTemp,
        ]);
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
