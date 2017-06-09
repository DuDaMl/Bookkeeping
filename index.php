<?php
require('vendor/autoload.php');
header('Content-type: text/html; charset=utf-8');
use bookkeeping\Models\Route;
use bookkeeping\Core\ErrorHandler;

ini_set('display_errors', 'on');
//(new ErrorHandler())->register();

$Route = new  Route($_SERVER['REQUEST_URI']);
$Route::start();

/*
class one
{
    //private $one = 1;

    public $one;

    function __construct()
    {
        $this->one = 1;
    }

    function getOne()
    {
        return $this->one;
    }

    function render()
    {
        echo $this->one;
        //echo ">" . $this->getOne() . "<<";
    }
}

class two extends  one
{
    function __construct()
    {
        $this->one = 2;
    }
}

//$one = new one();
//echo $one->getOne();

$two = new two();
//echo $two->getOne();
echo $two->render();