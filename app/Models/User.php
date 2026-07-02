<?php

class User extends BaseModel
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT u.*, r.chave AS role_slug, r.nome AS role_nome
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.email = :email LIMIT 1"
        );
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function findWithRole(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT u.*, r.chave AS role_slug, r.nome AS role_nome, r.chave AS role_slug, d.nome AS department_nome
             FROM users u
             JOIN roles r ON r.id = u.role_id
             LEFT JOIN departments d ON d.id = u.department_id
             WHERE u.id = :id LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function allWithRole(): array
    {
        $stmt = $this->db->query(
            "SELECT u.*, r.nome AS role_nome, r.chave AS role_slug, d.nome AS department_nome
             FROM users u
             JOIN roles r ON r.id = u.role_id
             LEFT JOIN departments d ON d.id = u.department_id
             ORDER BY u.nome"
        );
        return $stmt->fetchAll();
    }

    public function setTokenRecuperacao(int $id, string $token, string $expiraEm): bool
    {
        return $this->update($id, [
            'token_recuperacao' => $token,
            'token_expira' => $expiraEm,
        ]);
    }

    public function findByToken(string $token): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE token_recuperacao = :token AND token_expira > NOW() LIMIT 1"
        );
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function atualizarSenha(int $id, string $hash, bool $trocarObrigatorio = false): bool
    {
        return $this->update($id, [
            'senha' => $hash,
            'trocar_senha_obrigatorio' => $trocarObrigatorio ? 1 : 0,
            'token_recuperacao' => null,
            'token_expira' => null,
        ]);
    }

    public function registarAcesso(int $id): void
    {
        $this->update($id, ['ultimo_acesso' => date('Y-m-d H:i:s')]);
    }
}
