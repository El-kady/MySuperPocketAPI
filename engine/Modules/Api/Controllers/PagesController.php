<?php

namespace Modules\Api\Controllers;

use \Core\Service\Service;
use Modules\Api\Models\Page;

class PagesController extends ApiController
{

    private $page;

    function __construct()
    {
        parent::__construct();
        $this->page = new Page();
    }

    public function index()
    {
        Service::getJson()->render();
    }

    public function view($slug)
    {
        if ($row = $this->page->getRow(strtolower($slug),'slug')) {
            Service::getJson()->setData($row);
        }else{
            Service::getSession()->add('feedback_negative', 'NOT_FOUND');
        }

        Service::getJson()->render();
    }

}
