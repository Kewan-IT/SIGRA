<?php

class Attachment extends BaseModel
{
    protected string $table = 'attachments';

    public function doProcesso(int $processId): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.*, u.nome AS enviado_por_nome
             FROM attachments a
             LEFT JOIN users u ON u.id = a.enviado_por
             WHERE a.process_id = :process_id
             ORDER BY a.criado_em DESC"
        );
        $stmt->execute(['process_id' => $processId]);
        return $stmt->fetchAll();
    }
}
