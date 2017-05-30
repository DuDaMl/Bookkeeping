<?php
require('vendor/autoload.php');
header('Content-type: text/html; charset=utf-8');

$Route = new bookkeeping\Models\Route($_SERVER['REQUEST_URI']);
$controller = $Route->getController();

/*
$all_pays = $controller->getAll();

foreach($all_pays as $all => $pay)
{
    echo $pay->date_create . "<br/>";
}

?>


<?php
*/