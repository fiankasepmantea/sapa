<?php

namespace App\Models\SqlModel;

use Ramsey\Uuid\Guid\Guid;
use Ramsey\Uuid\Uuid;
use Dhiva\Core\DhivaAES;

/**
 * @property EndpointModelSql        $endpoint
 * @property SuperUserModelSql       $super_user
 * @property PerekamanFotoModelSql   $perekaman_foto
 * @property PesertaModelSql         $peserta
 */

class BaseModelSql
{
    /**
     * @var string
     */
    protected $table = '';
    /**
     * @var string
     */
    protected $primaryKey = '';
    /**
     * Unique Key pada table
     *
     * @var string
     */
    protected $uniqueKey = '';

    protected $db;

    protected $container = [];

    protected $providers = [
        "endpoint" => EndpointModelSql::class,
        "super_user" => SuperUserModelSql::class,
        "super_group" => SuperGroupModelSql::class,
        "unit" => UnitModelSql::class,
        "tool" => ToolModelSql::class,
        "status" => StatusModelSql::class,
        "application" => ApplicationModelSql::class,
];

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->primaryKey = $this->table . '_id';
        $this->uniqueKey = $this->table . '_unique';
    }
    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        if (!isset($this->providers[$name])) {
            throw new \Exception("class not found");
        } else {
            if (!isset($this->container[$name]) || !$this->container[$name]) {
                try {
                    $this->container["{$name}"] = new $this->providers[$name]();
                } catch (\Exception $e) {
                    throw new $e;
                }
            }
            return $this->container["{$name}"];
        }
    }

    public function index()
    {
        $result = $this->db
            ->table($this->table)
            ->get()
            ->getResult();
        return $result ? $result : false;
    }
    public function show($value)
    {
        $result = $this->db
            ->table($this->table)
            ->where($this->primaryKey, $value)
            ->get()
            ->getRow();
        return $result ? $result : false;
    }
    public function showBy($columnName, $value)
    {
        $result = $this->db
            ->table($this->table)
            ->where($columnName, $value)
            ->get()
            ->getRow();
        return $result ? $result : false;
    }
    public function allBy($columnName, $value)
    {
        $result = $this->db
            ->table($this->table)
            ->where($columnName, $value)
            ->get();
        return $result ? $result->getResult() : false;
    }
    public function insert($data)
    {
        $id = Uuid::uuid6()->toString();
        $data[$this->primaryKey] = $id;
        $data['created_at'] = date('Y-m-d H:i:s');
        $result = $this->db
            ->table($this->table)
            ->insert($data);
        return $result ? $id : false;
    }
    public function pagination($limit, $page, $where = false)
    {
        if (!$page) {
            return 'HALAMAN DIMULAI DARI 1';
        }
        $wh = $where ?: $this->table . '_id is not null';
        $result = $this->db
            ->table($this->table)
            ->where($wh)
            ->countAllResults();
        $pagination['total_data']   = $result;
        $pagination['total_pages']  = ceil($pagination['total_data'] / $limit);
        $pagination['curr_page'] = intval($page);
        $pagination['next_page']    = ($page >= $pagination['total_pages']) ? $pagination['total_pages'] : $page + 1;
        if ($page > 1) {
            $pagination['prev_page'] = $page - 1;
        }
        $pagination['data_per_page'] = intval($limit);
        $offset = ($page - 1)  * $limit;
        $pagination['data'] = $this->db
            ->table($this->table)
            ->where($wh)
            ->limit($limit, $offset)
            ->orderBy($this->table . '_unique', 'DESC')
            ->get()
            ->getResult();
        return $pagination;
    }
    public function paginationpost()
    {
        $result = $this->db
            ->table($this->table);
        if (isset($_POST["to"]) && trim($_POST["to"]) != "") {
            $result->where('DATE(created_at) <=', date('Y-m-d', strtotime($_POST['to'])));
        }
        if (((isset($_POST["where"]) && trim($_POST["where"]) != "") && (isset($_POST["where"]) && trim($_POST["where"]) != ""))) {
            $result->where($_POST['where'], $_POST['set']);
        }
        if ((isset($_POST["groupby"]) && trim($_POST["groupby"]) != "")) {
            $result->groupBy($_POST['groupby']);
        }
        if (((isset($_POST["orderby"]) && trim($_POST["orderby"]) != "")) && ((isset($_POST["sort"]) && trim($_POST["sort"]) != ""))) {
            $result->orderBy($_POST['orderby'], $_POST['sort']);
        }
        $result = $result->countAllResults();
        $pagination['total_data']   = $result;
        $pagination['total_pages']  = ceil($result / intval($_POST['limit']));
        $pagination['curr_page'] = intval($_POST['page']);
        $pagination['next_page'] = ($_POST['page'] >= $pagination['total_pages']) ? $pagination['total_pages'] : $_POST['page'] + 1;
        if ($_POST['page'] > 1) {
            $pagination['prev_page'] = $_POST['page'] - 1;
        }
        $pagination['data_per_page'] = intval($_POST['limit']);
        $offset = ($_POST['page'] - 1)  * $_POST['limit'];
        $pg = $this->db
            ->table($this->table)
            ->limit($_POST['limit'], $offset);
        if (isset($_POST["from"]) && trim($_POST["from"]) != "") {
            $pg->where('DATE(created_at) >=', date('Y-m-d', strtotime($_POST['from'])));
        }
        if (isset($_POST["to"]) && trim($_POST["to"]) != "") {
            $pg->where('DATE(created_at) <=', date('Y-m-d', strtotime($_POST['to'])));
        }
        if (((isset($_POST["where"]) && trim($_POST["where"]) != "") && (isset($_POST["where"]) && trim($_POST["where"]) != ""))) {
            $pg->where($_POST['where'], $_POST['set']);
        }
        if ((isset($_POST["groupby"]) && trim($_POST["groupby"]) != "")) {
            $pg->groupBy($_POST['groupby']);
        }
        if (((isset($_POST["orderby"]) && trim($_POST["orderby"]) != "")) && ((isset($_POST["sort"]) && trim($_POST["sort"]) != ""))) {
            $pg->orderBy($_POST['orderby'], $_POST['sort']);
        }
        $pagination['data'] = $pg->get()
            ->getResult();
        return $pagination;
    }
    public function paginationByDate($limit, $page, $from, $to, $where = false)
    {
        if (!$page) {
            return 'HALAMAN DIMULAI DARI 1';
        }
        $wh = $where ?: $this->table . '_id is not null';
        $result = $this->db
            ->table($this->table)
            ->where(['created_at >=' => $from, 'created_at <=', $to])
            ->where($wh)
            ->countAllResults();
        $pagination['total_data']   = $result;
        $pagination['total_pages']  = ceil($pagination['total_data'] / $limit);
        $pagination['curr_page'] = intval($page);
        $pagination['next_page']    = ($page >= $pagination['total_pages']) ? $pagination['total_pages'] : $page + 1;
        if ($page > 1) {
            $pagination['prev_page'] = $page - 1;
        }
        $pagination['data_per_page'] = intval($limit);
        $offset = ($page - 1)  * $limit;
        $pagination['data'] = $this->db
            ->table($this->table)
            ->where($wh)
            ->limit($limit, $offset)
            ->orderBy($this->table . '_unique', 'DESC')
            ->get()
            ->getResult();
        return $pagination;
    }
    public function update($data, $id)
    {
        unset($data[$this->primaryKey]);
        return $this->db
            ->table($this->table)
            ->where($this->primaryKey, $id)
            ->set($data)
            ->update();
    }
    public function updateBy($data, $id, $where = 0)
    {
        return $this->db
            ->table($this->table)
            ->where($where, $id)
            ->set($data)
            ->update();
    }
    public function destroy($id)
    {
        return $this->db
            ->table($this->table)
            ->where($this->primaryKey, $id)
            ->delete();
    }
    public function findByAnd($arrWhere)
    {
        return $this->db
            ->table($this->table)
            ->where($arrWhere)
            ->get()
            ->getRow();
    }
    public function allByAnd($arrWhere)
    {
        return $this->db
            ->table($this->table)
            ->where($arrWhere)
            ->get()
            ->getResult();
    }

    public function insertBatch(array $data)
    {
        foreach ($data as $row) {
            if (!isset($row['super_user_id'])) {
                $row['super_user_id'] = Uuid::uuid6()->toString();
            }
            if (!isset($row['created_at'])) {
                $row['created_at'] = date('Y-m-d H:i:s');
            }
        }

        $result = $this->db->table($this->table)->insertBatch($data);
        return $result;
    }
}
