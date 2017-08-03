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
    protected static $main_teamplate = 'Income';

    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        // Сохраненные настройки для контроллера.
        $M_IncomeSetting = new M_IncomeSetting($this->user->getId());

        // Проверка существования запроса на изменение настроек представления
        if(isset($_POST['settings']))
        {
            $M_IncomeSetting->prepareFormat($_POST);
            $M_IncomeSetting->update();
            header('Location: /' . Income::CONTROLLER_NAME . "/");

            /*
            $datatime_name = $_POST["format"];
            $M_IncomeSetting->date_start = $_POST[$datatime_name];
            $M_IncomeSetting->format = $_POST['format'];

            if(! $M_IncomeSetting->updateSettingByUserId())
            {
                // ошибки установки настроек контроллера
                $data['error'] = array(
                    'error' => true,
                    'text' => 'Не удалось внести настройки выбора по дате.  Загружены данные по умоляванию'
                );
            } else {
                header('Location: /' . Income::CONTROLLER_NAME . "/");
            }*/

        }

        if(!empty($_POST) && isset($_POST['category_id']))
        {
            $M_Income =  new M_Income();
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

        // настройки представления
        $data['settings'] = $M_IncomeSetting;

        // загрузка всех платежей текущего месяца
        $data['incomes'] = M_Income::getAll(  $this->user->getId(),
            $M_IncomeSetting->date_start,
            $M_IncomeSetting->date_end);

        // загрузка всех категорий расходов
        $data['categories'] = M_Category::getAll($this->user->getId(), self::CONTROLLER_NAME);
        $data['controller_name'] = self::getMainTeamplate();
        $this->content = $this->getView(self::getMainTeamplate() . '/Index.php', $data);
        $this->render();
    }

    /**
     * Редактирование записи (Платёж)
     * @param $id
     *
     */
    function edit($id)
    {
        $M_Income =  M_Income::getById($id)[0];

        if(empty($M_Income))
        {
            $data['error'] =  array(
                'error' => true,
                'text' => 'Данный платеж не существует'
            );
        } else {

            if ($M_Income->user_id != $this->user->getId())
            {
                $data['error'] = array(
                    'error' => true,
                    'text' => 'Доступ к данной записи закрыт для вас'
                );
            } else {

                if(!empty($_POST) && $_POST['category_id'] != '')
                {
                    $M_Income->amount = $_POST['amount'];
                    $M_Income->description = $_POST['description'];
                    $M_Income->category_id = $_POST['category_id'];
                    $M_Income->user_id = $this->user->getId();
                    $M_Income->date = $_POST['date'];

                    if($M_Income->update())
                    {
                        header("Location: /" . self::getMainTeamplate());
                        exit();
                    }
                }

                $data['income'] = $M_Income;

                // id текущего авторизированного пользователя
                $user_id = $this->user->getId();

                // Категории заданного типа (Расходы | Доходы | другое)
                $type_of_category = self::CONTROLLER_NAME;

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

        $M_Income =  M_Income::getById($id)[0];

        if(empty($M_Income))
        {
            $data['error'] =  array(
                'error' => true,
                'text' => 'Данный платеж не существует'
            );
        } else {
            if ($M_Income->user_id != $this->user->getId())
            {
                $data['error'] = array(
                    'error' => true,
                    'text' => 'Доступ к данной записи закрыт для вас'
                );
            } else {
                $data['income'] = $M_Income;

                if(!empty($_POST) && $_POST['id'] != '')
                {
                    if($M_Income->delete()){
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