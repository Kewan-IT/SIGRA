<?php

class ConfiguracaoController
{
    private Configuracao $model;

    public function __construct()
    {
        $this->model = new Configuracao();
    }

    public function index(): void
    {
        Auth::requireRole('admin');
        View::render('admin.configuracoes', ['config' => $this->model->todas()]);
    }

    public function update(): void
    {
        Auth::requireRole('admin');

        $this->model->set('nome_instituicao', trim($_POST['nome_instituicao'] ?? ''));
        $this->model->set('nome_sistema', trim($_POST['nome_sistema'] ?? ''));
        $this->model->set('sla_alerta_dias', (string) (int) ($_POST['sla_alerta_dias'] ?? 5));

        if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $destino = __DIR__ . '/../../public/assets/img/';
            if (!is_dir($destino)) {
                mkdir($destino, 0755, true);
            }
            $extensao = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            $nomeArquivo = 'logo.' . $extensao;
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $destino . $nomeArquivo)) {
                $this->model->set('logo', 'assets/img/' . $nomeArquivo);
            }
        }

        unset($_SESSION['_sigra_config']);

        Flash::sucesso('Configurações actualizadas.');
        header('Location: /configuracoes');
        exit;
    }
}
