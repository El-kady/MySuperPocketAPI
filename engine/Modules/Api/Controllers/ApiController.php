<?php

namespace Modules\Api\Controllers;

use \Core\Service\Service;
use \Core\System\BaseController;
use \Firebase\JWT\JWT;


class ApiController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        Service::getJson()->allowMethods();

        if (false == in_array($this->name, array("AuthController","PagesController"))) {
            $authorization = Service::getRequest()->getHeader("Authorization");
            if (false == empty($authorization)) {
                list($jwt) = sscanf($authorization, 'Bearer %s');
                if ($jwt) {
                    Service::getLogger()->info("JWT", array($jwt, Service::getConfig()->get("JWT_KEY")));

                    try {
                        $token = Token::validate($jwt, Service::getConfig()->get("JWT_KEY"));
                    } catch (Exception $e) {
                        exit($e->getMessage());
                        header('HTTP/1.0 401 Unauthorized');
                    }

                } else {
                    header('HTTP/1.0 400 Bad Request');
                }
            } else {
                header('HTTP/1.0 400 Bad Request');
                exit('Token not found in this request');
            }
        }

    }


}
