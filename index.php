<?php
require('vendor/autoload.php');
header('Content-type: text/html; charset=utf-8');

$Route = new bookkeeping\Models\Route($_SERVER['REQUEST_URI']);
$Route->start();

/*
class Pay {
    static $a = 5;

    function __construct()
    {

    }

    function getA()
    {
        return static::$a;
    }

    function setA($a)
    {
        self::$a = $a;
    }

    public function foo() {
        static $x = 0;
        echo ++$x . "<br/>";
    }

}

class Pay2 extends Pay {
    static $a = 46;



}
/*
$pay1 = new Pay;
//$pay1->a = 1;
$pay1->setA(1);// = 1;
$pay2 = new Pay;
//$pay2->a = '2123';
$pay2->setA(5242423);// = 1;
*/

/*
$pay1 = new Pay;
$pay2 = new Pay2;
echo $pay1->getA() . "<br/>";
echo $pay2->getA() . "<br/>";

$pay1->foo();
$pay2->foo();
$pay1->foo();
$pay2->foo();

class Model {
    public static $table = 'table';
    public static function getTable() {
        return self::$table;
    }
}

class User extends Model {
    public static $table = 'users';
}

echo User::getTable(); // 'table'*/