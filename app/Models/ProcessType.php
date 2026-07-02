<?php

class ProcessType extends BaseModel
{
    protected string $table = 'process_types';

    public function all(string $orderBy = 'nome'): array
    {
        return parent::all($orderBy);
    }

    public function ativos(): array
    {
        $stmt = $this->db->query("SELECT * FROM process_types WHERE ativo = 1 ORDER BY nome");
        return $stmt->fetchAll();
    }
}
