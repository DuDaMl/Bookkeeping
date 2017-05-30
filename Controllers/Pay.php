<?php
namespace bookkeeping\Controllers;
use bookkeeping\Models\Pay as M_Pay;
use bookkeeping\Models\Category as M_Category;

class Pay
{
    const MAIN_TEAMPLATE = 'Main';

    function __construct()
    {
        $this->input();
        $this->output();
    }

    function input()
    {
        if(!empty($_POST)){
           $this->isPost();
        }

        /*
        */

    }
    function isPost()
    {
        $M_Pay = new M_Pay();
       /* $M_Pay->date = $_POST['date'];
        $M_Pay->amount = $_POST['amount'];
        $M_Pay->category_id = $_POST['category_id'];
        $M_Pay->description = $_POST['description'];*/
        $M_Pay->save();

    }

    function output()
    {
        $M_Pay = new M_Pay();
        $data['pays'] = $M_Pay->getAll();

        $M_Category = new M_Category();
        $data['categories'] = $M_Category->getAll();

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