<?php

namespace App\Models\SqlModel;

class ApplicationModelSql extends BaseModelSql
{
    protected $table = 'application';

    public function indexApplication() {
        $result = $this->db
            ->table($this->table)
            ->join('unit u', 'u.unit_id = application.unit_id','innerjoin')
            ->join('super_user su', 'su.super_user_id = application.super_user_id', 'innerjoin')
            ->join('tool t', 't.tool_id = application.tool_id', 'leftjoin')
            ->join('status s', 's.status_id = application.status_id', 'innerjoin')
            ->get()
            ->getResult();
        return $result ? $result : false;
    }
}
