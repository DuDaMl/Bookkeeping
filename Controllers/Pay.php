<?php
namespace bookkeeping\Controllers;
use bookkeeping\Models\Pay as M_Pay;
use bookkeeping\Models\Category as M_Category;

class Pay
{
    const MAIN_TEAMPLATE = 'Pay';
    private $M_Pay;

    function __construct()
    {
        echo " __construct Controller Pay <br/>";
        $this->M_Pay = new M_Pay();

    }

    function start()
    {
        $this->input();
        $this->output();
    }


    function input()
    {

    }
    function isPost()
    {
        if($this->M_Pay->save()){
           return true;
        } else {
            return false;
        }
    }

    function output()
    {

    }

    function index()
    {
        if(!empty($_POST) && $_POST['category_id'] != ''){
            if($this->isPost()){
                header("Location: /");
                exit();
            }
        }

        $data['pays'] = $this->M_Pay->getAll();
        $M_Category = new M_Category();
        $data['categories'] = $M_Category->getAll();
        $data['error'] = $this->M_Pay->error_validation;
        $this->render($data);
    }

    function edit($id)
    {

        if(!empty($_POST) && $_POST['category_id'] != ''){
            if($this->isPost()){
                header("Location: /edit/" . $id);
                exit();
            }
        }

        $data['pay'] = $this->M_Pay->getById($id);
        $data['error'] = $this->M_Pay->error_validation;

        if(empty($data['pay'])){
            if(! empty($data['error'])){
                $data['error']['text'] = $data['error']['text'] . ' <br/> нет такой записи';
            } else {
                $data['error'] =  array(
                    'error' => true,
                    'text' => 'нет такой записи',
                );
            }
        }

        $M_Category = new M_Category();
        $data['categories'] = $M_Category->getAll();
        $this->render($data, 'Edit');
        $this->start();
    }

    function render($data, $view = 'Index')
    {
        foreach ($data as $k => $v) {
            $$k = $v;
        }

        return include (__DIR__ . '\..\View\\' . self::MAIN_TEAMPLATE . '\\' . $view . '.php');
    }
}