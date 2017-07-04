<?php
namespace bookkeeping\Controllers;
use bookkeeping\Controllers\Controller as Controller;
use bookkeeping\Models\Setting as M_Setting;
use bookkeeping\Models\User as M_User;
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
            if($this->M_Family->create(static::$current_user_id, $_POST['email']))
            {
                header("Location: /" . self::getMainTeamplate());
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $this->M_Family->error_validation;
        }

        $incoming = $this->M_Family->getIncomeRequest(static::$current_user_id);
        $request = $this->M_Family->getSendedRequest(static::$current_user_id);
        $confirmed = $this->M_Family->getConfirmedRequest(static::$current_user_id);

        $data['user'] = static::$current_user_id;
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
            if($this->M_Family->confirmeRelationshiop(static::$current_user_id, $_POST['relationship_id']))
            {
                header("Location: /" . self::getMainTeamplate());
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $this->M_Family->error_validation;
        }

        // Проверка существования записи
        $relationship = $this->M_Family->getWaitingRequestById($id, static::$current_user_id);
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
        $relationship = $this->M_Family->getDeleteableRequestById($id, static::$current_user_id);

        if( !empty($_POST)
            && $_POST['relationship_id'] != ''
            && $relationship != false
            && $relationship->id != '')
        {
            // Check allow for delete
            if($relationship->receiver_id != self::$current_user_id
                && $relationship->sender_id != self::$current_user_id
            )
            {
                $data['error'] = array(
                    'error' => true,
                    'text' => 'Нет прав на удаление запроса',
                );
            } else {
                if($this->M_Family->deleteRelationshiop($_POST['relationship_id']))
                {
                    header("Location: /" . self::getMainTeamplate());
                    exit();
                } else {
                    // ошибки добавления новой записи расходов
                    $data['error'] = $this->M_Family->error_validation;
                }
            }
        }

        $data['relationship'] = $relationship;

        // get receiver_id as deleting user
        if($relationship->sender_id == self::$current_user_id)
        {
            $user = M_User::getById($relationship->receiver_id);
        } else {
            $user = M_User::getById($relationship->sender_id);
        }


        if($relationship == false)
        {
            $data['error'] = $this->M_Family->error_validation;
        } else {
            // получение данных о получателе
            $data['deleting_user'] = $user;
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
        $relationship = $this->M_Family->getIncomeRequestById($id, static::$current_user_id);

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