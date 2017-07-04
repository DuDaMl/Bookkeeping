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
        $this->DB = DB::getInstance();
    }

    function getAll($type = 'Pay')
    {
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "`" . " 
        WHERE type = '" . $type . "' 
        AND user_id = " . $this->user_id . "
        ORDER BY name ASC";
        return $this->DB->query($sql);
    }

    function getAllPays()
    {
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` 
        WHERE type = 'Pay' 
        AND user_id = " . $this->user_id . "
        ORDER BY name ASC";
        return $this->DB->query($sql);
    }

    function getAllIncomes()
    {
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` 
        WHERE type = 'Income'  
        AND user_id = " . $this->user_id . "
        ORDER BY name ASC";
        return $this->DB->query($sql);
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
        return $this->DB->query($sql, 'fetch');
    }

    public function update()
    {
        if(! self::validate() )
        {
            $this->error_validation = array(
                'error' => true,
                'amount' => 'Ошибка в переданном id',
            );
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

        $sql = "UPDATE `" . self::TABLE_NAME . "` SET
                    name = :name,
                    type = :type,
                    user_id = :user_id
                    WHERE id = :id
                    ";
        echo $sql;
        /*echo $this->name . "<br/>";
        echo $this->type . "<br/>";
        echo $this->user_id . "<br/>";
        echo $this->id . "<br/>";*/

        $params = [
            ':name' => $this->name,
            ':type' => $this->type,
            ':user_id' => $this->user_id,
            ':id' => $this->id,
        ];

        return $this->DB->execute($sql, $params);
    }

    public function create()
    {
        if(!self::validate() || empty($this->name) )
        {
            $this->error_validation = array(
                'error' => true,
                'amount' => 'Имя категории не может быть пустым',
            );
            return false;
        }

        $sql = "INSERT INTO `" . self::TABLE_NAME . "`
                    (name,
                    user_id,
                    type)
                     VALUES (
                    :name,
                    :user_id,
                    :type
                    )";

        $params = [
            ':name' => $this->name,
            ':user_id' => $this->user_id,
            ':type' => $this->type
        ];

            return $this->DB->execute($sql, $params);

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

        $params = [
            ':id' => $this->id
        ];

        return $this->DB->execute($sql, $params);
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