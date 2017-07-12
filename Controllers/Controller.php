<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 08.06.2017
 * Time: 20:02
 */

namespace bookkeeping\Controllers;
use bookkeeping\Models\User as M_User;

class Controller
{
    protected static $main_teamplate;

    // Auth User
    protected  static $current_user_id;

    function __construct()
    {
        //$this->M_User = new M_User();

        // auth
        if(! M_User::checkAuth())
        {
            echo "<h1>no auth</h1>";
            //header('Location: /');
        } else {
            static::$current_user_id = M_User::getUserId();
        }
    }

    public static function getMainTeamplate()
    {
        return static::$main_teamplate;
    }

    public static function getCurrentUserId()
    {
        return static::$current_user_id;
    }

    function render(array $data, $view = 'Index')
    {
        foreach ($data as $k => $v) {
            $$k = $v;
        }

        // переменная для указания активного пункта главного меню
        $current_page = self::getMainTeamplate();
        $controller_name = self::getMainTeamplate();
        $user = M_User::getById(self::getCurrentUserId());

        return include (__DIR__ . '/../View/' . self::getMainTeamplate() . '/' . $view . '.php');
    }

    function getView(string $path, array $params)
    {
        ob_start();

        foreach ($params as $k => $v) {
            $$k = $v;
        }

        include ($path);
        return ob_get_clean();
    }
}