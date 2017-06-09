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
        echo " __construct Controller Category <br/>";
        $this->M_Category = new M_Category();
    }

    function input(){}
    function output(){}

    public function index()
    {
        if(isset($_POST['name'])){
            if($this->isPost('save')){
                header("Location: /Category/");
                exit();
            }
        }

        $data['categories'] = $this->M_Category->getAll();
        $data['error'] = $this->M_Category->error_validation;
        $this->render($data);
    }

    function edit($id)
    {
        if(isset($_POST['name'])){
            if($this->isPost('save')){
                header("Location: /edit/" . $id);
                exit();
            }
        }
        $data['category'] = $this->M_Category->getById($id);
        $data['error'] = $this->M_Category->error_validation;

        if(empty($data['category'])){
            if(! empty($data['error'])){
                $data['error']['text'] = $data['error']['text'] . ' <br/> нет такой записи';
            } else {
                $data['error'] =  array(
                    'error' => true,
                    'text' => 'нет такой записи',
                );
            }
        }


        $this->render($data, 'Edit');
        $this->start();
    }

    /**
     * @param $id
     */
    function delete($id)
    {


        if(!empty($_POST) && $_POST['id'] != ''){
            if($this->isPost('delete')){
                header("Location: /Category/");
                exit();
            }
        }

        $data['category'] = $this->M_Category->getById($id);
        $data['error'] = $this->M_Category->error_validation;

        if(empty($data['category'])){
            if(! empty($data['error'])){
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