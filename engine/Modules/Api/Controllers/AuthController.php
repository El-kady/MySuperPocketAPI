<?php

namespace Modules\Api\Controllers;

use \Core\Service\Service;
use \Modules\Api\Models\User;
use \Firebase\JWT\JWT;

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

        $this->user->register($data);

        Service::getJson()->render();
    }

    public function login()
    {
        $email = Service::getRequest()->post("email");
        $password = Service::getRequest()->post("password");

        if ($user = $this->user->login($email,$password)) {

            Service::getLogger()->info("User logged",[$email]);

            $tokenId    = base64_encode(mcrypt_create_iv(32));
            $issuedAt   = time();
            $notBefore  = $issuedAt - 10;
            $expire     = $notBefore + 60;
            $serverName = 'http://localhost/example';


            $data = [
                'iat'  => $issuedAt,         // Issued at: time when the token was generated
                'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
                'iss'  => $serverName,       // Issuer
                'nbf'  => $notBefore,        // Not before
                'exp'  => $expire,           // Expire
                'data' => [                  // Data related to the signer user
                    'user'   => $user
                ]
            ];

            $secretKey = Service::getConfig()->get("JWT_KEY");
            Service::getLogger()->info("secretKey",[$secretKey]);

            $jwt = JWT::encode(
                $data,
                $secretKey,
                'HS512'
            );


            Service::getLogger()->info("User JWT",[$jwt]);

            Service::getJson()->setData(array('token' => $jwt));
        }

        Service::getJson()->render();
    }

}
