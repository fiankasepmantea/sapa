<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class DevController extends ResourceController
{
    public function sukses($message = NULL)
    {
        if ($message) {
            $response = [
                'status'   => true,
                'messages' => $message
            ];
        } else {
            $response = [
                'status'   => true,
                'messages' => 'success'
            ];
        }

        return $this->respond($response, 200);
    }
}
