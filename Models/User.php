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
     * @param $id
     * @return Obj
     */
    static function getById($id)
    {
        if(!$id)
        {
            return false;
        }

        $DB = DB::getInstance();
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE  id = " . $id;
        $answer = $DB->query($sql, 'fetch');
        return $answer;
    }

    /**
     * @return array|bool
     */
    function getByEmail($email)
    {
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE  email = '" . $email ."'";
        $answer = $this->DB->query($sql, 'fetch');

        if(! empty($answer))
        {
            return $answer;
        } else {
            return false;
        }
    }

    static function getUserId()
    {
        if(self::checkAuth())
        {
            return $_SESSION["user_id"];
        }
    }


    /**
     * Check Auth user
     * @return bool
     */
    public static function checkAuth( )
    {
        if(isset($_SESSION['user_id']) && isset($_SESSION['token']))
        {
            return self::checkToken($_SESSION['user_id'], $_SESSION['token']);
        } else {
            return false;
        }
    }

    /**
     * Check $_Session token with saved token in DB
     * @param $user_id
     * @param $token
     * @return bool
     */
    public static function checkToken($user_id, $token)
    {
        $DB = DB::getInstance();
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE  id = " . $user_id . " LIMIT 1";
        $user = $DB->query($sql, 'fetch');

        if($user->token != $_SESSION['token'])
        {
            //session_destroy();
            return false;
        }

        if(self::updateToken($user_id))
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update token in DB and $_Session
     * @param $user_id
     * @return bool
     */
    public static function updateToken($user_id)
    {
        $DB = DB::getInstance();
        $token = bin2hex(random_bytes('64'));

        $sql = "UPDATE `" . self::TABLE_NAME . "` SET
                token = :token
                WHERE id = :id
                ";

        $params = [
            ':token' => $token,
            ':id' => $user_id
        ];

        $result = $DB->execute($sql, $params);

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

        $params = [
            ':email' => $this->email,
            ':given_name' => $this->given_name,
            ':family_name' => $this->family_name,
            ':picture' => $this->picture,
            ':link' => $this->link,
            ':gender' => $this->gender
        ];

        $result = $this->DB->execute($sql, $params);
        if($result)
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