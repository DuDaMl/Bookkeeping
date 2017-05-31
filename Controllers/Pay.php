<?php
namespace bookkeeping\Controllers;
use bookkeeping\Models\Pay as M_Pay;
use bookkeeping\Models\Category as M_Category;

class Pay
{
    const MAIN_TEAMPLATE = 'Main';
    private $M_Pay;

    function __construct()
    {
        $this->M_Pay = new M_Pay();
        $this->input();
        $this->output();
    }

    function input()
    {
        if(!empty($_POST) && $_POST['category_id'] != ''){
           $this->isPost();
        }
    }
    function isPost()
    {
        if($this->M_Pay->save()){
            header("Location: /");
            exit();
        }
    }

    function output()
    {

        $data['pays'] = $this->M_Pay->getAll();
        $M_Category = new M_Category();
        $data['categories'] = $M_Category->getAll();
        $data['error'] = $this->M_Pay->error_validation;

        $this->getTeamplate($data);
    }

    function getTeamplate($data)
    {
        foreach ($data as $k => $v) {
            $$k = $v;
        }

        return include (__DIR__ . '\..\View\\' . self::MAIN_TEAMPLATE . '.php');
    }
}