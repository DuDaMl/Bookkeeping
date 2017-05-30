<?php
namespace bookkeeping\Models;

use bookkeeping\Controllers\Pay as C_Pay;
use bookkeeping\Controllers\Category as C_Category;

class Route {


    protected $controller_name;

    public function __construct($uri)
    {
       $uri = explode('/', $uri, 1);
        $this->controller_name = '' . ucfirst(str_replace('/', '', $uri)[0]);
    }

    public function getController()
    {
        // ToDo валидация инициализируемого класса.

        switch($this->controller_name){
            case 'Category':
                return new C_Category();
                break;

            default:
                return new C_Pay();
                break;
        }



        /*if(class_exists($this->controller_name)){
           // return new $this->controller_name();
        }else{
            //return new Pay();
        }

        return $this->controller_name;*/
    }
}