<?php

namespace Core\Helpers;

use Core\Service\Service;

class Json
{
    private static $data = array();
    private static $code = 200;

    private $feedback_positive = array();
    private $feedback_negative = array();

    public static function allowMethods()
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }

    public static function setData($data)
    {
        self::$data = $data;
    }

    public static function setCode($code)
    {
        self::$code = $code;
    }

    public function render()
    {
        $this->feedback_positive = (array) Service::getSession()->get('feedback_positive');
        $this->feedback_negative = (array) Service::getSession()->get('feedback_negative');

        if (count($this->feedback_negative) > 0) {
            self::setCode(400);
        }

        if (count($this->feedback_positive) || count($this->feedback_negative)) {
            self::$data['messages'] = array_merge($this->feedback_positive,$this->feedback_negative);
        }

        Service::getSession()->set('feedback_positive', null);
        Service::getSession()->set('feedback_negative', null);

        if (version_compare(phpversion(), '5.4.0', '<')) {
            $json = json_encode(self::$data);
            $json = str_replace('\\/', '/', $json);
        } else {
            $json = json_encode(self::$data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        http_response_code(self::$code);
        header("Content-type:application/json; charset=UTF-8");
        echo $json;
    }

}