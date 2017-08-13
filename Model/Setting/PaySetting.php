<?php
namespace bookkeeping\Model\Setting;
use \bookkeeping\Model\User;
use \bookkeeping\Model\DB;
use \bookkeeping\Model\Setting\Preparer\Preparer;

/**
 * Класс предназначен для создания/обновления/возвращения параметров настроек для контроллера Pay
 * @package bookkeeping\Models\Settings
 */
class PaySetting
    extends Setting
{
    CONST TABLE_NAME = 'pay_setting';

    public $id;
    public $user_id;
    public $date_start;
    public $date_end;
    public $format;


    function __construct()
    {
        // Загрузка данных по user_id
        $this->get();
    }

    public function get()
    {
        $sql = "SELECT * FROM `" . static::TABLE_NAME . "`" .
            " WHERE  user_id = " . User::getId() . "
               LIMIT 1";

        $DB = DB::getInstance();
        $result = $DB->query($sql)[0];

        if(! $result)
        {
            // создание параметров по умолчанию
            $this->setDefault();
            $this->create( User::getId() );

        } else {
            $this->date_start = $result->date_start;
            $this->date_end = $result->date_end;
            $this->format = $result->format;
        }
    }

    // создание параметров по умолчанию
    protected function setDefault()
    {
        $this->date_start = date('Y-m-01');
        $this->date_end = date('Y-m-31');
        $this->format = 'month';
    }

    public function update($date)
    {
        $this->prepareFormat($date);

        if(! $this->validate())
        {
            return false;
        }

        $sql = "UPDATE `" . static::TABLE_NAME . "` SET
                date_start = :date_start,
                date_end = :date_end,
                format = :format
                WHERE user_id = :user_id
                ";

        $params = [
            ':date_start' => $this->date_start,
            ':date_end' => $this->date_end,
            ':format' => $this->format,
            ':user_id' =>  User::getId()
        ];

        $DB = DB::getInstance();
        return $DB->execute($sql, $params);
    }

    public function create($date)
    {
        $this->prepareFormat($date);

        if(! $this->validate())
        {
            return false;
        }

        $sql = "INSERT INTO  `" . static::TABLE_NAME . "` (
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
            ':user_id' =>  User::getId(),
            ':date_start' => $this->date_start,
            ':date_end' => $this->date_end,
            ':format' => $this->format
        ];

        $DB = DB::getInstance();
        return $DB->execute($sql, $params);
    }

    protected function prepareFormat(array $date)
    {
        $Preparer = Preparer::getInstance(static::class);
        $Preparer->prepare($this, $date);
    }

    private function validate()
    {
        if(! $this->date_start || ! $this->date_end || $this->format == '')
        {
            return false;
        }

        return true;
    }
}