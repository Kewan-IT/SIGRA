<?php

class Role extends BaseModel
{
    protected string $table = 'roles';

    public function all(string $orderBy = 'nome'): array
    {
        return parent::all($orderBy);
    }
}
