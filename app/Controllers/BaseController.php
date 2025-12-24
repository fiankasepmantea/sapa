<?php

namespace App\Controllers;

use Dhiva\Core\DhivaAES;
use Dhiva\Core\Keys;
use WebPConvert\WebPConvert;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use ArelAyudhi\DhivaProdevWa;
use Zoom\ZoomAPI;
// use App\Models\MongoModel\BaseMongoNoSql as BaseModelNoSql;
use Ramsey\Uuid\Guid\Guid;
use Ramsey\Uuid\Uuid;

use CodeIgniter\Database\Config;

const HTTP_BAD_REQUEST = 400;
const HTTP_UNAUTHORIZED = 401;
const HTTP_OK = 200;

const INSERT = 0;
const UPDATE = 1;
const DELETE = 2;
const GET    = 3;
const UNAUTHORIZED = 4;

const UNAUTHORIZED_CODE   = ['Unauthorized access', "incomplete data", "Token Expired", "Forbidden access", "Limited Access"];
const ERROR_CODE          = ["Gagal menambah data", "Gagal merubah data", "Gagal menghapus data", "Gagal mengambil data"];
const SUCCESS_CODE        = ["Berhasil menambah data", "Berhasil merubah data", "Berhasil menghapus data"];

abstract class BaseController extends Controller
{
    public $db;
    private $headers;
    private $Authorization;
    private $ClientSecret;
    protected $PublicKey;
    protected $userDatas;
    protected $zooms;

    protected $model;
    protected $mongo;
    protected $primaryKey;
    protected $table;

    private $isbypassed;
    private $destination;
    public $wablast;
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];
    // protected $session;
    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        helper('date');

        $this->model = model('App\Models\SqlModel\BaseModelSql');
        // $this->mongo = new BaseModelNoSql;
        // $this->wablast = new DhivaProdevWa\ProdevMessages(ProdevToken);
        // $this->zooms = new ZoomAPI("v8Tm8HvlRgSVazW3qfeTVw", "escqI7TyORCF13nRqi5GEVcD3tcbloDi", 'DvUhFu5BRJ2b7BOx43k4Xg');
        // $this->zooms = new ZoomAPI("1xYQb2cDS6KE5B8dS3XqBw", "De2Truv0ZdgQx4mUKRXDFHlEquv2He2n", 'CNXfyolVQU26LxA5rcHiUg');
        $this->initControllers();
        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
    }
    public function initControllers()
    {

        // $this->response = ResponseTrait::response();
        // $this->get_client_ip();
        // $this->response(GET, phpinfo());
        /**
         * @var array $bypassed isi array di variable $bypassed untuk mengabaikan pengecekan endpoint di database.
         * @example 	string $bypassed = ["/auth" => 'POST', "/data" => 'GET'];
         */
        $bypassed = [];
        /**
         * @var string $publickey terdapat di encodeloop header dan master db
         * @example : $publickey = 'bHJ0LmFuZHJvd2ViaG9zdC5jb20=';
         */
        $publickey = '';
        $this->initHeader();
        // print_r('asd');die;
        $this->initDb('postgre', $publickey);
        if (!$this->checkEndpoint($this->getEndpointInfo(), $bypassed)) {
            if (isset($this->ClientSecret) && isset($this->Authorization)) {
                if ((DhivaAES::jwtvalidator($this->ClientSecret, $this->Authorization))) {
                    $decodedToken = DhivaAES::validateTimestampWtihUserAccess($this->Authorization);
                    if (isset($decodedToken->super_user_id)) {
                        $update['access_at'] = date('Y-m-d H:i:s', time());
                        $this->userDatas = $decodedToken;
                        $this->model->super_user->update($update, $decodedToken->super_user_id);
                    } else {
                        $this->response(UNAUTHORIZED, 2);
                    }
                } else {
                    $this->response(UNAUTHORIZED, 3);
                }
            } else {
                $this->response(UNAUTHORIZED, 3);
            }
        }
    }
    public function decodeDb($dataDb)
    {
        $underscore      = '';
        $decodedDbResult = '';
        //get encoded domain,username,password
        $domain     = $dataDb->domain_db;
        $usernameDb = "";
        $passwordDb = "";
        if (isset($dataDb->username_db))
            $usernameDb = $dataDb->username_db;
        if (isset($dataDb->password_db))
            $passwordDb = $dataDb->password_db;

        //decode domain,username,password
        $decodeDomain = decodeloop($domain);

        $decodeUsernameDb = decodeloop($usernameDb);
        $decodePasswordDb = decodeloop($passwordDb);
        $splittedWords    = explode('_', $decodeDomain);

        foreach ($splittedWords as $key => $value) {
            if ($key != (count($splittedWords) - 1)) {
                $underscore = "_";
            }
            $decodedDbResult .= deshuffle_word($value) . $underscore;
            $underscore = '';
        }

        //change value domain,userbaneDb,passwordDb into decoded string
        $dataDb->domain_db   = $decodedDbResult;
        $dataDb->username_db = $decodeUsernameDb;
        $dataDb->password_db = $decodePasswordDb;
        return $dataDb;
    }
    public function response($code, $data = NULL)
    {
        $HTTP_CODE   = HTTP_BAD_REQUEST;
        $message = '';
        if (!$data) {
            if (ENVIRONMENT == 'development') {
                if ($code == 4) {
                    $message = UNAUTHORIZED_CODE[0];
                    $success = false;
                    $HTTP_CODE   = HTTP_UNAUTHORIZED;
                } elseif($code == 426) {
                    $success = true;
                    $HTTP_CODE   = HTTP_UPGRADE_REQUIRED;
                    $message = $data;
                } else {
                    $success = false;
                    $message = ERROR_CODE[$code] . " !";
                }
            } else {
                $success = false;
            }
        } else {
            if ($code == 3) {
                $success = true;
                $message = $data;
                $HTTP_CODE   = HTTP_OK;
            } elseif ($code == 0) {
                if (!$data) {
                    $success = false;
                    $message = ERROR_CODE[$code] . " !";
                } else {
                    $message =  SUCCESS_CODE[$code] . " !";
                    if (ENVIRONMENT == 'production') {
                        $message = self::encryptPayload(json_encode($message));
                        if (!$message) {
                            $message = 'Token Invalid!';
                        }
                    }
                    $success = true;
                    $HTTP_CODE   = HTTP_OK;
                }
            } else {
                if ($code == 4) {
                    $message = UNAUTHORIZED_CODE[$data];
                    $success = false;
                    $HTTP_CODE   = HTTP_UNAUTHORIZED;
                }elseif($code == 426) {
                    $success = true;
                    $HTTP_CODE   = HTTP_UPGRADE_REQUIRED;
                    $message = $data;
                }else {
                    $message = SUCCESS_CODE[$code] . " !";
                    if (ENVIRONMENT == 'production') {
                        $message = self::encryptPayload(json_encode($message));
                        if (!$message) {
                            $message = 'Token Invalid!';
                        }
                    }
                    $success = true;
                    $HTTP_CODE   = HTTP_OK;
                }
            }
        }
        if (ENVIRONMENT == 'production') {
            $message = self::encryptPayload(json_encode($message));
            if (!$message) {
                $message = 'Token Invalid!';
            }
        }
        $response = [
            'success' => $success,
            'code' => $HTTP_CODE,
            'message' => $message,
        ];
        header('Content-Type: application/json');
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        die;
    }
    public function responseStep1Successfully($code, $data = null)
    {
        $HTTP_CODE = HTTP_BAD_REQUEST;
        $success = false;
        $message = '';

        if ($code === 0) {
            $HTTP_CODE = HTTP_OK;
            $success = true;
            $message = $data['message'] ?? 'Success';
        } else {
            $message = 'An error occurred.';
        }

        $response = [
            'status' => $success ? 'success' : 'error',
            'message' => $message,
            'data' => $data['data'] ?? null
        ];

        header('Content-Type: application/json');
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        die;
    }

    function post($key = null, $clean = true)
    {
        // print_r([$_POST[$key], $key]);die;
        if (ENVIRONMENT == 'development') {
            if ($key) {
                if (isset($_POST[$key])) {
                    return  $_POST[$key];
                }
            } else {
                return $_POST;
            }
        } else {
            $encrypt_method = "AES-256-CBC";
            if ($this->isbypassed) {
                $secret_key = Keys::$Payload_Key;
                $secret_iv = Keys::$Payload_IV;
            } else {
                $secret_key = md5($this->userDatas->token);
                $secret_iv = md5($this->userDatas->access_at);
            }
            $keys = hash('sha256', $secret_key);
            $iv = substr(hash('sha256', $secret_iv), 0, 16);
            $decodedEncryptedData = $_POST['data'];
            $decrypted = openssl_decrypt($decodedEncryptedData, $encrypt_method, $keys, 0, $iv);
            $result = (array) json_decode(base64_decode($decrypted));
            if ($result == false) {
                $response = [
                    'code' => 203,
                    'success' => false,
                    'message' => 'Invalid Code',
                ];
                header('Content-Type: application/json');
                echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                die;
            }
            if (!$key) {
                $tr = [];
                foreach ($result as $k => $val) {
                    $g = self::setArrayValue($k, $val);
                    if ($g) {
                        $tr[[array_keys($g)[0]][0]][0][array_keys($g[array_keys($g)[0]][0])[0]] = array_values($g[array_keys($g)[0]][0])[0];
                    } else {
                        $tr[$k] = $val;
                    }
                }
                return $tr;
            }
            if (isset($result[$key])) {
                return $result[$key];
            }
            return false;
        }
        return false;
    }
    function setArrayValue($string, $value)
    {
        if (preg_match('/^(\w+)\[(\d+)\]\[(\w+)\]$/', $string, $matches)) {
            $root = $matches[1];
            $index = (int)$matches[2];
            $key = $matches[3];
            $array = [
                $root => [
                    $index => [
                        $key => $value
                    ]
                ]
            ];
            return $array;
        } else {
            return false;
        }
    }
    protected function encryptPayload($string)
    {
        $encrypt_method = "aes-256-cbc";
        if ($this->isbypassed) {
            $secret_key = Keys::$Payload_Key;
            $secret_iv = Keys::$Payload_IV;
        } else {
            $secret_key = md5($this->userDatas->token);
            $secret_iv = md5($this->userDatas->access_at);
        }
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
        return $output;
    }
    protected function decryptPayload($string)
    {
        $encrypt_method = "aes-256-cbc";
        if ($this->isbypassed) {
            $secret_key = Keys::$Payload_Key;
            $secret_iv = Keys::$Payload_IV;
        } else {
            $secret_key = md5($this->userDatas->token);
            $secret_iv = md5($this->userDatas->access_at);
        }
        $keys = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $decodedEncryptedData = $string;
        $decrypted = openssl_decrypt($decodedEncryptedData, $encrypt_method, $keys, 0, $iv);
        $result = json_decode(base64_decode($decrypted));
        print_r($result);
        die;
    }
    protected function getEndpointInfo()
    {
        if (!$_SERVER['PATH_INFO']) {
            $PATH_INFO = str_replace($_SERVER['DOCUMENT_URI'], "", $_SERVER['REQUEST_URI']);
            $endpointAccessed = $PATH_INFO;
        } else {
            $endpointAccessed = $_SERVER['PATH_INFO'];
        }

        $endpointRequestMethod = $_SERVER['REQUEST_METHOD'];
        $endpointRequest = "/";
        $endpointUpdate = "update";
        $endpointDelete = "delete";
        $endpointPage = "pages";
        $endpointPages = "pagesbydate";
        $endpointAccessExploded = explode('/', $endpointAccessed ?? '');
        $countEndpointAccessExploded = count($endpointAccessExploded);

        $separator = '/';
        $lastSegment = $countEndpointAccessExploded - 1;
        for ($i = 0; $i <= $lastSegment; $i++) {
            if ($countEndpointAccessExploded - 1 == $i) {
                $separator = "";
            }

            if ($endpointAccessExploded[$i] != "") {
                $endpointRequest .= $endpointAccessExploded[$i] . $separator;
            }
            if (($endpointAccessExploded[$i] == $endpointUpdate) || ($endpointAccessExploded[$i] == $endpointDelete) || ($endpointAccessExploded[$i] == $endpointPage) || ($endpointAccessExploded[$i] == $endpointPages)) {
                break;
            }
            if (str_contains($endpointAccessExploded[$i], '_by')) {
                $lastSegment = $i + 1;
            }
        }
        // print_r($endpointRequest);die();
        $endpointRequest = str_replace('/gustangan', '', $endpointRequest);
        return [$endpointRequestMethod, $endpointRequest];
    }
    public function DbInit($dbSelect, $query, $useMasterDb = true)
    {
        if ($query && $useMasterDb) {
            $dbSelect =
                [
                    'DSN'      => '',
                    'hostname' => 'localhost',
                    'username' => encodeloop($query->username_db),
                    'password' => encodeloop($query->password_db),
                    'database' => $query->domain_db,
                    'DBPrefix' => $query->domain_db . '.',
                    'pConnect' => false,
                    'DBDebug'  => true,
                    'charset'  => 'utf8',
                    'DBCollat' => 'utf8_bin',
                    'swapPre'  => '',
                    'encrypt'  => true,
                    'compress' => false,
                    'strictOn' => false,
                    'failover' => [],
                ];
        }
        // if ($PGDBPrefix) {
        //     $db = new \Config\Database;
        //     $dbSelect = $db->postgre;
        //     $dbSelect['DBPrefix'] = '';
        //     $this->table = $PGDBPrefix;
        // }
        $this->db = \Config\Database::connect($dbSelect);
    }
    // private function get_client_ip()
    // {
    //     if (isset($_SERVER['REMOTE_ADDR']) && isset($_SERVER['SERVER_NAME'])) {
    //         if (($_SERVER['SERVER_NAME'] == NAMA_SERVER) && ($_SERVER['REMOTE_ADDR'] != REMOT) && (ENVIRONMENT == "production")) {
    //             $this->response(UNAUTHORIZED, 3);
    //         }
    //     } else {
    //         $this->response(UNAUTHORIZED, 3);
    //     }
    // }
    /**
     * Inisialisasi tipe database yang dijalankan
     *
     * @param string $selectedDb @var| postgre | mysql.
     * @return void
     */
    private function initDB(string $selectedDb, string $publickey)
    {
        $this->DbInit($selectedDb, false);
        if (isset($this->PublicKey)) {
            $builder = $this->db
                ->table('super_domain')
                ->where('name', decodeloop($this->PublicKey));
            $dbInfo =  $builder->get()->getRow();
            if (!$dbInfo) {
                $this->response(UNAUTHORIZED, 1);
            }
            $this->DbInit($selectedDb, $this->decodeDb($dbInfo));
        } else {
            if ($publickey) {
                $builder = $this->db
                    ->table('super_domain')
                    ->where('name', decodeloop($this->PublicKey));
                $dbInfo =  $builder->get()->getRow();
                if ($dbInfo) {
                    $this->response(UNAUTHORIZED, 1);
                }
                $this->DbInit($selectedDb, false, false);
            } else {
                $this->DbInit($selectedDb, false, false);
            }
        }
    }
    private function initHeader()
    {
        $this->headers = getallheaders();
        if (isset($this->headers['Publickey'])) {
            $this->PublicKey = $this->headers['Publickey'];
        } else if (isset($this->headers['publickey'])) {
            $this->PublicKey = $this->headers['publickey'];
        }
        if (isset($this->headers['ClientSecret'])) {
            $this->ClientSecret = $this->headers['ClientSecret'];
        } else if (isset($this->headers['clientSecret'])) {
            $this->ClientSecret = $this->headers['clientSecret'];
        } else if (isset($this->headers['clientsecret'])) {
            $this->ClientSecret = $this->headers['clientsecret'];
        } else if (isset($this->headers['Clientsecret'])) {
            $this->ClientSecret = $this->headers['Clientsecret'];
        }
        if (isset($this->headers['Authorization'])) {
            $this->Authorization = $this->headers['Authorization'];
        } else if (isset($this->headers['authorization'])) {
            $this->Authorization = $this->headers['authorization'];
        }
    }
    private function checkEndpoint($endpoint, $bypassed = [])
    {
        $status = false;
        if (!$bypassed) {
            $builder = $this->db
                ->table('endpoint')
                ->where('method', $endpoint[0])
                ->like('value', $endpoint[1], 'after')
                ->limit(1);
            $result =  $builder->get()->getRow();
            // print_r($result);die;
            if (isset($result)) {
                if ($result->bypass == true) {
                    $status = true;
                    $this->isbypassed = true;
                } else {
                    $exp = explode('/', $endpoint[1]);
                    if (in_array('pagination', $exp)) {
                        if (isset($_POST['where'])) {
                            $pp =  explode('#', $result->pagination);
                            $wheres = explode(',', $_POST['where']);
                            if (!array_intersect($pp, $wheres)) {
                                $this->response(UNAUTHORIZED, 3);
                            }
                        }
                    }
                    $status = false;
                }
            } else {
                $this->response(UNAUTHORIZED, 3);
            }
        } else {
            if (in_array($endpoint[1], $bypassed) == false) {
                if (!empty($bypassed[$endpoint[1]])) {
                    if ($bypassed[$endpoint[1]] == $endpoint[0]) {
                        $status = true;
                    }
                }
            }
        }

        return $status;
    }
   
    public function index()
    {
        $data = $this->model->{$this->table}->index();
        $this->response(GET, $data);
    }
    public function show($value)
    {
        $data = $this->model->{$this->table}->show($value);
        $this->response(GET, $data);
    }
    public function pagination($limit, $page)
    {
        $data = $this->model->{$this->table}->pagination($limit, $page);
        $this->response(GET, $data);
    }
    public function paginationpost()
    {
        $result = $this->db
            ->table($this->table)
            ->select('*');
        $post = $this->post();
        if (isset($post['from'])) {
            $result->where('DATE(created_at) <=', date('Y-m-d', strtotime($post['from'])));
        }
        if (isset($post['to'])) {
            $result->where('DATE(created_at) <=', date('Y-m-d', strtotime($post['to'])));
        }
        if (isset($post['where']) && isset($post['set'])) {
            $sets = explode(",", $post['set']);
            $wheres = explode(",", $post['where']);
            foreach ($sets as $k => $b) {
                $result->where($wheres[$k], strval($b));
            }
        }
        if (isset($post['groupby'])) {
            $result->groupBy($post['groupby']);
        }
        if (isset($post['orderby']) && isset($post['sort'])) {
            $result->orderBy($post['orderby'], $post['sort']);
        }
        $result = $result->countAllResults();
        $pagination['total_data']        = $result;
        $pagination['total_pages']  = ceil($result / intval($post['limit']));
        $pagination['curr_page'] = intval($post['page']);
        $pagination['next_page']    = ($post['page'] >= $pagination['total_pages']) ? $pagination['total_pages'] : $post['page'] + 1;
        if ($post['page'] > 1) {
            $pagination['prev_page'] = $post['page'] - 1;
        }
        $pagination['data_per_page'] = intval($post['limit']);
        $offset = ($post['page'] - 1)  * $post['limit'];
        $pg = $this->db
            ->table($this->table)
            ->select('*')
            ->limit($post['limit'], $offset);
        if (isset($post['from'])) {
            $pg->where('DATE(created_at) >=', date('Y-m-d', strtotime($post['from'])));
        }

        if (isset($post['to'])) {
            $pg->where('DATE(created_at) <=', date('Y-m-d', strtotime($post['to'])));
        }
        if (isset($post['where']) && isset($post['set'])) {
            $sets = explode(",", $post['set']);
            $wheres = explode(",", $post['where']);
            foreach ($sets as $k => $b) {
                $pg->where($wheres[$k], strval($b));
            }
        }
        if (isset($post['groupby'])) {
            $pg->groupBy($post['groupby']);
        }
        if (isset($post['orderby']) && isset($post['sort'])) {
            $pg->orderBy($post['orderby'], $post['sort']);
        }
        $pagination['data'] = $pg->get()->getResult();
        $this->response(GET, $pagination);
    }
    public function paginationbyDate($limit, $page, $from, $to)
    {
        $data = $this->model->{$this->table}->paginationByDate($limit, $page, $from, $to);
        $this->response(GET, $data);
    }
    public function showBy($columnName, $value)
    {
        $data = $this->model->{$this->table}->showBy($columnName, $value);
        $this->response(GET, $data);
    }
    public function allBy($columnName, $value)
    {
        $data = $this->model->{$this->table}->allBy($columnName, $value);
        $this->response(GET, $data);
    }
    public function allByPost()
    {
        $data = $this->model->{$this->table}->allByAnd(postArray());
        $this->response(GET, $data);
    }
    public function showByPost()
    {
        $data = $this->model->{$this->table}->findByAnd(postArray());
        $this->response(GET, $data);
    }
    public function create()
    {
        $data = $this->request->getPost();
        $result = $this->model->{$this->table}->insert($data);
        $this->response(INSERT, $result);
    }
    public function update($id)
    {
        $data = $this->request->getPost();
        $result = $this->model->{$this->table}->update($data, $id);
        $this->response(UPDATE, $result);
    }
    public function destroy($id)
    {
        $data = $this->model->{$this->table}->destroy($id);
        $this->response(DELETE, $data);
    }
    /**
     * setPrefix
     *
     * @return void
     */
    // public function createImage($path, $file_name, $post): string
    // {
    //     if (!is_dir(ROOTPATH .  $path . '/assets' . '/')) {
    //         mkdir(ROOTPATH .  $path . '/assets' . '/', 0775, TRUE);
    //     }
    //     if ($imagefile = $this->request->getFiles()) {
    //         foreach ($imagefile['file'] as $img) {
    //             if ($img->isValid() && !$img->hasMoved()) {
    //                 $newName = $img->getRandomName();
    //                 $img->getTempName();
    //                 $now = date('YmdHis');
    //                 $fileName =  md5($now) . '.webp';
    //                 $destination = ROOTPATH . $path . '/assets' . '/' . $fileName;
    //                 $options = [];
    //                 WebPConvert::convert($img->getTempName(), $destination, $options);
    //                 $img->move(WRITEPATH . 'uploads', $newName);
    //             }
    //         }
    //     }
    // }


    /**
     * createSingleImage
     * @param  string $path
     * 
     * Content-Disposition: form-data; 
     * 
     * name="file";
     * 
     * filename="/C:/Users/Test/OneDrive/Gambar/Rol Kamera/WIN_20230911_15_57_04_Pro.jpg"
     *  
     */
    public function createImage(string $path)
    {
        $allowedExt = ['jpg', 'png', 'jpeg'];
        $imagefile = $this->request->getFiles();
        if (is_array($_FILES['file']['name'])) {
            foreach ($imagefile['file'] as $v => $img) {
                if ($img->isValid() && !$img->hasMoved()) {
                    if (!in_array($img->guessExtension(), $allowedExt)) {
                        $this->response(GET, 'NOT_ACCEPTABLE');
                    }
                    if (!is_dir(PATH_IMAGES_SERVER .  $path . '/')) {
                        mkdir(PATH_IMAGES_SERVER .  $path . '/', 0775, TRUE);
                    }
                    $fileName =  Uuid::uuid6()->toString() . '.webp';
                    $destination = PATH_IMAGES_SERVER . $path . '/' . $fileName;
                    $uri = PATH_IMAGES . $path . '/' . $fileName;
                    $options = [];
                    WebPConvert::convert($img->getTempName(), $destination, $options);
                    $this->destination[$v] = DhivaAES::base64url_encode($uri);
                } else {
                    $this->response(GET, $img->getErrorString());
                }
            }
        } else {
            $img = $this->request->getFile('file');
            if ($img->isValid() && !$img->hasMoved()) {
                if (!in_array($img->guessExtension(), $allowedExt)) {
                    $this->response(GET, 'NOT_ACCEPTABLE');
                }
                if (!is_dir(PATH_IMAGES_SERVER .  $path . '/')) {
                    mkdir(PATH_IMAGES_SERVER .  $path . '/', 0775, TRUE);
                }
                $fileName =  Uuid::uuid6()->toString() . '.webp';
                $destination = PATH_IMAGES_SERVER . $path . '/' . $fileName;
                $uri = PATH_IMAGES . $path . '/' . $fileName;
                $options = [];
                WebPConvert::convert($img->getTempName(), $destination, $options);
                $this->destination = DhivaAES::base64url_encode($uri);
            } else {
                $this->response(GET, $img->getErrorString());
            }
        }
        return $this->destination;
    }
    public function cekToken()
    {
        $this->response(GET, 'active');
    }

    public function createPDF(string $path)
    {
        $allowedExt = ['pdf', 'PDF'];

        if (is_array($_FILES['file']['name'])) {
            $pdfs = $this->request->getFiles();
            $des = [];
            foreach ($pdfs['file'] as $i => $pdf) {
                if ($pdf->isValid() && !$pdf->hasMoved()) {
                    if (!in_array($pdf->guessExtension(), $allowedExt)) {
                        $this->response(GET, 'NOT_ACCEPTABLE');
                    }
                    if (!is_dir(PATH_PDF_SERVER .  $path . '/')) {
                        mkdir(PATH_PDF_SERVER .  $path . '/', 0775, TRUE);
                    }
                    $fileName =  md5($pdf->getName()) . DhivaAES::aesencodeid(strval(intval(microtime(true) * 1000)), 5) . $i . '.pdf';
                    $destination = PATH_PDF_SERVER . $path;
                    $uri = PATH_PDF . $path . '/' . $fileName;
                    $pdf->move($destination, $fileName);
                    $des[$i] = DhivaAES::base64url_encode($uri);
                } else {
                    $this->response(GET, $pdf->getErrorString());
                }
            }
        } else {
            $pdf = $this->request->getFile('file');

            if ($pdf->isValid() && !$pdf->hasMoved()) {
                if (!in_array($pdf->guessExtension(), $allowedExt)) {
                    $this->response(GET, 'NOT_ACCEPTABLE');
                }
                if (!is_dir(PATH_PDF_SERVER .  $path . '/')) {
                    mkdir(PATH_PDF_SERVER .  $path . '/', 0775, TRUE);
                }
                $fileName =  md5($pdf->getName()) . DhivaAES::aesencodeid(strval(intval(microtime(true) * 1000)), 5) . rand(10, 1000) . '.pdf';
                $destination = PATH_PDF_SERVER . $path;
                $uri = PATH_PDF . $path . '/' . $fileName;
                $pdf->move($destination, $fileName);
                $des = DhivaAES::base64url_encode($uri);
            } else {
                $this->response(GET, $pdf->getErrorString());
            }
        }

        return $des;
    }
    protected function broadcast($url, $data)
    {
        $server   = 'mqtt.adhivasindo.com';
        $port     = 1883;
        $clientId = strval(rand(5, 15)) . DhivaAES::randomStr(7);
        $username = 'dhivapos';
        $password = 'FurlaRasaMelon2024';

        $mqtt = new \PhpMqtt\Client\MqttClient(
            $server,
            $port,
            $clientId,
            \PhpMqtt\Client\MqttClient::MQTT_3_1_1,
        );
        $connectionSettings = (new \PhpMqtt\Client\ConnectionSettings)
            ->setUsername($username)
            ->setPassword($password);
        $mqtt->connect($connectionSettings, false);
        $mqtt->publish('/mqtt/devSismenas/' . $url, json_encode($data), 0);
        $mqtt->disconnect();
    }
}
