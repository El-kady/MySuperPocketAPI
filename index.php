<?php

define('DS',DIRECTORY_SEPARATOR);
define('ROOT', __DIR__ . DS);
define('PATH_APP', ROOT . 'engine' . DS);

require PATH_APP . 'autoload.php';
$autoload = new Autoload();
$autoload->register();

require ROOT.'vendor/autoload.php';

use Bootstrap\App;

$app = new App();

