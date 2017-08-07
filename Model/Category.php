<?php
namespace bookkeeping\Model;
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

    /**
     * @param int | $user_id
     * @param string | $type
     * @return array|bool|object
     */
    static function getAll($user_id, $type)
    {
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "`" . " 
        WHERE type = '" . $type . "' 
        AND user_id = " . $user_id . "
        ORDER BY name ASC";

        $DB = DB::getInstance();
        return $DB->query($sql);
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
        if(filter_var($id, FILTER_VALIDATE_INT))
        {
            $id = str_replace('+', '', $id);
            $id = str_replace('-', '', $id);
        } else {
            $this->error_validation = array(
                'error' => true,
                'text' => 'Ошибка в передаваемом id',
            );
            return false;
        }

        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE  id = " . $id . " LIMIT 1";

        return $this->DB->query($sql);

    }

    public function update()
    {
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
        if( empty($this->name) ||
            empty($this->type) )
        {
            $this->error_validation = array(
                'error' => true,
                'amount' => 'Имя категории не может быть пустым или отсутствует тип категории',
            );

            return false;
        }

        $sql = "INSERT INTO `" . self::TABLE_NAME . "`
                    (name, user_id, type)
                     VALUES 
                    (:name, :user_id, :type )";

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

    public function setId(int $id)
    {
        $this->id = $id;
    }

    function prepareData($date)
    {
        $this->name = strip_tags(trim(htmlspecialchars($date['name'])));
        $this->type = strip_tags(trim(htmlspecialchars($date['type'])));
        $this->description = strip_tags(trim(htmlspecialchars($date['description'])));
    }
}