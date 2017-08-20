<?php
namespace bookkeeping\Controller;
use bookkeeping\Controller\Controller as Controller;
use bookkeeping\Model\User as M_User;
use bookkeeping\Model\Setting\Setting;
use bookkeeping\Model\Income as M_Income;
use bookkeeping\Model\Category as M_Category;
use bookkeeping\Model\Views\View as M_View;
use bookkeeping\Model\Exceptions\MultiException;
use bookkeeping\Model\Exceptions\DateNotFilledException;


class Income
    extends Controller
{
    const CONTROLLER_NAME = 'Income';

    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        // Сборщик ошибок
        $errors = new MultiException();

        // Объек хранящий настройки представления контроллера
        $Setting = Setting::getInstance(static::CONTROLLER_NAME);

        // Проверка существования запроса на изменение настроек представления
        // Обновление настроек контроллера
        if($Setting->update($_POST))
        {
            header('Location: /' . static::CONTROLLER_NAME . "/");
            exit();
        }

        $M_Income =  new M_Income();

        // Создание записи расходов.
        if (! empty($_POST['add']))
        {
            try {
                $M_Income->create($_POST);
                header("Location: /" . static::CONTROLLER_NAME . "/");
                exit();
            } catch (DateNotFilledException $e){
                $errors[] = $e;
            } catch (\ArgumentCountError  $e){
                // todo Ошибка обращения к классу. Записать в Лог администратора
            }
        }

        // Создание объекта представления для контроллера
        $M_View = M_View::getInstance(static::CONTROLLER_NAME);
        $M_View->user = $this->user;
        $M_View->errors = $errors;
        $M_View->index($Setting);


    }

    /**
     * Редактирование записи (Платёж)
     * @param $id
     *
     */
    // todo права на редактирование данной записи
    // todo существование данной записи
    function edit($id)
    {
        $M_Income =  M_Income::getById($id)[0];

        if($M_Income->update($_POST))
        {
            header("Location: /" . static::CONTROLLER_NAME);
            exit();
        }

        // Создание объекта представления для контроллера
        $M_View = M_View::getInstance(static::CONTROLLER_NAME);
        $M_View->user = $this->user;
        $M_View->income = $M_Income;
        $M_View->edit();


    }

    /**
     * @param $id
     */
    function delete($id)
    {
        // todo права на редактирование данной записи
        // todo существование данной записи
        $M_Income =  M_Income::getById($id)[0];


        if(!empty($_POST) && $_POST['id'] != '')
        {
            if($M_Income->delete()){
                header("Location: /" . static::CONTROLLER_NAME);
                exit();
            }
        }

        $M_View = M_View::getInstance(static::CONTROLLER_NAME);
        $M_View->user = $this->user;
        $M_View->income = $M_Income;
        $M_View->delete();
    }
}