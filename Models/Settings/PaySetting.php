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
    private $id;
    public $user_id;
    protected static $controller = 'pay';
    public $date_start;
    public $date_end;
    public $format;
    public $value;

    public $error_validation;

    /**
     * PaySetting constructor.
     * Инициализация статической переменной user_id
     * @param int $user_id - id авторихзированного пользователя
     */
    function __construct()
    {
    }

    /**
     * Возвращает запись из БД для указанного контроллера и id пользователя
     * Или значение по умолчанию
     * @param int | $user_id
     * @return object | Обьект с полями значений настроек
     */
    public function getSettings()
    {
        if(! $this->user_id)
        {
            return false;
        }

       $sql = "SELECT * FROM `" . self::TABLE_NAME . "`" .
              " WHERE  controller = '" . self::getController() . "'
               AND user_id = " . $this->user_id . "
               LIMIT 1";

       $DB = DB::getInstance();
       $result = $DB->query($sql)[0];

       if(! $result)
       {
           // создание параметров для контроллера по умолчанию
           $data_params = array(
               'date_start' => date('Y-m-01'),
               'date_end' => date('Y-m-31'),
               'format' => 'month'
           );
           self::create($this->user_id, serialize($data_params));
       } else {

           $data_params = unserialize($result->value);
       }

        $this->date_start = $data_params['date_start'];
        $this->date_end = $data_params['date_end'];
        $this->format = $data_params['format'];

        return true;
    }

    /**
     * Обновление настроек в БД
     * @param int | $id - id записи настроек в таблице БД
     * @param string | $value - изменяемые значения
     * @return bool
     */
    protected function edit($id, $value)
    {
        if(empty($id))
        {
            // todo pointed this mistake
            return false;
        }

        $sql = "UPDATE `" . self::TABLE_NAME . "` SET
                value = :value
                WHERE id = :id
                ";

        $params = [
            ':value' => $value,
            ':id' => $id
        ];


        $DB = DB::getInstance();
        return $DB->execute($sql, $params);
    }

    /**
     * Создание новой записи параметров для определенного контроллера и авторизированного пользователя
     * @param  string | $value - сохранямые параметры
     * @return bool
     */
    protected static function create($user_id, $value)
    {
        $sql = "INSERT INTO  `" . self::TABLE_NAME . "` (
                `user_id`,
                `controller`,
                `value`)
                VALUES (
                :user_id,
                :controller,
                :value )
                ";

        $params = [
            ':user_id' => $user_id,
            ':value' => $value,
            ':controller' => self::getController()
        ];

        $DB = DB::getInstance();
        return $DB->execute($sql, $params);
    }

    /**
     * Возвращает id записи для текущего пользователя и контроллера
     * @return array|bool|object
     */
    private function getSettingId()
    {
        $DB = DB::getInstance();
        $sql = "SELECT id FROM `" . self::TABLE_NAME . "`" .
            " WHERE  controller = '" . self::getController() . "'
               AND user_id = " . $this->user_id . "
               LIMIT 1";

        return $DB->query($sql)[0];
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

        $params = array(
            'date_start' => $data_report_start,
            'date_end' => $data_report_end,
            'format' => $this->format
        );

        $this->value = serialize($params);
        $this->date_start = $data_report_start;
        $this->date_end = $data_report_end;

        if(! $this->validateDate( $this->date_start, 'Y-m-d')
            || ! $this->validateDate($this->date_end, 'Y-m-d'))
        {
            return false;
        }

        return true;
    }

    /**
     * Запись настроек контроллера в БД.
     * @param array  - $_POST ['setting']
     * @return bool
     */
    public function setFormat()
    {
        // проверка существования записи для выбора действия INSERT/DELETE
        $controller_db = self::getSettingId();

        if(! $this->value)
        {
            return false;
        }

        if(! empty ($controller_db))
        {
            // update
            $result = $this->edit($controller_db->id, $this->value);
            $this->error_validation .= " no update";
        } else {
            // insert
            $result = self::create(self::getUserId(), $this->value);
            $this->error_validation .= " no insert";
        }

        if(! $result)
        {
            // todo записать в лог.
            return false;
        }
        return true;
    }

}