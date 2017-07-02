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

    protected function __construct(){

        $this->connection = new PDO('mysql:host=127.0.0.1;dbname=bookkeeping', 'root', '');
        $this->connection->exec("set names utf8");
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance() {
        if (null === self::$_instance) {
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

    public function query($sql)
    {
        $sth = $this->connection->prepare($sql);
        $res = $sth->execute();
        if (false !== $res) {
            return $sth->fetchAll(\PDO::FETCH_CLASS);
        }
        return [];
    }

    public function getError() {
        return $this->error;
    }

    private function __clone() { }

}