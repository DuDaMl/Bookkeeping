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
        $this->DB = DB::getInstance()->getConnection();
        $this->M_User = new User();
    }

    /**
     * Получение запросов на связи по id Отправителя и статусу операции
     * @param $sender_id
     * @param $status
     * @return array
     */
    public function getRequestByStatus($sender_id, $status)
    {
        $sql = "SELECT family.id, family.date, user.family_name, user.given_name, user.picture 
        FROM `family` 
        LEFT JOIN `user`
        ON user.id = family.receiver_id
        WHERE family.sender_id = " . $sender_id . "
        AND family.status = " . $status;

        $result = $this->DB->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_CLASS);
    }

    /**
     * Подтверждение связи получателем
     * @param $id
     * @return bool
     */
    function confirmeRelationshiop($id)
    {
        $relationship = $this->getRelationshipById($id);


        if($relationship)
        {
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
     * Удаление запроса связи
     * @param $id
     * @return bool
     */
    function deletedRelationshiop($id)
    {
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

        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':id',  $id, PDO::PARAM_INT);

        if(isset($this->id) && $this->id != '')
        {
            $stmt->bindParam(':id',  $this->id, PDO::PARAM_INT);
        }

        if($stmt->execute())
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

        $result = $this->DB->prepare($sql);
        $result->execute();
        $relationship = $result->fetchAll(PDO::FETCH_CLASS);

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
        $receiver = $this->M_User->getByEmail($receiver_email);

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

    public function checkRelationshipByIds($sender_id, $receiver_id)
    {
        $sql = "SELECT id
        FROM `family` 
        WHERE receiver_id = :receiver_id
        AND sender_id = :sender_id";

        $result = $this->DB->prepare($sql);
        $result->bindParam(':receiver_id',  $receiver_id, PDO::PARAM_INT);
        $result->bindParam(':sender_id',  $sender_id, PDO::PARAM_INT);
        $result->execute();
        $relationship = $result->fetchAll(PDO::FETCH_CLASS);

        // Проверка существования отправителя
        if($relationship)
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
    public function create($sender_id, $receiver_email)
    {
        $this->sender_id = $sender_id;

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
        if($this->checkRelationshipByIds($this->sender_id, $this->receiver_id))
        {
            $this->error_validation = array(
                'error' => true,
                'text' => 'Не стоит добавлять более одного запроса на человека',
            );
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
        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':sender_id', $this->sender_id , PDO::PARAM_STR);
        $stmt->bindParam(':receiver_id', $this->receiver_id , PDO::PARAM_INT);
        $stmt->bindParam(':status', $this->status , PDO::PARAM_INT);
        $stmt->bindParam(':date', $date , PDO::PARAM_STR);

        if($stmt->execute())
        {
            return true;
        } else {
            return false;
        }
    }

}