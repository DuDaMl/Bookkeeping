<?php
namespace bookkeeping\Controllers;
use bookkeeping\Controllers\Controller as Controller;
use bookkeeping\Models\Setting as M_Setting;
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
        $this->M_Pay = new M_Pay($this->user->id);
        $this->M_Setting = new M_Setting($this->user->id);
        $this->setting();
    }

    /**
     * установка времени отчета контроллера
     */
    public function setting()
    {
        if(isset($_POST['format']))
        {
            $format_value = $_POST['format'];

            switch($_POST['format'])
            {
                case 'day':
                    $date = $_POST['day'];
                    $data_report_start = $date;
                    $data_report_end = $date;
                    break;
                case 'month':
                    $date = $_POST['month'];
                    $data_report_start = $date . "-01";
                    $data_report_end = $date . "-31";
                    break;
                case 'year':
                    $date = $_POST['year'];
                    $data_report_start = $date . "-01-01";
                    $data_report_end = $date . "-12-31";
                    break;
                default:
                    $format_value = 'month';
                    $data_report_start = date('Y-m-01');
                    $data_report_end = date('Y-m-31');
                    break;
            }

            $params = array(
                'date_start' => $data_report_start,
                'date_end' => $data_report_end,
                'format' => $format_value
            );

            // изменения параметров представления контроллера
           $result =  $this->M_Setting->setFormat(self::getMainTeamplate(), $params);

            if(! $result)
            {
                // todo записать в лог.
                $this->M_Setting->error_validation;
            }

            header("Location: /" . self::getMainTeamplate());
            exit();

        }
    }

    /**
     * получение данных даты отчета контроллера
     * @return object
     */
    function getSettings()
    {
        // загрузка параметров контроллера
        $params = $this->M_Setting->getByController(self::getMainTeamplate());

        // если данных нет, то загрузка данных по умолчанию
        if(empty($params))
        {
            $data_params = array(
                'date_start' => date('Y-m-01'),
                'date_end' => date('Y-m-31'),
                'format' => 'month'
            );
        } else {
            $data_params = unserialize($params->value);
        }

       return (object) $data_params;
    }

    function isPost($action)
    {
        if($this->M_Pay->$action())
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
            $data['error'] = $this->M_Pay->error_validation;
        }

        // параметры контроллера
        $data['settings'] =  $this->getSettings();

        // загрузка всех платежей текущего месяца
        $data['pays'] = $this->M_Pay->getAll($data['settings']->date_start, $data['settings']->date_end);

        // загрузка всех категорий расходов
        $data['categories'] =  (new M_Category($this->user->id))->getAll();
        $this->render($data);
    }

    function edit($id)
    {
        if(!empty($_POST) && $_POST['category_id'] != ''){
            if($this->isPost('update')){
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

        $M_Category = new M_Category($this->user->id);
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
            if($this->isPost('delete')){
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