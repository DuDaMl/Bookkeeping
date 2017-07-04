<?php
namespace bookkeeping\Controllers;
use bookkeeping\Controllers\Controller as Controller;
use bookkeeping\Models\Income as M_Income;
use bookkeeping\Models\Category as M_Category;

class Income
    extends Controller
{
    protected static $main_teamplate = 'Income';
    private $M_Income;

    function __construct()
    {
        parent::__construct();
        $this->M_Income = new M_Income(static::$current_user_id);
    }

    function isPost($action)
    {
        if($this->M_Income->$action())
        {
            return true;
        } else {
            return false;
        }
    }

    function index()
    {
        if(!empty($_POST) && $_POST['category_id'] != '')
        {
            if($this->isPost('create'))
            {
                header("Location: /" . self::getMainTeamplate());
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $this->M_Income->error_validation;
        }

        // загрузка всех платежей текущего месяца
        $data['incomes'] = $this->M_Income->getAll();

        // загрузка всех категорий расходов
        $data['categories'] =  (new M_Category(static::$current_user_id))->getAll('Income');

        $this->render($data);
    }

    function edit($id)
    {
        if(!empty($_POST) && $_POST['category_id'] != '')
        {
            if($this->isPost('update')){
                header("Location: /" . self::getMainTeamplate());
                exit();
            }
        }

        $data['income'] = $this->M_Income->getById($id);
        $data['error'] = $this->M_Income->error_validation;

        if(empty($data['income']))
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

        $M_Category = new M_Category(static::$current_user_id);
        $data['categories'] = $M_Category->getAll('Income');
        $this->render($data, 'Edit');
    }

    /**
     * @param $id
     */
    function delete($id)
    {
        if(!empty($_POST) && $_POST['id'] != '')
        {
            if($this->isPost('delete'))
            {
                header("Location: /" . self::getMainTeamplate());
                exit();
            }
        }

        $data['income'] = $this->M_Income->getById($id);
        $data['error'] = $this->M_Income->error_validation;

        if(empty($data['income']))
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
    }
}