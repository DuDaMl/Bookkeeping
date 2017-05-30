<?php
namespace bookkeeping\Models;

class Pay
{
    const TABLE_NAME = 'pay';
    protected $DB;


    public $amount;
    public $description;
    public $category_id;
    public $date;


    function __construct()
    {
        $this->DB = DB::getInstance()->getConnection();
    }

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

    function save()
    {
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

        $stmt->bindParam(':amount', $_POST['amount'], $this->DB::PARAM_INT);
        $stmt->bindParam(':description', $_POST['description'], $this->DB::PARAM_STR);
        $stmt->bindParam(':category_id', $_POST['category_id'], $this->DB::PARAM_INT);
        $stmt->bindParam(':date', date('Y-m-d'), $this->DB::PARAM_STR);
        $stmt->execute();
        //echo   $this->DB->lastInsertId();
    }
}