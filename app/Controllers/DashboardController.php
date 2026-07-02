<?php

class DashboardController
{
    public function index(): void
    {
        Auth::requireLogin();

        $processModel = new Process();
        $movementModel = new ProcessMovement();

        $stats = $processModel->estatisticasDashboard();
        $ultimosMovimentos = $movementModel->ultimosMovimentos(10);

        View::render('dashboard.index', [
            'stats' => $stats,
            'ultimosMovimentos' => $ultimosMovimentos,
        ]);
    }
}
