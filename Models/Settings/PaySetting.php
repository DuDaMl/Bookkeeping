<?php
namespace bookkeeping\Models\Settings;
use \bookkeeping\Models\DB;
use \PDO;

/**
 * Класс предназначен для создания/обновления/возвращения параметров настроек для контроллера Pay
 * @package bookkeeping\Models\Settings
 */
class PaySetting extends Setting
{
    CONST TABLE_NAME = 'pay_setting';

    private $id;
    public $user_id;
    public $date_start;
    public $date_end;
    public $format;
    public $error_validation;

    /**
     * PaySetting constructor.
     * Загрузка настроек по указаному user_id
     * Инициализация user_id
     * @param int $user_id - id авторихзированного пользователя
     */
    function __construct(int $user_id)
    {
        $this->user_id = $user_id;

        // Загрузка данных по user_id
        $this->getSettingsByUserId();
    }

    /**
     * Возвращает запись из БД для указанного user_id
     * Или значение по умолчанию
     * @return object | Обьект с полями значений настроек
     */
    public function getSettingsByUserId()
    {
        if(! $this->user_id)
        {
            return false;
        }

       $sql = "SELECT * FROM `" . PaySetting::TABLE_NAME . "`" .
              " WHERE  user_id = " . $this->user_id . "
               LIMIT 1";

       $DB = DB::getInstance();
       $result = $DB->query($sql)[0];

       if(! $result)
       {
           // создание параметров по умолчанию
           $this->date_start = date('Y-m-01');
           $this->date_end = date('Y-m-31');
           $this->format = 'month';

           $this->create($this->user_id);

       } else {
           $this->date_start = $result->date_start;
           $this->date_end = $result->date_end;
           $this->format = $result->format;
       }
    }

    /**
     * Обновление настроек в БД
     * @return bool
     */
    public function updateSettingByUserId()
    {
        if(empty($this->user_id) ||
        empty($this->date_start) ||
        $this->format == '')
        {
            return false;
        }

        if(! $this->prepareFormat($this->date_start))
        {
            return false;
        }

        $sql = "UPDATE `" . self::TABLE_NAME . "` SET
                date_start = :date_start,
                date_end = :date_end,
                format = :format
                WHERE user_id = :user_id
                ";

        $params = [
            ':date_start' => $this->date_start,
            ':date_end' => $this->date_end,
            ':format' => $this->format,
            ':user_id' => $this->user_id
        ];

        $DB = DB::getInstance();
        return $DB->execute($sql, $params);
    }

    /**
     * Создание новой записи параметров для определенного контроллера и авторизированного пользователя
     * @param  string | $value - сохранямые параметры
     * @return bool
     */
    protected function create($user_id)
    {

        $sql = "INSERT INTO  `" . self::TABLE_NAME . "` (
                `user_id`,
                `date_start`,
                `date_end`,
                `format`)
                VALUES (
                :user_id,
                :date_start,
                :date_end,
                :format )
                ";

        $params = [
            ':user_id' => $this->user_id,
            ':date_start' => $this->date_start,
            ':date_end' => $this->date_end,
            ':format' => $this->format
        ];

        $DB = DB::getInstance();
        return $DB->execute($sql, $params);
    }

    /**
     * Функция подготовки параметров настроек для контроллера перед сохранением в БД
     * @return
     */
    public function prepareFormat($date)
    {
        $date = trim($date);

        switch($this->format)
        {
            case 'day':
                $data_report_start = $date;
                $data_report_end = $date;
                break;
            case 'month':
                $data_report_start = $date . "-01";
                $data_report_end = $date . "-31";
                break;
            case 'year':
                $data_report_start = $date . "-01-01";
                $data_report_end = $date . "-12-31";
                break;
            default:
                $data_report_start = date('Y-m-01');
                $data_report_end = date('Y-m-31');
                break;
        }

        $this->date_start = $data_report_start;
        $this->date_end = $data_report_end;

        if(! $this->validateDate( $this->date_start, 'Y-m-d')
            || ! $this->validateDate($this->date_end, 'Y-m-d'))
        {
            return false;
        }

        return true;
    }
}