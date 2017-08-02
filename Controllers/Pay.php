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
            header('Location: /' . Pay::CONTROLLER_NAME . "/");
        }

        if(!empty($_POST) && isset($_POST['category_id']))
        {
            $M_Pay = new M_Pay();
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
        //$M_PaySetting->user_id = $this->user->getId();

        // настройки представления
        $data['settings'] = $M_PaySetting;

        // загрузка всех платежей текущего месяца
        $data['pays'] = M_Pay::getAll(  $this->user->getId(),
                                        $M_PaySetting->date_start,
                                        $M_PaySetting->date_end);

        // загрузка всех категорий расходов
        $data['categories'] = M_Category::getAll($this->user->getId(), static::CONTROLLER_NAME);
        $data['controller_name'] = self::getMainTeamplate();

        $this->content = $this->getView(self::getMainTeamplate() . '/Index.php', $data);
        $this->render();
    }

    /**
     * Редактирование записи (Платёж)
     * @param $id
     *
     */
    function edit(int $id)
    {
        //echo $id; echo "<br/>";

        if(! is_int($id))
        {
            $ex = new \TypeError('id должно быть целове число.');
            throw $ex;
        }

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
                $type_of_category = static::CONTROLLER_NAME;

                // загрузка всех категорий расходов
                $data['categories'] = M_Category::getAll($user_id, $type_of_category);
            }
        }

        $data['controller_name'] = self::getMainTeamplate();
        $this->content = $this->getView(self::getMainTeamplate() . '/Edit.php', $data);
        $this->render();
    }

    /**
     * @param $id
     */
    function delete($id)
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
                $data['pay'] = $M_Pay;

                if(!empty($_POST) && $_POST['id'] != '')
                {
                    if($M_Pay->delete()){
                        header("Location: /" . self::getMainTeamplate());
                        exit();
                    }
                }

            }
        }

        $data['controller_name'] = self::getMainTeamplate();
        $this->content = $this->getView(self::getMainTeamplate() . '/Delete.php', $data);
        $this->render();
    }
}