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
        $this->DB = DB::getInstance()->getConnection();
    }

    /**
     * @return array|bool
     */

   protected function get($sql)
   {
       try
       {
           $result = $this->DB->prepare($sql);
           $result->execute();
           return $result->fetchAll(PDO::FETCH_CLASS);
       }
       catch(PDOException $e)
       {
           echo $e->getMessage();
           return false;
       }
   }

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
       return $this->get($sql);
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
        $answer = $this->get($sql);
        return $answer[0];
    }

    /**
     * @return bool
     */
    public function save($sql)
    {
        if($this->user_id == false || ! self::validate())
        {
            return false;
        }

        if(empty($this->amount)
            || empty($this->category_id)
            || empty($this->date))
        {
            return false;
        }

        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':amount',  $this->amount, PDO::PARAM_INT);
        $stmt->bindParam(':description', $this->description , PDO::PARAM_STR);
        $stmt->bindParam(':category_id', $this->category_id , PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $this->user_id , PDO::PARAM_INT);
        $stmt->bindParam(':date', $this->date , PDO::PARAM_STR);

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

    public function create()
    {
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

        return $this->save($sql);
    }

    public function update()
    {

        if(! isset($this->id) || $this->id == '')
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

        return $this->save($sql);
    }
    /**
     * @return bool
     */
    function delete()
    {
        if(! self::validate()){

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

        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':id',  $this->id, PDO::PARAM_INT);

        if($stmt->execute())
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