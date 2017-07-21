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

    function __construct()
    {
        parent::__construct();

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
        //Array ( [settings] => 1 [day] => 2017-07-21 [format] => month [month] => 2017-07 [year] => 2017 )
        $M_PaySetting = new M_PaySetting();
        $M_PaySetting->user_id = $this->user->getId();
        $M_PaySetting->format = $_POST['format'];
        $date = $_POST[$M_PaySetting->format];

        if($M_PaySetting->prepareFormat($date))
        {
            // изменения параметров представления контроллера
            if(! $M_PaySetting->setFormat())
            {
                // todo записать в лог.
                $M_PaySetting->error_validation;
            } else {
                header("Location: /" . self::getMainTeamplate());
                exit();
            }
        }

    }

    function index()
    {
        $M_PaySetting = new M_PaySetting();

        if(!empty($_POST) && $_POST['category_id'] != '')
        {
            $M_Pay =  new M_Pay();
            $M_Pay->amount = $_POST['amount'];
            $M_Pay->description = $_POST['description'];
            $M_Pay->category_id = $_POST['category_id'];
            $M_Pay->user_id = $this->user->getId();
            $M_Pay->date = $_POST['date'];

            if($M_Pay->create())
            {
                header("Location: /" . self::getMainTeamplate());
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $M_Pay->error_validation;
        }

        // параметры контроллера
        $M_PaySetting->user_id = $this->user->getId();
        $M_PaySetting->getSettings();

        // настройки представления
        $data['settings'] = $M_PaySetting;

        // загрузка всех платежей текущего месяца
        $data['pays'] = M_Pay::getAll(  $M_PaySetting->user_id,
                                        $M_PaySetting->date_start,
                                        $M_PaySetting->date_end);

        // загрузка всех категорий расходов
        $data['categories'] = M_Category::getAll($this->user->getId(), self::CONTROLLER_NAME);
        $this->render($data);
    }

    function edit($id)
    {

        $M_Pay =  M_Pay::getById($id)[0];

        if(empty($M_Pay))
        {
            $data['error'] =  array(
                'error' => true,
                'text' => 'Данный платеж не существует'
            );
        } else {

            if ($M_Pay->user_id != $this->user->getId())
            {
                $data['error'] = array(
                    'error' => true,
                    'text' => 'Доступ к данной записи закрыт для вас'
                );
            } else {
                if(!empty($_POST) && $_POST['category_id'] != '')
                {
                    $M_Pay->amount = $_POST['amount'];
                    $M_Pay->description = $_POST['description'];
                    $M_Pay->category_id = $_POST['category_id'];
                    $M_Pay->user_id = $this->user->getId();
                    $M_Pay->date = $_POST['date'];

                    if($M_Pay->update())
                    {
                        header("Location: /" . self::getMainTeamplate());
                        exit();
                    }
                }

                $data['pay'] = $M_Pay;

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
        $M_Pay =  M_Pay::getById($id)[0];

        if(!empty($_POST) && $_POST['id'] != '')
        {
            if($M_Pay->delete()){
                header("Location: /" . self::getMainTeamplate());
                exit();
            }
        }

        //$M_Pay =  M_Pay::getById($id);

        if(empty($M_Pay))
        {
            $data['error'] =  array(
                'error' => true,
                'text' => 'Данный платеж не существует'
            );
        } else {
            if ($M_Pay->user_id != $this->user->getId())
            {
                $data['error'] = array(
                    'error' => true,
                    'text' => 'Доступ к данной записи закрыт для вас'
                );
            } else {
                $data['pay'] = $M_Pay;

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