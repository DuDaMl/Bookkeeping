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
    protected $user;

    function __construct()
    {
        $this->M_User = new M_User();

        // auth
        if(isset($_SESSION['user_id']))
        {
            if(! $this->M_User->checkToken($_SESSION['user_id'], $_SESSION['token']))
            {
                // todo not auth user;
                echo 'not auth user' . '<br/>';
            } else {
                $this->user = $this->M_User->getById($_SESSION['user_id']);
            }

        } else {
            // todo not auth user;
            echo 'not auth user' . '<br/>';
        }
    }

    function render($data, $view = 'Index')
    {
        foreach ($data as $k => $v) {
            $$k = $v;
        }

        // переменная для указания активного пункта главного меню
        $current_page = $this->main_teamplate;
        $controller_name = $this->main_teamplate;

        $user = $this->user;

        return include (__DIR__ . '\..\View\\' . $this->main_teamplate . '\\' . $view . '.php');
    }
}