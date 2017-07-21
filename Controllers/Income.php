<?php
namespace bookkeeping\Controllers;
use bookkeeping\Controllers\Controller as Controller;
use bookkeeping\Models\Settings\IncomeSetting as M_IncomeSetting;
use bookkeeping\Models\Income as M_Income;
use bookkeeping\Models\Category as M_Category;

class Income
    extends Controller
{
    const CONTROLLER_NAME = 'Income';
    protected static $main_teamplate = 'Income';

    function __construct()
    {
        parent::__construct();
        
        // Проверка существования запроса на изменение настроек представления
        if(isset($_POST['settings']))
        {
            $this->setSetting();
        }
        
    }

    public function setSetting()
    {



        $M_IncomeSetting = new M_IncomeSetting();
        $M_IncomeSetting->user_id = $this->user->getId();

        // изменения параметров представления контроллера
        $result =  $M_IncomeSetting->setFormat();

        if(! $result)
        {
            // todo записать в лог.
            $M_IncomeSetting->error_validation;
        }

        header("Location: /" . self::getMainTeamplate());
        exit();
    }

    function isPost($action)
    {
        $M_Income = new M_Income();

        if($M_Income->$action())
        {
            return true;
        } else {
            return false;
        }
    }

    function index()
    {
        $M_Income = new M_Income();
        $M_IncomeSetting = new M_IncomeSetting();

        if(!empty($_POST) && $_POST['category_id'] != '')
        {

            $M_Income->amount = $_POST['amount'];
            $M_Income->description = $_POST['description'];
            $M_Income->category_id = $_POST['category_id'];
            $M_Income->user_id = $this->user->getId();
            $M_Income->date = $_POST['date'];

            if($M_Income->create())
            {
                header("Location: /" . self::getMainTeamplate());
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $M_Income->error_validation;
        }

        // параметры контроллера
        $M_IncomeSetting->user_id = $this->user->getId();
        $data['settings'] = $M_IncomeSetting->getSettings();

        print_r($data['settings']);

        // загрузка всех платежей текущего месяца
        $data['incomes'] = M_Income::getAll($this->user->getId(),
                                            $data['settings']->date_start,
                                            $data['settings']->date_end);

        // загрузка всех категорий расходов
        $data['categories'] =  (new M_Category($this->user->getId()))->getAll($this->user->getId(), self::CONTROLLER_NAME);

        $this->render($data);
    }

    function edit($id)
    {
        if(!empty($_POST) && $_POST['category_id'] != '')
        {
            if($this->isPost('update')){
                header("Location: /" . self::getMainTeamplate());
                exit();
            }
        }

        $income = M_Income::getById($id);

        if(empty($income))
        {
            $data['error'] =  array(
                'error' => true,
                'text' => 'Данный платеж не существует'
            );
        } else {
            $income = (object) $income;

            if ($income->user_id != $this->user->getId()) {
                $data['error'] = array(
                    'error' => true,
                    'text' => 'Доступ к данной записи закрыт для вас'
                );
            } else {

                $data['income'] = $income;

                // id текущего авторизированного пользователя
                $user_id = $this->user->getId();

                // Категории заданного типа (Расходы | Доходы | другое)
                $type_of_category = self::CONTROLLER_NAME;

                // загрузка всех категорий расходов
                $data['categories'] = M_Category::getAll($user_id, $type_of_category);
            }
        }

        $this->render($data, 'Edit');
    }

    /**
     * @param $id
     */
    function delete($id)
    {
        if(!empty($_POST) && $_POST['id'] != '')
        {
            if($this->isPost('delete'))
            {
                header("Location: /" . self::getMainTeamplate());
                exit();
            }
        }

        $income = M_Income::getById($id);

        if(empty($income))
        {
            $data['error'] =  array(
                'error' => true,
                'text' => 'Данный платеж не существует'
            );
        } else {
            $income = (object) $income;

            if ($income->user_id != $this->user->getId()) {
                $data['error'] = array(
                    'error' => true,
                    'text' => 'Доступ к данной записи закрыт для вас'
                );
            } else {

                $data['income'] = $income;

                // id текущего авторизированного пользователя
                $user_id = $this->user->getId();

                // Категории заданного типа (Расходы | Доходы | другое)
                $type_of_category = self::CONTROLLER_NAME;

                // загрузка всех категорий расходов
                $data['categories'] = M_Category::getAll($user_id, $type_of_category);
            }
        }

        $this->render($data, 'Delete');
    }
}