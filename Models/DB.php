<?php
namespace bookkeeping\Models;
use PDO;
use bookkeeping\Models\Core\Singleton;

class DB
    extends Singleton
{
    protected $connection;
    public $error;
    protected static $_instance;

    protected function __construct()
    {
        try {
            $this->connection = new PDO('mysql:host=127.0.0.1;dbname=bookkeeping', 'root', '');
            $this->connection->exec("set names utf8");
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    public static function getInstance()
    {
        if (null === self::$_instance)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function execute($sql, $params = [])
    {
        $sth = $this->connection->prepare($sql);
        $res = $sth->execute($params);
        return $res;
    }

    public function query($sql, $className = '')
    {
        $sth = $this->connection->prepare($sql);
        $res = $sth->execute();

        if ($res !== false)
        {
            if($className != '')
            {
                return $sth->fetchAll(PDO::FETCH_CLASS, $className);
            } else {
                return $sth->fetchAll(PDO::FETCH_OBJ);
            }
        }
        return [];
    }


    public function getError() {
        return $this->error;
    }

    private function __clone() { }

}