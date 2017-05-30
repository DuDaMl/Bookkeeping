<?php
namespace bookkeeping\Models;
use PDO;

class DB
{
    protected $connection;
    public $error;
    private static $_instance;

    private function __construct(){
        $DB_host = "localhost";
        $DB_user = "root";
        $DB_pass = "";
        $DB_name = "bookkeeping";

        try
        {
            $this->connection = new PDO("mysql:host={$DB_host};dbname={$DB_name}",$DB_user,$DB_pass);
            $this->connection->exec("set names utf8");
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    }

    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function getError() {
        return $this->error;
    }

    private function __clone() { }

}