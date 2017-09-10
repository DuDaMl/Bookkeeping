<?php
namespace bookkeeping\Controller;
use bookkeeping\Model\Setting\Setting;
use bookkeeping\Model\Pay as M_Pay;
use bookkeeping\Model\Views\View as M_View;
use bookkeeping\Model\Exceptions\MultiException;
use bookkeeping\Model\Exceptions\DateNotFilledException;

class Pay extends Controller
{
    const CONTROLLER_NAME = 'Pay';

    function __construct()
    {
        parent::__construct();

    }

    /**
     * Отображение статистика расходов
     */
    function index()
    {
        // Сборщик ошибок
        $errors = new MultiException();

        // Объек хранящий настройки представления контроллера
        $Setting = Setting::getInstance(static::CONTROLLER_NAME);

        // Обновление настроек контроллера Pay
        if ($Setting->update($_POST))
        {
            header('Location: /' . static::CONTROLLER_NAME . "/");
            exit();
        }

        $M_Pay = new M_Pay();

        // Создание записи расходов.
        if (! empty($_POST['add']))
        {
            try {
                $M_Pay->create($_POST);
                header("Location: /" . static::CONTROLLER_NAME . "/");
                exit();
                } catch (DateNotFilledException $e){
                $errors[] = $e;
                } catch (\ArgumentCountError  $e){
                    // todo Ошибка обращения к классу. Записать в Лог администратора (неверный тип передаваемых параметров)
            }
        }

        // Создание объекта представления для контроллера
        $M_View = M_View::getInstance(static::CONTROLLER_NAME);
        $M_View->user = $this->user;
        $M_View->errors = $errors;
        $M_View->index($Setting);
    }

    /**
     * Редактирование записи (Расход)
     * @param $id
     */
    // todo права на редактирование данной записи
    // todo существование данной записи
    function edit(int $id)
    {
        $M_Pay =  M_Pay::getById($id)[0];

        // обновление данных о платеже
        if($M_Pay->update($_POST))
        {
            header("Location: /" . static::CONTROLLER_NAME);
            exit();
        }

        // Создание объекта представления для контроллера
        $M_View = M_View::getInstance(static::CONTROLLER_NAME);
        $M_View->user = $this->user;
        $M_View->pay = $M_Pay;
        $M_View->edit();
    }

    /**
     * Удаление записи (Расход)
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

        $M_View = M_View::getInstance(static::CONTROLLER_NAME);
        $M_View->user = $this->user;
        $M_View->pay = $M_Pay;
        $M_View->delete();

    }
}