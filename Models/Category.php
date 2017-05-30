<?php
namespace bookkeeping\Models;
use \PDO;

class Category
{

    const TABLE_NAME = 'category';
    protected $DB;


    public $amount;
    public $description;
    public $category_id;
    public $date_create;


    function __construct()
    {
        $this->DB = DB::getInstance()->getConnection();
    }

    function getAll()
    {
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "`";

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

    public function select($sql){
    }
}