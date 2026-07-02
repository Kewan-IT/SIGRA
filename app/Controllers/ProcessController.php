<?php

class ProcessController
{
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
        Auth::requireLogin();
        if (!Auth::podeRegistarProcesso()) {
            http_response_code(403);
            View::render('errors.403', []);
            return;
        }

        View::render('processos.create', [
            'distritos' => $this->districtModel->ativos(),
            'tipos' => $this->typeModel->ativos(),
            'numeroSugerido' => $this->processModel->gerarNumeroProcesso(),
        ]);
    }

    public function store(): void
    {
        Auth::requireLogin();
        if (!Auth::podeRegistarProcesso()) {
            http_response_code(403);
            View::render('errors.403', []);
            return;
        }

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

        $secretaria = $this->departmentModel->findByChave('secretaria');
        if ($secretaria === null) {
            Flash::erro('O sector "Secretaria" não está configurado. Contacte o administrador.');
            header('Location: /processos/novo');
            exit;
        }

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
            'department_atual_id' => $secretaria['id'],
            'estado_atual' => 'recebido',
            'observacoes' => $observacoes,
            'criado_por' => Auth::id(),
        ]);

        $this->movementModel->insert([
            'process_id' => $id,
            'de_department_id' => null,
            'para_department_id' => $secretaria['id'],
            'de_usuario_id' => null,
            'para_usuario_id' => null,
            'estado_anterior' => null,
            'estado_novo' => 'recebido',
            'observacao' => 'Processo registado na Secretaria.',
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

        View::render('processos.show', [
            'processo' => $processo,
            'historico' => $historico,
            'anexos' => $anexos,
            'podeAgir' => $this->podeAgir($processo),
            'sectorAnterior' => $this->sectorAnterior($historico, $processo),
        ]);
    }

    /** Formulário único de encaminhamento: escolher sector destino + utilizador destino */
    public function encaminharForm(string $id): void
    {
        Auth::requireLogin();
        $id = (int) $id;
        $processo = $this->processModel->findCompleto($id);
        if ($processo === null) {
            http_response_code(404);
            View::render('errors.404', []);
            return;
        }

        if (!$this->podeAgir($processo)) {
            http_response_code(403);
            View::render('errors.403', []);
            return;
        }

        $sectores = $this->departmentModel->ativos();
        $utilizadores = array_filter($this->userModel->allWithRole(), fn($u) => (int) $u['ativo'] === 1);

        View::render('processos.encaminhar', [
            'processo' => $processo,
            'sectores' => $sectores,
            'utilizadores' => $utilizadores,
        ]);
    }

    /** Encaminha o processo para o sector e utilizador escolhidos pelo técnico */
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

        if (!$this->podeAgir($processo)) {
            http_response_code(403);
            View::render('errors.403', []);
            return;
        }

        if ($processo['estado_atual'] === 'concluido') {
            Flash::erro('Este processo já está concluído.');
            header('Location: /processos/' . $id);
            exit;
        }

        $setorDestinoId = (int) ($_POST['department_destino_id'] ?? 0);
        $tecnicoDestinoId = (int) ($_POST['funcionario_destino_id'] ?? 0);
        $observacao = trim($_POST['observacao'] ?? '');

        $setorDestino = $setorDestinoId > 0 ? $this->departmentModel->find($setorDestinoId) : null;
        $tecnicoDestino = $tecnicoDestinoId > 0 ? $this->userModel->find($tecnicoDestinoId) : null;

        if ($setorDestino === null || (int) $setorDestino['ativo'] !== 1) {
            Flash::erro('Seleccione um sector de destino válido.');
            header('Location: /processos/' . $id . '/encaminhar');
            exit;
        }

        if ($tecnicoDestino === null || (int) $tecnicoDestino['ativo'] !== 1) {
            Flash::erro('Seleccione o técnico/utilizador de destino.');
            header('Location: /processos/' . $id . '/encaminhar');
            exit;
        }

        $this->processModel->update($id, [
            'funcionario_responsavel_id' => $tecnicoDestinoId,
            'department_atual_id' => $setorDestinoId,
            'estado_atual' => 'encaminhado',
        ]);

        $this->movementModel->insert([
            'process_id' => $id,
            'de_department_id' => $processo['department_atual_id'],
            'para_department_id' => $setorDestinoId,
            'de_usuario_id' => $processo['funcionario_responsavel_id'],
            'para_usuario_id' => $tecnicoDestinoId,
            'estado_anterior' => $processo['estado_atual'],
            'estado_novo' => 'encaminhado',
            'observacao' => $observacao ?: ('Encaminhado para ' . $setorDestino['nome'] . '.'),
            'usuario_id' => Auth::id(),
        ]);

        $this->notificationModel->notificarUtilizador(
            $tecnicoDestinoId,
            $id,
            'novo_processo',
            "Foi-lhe encaminhado o processo {$processo['numero_processo']} ({$setorDestino['nome']})."
        );

        $this->auditModel->registar(Auth::id(), 'encaminhar', 'processes', $id, "Encaminhado para {$setorDestino['nome']} / utilizador #{$tecnicoDestinoId}");

        Flash::sucesso('Processo encaminhado para ' . $setorDestino['nome'] . '.');
        header('Location: /processos/' . $id);
        exit;
    }

    /** Devolve o processo ao sector/utilizador do passo anterior do histórico */
    public function devolver(string $id): void
    {
        Auth::requireLogin();
        $id = (int) $id;

        $processo = $this->processModel->find($id);
        if ($processo === null) {
            Flash::erro('Processo não encontrado.');
            header('Location: /processos');
            exit;
        }

        if (!$this->podeAgir($processo)) {
            http_response_code(403);
            View::render('errors.403', []);
            return;
        }

        $historico = $this->movementModel->historicoDoProcesso($id);
        $anterior = $this->sectorAnterior($historico, $processo);

        if ($anterior === null) {
            Flash::erro('Não há um sector anterior para onde devolver este processo.');
            header('Location: /processos/' . $id);
            exit;
        }

        $observacao = trim($_POST['observacao'] ?? 'Processo devolvido para revisão.');

        $this->processModel->update($id, [
            'department_atual_id' => $anterior['department_id'],
            'funcionario_responsavel_id' => $anterior['usuario_id'],
            'estado_atual' => 'devolvido',
        ]);

        $this->movementModel->insert([
            'process_id' => $id,
            'de_department_id' => $processo['department_atual_id'],
            'para_department_id' => $anterior['department_id'],
            'de_usuario_id' => $processo['funcionario_responsavel_id'],
            'para_usuario_id' => $anterior['usuario_id'],
            'estado_anterior' => $processo['estado_atual'],
            'estado_novo' => 'devolvido',
            'observacao' => $observacao,
            'usuario_id' => Auth::id(),
        ]);

        if (!empty($anterior['usuario_id'])) {
            $this->notificationModel->notificarUtilizador(
                (int) $anterior['usuario_id'],
                $id,
                'devolvido',
                "O processo {$processo['numero_processo']} foi devolvido: {$observacao}"
            );
        }

        $this->auditModel->registar(Auth::id(), 'devolver', 'processes', $id, $observacao);

        Flash::sucesso('Processo devolvido ao sector anterior.');
        header('Location: /processos/' . $id);
        exit;
    }

    /** Marca o processo como concluído no sector/utilizador actual */
    public function concluir(string $id): void
    {
        Auth::requireLogin();
        $id = (int) $id;

        $processo = $this->processModel->find($id);
        if ($processo === null) {
            Flash::erro('Processo não encontrado.');
            header('Location: /processos');
            exit;
        }

        if (!$this->podeAgir($processo)) {
            http_response_code(403);
            View::render('errors.403', []);
            return;
        }

        if ($processo['estado_atual'] === 'concluido') {
            Flash::erro('Este processo já está concluído.');
            header('Location: /processos/' . $id);
            exit;
        }

        $observacao = trim($_POST['observacao'] ?? 'Processo concluído.');

        $this->processModel->update($id, [
            'estado_atual' => 'concluido',
            'concluido_em' => date('Y-m-d H:i:s'),
        ]);

        $this->movementModel->insert([
            'process_id' => $id,
            'de_department_id' => $processo['department_atual_id'],
            'para_department_id' => $processo['department_atual_id'],
            'de_usuario_id' => $processo['funcionario_responsavel_id'],
            'para_usuario_id' => $processo['funcionario_responsavel_id'],
            'estado_anterior' => $processo['estado_atual'],
            'estado_novo' => 'concluido',
            'observacao' => $observacao,
            'usuario_id' => Auth::id(),
        ]);

        $this->auditModel->registar(Auth::id(), 'concluir', 'processes', $id, $observacao);

        Flash::sucesso('Processo marcado como concluído.');
        header('Location: /processos/' . $id);
        exit;
    }

    /**
     * Regra de permissão para agir sobre um processo (encaminhar, devolver, concluir):
     * - administrador: sempre pode;
     * - utilizador a quem o processo está actualmente atribuído: pode;
     * - processo ainda sem responsável definido: qualquer utilizador do sector actual pode assumi-lo;
     * - chefe de departamento pertencente ao sector actual: pode sempre intervir.
     */
    private function podeAgir(array $processo): bool
    {
        if (Auth::isAdmin()) {
            return true;
        }

        $userId = Auth::id();
        $userDeptId = Auth::departmentId();
        $deptAtualId = (int) $processo['department_atual_id'];
        $responsavelId = $processo['funcionario_responsavel_id'] ?? null;

        if ($responsavelId !== null && (int) $responsavelId === (int) $userId) {
            return true;
        }

        if ($responsavelId === null && $userDeptId === $deptAtualId) {
            return true;
        }

        if (Auth::hasRole('chefe_departamento') && $userDeptId === $deptAtualId) {
            return true;
        }

        return false;
    }

    /** Devolve o sector/utilizador de onde o processo veio no último movimento, se existir */
    private function sectorAnterior(array $historico, array $processo): ?array
    {
        if (empty($historico)) {
            return null;
        }

        $ultimo = end($historico);
        if (empty($ultimo['de_department_id'])) {
            return null;
        }

        return [
            'department_id' => (int) $ultimo['de_department_id'],
            'department_nome' => $ultimo['de_departamento_nome'] ?? null,
            'usuario_id' => $ultimo['de_usuario_id'] ?? null,
            'usuario_nome' => $ultimo['de_usuario_nome'] ?? null,
        ];
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
