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

    // todo обработка ошибок возникшик при создании записи
    function index()
    {
        // Настройки для контроллера.
        //$M_PaySetting = new M_PaySetting();

        $M_PaySetting = Setting::getInstance(static::CONTROLLER_NAME);


        // Проверка существования запроса на изменение настроек представления
        if(isset($_POST['settings']))
        {
            $M_PaySetting->update($_POST);
            header('Location: /' . static::CONTROLLER_NAME . "/");
            exit();
        }

        if(!empty($_POST) && isset($_POST['category_id']))
        {
            $M_Pay = new M_Pay();

            if($M_Pay->create($_POST))
            {
                header("Location: /" . static::CONTROLLER_NAME . "/");
                exit();
            }
        }

        $M_View = new M_View();
        $M_View->settings = $M_PaySetting;
        $M_View->pays = M_Pay::getAll($M_PaySetting);
        $M_View->categories = M_Category::getAll(static::CONTROLLER_NAME);
        $M_View->controller_name = static::CONTROLLER_NAME;
        $M_View->current_page = static::CONTROLLER_NAME;
        $M_View->user = $this->user;
        $M_View->content = $M_View->render(static::CONTROLLER_NAME . '/Index.php');
        $M_View->display();
        exit();

        /*
        // настройки представления
        $data['settings'] = $M_PaySetting;

        // загрузка всех платежей текущего месяца
        // todo перенести метод в другую модель
        $data['pays'] = M_Pay::getAll($M_PaySetting);

        // загрузка всех категорий расходов
        $data['categories'] = M_Category::getAll(static::CONTROLLER_NAME);
        $data['controller_name'] = static::CONTROLLER_NAME;
        $this->content = $this->getView(static::CONTROLLER_NAME . '/Index.php', $data);
        $this->render();
        */
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

        $data['pay'] = $M_Pay;

        // Категории заданного типа (Расходы | Доходы | другое)
        $type_of_category = static::CONTROLLER_NAME;

        // загрузка всех категорий расходов
        $data['categories'] = M_Category::getAll($type_of_category);

        $data['controller_name'] = static::CONTROLLER_NAME;
        $this->content = $this->getView(static::CONTROLLER_NAME . '/Edit.php', $data);
        $this->render();
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

        $data['pay'] = $M_Pay;
        $data['controller_name'] = static::CONTROLLER_NAME;
        $this->content = $this->getView(static::CONTROLLER_NAME . '/Delete.php', $data);
        $this->render();
    }
}