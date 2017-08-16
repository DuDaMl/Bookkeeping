<?php
namespace bookkeeping\Controller;
use bookkeeping\Controller\Controller as Controller;
use bookkeeping\Model\Setting as M_Setting;
use bookkeeping\Model\User;
use bookkeeping\Model\Family as M_Family;
use bookkeeping\Model\Category as M_Category;
use bookkeeping\Model\Views\View as M_View;

class Family
    extends Controller
{
    const CONTROLLER_NAME = 'Family';

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

            $M_Family->sender_id = User::getId();



            // Создание запроса.
            if($M_Family->create($_POST['email']))
            {
                header("Location: /" . static::CONTROLLER_NAME);
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $M_Family->error_validation;
        }

        // Создание объекта представления для контроллера
        $M_View = M_View::getInstance(static::CONTROLLER_NAME);
        $M_View->user = $this->user;
        $M_View->index();

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
            if($M_Family->confirmeRelationshiop(User::getId(), $_POST['relationship_id']))
            {
                header("Location: /" . static::CONTROLLER_NAME);
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $M_Family->error_validation;
        }

        // Проверка существования записи
        $relationship = $M_Family->getWaitingRequestById($id, User::getId());

        if($relationship == false)
        {
            $data['error'] = $M_Family->error_validation;
        } else {
            // получение данных о получателе
            $data['relationship'] = $relationship;
        }


        // Создание объекта представления для контроллера
        $M_View = M_View::getInstance(static::CONTROLLER_NAME);
        $M_View->user = $this->user;
        $M_View->relationship = $relationship;
        $M_View->confirm();

    }

    /**
     * Удаление своих запросов, где sender = user->id
     * @param $id
     */
    function delete($id)
    {
        $M_Family = new M_Family();

        // Проверка существования записи
        $relationship = $M_Family->getDeleteableRequestById($id, User::getId())[0];

        if( !empty($_POST)
            && $_POST['relationship_id'] != ''
            && $relationship != false
            && $relationship->id != '')
        {
            // Check allow for delete
            if($relationship->receiver_id != User::getId()
                && $relationship->sender_id != User::getId()
            )
            {
                $data['error'] = array(
                    'error' => true,
                    'text' => 'Нет прав на удаление запроса',
                );
            } else {
                if($M_Family->deleteRelationshiop($_POST['relationship_id']))
                {
                    header("Location: /" . static::CONTROLLER_NAME);
                    exit();
                } else {
                    // ошибки добавления новой записи расходов
                    $data['error'] = $M_Family->error_validation;
                }
            }
        }

        $data['relationship'] = $relationship;

        // get receiver_id as deleting user
        if($relationship->sender_id == User::getId())
        {
            $user = User::getById($relationship->receiver_id)[0];
        } else {
            $user = User::getById($relationship->sender_id)[0];
        }


        if($relationship == false)
        {
            $data['error'] = $M_Family->error_validation;
        } else {
            // получение данных о получателе
            $data['deleting_user'] = $user;
        }

        // Создание объекта представления для контроллера
        $M_View = M_View::getInstance(static::CONTROLLER_NAME);
        $M_View->user = $this->user;
        $M_View->deleting_user = $user;
        $M_View->relationship = $relationship;
        $M_View->delete();

    }

    /**
     * Отмена запросов от других пользователей
     * @param $id
     */
    function cancel($id)
    {
        $M_Family = new M_Family();
        // Проверка существования записи
        $relationship = $M_Family->getIncomeRequestById($id, User::getId())[0];

        if( !empty($_POST)
            && $_POST['relationship_id'] != ''
            && $relationship != false
            && $relationship->id != '')
        {
            if($M_Family->cancelRelationshiop($_POST['relationship_id']))
            {
                header("Location: /" . static::CONTROLLER_NAME);
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
            //$data['relationship'] = $relationship;
        }


        // Создание объекта представления для контроллера
        $M_View = M_View::getInstance(static::CONTROLLER_NAME);
        $M_View->user = $this->user;
        $M_View->relationship = $relationship;
        $M_View->cancel();

        $data['controller_name'] = static::CONTROLLER_NAME;

    }
}