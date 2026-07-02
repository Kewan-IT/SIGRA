<?php

class ProcessController
{
    /**
     * Sequência fixa de tramitação do Gabinete do Governador da Zambézia.
     * Cada etapa define: estado (chave interna), departamento (chave da tabela
     * departments), rótulo legível e a acção que o utilizador executa para
     * avançar para a etapa seguinte.
     */
    private const SEQUENCIA = [
        ['estado' => 'recebido',          'dept' => 'dfp',                     'rotulo' => 'Recebido no DFP'],
        ['estado' => 'distribuido_tecnico','dept' => 'tecnico',                'rotulo' => 'Distribuído ao Técnico'],
        ['estado' => 'enviado_chefe',      'dept' => 'chefe_departamento',      'rotulo' => 'Enviado ao Chefe do Departamento'],
        ['estado' => 'enviado_diretor',    'dept' => 'director_gabinete',       'rotulo' => 'Enviado ao Director do Gabinete'],
        ['estado' => 'retornado_dfp_1',    'dept' => 'dfp',                     'rotulo' => 'Retornado ao DFP'],
        ['estado' => 'enviado_governador', 'dept' => 'gabinete_governador',     'rotulo' => 'Enviado ao Gabinete do Governador'],
        ['estado' => 'homologado',         'dept' => 'gabinete_governador',     'rotulo' => 'Homologado pelo Governador'],
        ['estado' => 'retornado_dfp_2',     'dept' => 'dfp',                     'rotulo' => 'Retornado ao DFP'],
        ['estado' => 'enviado_tribunal',   'dept' => 'tribunal_administrativo', 'rotulo' => 'Enviado ao Tribunal Administrativo'],
        ['estado' => 'concluido',          'dept' => 'tribunal_administrativo', 'rotulo' => 'Processo Concluído'],
    ];

    private Process $processModel;
    private ProcessMovement $movementModel;
    private Department $departmentModel;
    private District $districtModel;
    private ProcessType $typeModel;
    private User $userModel;
    private Attachment $attachmentModel;
    private Notification $notificationModel;
    private AuditLog $auditModel;

    public function __construct()
    {
        $this->processModel = new Process();
        $this->movementModel = new ProcessMovement();
        $this->departmentModel = new Department();
        $this->districtModel = new District();
        $this->typeModel = new ProcessType();
        $this->userModel = new User();
        $this->attachmentModel = new Attachment();
        $this->notificationModel = new Notification();
        $this->auditModel = new AuditLog();
    }

    public function index(): void
    {
        Auth::requireLogin();

        $filtros = [
            'numero_processo' => $_GET['numero_processo'] ?? '',
            'requerente' => $_GET['requerente'] ?? '',
            'district_id' => $_GET['district_id'] ?? '',
            'tipo_id' => $_GET['tipo_id'] ?? '',
            'department_atual_id' => $_GET['department_atual_id'] ?? '',
            'estado_atual' => $_GET['estado_atual'] ?? '',
            'ano' => $_GET['ano'] ?? '',
            'atrasados' => $_GET['atrasados'] ?? '',
        ];

        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        $porPagina = (int) ($_GET['por_pagina'] ?? 20);
        if (!in_array($porPagina, [10, 20, 50], true)) {
            $porPagina = 20;
        }

        $processos = $this->processModel->listar($filtros, $pagina, $porPagina);
        $total = $this->processModel->contarComFiltros($filtros);
        $totalPaginas = (int) ceil($total / $porPagina);

        View::render('processos.index', [
            'processos' => $processos,
            'filtros' => $filtros,
            'pagina' => $pagina,
            'porPagina' => $porPagina,
            'totalPaginas' => max(1, $totalPaginas),
            'total' => $total,
            'distritos' => $this->districtModel->ativos(),
            'tipos' => $this->typeModel->ativos(),
            'departamentos' => $this->departmentModel->all(),
        ]);
    }

    public function createForm(): void
    {
        Auth::requireRole(['admin', 'recepcao_dfp']);

        View::render('processos.create', [
            'distritos' => $this->districtModel->ativos(),
            'tipos' => $this->typeModel->ativos(),
            'numeroSugerido' => $this->processModel->gerarNumeroProcesso(),
        ]);
    }

    public function store(): void
    {
        Auth::requireRole(['admin', 'recepcao_dfp']);

        $assunto = trim($_POST['assunto'] ?? '');
        $tipoId = (int) ($_POST['tipo_id'] ?? 0);
        $districtId = (int) ($_POST['district_id'] ?? 0);
        $requerente = trim($_POST['requerente'] ?? '');
        $dataEntrada = $_POST['data_entrada'] ?? date('Y-m-d');
        $observacoes = trim($_POST['observacoes'] ?? '');

        if ($assunto === '' || $tipoId === 0 || $districtId === 0 || $requerente === '') {
            Flash::erro('Preencha todos os campos obrigatórios.');
            header('Location: /processos/novo');
            exit;
        }

        $tipo = $this->typeModel->find($tipoId);
        $prazoDias = $tipo['prazo_padrao_dias'] ?? 15;
        $prazoData = date('Y-m-d', strtotime($dataEntrada . " +{$prazoDias} days"));

        $dfp = $this->departmentModel->findByChave('dfp');

        $numeroProcesso = trim($_POST['numero_processo'] ?? '') ?: $this->processModel->gerarNumeroProcesso();

        $id = $this->processModel->insert([
            'numero_processo' => $numeroProcesso,
            'codigo_interno' => $this->processModel->gerarCodigoInterno(),
            'assunto' => $assunto,
            'tipo_id' => $tipoId,
            'district_id' => $districtId,
            'requerente' => $requerente,
            'data_entrada' => $dataEntrada,
            'prazo_data' => $prazoData,
            'department_atual_id' => $dfp['id'],
            'estado_atual' => 'recebido',
            'observacoes' => $observacoes,
            'criado_por' => Auth::id(),
        ]);

        $this->movementModel->insert([
            'process_id' => $id,
            'de_department_id' => null,
            'para_department_id' => $dfp['id'],
            'de_usuario_id' => null,
            'para_usuario_id' => null,
            'estado_anterior' => null,
            'estado_novo' => 'recebido',
            'observacao' => 'Processo registado na recepção (DFP).',
            'usuario_id' => Auth::id(),
        ]);

        $this->handleUploads($id);

        $this->auditModel->registar(Auth::id(), 'criar', 'processes', $id, "Processo {$numeroProcesso} registado");

        Flash::sucesso("Processo {$numeroProcesso} registado com sucesso.");
        header('Location: /processos/' . $id);
        exit;
    }

    public function show(string $id): void
    {
        Auth::requireLogin();
        $id = (int) $id;

        $processo = $this->processModel->findCompleto($id);
        if ($processo === null) {
            http_response_code(404);
            View::render('errors.404', []);
            return;
        }

        $historico = $this->movementModel->historicoDoProcesso($id);
        $anexos = $this->attachmentModel->doProcesso($id);

        $proximaEtapa = $this->proximaEtapa($processo['estado_atual']);
        $tecnicos = $this->userModel->allWithRole();

        View::render('processos.show', [
            'processo' => $processo,
            'historico' => $historico,
            'anexos' => $anexos,
            'proximaEtapa' => $proximaEtapa,
            'tecnicos' => array_filter($tecnicos, fn($u) => $u['role_slug'] ?? null !== null),
            'sequencia' => self::SEQUENCIA,
        ]);
    }

    public function distribuirForm(string $id): void
    {
        Auth::requireRole(['admin', 'chefe_departamento']);
        $id = (int) $id;
        $processo = $this->processModel->findCompleto($id);
        if ($processo === null) {
            http_response_code(404);
            View::render('errors.404', []);
            return;
        }

        $tecnicos = array_filter($this->userModel->allWithRole(), function ($u) {
            return in_array($u['role_slug'] ?? '', ['tecnico'], true) && (int) $u['ativo'] === 1;
        });

        View::render('processos.distribuir', [
            'processo' => $processo,
            'tecnicos' => $tecnicos,
        ]);
    }

    public function distribuir(string $id): void
    {
        Auth::requireRole(['admin', 'chefe_departamento']);
        $id = (int) $id;
        $tecnicoId = (int) ($_POST['funcionario_responsavel_id'] ?? 0);
        $observacao = trim($_POST['observacao'] ?? '');

        $processo = $this->processModel->find($id);
        if ($processo === null || $tecnicoId === 0) {
            Flash::erro('Não foi possível distribuir o processo.');
            header('Location: /processos/' . $id);
            exit;
        }

        $tecnicoDept = $this->departmentModel->findByChave('tecnico');

        $this->processModel->update($id, [
            'funcionario_responsavel_id' => $tecnicoId,
            'department_atual_id' => $tecnicoDept['id'],
            'estado_atual' => 'distribuido_tecnico',
        ]);

        $this->movementModel->insert([
            'process_id' => $id,
            'de_department_id' => $processo['department_atual_id'],
            'para_department_id' => $tecnicoDept['id'],
            'de_usuario_id' => null,
            'para_usuario_id' => $tecnicoId,
            'estado_anterior' => $processo['estado_atual'],
            'estado_novo' => 'distribuido_tecnico',
            'observacao' => $observacao ?: 'Processo distribuído ao técnico.',
            'usuario_id' => Auth::id(),
        ]);

        $this->notificationModel->notificarUtilizador(
            $tecnicoId,
            $id,
            'novo_processo',
            "Foi-lhe atribuído o processo {$processo['numero_processo']}."
        );

        $this->auditModel->registar(Auth::id(), 'distribuir', 'processes', $id, "Distribuído ao utilizador #{$tecnicoId}");

        Flash::sucesso('Processo distribuído com sucesso.');
        header('Location: /processos/' . $id);
        exit;
    }

    /** Avança o processo para a etapa seguinte da sequência fixa de tramitação */
    public function encaminhar(string $id): void
    {
        Auth::requireLogin();
        $id = (int) $id;

        $processo = $this->processModel->find($id);
        if ($processo === null) {
            Flash::erro('Processo não encontrado.');
            header('Location: /processos');
            exit;
        }

        $proxima = $this->proximaEtapa($processo['estado_atual']);
        if ($proxima === null) {
            Flash::erro('Este processo já está concluído.');
            header('Location: /processos/' . $id);
            exit;
        }

        $observacao = trim($_POST['observacao'] ?? '');
        $departamentoDestino = $this->departmentModel->findByChave($proxima['dept']);

        $dadosUpdate = [
            'department_atual_id' => $departamentoDestino['id'],
            'estado_atual' => $proxima['estado'],
        ];

        if ($proxima['estado'] === 'concluido') {
            $dadosUpdate['concluido_em'] = date('Y-m-d H:i:s');
        }

        $this->processModel->update($id, $dadosUpdate);

        $this->movementModel->insert([
            'process_id' => $id,
            'de_department_id' => $processo['department_atual_id'],
            'para_department_id' => $departamentoDestino['id'],
            'de_usuario_id' => null,
            'para_usuario_id' => null,
            'estado_anterior' => $processo['estado_atual'],
            'estado_novo' => $proxima['estado'],
            'observacao' => $observacao ?: $proxima['rotulo'],
            'usuario_id' => Auth::id(),
        ]);

        $this->auditModel->registar(Auth::id(), 'encaminhar', 'processes', $id, $proxima['rotulo']);

        Flash::sucesso('Processo encaminhado: ' . $proxima['rotulo']);
        header('Location: /processos/' . $id);
        exit;
    }

    /** Devolve o processo para a etapa anterior (usado pelo Director/Governador) */
    public function devolver(string $id): void
    {
        Auth::requireRole(['admin', 'director_gabinete', 'gabinete_governador']);
        $id = (int) $id;

        $processo = $this->processModel->find($id);
        if ($processo === null) {
            Flash::erro('Processo não encontrado.');
            header('Location: /processos');
            exit;
        }

        $observacao = trim($_POST['observacao'] ?? 'Processo devolvido para revisão.');
        $dfp = $this->departmentModel->findByChave('dfp');

        $this->processModel->update($id, [
            'department_atual_id' => $dfp['id'],
            'estado_atual' => 'devolvido',
        ]);

        $this->movementModel->insert([
            'process_id' => $id,
            'de_department_id' => $processo['department_atual_id'],
            'para_department_id' => $dfp['id'],
            'de_usuario_id' => null,
            'para_usuario_id' => null,
            'estado_anterior' => $processo['estado_atual'],
            'estado_novo' => 'devolvido',
            'observacao' => $observacao,
            'usuario_id' => Auth::id(),
        ]);

        if (!empty($processo['funcionario_responsavel_id'])) {
            $this->notificationModel->notificarUtilizador(
                $processo['funcionario_responsavel_id'],
                $id,
                'devolvido',
                "O processo {$processo['numero_processo']} foi devolvido: {$observacao}"
            );
        }

        $this->auditModel->registar(Auth::id(), 'devolver', 'processes', $id, $observacao);

        Flash::sucesso('Processo devolvido ao DFP.');
        header('Location: /processos/' . $id);
        exit;
    }

    private function proximaEtapa(string $estadoAtual): ?array
    {
        foreach (self::SEQUENCIA as $index => $etapa) {
            if ($etapa['estado'] === $estadoAtual) {
                return self::SEQUENCIA[$index + 1] ?? null;
            }
        }
        // Estado "devolvido" ou desconhecido -> reinicia no DFP (distribuição)
        if ($estadoAtual === 'devolvido') {
            return self::SEQUENCIA[1];
        }
        return null;
    }

    private function handleUploads(int $processId): void
    {
        if (empty($_FILES['anexos']) || empty($_FILES['anexos']['name'][0])) {
            return;
        }

        $destino = __DIR__ . '/../../public/storage/attachments/';
        if (!is_dir($destino)) {
            mkdir($destino, 0755, true);
        }

        $total = count($_FILES['anexos']['name']);
        for ($i = 0; $i < $total; $i++) {
            if ($_FILES['anexos']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }
            $nomeOriginal = basename($_FILES['anexos']['name'][$i]);
            $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
            $nomeArquivo = 'proc' . $processId . '_' . bin2hex(random_bytes(6)) . '.' . $extensao;
            $caminhoDestino = $destino . $nomeArquivo;

            if (move_uploaded_file($_FILES['anexos']['tmp_name'][$i], $caminhoDestino)) {
                $this->attachmentModel->insert([
                    'process_id' => $processId,
                    'nome_original' => $nomeOriginal,
                    'nome_arquivo' => $nomeArquivo,
                    'caminho' => 'storage/attachments/' . $nomeArquivo,
                    'tipo' => mime_content_type($caminhoDestino) ?: null,
                    'tamanho' => filesize($caminhoDestino) ?: null,
                    'enviado_por' => Auth::id(),
                ]);
            }
        }
    }
}
