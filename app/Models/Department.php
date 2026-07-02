<?php

class Department extends BaseModel
{
    protected string $table = 'departments';

    public function all(string $orderBy = 'ordem, nome'): array
    {
        return parent::all($orderBy);
    }

    /** Sectores activos, ordenados por nome — usados nos selects de encaminhamento */
    public function ativos(): array
    {
        $stmt = $this->db->query("SELECT * FROM departments WHERE ativo = 1 ORDER BY nome");
        return $stmt->fetchAll();
    }

    public function findByChave(string $chave): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM departments WHERE chave = :chave LIMIT 1");
        $stmt->execute(['chave' => $chave]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /** Gera uma chave única (slug) a partir do nome do sector */
    public function gerarChave(string $nome): string
    {
        $base = strtolower(trim($nome));
        $base = iconv('UTF-8', 'ASCII//TRANSLIT', $base) ?: $base;
        $base = preg_replace('/[^a-z0-9]+/', '_', $base);
        $base = trim($base, '_');
        $base = $base !== '' ? $base : 'sector';

        $chave = substr($base, 0, 40);
        $sufixo = 1;
        while ($this->findByChave($chave) !== null) {
            $sufixo++;
            $chave = substr($base, 0, 40 - strlen('_' . $sufixo)) . '_' . $sufixo;
        }
        return $chave;
    }

    /** Verifica se o sector já tem histórico associado (processos, movimentos ou utilizadores) */
    public function emUso(int $id): bool
    {
        $tabelas = [
            ['tabela' => 'processes', 'coluna' => 'department_atual_id'],
            ['tabela' => 'process_movements', 'coluna' => 'de_department_id'],
            ['tabela' => 'process_movements', 'coluna' => 'para_department_id'],
            ['tabela' => 'users', 'coluna' => 'department_id'],
        ];

        foreach ($tabelas as $t) {
            $stmt = $this->db->prepare("SELECT 1 FROM {$t['tabela']} WHERE {$t['coluna']} = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
            if ($stmt->fetch() !== false) {
                return true;
            }
        }
        return false;
    }
}
