<?php

class NotificationController
{
    private Notification $model;

    public function __construct()
    {
        $this->model = new Notification();
    }

    public function index(): void
    {
        Auth::requireLogin();
        $notificacoes = $this->model->doUtilizador(Auth::id(), 50);
        View::render('notificacoes.index', ['notificacoes' => $notificacoes]);
    }

    public function marcarLida(string $id): void
    {
        Auth::requireLogin();
        $this->model->marcarComoLida((int) $id, Auth::id());
        header('Location: /notificacoes');
        exit;
    }

    public function marcarTodasLidas(): void
    {
        Auth::requireLogin();
        $this->model->marcarTodasComoLidas(Auth::id());
        header('Location: /notificacoes');
        exit;
    }
}
