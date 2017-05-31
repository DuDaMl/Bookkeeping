<?php
namespace bookkeeping\Models;
use \DateTime;
class Pay
{
    const TABLE_NAME = 'pay';
    protected $DB;
    public $amount;
    public $description;
    public $category_id;
    public $date;

    public $error_validation;

    function __construct()
    {
        $this->DB = DB::getInstance()->getConnection();
    }

    /**
     * @return array|bool
     */
   function getAll()
   {
       $sql = "SELECT * FROM `" . self::TABLE_NAME . "`     
               LEFT JOIN category
               ON pay.category_id = category.id 
               ORDER BY date DESC";

       try
       {
           $result = $this->DB->prepare($sql);
           $result->execute();
           return $result->fetchAll($this->DB::FETCH_CLASS);
       }
       catch(PDOException $e)
       {
           echo $e->getMessage();
           return false;
       }
   }

    public function select($sql)
    {
       // return $this->DB->select($sql);
    }

    /**
     * Валидация данных формы. Инициализация переменных класса
     * @input $_POST data
     * @return bool true/false
     */
    function validate()
    {
        // Указатель наличия ошибка в массиве $_POST
        $error = false;

        // валидация введенной суммы
        if(! filter_var($_POST['amount'], FILTER_VALIDATE_INT)){
            $this->error_validation = array(
                'error' => true,
                'amount' => 'Ошибка в указанной сумме',
            );
        } else {
            $amount = str_replace('+','',$_POST['amount']);
            $this->amount = str_replace('-','',$amount);
        }

        if(! filter_var($_POST['category_id'], FILTER_VALIDATE_INT)){
            $this->error_validation = array(
                'error' => true,
                'category_id' => 'Ошибка в выбранной категории',
            );
        } else {
            $category_id = str_replace('+','',$_POST['category_id']);
            $this->category_id = str_replace('-','',$category_id);
        }

        if(! self::validateDate($_POST['date'])){
            $this->error_validation = array(
                'error' => true,
                'date' =>  'Ошибка в выбранной дате'
            );
        } else {
            $this->date = $_POST['date'];
        }

        if($this->error_validation['error'] == true){
            return false;
        }

        $this->description = htmlspecialchars($_POST['description']);
        return true;
    }

    function save()
    {
        if(! self::validate()){

            return false;
        }

        $sql = "INSERT INTO `" . self::TABLE_NAME . "`
            (amount,
            description,
            category_id,
            date)
             VALUES (
            :amount,
            :description,
            :category_id,
            :date
            )";

        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':amount',  $this->amount, $this->DB::PARAM_INT);
        $stmt->bindParam(':description', $this->description , $this->DB::PARAM_STR);
        $stmt->bindParam(':category_id', $this->category_id , $this->DB::PARAM_INT);
        $stmt->bindParam(':date', $this->date , $this->DB::PARAM_STR);
        $stmt->execute();
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