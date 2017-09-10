<?php
namespace bookkeeping\Model;

use bookkeeping\Model\Exceptions\DateNotFilledException;

class Model
{
    use \bookkeeping\Model\Traits\ValidateDate;
    use \bookkeeping\Model\Traits\ValidateInt;

    public $id;
    public $user_id;
    public $amount;
    public $description;
    public $category_id;
    public $date;

    /**
     * Создание запись о расходе
     * @param array $date
     * @return ????
     * @throws DateNotFilledException     данные не заполены
     */
    public function create($date)
    {
        $this->prepareFormat($date);

        if( empty($this->amount)
            || empty($this->category_id)
            || empty($this->date))
        {
            // todo разделить важность ошибок. на незаполненные пользователем и не переданные из системы(category_id).
            $e = new DateNotFilledException('Необходимые данные не заполнены (Сумма)');
            throw $e;
        }

        $sql = "INSERT INTO `" . static::TABLE_NAME . "`
                    (amount,
                    description,
                    user_id,
                    category_id,
                    date)
                     VALUES (
                    :amount,
                    :description,
                    :user_id,
                    :category_id,
                    :date
                    )";

        $params = [
            ':amount' => $this->amount,
            ':description' => $this->description,
            ':category_id' => $this->category_id,
            ':user_id' =>  User::getId(),
            ':date' => $this->date
        ];

        $DB = DB::getInstance();
        // todo Exception PDO
        return $DB->execute($sql, $params);
    }

    /**
     * update record in db
     * @return bool
     */
    public function update($date)
    {
        if(empty ($date))
        {
            return false;
        }

        $this->prepareFormat($date);

        if(! isset($this->id) || $this->id == '')
        {
            return false;
        }

        if( empty($this->amount)
            || empty($this->category_id)
            || empty($this->date))
        {
            // todo разделить важность ошибок. на незаполненные пользователем и не переданные из системы(category_id).
            $e = new DateNotFilledException('Необходимые данные не заполнены (Сумма)');
            throw $e;
        }

        $sql = "UPDATE `" . static::TABLE_NAME . "` SET
                    amount = :amount,
                    description = :description,
                    category_id = :category_id,
                    user_id = :user_id,
                    date = :date
                    WHERE id = :id";

        $params = [
            ':id' => $this->id,
            ':amount' => $this->amount,
            ':description' => $this->description,
            ':category_id' => $this->category_id,
            ':user_id' =>  User::getId(),
            ':date' => $this->date
        ];

        $DB = DB::getInstance();
        return $DB->execute($sql, $params);
    }

    /**
     * delete record in db
     * @param id
     * @return bool
     */
    function delete()
    {
        if(! isset($this->id) || $this->id == '')
        {
            return false;
        }

        $sql = "DELETE FROM `" . static::TABLE_NAME . "`  WHERE id = :id ";

        $params = [
            ':id' => $this->id
        ];

        $DB = DB::getInstance();
        $result = $DB->execute($sql, $params);

        if($result)
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return id авторизированного пользователя
     */
    protected static function getUserId()
    {
        $M_User = new User();
        return $M_User->getId();
    }

    function prepareFormat(array $date)
    {
        $this->amount = $this->validateInt($date['amount']);
        $this->category_id = $this->validateInt($date['category_id']);
        $this->date = $this->validateDate($date['date']);
        $this->description = trim(htmlspecialchars($date['description']));
    }
}