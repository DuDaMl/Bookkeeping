<?php
namespace bookkeeping\Controllers;
use bookkeeping\Controllers\Controller as Controller;
use bookkeeping\Models\Pay as M_Pay;
use bookkeeping\Models\Category as M_Category;

class Pay
    extends Controller
{
    protected $main_teamplate = 'Pay';
    private $M_Pay;

    function __construct()
    {
        echo " __construct Controller Pay <br/>";
        $this->M_Pay = new M_Pay();
    }

    function isPost($action)
    {
        if($this->M_Pay->$action()){
            return true;
        } else {
            return false;
        }
    }

    function index()
    {
        if(!empty($_POST) && $_POST['category_id'] != ''){
            if($this->isPost('save')){
                header("Location: /");
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $this->M_Pay->error_validation;
        }

        // загрузка всех платежей текущего месяца
        $data['pays'] = $this->M_Pay->getAll();

        // загрузка всех категорий расходов
        $data['categories'] =  (new M_Category())->getAll();


        $this->render($data);
    }

    function edit($id)
    {
        if(!empty($_POST) && $_POST['category_id'] != ''){
            if($this->isPost('save')){
                header("Location: /");
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
    }

    /**
     * @param $id
     */
    function delete($id)
    {
        if(!empty($_POST) && $_POST['id'] != ''){
            if($this->isPost('delete')){
                header("Location: /");
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

        $this->render($data, 'Delete');
    }
}