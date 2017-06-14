<?php
namespace bookkeeping\Controllers;
use bookkeeping\Controllers\Controller as Controller;
use bookkeeping\Models\Setting as M_Setting;
use bookkeeping\Models\Pay as M_Pay;
use bookkeeping\Models\Category as M_Category;


class Pay
    extends Controller
{
    protected $main_teamplate = 'Pay';
    private $M_Pay;

    // Начало формирования отчёта
    public $data_report_start;

    // Конец формирования отчёта
    public $data_report_end;

    // Формат диапазона: Год|Месяц|День
    public $format;

    function __construct()
    {
        parent::__construct();
        $this->M_Pay = new M_Pay();
        $this->M_Setting = new M_Setting();
        $this->setting();
    }

    public function setting()
    {
        if(isset($_POST['format']))
        {
            switch($_POST['format'])
            {
                case 'day':
                    $format_value = 'day';
                    $date = $_POST['day'];
                    $format = 'Y-m-d';
                    $this->data_report_start = $date;
                    $this->data_report_end = $date;
                    break;
                case 'month':
                    $format_value = 'month';
                    $date = $_POST['month'];
                    $format = 'Y-m';
                    $this->data_report_start = $date . "-01";
                    $this->data_report_end = $date . "-31";
                    break;
                case 'year':
                    $format_value = 'year';
                    $date = $_POST['year'];
                    $format = 'Y';
                    $this->data_report_start = $date . "-01-01";
                    $this->data_report_end = $date . "-12-31";
                    break;
                default:
                    $format_value = 'month';
                    $date = 'Y-m-01';
                    $format = 'Y-m-d';
                    $this->data_report_start = date('Y-m-01');
                    $this->data_report_end = date('Y-m-31');
                    break;
            }

            // Валидация даты
            if(! $this->M_Pay->validateDate($date, $format))
            {
                echo "<h1>false</h1>";
                // в случае неправльно указанных данных устанавливается значение по умолчанию
                $this->data_report_start = date('Y-m-01');
                $this->data_report_end = date('Y-m-31');
            }

            // Запись параметров в БД
            $date_start = $this->M_Setting->getByControllerAndParam($this->main_teamplate, 'date_start');
            $date_end = $this->M_Setting->getByControllerAndParam($this->main_teamplate, 'date_end');
            $date_formate = $this->M_Setting->getByControllerAndParam($this->main_teamplate, 'format');

            // todo check the isset param

            $this->M_Setting->edit($date_start->id, $this->data_report_start);
            $this->M_Setting->edit($date_end->id, $this->data_report_end);
            $this->M_Setting->edit($date_formate->id, $format_value);

            header("Location: /" . $this->main_teamplate);
            exit();
        }

        // Init data
        $date_start = $this->M_Setting->getByControllerAndParam($this->main_teamplate, 'date_start');
        $date_end = $this->M_Setting->getByControllerAndParam($this->main_teamplate, 'date_end');
        $date_formate = $this->M_Setting->getByControllerAndParam($this->main_teamplate, 'format');

        $this->data_report_start = $date_start->value;
        $this->data_report_end = $date_end->value;
        $this->date_formate = $date_formate->value;
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
            if($this->isPost('save'))
            {
                header("Location: /");
                exit();
            }

            // ошибки добавления новой записи расходов
            $data['error'] = $this->M_Pay->error_validation;
        }

        // загрузка всех платежей текущего месяца
        $data['pays'] = $this->M_Pay->getAll($this->data_report_start, $this->data_report_end);

        // загрузка всех категорий расходов
        $data['categories'] =  (new M_Category())->getAll();
        $data['settings'] =  $this->M_Setting->getAllParamByController($this->main_teamplate);
        $this->render($data);
    }

    function edit($id)
    {
        if(!empty($_POST) && $_POST['category_id'] != ''){
            if($this->isPost('save')){
                header("Location: /");
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

        $M_Category = new M_Category();
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
                header("Location: /");
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