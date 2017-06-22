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

    // Сохраненный пользователь из БД найденный по id и авторизированный
    protected $user;

    function __construct()
    {
        $this->M_User = new M_User();

        // auth
        if(isset($_SESSION['user_id']))
        {
            if(! $this->M_User->checkToken($_SESSION['user_id'], $_SESSION['token']))
            {
                echo "no auth";
                //header('Location: /');
            } else {
                $this->user = $this->M_User->getById($_SESSION['user_id']);
            }

        } else {
            echo "no auth";
            //header('Location: /');
        }
    }

    public static function getMainTeamplate()
    {
        return static::$main_teamplate;
    }



    function render(array $data, $view = 'Index')
    {
        foreach ($data as $k => $v) {
            $$k = $v;
        }


        // переменная для указания активного пункта главного меню
        $current_page = self::getMainTeamplate();
        $controller_name = self::getMainTeamplate();

        $user = $this->user;

        //return include (__DIR__ . '\..\View\\' . $this->main_teamplate . '\\' . $view . '.php');
        return include (__DIR__ . '/../View/' . self::getMainTeamplate() . '/' . $view . '.php');
    }
}