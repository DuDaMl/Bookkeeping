<?php
namespace bookkeeping\Model;

class Model
{
    use \bookkeeping\Model\Traits\ValidateDate;
    use \bookkeeping\Model\Traits\ValidateInt;

    public $id;
    public $user_id;
    public $amount;
    public $description;
    public $category_id;
    public $date;

    public $error_validation;

    /**
     * Create record in db
     * @return bool
     */
    public function create()
    {
        if( empty($this->amount)
            || empty($this->category_id)
            || empty($this->date))
        {
            // todo Exception notFillingData
            return false;
        }

        $sql = "INSERT INTO `" . static::TABLE_NAME . "`
                    (amount,
                    description,
                    user_id,
                    category_id,
                    date)
                     VALUES (
                    :amount,
                    :description,
                    :user_id,
                    :category_id,
                    :date
                    )";

        $params = [
            ':amount' => $this->amount,
            ':description' => $this->description,
            ':category_id' => $this->category_id,
            ':user_id' => $this->user_id,
            ':date' => $this->date
        ];

        $DB = DB::getInstance();
        // todo Exception PDO
        return $DB->execute($sql, $params);
    }

    /**
     * update record in db
     * @return bool
     */
    public function update()
    {

        if(! isset($this->id) || $this->id == '')
        {
            return false;
        }

        if( empty($this->amount)
            || empty($this->category_id)
            || empty($this->date))
        {
            return false;
        }

        $sql = "UPDATE `" . static::TABLE_NAME . "` SET
                    amount = :amount,
                    description = :description,
                    category_id = :category_id,
                    user_id = :user_id,
                    date = :date
                    WHERE id = :id";

        $params = [
            ':id' => $this->id,
            ':amount' => $this->amount,
            ':description' => $this->description,
            ':category_id' => $this->category_id,
            ':user_id' => $this->user_id,
            ':date' => $this->date
        ];

        $DB = DB::getInstance();
        return $DB->execute($sql, $params);
    }

    /**
     * delete record in db
     * @param id
     * @return bool
     */
    function delete()
    {
        if(! isset($this->id) || $this->id == '')
        {
            return false;
        }

        $sql = "DELETE FROM `" . static::TABLE_NAME . "`  WHERE id = :id ";

        $params = [
            ':id' => $this->id
        ];

        $DB = DB::getInstance();
        $result = $DB->execute($sql, $params);

        if($result)
        {
            return true;
        } else {
            return false;
        }
    }

    function prepareFormat($date)
    {
        $this->amount = $this->validateInt($date['amount']);
        $this->category_id = $this->validateInt($date['category_id']);
        $this->user_id = $this->validateInt($date['user_id']);

        if($this->validateDate($date['date']))
        {
            $this->date = $date['date'];
        }

        $this->description = trim(htmlspecialchars($_POST['description']));
    }
}