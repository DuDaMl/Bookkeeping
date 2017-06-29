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

        $incoming = $this->M_Family->getIncomeRequest($this->user->id);
        $request = $this->M_Family->getSendedRequest($this->user->id);
        $confirmed = $this->M_Family->getConfirmedRequest($this->user->id);

        $data['user'] = $this->user;
        $data['incomig_request'] = $incoming;
        $data['waiting_request'] = $request;
        $data['confirmed_request'] = $confirmed;

        $this->render($data);
    }

    /**
     * Подтверждение доступа к данных Получателем (receiver)
     * @param $id
     */
    function confirm($id)
    {

        // Проверка существования запроса с другой стороны

        if(!empty($_POST) && $_POST['relationship_id'] != '')
        {
            if($this->M_Family->confirmeRelationshiop($this->user->id, $_POST['relationship_id']))
            {
                header("Location: /" . self::getMainTeamplate());
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $this->M_Family->error_validation;
        }

        // Проверка существования записи
        $relationship = $this->M_Family->getWaitingRequestById($id, $this->user->id);
        if($relationship == false)
        {
            $data['error'] = $this->M_Family->error_validation;
        } else {
            // получение данных о получателе
            $data['relationship'] = $relationship;
        }

        $this->render($data, 'Confirme');
    }

    /**
     * Удаление своих запросов, где sender = user->id
     * @param $id
     */
    function delete($id)
    {
        // Проверка существования записи
        $relationship = $this->M_Family->getDeleteableRequestById($id, $this->user->id);

        if( !empty($_POST)
            && $_POST['relationship_id'] != ''
            && $relationship != false
            && $relationship->id != '')
        {
            if($this->M_Family->deleteRelationshiop($_POST['relationship_id']))
            {
                header("Location: /" . self::getMainTeamplate());
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $this->M_Family->error_validation;
        }

        if($relationship == false)
        {
            $data['error'] = $this->M_Family->error_validation;
        } else {
            // получение данных о получателе
            $data['relationship'] = $relationship;
        }

        $this->render($data, 'Delete');
    }

    /**
     * Отмена запросов от других пользователей
     * @param $id
     */
    function cancel($id)
    {
        // Проверка существования записи
        $relationship = $this->M_Family->getIncomeRequestById($id, $this->user->id);

        if( !empty($_POST)
            && $_POST['relationship_id'] != ''
            && $relationship != false
            && $relationship->id != '')
        {
            if($this->M_Family->cancelRelationshiop($_POST['relationship_id']))
            {
                header("Location: /" . self::getMainTeamplate());
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $this->M_Family->error_validation;
        }

        if($relationship == false)
        {
            $data['error'] = $this->M_Family->error_validation;
        } else {
            // получение данных о получателе
            $data['relationship'] = $relationship;
        }

        $this->render($data, 'Cancel');
    }
}