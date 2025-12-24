<?php

namespace App\Models\SqlModel;

class SuperUserModelSql extends BaseModelSql
{
    protected $table = 'super_user';
    public function auth($username, $password)
    {
        $data = $this->db
            ->table($this->table)
            ->where('password', $password)
            ->where('username', $username)
            ->get()
            ->getRow();
        return $data;
    }
}
