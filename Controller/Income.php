<?php
namespace bookkeeping\Controller;
use bookkeeping\Controller\Controller as Controller;
use bookkeeping\Model\User as M_User;
use bookkeeping\Model\Setting\IncomeSetting as M_IncomeSetting;
use bookkeeping\Model\Income as M_Income;
use bookkeeping\Model\Category as M_Category;

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
        // Сохраненные настройки для контроллера.
        $Setting = new M_IncomeSetting();

        // Проверка существования запроса на изменение настроек представления
        if(isset($_POST['settings']))
        {
            $Setting->update($_POST);
            header('Location: /' . static::CONTROLLER_NAME . "/");
            exit();
        }

        if(!empty($_POST) && isset($_POST['category_id']))
        {
            $M_Income =  new M_Income();

            if($M_Income->create($_POST))
            {
                header("Location: /" . static::CONTROLLER_NAME);
                exit();
            }

            // todo обработка ошибок возникшик при создании записи
        }

        // настройки представления
        $data['settings'] = $Setting;

        // загрузка всех платежей текущего месяца
        $data['incomes'] = M_Income::getAll($Setting);

        // загрузка всех категорий расходов
        $data['categories'] = M_Category::getAll( self::CONTROLLER_NAME);
        $data['controller_name'] = static::CONTROLLER_NAME;
        $this->content = $this->getView(static::CONTROLLER_NAME . '/Index.php', $data);
        $this->render();
    }

    /**
     * Редактирование записи (Платёж)
     * @param $id
     *
     */
    function edit($id)
    {
        // todo права на редактирование данной записи
        // todo существование данной записи
        $M_Income =  M_Income::getById($id)[0];

        if(!empty($_POST) && $_POST['category_id'] != '')
        {

            if($M_Income->update($_POST))
            {
                header("Location: /" . static::CONTROLLER_NAME);
                exit();
            }
        }

        $data['income'] = $M_Income;

        // Категории заданного типа (Расходы | Доходы | другое)
        $type_of_category = self::CONTROLLER_NAME;

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
        $M_Income =  M_Income::getById($id)[0];


        if(!empty($_POST) && $_POST['id'] != '')
        {
            if($M_Income->delete()){
                header("Location: /" . static::CONTROLLER_NAME);
                exit();
            }
        }


        $data['income'] = $M_Income;
        $data['controller_name'] = static::CONTROLLER_NAME;
        $this->content = $this->getView(static::CONTROLLER_NAME . '/Delete.php', $data);
        $this->render();
    }
}