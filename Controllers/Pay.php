<?php
namespace bookkeeping\Controllers;
use bookkeeping\Controllers\Controller as Controller;
use bookkeeping\Models\User as M_User;
use bookkeeping\Models\Settings\PaySetting as M_PaySetting;
use bookkeeping\Models\Pay as M_Pay;
use bookkeeping\Models\Category as M_Category;

class Pay
    extends Controller
{
    const CONTROLLER_NAME = 'Pay';
    protected static $main_teamplate = 'Pay';
    private $M_Pay;

    function __construct()
    {
        parent::__construct();
        $this->M_Pay = new M_Pay(self::getCurrentUserId());

        // Проверка существования запроса на изменение настроек представления
        if(isset($_POST['settings']))
        {
            $this->setSetting();
        }
    }

    /**
     * установка времени отчета контроллера
     */
    public function setSetting()
    {
        $M_PaySetting = new M_PaySetting(self::getCurrentUserId());

        // изменения параметров представления контроллера
        $result =  $M_PaySetting->setFormat();

        if(! $result)
        {
            // todo записать в лог.
            $M_PaySetting->error_validation;
        }

        header("Location: /" . self::getMainTeamplate());
        exit();
    }

    function index()
    {
        if(!empty($_POST) && $_POST['category_id'] != '')
        {
            if($this->M_Pay->create())
            {
                header("Location: /" . self::getMainTeamplate());
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $this->M_Pay->error_validation;
        }

        // id текущего авторизированного пользователя
        $user_id = self::getCurrentUserId();

        // Категории заданного типа (Расходы | Доходы | другое)
        $type_of_category = self::CONTROLLER_NAME;

        // параметры контроллера
        $data['settings'] =  (object) M_PaySetting::getSettings($user_id);

        // загрузка всех платежей текущего месяца
        $data['pays'] = M_Pay::getAll($user_id,
                                      $data['settings']->date_start,
                                      $data['settings']->date_end);

        // загрузка всех категорий расходов
        $data['categories'] = M_Category::getAll($user_id, $type_of_category);

        $this->render($data);
    }

    function edit($id)
    {
        if(!empty($_POST) && $_POST['category_id'] != ''){
            if($this->M_Pay->update()){
                header("Location: /" . self::getMainTeamplate());
                exit();
            }
        }

        $pay =  M_Pay::getById($id);

        if(empty($pay))
        {
            $data['error'] =  array(
                'error' => true,
                'text' => 'Данный платеж не существует'
            );
        } else {
            $pay = (object)$pay;

            if ($pay->user_id != self::getCurrentUserId()) {
                $data['error'] = array(
                    'error' => true,
                    'text' => 'Доступ к данной записи закрыт для вас'
                );
            } else {

                $data['pay'] = $pay;

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
            if($this->M_Pay->delete()){
                header("Location: /" . self::getMainTeamplate());
                exit();
            }
        }

        $pay =  M_Pay::getById($id);

        if(empty($pay))
        {
            $data['error'] =  array(
                'error' => true,
                'text' => 'Данный платеж не существует'
            );
        } else {
            $pay = (object)$pay;

            if ($pay->user_id != self::getCurrentUserId()) {
                $data['error'] = array(
                    'error' => true,
                    'text' => 'Доступ к данной записи закрыт для вас'
                );
            } else {

                $data['pay'] = $pay;

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