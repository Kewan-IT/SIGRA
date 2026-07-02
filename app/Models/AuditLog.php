<?php

class AuditLog extends BaseModel
{
    protected string $table = 'audit_logs';

    public function registar(?int $userId, string $acao, string $tabela, ?int $registroId, string $detalhes = ''): void
    {
        $this->insert([
            'usuario_id' => $userId,
            'acao' => $acao,
            'tabela_afetada' => $tabela,
            'registro_id' => $registroId,
            'detalhes' => $detalhes,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    }

    public function listar(int $pagina = 1, int $porPagina = 50): array
    {
        $offset = max(0, ($pagina - 1) * $porPagina);
        $stmt = $this->db->prepare(
            "SELECT al.*, u.nome AS usuario_nome
             FROM audit_logs al
             LEFT JOIN users u ON u.id = al.usuario_id
             ORDER BY al.criado_em DESC
             LIMIT :limit_a OFFSET :offset_a"
        );
        $stmt->bindValue(':limit_a', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset_a', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
