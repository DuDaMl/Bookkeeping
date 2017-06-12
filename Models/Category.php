<?php
namespace bookkeeping\Models;
use \PDO;

class Category
{

    const TABLE_NAME = 'category';
    protected $DB;
    public $name;
    public $type;

    public $error_validation;

    function __construct()
    {
        $this->DB = DB::getInstance()->getConnection();
    }

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

    function getAll($type = 'Pay')
    {
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "`" . " WHERE type = '" . $type . "' ORDER BY name ASC";
        return $this->get($sql);
    }

    function getAllPays()
    {
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE type = 'Pay' ORDER BY name ASC";
        return $this->get($sql);
    }

    function getAllIncomes()
    {
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE type = 'Income'  ORDER BY name ASC";
        return $this->get($sql);
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
     * @return bool
     */
    function save()
    {
        if(! self::validate())
        {
            return false;
        }

        if(empty($this->name))
        {
            $this->error_validation = array(
                'error' => true,
                'amount' => 'Имя категории не может быть пустым',
            );
           return false;
        }

            if(isset($this->id) && $this->id != '')
            {
                // Update
                $sql = "UPDATE `" . self::TABLE_NAME . "` SET
                    name = :name,
                    type = :type
                    WHERE id = :id
                    ";
            } else {
                // Create
                $sql = "INSERT INTO `" . self::TABLE_NAME . "`
                    (name,
                    type)
                     VALUES (
                    :name,
                    :type
                    )";
            }

        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':name', $this->name , PDO::PARAM_STR);
        $stmt->bindParam(':type', $this->type , PDO::PARAM_STR);

        if(isset($this->id) && $this->id != ''){
            $stmt->bindParam(':id',  $this->id, PDO::PARAM_INT);
        }

        if($stmt->execute()){
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    function delete()
    {
        if(! self::validate()){

            return false;
        }

        if(isset($this->id) && $this->id != '')
        {
            // Delete
            $sql = "DELETE FROM `" . self::TABLE_NAME . "` 
                    WHERE id = :id
                    ";
        } else {
            // ошибка, попытка удаления без id
            return false;
        }

        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':id',  $this->id, PDO::PARAM_INT);

        if($stmt->execute()){
            return true;
        } else {
            return false;
        }
    }

    function validate()
    {
        // валидация переданного id
        if(isset($_POST['id']))
        {
            if(! filter_var($_POST['id'], FILTER_VALIDATE_INT))
            {
                $this->error_validation = array(
                    'error' => true,
                    'amount' => 'Ошибка в указанном id',
                );
                return false;
            } else {
                $id = str_replace('+','',$_POST['id']);
                $this->id = str_replace('-','',$id);
            }
        }
        /*
        if($_POST['type'] != 'Pay'
            && $_POST['type'] != 'Income')
        {
            $this->error_validation = array(
                'error' => true,
                'amount' => 'Ошибка в указанном типе категории',
            );
            return false;
        }
        */
        $this->name = strip_tags($_POST['name']);
        $this->type = strip_tags($_POST['type']);
        return true;
    }
}