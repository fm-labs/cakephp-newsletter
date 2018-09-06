<?php

namespace Newsletter\Controller\Admin;

use App\Controller\Admin\AppController as BaseController;

class AppController extends BaseController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Backend.Backend');
    }
}
