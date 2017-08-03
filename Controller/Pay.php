<?php
namespace bookkeeping\Controller;
use bookkeeping\Controller\Controller as Controller;
use bookkeeping\Model\User as M_User;
use bookkeeping\Model\Setting\PaySetting as M_PaySetting;
use bookkeeping\Model\Pay as M_Pay;
use bookkeeping\Model\Category as M_Category;

class Pay
    extends Controller
{
    const CONTROLLER_NAME = 'Pay';
    protected static $main_teamplate = 'Pay';

    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        // Настройки для контроллера.
        $M_PaySetting = new M_PaySetting($this->user->getId());

        // Проверка существования запроса на изменение настроек представления
        if(isset($_POST['settings']))
        {
            $M_PaySetting->prepareFormat($_POST);
            $M_PaySetting->update();
            header('Location: /' . static::CONTROLLER_NAME . "/");
            exit();
        }

        if(!empty($_POST) && isset($_POST['category_id']))
        {
            $M_Pay = new M_Pay();
            $_POST['user_id'] = $this->user->getId();
            $M_Pay->prepareFormat($_POST);

            if($M_Pay->create())
            {
                header("Location: /" . static::CONTROLLER_NAME);
                exit();
            }
            // todo обработка ошибок возникшик при создании записи
        }

        // настройки представления
        $data['settings'] = $M_PaySetting;

        // загрузка всех платежей текущего месяца
        // todo перенести метод в другую модель
        $data['pays'] = M_Pay::getAll($M_PaySetting);

        // загрузка всех категорий расходов
        $data['categories'] = M_Category::getAll($this->user->getId(), static::CONTROLLER_NAME);
        $data['controller_name'] = static::CONTROLLER_NAME;

        $this->content = $this->getView(static::CONTROLLER_NAME . '/Index.php', $data);
        $this->render();
    }

    /**
     * Редактирование записи (Платёж)
     * @param $id
     */
    function edit(int $id)
    {
        $M_Pay =  M_Pay::getById($id)[0];

        if(isset($_POST['category_id']) && $_POST['category_id'] != '')
        {
            $_POST['user_id'] = $this->user->getId();
            $M_Pay->prepareFormat($_POST);

            if($M_Pay->update())
            {
                header("Location: /" . static::CONTROLLER_NAME);
                exit();
            }
        }

        $data['pay'] = $M_Pay;

        // id текущего авторизированного пользователя
        $user_id = $this->user->getId();

        // Категории заданного типа (Расходы | Доходы | другое)
        $type_of_category = static::CONTROLLER_NAME;

        // загрузка всех категорий расходов
        $data['categories'] = M_Category::getAll($user_id, $type_of_category);


        $data['controller_name'] = static::CONTROLLER_NAME;
        $this->content = $this->getView(static::CONTROLLER_NAME . '/Edit.php', $data);
        $this->render();
    }

    /**
     * @param $id
     */
    function delete($id)
    {
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