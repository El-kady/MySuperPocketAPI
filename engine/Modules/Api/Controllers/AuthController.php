<?php

namespace Modules\Api\Controllers;

use \Core\Service\Service;

use \Modules\Api\Models\User;

class AuthController extends ApiController
{
    private $user;

    function __construct()
    {
        parent::__construct();
        $this->user = new User();
    }

    public function index()
    {

    }

    public function register()
    {
        $data = [

            "name" => Service::getRequest()->post("name"),
            "phone_number" => Service::getRequest()->post("email"),
            "password" => Service::getRequest()->post("password"),
        ];
var_dump($_REQUEST);

        $this->user->register($data);

        Service::getJson()->render();
    }

}
