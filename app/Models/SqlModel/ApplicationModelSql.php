<?php

namespace App\Models\SqlModel;

class ApplicationModelSql extends BaseModelSql
{
    protected $table = 'application';

    public function indexApplication() {
        $result = $this->db
            ->table($this->table)
            ->get()
            ->getResult();
        return $result ? $result : false;
    }
}
