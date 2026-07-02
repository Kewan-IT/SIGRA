<?php

class Configuracao extends BaseModel
{
    protected string $table = 'configuracoes';

    public function get(string $chave, string $default = ''): string
    {
        $stmt = $this->db->prepare("SELECT valor FROM configuracoes WHERE chave = :chave LIMIT 1");
        $stmt->execute(['chave' => $chave]);
        $row = $stmt->fetch();
        return $row !== false ? ($row['valor'] ?? $default) : $default;
    }

    public function set(string $chave, string $valor): void
    {
        $stmt = $this->db->prepare("SELECT id FROM configuracoes WHERE chave = :chave LIMIT 1");
        $stmt->execute(['chave' => $chave]);
        if ($stmt->fetch()) {
            $upd = $this->db->prepare("UPDATE configuracoes SET valor = :valor WHERE chave = :chave");
            $upd->execute(['valor' => $valor, 'chave' => $chave]);
        } else {
            $this->insert(['chave' => $chave, 'valor' => $valor]);
        }
    }

    public function todas(): array
    {
        $stmt = $this->db->query("SELECT chave, valor FROM configuracoes");
        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['chave']] = $row['valor'];
        }
        return $result;
    }
}
