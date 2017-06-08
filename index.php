<?php
require('vendor/autoload.php');
header('Content-type: text/html; charset=utf-8');
use bookkeeping\Models\Route;
use bookkeeping\Core\ErrorHandler;

ini_set('display_errors', 'on');
//(new ErrorHandler())->register();

$Route = new  Route($_SERVER['REQUEST_URI']);
$Route::start();

