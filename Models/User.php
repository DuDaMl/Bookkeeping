<?php
namespace bookkeeping\Models;
use \DateTime;
use \PDO;

class User
{
    const TABLE_NAME = 'user';
    protected $DB;
    public $id;
    public $token;
    public $email;
    public $given_name;
    public $family_name;
    public $picture;
    public $link;
    public $gender;

    public $error_validation;

    function __construct()
    {
        $this->DB = DB::getInstance();
    }
    /**
     * @return array|bool
     */

    protected function get($sql)
    {
        try
        {
            $result = $this->DB->query($sql);
            return $result;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return false;
        }

        //$result = $this->DB->query($sql);
        //return $result;
        /*try
        {
            $result = $this->DB->prepare($sql);

            $result->execute();

            return $result->fetchAll(PDO::FETCH_CLASS);
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return false;
        }*/

    }

    /**
     * @param $id
     * @return Obj
     */
    function getById($id)
    {
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE  id = " . $id;
        $answer = $this->get($sql);
        return $answer[0];
    }
    /**
     * @return array|bool
     */
    /*function getTokenById($id)
    {
        $sql = "SELECT `token` FROM `" . self::TABLE_NAME . "` WHERE  id = " . $id;
        $answer = $this->get($sql);
        return $answer[0];
    }*/
    /**
     * @return array|bool
     */
    function getByEmail($email)
    {
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE  email = '" . $email ."'";
        $answer = $this->get($sql);

        if(! empty($answer))
        {
            return $answer[0];
        } else {
            return false;
        }

    }

    function checkToken($user_id)
    {
        // user from db by user id as Obj
        $user = $this->getById($user_id);
        if($user->token != $_SESSION['token'])
        {
            //session_destroy();
            return false;
        }

        $token = bin2hex(random_bytes('64'));

        $sql = "UPDATE `" . self::TABLE_NAME . "` SET
                token = :token
                WHERE id = :id
                ";

        $params = [
          ':token' => $token,
          ':id' => $user_id
        ];


        $result = $this->DB->execute($sql, $params);

        //return $result;

        //$stmt = $this->DB->prepare($sql);
        //$stmt->bindParam(':token', $token, PDO::PARAM_STR);
        //$stmt->bindParam(':id',  $user_id, PDO::PARAM_INT);

        if($result)
        {
            $_SESSION['token'] =  $token;
            return true;
        } else {
            return false;
        }
    }

    function setSession($user_id)
    {
        $token = bin2hex(random_bytes('64'));
        $_SESSION['token'] =  $token;
        $_SESSION['user_id'] = $user_id;

        $sql = "UPDATE `" . self::TABLE_NAME . "` SET
                token = :token
                WHERE id = :id
                ";

        $params = [
            ':token' => $token,
            ':id' => $user_id
        ];


        $result = $this->DB->execute($sql, $params);

        if($result)
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    function save()
    {
        // Create
        $sql = "INSERT INTO `" . self::TABLE_NAME . "`
                    (email,
                    given_name,
                    family_name,
                    picture,
                    link,
                    gender)
                     VALUES (
                    :email,
                    :given_name,
                    :family_name,
                    :picture,
                    :link,
                    :gender
                    )";

        $stmt = $this->DB->prepare($sql);
        $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
        $stmt->bindParam(':given_name', $this->given_name, PDO::PARAM_STR);
        $stmt->bindParam(':family_name', $this->family_name, PDO::PARAM_STR);
        $stmt->bindParam(':picture', $this->picture, PDO::PARAM_STR);
        $stmt->bindParam(':link', $this->link, PDO::PARAM_STR);
        $stmt->bindParam(':gender', $this->gender, PDO::PARAM_STR);

        if($stmt->execute())
        {
            return $this->DB->lastInsertId();
        } else {
            return false;
        }
    }

    function logout()
    {
        session_destroy();
    }
}