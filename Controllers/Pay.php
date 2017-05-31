<?php
namespace bookkeeping\Controllers;
use bookkeeping\Models\Pay as M_Pay;
use bookkeeping\Models\Category as M_Category;

class Pay
{
    const MAIN_TEAMPLATE = 'Main';
    private $output_data;
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

        $this->output_data['error'] = $this->M_Pay->error_validation;
    }

    function output()
    {

        $this->output_data['pays'] = $this->M_Pay->getAll();

        $M_Category = new M_Category();
        $this->output_data['categories'] = $M_Category->getAll();

        $this->output_data['error'] = $this->M_Pay->error_validation;
        //print_r($this->output_data['pays']);



        $this->getTeamplate($this->output_data);
    }

    function getTeamplate($data)
    {
        foreach ($data as $k => $v) {
            $$k = $v;
        }

        return include (__DIR__ . '\..\View\\' . self::MAIN_TEAMPLATE . '.php');
    }
}