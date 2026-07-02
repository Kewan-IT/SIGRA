<?php

class AttachmentController
{
    private Attachment $model;

    public function __construct()
    {
        $this->model = new Attachment();
    }

    public function download(string $id): void
    {
        Auth::requireLogin();
        $anexo = $this->model->find((int) $id);

        if ($anexo === null) {
            http_response_code(404);
            echo 'Anexo não encontrado.';
            return;
        }

        $caminho = __DIR__ . '/../../public/' . $anexo['caminho'];
        if (!file_exists($caminho)) {
            http_response_code(404);
            echo 'Ficheiro não encontrado no servidor.';
            return;
        }

        header('Content-Type: ' . ($anexo['tipo'] ?: 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . basename($anexo['nome_original']) . '"');
        header('Content-Length: ' . filesize($caminho));
        readfile($caminho);
        exit;
    }
}
