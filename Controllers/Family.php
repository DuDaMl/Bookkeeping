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

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Лобби запросов на добавление доступа к данным пользователя для другого пользователя
     */
    function index()
    {
        $M_Family = new M_Family();
        
        if(! empty($_POST))
        {
            $M_Family->sender_id = $this->user->getId();

            // Создание запроса.
            if($M_Family->create($_POST['email']))
            {
                header("Location: /" . self::getMainTeamplate());
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $M_Family->error_validation;
        }

        $data['incomig_request'] = $M_Family->getIncomeRequest($this->user->getId());
        $data['waiting_request'] = $M_Family->getSendedRequest($this->user->getId());
        $data['confirmed_request'] = $M_Family->getConfirmedRequest($this->user->getId());
        $data['user'] = $this->user->getId();
        $this->render($data);
    }

    /**
     * Подтверждение доступа к данных Получателем (receiver)
     * @param $id
     */
    function confirm($id)
    {
        $M_Family = new M_Family();
        // Проверка существования запроса с другой стороны

        if(!empty($_POST) && $_POST['relationship_id'] != '')
        {
            if($M_Family->confirmeRelationshiop($this->user->getId(), $_POST['relationship_id']))
            {
                header("Location: /" . self::getMainTeamplate());
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $M_Family->error_validation;
        }

        // Проверка существования записи
        $relationship = $M_Family->getWaitingRequestById($id, $this->user->getId());
        if($relationship == false)
        {
            $data['error'] = $M_Family->error_validation;
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
        $M_Family = new M_Family();

        // Проверка существования записи
        $relationship = $M_Family->getDeleteableRequestById($id, $this->user->getId())[0];

        if( !empty($_POST)
            && $_POST['relationship_id'] != ''
            && $relationship != false
            && $relationship->id != '')
        {
            // Check allow for delete
            if($relationship->receiver_id != $this->user->getId()
                && $relationship->sender_id != $this->user->getId()
            )
            {
                $data['error'] = array(
                    'error' => true,
                    'text' => 'Нет прав на удаление запроса',
                );
            } else {
                if($M_Family->deleteRelationshiop($_POST['relationship_id']))
                {
                    header("Location: /" . self::getMainTeamplate());
                    exit();
                } else {
                    // ошибки добавления новой записи расходов
                    $data['error'] = $M_Family->error_validation;
                }
            }
        }

        $data['relationship'] = $relationship;

        // get receiver_id as deleting user
        if($relationship->sender_id == $this->user->getId())
        {
            $user = M_User::getById($relationship->receiver_id)[0];
        } else {
            $user = M_User::getById($relationship->sender_id)[0];
        }


        if($relationship == false)
        {
            $data['error'] = $M_Family->error_validation;
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
        $M_Family = new M_Family();
        // Проверка существования записи
        $relationship = $M_Family->getIncomeRequestById($id, $this->user->getId())[0];

        if( !empty($_POST)
            && $_POST['relationship_id'] != ''
            && $relationship != false
            && $relationship->id != '')
        {
            if($M_Family->cancelRelationshiop($_POST['relationship_id']))
            {
                header("Location: /" . self::getMainTeamplate());
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $M_Family->error_validation;
        }

        if($relationship == false)
        {
            $data['error'] = $M_Family->error_validation;
        } else {
            // получение данных о получателе
            $data['relationship'] = $relationship;
        }

        $this->render($data, 'Cancel');
    }
}