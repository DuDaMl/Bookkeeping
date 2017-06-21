<?php
namespace bookkeeping\Models;
use \PDO;

class Category
{

    const TABLE_NAME = 'category';
    protected $DB;
    public $name;
    public $user_id;
    public $type;

    public $error_validation;

    function __construct($user_id)
    {
        $this->user_id = $user_id;
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
    protected function save($sql)
    {
        if($this->user_id == false || ! self::validate())
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

        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':name', $this->name , PDO::PARAM_STR);
        $stmt->bindParam(':type', $this->type , PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $this->type , PDO::PARAM_STR);

        if(isset($this->id) && $this->id != ''){
            $stmt->bindParam(':id',  $this->id, PDO::PARAM_INT);
        }

        if($stmt->execute()){
            return true;
        } else {
            return false;
        }
    }

    public function update()
    {
        if(! isset($this->id) && $this->id == '')
        {
            return false;
        }

        $sql = "UPDATE `" . self::TABLE_NAME . "` SET
                    name = :name,
                    type = :type
                    user_id = :user_id
                    WHERE id = :id
                    ";

        return $this->save($sql);
    }

    public function create()
    {
        $sql = "INSERT INTO `" . self::TABLE_NAME . "`
                    (name,
                    user_id,
                    type)
                     VALUES (
                    :name,
                    :user_id,
                    :type
                    )";

        return $this->save($sql);
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
        if(! filter_var($this->user_id, FILTER_VALIDATE_INT))
        {
            $this->error_validation = array(
                'error' => true,
                'amount' => 'Ошибка в указанном user_id',
            );
            return false;
        } else {
            $id = str_replace('+','',$this->user_id);
            $this->user_id = str_replace('-','',$id);
        }

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

        $this->name = strip_tags($_POST['name']);
        $this->type = strip_tags($_POST['type']);
        return true;
    }
}