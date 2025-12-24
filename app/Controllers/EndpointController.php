<?php

namespace App\Controllers;

use Dhiva\Core\DhivaAES;

class EndpointController extends BaseController
{
    protected $table = 'endpoint';
    public function encodeEndpoint()
    {
        $value = $this->request->getPost('value');
        $method = $this->request->getPost('method');
        $data = [];
        if ($value && $method) {
            $g = $this->model->endpoint
                ->findByAnd(
                    [
                        'value' => $value,
                        'method' => $method
                    ]
                );
            if ($g) {
                $data['description'] = DhivaAES::base64url_encode($value);
                $this->model->endpoint->updateBy($data, $value, 'value');
            } else {
                $data = [
                    'value' => $value,
                    'method' => $method,
                    'description' => DhivaAES::base64url_encode($value),
                    'type' => 'backend',
                    'bypass' => '0'
                ];
                $this->model->endpoint->insert($data);
            }
        }
        $this->response(GET, $data);
    }
    public function checkEncodeEndpoint()
    {
        if (ENVIRONMENT == 'development') {
            $e = (DhivaAES::base64url_decode($_POST['endpoint']));
            return $this->response(GET, $e);
        }
    }
}
