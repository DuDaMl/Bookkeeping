<?php
namespace bookkeeping\Controllers;
use bookkeeping\Controllers\Controller as Controller;
use bookkeeping\Models\Setting as M_Setting;
use bookkeeping\Models\Family as M_Family;
use bookkeeping\Models\Category as M_Category;

class Family
    extends Controller
{
    protected static $main_teamplate = 'Family';
    private $M_Family;

    function __construct()
    {
        parent::__construct();
        //$this->M_Family = new M_Family($this->user->id);
        //$this->M_Setting = new M_Setting($this->user->id);
        //$this->setting();
    }

    function isPost($action)
    {
        if($this->M_Family->$action())
        {
            return true;
        } else {
            return false;
        }
    }

    function index()
    {
        if(!empty($_POST))
        {
            if($this->isPost('create'))
            {
                header("Location: /" . $this->main_teamplate);
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $this->M_Family->error_validation;
        }

        $data = array(
            1 => 1
        );

        $this->render($data);
    }

    function confirm($id)
    {
        if(!empty($_POST) && $_POST['category_id'] != ''){
            if($this->isPost('update')){
                header("Location: /" . $this->main_teamplate);
                exit();
            }
        }

        $data = array(
            1 => 1
        );

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
                header("Location: /" . $this->main_teamplate);
                exit();
            }
        }

        $data = array(
            1 => 1
        );

        $this->render($data, 'Delete');
    }
}