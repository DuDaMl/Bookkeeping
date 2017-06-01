<?php
namespace bookkeeping\Models;
use \DateTime;
class Pay
{
    const TABLE_NAME = 'pay';
    protected $DB;
    public $id;
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
       $sql = "SELECT pay.id, pay.amount, pay.category_id, pay.description, pay.date, category.name
               FROM `" . self::TABLE_NAME . "`     
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
               WHERE  id = :id";

        try
        {
            $result = $this->DB->prepare($sql);
            $result->execute(array(
                ':id' => $id
            ));
            return $result->fetch($this->DB::FETCH_OBJ);
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

        // валидация переданного id
        if(isset($_POST['id'])){
            if(! filter_var($_POST['id'], FILTER_VALIDATE_INT)){
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
        if(! filter_var($_POST['amount'], FILTER_VALIDATE_INT)
        || $_POST['amount'] == 0){
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

        $this->description = strip_tags($_POST['description']);
        return true;
    }

    function save()
    {
        if(! self::validate()){

            return false;
        }

        if(isset($this->id) && $this->id != ''){
            // Update
            $sql = "UPDATE `" . self::TABLE_NAME . "` SET
                    amount = :amount,
                    description = :description,
                    category_id = :category_id,
                    date = :date
                    WHERE id = :id
                    ";
        } else {
            // Create
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
        }

        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':amount',  $this->amount, $this->DB::PARAM_INT);
        $stmt->bindParam(':description', $this->description , $this->DB::PARAM_STR);
        $stmt->bindParam(':category_id', $this->category_id , $this->DB::PARAM_INT);
        $stmt->bindParam(':date', $this->date , $this->DB::PARAM_STR);

        if(isset($this->id) && $this->id != ''){
            $stmt->bindParam(':id',  $this->id, $this->DB::PARAM_INT);
        }

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