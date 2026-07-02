<?php
/**
 * SIGRA - BaseModel
 * Classe base para todos os modelos, com helpers PDO comuns.
 */

abstract class BaseModel
{
    protected PDO $db;
    protected string $table = '';
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function all(string $orderBy = ''): array
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy !== '') {
            $sql .= " ORDER BY {$orderBy}";
        }
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function insert(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($c) => ':' . $c, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sets = [];
        foreach (array_keys($data) as $column) {
            $sets[] = "{$column} = :{$column}";
        }
        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s = :__id',
            $this->table,
            implode(', ', $sets),
            $this->primaryKey
        );

        $data['__id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function count(string $where = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table}";
        if ($where !== '') {
            $sql .= " WHERE {$where}";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['total'];
    }

    /**
     * Garante que uma coluna existe na tabela (auto-migração leve),
     * usando SHOW COLUMNS para não quebrar instalações já existentes.
     */
    public static function ensureColumn(PDO $db, string $table, string $column, string $definitionSql): void
    {
        $stmt = $db->prepare("SHOW COLUMNS FROM {$table} LIKE :col");
        $stmt->execute(['col' => $column]);
        if ($stmt->fetch() === false) {
            $db->exec("ALTER TABLE {$table} ADD COLUMN {$definitionSql}");
        }
    }
}
