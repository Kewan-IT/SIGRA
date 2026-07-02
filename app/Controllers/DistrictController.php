<?php

class DistrictController
{
    private District $model;

    public function __construct()
    {
        $this->model = new District();
    }

    public function index(): void
    {
        Auth::requireRole('admin');
        View::render('distritos.index', ['distritos' => $this->model->all()]);
    }

    public function store(): void
    {
        Auth::requireRole('admin');
        $nome = trim($_POST['nome'] ?? '');
        if ($nome !== '') {
            $this->model->insert(['nome' => $nome]);
            (new AuditLog())->registar(Auth::id(), 'criar', 'districts', null, $nome);
            Flash::sucesso('Distrito adicionado.');
        } else {
            Flash::erro('Indique o nome do distrito.');
        }
        header('Location: /distritos');
        exit;
    }

    public function update(string $id): void
    {
        Auth::requireRole('admin');
        $id = (int) $id;
        $nome = trim($_POST['nome'] ?? '');
        $ativo = isset($_POST['ativo']) ? 1 : 0;
        if ($nome !== '') {
            $this->model->update($id, ['nome' => $nome, 'ativo' => $ativo]);
            Flash::sucesso('Distrito actualizado.');
        }
        header('Location: /distritos');
        exit;
    }

    public function delete(string $id): void
    {
        Auth::requireRole('admin');
        $this->model->delete((int) $id);
        Flash::sucesso('Distrito removido.');
        header('Location: /distritos');
        exit;
    }
}
