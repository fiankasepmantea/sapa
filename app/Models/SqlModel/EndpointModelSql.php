<?php

namespace App\Models\SqlModel;

class EndpointModelSql extends BaseModelSql
{
    protected $table = 'endpoint';

    public function insert($data)
    {
        return $this->db
            ->table($this->table)
            ->insert($data);
    }
}
