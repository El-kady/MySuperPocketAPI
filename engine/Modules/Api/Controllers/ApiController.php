<?php

namespace Modules\Api\Controllers;

use \Core\Service\Service;
use \Core\System\BaseController;


class ApiController extends BaseController
{
	
	public function __construct()
	{
  		parent::__construct();
        Service::getJson()->allowMethods();
	}


}
