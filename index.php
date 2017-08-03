<?php
session_start();
require('vendor/autoload.php');
header('Content-type: text/html; charset=utf-8');
use bookkeeping\Model\Route;
use bookkeeping\Core\ErrorHandler;


//phpinfo();

ini_set('display_errors', 'on');
//(new ErrorHandler())->register();

$Route = new  Route($_SERVER['REQUEST_URI']);
$Route::start();

/*
try {
    // код который может выбросить исключение
    if (true) {
        throw new Exception("One");
    } else {
        echo "Zero";
    }
} catch (Exception $e) {
    // код который может обработать исключение
    echo $e->getMessage();
}
*/
/*
abstract class Model {
    public static $table='some table';
    public $err = 'Model';

    public static function getTable() {
        return self::$table;
    }
}
class User extends Model {
    public static $table='users';
    public $err = 'User';

}
echo User::getTable(); // some table
/*

*/