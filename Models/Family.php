<?php
namespace bookkeeping\Models;
use \DateTime;
use \PDO;
use bookkeeping\Models\User as M_Usaer;

class Family
{
    const TABLE_NAME = 'family';
    protected $DB;
    public $id;
    public $sender_id;
    public $receiver_id	;
    public $status;
    public $date;

    private $M_User;


    // Статусы для операций
    // запрос отправлен
    const WAITING = 0;

    // запрос подтвержден
    const CONFIRMED = 1;

    // запрос отказан
    const REFUSED = 2;

    public $error_validation;

    function __construct()
    {
        $this->DB = DB::getInstance();
        $this->M_User = new User();
    }


    public function getSendedRequest($sender_id)
    {
        $sql = "SELECT family.id, family.date, user.family_name, user.given_name, user.picture 
        FROM `family` 
        LEFT JOIN `user`
        ON user.id = family.receiver_id
        WHERE family.sender_id = " . $sender_id . " 
        AND family.status = " . Family::WAITING ;
        $result = $this->DB->query($sql);
        return $result;
    }

    public function getIncomeRequest($receiver_id)
    {
        $sql = "SELECT family.id, family.date, user.family_name, user.given_name, user.picture 
        FROM `family` 
        LEFT JOIN `user`
        ON user.id = family.sender_id
        WHERE family.receiver_id = " . $receiver_id . " 
        AND family.status = " . Family::WAITING ;
        $result = $this->DB->query($sql);
        return $result;
    }

    public function getIncomeRequestById($id, $receiver_id)
    {
        $sql = "SELECT family.id, family.date, user.family_name, user.given_name, user.picture 
        FROM `family` 
        LEFT JOIN `user`
        ON user.id = family.sender_id
        WHERE family.receiver_id = " . $receiver_id . " 
        AND family.status = " . Family::WAITING . "
        AND family.id = " . $id . " 
        LIMIT 1";
        $request = $this->DB->query($sql);

        if($request)
        {
            return $request;
        } else {
            $this->error_validation = array(
                'error' => true,
                'text' => 'Нет такого пользователя',
            );
            return false;
        }
    }

    public function getWaitingRequestById($id, $receiver_id)
    {
        $sql = "SELECT family.id, family.date, user.family_name, user.given_name, user.picture 
        FROM `family` 
        LEFT JOIN `user`
        ON user.id = family.sender_id
        WHERE family.receiver_id = " . $receiver_id . " 
        AND family.status = " . Family::WAITING . "
        AND family.id = " . $id . " 
        LIMIT 1";
        $request = $this->DB->query($sql);

        if($request)
        {
            return $request[0];
        } else {
            $this->error_validation = array(
                'error' => true,
                'text' => 'Нет такого пользователя',
            );
            return false;
        }
    }

    public function getDeleteableRequestById($id, $sender_id)
    {
        $sql = "SELECT  *
        FROM `family` 
        WHERE family.id = " . $id . " 
        LIMIT 1";

        $request = $this->DB->query($sql);
        if($request)
        {
            return $request;
        } else {
            $this->error_validation = array(
                'error' => true,
                'text' => 'Нет такого пользователя',
            );
            return false;
        }

    }

    public function getConfirmedRequest($user_id)
    {
        $sql = "SELECT family.id, family.date, user.id as user_id, user.family_name, user.given_name, user.picture 
        FROM `family` 
        LEFT JOIN `user`
        ON user.id = family.receiver_id OR user.id = family.sender_id
        WHERE family.sender_id = " . $user_id . " 
        AND family.status = " . Family::CONFIRMED . "
        OR family.receiver_id = " . $user_id . " 
        AND family.status = " . Family::CONFIRMED . " 
        ORDER BY family.id";
        $result = $this->DB->query($sql);
        return $result;
    }

    /**
     * Подтверждение связи получателем
     * @param $id
     * @return bool
     */
    function confirmeRelationshiop($receiver_id, $id)
    {
        $relationship = $this->getRelationshipById($id);

        if($relationship)
        {
            if($relationship->receiver_id != $receiver_id)
            {
                $this->error_validation = array(
                    'error' => true,
                    'text' => 'Попытка несанкционированного подтверждения запроса',
                );
                return false;
                // todo Попытка махинаций, сообщить администратору
            }
            $result = $this->updateStatus($relationship->id, Family::CONFIRMED);
        } else {
            $this->error_validation = array(
                'error' => true,
                'text' => 'Нет такого запроса',
            );
            return false;
        }

        if($result)
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Удаление связи с получателем
     * @param $id
     * @return bool
     */
    function deleteRelationshiop($id)
    {
        $sql = "DELETE 
        FROM `family` 
        WHERE id = :id";

        $values = [
            ':id' => $id
        ];

        $request = $this->DB->execute($sql, $values);
        return $request;
    }

    public function cancelRelationshiop($id)
    {
        return $this->updateStatus($id, Family::REFUSED);
    }

    /**
     * Отказ от установки связи
     * @param $id
     * @return bool
     */
    function refusedRelationshiop($id)
    {
        $this->getRelationshipById($id);

        if($this->id != '')
        {
            $result = $this->updateStatus($this->id, Family::REFUSED);
        }

        if($result)
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Обновление статуса операции связи аккаунтов
     * @param $id
     * @param $status
     * @return bool
     */
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE `" . self::TABLE_NAME . "` SET
                    status = " . $status . "
                    WHERE id = :id";

        $values = [
            'id' => $id
        ];

        $request = $this->DB->execute($sql, $values);


        if(isset($this->id) && $this->id != '')
        {

            $values = [
                'id' => $this->id
            ];
        }

        if($request = $this->DB->execute($sql, $values))
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Получение записи запроса на добавление
     * @param $id
     *
     * @return array|bool
     */
    public function getRelationshipById($id)
    {
        $sql = "SELECT *
        FROM `family` 
        WHERE id = " . $id . "
        LIMIT 1";

        $relationship = $this->DB->query($sql);

        // Проверка существования отправителя
        if($relationship)
        {
            return $relationship[0];
        } else {
            $this->error_validation = array(
                'error' => true,
                'text' => 'Нет такого запроса',
            );
            return false;
        }
    }

    /**
     * Получение id пользователя по Email
     * @param $receiver_email
     * @return bool
     */
    public function setReceiverByEmail($receiver_email)
    {
        $receiver = $this->M_User->getByEmail($receiver_email)[0];

        // Проверка существования получателя
        if(! empty($receiver))
        {
            $this->receiver_id = $receiver->id;
            return true;
        } else {
            $this->error_validation = array(
                'error' => true,
                'text' => 'Нет такого получателя',
            );
            return false;
        }
    }

    public function setReceiverById($receiver_id)
    {
        $receiver = $this->M_User->getById($receiver_id);

        // Проверка существования получателя
        if(isset($receiver) && $receiver->id != '')
        {
            $this->receiver_id = $receiver->id;
            return true;
        } else {
            $this->error_validation = array(
                'error' => true,
                'text' =>  'Нет такого получателя',
            );
            return false;
        }
    }

    public function getReceiverEmailById($receiver_id)
    {
        $receiver = $this->M_User->getById($receiver_id);

        // Проверка существования получателя
        if(isset($receiver) && $receiver->id != '')
        {
            return $receiver;
        } else {
            $this->error_validation = array(
                'error' => true,
                'text' =>  'Нет такого получателя',
            );
            return false;
        }
    }

    protected function checkRelationshipByIds()
    {

        if($this->sender_id == '' || $this->receiver_id == '')
        {
            $this->error_validation = array(
                'error' => true,
                'text' => 'Несуществующие данные для просмотра',
            );

            return false;
        }

        $sql = "SELECT *
        FROM `family` 
        WHERE receiver_id = :receiver_id 
        AND sender_id = :sender_id
        OR receiver_id = :receiver_id 
        AND sender_id = :sender_id" ;

        $values = [
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id
        ];

        $result = $this->DB->execute($sql, $values);

        if($result)
        {
            return true;
        } else {
            $this->error_validation = array(
                'error' => true,
                'text' => 'Нет такого запроса',
            );
            return false;
        }
    }

    /**
     * Создание запроса на соединение данных
     * @param $sender_id
     * @param $receiver_email
     * @return bool
     */
    public function create($receiver_email)
    {
        // Проверка существования пользователя с данным Email
        if(! $this->setReceiverByEmail($receiver_email))
        {
            return false;
        }

        // отказ от создание запроса на самого себя
        if($this->sender_id == $this->receiver_id)
        {
            $this->error_validation = array(
                'error' => true,
                'text' => 'Нет смысла добавлять себя к себе',
            );
            return false;
        }

        // проверка существования подобного запроса
        if(! $this->checkRelationshipByIds())
        {
            return false;
        }

        // Создание
        $sql = "INSERT INTO `" . self::TABLE_NAME . "`
                    (sender_id,
                    receiver_id,
                    status,
                    date)
                     VALUES (
                    :sender_id,
                    :receiver_id,
                    :status,
                    :date
                    )";

        $date = date('Y-m-d');
        $this->status = Family::WAITING;

        $values = [
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'status' => $this->status,
            'date' => $date
        ];

        return $this->DB->execute($sql, $values);
    }
}