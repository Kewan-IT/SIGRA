<?php

class DepartmentController
{
    private Department $model;

    public function __construct()
    {
        $this->model = new Department();
    }

    public function index(): void
    {
        Auth::requireRole('admin');
        View::render('departamentos.index', ['departamentos' => $this->model->all()]);
    }

    public function store(): void
    {
        Auth::requireRole('admin');
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');

        if ($nome === '') {
            Flash::erro('Indique o nome do sector.');
            header('Location: /departamentos');
            exit;
        }

        $chave = $this->model->gerarChave($nome);

        $this->model->insert([
            'chave' => $chave,
            'nome' => $nome,
            'descricao' => $descricao ?: null,
            'ordem' => 50,
            'ativo' => 1,
        ]);

        (new AuditLog())->registar(Auth::id(), 'criar', 'departments', null, "Sector criado: {$nome}");

        Flash::sucesso('Sector adicionado.');
        header('Location: /departamentos');
        exit;
    }

    public function update(string $id): void
    {
        Auth::requireRole('admin');
        $id = (int) $id;
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $ativo = isset($_POST['ativo']) ? 1 : 0;
        if ($nome !== '') {
            $this->model->update($id, ['nome' => $nome, 'descricao' => $descricao, 'ativo' => $ativo]);
            Flash::sucesso('Sector actualizado.');
        }
        header('Location: /departamentos');
        exit;
    }

    public function delete(string $id): void
    {
        Auth::requireRole('admin');
        $id = (int) $id;

        $departamento = $this->model->find($id);
        if ($departamento === null) {
            Flash::erro('Sector não encontrado.');
            header('Location: /departamentos');
            exit;
        }

        if ($this->model->emUso($id)) {
            // Já tem processos, movimentos ou utilizadores associados: preserva o
            // histórico e apenas desactiva o sector (deixa de aparecer nos selects).
            $this->model->update($id, ['ativo' => 0]);
            Flash::sucesso('O sector tem histórico associado, por isso foi apenas desactivado (não pode ser eliminado em definitivo).');
        } else {
            $this->model->delete($id);
            Flash::sucesso('Sector eliminado.');
        }

        (new AuditLog())->registar(Auth::id(), 'eliminar', 'departments', $id, "Sector removido: {$departamento['nome']}");

        header('Location: /departamentos');
        exit;
    }
}
