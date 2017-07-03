<?php
namespace bookkeeping\Models;
use \DateTime;
use \PDO;

class Pay
{
    const TABLE_NAME = 'pay';
    protected $DB;
    public $id;
    public $amount;
    public $description;
    public $category_id;
    public $user_id;
    public $date;

    public $error_validation;

    function __construct($user_id)
    {
        $this->user_id = $user_id;
        $this->DB = DB::getInstance();
    }

    /**
     * return all pays from pointed data limit
     * @param $start
     * @param $end
     * @return array|object
     */
   function getAll($start, $end)
   {
       $sql = "SELECT pay.id, pay.amount, pay.category_id, pay.description, pay.user_id, pay.date, category.name, category.type
               FROM `" . self::TABLE_NAME . "`     
               LEFT JOIN category
               ON pay.category_id = category.id 
               WHERE pay.date BETWEEN  '" . $start ."' 
               AND '" . $end ."'
               AND pay.user_id = " . $this->user_id . "
               AND category.type = 'Pay'
               ORDER BY date DESC, id DESC
               ";

       return $this->DB->query($sql);
   }

    /**
     * @return array|bool
     */
    function getById($id)
    {
        if(filter_var($id, FILTER_VALIDATE_INT)){
            $id = str_replace('+', '', $id);
            $id = str_replace('-', '', $id);
        } else {
            $this->error_validation = array(
                'error' => true,
                'text' => 'Ошибка в передаваемом id',
            );
            return false;
        }

        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` 
        WHERE  id = " . $id . "
        AND pay.user_id = " . $this->user_id;

        $answer = $this->DB->query($sql, 'fetch');
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
        if(! filter_var($this->user_id, FILTER_VALIDATE_INT))
        {
            $this->error_validation = array(
                'error' => true,
                'amount' => 'Ошибка в указанном user_id',
            );
        } else {
            $id = str_replace('+','',$this->user_id);
            $this->user_id = str_replace('-','',$id);
        }

        // валидация переданного id
        if(isset($_POST['id']))
        {
            if(! filter_var($_POST['id'], FILTER_VALIDATE_INT))
            {
                $this->error_validation = array(
                    'error' => true,
                    'amount' => 'Ошибка в указанном id',
                );
            } else {
                $id = str_replace('+','',$_POST['id']);
                $this->id = str_replace('-','',$id);
            }
        }

        // валидация введенной суммы
        if(isset($_POST['amount']))
        {
            if(! filter_var($_POST['amount'], FILTER_VALIDATE_INT)
                || $_POST['amount'] == 0)
            {
                $this->error_validation = array(
                    'error' => true,
                    'amount' => 'Ошибка в указанной сумме',
                );
            } else {
                $amount = str_replace('+','',$_POST['amount']);
                $this->amount = str_replace('-','',$amount);
            }
        }

        if(isset($_POST['category_id']))
        {
            if(! filter_var($_POST['category_id'], FILTER_VALIDATE_INT))
            {
                $this->error_validation = array(
                    'error' => true,
                    'category_id' => 'Ошибка в выбранной категории',
                );
            } else {
                $category_id = str_replace('+','',$_POST['category_id']);
                $this->category_id = str_replace('-','',$category_id);
            }
        }

        if(isset($_POST['date']))
        {
            if(! self::validateDate($_POST['date']))
            {
                $this->error_validation = array(
                    'error' => true,
                    'date' =>  'Ошибка в выбранной дате'
                );
            } else {
                $this->date = $_POST['date'];
            }

            if($this->error_validation['error'] == true)
            {
                return false;
            }
        }

        $this->description = strip_tags($_POST['description']);
        return true;
    }

    /**
     * @param $date
     * @param string $format
     * @return bool
     */
    function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}