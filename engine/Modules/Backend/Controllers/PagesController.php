<?php
namespace Modules\Backend\Controllers;

use \Core\Service\Service;
use \Modules\Backend\Models\Page;

class PagesController extends BackendController
{

    private $page;

    function __construct()
    {
        parent::__construct();
        $this->page = new Page();
    }

    public function index()
    {
        $rows = $this->page->getAll();
        Service::getView()
            ->setTitle(Service::getText()->get("PAGES"))
            ->render("pages/index", ["rows" => $rows]);
    }

    public function add()
    {

        Service::getView()
            ->setTitle(Service::getText()->get("ADD"))
            ->render("pages/form", []);
    }

    public function edit($id)
    {
        $row = $this->page->getRow((int)$id);
        Service::getForm()->fillData('page', $row);

        Service::getView()
            ->setTitle(Service::getText()->get("EDIT"))
            ->render("pages/form", ["id" => $row["id"]]);
    }

    public function save($id = 0)
    {
        $data = [
            "slug" => Service::getRequest()->post("slug"),
            "title" => Service::getRequest()->post("title"),
            "content" => Service::getRequest()->post("content",false)
        ];

        if ($this->page->saveData($data, (int)$id)) {
            Service::getRedirect()->to("/backend/pages");
        } else {
            Service::getForm()->fillTmp('page', $data);
            Service::getRedirect()->absolute(Service::getRequest()->post("back"));
        }
    }

    public function delete($id)
    {
        if (Service::getRequest()->post("delete")) {
            $this->page->delete("id = :id ", [":id" => (int)$id]);
        }
        Service::getRedirect()->to("/backend/pages");
    }
}
