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
        $this->M_Family = new M_Family();
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

    /**
     * Лобби запросов на добавление расходов/категорий/ другого пользователя
     */
    function index()
    {
        if(!empty($_POST))
        {
            if($this->M_Family->create($this->user->id, $_POST['email']))
            {
                header("Location: /" . self::getMainTeamplate());
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $this->M_Family->error_validation;
        }
        $request = $this->M_Family->getAllSendedRequestBySenderIdWithReseiverEmail($this->user->id);

        if($request == false)
        {
            $data['error'] = $this->M_Family->error_validation;
        } else {
            $data['waiting_request'] = $request;
        }

        print_r($request);

        $this->render($data);
    }

    /**
     * Подтверждение доступа к данных
     * @param $id
     */
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
     * Удаление своих запросов
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

    /**
     * Отмена запросов от других пользователей
     * @param $id
     */
    function cancel($id)
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