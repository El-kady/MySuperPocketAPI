<?php

define('ENVIRONMENT', 'development');

if (ENVIRONMENT == 'development' || ENVIRONMENT == 'dev') {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}

ini_set('session.cookie_httponly', 1);

$isSecureRequest = ((isset ($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') || $_SERVER['SERVER_PORT'] == 443);
$url_scheme=  $isSecureRequest ? 'https://' : 'http://';

$config = array(
    'URL' => $url_scheme . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']),

    'DB_TYPE' => 'mysql',
    'DB_PORT' => '3306',
    'DB_CHARSET' => 'utf8',

    'PATH_ROOT' => ROOT,
    'PATH_UPLOADS' => ROOT . 'uploads' . DS
);

if ($_SERVER["HTTP_HOST"] == "localhost") {
    $config["DB_HOST"] = "localhost";
    $config["DB_NAME"] = "mysuperpocket";
    $config["DB_USER"] = "root";
    $config["DB_PASS"] = "123456";
}else{
    $config["DB_HOST"] = "us-cdbr-iron-east-05.cleardb.net";
    $config["DB_NAME"] = "heroku_bc200a18482910d";
    $config["DB_USER"] = "b83c64345611f9";
    $config["DB_PASS"] = "74fe063e";
}

$config["JWT_KEY"] = 'Thi$ secret is not secret 123 :)!!!';

return $config;

