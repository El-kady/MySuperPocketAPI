<?php

namespace Modules\Api\Controllers;

use \Core\Service\Service;

class HomeController extends ApiController
{


    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        Service::getJson()->render();
    }

}
