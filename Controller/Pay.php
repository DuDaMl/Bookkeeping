<?php
namespace bookkeeping\Controller;
use bookkeeping\Controller\Controller as Controller;
use bookkeeping\Model\Setting\Setting;
use bookkeeping\Model\User;
use bookkeeping\Model\Setting\PaySetting as M_PaySetting;
use bookkeeping\Model\Pay as M_Pay;
use bookkeeping\Model\Category as M_Category;
use bookkeeping\Model\Views\View as M_View;

class Pay
    extends Controller
{
    const CONTROLLER_NAME = 'Pay';

    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        // Объек хранящий настройки представления контроллера
         $Setting = Setting::getInstance(static::CONTROLLER_NAME);

        // Обновление настроек контроллера
        if($Setting->update($_POST))
        {
            header('Location: /' . static::CONTROLLER_NAME . "/");
            exit();
        }

        if(! empty($_POST) && isset($_POST['category_id']))
        {
            $M_Pay = new M_Pay();

            // Создание записи расходов.
            if($M_Pay->create($_POST))
            {
                header("Location: /" . static::CONTROLLER_NAME . "/");
                exit();
            }
        }

        // Создание объекта представления для контроллера
        $M_View = M_View::getInstance(static::CONTROLLER_NAME);
        $M_View->user = $this->user;
        $M_View->index($Setting);
    }

    /**
     * Редактирование записи (Платёж)
     * @param $id
     */
    // todo права на редактирование данной записи
    // todo существование данной записи
    function edit(int $id)
    {
        $M_Pay =  M_Pay::getById($id)[0];

        if(isset($_POST['category_id']) && $_POST['category_id'] != '')
        {
            if($M_Pay->update($_POST))
            {
                header("Location: /" . static::CONTROLLER_NAME);
                exit();
            }
        }


        // Создание объекта представления для контроллера
        $M_View = M_View::getInstance(static::CONTROLLER_NAME);
        $M_View->user = $this->user;
        $M_View->pay = $M_Pay;
        $M_View->edit();
    }

    /**
     * @param $id
     */
    function delete($id)
    {
        // todo права на редактирование данной записи
        // todo существование данной записи
        $M_Pay =  M_Pay::getById($id)[0];

        if(!empty($_POST) && $_POST['id'] != '')
        {
            if($M_Pay->delete()){
                header("Location: /" . static::CONTROLLER_NAME);
                exit();
            }
        }

        $M_View = M_View::getInstance(static::CONTROLLER_NAME);
        $M_View->user = $this->user;
        $M_View->pay = $M_Pay;
        $M_View->delete();

    }
}