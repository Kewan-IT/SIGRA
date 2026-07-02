<?php

class AuditController
{
    private AuditLog $model;

    public function __construct()
    {
        $this->model = new AuditLog();
    }

    public function index(): void
    {
        Auth::requireRole('admin');
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        View::render('admin.auditoria', [
            'logs' => $this->model->listar($pagina, 50),
            'pagina' => $pagina,
        ]);
    }
}
