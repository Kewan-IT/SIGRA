<?php

class Process extends BaseModel
{
    protected string $table = 'processes';

    /**
     * Lista processos com filtros opcionais e paginação.
     * NOTA: usa placeholders com sufixos únicos (:distrito_f, :tipo_f, etc.)
     * para evitar o erro HY093 quando o mesmo filtro é usado em várias
     * cláusulas da mesma query (lição aprendida no KewanFarma).
     */
    public function listar(array $filtros = [], int $pagina = 1, int $porPagina = 20): array
    {
        $where = [];
        $params = [];

        if (!empty($filtros['numero_processo'])) {
            $where[] = 'p.numero_processo LIKE :numero_f';
            $params['numero_f'] = '%' . $filtros['numero_processo'] . '%';
        }
        if (!empty($filtros['requerente'])) {
            $where[] = 'p.requerente LIKE :requerente_f';
            $params['requerente_f'] = '%' . $filtros['requerente'] . '%';
        }
        if (!empty($filtros['district_id'])) {
            $where[] = 'p.district_id = :district_f';
            $params['district_f'] = $filtros['district_id'];
        }
        if (!empty($filtros['tipo_id'])) {
            $where[] = 'p.tipo_id = :tipo_f';
            $params['tipo_f'] = $filtros['tipo_id'];
        }
        if (!empty($filtros['department_atual_id'])) {
            $where[] = 'p.department_atual_id = :department_f';
            $params['department_f'] = $filtros['department_atual_id'];
        }
        if (!empty($filtros['funcionario_responsavel_id'])) {
            $where[] = 'p.funcionario_responsavel_id = :funcionario_f';
            $params['funcionario_f'] = $filtros['funcionario_responsavel_id'];
        }
        if (!empty($filtros['estado_atual'])) {
            $where[] = 'p.estado_atual = :estado_f';
            $params['estado_f'] = $filtros['estado_atual'];
        }
        if (!empty($filtros['ano'])) {
            $where[] = 'YEAR(p.data_entrada) = :ano_f';
            $params['ano_f'] = $filtros['ano'];
        }
        if (!empty($filtros['atrasados'])) {
            $where[] = 'p.prazo_data IS NOT NULL AND p.prazo_data < CURDATE() AND p.estado_atual <> :concluido_f';
            $params['concluido_f'] = 'concluido';
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $offset = max(0, ($pagina - 1) * $porPagina);

        $sql = "SELECT p.*, t.nome AS tipo_nome, di.nome AS distrito_nome,
                       de.nome AS departamento_nome, u.nome AS funcionario_nome
                FROM processes p
                JOIN process_types t ON t.id = p.tipo_id
                JOIN districts di ON di.id = p.district_id
                JOIN departments de ON de.id = p.department_atual_id
                LEFT JOIN users u ON u.id = p.funcionario_responsavel_id
                {$whereSql}
                ORDER BY p.criado_em DESC
                LIMIT :limit_f OFFSET :offset_f";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit_f', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset_f', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function contarComFiltros(array $filtros = []): int
    {
        $where = [];
        $params = [];

        if (!empty($filtros['numero_processo'])) {
            $where[] = 'p.numero_processo LIKE :numero_fc';
            $params['numero_fc'] = '%' . $filtros['numero_processo'] . '%';
        }
        if (!empty($filtros['requerente'])) {
            $where[] = 'p.requerente LIKE :requerente_fc';
            $params['requerente_fc'] = '%' . $filtros['requerente'] . '%';
        }
        if (!empty($filtros['district_id'])) {
            $where[] = 'p.district_id = :district_fc';
            $params['district_fc'] = $filtros['district_id'];
        }
        if (!empty($filtros['tipo_id'])) {
            $where[] = 'p.tipo_id = :tipo_fc';
            $params['tipo_fc'] = $filtros['tipo_id'];
        }
        if (!empty($filtros['department_atual_id'])) {
            $where[] = 'p.department_atual_id = :department_fc';
            $params['department_fc'] = $filtros['department_atual_id'];
        }
        if (!empty($filtros['funcionario_responsavel_id'])) {
            $where[] = 'p.funcionario_responsavel_id = :funcionario_fc';
            $params['funcionario_fc'] = $filtros['funcionario_responsavel_id'];
        }
        if (!empty($filtros['estado_atual'])) {
            $where[] = 'p.estado_atual = :estado_fc';
            $params['estado_fc'] = $filtros['estado_atual'];
        }
        if (!empty($filtros['ano'])) {
            $where[] = 'YEAR(p.data_entrada) = :ano_fc';
            $params['ano_fc'] = $filtros['ano'];
        }
        if (!empty($filtros['atrasados'])) {
            $where[] = 'p.prazo_data IS NOT NULL AND p.prazo_data < CURDATE() AND p.estado_atual <> :concluido_fc';
            $params['concluido_fc'] = 'concluido';
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT COUNT(*) AS total FROM processes p {$whereSql}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['total'];
    }

    public function findCompleto(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, t.nome AS tipo_nome, di.nome AS distrito_nome,
                    de.nome AS departamento_nome, de.chave AS departamento_chave,
                    u.nome AS funcionario_nome, c.nome AS criado_por_nome
             FROM processes p
             JOIN process_types t ON t.id = p.tipo_id
             JOIN districts di ON di.id = p.district_id
             JOIN departments de ON de.id = p.department_atual_id
             LEFT JOIN users u ON u.id = p.funcionario_responsavel_id
             LEFT JOIN users c ON c.id = p.criado_por
             WHERE p.id = :id LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /** Estatísticas para o dashboard */
    public function estatisticasDashboard(): array
    {
        $stats = [];

        $stats['recebidos_hoje'] = (int) $this->db->query(
            "SELECT COUNT(*) AS total FROM processes WHERE DATE(criado_em) = CURDATE()"
        )->fetch()['total'];

        $stats['em_andamento'] = (int) $this->db->query(
            "SELECT COUNT(*) AS total FROM processes WHERE estado_atual <> 'concluido'"
        )->fetch()['total'];

        $stats['concluidos'] = (int) $this->db->query(
            "SELECT COUNT(*) AS total FROM processes WHERE estado_atual = 'concluido'"
        )->fetch()['total'];

        $stats['atrasados'] = (int) $this->db->query(
            "SELECT COUNT(*) AS total FROM processes
             WHERE prazo_data IS NOT NULL AND prazo_data < CURDATE() AND estado_atual <> 'concluido'"
        )->fetch()['total'];

        $stats['por_departamento'] = $this->db->query(
            "SELECT d.nome, COUNT(p.id) AS total
             FROM departments d
             LEFT JOIN processes p ON p.department_atual_id = d.id AND p.estado_atual <> 'concluido'
             GROUP BY d.id, d.nome
             ORDER BY d.ordem"
        )->fetchAll();

        return $stats;
    }

    public function relatorioPorDistrito(): array
    {
        return $this->db->query(
            "SELECT di.nome, COUNT(p.id) AS total
             FROM districts di
             LEFT JOIN processes p ON p.district_id = di.id
             GROUP BY di.id, di.nome
             ORDER BY total DESC"
        )->fetchAll();
    }

    public function relatorioPorTipo(): array
    {
        return $this->db->query(
            "SELECT t.nome, COUNT(p.id) AS total
             FROM process_types t
             LEFT JOIN processes p ON p.tipo_id = t.id
             GROUP BY t.id, t.nome
             ORDER BY total DESC"
        )->fetchAll();
    }

    public function relatorioPorFuncionario(): array
    {
        return $this->db->query(
            "SELECT u.nome, COUNT(p.id) AS total
             FROM users u
             LEFT JOIN processes p ON p.funcionario_responsavel_id = u.id
             GROUP BY u.id, u.nome
             HAVING total > 0
             ORDER BY total DESC"
        )->fetchAll();
    }

    public function relatorioPorMes(int $ano): array
    {
        $stmt = $this->db->prepare(
            "SELECT MONTH(data_entrada) AS mes, COUNT(*) AS total
             FROM processes
             WHERE YEAR(data_entrada) = :ano
             GROUP BY MONTH(data_entrada)
             ORDER BY mes"
        );
        $stmt->execute(['ano' => $ano]);
        return $stmt->fetchAll();
    }

    public function tempoMedioTramitacaoDias(): float
    {
        $row = $this->db->query(
            "SELECT AVG(DATEDIFF(concluido_em, criado_em)) AS media
             FROM processes WHERE concluido_em IS NOT NULL"
        )->fetch();
        return $row['media'] !== null ? round((float) $row['media'], 1) : 0.0;
    }

    public function gerarNumeroProcesso(): string
    {
        $ano = date('Y');
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS total FROM processes WHERE YEAR(data_entrada) = :ano"
        );
        $stmt->execute(['ano' => $ano]);
        $seq = ((int) $stmt->fetch()['total']) + 1;
        return $seq . '/' . $ano;
    }

    public function gerarCodigoInterno(): string
    {
        return 'SIGRA-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }
}
