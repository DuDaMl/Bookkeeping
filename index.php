<?php
require('vendor/autoload.php');
header('Content-type: text/html; charset=utf-8');
use bookkeeping\Models\Route;

$Route = new  Route($_SERVER['REQUEST_URI']);
$Route::start();
