<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Dhiva\Core\DhivaAES;
use CodeIgniter\Database\Config;

class DhivaCommand extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Prodev';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'sebat';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Dhiva Command';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'command:name [arguments] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = ['kuy'];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        if (isset($params[0])) {
            if (!file_exists(APPPATH . '/Config/Database.php')) {
                $schema = $this->initDb();
                if ($schema) {
                    CLI::write("Installasi core berhasil ya baginda", 'green');
                    $this->createAppId();
                    $this->changeEnv();
                }
            } else {
                CLI::write("Sudah di config wahai yang mulia baginda", 'green');
            }
        } else {
            if (!file_exists(APPPATH . '/Config/Database.php')) {
                CLI::write('Aplikasi belum di setup ya baginda' . PHP_EOL . 'Silahkan jalankan perintah');
                CLI::write('php spark sebat kuy', 'green');
                die;
            }
            CLI::write('Buah duku di pohon kaktus' . PHP_EOL . 'Tidak bisa diambil karena berduri' . PHP_EOL . 'Pinjamkanlah dulu aku sembilan ratus' . PHP_EOL . 'Biar aku bisa wara-wiri' . PHP_EOL);
            $command = CLI::promptByKey(
                'Perintahmu ya Baginda?',
                [
                    'Ubah environment ke ' . $this->checkEnv(),
                    'Buatkan APP ID',
                    'Buatkan CRUD',
                    'Ubah durasi JWT Token',
                    'Buatkan Kopi',
                    'Belum kepikiran ntar dulu'
                ]
            );
            switch ($command) {
                case 0:
                    $this->changeEnv();
                    break;
                case 1:
                    $this->createAppId();
                    break;
                case 2:
                    $this->createComponent();
                    break;
                case 3:
                    $this->changeJWTtimeout();
                    break;
                case 4:
                    CLI::write("Silahkan bikin sendiri wahai banginda");
                    break;
                case 5:
                    CLI::write("Mikir mulu!");
                    break;
                default:
                    CLI::write("Perintahnya tidak ada wahai baginda");
            }
        }
    }
    private function changeJWTtimeout()
    {
        if (JWT_BY == 'JAM') {
            CLI::write("Durasi Sekarang : " . JWT_TIMEOUT / HOUR . " " . JWT_BY);
        } elseif (JWT_BY == 'HARI') {
            CLI::write("Durasi Sekarang : " . JWT_TIMEOUT / DAY . " " . JWT_BY);
        }
        CLI::newLine(1);
        $pd = CLI::promptByKey('Pilih Durasi ', ['JAM', 'HARI']);
        if ($pd == 0) {
            $dr = CLI::prompt("Berapa jam?", null, ['required']);
            if (preg_match("/^\\d+$/", $dr)) {
                $str = file_get_contents(APPPATH . '/Config/Constants.php');
                $new = HOUR * $dr;
                $str = str_replace(['define("JWT_TIMEOUT", ' . JWT_TIMEOUT . ');', 'define("JWT_BY", "' . JWT_BY . '");'], ['define("JWT_TIMEOUT", ' . $new . ');', 'define("JWT_BY", "JAM");'], $str);
                file_put_contents(APPPATH . '/Config/Constants.php', $str);
                CLI::write("Durasi Sekarang : " . $dr . " Jam");
            }
        } elseif ($pd == 1) {
            $dr = CLI::prompt("Berapa hari?", null, ['required']);
            if (preg_match("/^\\d+$/", $dr)) {
                $str = file_get_contents(APPPATH . '/Config/Constants.php');
                $new = DAY * $dr;
                $str = str_replace(['define("JWT_TIMEOUT", ' . JWT_TIMEOUT . ');', 'define("JWT_BY", "' . JWT_BY . '");'], ['define("JWT_TIMEOUT", ' . $new . ');', 'define("JWT_BY", "HARI");'], $str);
            }
            file_put_contents(APPPATH . '/Config/Constants.php', $str);
            CLI::write("Durasi Sekarang : " . $dr . " Hari");
        }
    }
    private function initDb()
    {
        $return = false;
        $schema = '';
        $DBDriver = CLI::promptByKey('Database Driver ', ['MySQLi', 'Postgre']);
        $username = CLI::prompt("Username Database", null, ['required']);
        $password = CLI::prompt("Password Database");
        $tabel = CLI::prompt("Nama Database", null, ['required']);
        if ($DBDriver == 0) {
            $DBDriver = 'MySQLi';
            $schema = '';
            $str = file_get_contents(APPPATH . '/Libraries/DhivaComponent/DatabaseClient.txt');
            $str = str_replace(['{{DBDriver}}', '{{username}}', '{{password}}', '{{tabel}}', '{{schema}}'], [$DBDriver, $username, $password, $tabel, $schema], $str);
            file_put_contents(APPPATH . '/Config/Database.php', $str);
            try {
                $first = [
                    'DSN'          => '',
                    'hostname'     => 'localhost',
                    'username'     => $username,
                    'password'     => $password,
                    'database'     => '',
                    'DBDriver'     => 'MySQLi',
                    'schema'       => '',
                    'DBPrefix'     => '',
                    'pConnect'     => false,
                    'DBDebug'      => true,
                    'charset'      => 'utf8',
                    'DBCollat'     => 'utf8_bin',
                    'swapPre'      => '',
                    'encrypt'      => false,
                    'compress'     => false,
                    'strictOn'     => false,
                    'failover'     => [],
                    'numberNative' => false,
                ];
                $forge = \Config\Database::forge($first);
                $forge->createDatabase($tabel);
            } catch (\Throwable $e) {
                CLI::write('Ini Errornya ya baginda');
                CLI::write($e->getMessage(), 'red');
                unlink(APPPATH . '/Config/Database.php');
                die;
            }
            $this->initTbMySql();
            $return = true;
        } else if ($DBDriver == 1) {
            $DBDriver = 'Postgre';
            $schema = CLI::prompt("Schema Database", null, ['required']);
            $str = file_get_contents(APPPATH . '/Libraries/DhivaComponent/DatabaseClient.txt');
            $str = str_replace(['{{DBDriver}}', '{{username}}', '{{password}}', '{{tabel}}', '{{schema}}'], [$DBDriver, $username, $password, $tabel, $schema], $str);
            file_put_contents(APPPATH . '/Config/Database.php', $str);
            try {
                $db = \Config\Database::connect();
                $db->query('CREATE SCHEMA ' . $schema);
                $db->error();
            } catch (\Throwable $e) {
                CLI::write('Ini Errornya ya baginda');
                CLI::write($e->getMessage(), 'red');
                unlink(APPPATH . '/Config/Database.php');
                die;
            }
            $this->initTbPostgre();
            $return = true;
        }
        $this->dbseed();
        return $return;
    }
    private function createComponent()
    {
        $component = CLI::prompt("Nama Component", null, ['required']);
        $routes = CLI::prompt("Nama Routing", null, ['required']);
        $tabel = CLI::prompt("Tabel Database", null, ['required']);
        $str1 = file_get_contents(APPPATH . '/Libraries/DhivaComponent/Controller.txt');
        $str1 = str_replace(['{{controller}}', '{{model}}', '{{tabel}}'], [ucfirst($component), ucfirst($component), $tabel], $str1);
        file_put_contents(APPPATH . "/Controllers/" . ucfirst($component) . 'Controller.php', $str1);

        $str2 = file_get_contents(APPPATH . '/Libraries/DhivaComponent/Model.txt');
        $str2 = str_replace(['{{model}}', '{{tabel}}'], [ucfirst($component), $tabel], $str2);
        file_put_contents(APPPATH . "/Models/SqlModel/" . ucfirst($component) . 'ModelSql.php', $str2);

        $str3 = file_get_contents(APPPATH . '/Libraries/DhivaComponent/Routes.txt');
        $str3 = str_replace(['{{routes}}', '{{controller}}'], [$routes, ucfirst($component)], $str3);
        file_put_contents(APPPATH . "/Config/Routes.php", $str3, FILE_APPEND);

        $str4 = file_get_contents(APPPATH . '/Models/SqlModel/BaseModelSql.php');
        $s = explode('];', explode('protected $providers = [', $str4)[1]);
        $r = $s[0] . '    "' . $tabel . '" => ' . ucfirst($component) . 'ModelSql::class,' . PHP_EOL;
        $done = str_replace($s[0], $r, $str4);
        file_put_contents(APPPATH . '/Models/SqlModel/BaseModelSql.php', $done);
        $this->createComponentDb($tabel);
        CLI::write("Component berhasil ditambahkan ya baginda", 'green');
    }
    /**
     * Masih ada bug buat primary key di Postgre
     * jadi belum bisa dipake
     * @link https://codeigniter4.github.io/userguide/dbmgmt/forge.html#forge-addprimarykey
     */
    private function createComponentDb($tabel, $buat = false)
    {
        if ($buat) {

            $forge = Database::forge();
            $forge->addField([
                $tabel . '_id' => [
                    'type'           => 'VARCHAR',
                    'constraint'     => 200,
                    'primary'       => true
                ],
                $tabel . '_unique' => [
                    'type'           => 'INT',
                    'constraint'     => 255,
                    'auto_increment' => true,
                    'unique'         => true,
                ],
                'created_at timestamp without time zone NULL DEFAULT CURRENT_TIMESTAMP'
            ]);
            $forge->createTable($tabel);
            $db = \Config\Database::connect();
            $db->query('ALTER TABLE ' . $tabel . ' ADD CONSTRAINT ' . $tabel . '_pk PRIMARY KEY (' . $tabel . '_id)');
        } else {
            $forge = \Config\Database::forge();
            $forge->addField([
                $tabel . '_id' => [
                    'type'           => 'VARCHAR',
                    'constraint'     => 200,
                ],
                $tabel . '_unique' => [
                    'type'           => 'INT',
                    'constraint'     => 255,
                    'auto_increment' => true,
                    'unique'         => true,
                ],
                'created_at timestamp without time zone NULL DEFAULT CURRENT_TIMESTAMP'
            ]);
            $forge->addKey($tabel . '_id', true);
            $forge->createTable($tabel);
        }
    }
    private function checkEnv()
    {
        return (ENVIRONMENT != 'development') ? 'development' : 'production';
    }
    private function createAppId()
    {
        $str = file_exists(APPPATH . '/Config/Keys.php');
        if (!$str) {
            // Jangan di dirapihin/format document!, memang kek gini rule nya
            $text = '<?php

namespace Dhiva\Core;

class Keys
{
    public static $password = "' . DhivaAES::randomStr(16) . '";
    public static $salt = "' . DhivaAES::randomStr(16) . '";
    public static $iv = "' . DhivaAES::randomStr(16) . '";
    public static $iterations = 2;
    public static $keyLength = 2;
    public static $route = "' . DhivaAES::randomStr(16) . '";
    public static $JWT_KEY = "' . DhivaAES::randomStr(20) . '";
}

            ';
            file_put_contents(APPPATH . '/Config/Keys.php', $text, FILE_APPEND);
            CLI::write('Berhasil ditambahkan APP ID ya baginda', 'green');
        } else {
            CLI::write('APP ID Sudah ada ya banginda, tidak perlu di generate lagi', 'green');
        }
    }
    private function changeEnv()
    {
        if (!file_exists(ROOTPATH . ".env")) {
            if (!file_exists(ROOTPATH . "env")) {
                $str = file_get_contents(APPPATH . '/Libraries/DhivaComponent/env.txt');
                file_put_contents(ROOTPATH . ".env", $str);
            } else {
                copy(ROOTPATH . "env", ROOTPATH . ".env");
            }
        }
        $str = file_get_contents(ROOTPATH . ".env");
        if (str_contains($str, '# CI_ENVIRONMENT')) {
            $search = '# CI_ENVIRONMENT';
        } else {
            $search = 'CI_ENVIRONMENT';
        }
        if (ENVIRONMENT == 'development') {
            $str = str_replace($search . ' = development', $search . ' = production', $str);
            $rr = 'production';
        } else {
            $str = str_replace($search . ' = production', $search . ' = development', $str);
            $rr = 'development';
        }
        file_put_contents(ROOTPATH . ".env", $str);
        CLI::write('Berhasil dirubah ke ' . $rr . ' ya baginda', 'green');
    }
    private function initTbPostgre()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();
        $forge->addField([
            'super_user_id' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
            ],
            'super_user_unique' => [
                'type'           => 'INT',
                'constraint'     => 255,
                'auto_increment' => true,
                'unique'         => true,
            ],
            'super_group_id'     => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
                'null'           => true,
            ],
            'domain_id'     => [
                'type'           => 'INT',
                'constraint'     => 5,
            ],
            'name' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
            ],
            'email' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
                'null'           => true,
            ],
            'username' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
            ],
            'password' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
            ],
            'avatar' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
                'null'           => true,
            ],
            'status' => [
                'type'           => 'INT',
                'constraint'     => 1,
                'null'           => true,
            ],
            'token' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
                'null'           => true,
            ],
            'created_at timestamp without time zone NULL DEFAULT CURRENT_TIMESTAMP',
            'login_date timestamp without time zone NULL DEFAULT CURRENT_TIMESTAMP',
            'access_at timestamp without time zone NULL DEFAULT CURRENT_TIMESTAMP'
        ]);
        $forge->createTable('super_user');
        $db->query('ALTER TABLE ' . 'super_user ADD CONSTRAINT super_user_pk PRIMARY KEY (super_user_id)');
        $forge->addField([
            'super_group_id'     => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
            ],
            'super_group_unique' => [
                'type'           => 'INT',
                'constraint'     => 255,
                'auto_increment' => true,
                'unique'         => true,
            ],
            'domain_id' => [
                'type'           => 'INT',
                'constraint'     => 255,
            ],
            'group_name' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
                'null'           => true,
            ],
            'status' => [
                'type'           => 'INT',
                'constraint'     => 1,
                'null'           => true,
            ],
            'created_at timestamp without time zone NULL DEFAULT CURRENT_TIMESTAMP',
        ]);
        $forge->createTable('super_group');
        $db->query("CREATE TYPE endpoint_type" . " AS enum ('frontend', 'backend')");
        $db->query("CREATE TYPE endpoint_method" . " AS enum ('POST', 'GET', 'PUT', 'DELETE')");
        $db->query("CREATE TYPE endpoint_bypass" . " AS enum ('0', '1')");

        $forge->addField([
            'endpoint_id'     => [
                'type'           => 'INT',
                'constraint'     => 255,
                'auto_increment' => true,
            ],
            'value' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
            ],
            'description' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
                'null'           => true,
            ],
            'created_by' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
                'null'           => true,
            ],
            'created_at timestamp without time zone NULL DEFAULT CURRENT_TIMESTAMP',
        ]);
        $forge->createTable('endpoint');
        $db->query('ALTER TABLE endpoint ADD CONSTRAINT endpoint_id_pk PRIMARY KEY (endpoint_id)');
        $db->query("ALTER TABLE endpoint ADD method endpoint_method NOT NULL;");
        $db->query("ALTER TABLE endpoint ADD type endpoint_type NOT NULL;");
        $db->query("ALTER TABLE endpoint ADD bypass endpoint_bypass NOT NULL;");
    }
    private function initTbMySql()
    {
        $forge = \Config\Database::forge();
        $forge->addField([
            'super_user_id' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
            ],
            'super_user_unique' => [
                'type'           => 'INT',
                'constraint'     => 255,
                'auto_increment' => true,
                'unique'         => true,
            ],
            'super_group_id' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
                'null'           => true,
            ],
            'domain_id'     => [
                'type'           => 'INT',
                'constraint'     => 5,
            ],
            'name' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
            ],
            'email' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
                'null'           => true,
            ],
            'username' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
            ],
            'password' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
            ],
            'avatar' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
                'null'           => true,
            ],
            'status' => [
                'type'           => 'INT',
                'constraint'     => 1,
                'null'           => true,
            ],
            'token' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
                'null'           => true,
            ],
            'created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP',
            'login_date DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'access_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);
        $forge->addKey('super_user_id', true);
        $forge->createTable('super_user');
        $forge->addField([
            'super_group_id'     => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
            ],
            'super_group_id' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
                'null'           => true,
            ],
            'domain_id' => [
                'type'           => 'INT',
                'constraint'     => 255,
            ],
            'group_name' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
                'null'           => true,
            ],
            'status' => [
                'type'           => 'INT',
                'constraint'     => 1,
                'null'           => true,
            ],
            'created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $forge->addKey('super_group_id', true);
        $forge->createTable('super_group');
        $forge->addField([
            'endpoint_id'     => [
                'type'           => 'INT',
                'constraint'     => 255,
                'auto_increment' => true,
            ],
            'value' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
            ],
            'method' => [
                'type'           => 'ENUM',
                'constraint'     => ['POST', 'GET', 'PUT', 'DELETE']
            ],
            'type' => [
                'type'           => 'ENUM',
                'constraint'     => ['frontend', 'backend'],
                'default'        => 'backend',
            ],
            'bypass' => [
                'type'           => 'ENUM',
                'constraint'     => ['0', '1'],
                'default'        => '0',
            ],
            'description' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
                'null'           => true,
            ],
            'created_by' => [
                'type'           => 'VARCHAR',
                'constraint'     => 200,
                'null'           => true,
            ],
            'created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $forge->addKey('endpoint_id', true);
        $forge->createTable('endpoint');
    }
    private function dbseed()
    {
        $endpoint = [
            [
                'value'         => '/auth',
                'method'        => 'POST',
                'type'          => 'backend',
                'bypass'        => '1',
                'description'   => '',
                'created_by'    => 'admin',
            ],
            [
                'value'         => '/endpoencode',
                'method'        => 'POST',
                'type'          => 'backend',
                'bypass'        => '0',
                'description'   => '',
                'created_by'    => 'admin',
            ],
            [
                'value'         => '/endpodecode',
                'method'        => 'POST',
                'type'          => 'backend',
                'bypass'        => '0',
                'description'   => '',
                'created_by'    => 'admin',
            ]
        ];

        $super_group = [
            [
                'super_group_id'    => '3b4qDSQnzgMD6Pb3ERHRm2A8Q4nl1sNb8t5GQNNTV8I',
                'domain_id'         => 0,
                'group_name'        => 'SUPER ADMIN',
                'status'            => 1,
            ],
            [
                'super_group_id'    => 'uxTYrVUDOM4wt3ERHRm2A8Q4nl1sNb8t5GQNNTV8I',
                'domain_id'         => 0,
                'group_name'        => 'MEMBER',
                'status'            => 1,
            ]
        ];

        $super_user = [
            [
                'super_user_id'     => '3b4qDSQnzgMD6PbpqB5AIFiaIpb5V96x8t5GQNNTV8I',
                'super_group_id' => '3b4qDSQnzgMD6Pb3ERHRm2A8Q4nl1sNb8t5GQNNTV8I',
                'domain_id'         => 0,
                'name'              => 'arel',
                'email'             => 'arel@arel.com',
                'username'          => 'arel',
                'password'          => '21232f297a57a5a743894a0e4a801fc3',
            ],
            [
                'super_user_id'     => 'UvURwrrMy-Rm2A8Q4nl1sNb-uxTYrVUDOM4wt3_ERH',
                'super_group_id' => 'uxTYrVUDOM4wt3ERHRm2A8Q4nl1sNb8t5GQNNTV8I',
                'domain_id'         => 0,
                'name'              => 'adit',
                'email'             => 'adit@adit.com',
                'username'          => 'adit',
                'password'          => '486b6c6b267bc61677367eb6b6458764',
            ],
        ];
        $db = \Config\Database::connect();
        $db->table('endpoint')->insertBatch($endpoint);
        $db->table('super_group')->insertBatch($super_group);
        $db->table('super_user')->insertBatch($super_user);
    }
}
class Database extends Config
{
}
