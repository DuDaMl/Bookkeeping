<?php
namespace bookkeeping\Models\Settings;
use \bookkeeping\Models\DB;
use \PDO;

class PaySetting extends Setting
{
    use \bookkeeping\Models\Traits\ValidateDate;

    const TABLE_NAME = 'setting';
    private $id;
    protected static $user_id;
    protected static $controller = 'pay';
    public $value;

    public $error_validation;

    function __construct(int $user_id)
    {
        static::$user_id = $user_id;
    }

    /**
     * @return int|id
     */
    public function getSettings()
    {
        $DB = DB::getInstance();

       $sql = "SELECT * FROM `" . self::TABLE_NAME . "`" .
              " WHERE  controller = '" . self::getController() . "'
               AND user_id = " . self::getUserId() . "
               LIMIT 1";

       $result = $DB->query($sql, 'fetch');

       if(! $result)
       {
           // создание параметров для контроллера по умолчанию
           $data_params = array(
               'date_start' => date('Y-m-01'),
               'date_end' => date('Y-m-31'),
               'format' => 'month'
           );

           $this->create(serialize($data_params));
           return (object) $data_params;
       } else {
           // возвращение сохраненных ранее параметров контроллера
           return (object) unserialize($result->value);
       }
    }

    /**
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
                user_id = :user_id,
                value = :value
                WHERE id = :id
                ";

        $params = [
            ':user_id' => self::getUserId(),
            ':value' => $value,
            ':id' => $id
        ];

        $DB = DB::getInstance();
        return $DB->execute($sql, $params);
    }

    /**
     * @return bool
     */
    protected function create($value)
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
            ':user_id' => self::getUserId(),
            ':value' => $value,
            ':controller' => self::getController()
        ];

        $DB = DB::getInstance();
        return $DB->execute($sql, $params);
    }

    private static function getSettingId()
    {
        $DB = DB::getInstance();
        $sql = "SELECT id FROM `" . self::TABLE_NAME . "`" .
            " WHERE  controller = '" . self::getController() . "'
               AND user_id = " . self::getUserId() . "
               LIMIT 1";

        return $DB->query($sql, 'fetch');
    }

    protected function prepareFormat()
    {
        if(isset($_POST['settings']))
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

            return $params;
        }
    }

    public function setFormat()
    {
        $params = $this->prepareFormat();

        // валидация введенных дат по переданной маске из контроллера
        if(! $this->validateDate($params['date_start'], 'Y-m-d')
            || ! $this->validateDate($params['date_end'], 'Y-m-d'))
        {
            // в случае неправльно указанных данных устанавливается значение по умолчанию
            $params['date_start']= date('Y-m-01');
            $params['date_end'] = date('Y-m-31');
        }

        // проверка существования записи для выбора действия INSERT/DELETE
        $controller_db = self::getSettingId();

        if(! empty ($controller_db))
        {
            // update
            $result = $this->edit($controller_db->id, serialize($params));
            $this->error_validation .= " no update";
        } else {
            // insert
            $result = $this->create(serialize($params));
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