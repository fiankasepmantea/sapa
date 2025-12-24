<?php

namespace App\Controllers;

use Dhiva\Core\DhivaAES;
use CodeIgniter\RESTful\ResourceController;

class AssetsController extends ResourceController
{
    public function decodeImage($url)
    {

        $url = WRITEPATH . DhivaAES::base64url_decode($url);
        if (!file_exists($url)) {
            $this->notfound();
        }
        $this->response
            ->setStatusCode(200)
            ->setContentType('image/webp')
            ->setBody(file_get_contents($url))
            ->send();
    }
    private function notfound()
    {
        $response = [
            'status'   => false,
            'code' => HTTP_NOT_FOUND,
            'messages' => 'Not found'
        ];
        header('Content-Type: application/json');
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        die;
    }
}
