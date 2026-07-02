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

    public function update(string $id): void
    {
        Auth::requireRole('admin');
        $id = (int) $id;
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $ativo = isset($_POST['ativo']) ? 1 : 0;
        if ($nome !== '') {
            $this->model->update($id, ['nome' => $nome, 'descricao' => $descricao, 'ativo' => $ativo]);
            Flash::sucesso('Departamento actualizado.');
        }
        header('Location: /departamentos');
        exit;
    }
}
