<?php

class District extends BaseModel
{
    protected string $table = 'districts';

    public function all(string $orderBy = 'nome'): array
    {
        return parent::all($orderBy);
    }

    public function ativos(): array
    {
        $stmt = $this->db->query("SELECT * FROM districts WHERE ativo = 1 ORDER BY nome");
        return $stmt->fetchAll();
    }
}
