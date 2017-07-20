<?php
namespace bookkeeping\Controllers;
use bookkeeping\Models\User as User;

class Controller
{
    protected static $main_teamplate;

    protected $user;

    function __construct()
    {
        $this->user = new User();
        $this->checkAuth();
    }

    /**
     * @return bool
     */
    function checkAuth()
    {
        if(! isset($_SESSION['user_id']) || ! isset($_SESSION['token']))
        {
            echo "<h1>no auth</h1>";
            //header('Location: /');
        }

        // auth
        if(! $this->user->checkToken($_SESSION['user_id'], $_SESSION['token']))
        {
            echo "<h1>no auth</h1>";
            //header('Location: /');
        } else {

            $token = $this->user->updateToken();

            // Создание нового токена
            if($token)
            {
                $_SESSION['token'] =  $token;
            } else {
                //todo some error exception
            }
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