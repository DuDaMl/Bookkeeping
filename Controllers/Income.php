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
    private $M_Income;

    function __construct()
    {
        parent::__construct();
        $this->M_Income = new M_Income(self::getCurrentUserId());

        // Проверка существования запроса на изменение настроек представления
        if(isset($_POST['settings']))
        {
            $this->setSetting();
        }
    }

    public function setSetting()
    {
        $M_IncomeSetting = new M_IncomeSetting(self::getCurrentUserId());

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
        if($this->M_Income->$action())
        {
            return true;
        } else {
            return false;
        }
    }

    function index()
    {
        if(!empty($_POST) && $_POST['category_id'] != '')
        {
            if($this->isPost('create'))
            {
                header("Location: /" . self::getMainTeamplate());
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $this->M_Income->error_validation;
        }

        // id текущего авторизированного пользователя
        $user_id = self::getCurrentUserId();

        // параметры контроллера
        $data['settings'] =  (object) M_IncomeSetting::getSettings($user_id);

        // загрузка всех платежей текущего месяца
        $data['incomes'] = M_Income::getAll($user_id,
                                            $data['settings']->date_start,
                                            $data['settings']->date_end);

        // загрузка всех категорий расходов
        $data['categories'] =  (new M_Category(static::$current_user_id))->getAll(self::getCurrentUserId(), self::CONTROLLER_NAME);

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

            if ($income->user_id != self::getCurrentUserId()) {
                $data['error'] = array(
                    'error' => true,
                    'text' => 'Доступ к данной записи закрыт для вас'
                );
            } else {

                $data['income'] = $income;

                // id текущего авторизированного пользователя
                $user_id = self::getCurrentUserId();

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

            if ($income->user_id != self::getCurrentUserId()) {
                $data['error'] = array(
                    'error' => true,
                    'text' => 'Доступ к данной записи закрыт для вас'
                );
            } else {

                $data['income'] = $income;

                // id текущего авторизированного пользователя
                $user_id = self::getCurrentUserId();

                // Категории заданного типа (Расходы | Доходы | другое)
                $type_of_category = self::CONTROLLER_NAME;

                // загрузка всех категорий расходов
                $data['categories'] = M_Category::getAll($user_id, $type_of_category);
            }
        }

        $this->render($data, 'Delete');
    }
}