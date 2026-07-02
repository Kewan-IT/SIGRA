<?php

class Department extends BaseModel
{
    protected string $table = 'departments';

    /**
     * Mapa do fluxo fixo do Gabinete do Governador da Zambézia.
     * Cada chave é o departamento actual; o valor é a chave do próximo
     * departamento quando a acção "encaminhar" é executada.
     * Alguns departamentos têm mais de um destino possível (ex: retorno).
     */
    private const FLUXO_ENCAMINHAR = [
        'dfp'                     => 'tecnico',
        'tecnico'                 => 'chefe_departamento',
        'chefe_departamento'      => 'director_gabinete',
        'director_gabinete'       => 'gabinete_governador', // via retorno ao DFP, tratado no controller
        'gabinete_governador'     => 'tribunal_administrativo',
    ];

    public function all(string $orderBy = 'ordem'): array
    {
        return parent::all($orderBy);
    }

    public function findByChave(string $chave): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM departments WHERE chave = :chave LIMIT 1");
        $stmt->execute(['chave' => $chave]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function proximoDaChave(string $chaveAtual): ?string
    {
        return self::FLUXO_ENCAMINHAR[$chaveAtual] ?? null;
    }
}
