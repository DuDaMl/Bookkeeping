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

    /**
     * получение данных даты отчета контроллера
     * @return object
     */
    function getSettings()
    {
        $M_PaySetting = new M_PaySetting(self::getCurrentUserId());

        // загрузка параметров контроллера
        $params = $M_PaySetting->getSettings();
        return $params;
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

        // параметры контроллера
        $data['settings'] =  $this->getSettings();

        // загрузка всех платежей текущего месяца
        $data['pays'] = $this->M_Pay->getAll($data['settings']->date_start, $data['settings']->date_end);

        // загрузка всех категорий расходов
        $data['categories'] =  (new M_Category(self::getCurrentUserId()))->getAll();
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

        $data['pay'] = $this->M_Pay->getById($id);
        $data['error'] = $this->M_Pay->error_validation;

        if(empty($data['pay']))
        {
            if(! empty($data['error']))
            {
                $data['error']['text'] = $data['error']['text'] . ' <br/> нет такой записи';
            } else {
                $data['error'] =  array(
                    'error' => true,
                    'text' => 'нет такой записи',
                );
            }
        }

        $M_Category = new M_Category(self::getCurrentUserId());
        $data['categories'] = $M_Category->getAll();
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

        $data['pay'] = $this->M_Pay->getById($id);
        $data['error'] = $this->M_Pay->error_validation;

        if(empty($data['pay']))
        {
            if(! empty($data['error']))
            {
                $data['error']['text'] = $data['error']['text'] . ' <br/> нет такой записи';
            } else {
                $data['error'] =  array(
                    'error' => true,
                    'text' => 'нет такой записи',
                );
            }
        }

        $this->render($data, 'Delete');
    }
}