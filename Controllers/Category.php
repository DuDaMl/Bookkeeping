<?php
namespace bookkeeping\Controllers;
use bookkeeping\Controllers\Controller as Controller;
use bookkeeping\Models\Pay as M_Pay;
use bookkeeping\Models\Category as M_Category;


class Category
    extends Controller
{
    protected $main_teamplate = 'Category';
    private $M_Category;

    public function __construct()
    {
        parent::__construct();
        $this->M_Category = new M_Category();
    }

    function input(){}
    function output(){}

    public function index()
    {
        if(isset($_POST['name']))
        {
            if($this->isPost('save'))
            {
                header("Location: /Category/");
                exit();
            }
        }

        $data['categories_pays'] = $this->M_Category->getAllPays();
        $data['categories_incomes'] = $this->M_Category->getAllIncomes();
        $data['error'] = $this->M_Category->error_validation;
        $this->render($data);
    }

    function edit($id)
    {
        if(isset($_POST['name']))
        {
            if($this->isPost('save'))
            {
                header("Location: /edit/" . $id);
                exit();
            }
            $data['error'] = $this->M_Category->error_validation;
        }

        $data['category'] = $this->M_Category->getById($id);

        //print_r($data['category']);

        if(empty($data['category']))
        {
            if(! empty($data['error']))
            {
                $data['error']['text'] = $data['error']['text'] . ' <br/> нет такой записи';
            } else {
                $data['error'] =  array(
                    'error' => true,
                    'text' => 'нет такой записи',
                );
            }
        }

        $this->render($data, 'Edit');
    }

    /**
     * @param $id
     */
    function delete($id)
    {
        if(!empty($_POST) && $_POST['id'] != '')
        {
            if($this->isPost('delete')){
                header("Location: /Category/");
                exit();
            }
        }

        $data['category'] = $this->M_Category->getById($id);
        $data['error'] = $this->M_Category->error_validation;

        if(empty($data['category']))
        {
            if(! empty($data['error']))
            {
                $data['error']['text'] = $data['error']['text'] . ' <br/> нет такой записи';
            } else {
                $data['error'] =  array(
                    'error' => true,
                    'text' => 'нет такой записи',
                );
            }
        }

        $this->render($data, 'Delete');
        $this->start();
    }

    function start()
    {
        $this->input();
        $this->output();
    }

    function isPost($action)
    {
        if($this->M_Category->$action()){
            return true;
        } else {
            return false;
        }
    }


}