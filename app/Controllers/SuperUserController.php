<?php

namespace App\Controllers;

use Dhiva\Core\DhivaAES;

class SuperUserController extends BaseController
{
    private $dateNow;
    protected $table = 'super_user';
    public function __construct()
    {
        $this->dateNow = mdate('%Y-%m-%d %h:%i:%s', time());
    }
    public function auth()
    {
        $post = $this->post();
        $authResult = $this->model->super_user->auth($post['username'], md5($post['password']));
        if ($authResult) {
            $token     = md5($this->dateNow);
            $update['login_date'] = $this->dateNow;
            $update['access_at']  = $this->dateNow;
            $update['token']      = $token;
            $this->model->super_user->updateBy($update, $authResult->super_user_id, 'super_user_id');
            $authResult->token = $token;
            $this->response(GET, $this->getJwtToken($authResult));
        }
        $this->response(UNAUTHORIZED, 3);
    }
    public function logout()
    {
        $update['token'] = null;
        $this->model->super_user->updateBy($update, $this->userDatas->super_user_id, 'super_user_id');
        $this->response(GET, 'sukses');
    }
    protected function getJwtToken($userData)
    {
        $dataToken['timestamp'] = now();
        $dataToken['super_user_id'] = $userData->super_user_id;
        $dataToken['email'] = $userData->email;
        $dataToken['name'] = $userData->name;
        $dataToken['username'] = $userData->username;
        $dataToken['token'] = $userData->token;
        $dataToken['access_at'] = $userData->access_at;
        $dataToken['super_group_id'] = $userData->super_group_id;
        $output['Authorization'] = DhivaAES::generateToken($dataToken);
        $output['ClientSecret'] = DhivaAES::jwtencode($output['Authorization']);
        return $output;
    }
}
