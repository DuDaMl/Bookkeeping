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
     * Все отправленные запросы пользователя
     * @param $receiver_id
     * @return array|bool
     */
    public function getAllSendedRequestBySenderIdWithReseiverEmail($sender_id)
    {
        if(! $this->setSenderById($sender_id))
        {
            $this->error_validation = array(
                'error' => true,
                'text' => 'Не определен получатель по переданному ID',
            );

            return false;
        }

        $sql = "SELECT family.id, family.date, user.family_name, user.given_name, user.picture 
        FROM `family` 
        LEFT JOIN user
        ON user.id = family.receiver_id
        WHERE family.sender_id = " . $this->sender_id . "
        AND family.status = " . Family::WAITING;
echo $sql;
        try
        {
            $result = $this->DB->prepare($sql);
            $result->execute();
            $sender_data = $result->fetchAll(PDO::FETCH_CLASS);
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return false;
        }

        return $sender_data;
    }

    /**
     * Установка id отправителя
     * @param $sender_id
     * @return bool
     */

    public function setSenderById($sender_id)
    {

        $sender = $this->M_User->getById($sender_id);

        // Проверка существования отправителя
        if(isset($sender) && $sender->id != '')
        {
            $this->sender_id = $sender->id;
            return true;
        } else {
            $this->error_validation = array(
                'error' => true,
                'text' => 'Нет такого отправителя',
            );
            return false;
        }
    }

    /**
     * Установка id получателя
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

    /**
     * Создание запроса на соединение данных
     * @param $sender_id
     * @param $receiver_email
     * @return bool
     */
    public function create($sender_id, $receiver_email)
    {
        if(! $this->setSenderById($sender_id) || ! $this->setReceiverByEmail($receiver_email))
        {
            $this->error_validation = array(
                'error' => true,
                'text' => 'Не определени отправитель или получатель',
            );
            return false;
        }

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