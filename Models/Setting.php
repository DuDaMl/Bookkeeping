<?php
namespace bookkeeping\Models;
use bookkeeping\Models\Settings\PaySetting;

use \PDO;


class Setting
{
    use \bookkeeping\Models\Traits\ValidateDate;

    const TABLE_NAME = 'setting';
    protected $DB;
    public $id;
    static $user_id;
    static $controller;
    public $value;

    public $error_validation;

    function __construct(string $controller, int $user_id)
    {
        static::$user_id = $user_id;
        static::$controller = $controller;
        $this->DB = DB::getInstance();
    }

    static function getController()
    {
        return static::$controller;
    }
    static function getUserId()
    {
        return static::$user_id;
    }
    /**
     * @return array|bool
     */
    function getById(int $id)
    {
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE  id = " . $id;
        $answer =  $this->DB->query($sql, 'fetch');
        return $answer;
    }

    /**
     * @return int|id
     */
    public static function getByController()
    {
        $DB = DB::getInstance();

       $sql = "SELECT * FROM `" . self::TABLE_NAME . "`" .
              " WHERE  controller = '" . self::getController() . "'
               AND user_id = " . self::getUserId() . "
               LIMIT 1";

       return  $DB->query($sql, 'fetch');

    }

    /**
     * @return bool
     */
    function edit($id, $value)
    {
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
    function create($value)
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

        return $this->DB->execute($sql, $params);
    }

    public function setFormat(array $params)
    {
        /*switch(self::getController())
        {
            case 'Pay':
                return (new PaySetting())->setFormat($params);
                break;
            default:
                // todo create Error Exception;
                return false;
                break;
        }*/

        // валидация введенных дат по переданной маске из контроллера
        if(! $this->validateDate($params['date_start'], 'Y-m-d'))
        {
            // в случае неправльно указанных данных устанавливается значение по умолчанию
            $params['date_start']= date('Y-m-01');
            $params['date_end'] = date('Y-m-31');
        }

        // проверка существования записи для выбора действия INSERT/DELETE
        $controller_db = $this->getByController();


        if(! empty ($controller_db))
        {
            // update
            $result = $this->edit($controller_db->id, serialize($params));
            $this->error_validation .= " no update";
        } else {
            // insert
            $result = $this->create(self::getController(), serialize($params));
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