<?php

namespace Modules\Frontend\Controllers;

use \Core\Service\Service;

class HomeController extends FrontendController
{


    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        Service::getView()->setTitle(Service::getConfig()->get("site_name"))->render("index",[]);
    }

}
