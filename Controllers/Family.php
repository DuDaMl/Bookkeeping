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
            // Создание запроса.
            if($this->M_Family->create($this->user->id, $_POST['email']))
            {
                header("Location: /" . self::getMainTeamplate());
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $this->M_Family->error_validation;
        }

        $request = $this->M_Family->getRequestByStatus($this->user->id, M_Family::WAITING);
        $confirmed = $this->M_Family->getRequestByStatus($this->user->id, M_Family::CONFIRMED);

        $data['waiting_request'] = $request;
        $data['confirmed_request'] = $confirmed;

        $this->render($data);
    }

    /**
     * Подтверждение доступа к данных
     * @param $id
     */
    function confirm($id)
    {
        if(!empty($_POST) && $_POST['relationship_id'] != '')
        {
            if($this->M_Family->confirmeRelationshiop($_POST['relationship_id']))
            {
                header("Location: /" . self::getMainTeamplate());
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $this->M_Family->error_validation;
        }

        // Проверка существования записи
        $relationship = $this->M_Family->getRelationshipById($id);

        if($relationship == false)
        {
            $data['error'] = $this->M_Family->error_validation;
        } else {
            // получение данных о получателе
            $data['receiver'] = $this->M_Family->getReceiverEmailById($relationship->receiver_id);
            $data['relationship'] = $relationship;
        }

        $this->render($data, 'Confirme');
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