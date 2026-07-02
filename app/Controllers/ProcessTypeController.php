<?php

class ProcessTypeController
{
    private ProcessType $model;

    public function __construct()
    {
        $this->model = new ProcessType();
    }

    public function index(): void
    {
        Auth::requireRole('admin');
        View::render('tipos.index', ['tipos' => $this->model->all()]);
    }

    public function store(): void
    {
        Auth::requireRole('admin');
        $nome = trim($_POST['nome'] ?? '');
        $prazo = (int) ($_POST['prazo_padrao_dias'] ?? 15);
        if ($nome !== '') {
            $this->model->insert(['nome' => $nome, 'prazo_padrao_dias' => $prazo]);
            Flash::sucesso('Tipo de processo adicionado.');
        } else {
            Flash::erro('Indique o nome do tipo de processo.');
        }
        header('Location: /tipos-processo');
        exit;
    }

    public function update(string $id): void
    {
        Auth::requireRole('admin');
        $id = (int) $id;
        $nome = trim($_POST['nome'] ?? '');
        $prazo = (int) ($_POST['prazo_padrao_dias'] ?? 15);
        $ativo = isset($_POST['ativo']) ? 1 : 0;
        if ($nome !== '') {
            $this->model->update($id, ['nome' => $nome, 'prazo_padrao_dias' => $prazo, 'ativo' => $ativo]);
            Flash::sucesso('Tipo de processo actualizado.');
        }
        header('Location: /tipos-processo');
        exit;
    }

    public function delete(string $id): void
    {
        Auth::requireRole('admin');
        $this->model->delete((int) $id);
        Flash::sucesso('Tipo de processo removido.');
        header('Location: /tipos-processo');
        exit;
    }
}
