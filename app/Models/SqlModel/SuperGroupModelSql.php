<?php

namespace App\Models\SqlModel;

class SuperGroupModelSql extends BaseModelSql
{
    protected $table = 'super_group';

    public function insert($data)
    {
        $result = $this->db
            ->table($this->table)
            ->insert($data);
        return $result;
    }
}
