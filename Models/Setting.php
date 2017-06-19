<?php
namespace bookkeeping\Models;
use \DateTime;
use \PDO;

class Setting
{
    const TABLE_NAME = 'setting';
    protected $DB;
    public $id;
    public $controller;
    public $value;

    public $error_validation;

    function __construct()
    {
        $this->DB = DB::getInstance()->getConnection();
    }
    /**
     * @return array|bool
     */

    protected function get($sql)
    {
        try
        {
            $result = $this->DB->prepare($sql);
            $result->execute();
            return $result->fetchAll(PDO::FETCH_CLASS);
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return false;
        }
    }
    /**
     * @return array|bool
     */
    function getById($id)
    {
        if(filter_var($id, FILTER_VALIDATE_INT)){
            $id = str_replace('+', '', $id);
            $id = str_replace('-', '', $id);
        } else {
            $this->error_validation = array(
                'error' => true,
                'text' => 'Ошибка в передаваемом id',
            );
            return false;
        }

        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE  id = " . $id;
        $answer = $this->get($sql);
        return $answer[0];
    }

    /**
     * @return int|id
     */
    function getByController($controller)
    {
       $sql = "SELECT * FROM `" . self::TABLE_NAME . "`" .
              " WHERE  controller = '" . $controller . "'";

        $answer = $this->get($sql);

        if(! empty($answer))
        {
            return $answer[0];
        } else {
            return false;
        }

    }

    /**
     * @return bool
     */
    function edit($id, $value)
    {
        // Update
        $sql = "UPDATE `" . self::TABLE_NAME . "` SET
                value = :value
                WHERE id = :id
                ";

        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':value', $value, PDO::PARAM_STR);
        $stmt->bindParam(':id',  $id, PDO::PARAM_INT);

        if($stmt->execute())
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    function create($controller, $value)
    {
        $sql = "INSERT INTO  `" . self::TABLE_NAME . "` (
                `controller`,
                `value`)
                VALUES (
                :controller,
                :value )
                ";

        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':controller', $controller, PDO::PARAM_STR);
        $stmt->bindParam(':value',  $value, PDO::PARAM_STR);

        if($stmt->execute())
        {
            return true;
        } else {
            return false;
        }
    }

    public function setFormat($controller, array $params)
    {
        // проверка на существование значений
        if(empty($controller))
        {
            // todo отобразить данную ошибку в логах
            $this->error_validation .= "нельзя сохранить парметры без имени контроллера";
            return false;
        }

        // валидация введенных дат по переданной маске из контроллера
        if(! $this->validateDate($params['date_start'], 'Y-m-d'))
        {
            // в случае неправльно указанных данных устанавливается значение по умолчанию
            $params['date_start']= date('Y-m-01');
            $params['date_end'] = date('Y-m-31');
        }

        // проверка существования записи для выбора действия INSERT/DELETE
        $controller_db = $this->getByController($controller);


        if(! empty ($controller_db))
        {
            // update
            $result = $this->edit($controller_db->id, serialize($params));
            $this->error_validation .= " no update";
        } else {
            // insert
            $result = $this->create($controller, serialize($params));
            $this->error_validation .= " no insert";
        }

        if(! $result)
        {
            // todo записать в лог.
            return false;
        }
        return true;

    }


    /**
     * @param $date
     * @param string $format
     * @return bool false если формат не верен, дату в формате если верно
     */
    function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}