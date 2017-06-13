<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 08.06.2017
 * Time: 20:02
 */

namespace bookkeeping\Controllers;
use bookkeeping\Models\User as M_User;

abstract class Controller
{
    protected $main_teamplate;

    function __construct()
    {

        //echo " __construct Controller Controller <br/>";

        $this->M_User = new M_User();

        // auth
        if(isset($_SESSION['user_id']))
        {
            if(! $this->M_User->checkSession($_SESSION['user_id'], $_SESSION['token']))
            {
                // todo not auth user;
                echo 'not auth user' . '<br/>';
            }

        } else {
            // todo not auth user;
            echo 'not auth user' . '<br/>';
        }

        print_r($_SESSION);

    }

    function render($data, $view = 'Index')
    {
        foreach ($data as $k => $v) {
            $$k = $v;
        }

        // переменная для указания активного пункта главного меню
        $current_page = $this->main_teamplate;
        //echo get_class();
        $controller_name = $this->main_teamplate;

        return include (__DIR__ . '\..\View\\' . $this->main_teamplate . '\\' . $view . '.php');
    }
}