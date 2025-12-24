<?php

namespace App\Controllers;

/**
* Model Load
* $this->model->application;
*/

class ApplicationController extends BaseController
{
    protected $table = 'application';

    public function index()
    {
        $data = $this->model->{$this->table}->indexApplication();
        $this->response(GET, $data);
    }
}
