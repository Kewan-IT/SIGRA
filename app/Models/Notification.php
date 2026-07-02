<?php

class Notification extends BaseModel
{
    protected string $table = 'notifications';

    public function doUtilizador(int $userId, int $limite = 20): array
    {
        $stmt = $this->db->prepare(
            "SELECT n.*, p.numero_processo
             FROM notifications n
             LEFT JOIN processes p ON p.id = n.process_id
             WHERE n.usuario_id = :usuario_id
             ORDER BY n.criado_em DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function naoLidasCount(int $userId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS total FROM notifications WHERE usuario_id = :usuario_id AND lida = 0"
        );
        $stmt->execute(['usuario_id' => $userId]);
        return (int) $stmt->fetch()['total'];
    }

    public function marcarComoLida(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE notifications SET lida = 1 WHERE id = :id AND usuario_id = :usuario_id"
        );
        return $stmt->execute(['id' => $id, 'usuario_id' => $userId]);
    }

    public function marcarTodasComoLidas(int $userId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE notifications SET lida = 1 WHERE usuario_id = :usuario_id"
        );
        return $stmt->execute(['usuario_id' => $userId]);
    }

    public function notificarUtilizador(int $userId, ?int $processId, string $tipo, string $mensagem): void
    {
        $this->insert([
            'usuario_id' => $userId,
            'process_id' => $processId,
            'tipo' => $tipo,
            'mensagem' => $mensagem,
        ]);
    }
}
