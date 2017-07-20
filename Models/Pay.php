<?php
namespace bookkeeping\Models;

class Pay
{
    use \bookkeeping\Models\Traits\ValidateDate;

    const TABLE_NAME = 'pay';
    protected $DB;
    public $id;
    public $amount;
    public $description;
    public $category_id;
    public $user_id;
    public $date;

    public $error_validation;

    function __construct()
    {

        $this->DB = DB::getInstance();
    }

    /**
     * return all pays from pointed data limit
     * @param $start
     * @param $end
     * @return array|object
     */
   static function getAll($user_id, $start, $end)
   {
       $sql = "SELECT pay.id, pay.amount, pay.category_id, pay.description, pay.user_id, pay.date, category.name, category.type
               FROM `" . self::TABLE_NAME . "`     
               LEFT JOIN category
               ON pay.category_id = category.id 
               WHERE pay.date BETWEEN  '" . $start ."' 
               AND '" . $end ."'
               AND pay.user_id = " . $user_id . "
               AND category.type = 'Pay'
               ORDER BY date DESC, id DESC
               ";

       $DB = DB::getInstance();
       return $DB->query($sql);
   }

    /**
     * @param int | $id
     * @return array|bool
     */
    public static function getById(int $id)
    {
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE  id = " . $id ;
        $DB = DB::getInstance();
        $answer = $DB->query($sql, static::class);
        return $answer;
    }

    /**
     * create pay
     * @return bool
     */
    public function create()
    {
        if(! self::validate())
        {
            return false;
        }

        if(empty($this->amount)
            || empty($this->category_id)
            || empty($this->date))
        {
            return false;
        }

        $sql = "INSERT INTO `" . self::TABLE_NAME . "`
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
            ':user_id' => $this->user_id,
            ':date' => $this->date
        ];

        echo $sql;

        return $this->DB->execute($sql, $params);

    }

    /**
     * update pay
     * @return bool
     */
    public function update()
    {
        if(! $this->validate())
        {
            return false;
        }

        if(! isset($this->id) || $this->id == '')
        {
            return false;
        }


        if(empty($this->amount)
            || empty($this->category_id)
            || empty($this->date))
        {
            return false;
        }

        $sql = "UPDATE `" . self::TABLE_NAME . "` SET
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
            ':user_id' => $this->user_id,
            ':date' => $this->date
        ];

        return $this->DB->execute($sql, $params);

    }

    /**
     * delete pay
     * @return bool
     */
    function delete()
    {
        if(! $this->validate()){

            return false;
        }

        if(isset($this->id) && $this->id != '')
        {
            // Delete
            $sql = "DELETE FROM `" . self::TABLE_NAME . "` 
                    WHERE id = :id
                    ";
        } else {
            // ошибка, попытка удаления без id
            return false;
        }

        $params = [
            ':id' => $this->id
        ];

        $result = $this->DB->execute($sql, $params);


        if($result)
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Валидация данных формы. Инициализация переменных класса
     * @input $_POST data
     * @return bool true/false
     */
    function validate()
    {

        if(isset($this->user_id))
        {

            if(! $this->validateInt('user_id'))
            {
                return false;
            }
        }

        if(isset($this->id))
        {
            if(! $this->validateInt('id'))
            {
                return false;
            }
        }

        if(isset($this->category_id))
        {
            if(! $this->validateInt('category_id'))
            {
                return false;
            }
        }

        if(isset($this->amount))
        {
            if(! $this->validateInt('amount'))
            {
                return false;
            }
        }

        if(isset($this->date))
        {
            if(! self::validateDate($this->date))
            {
                $this->error_validation = array(
                    'error' => true,
                    'date' =>  'Ошибка в выбранной дате'
                );
                return false;
            }

        }

        if($this->description)
        {
            $this->description = strip_tags($this->description);
        }

        return true;
    }

    function validateInt($var)
    {
        if(! filter_var($this->$var, FILTER_VALIDATE_INT)) {

            $this->error_validation = array(
                'error' => true,
                'amount' => 'Ошибка в указанном ' . $var,
            );
            return false;
        }
        return true;
    }
}