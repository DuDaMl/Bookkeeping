<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 08.06.2017
 * Time: 20:02
 */

namespace bookkeeping\Controllers;


abstract class Controller
{
    protected $main_teamplate;

    function __construct()
    {
        echo " __construct Controller Controller <br/>";
    }

    function render($data, $view = 'Index')
    {


        foreach ($data as $k => $v) {
            $$k = $v;
        }

        // переменная для указания активного пункта главного меню
        $current_page = $this->main_teamplate;

        return include (__DIR__ . '\..\View\\' . $this->main_teamplate . '\\' . $view . '.php');
    }
}