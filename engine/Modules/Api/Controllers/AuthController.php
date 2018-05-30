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
            "email" => Service::getRequest()->post("email"),
            "phone_number" => Service::getRequest()->post("phone_number"),
            "password" => Service::getRequest()->post("password"),
        ];

        if ($this->user->register($data) && $user = $this->user->getRow($data["email"], "email")) {
            if ($jwt = $this->user->getJWT($user)) {
                Service::getJson()->setData(array('user' => $user,'token' => $jwt));
            }
        }

        Service::getJson()->render();
    }

    public function login()
    {
        $email = Service::getRequest()->post("email");
        $password = Service::getRequest()->post("password");

        if ($user = $this->user->login($email, $password)) {
            if ($jwt = $this->user->getJWT($user)) {
                Service::getJson()->setData(array('user' => $user,'token' => $jwt));
            }
        }

        Service::getJson()->render();
    }

}
