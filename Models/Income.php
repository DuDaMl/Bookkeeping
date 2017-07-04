<?php
namespace bookkeeping\Models;
use \DateTime;
use \PDO;

class Income
{
    const TABLE_NAME = 'income';
    protected $DB;
    public $id;
    public $user_id;
    public $amount;
    public $description;
    public $category_id;
    public $date;

    public $error_validation;

    function __construct($user_id)
    {
        $this->user_id = $user_id;
        $this->DB = DB::getInstance();
    }

    /**
     * @return array|bool
     */
   function getAll()
   {
       $month_start = date('Y-01-01');
       $month_end = date('Y-12-30');

       $sql = "SELECT income.id, income.amount, income.user_id, income.category_id, income.description, income.date, category.name, category.type
               FROM `" . self::TABLE_NAME . "`     
               LEFT JOIN category
               ON income.category_id = category.id 
               WHERE income.date BETWEEN  '" . $month_start ."' 
               AND '" . $month_end ."'  
               AND category.type = 'Income'
               AND income.user_id = " . $this->user_id . "
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

        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE  id = " . $id;
        return $this->DB->query($sql,'fetch');
    }

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
                    category_id,
                    user_id,
                    date)
                     VALUES (
                    :amount,
                    :description,
                    :category_id,
                    :user_id,
                    :date
                    )";

        $params = [
            ':amount' => $this->amount,
            ':description' => $this->description,
            ':user_id' => $this->user_id,
            ':category_id' => $this->category_id,
            ':date' => $this->date,
        ];

        return $this->DB->execute($sql, $params);
    }

    public function update()
    {
        if(! self::validate())
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
                    WHERE id = :id
                    ";

        $params = [
            ':amount' => $this->amount,
            ':description' => $this->description,
            ':user_id' => $this->user_id,
            ':category_id' => $this->category_id,
            ':date' => $this->date,
            ':id' => $this->id,
        ];

        return $this->DB->execute($sql, $params);
    }

    /**
     * @return bool
     */
    function delete()
    {
        if(! self::validate())
        {
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

        return $this->DB->execute($sql, $params);
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

    /**
     * Валидация данных формы. Инициализация переменных класса
     * @input $_POST data
     * @return bool true/false
     */
    function validate()
    {
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
}