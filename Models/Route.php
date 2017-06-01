<?php
namespace bookkeeping\Models;

use bookkeeping\Controllers\Pay as C_Pay;
use bookkeeping\Controllers\Category as C_Category;

class Route {

    protected $controller_name;
    protected $controller_method;
    protected $var;

    public function __construct($uri)
    {
        // /edit/1
        // /category/edit/1
        $allow_controller_name = array(
            0 => 'Category'
        );


        $uri = explode('/', $uri);

        if(in_array($uri[1],$allow_controller_name)){
            // для любого контроллера
            $this->controller_name = '' . ucfirst(str_replace('/', '', $uri)[1]);
            $this->controller_method = '' . ucfirst(str_replace('/', '', $uri)[2]);
        } else {
            // дял дефолтного контроллера
            $this->controller_name = '';
            $this->controller_method = '' . str_replace('/', '', $uri)[1];
            $this->var = $uri[2];
        }


        //echo $this->controller_name . "<= controller_name <br>";
        //echo $this->controller_method . "<= controller_method <br>";
        //echo $var . "<= var <br>";
    }



    public function start()
    {
        // ToDo валидация инициализируемого класса.

        $controller_method = $this->controller_method;


        switch($this->controller_name){
            case 'Category':
                if($this->controller_method != ''){
                    $M_Category =  new C_Category();
                    $M_Category->$controller_method($this->var);
                } else {
                    $M_Category =  new C_Category();
                    $M_Category->index($this->var);
                }

                break;

            default:

                if($this->controller_method != ''){

                    $C_Pay = new C_Pay();
                    $C_Pay->$controller_method($this->var);
                } else {
                    $C_Pay = new C_Pay();
                    $C_Pay->index();
                }
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