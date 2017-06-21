<?php
namespace bookkeeping\Models;

use bookkeeping\Controllers\Index;// as Pay;
use bookkeeping\Controllers\Pay;// as Pay;
use bookkeeping\Controllers\Income;// as Income;
use bookkeeping\Controllers\Category;// as Category;

final class Route {

    static $allow_controller_name = array(
        0 => 'Category',
        1 => 'Pay',
        2 => 'Income',
        3 => 'Index'
    );
    static $controller_name;
    static $controller_method;
    static $var;

    public function __construct($uri)
    {

        // clear get request
        $pos = strpos($uri,'?');

        if($pos > 0)
        {
            $uri = substr($uri, 0, $pos);
        }

        $uri = explode('/', $uri);

        // for google oauth on XAMPP
        // todo delete on hosting
        if($uri[1] == 'bookkeeping.com')
        {
            array_splice($uri,0, 1);
        }

        if(in_array(ucfirst($uri[1]), self::$allow_controller_name))
        {
            // для любого контроллера
            self::$controller_name = '' . ucfirst(str_replace('/', '', $uri)[1]);

            if(isset($uri[2]))
            {
                self::$controller_method = '' . ucfirst(str_replace('/', '', $uri)[2]);
            }

            if(isset($uri[3]))
            {
                self::$var = $uri[3];
            }

        } else {
            // дял дефолтного контроллера
            self::$controller_name = '';
            self::$controller_method = '' . str_replace('/', '', $uri)[1];

            if(isset($uri[2]))
            {
                self::$var = $uri[2];
            }

        }
    }

    public static function start()
    {
        // ToDo валидация инициализируемого класса.
        $controller_method = self::$controller_method;

        if(in_array(self::$controller_name, self::$allow_controller_name))
        {
            $controller_name = trim(self::$controller_name);

            switch(self::$controller_name){
                case 'Category':
                    $C_Controller = new Category();
                    break;
                case 'Pay':
                    $C_Controller = new Pay();
                    break;
                case 'Income':
                    $C_Controller = new Income();
                    break;
                case 'Index':
                    $C_Controller = new Index();
                    break;
            }

            if(self::$controller_method != '')
            {
                if(method_exists($C_Controller, $controller_method))
                {
                    $C_Controller->$controller_method(self::$var);
                } else {
                    $C_Controller->index();
                }
            } else {
                $C_Controller->index(self::$var);
            }
        } else {
            if(self::$controller_method != '')
            {
                $C_Index = new Index();
                if(method_exists($C_Index, $controller_method))
                {
                    $C_Index->$controller_method(self::$var);
                } else {
                    $C_Index->index();
                }
            } else {
                $C_Index = new Index();
                $C_Index->index();
            }
        }
    }
}