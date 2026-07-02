<?php

class ProcessMovement extends BaseModel
{
    protected string $table = 'process_movements';

    public function historicoDoProcesso(int $processId): array
    {
        $stmt = $this->db->prepare(
            "SELECT pm.*,
                    dde.nome AS de_departamento_nome,
                    dpa.nome AS para_departamento_nome,
                    ude.nome AS de_usuario_nome,
                    upa.nome AS para_usuario_nome,
                    ua.nome AS executado_por_nome
             FROM process_movements pm
             LEFT JOIN departments dde ON dde.id = pm.de_department_id
             LEFT JOIN departments dpa ON dpa.id = pm.para_department_id
             LEFT JOIN users ude ON ude.id = pm.de_usuario_id
             LEFT JOIN users upa ON upa.id = pm.para_usuario_id
             JOIN users ua ON ua.id = pm.usuario_id
             WHERE pm.process_id = :process_id
             ORDER BY pm.criado_em ASC, pm.id ASC"
        );
        $stmt->execute(['process_id' => $processId]);
        return $stmt->fetchAll();
    }

    public function ultimosMovimentos(int $limite = 10): array
    {
        $stmt = $this->db->prepare(
            "SELECT pm.*, p.numero_processo, p.assunto, ua.nome AS executado_por_nome,
                    dpa.nome AS para_departamento_nome
             FROM process_movements pm
             JOIN processes p ON p.id = pm.process_id
             JOIN users ua ON ua.id = pm.usuario_id
             LEFT JOIN departments dpa ON dpa.id = pm.para_department_id
             ORDER BY pm.criado_em DESC, pm.id DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
