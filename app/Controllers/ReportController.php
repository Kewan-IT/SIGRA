<?php

class ReportController
{
    private Process $processModel;

    public function __construct()
    {
        $this->processModel = new Process();
    }

    public function index(): void
    {
        Auth::requireLogin();

        $ano = (int) ($_GET['ano'] ?? date('Y'));

        View::render('relatorios.index', [
            'ano' => $ano,
            'porDistrito' => $this->processModel->relatorioPorDistrito(),
            'porTipo' => $this->processModel->relatorioPorTipo(),
            'porFuncionario' => $this->processModel->relatorioPorFuncionario(),
            'porMes' => $this->processModel->relatorioPorMes($ano),
            'tempoMedio' => $this->processModel->tempoMedioTramitacaoDias(),
            'stats' => $this->processModel->estatisticasDashboard(),
        ]);
    }
}
