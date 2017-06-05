<?php
namespace bookkeeping\Models;

use bookkeeping\Controllers\Pay as C_Pay;
use bookkeeping\Controllers\Category as C_Category;

final class Route {

    static $controller_name;
    static $controller_method;
    static $var;

    public function __construct($uri)
    {
        $allow_controller_name = array(
            0 => 'Category'
        );

        $uri = explode('/', $uri);

        if(in_array($uri[1],$allow_controller_name))
        {
            // для любого контроллера
            self::$controller_name = '' . ucfirst(str_replace('/', '', $uri)[1]);
            self::$controller_method = '' . ucfirst(str_replace('/', '', $uri)[2]);
        } else {
            // дял дефолтного контроллера
            self::$controller_name = '';
            self::$controller_method = '' . str_replace('/', '', $uri)[1];
            self::$var = $uri[2];
        }
    }

    public static function start()
    {
        // ToDo валидация инициализируемого класса.
        $controller_method = self::$controller_method;

        switch(self::$controller_name){
            case 'Category':
                if(self::$ontroller_method != '')
                {
                    $M_Category =  new C_Category();
                    $M_Category->$controller_method(self::$var);
                } else {
                    $M_Category =  new C_Category();
                    $M_Category->index(self::$var);
                }

                break;

            default:

                if(self::$controller_method != '')
                {
                    $C_Pay = new C_Pay();
                    if(method_exists($C_Pay, $controller_method))
                    {
                        $C_Pay->$controller_method(self::$var);
                    } else {
                        $C_Pay->index();
                    }
                } else {
                    $C_Pay = new C_Pay();
                    $C_Pay->index();
                }
                break;
        }
    }
}