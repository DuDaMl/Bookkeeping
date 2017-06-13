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
    public $param;
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
    function getByControllerAndParam($controller, $param)
    {
       $sql = "SELECT * FROM `" . self::TABLE_NAME . "`" .
              " WHERE  controller = '" . $controller . "'" .
              " AND param = '" . $param . "'";
        $answer = $this->get($sql);
        return $answer[0];
    }

    /**
     * @return int|id
     */
    function getAllParamByController($controller)
    {
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "`" .
            " WHERE  controller = '" . $controller . "'";
        $answer = $this->get($sql);

        $settings = array();

        // todo обработка false
        foreach($answer as $item)
        {
            $settings[$item->param] = $item->value;
        }
        return (object)$settings;
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
}